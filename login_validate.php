<?php
  session_start();
  require 'functions.php';
  if(!isset($_POST["type"])) {
    goToDestination($loginPage);
  }
  $conn = connectToDb();
  if($_POST["type"] === "login") {
    // check the login info from DB
    $email = getRequiredPostArgument($conn, "email");
    // TODO password must not be escaped, because it can be weakened
    $password = sha1(getRequiredPostArgument($conn, "password", false));
    
    login($conn, $email, $password);
  } else if($_POST["type"] === "register") {
    // check register info and insert into DB
    $name = getRequiredPostArgument($conn, "name");
    $surname = getRequiredPostArgument($conn, "surname");
    $email = getRequiredPostArgument($conn, "email");
    // TODO password must not be escaped, because it can be weakened
    $password = sha1(getRequiredPostArgument($conn, "password", false));
    if(strlen($name) > 50) {
      goToWithError($loginPage, 'Name too long');
    }
    if(strlen($surname) > 50) {
      goToWithError($loginPage, 'Surname too long');
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      goToWithError($loginPage, 'Invalid email');
    }
    //var_dump($password);
    signup($conn, $name, $surname, $email, $password);
  } else {
    goToDestination($loginPage);
  }

?>
