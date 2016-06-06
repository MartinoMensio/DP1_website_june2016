<?php
// main page: not authenticated
  require 'functions.php';
  
  checkAuthentication(true);

  // Connect to database.
  $conn = connectToDb();
  //listAllReservations($conn);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Machine reservation - Personal</title>
<link rel="stylesheet" type="text/css" href="lib/w3.css">
<link rel="stylesheet" type="text/css" href="mystyle.css">
</head>
<body>
<div class="w3-container w3-indigo w3-center w3-animate-top">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - Personal</h1>
  <h1 class="w3-red"><noscript>warning: Javascript is disabled, some functions may not work</noscript></h1>
</div>
<div class="w3-sidenav w3-light-blue w3-card-8 w3-animate-left" style="width:25%">
	<?php
    sidenavPrint();
  ?> 
</div>
<div class="w3-animate-right w3-padding-medium" style="margin-left:25%" id="content">
<?php
  if(isset($_REQUEST["error"])) {
    $error = $_REQUEST["error"];
    echo '<div id="error" class="w3-padding-medium"><h1>';
    echo 'Error: '.htmlentities($error);
    echo '</h1>';
    echo '<button class="w3-btn w3-indigo" type="button" onclick="hideError()">OK</button>';
    echo '</div>';
    echo '<div id="list_reservations" class="hidden">';
  } else {
    $error = false;
    echo '<div id="list_reservations" class="visible">';
  }
?>
<?php
  listUserReservations($conn);
?>
</div>
<script type="text/javascript">
function remove_reservation(id) {
  location = "reservation_submit.php?type=remove&id="+id;
}
function hideError() {
  document.getElementById('error').className = 'hidden';
  document.getElementById('list_reservations').className = 'visible';
}
</script>
</body>
</html>
