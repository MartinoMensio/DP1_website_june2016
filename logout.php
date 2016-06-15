<?php
  // delete everything and redirect to homepage 
  session_start();
  session_unset();
  session_destroy();
  header('Location: ' . 'index.php');
?>