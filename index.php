<?php
  $host = "us-cdbr-azure-west-c.cloudapp.net";
  $user = "b411bdc8084ca4";
  $pwd = "fdd4ffbb";
  $db = "dp-web-jun16-martinomensio";
  // Connect to database.
  $conn = new mysqli_connect( $host, $user, $pwd, $db);
  if(!conn) {
    die("impossible to connect to database");
  }
  echo 'successfully connected to db';
  $conn->autocommit(false);
  $query = $conn->query("SELECT * FROM users");
  if(!query) {
    $conn->close();
    die("query failed");
  }
  while($row = $query->fetch_object()) {
    var_dump($row);
  }
  $conn->close();
?>
