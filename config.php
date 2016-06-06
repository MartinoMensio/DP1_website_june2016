<?php
// show warnings and errors
ini_set('display_errors', 1);

// force HTTPS
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], TRUE, 301);
}

function checkCookies() {
	//echo sizeof($_REQUEST);
	if(isset($_REQUEST["testing"])) {
		// cookie has been set by last visit, verify it
		if(!isset($_COOKIE["test"])) {
			// cookies are disabled
			die('you must enable cookies to view this site');
		}
	} else if (!isset($_COOKIE["test"])) {
		// this is the first visit, or cookies disabled
		setcookie('test', 'hello');
		if (sizeof($_REQUEST)) {
			// add a new argument
			header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "&testing", TRUE, 301);
		} else {
			// this is the only argument
			header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?testing", TRUE, 301);
		}
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