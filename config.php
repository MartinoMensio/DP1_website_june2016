<?php
// show warnings and errors
ini_set('display_errors', 1);

// force HTTPS
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], TRUE, 301);
}

function checkCookies() {
	setcookie('test', 1);
	if(!isset($_GET['cookies'])){
		if (sizeof($_GET)) {
			// add a new argument
			header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "&cookies", TRUE, 301);
		} else {
			// this is the only argument
			header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?cookies", TRUE, 301);
		}
	}
	if(count($_COOKIE) > 0){
		// ok
	} else {
		die('<h1>you must enable cookies to view this site</h1>');
	}

}

checkCookies();

$maxInactiveTime = 60 * 2;
$numberOfMachines = 4;

$loginPage = "login.php";

// check which db to use
$useLocalDb = false;
if ($useLocalDb) {
	$host = "localhost";
	$user = "root";
	$pwd = "";
	$db = "machines_reservation";
} else {
	$host = "us-cdbr-azure-west-c.cloudapp.net";
	$user = "b411bdc8084ca4";
	$pwd = "fdd4ffbb";
	$db = "dp-web-jun16-martinomensio";
}