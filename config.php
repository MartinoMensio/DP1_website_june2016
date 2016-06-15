<?php
// show warnings and errors
ini_set('display_errors', 1);

// force HTTPS
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], TRUE, 301);
	die();
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
		die();
	}
	if(count($_COOKIE) > 0){
		// ok
	} else {
		die('<h1 style="color:#fff;background-color:#f44336">you must enable cookies to view this site</h1>');
	}

}

if (!isset($_COOKIE['test'])) {
	checkCookies();
}

$maxInactiveTime = 60 * 2;
$numberOfMachines = 4;

$loginPage = "login.php";
$homePage = "index.php";

// this array stores a couple of pages: success and error
$redirections = array(
	'login_validate.php' => array(
		'success' => 'index.php',
		'error' => 'login.php'
	),
	'reservation_submit.php' => array(
		'success' => '', // stay on the same page
		'error' => 'new_reservation.php'
	),
	'reservation_delete.php' => array(
		'success' => '', // stay on the same page
		'error' => 'list_user_reservations.php'
	)
);

// redirect navigation from function pages to homepage
switch (basename($_SERVER["SCRIPT_FILENAME"])) {
	case 'config.php':
	case 'functions.php':
		header("Location: $homePage");
		break;
	default:
		// nothing
		break;
}

// check which db to use
$database = "azure";
if ($database === "local") {
	$host = "localhost";
	$user = "root";
	$pwd = "";
	$db = "machines_reservation";
} else if($database === "azure") {
	require '../db_credentials.php';
	$host = $azure_host;
	$user = $azure_user;
	$pwd = $azure_pwd;
	$db = $azure_db;
} else {
	$host = "localhost";
	$user = "s232297";
	$pwd = "angstshs";
	$db = "s232297";
}

?>