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
		var_dump($_SESSION);
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
	echo '<tr><th>Starting time</th><th>Duration</th><th>Selected machine</th><th>User</th></tr>';
	while($row = $query->fetch_object()) {
		echo "<tr><td>".$row->starting_hour.":".$row->starting_minute."</td><td>".$row->duration."</td><td>".$row->machine."</td><td>".$row->user_id."</td></tr>";
	}
	echo '</table>';
}
function sidenavPrint() {
	global $authenticated, $loginPage;
	// look if user is logged in (checked by a init function)
	if($authenticated) {
		echo '<h1>Hello user</h1>';
		echo '<a href="profile.php">profile</a>';
		echo '<a href="#">add reservation</a>';
		echo '<a href="#">list my reservations</a>';
		echo "<a href=\"$loginPage\">logout</a>";
	} else {
		echo '<a href="login.html">login</a>';
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
?>