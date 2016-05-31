<?php
  session_start();
  require 'functions.php';
  if(!isset($_POST["type"])) {
    header('Location: ' . $loginPage);
    die();
  }
  $conn = connectToDb();
  // TODO move login and signup into functions.php
  if($_POST["type"] === "login") {
    // check the login info from DB
    $email = getRequiredPostArgument($conn, "email");
    $password = md5(getRequiredPostArgument($conn, "password"));
    
    login($conn, $email, $password);
  } else if($_POST["type"] === "register") {
    // check register info and insert into DB
    $name = getRequiredPostArgument($conn, "name");
    $surname = getRequiredPostArgument($conn, "surname");
    $email = getRequiredPostArgument($conn, "email");
    $password = md5(getRequiredPostArgument($conn, "password"));
    //var_dump($password);
    signup($conn, $name, $surname, $email, $password);
  } else {
    header('Location: ' . $loginPage);
    die();
  }

?>
