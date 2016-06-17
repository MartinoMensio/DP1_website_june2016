<?php
require 'config.php';

$authenticated = false;

// this function checks if authentication is valid or expired
// the parameter $redirect is a boolean:
// - true means that if authentication is not valid, the user must go to login page
// - false means that for this page authentication is not required
function checkAuthentication($redirect) {
  global $maxInactiveTime, $loginPage, $authenticated;
  session_start();
  // check the value of the timeout
  if(!isset($_SESSION['timeout']) || $_SESSION['timeout'] + $maxInactiveTime < time()) {
    // expired or new session
    if(!isset($_SESSION['timeout'])) {
      $message = 'In order to view this page you must be authenticated';
    } else {
      $message = 'Your session expired. Please log in again';
    }
    
    session_unset();
    session_destroy();
    if($redirect) {
      // go to login
      goToPage("$loginPage?error=$message");
      die();
    }
  } else {
    // valid session, update the timeout
    $_SESSION['timeout'] = time();
    $authenticated = true;
  }
}

// generic function to connect to database disabling autocommit
function connectToDb() {
  global $host, $user, $pwd, $db;
  $conn = @new mysqli($host, $user, $pwd, $db);
  if($conn->connect_error) {
    die('<div style="color:#fff;background-color:#f44336"><h1>Connection with database failed</h1><h3>Please contact the system administrator</h3></div>');
  }
  if(!$conn) {
    die('impossible to connect to database');
  }
  // the db credentials are not more needed. For security reasons, i unset them
  unset($host);
  unset($user);
  unset($pwd);
  unset($db);
  $conn->autocommit(false);
  return $conn;
}

// this function is called to display all the reservations
function listAllReservations($conn) {
  $result = $conn->query('SELECT * FROM reservations ORDER BY starting_hour, starting_minute');
  if(!$result) {
    die('impossible to list the reservations');
  }
  if($result->num_rows == 0) {
    echo '<h3>There are no reservations stored</h3>';
  } else {
    echo '<table class="w3-table w3-bordered w3-striped w3-centered w3-hoverable">';
    echo '<tr class="w3-blue"><th>Starting time</th><th>Ending time</th><th>Duration (minutes)</th><th>Selected machine</th></tr>';
    while($row = $result->fetch_object()) {
      // compute the duration
      $duration = $row->ending_hour*60 + $row->ending_minute -$row->starting_hour*60 - $row->starting_minute;
      echo '<tr><td>'.sprintf("%02d:%02d",$row->starting_hour, $row->starting_minute).'</td><td>'.sprintf("%02d:%02d",$row->ending_hour, $row->ending_minute).'</td><td>'.$duration.'</td><td>'.$row->machine.'</td></tr>';
    }
    $result->close();
    echo '</table>';
  }
}

// this function is called to display the current user reservations
function listUserReservations($conn) {
  // the user id is stored in the session array
  $result = $conn->query('SELECT * FROM reservations WHERE user_id ='.$_SESSION['user_id'].' ORDER BY starting_hour, starting_minute');
  if(!$result) {
    die('impossible to list the reservations');
  }
  if($result->num_rows == 0) {
    echo '<h3>You have no reservations</h3>';
  } else {
    echo '<table class="w3-table w3-bordered w3-striped w3-centered w3-hoverable">';
    echo '<tr class="w3-blue"><th>Starting time</th><th>Ending time</th><th>Duration (minutes)</th><th>Selected machine</th><th></th></tr>';
    while($row = $result->fetch_object()) {
      $duration = $row->ending_hour*60 + $row->ending_minute -$row->starting_hour*60 - $row->starting_minute;
      // the remove can be done by using a link, so that also if client disabled javascript it works
      echo '<tr><td>'.sprintf("%02d:%02d",$row->starting_hour, $row->starting_minute).'</td><td>'.sprintf("%02d:%02d",$row->ending_hour, $row->ending_minute).'</td><td>'.$duration.'</td><td>'.$row->machine.'</td><td><a class="w3-btn w3-indigo" type="button" href="reservation_delete.php?type=remove&id='.$row->id.'">Remove</a></td></tr>';
    }
    $result->close();
    echo '</table>';
  }
}

// print the sidenav that contains some useful links
function sidenavPrint() {
  global $authenticated, $loginPage;
  echo '<a href="index.php">All reservations</a>';
  // look if user is logged in (checked by the checkAuthentication function)
  if($authenticated) {
    // authenticated user
    echo '<h1 class="w3-padding-medium">Hello '.$_SESSION['name'].'</h1>';
    echo '<a href="profile.php">Profile</a>';
    echo '<a href="new_reservation.php">New reservation</a>';
    echo '<a href="list_user_reservations.php">My reservations</a>';
    echo '<a href="logout.php">logout</a>';
  } else {
    // not autenthicated user
    echo "<a href=\"$loginPage\">Login or register</a>";
  }
}

// on which file am I executing this script?
function getCurrentScriptFileName() {
  return basename($_SERVER['SCRIPT_FILENAME']);
}

// where should I go on success?
function getRedirectionPageSuccess() {
  global $redirections;
  return $redirections[getCurrentScriptFileName()]['success'];
}

// where should I go on error?
function getRedirectionPageError() {
  global $redirections;
  return $redirections[getCurrentScriptFileName()]['error'];
}

// return a sanitized version of a POST argument
function getRequiredPostArgument($conn, $name, $escape = true) {
  // check if the argument is present
  if(!isset($_POST[$name]) || $_POST[$name] === '') {
    goToWithError("missing required data: $name");
    die();
  }
  // if escape is set to true, also sanitize with htmlentities
  if ($escape) {
    $result = $conn->real_escape_string(htmlentities(trim($_POST[$name])));
  } else {
    // only prevent SQL injection, but not escape HTML characters
    // this is used for the passwords
    // SQL injection can also be prevented by using prepared statements, but just to be sure use the real_escape_string method
    // passwords are never displayed, so escaping html special characters has no reason to be done
    $result = $conn->real_escape_string($_POST[$name]);
  }
  return $result;
}

// redirect on corresponding error page
function goToWithError($error) {
  header('Location: '.getRedirectionPageError()."?error=$error");
  die();
}

// redirect on corresponding success page
function goToDestination() {
  header('Location: '.getRedirectionPageSuccess());
  die();
}

// go to a custom destination page
function goToPage($destination) {
  header("Location: $destination");
  die();
}

// login function
// email and password are already sanitized and checked by caller
function login($conn, $email, $password) {
  $result = $conn->query("SELECT name, surname, id FROM users WHERE email = '$email' AND password = '$password'");
  if(!$result) {
    goToWithError('Impossible to create the query');
  }
  if($result->num_rows == 0) {
    // both if password wrong or if non-existing account
    goToWithError('Wrong credentials');
  }
  if(!($row = $result->fetch_object())) {
    goToWithError('Error fetching the result');
  }
  $result->close();
  // save into the session array some useful data
  $_SESSION['timeout'] = time();
  $_SESSION['name'] = $row->name;
  $_SESSION['surname'] = $row->surname;
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  $_SESSION['user_id'] = $row->id;
  // and go to the right place
  goToDestination();
}

// parameters are already sanitized and checked by caller
function signup($conn, $name, $surname, $email, $password) {
  $result = $conn->query("INSERT INTO users(name, surname, email, password) VALUES('$name', '$surname', '$email', '$password')");
  if(!$result) {
    goToWithError('Impossible to create the account. Maybe the email was already used');
  }
  // the id of the last inserted value
  $id = $conn->insert_id;
  if(!$conn->commit()) {
    goToWithError('Impossible to commit. Please try again');
  }
  // save into the session array
  $_SESSION['timeout'] = time();
  $_SESSION['name'] = $name;
  $_SESSION['surname'] = $surname;
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  $_SESSION['user_id'] = $id;
  // and go to the right place
  goToDestination();
}

// as for other functions, parameters are sanitized and checked by caller
function insertNewReservation($conn, $duration, $starting_minute, $starting_hour) {
  global $numberOfMachines;
  
  $reservation = new stdClass();
  // compute the ending time
  $ending_minute = ($starting_minute + $duration) % 60;
  $ending_hour = floor(($starting_minute + $duration) / 60) + $starting_hour;
  
  $starting_time = $starting_minute+60*$starting_hour;
  $ending_time = $ending_minute+60*$ending_hour;
  
  // also if not released explicitly, on rollback (caused by die) the tables are unlocked
  
  $machines = array();
  // initialize array of boolean that say: the machine is available
  for($i = 0; $i < $numberOfMachines; $i++) {
    $machines[$i] = true;
  }
  $result = $conn->query("SELECT machine FROM reservations WHERE starting_hour*60+starting_minute < $ending_time AND ending_hour*60+ending_minute > $starting_time FOR UPDATE");
  if(!$result) {
    goToWithError('error in the query');
  }
  while ($row = $result->fetch_object()) {
    // this machine is used in this time slot, so i can't use it anymore
    $machines[$row->machine] = false;
  }
  $result->close();
  $machine = -1;
  
  // scan the array to see if some free machines are still there
  for ($i=0; $i < $numberOfMachines; $i++) { 
    if ($machines[$i]) {
      $machine = $i;
      break;
    }
  }
  
  if($machine == -1) {
    // no machine is free
    goToWithError('No machine is free in this time slot');
  }

  $curTime = date('H:i:s');
  $pieces = explode(':', $curTime);
  // the reservation time is the number of seconds after 00:00:00
  $reservation_time = ($pieces[0] * 60 + $pieces[1]) * 60 + $pieces[2];
  
  $result = $conn->query("INSERT INTO reservations(reservation_time, starting_hour, starting_minute, ending_hour, ending_minute, machine, user_id) VALUES($reservation_time, $starting_hour, $starting_minute, $ending_hour, $ending_minute, $machine, ".$_SESSION['user_id'].')');
  if(!$result) {
    goToWithError('impossible to insert the reservation');
  }
  // the id of the last inserted value
  $id = $conn->insert_id;
  if(!$conn->commit()) {
    goToWithError('commit');
  }
  $reservation->duration = $duration;
  $reservation->starting_minute = $starting_minute;
  $reservation->starting_hour = $starting_hour;
  $reservation->ending_minute = $ending_minute;
  $reservation->ending_hour = $ending_hour;
  $reservation->machine = $machine;
  $reservation->id = $id;
  return $reservation;
}

// the parameter id is already sanitized and checked by the caller
function removeReservation($conn, $id) {
  $result = $conn->query("SELECT reservation_time, user_id FROM reservations WHERE id = $id FOR UPDATE");
  if(!$result) {
    goToWithError('Error in query');
  }
  if($result->num_rows == 0) {
    goToWithError('Reservation not found. Impossible to delete it');
  }
  if(!($row = $result->fetch_object())) {
    goToWithError('Error fetching object');
  }
  $result->close();
  // check who created this reservation
  if ($row->user_id != $_SESSION['user_id']) {
    goToWithError('You tried to delete a reservation that was created by another user!');
  }

  $curTime = date('H:i:s');
  $pieces = explode(':', $curTime);
  $time_now = ($pieces[0] * 60 + $pieces[1]) * 60 + $pieces[2];
  $timeAfterReservation = $time_now - $row->reservation_time;
  // check if at least 60 seconds have passed since the reservation submission
  // a negative time means that it has been created in another day, so i let the deletion to happen
  // in this way i block the deletion only in the minute after the creation
  // the requirements say that we don't have to consider days
  if ($timeAfterReservation < 60 && $timeAfterReservation >= 0) {
    goToWithError('In order to delete a reservation, at least 1 minute has to pass since the reservation submission');
  }
  
  // now I can delete it
  $result = $conn->query("DELETE FROM reservations WHERE id = $id");
  if(!$result) {
    goToWithError('impossible to delete, consistency may be lost');
  }
  if(!$conn->commit()) {
    goToWithError('commit');
  }
  return;
}
?>