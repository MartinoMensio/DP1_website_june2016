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
    $reservation = insertNewReservation($conn, $duration, $starting_minute, $starting_hour);
  } else if($_REQUEST["type"] === "remove") {
    //echo 'you want to remove id ='.$_REQUEST["id"];
    $id = $_REQUEST["id"];
    removeReservation($conn, $id);
  } else {
    header('Location: '.'new_reservation.php?error');
    die();
  }  
  
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
    echo "added reservation with start time: $start_time duration: $reservation->duration machine: $reservation->machine";
  } else if($_REQUEST["type"] === "remove") {
    echo "deleted reservation";
  }
?>
</div>
</body>
</html>
