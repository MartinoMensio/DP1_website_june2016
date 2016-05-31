<?php
// main page: not authenticated
  require 'functions.php';
  
  // this page requires authentication
  checkAuthentication(true);

  // Connect to database.
  $conn = connectToDb();
  //listAllReservations($conn);
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
<form action="reservation_submit.php" method="post">
  <input type="text" value="add" hidden="hidden" name="type" />
  <table>
  <tr><td>Duration (minutes):</td><td><input type="number" min="0" max="1439" required="required" name="duration" /></td></tr>
  <tr><td>Start time:</td><td><input type="time" required="required" name="start_time" /></td></tr>
  </table>
  <input type="submit" value="submit" />
</form>
</div>
</body>
</html>
