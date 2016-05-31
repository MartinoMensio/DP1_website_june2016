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
    $stmt = $conn->prepare("SELECT name, surname, id FROM users WHERE email = ? AND password = ?");
    if(!$stmt) {
      var_dump($result);
      echo $conn->error;
      header('Location: '.'login.html');
      die();
    }
    $stmt->bind_param("ss", $email, $password);
    $result = $stmt->execute();
    if(!$result) {
      header('Location: '.'login.html');
      die();
    }
    //echo mysqli_num_rows($result);
    $stmt->bind_result($name, $surname, $id);
    if(!$stmt->fetch()) {
      header('Location: '.'login.html');
      die();
    }
    //var_dump($name);
    //var_dump($surname);
  } else {
    // check register info and insert into DB
    $name = getRequiredPostArgument($conn, "name");
    $surname = getRequiredPostArgument($conn, "surname");
    $email = getRequiredPostArgument($conn, "email");
    $password = md5(getRequiredPostArgument($conn, "password"));
    //var_dump($password);
    $stmt = $conn->prepare("INSERT INTO users(name, surname, email, password) VALUES(?, ?, ?, ?)");
    if(!$stmt) {
      var_dump($result);
      echo $conn->error;
      header('Location: '.'login.html');
      die();
    }
    $stmt->bind_param("ssss", $name, $surname, $email, $password);
    if(!$stmt->execute()) {
      header('Location: '.'login.html');
      die();
    }
    $id = $conn->insert_id;
  }
  
  $conn->commit();
  // check post parameters and type: register or 
  $_SESSION['timeout'] = time();
  $_SESSION['name'] = $name;
  $_SESSION['surname'] = $surname;
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;
  $_SESSION['user_id'] = $id;
  //var_dump($_SESSION);
  header('Location: ' . 'index.php');
  die();
?>
