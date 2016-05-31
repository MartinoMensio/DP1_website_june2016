<?php
// show warnings and errors
ini_set('display_errors', 1);

// force HTTPS
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], TRUE, 301);
}

$maxInactiveTime = 60 * 2;
$loginPage = "login.html";

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