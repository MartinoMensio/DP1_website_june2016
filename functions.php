<?php
require 'config.php';

$authenticated = false;

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
		$authenticated = true;
	}
}

function connectToDb() {
	global $host, $user, $pwd, $db;
	$conn = new mysqli($host, $user, $pwd, $db);
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
	$query = $conn->query("SELECT * FROM reservations ORDER BY starting_hour, starting_minute");
	if(!$query) {
		die("impossible to list the reservations");
	}
	if($conn->affected_rows == 0) {
		die("no data is stored");
	}
	echo '<table>';
	echo '<tr><th>Starting time</th><th>Duration</th><th>Selected machine</th></tr>';
	while($row = $query->fetch_object()) {
		echo "<tr><td>".$row->starting_hour.":".$row->starting_minute."</td><td>".$row->duration."</td><td>".$row->machine."</td></tr>";
	}
	echo '</table>';
}

function listUserReservations($conn) {
	$query = $conn->query("SELECT * FROM reservations WHERE user_id =".$_SESSION["user_id"]." ORDER BY starting_hour, starting_minute");
	if(!$query) {
		die("impossible to list the reservations");
	}
	if($conn->affected_rows == 0) {
		die("no data is stored");
	}
	echo '<table>';
	echo '<tr><th>Starting time</th><th>Duration</th><th>Selected machine</th></tr>';
	while($row = $query->fetch_object()) {
		echo "<tr><td>".$row->starting_hour.":".$row->starting_minute."</td><td>".$row->duration."</td><td>".$row->machine.'</td><td><button type="button" onclick="remove_reservation('.$row->id.')">Remove</button></td></tr>';
	}
	echo '</table>';
}

function sidenavPrint() {
	global $authenticated, $loginPage;
	echo '<a href="index.php">Homepage (all reservations)</a>';
	// look if user is logged in (checked by a init function)
	if($authenticated) {
		echo '<h1>Hello '.$_SESSION["name"].'</h1>';
		echo '<a href="profile.php">profile</a>';
		echo '<a href="new_reservation.php">add reservation</a>';
		echo '<a href="list_user_reservations.php">list my reservations</a>';
		echo '<a href="logout.php">logout</a>';
	} else {
		echo "<a href=\"$loginPage\">login</a>";
	}
}

function getRequiredPostArgument($conn, $name) {
	global $loginPage;
	//var_dump($_POST[$name]);
	if(!isset($_POST[$name])) {
		header('Location: '.$loginPage);
		die();
	}
	$result = $conn->real_escape_string(htmlentities($_POST[$name]));
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
	$stmt = $conn->prepare("SELECT name, surname, id FROM users WHERE email = ? AND password = ?");
	if(!$stmt) {
		goToWithError('login.html','prepare');
	}
	if(!$stmt->bind_param("ss", $email, $password)) {
		goToWithError('login.html','bind_param');
	}
	if(!$stmt->execute()) {
		goToWithError('login.html','execute');
	}
	if(!$stmt->bind_result($name, $surname, $id)) {
		goToWithError('login.html','bind_result');
	}
	if(!$stmt->fetch()) {
		goToWithError('login.html','fetch');
	}
	/* // don't need to commit for a select statement
	if(!$conn->commit()) {
		goToWithError('login.html','commit');
	}*/
  $_SESSION['timeout'] = time();
  $_SESSION['name'] = $name;
  $_SESSION['surname'] = $surname;
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  $_SESSION['user_id'] = $id;
  //var_dump($_SESSION);
  goToDestination('index.php');
}

function signup($conn, $name, $surname, $email, $password) {
	$stmt = $conn->prepare("INSERT INTO users(name, surname, email, password) VALUES(?, ?, ?, ?)");
	if(!$stmt) {
		goToWithError('login.html', 'prepare');
	}
	if(!$stmt->bind_param("ssss", $name, $surname, $email, $password)) {
		goToWithError('login.html', 'bind_param');
	}
	if(!$stmt->execute()) {
		goToWithError('login.html', 'execute');
	}
	// the id of the last inserted value
	$id = $conn->insert_id;
	if(!$conn->commit()) {
		goToWithError('login.html', 'commit');
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
	// TODO check values of three parameters
	// TODO check overlapping reservations (SELECT locking)
	// TODO machine choose
	$reservation = new stdClass();
	$machine = 1;
	
	$stmt = $conn->prepare("INSERT INTO reservations(duration, starting_hour, starting_minute, machine, user_id) VALUES(?, ?, ?, ?, ?)");
	if(!$stmt) {
		goToWithError('new_reservation.php','prepare');
	}
	if(!$stmt->bind_param("iiiii", $duration, $starting_hour, $starting_minute, $machine, $_SESSION["user_id"])) {
		goToWithError('new_reservation.php','bind_param');
	}
	if(!$stmt->execute()) {
		goToWithError('new_reservation.php','bind_param');
	}
	// the id of the last inserted value
	$id = $conn->insert_id;
	if(!$conn->commit()) {
		goToWithError('new_reservation.php','commit');
	}
	$reservation->duration = $duration;
	$reservation->starting_minute = $starting_minute;
	$reservation->starting_hour = $starting_hour;
	$reservation->machine = 1;
	$reservation->id = $id;
	return $reservation;
}

function removeReservation($conn, $id) {
	$stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
	if(!$stmt) {
		goToWithError('list_user_reservations.php','prepare');
	}
	if(!$stmt->bind_param("i", $id)) {
		goToWithError('list_user_reservations.php','bind_param');
	}
	if(!$stmt->execute()) {
		goToWithError('list_user_reservations.php','execute');
	}
	if(!$conn->commit()) {
		goToWithError('list_user_reservations.php','commit');
	}
	return;
}
?>