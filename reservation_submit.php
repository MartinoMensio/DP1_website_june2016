<?php
  require 'functions.php';
    // this page requires authentication
  checkAuthentication(true);
  // Connect to database.
  $conn = connectToDb();

  if(!isset($_REQUEST['type'])) {
    goToWithError('Invalid request');
  }
  if($_REQUEST['type'] === 'add') {
    $duration = getRequiredPostArgument($conn, 'duration');
    $start_time = getRequiredPostArgument($conn, 'start_time');
    if(filter_var($duration, FILTER_VALIDATE_INT) === FALSE) {
      goToWithError('Duration must be an integer');
    }
    if($duration <= 0 || $duration >= 60*24) {
      goToWithError('Duration must be between 0 and 1440 (bounds not included)');
    }
    // $start_time is a string in the form '12:05'
    $pieces = explode(':', $start_time);
    if (count($pieces) != 2) {
      goToWithError('Invalid format for starting time');
    }
    $starting_minute = $pieces[1];
    $starting_hour = $pieces[0];
    // starting minute can be '05', FILTER_VALIDATE_INT rejects it
    if(ctype_digit($starting_minute) === FALSE) {
      goToWithError('Minutes of starting time must be integer');
    }
    if($starting_minute < 0 || $starting_minute >= 60) {
      goToWithError('Minutes of starting time have an invalid value');
    }
    // starting hour can be '05', FILTER_VALIDATE_INT rejects it
    if(ctype_digit($starting_hour) === FALSE) {
      goToWithError('Hours of starting time must be integer');
    }
    if($starting_hour < 0 || $starting_hour >= 24) {
      goToWithError('Hours of starting time have an invalid value');
    }
    // rebuild the string in order to be sure that is not like '1:5' but '01:05'
    $start_time = sprintf("%02d:%02d", $pieces[0], $pieces[1]);

    if ($starting_hour < 0 || $starting_hour > 23 || $starting_minute < 0 || $starting_minute > 59) {
      goToWithError('Invalid starting hour');
    }
    if ($duration < 0 || $duration > 60 * 24 - 1) {
      goToWithError('Invalid duration');
    }
    if ($starting_hour * 60 + $starting_minute + $duration > 60*24 - 1) {
      goToWithError('The reservation finishes the next day');
    }
    $reservation = insertNewReservation($conn, $duration, $starting_minute, $starting_hour);
  } else {
    goToWithError('Invalid request');
  }  
  
?>
<!DOCTYPE html>
<html>

<head>
  <title>Machine reservations - Reservation added</title>
  <link rel="stylesheet" type="text/css" href="lib/w3.css" />
  <link rel="stylesheet" type="text/css" href="mystyle.css" />
</head>

<body>
  <div class="w3-container w3-indigo w3-center topbar">
    <h1>Reservation added</h1>
  </div>
  <div class="placeholder">i am not visible</div>
  <div class="w3-sidenav w3-light-blue w3-card-8 w3-animate-left" style="width:25%">
    <?php sidenavPrint(); ?>
  </div>
  <div class="w3-animate-right w3-padding-medium" style="margin-left:25%">
    <h1 class="w3-red"><noscript>warning: Javascript is disabled, some functions may not work</noscript></h1>
    <h2>
      <?php
        if($_REQUEST['type'] === 'add') {
          echo "added reservation beginning at: $start_time duration: $reservation->duration minutes on machine: $reservation->machine";
        }
      ?>
    </h2>
    <a class="w3-btn w3-indigo" href="list_user_reservations.php">Go to my reservations</a>
  </div>
</body>

</html>