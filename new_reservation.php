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
<?php
  if(isset($_REQUEST["error"])) {
    $error = $_REQUEST["error"];
    echo '<div id="error" class="w3-padding-medium"><h1>';
    echo 'Error: '.htmlentities($error);
    echo '</h1>';
    echo '<button class="w3-btn w3-indigo" type="button" onclick="hideError()">OK</button>';
    echo '</div>';
    echo '<div id="new_form" class="hidden">';
  } else {
    $error = false;
    echo '<div id="new_form" class="visible">';
  }
?>
<form action="reservation_submit.php" method="post">
  <input type="text" value="add" hidden="hidden" name="type" />
  <table>
  <tr><td>Duration (minutes):</td><td><input type="number" min="0" max="1439" required="required" name="duration" placeholder="duration in minutes" /></td></tr>
  <tr><td>Start time:</td><td><input type="time" required="required" name="start_time" /></td></tr>
  </table>
  <input class="w3-btn w3-indigo" type="submit" value="create reservation" />
</form>
</div>
</div>
<script type="text/javascript">
function hideError() {
  document.getElementById('error').className = 'hidden';
  document.getElementById('new_form').className = 'visible';
}
</script>
</body>
</html>
