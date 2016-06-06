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
	echo '<tr><th>Starting time</th><th>Ending time</th><th>Duration (minutes)</th><th>Selected machine</th></tr>';
	while($row = $query->fetch_object()) {
		$duration = $row->ending_hour*60 + $row->ending_minute -$row->starting_hour*60 - $row->starting_minute;
		echo "<tr><td>".sprintf("%02d:%02d",$row->starting_hour, $row->starting_minute)."</td><td>".sprintf("%02d:%02d",$row->ending_hour, $row->ending_minute)."</td><td>".$duration."</td><td>".$row->machine."</td></tr>";
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
	echo '<tr><th>Starting time</th><th>Ending time</th><th>Duration (minutes)</th><th>Selected machine</th></tr>';
	while($row = $query->fetch_object()) {
		$duration = $row->ending_hour*60 + $row->ending_minute -$row->starting_hour*60 - $row->starting_minute;
		echo "<tr><td>".sprintf("%02d:%02d",$row->starting_hour, $row->starting_minute)."</td><td>".sprintf("%02d:%02d",$row->ending_hour, $row->ending_minute)."</td><td>".$duration."</td><td>".$row->machine.'</td><td><button type="button" onclick="remove_reservation('.$row->id.')">Remove</button></td></tr>';
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
		echo "<a href=\"$loginPage\">login or register</a>";
	}
}

function getRequiredPostArgument($conn, $name) {
	global $loginPage;
	//var_dump($_POST[$name]);
	if(!isset($_POST[$name])) {
		header('Location: '.$loginPage);
		die();
	}
	$result = $conn->real_escape_string(htmlentities(trim($_POST[$name])));
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
		goToWithError('login.php','prepare');
	}
	if(!$stmt->bind_param("ss", $email, $password)) {
		goToWithError('login.php','bind_param');
	}
	if(!$stmt->execute()) {
		goToWithError('login.php','execute');
	}
	if(!$stmt->bind_result($name, $surname, $id)) {
		goToWithError('login.php','bind_result');
	}
	if(!$stmt->fetch()) {
		// gets there when the selects founds 0 rows
		goToWithError('login.php','account not found. Please check your data');
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
		goToWithError('login.php', 'prepare');
	}
	if(!$stmt->bind_param("ssss", $name, $surname, $email, $password)) {
		goToWithError('login.php', 'bind_param');
	}
	if(!$stmt->execute()) {
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
	// TODO check values of three parameters
	// TODO check overlapping reservations (SELECT locking)
	// TODO machine choose
	$reservation = new stdClass();
	
	$ending_minute = ($starting_minute + $duration) % 60;
	$ending_hour = floor(($starting_minute + $duration) / 60) + $starting_hour;
	
	$starting_time = $starting_minute+60*$starting_hour;
	$ending_time = $ending_minute+60*$ending_hour;
	
	//echo "$ending_hour:$ending_minute";
	
	// also if not released explicitly, on rollback (caused by die) the tables are unlocked
	/*
	LOCK TABLES reservations WRITE;
	
	SELECT machine FROM reservations
	WHERE starting_hour < sh
	AND starting_minute < sm
	AND ending_hour > eh
	AND ending_minute > em;

	for each of them, remove from the available ones
	and see if some is still available
	
	INSERT INTO reservations ...;
	
	UNLOCK TABLES;
	*/
	$machines = [];
	for($i = 0; $i < $numberOfMachines; $i++) {
		$machines[$i] = true;
	}
	$stmt = $conn->prepare("SELECT machine FROM reservations WHERE starting_hour*60+starting_minute < ? AND ending_hour*60+ending_minute > ? FOR UPDATE");
	if(!$stmt) {
		goToWithError('new_reservation.php','prepare select');
	}
	if(!$stmt->bind_param("ii", $ending_time, $starting_time)) {
		goToWithError('new_reservation.php','bind_param select');
	}
	if(!$stmt->execute()) {
		goToWithError('new_reservation.php','execute select');
	}
	if(!$stmt->bind_result($machine)) {
		goToWithError('new_reservation.php', 'bind_result select');
	}
	while ($stmt->fetch()) {
		$machines[$machine] = false;
	}
	
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
	
	$stmt = $conn->prepare("INSERT INTO reservations(starting_hour, starting_minute, ending_hour, ending_minute, machine, user_id) VALUES(?, ?, ?, ?, ?, ?)");
	if(!$stmt) {
		goToWithError('new_reservation.php','prepare insert');
	}
	if(!$stmt->bind_param("iiiiii", $starting_hour, $starting_minute, $ending_hour, $ending_minute, $machine, $_SESSION["user_id"])) {
		goToWithError('new_reservation.php','bind_param insert');
	}
	if(!$stmt->execute()) {
		goToWithError('new_reservation.php','execute insert');
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
	// TODO for update?
	$stmt = $conn->prepare("SELECT starting_hour, starting_minute FROM reservations WHERE id = ? FOR UPDATE");
	if(!$stmt) {
		goToWithError('list_user_reservations.php','prepare select');
	}
	if(!$stmt->bind_param("i", $id)) {
		goToWithError('list_user_reservations.php','bind_param select');
	}
	if(!$stmt->execute()) {
		goToWithError('list_user_reservations.php','execute select');
	}
	if(!$stmt->bind_result($starting_hour, $starting_minute)) {
		goToWithError('login.php','bind_result select');
	}
	if(!$stmt->fetch()) {
		// gets there when the selects founds 0 rows
		goToWithError('login.php','reservation not found. Impossible to delete it');
	}
	$curTime = date('H:i');
	$pieces = explode(":", $curTime);
	$timeBeforeStart = ($starting_hour-$pieces[0])*60 + $starting_minute - $pieces[1];
	//echo "time: $curTime";
	//echo "$starting_hour:$starting_minute";
	//echo $timeBeforeStart;
	//die();
	if ($timeBeforeStart < 1) {
		goToWithError('list_user_reservations.php', 'Reservation start time is passed or too near to delete');
	}
	// clean up $stmt
	unset($stmt);
	
	
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