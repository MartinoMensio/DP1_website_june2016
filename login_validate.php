<?php
  session_start();
  require 'functions.php';
  if(!isset($_POST["type"])) {
    header('Location: ' . $loginPage);
    die();
  }
  $conn = connectToDb();
  if($_POST["type"] === "login") {
    // check the login info from DB
    $email = getRequiredPostArgument($conn, "email");
    // TODO password must not be escaped, because it can be weakened
    $password = md5(getRequiredPostArgument($conn, "password"));
    
    login($conn, $email, $password);
  } else if($_POST["type"] === "register") {
    // check register info and insert into DB
    $name = getRequiredPostArgument($conn, "name");
    $surname = getRequiredPostArgument($conn, "surname");
    $email = getRequiredPostArgument($conn, "email");
    // TODO password must not be escaped, because it can be weakened
    $password = md5(getRequiredPostArgument($conn, "password"));
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
    header('Location: ' . $loginPage);
    die();
  }

?>
