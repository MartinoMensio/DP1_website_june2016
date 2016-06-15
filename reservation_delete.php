<?php
  require 'functions.php';
    // this page requires authentication
  checkAuthentication(true);
  
  // Connect to database.
  $conn = connectToDb();

  if(!isset($_REQUEST["type"])) {
    goToWithError('Invalid request');
  }
  if($_REQUEST["type"] === "remove") {
    // check and validate id
    $id = $_REQUEST["id"];
    if(filter_var($id, FILTER_VALIDATE_INT) === FALSE) {
      goToWithError('Invalid ID specified');
    }
    removeReservation($conn, $id);
  } else {
    goToWithError('Invalid request');
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
  if($_REQUEST["type"] === "remove") {
    echo "deleted reservation";
  }
?>
</h2>
<a class="w3-btn w3-indigo" href="list_user_reservations.php">Go to my reservations</a>
</div>
</body>
</html>
