<?php
require 'config.php';

$authenticated = false;

// chech http://php.net/manual/en/features.http-auth.php

function checkAuthentication($redirect) {
	global $maxInactiveTime, $loginPage, $authenticated;
	session_start();
	if(!isset($_SESSION['timeout']) || $_SESSION['timeout'] + $maxInactiveTime < time()) {
		// expired or new session
		session_unset();
  	session_destroy();
		if($redirect) {
			// go to login
			header('Location: ' . $loginPage);
			die();
		}
		//var_dump($_SESSION);
	} else {
		// valid session
		$_SESSION['timeout'] = time();
		$authenticated = true;
	}
}

function connectToDb() {
	global $host, $user, $pwd, $db;
	$conn = new mysqli($host, $user, $pwd, $db);
	// TODO catch errors of connection
	if(!$conn) {
		die("impossible to connect to database");
	}
	unset($host);
	unset($user);
	unset($pwd);
	unset($db);
	$conn->autocommit(false);
	return $conn;
}

function listAllReservations($conn) {
	$result = $conn->query("SELECT * FROM reservations ORDER BY starting_hour, starting_minute");
	if(!$result) {
		die("impossible to list the reservations");
	}
	if($result->num_rows == 0) {
		echo '<h3>There are no reservations stored</h3>';
	} else {
		echo '<table class="w3-table w3-bordered w3-striped w3-centered">';
		echo '<tr class="w3-blue"><th>Starting time</th><th>Ending time</th><th>Duration (minutes)</th><th>Selected machine</th></tr>';
		while($row = $result->fetch_object()) {
			$duration = $row->ending_hour*60 + $row->ending_minute -$row->starting_hour*60 - $row->starting_minute;
			echo "<tr><td>".sprintf("%02d:%02d",$row->starting_hour, $row->starting_minute)."</td><td>".sprintf("%02d:%02d",$row->ending_hour, $row->ending_minute)."</td><td>".$duration."</td><td>".$row->machine."</td></tr>";
		}
		$result->close();
		echo '</table>';
	}
}

function listUserReservations($conn) {
	$result = $conn->query("SELECT * FROM reservations WHERE user_id =".$_SESSION["user_id"]." ORDER BY starting_hour, starting_minute");
	if(!$result) {
		die("impossible to list the reservations");
	}
	if($result->num_rows == 0) {
		echo '<h3>You have no reservations</h3>';
	} else {
		echo '<table class="w3-table w3-bordered w3-striped w3-centered">';
		echo '<tr class="w3-blue"><th>Starting time</th><th>Ending time</th><th>Duration (minutes)</th><th>Selected machine</th><th></th></tr>';
		while($row = $result->fetch_object()) {
			$duration = $row->ending_hour*60 + $row->ending_minute -$row->starting_hour*60 - $row->starting_minute;
			echo "<tr><td>".sprintf("%02d:%02d",$row->starting_hour, $row->starting_minute)."</td><td>".sprintf("%02d:%02d",$row->ending_hour, $row->ending_minute)."</td><td>".$duration."</td><td>".$row->machine.'</td><td><button class="w3-btn w3-indigo" type="button" onclick="remove_reservation('.$row->id.')">Remove</button></td></tr>';
		}
		$result->close();
		echo '</table>';
	}
}

function sidenavPrint() {
	global $authenticated, $loginPage;
	echo '<a href="index.php">Homepage (all reservations)</a>';
	// look if user is logged in (checked by a init function)
	if($authenticated) {
		echo '<h1 class="w3-padding-medium">Hello '.$_SESSION["name"].'</h1>';
		echo '<a href="profile.php">profile</a>';
		echo '<a href="new_reservation.php">add reservation</a>';
		echo '<a href="list_user_reservations.php">list my reservations</a>';
		echo '<a href="logout.php">logout</a>';
	} else {
		echo "<a href=\"$loginPage\">login or register</a>";
	}
}

function getRequiredPostArgument($conn, $name, $escape = true) {
	global $loginPage;
	//var_dump($_POST[$name]);
	if(!isset($_POST[$name])) {
		header('Location: '.$loginPage);
		die();
	}
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

function goToWithError($destination, $error) {
	header("Location: $destination?error=$error");
	die();
}

function goToDestination($destination) {
	header("Location: $destination");
	die();
}

function login($conn, $email, $password) {
	$result = $conn->query("SELECT name, surname, id FROM users WHERE email = '$email' AND password = '$password'");
	if(!$result) {
		goToWithError('login.php',"impossible to create the query");
	}
	if($result->num_rows == 0) {
		goToWithError('login.php','Wrong credentials');
	}
	if(!($row = $result->fetch_object())) {
		goToWithError('login.php','Error fetching the result');
	}
	$result->close();
  $_SESSION['timeout'] = time();
  $_SESSION['name'] = $row->name;
  $_SESSION['surname'] = $row->surname;
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  $_SESSION['user_id'] = $row->id;
  //var_dump($_SESSION);
  goToDestination('index.php');
}

function signup($conn, $name, $surname, $email, $password) {
	$result = $conn->query("INSERT INTO users(name, surname, email, password) VALUES('$name', '$surname', '$email', '$password')");
	if(!$result) {
		goToWithError('login.php', 'impossible to create the account. Maybe the email was already used');
	}
	// the id of the last inserted value
	$id = $conn->insert_id;
	if(!$conn->commit()) {
		goToWithError('login.php', 'commit');
	}
  $_SESSION['timeout'] = time();
  $_SESSION['name'] = $name;
  $_SESSION['surname'] = $surname;
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  $_SESSION['user_id'] = $id;
  //var_dump($_SESSION);
  goToDestination('index.php');
}

function insertNewReservation($conn, $duration, $starting_minute, $starting_hour) {
	global $numberOfMachines;
	// values of parameters are checked by caller
	$reservation = new stdClass();
	
	$ending_minute = ($starting_minute + $duration) % 60;
	$ending_hour = floor(($starting_minute + $duration) / 60) + $starting_hour;
	
	$starting_time = $starting_minute+60*$starting_hour;
	$ending_time = $ending_minute+60*$ending_hour;
	
	// also if not released explicitly, on rollback (caused by die) the tables are unlocked
	
	$machines = array();
	for($i = 0; $i < $numberOfMachines; $i++) {
		$machines[$i] = true;
	}
	$result = $conn->query("SELECT machine FROM reservations WHERE starting_hour*60+starting_minute < $ending_time AND ending_hour*60+ending_minute > $starting_time FOR UPDATE");
	if(!$result) {
		goToWithError('new_reservation.php','error in the query');
	}
	while ($row = $result->fetch_object()) {
		$machines[$row->machine] = false;
	}
	$result->close();
	$machine = -1;
	
	for ($i=0; $i < $numberOfMachines; $i++) { 
		if ($machines[$i]) {
			$machine = $i;
			break;
		}
	}
	//var_dump($machines);
	
	if($machine == -1) {
		// no machine is free
		goToWithError('new_reservation.php','no machine is free in this time slot');
	}

	$curTime = date('H:i:s');
	$pieces = explode(":", $curTime);
	$reservation_time = ($pieces[0] * 60 + $pieces[1]) * 60 + $pieces[2];
	
	$result = $conn->query("INSERT INTO reservations(reservation_time, starting_hour, starting_minute, ending_hour, ending_minute, machine, user_id) VALUES($reservation_time, $starting_hour, $starting_minute, $ending_hour, $ending_minute, $machine, ".$_SESSION["user_id"].")");
	if(!$result) {
		goToWithError('new_reservation.php','impossible to insert the reservation');
	}
	// the id of the last inserted value
	$id = $conn->insert_id;
	if(!$conn->commit()) {
		goToWithError('new_reservation.php','commit');
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

function removeReservation($conn, $id) {
	$result = $conn->query("SELECT reservation_time, user_id FROM reservations WHERE id = $id FOR UPDATE");
	if(!$result) {
		goToWithError('list_user_reservations.php','error in query');
	}
	if($result->num_rows == 0) {
		goToWithError('list_user_reservations.php','reservation not found. Impossible to delete it');
	}
	if(!($row = $result->fetch_object())) {
		goToWithError('list_user_reservations.php','error fetching object');
	}
	$result->close();

	if ($row->user_id != $_SESSION["user_id"]) {
		goToWithError('list_user_reservations.php', 'You tried to delete a reservation that was created by another user!');
	}

	$curTime = date('H:i:s');
	$pieces = explode(":", $curTime);
	$time_now = ($pieces[0] * 60 + $pieces[1]) * 60 + $pieces[2];
	$timeAfterReservation = $time_now - $row->reservation_time;
	// check if at least 60 seconds have passed since the reservation submission
	if ($timeAfterReservation < 60) {
		goToWithError('list_user_reservations.php', 'In order to delete a reservation, at least 1 minute has to pass since the reservation submission');
	}
	
	
	$result = $conn->query("DELETE FROM reservations WHERE id = $id");
	if(!$result) {
		goToWithError('list_user_reservations.php','impossible to delete, consistency may be lost');
	}
	if(!$conn->commit()) {
		goToWithError('list_user_reservations.php','commit');
	}
	return;
}
?>