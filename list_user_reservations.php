<?php
  require 'functions.php';
  // this page requires authentication
  checkAuthentication(true);

  // Connect to database.
  $conn = connectToDb();
?>
<!DOCTYPE html>
<html>

<head>
  <title>Machine reservation - My reservations</title>
  <link rel="stylesheet" type="text/css" href="lib/w3.css" />
  <link rel="stylesheet" type="text/css" href="mystyle.css" />
  <script src="lib/jquery-1.12.4.min.js"></script>
</head>

<body>
  <div class="w3-container w3-indigo w3-center topbar">
    <h1>My reservations</h1>
  </div>
  <div class="placeholder">i am not visible</div>
  <div class="w3-sidenav w3-light-blue w3-card-8 w3-animate-left" style="width:25%">
    <?php sidenavPrint(); ?>
  </div>
  <div class="w3-animate-right w3-padding-medium" style="margin-left:25%" id="content">
    <h1 class="w3-red"><noscript>warning: Javascript is disabled, some functions may not work</noscript></h1>
    <?php
      if(isset($_REQUEST['error'])) {
        $error = $_REQUEST['error'];
        echo '<div id="error" class="w3-padding-medium"><h2>';
        echo 'Error: '.htmlentities($error);
        echo '</h2>';
        echo '<button class="w3-btn w3-indigo" type="button" onclick="hideError()">OK</button>';
        echo '</div>';
        echo '<div id="list_reservations" class="hidden">';
      } else {
        $error = false;
        echo '<div id="list_reservations" class="visible">';
      }
    ?>
      <?php listUserReservations($conn); ?>
    </div>
  </div>
  <script type="text/javascript">
    function hideError() {
      $('#error').addClass('hidden');
      $('#list_reservations').removeClass('hidden');
    }
  </script>
</body>

</html>