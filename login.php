<!DOCTYPE html>
<html>
<head>
<title>Machine reservation</title>
<link rel="stylesheet" type="text/css" href="lib/w3.css">
<link rel="stylesheet" type="text/css" href="mystyle.css">
</head>
<body>
<div class="w3-container w3-teal w3-center w3-animate-top">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - Login</h1>
</div>
<?php
  if(isset($_REQUEST["error"])) {
    $error = $_REQUEST["error"];
    echo '<div id="error" class="w3-padding-medium"><h1>';
    echo 'Error: '.htmlentities($error);
    echo '</h1>';
    echo '<button type="button" onclick="hideError()">OK</button>';
    echo '</div>';
    echo '<div id="login_form" class="hidden">';
  } else {
    $error = false;
    echo '<div id="login_form" class="visible">';
  }
?>
<a href="index.php" class="w3-padding-medium">no login, go to homepage</a>
<div class="w3-sidenav w3-animate-left w3-padding-medium" style="width:50%">
<h1>Login</h1>
<form action="login_validate.php" method="post">
  <input type="text" value="login" hidden="hidden" name="type" />
  <table>
  <tr><td>Username (email):</td><td><input type="email" required="required" name="email" /></td></tr>
  <tr><td>Password:</td><td><input type="password" required="required" name="password" /></td></tr>
  </table>
  <input type="submit" value="Login" />
  
</form>
</div>
<div class=" w3-animate-right w3-padding-medium" style="margin-left:50%">
  <h1>Register</h1>
  <form action="login_validate.php" method="post">
    <input type="text" value="register" hidden="hidden" name="type" />
    <table>
    <tr><td>Name:</td><td><input type="text" required="required" name="name" /></td></tr>
    <tr><td>Surname:</td><td><input type="text" required="required" name="surname" /></td></tr>
    <tr><td>Email:</td><td><input type="email" required="required" name="email" /></td></tr>
    <tr><td>Password:</td><td><input type="password" required="required" name="password" /></td></tr>
    </table>
    <input type="submit" value="Register" />
  </form>
</div>
</div>
<script type="text/javascript">
function hideError() {
  document.getElementById('error').className = 'hidden';
  document.getElementById('login_form').className = 'visible';
}
</script>
</body>
</html>