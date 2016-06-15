<?php
  require 'functions.php';
  // main page: not authenticated
  checkAuthentication(false);

  // Connect to database.
  $conn = connectToDb();
?>
<!DOCTYPE html>
<html>
<head>
<title>Machine reservation</title>
<link rel="stylesheet" type="text/css" href="lib/w3.css" />
<link rel="stylesheet" type="text/css" href="mystyle.css" />
</head>
<body>
<div class="w3-container w3-indigo w3-center topbar">
  <h1>Machine Reservations</h1>
</div>
<div class="placeholder">i am not visible</div>
<div class="w3-sidenav w3-light-blue w3-card-8 w3-animate-left" style="width:25%">
  <?php
    sidenavPrint();
  ?> 
</div>
<div class="w3-animate-right w3-padding-medium" style="margin-left:25%" id="content">
<h1 class="w3-red"><noscript>warning: Javascript is disabled, some functions may not work</noscript></h1>
<?php
  listAllReservations($conn);
?>
</div>
</body>
</html>
