<?php
require 'functions.php';
    // this page requires authentication
  checkAuthentication(true);
  if(!isset($_REQUEST["type"])) {
    header('Location: ' . 'new_reservation.php');
    die();
  }
  // Connect to database.
  $conn = connectToDb();
  if($_REQUEST["type"] === "add") {
    $duration = getRequiredPostArgument($conn, "duration");
    $start_time = getRequiredPostArgument($conn, "start_time");
    $pieces = explode(":", $start_time);
    $starting_minute = $pieces[1];
    $starting_hour = $pieces[0];
    // TODO check values of three parameters
    // TODO check overlapping reservations (SELECT locking)
    // TODO machine choose
    $machine = 1;
    
    $stmt = $conn->prepare("INSERT INTO reservations(duration, starting_hour, starting_minute, machine, user_id) VALUES(?, ?, ?, ?, ?)");
    if(!$stmt) {
      echo $conn->error;
      header('Location: '.'new_reservation.php?error');
      die();
    }
    $stmt->bind_param("iiiii", $duration, $starting_hour, $starting_minute, $machine, $_SESSION["user_id"]);
    if(!$stmt->execute()) {
      header('Location: '.'new_reservation.php?error');
      die();
    }
  } else if($_REQUEST["type"] === "remove") {
    //echo 'you want to remove id ='.$_REQUEST["id"];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    if(!$stmt) {
      echo $conn->error;
      header('Location: '.'list_user_reservations.php?error');
      die();
    }
    $stmt->bind_param("i", $_REQUEST["id"]);
    if(!$stmt->execute()) {
      header('Location: '.'list_user_reservations.php?error');
      die();
    }
  } else {
    header('Location: '.'new_reservation.php?error');
    die();
  }
  
  $conn->commit();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Machine reservation - New reservation</title>
<link rel="stylesheet" type="text/css" href="lib/w3.css">
<link rel="stylesheet" type="text/css" href="mystyle.css">
</head>
<body>
<div class="w3-container w3-teal w3-center w3-animate-top">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - New reservation</h1>
</div>
<div class="w3-sidenav w3-light-green w3-card-8 w3-animate-left" style="width:25%">
	<?php
    sidenavPrint();
  ?> 
</div>
<div class="w3-animate-right" style="margin-left:25%">
<?php
  if($_REQUEST["type"] === "add") {
    echo "added reservation with start time: $start_time duration: $duration machine: $machine";
  } else if($_REQUEST["type"] === "remove") {
    echo "deleted reservation";
  }
?>
</div>
</body>
</html>
