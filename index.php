<?php
  $host = "us-cdbr-azure-west-c.cloudapp.net";
  $user = "b411bdc8084ca4";
  $pwd = "fdd4ffbb";
  $db = "dp-web-jun16-martinomensio";
  // Connect to database.
  try {
    $conn = new PDO( "mysql:host=$host;dbname=$db", $user, $pwd);
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
  }
  catch(Exception $e){
    die(var_dump($e));
  }
  echo 'successfully connected to db';
?>
