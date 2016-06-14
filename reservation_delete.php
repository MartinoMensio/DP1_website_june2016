<?php
  require 'functions.php';
    // this page requires authentication
  checkAuthentication(true);
  if(!isset($_REQUEST["type"])) {
    goToWithError('Invalid request');
  }
  // Connect to database.
  $conn = connectToDb();
  if($_REQUEST["type"] === "remove") {
    //echo 'you want to remove id ='.$_REQUEST["id"];
    $id = $_REQUEST["id"];
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
  if($_REQUEST["type"] === "remove") {
    echo "deleted reservation";
  }
?>
</h2>
</div>
</body>
</html>