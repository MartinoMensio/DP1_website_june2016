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
<title>Machine reservation - Profile</title>
<link rel="stylesheet" type="text/css" href="lib/w3.css">
<link rel="stylesheet" type="text/css" href="mystyle.css">
</head>
<body>
<div class="w3-container w3-teal w3-center w3-animate-top">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - Profile</h1>
</div>
<div class="w3-sidenav w3-light-green w3-card-8 w3-animate-left" style="width:25%">
	<?php
    sidenavPrint();
  ?> 
</div>
<div class="w3-animate-right" style="margin-left:25%">
  <table>
<?php
  echo '<tr><td>name:</td><td>'.$_SESSION['name'].'</td><tr>';
  echo '<tr><td>surname:</td><td>'.$_SESSION['surname'].'</td><tr>';
  echo '<tr><td>email:</td><td>'.$_SESSION['email'].'</td><tr>';
?>
</table>
</div>
</body>
</html>
