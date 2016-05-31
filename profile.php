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
</head>
<body>
<div class="w3-container w3-teal w3-center w3-animate-top">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - Profile</h1>
</div>
<div class="w3-sidenav w3-light-green w3-card-8 w3-animate-left" style="width:25%">
	<?php
    echo '<a href="index.php">Homepage</a>';
    sidenavPrint();
  ?> 
</div>
<div class="w3-animate-right" style="margin-left:25%">
... page content ...
<!-- iframe sucks, need a partial for sidenav and a partial for content -->
<?php
  echo 'name: '.$_SESSION['name'].'<br />';
  echo 'surname: '.$_SESSION['surname'].'<br />';
  echo 'email: '.$_SESSION['email'].'<br />';
?>
</div>
</body>
</html>
