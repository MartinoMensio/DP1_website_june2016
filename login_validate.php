<?php
  session_start();
  require 'functions.php';
  if(!isset($_POST['type'])) {
    goToWithError('Incorrect request');
  }
  $conn = connectToDb();
  if($_POST['type'] === 'login') {
    $email = getRequiredPostArgument($conn, 'email');
    // password must not be escaped, because it can be weakened
    $password = sha1(getRequiredPostArgument($conn, 'password', false));
    // check the login info from DB
    login($conn, $email, $password);
  } else if($_POST['type'] === 'register') {
    $name = getRequiredPostArgument($conn, 'name');
    $surname = getRequiredPostArgument($conn, 'surname');
    $email = getRequiredPostArgument($conn, 'email');
    // password must not be escaped, because it can be weakened
    $password = sha1(getRequiredPostArgument($conn, 'password', false));
    // check the parameters
    if(strlen($name) > 50) {
      goToWithError('Name too long');
    }
    if(strlen($surname) > 50) {
      goToWithError('Surname too long');
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      goToWithError('Invalid email');
    }
    // try to signup
    signup($conn, $name, $surname, $email, $password);
  } else {
    goToWithError('Incorrect request');
  }

?>
