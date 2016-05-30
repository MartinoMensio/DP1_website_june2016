<?php
require 'functions.php';

  // Connect to database.
  $conn = connectToDb();
  listAllReservations($conn);
?>
