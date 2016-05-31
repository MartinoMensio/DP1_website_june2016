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
<div class="w3-container w3-teal w3-center w3-animate-top">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - Personal</h1>
</div>
<div class="w3-sidenav w3-light-green w3-card-8 w3-animate-left" style="width:25%">
	<?php
    sidenavPrint();
  ?> 
</div>
<div class="w3-animate-right w3-padding-medium" style="margin-left:25%" id="content">
<?php
  listUserReservations($conn);
?>
</div>
<script type="text/javascript">
function remove_reservation(id) {
  location = "reservation_submit.php?type=remove&id="+id;
}
</script>
</body>
</html>
