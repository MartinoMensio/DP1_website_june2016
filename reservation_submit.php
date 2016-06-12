<?php
  require 'functions.php';
    // this page requires authentication
  checkAuthentication(true);
  if(!isset($_REQUEST["type"])) {
    goToWithError('new_reservation.php', 'Invalid request');
  }
  // Connect to database.
  $conn = connectToDb();
  if($_REQUEST["type"] === "add") {
    $duration = getRequiredPostArgument($conn, "duration");
    $start_time = getRequiredPostArgument($conn, "start_time");
    // TODO check values of duration and start time
    $pieces = explode(":", $start_time);
    if (count($pieces) != 2) {
      goToWithError('new_reservation.php', 'Invalid format for starting hour');
    }
    $starting_minute = $pieces[1];
    $starting_hour = $pieces[0];
    $start_time = sprintf("%02d:%02d", $pieces[0], $pieces[1]);
    //die($start_time);
    if ($starting_hour < 0 || $starting_hour > 23 || $starting_minute < 0 || $starting_minute > 59) {
      goToWithError('new_reservation.php', 'Invalid starting hour');
    }
    if ($duration < 0 || $duration > 60 * 24 - 1) {
      goToWithError('new_reservation.php', 'Invalid duration');
    }
    if ($starting_hour * 60 + $starting_minute + $duration > 60*24 - 1) {
      goToWithError('new_reservation.php', 'The reservation finishes the next day');
    }
    $reservation = insertNewReservation($conn, $duration, $starting_minute, $starting_hour);
  } else if($_REQUEST["type"] === "remove") {
    //echo 'you want to remove id ='.$_REQUEST["id"];
    $id = $_REQUEST["id"];
    removeReservation($conn, $id);
  } else {
    goToWithError('new_reservation.php', 'Invalid request');
  }  
  
?>
<!DOCTYPE html>
<html>
<head>
<title>Machine reservation - New reservation</title>
<link rel="stylesheet" type="text/css" href="lib/w3.css" />
<link rel="stylesheet" type="text/css" href="mystyle.css" />
</head>
<body>
<div class="w3-container w3-indigo w3-center topbar">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - New reservation</h1>
</div>
<div class="placeholder">i am not visible</div>
<div class="w3-sidenav w3-light-blue w3-card-8 w3-animate-left" style="width:25%">
	<?php
    sidenavPrint();
  ?> 
</div>
<div class="w3-animate-right w3-padding-medium" style="margin-left:25%">
<h1 class="w3-red"><noscript>warning: Javascript is disabled, some functions may not work</noscript></h1>
<h2>
<?php
  if($_REQUEST["type"] === "add") {
    echo "added reservation beginning at: $start_time duration: $reservation->duration minutes on machine: $reservation->machine";
  } else if($_REQUEST["type"] === "remove") {
    echo "deleted reservation";
  }
?>
</h2>
</div>
</body>
</html>
