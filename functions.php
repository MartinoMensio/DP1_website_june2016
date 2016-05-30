<?php
require 'config.php';

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
	echo "<table>";
	echo "<tr><th>Starting time</th><th>Duration</th><th>Selected machine</th><th>User</th></tr>";
	while($row = $query->fetch_object()) {
		echo "<tr><td>".$row->starting_hour.":".$row->starting_minute."</td><td>".$row->duration."</td><td>".$row->machine."</td><td>".$row->user_id."</td></tr>";
	}
}
?>