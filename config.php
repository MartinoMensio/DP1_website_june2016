<?php
ini_set('display_errors', 1);

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