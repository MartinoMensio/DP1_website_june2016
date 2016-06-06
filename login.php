<?php
  require 'functions.php';
  // this page requires authentication
  checkAuthentication(false);
?>
<!DOCTYPE html>
<html>
<head>
<title>Machine reservation</title>
<link rel="stylesheet" type="text/css" href="lib/w3.css">
<link rel="stylesheet" type="text/css" href="mystyle.css">
</head>
<body>
<div class="w3-container w3-indigo w3-center w3-animate-top">
<!-- the title must be dynamic -->
	<h1>Machine Reservations - Login</h1>
  <h1 class="w3-red"><noscript>warning: Javascript is disabled, some functions may not work</noscript></h1>
</div>
<div class="w3-sidenav w3-light-blue w3-card-8 w3-animate-left" style="width:25%">
	<?php
    sidenavPrint();
  ?> 
</div>
<div class="w3-row" style="margin-left:25%">
  <?php
  if(isset($_REQUEST["error"])) {
    $error = $_REQUEST["error"];
    echo '<div id="error" class="w3-padding-medium"><h1>';
    echo 'Error: '.htmlentities($error);
    echo '</h1>';
    echo '<button class="w3-btn w3-indigo" type="button" onclick="hideError()">OK</button>';
    echo '</div>';
    echo '<div id="login_form" class="hidden">';
  } else {
    $error = false;
    echo '<div id="login_form" class="visible">';
  }
?>
  <div class="w3-half w3-animate-left w3-padding-medium" style="width:50%">
    <h1>Login</h1>
    <form action="login_validate.php" method="post">
      <input type="text" value="login" hidden="hidden" name="type" />
      <table>
      <tr><td>Username (email):</td><td><input type="email" maxlength="50" required="required" name="email" placeholder="your email" /></td></tr>
      <tr><td>Password:</td><td><input type="password" maxlength="50" required="required" name="password" placeholder="password" /></td></tr>
      </table>
      <input class="w3-btn w3-indigo" type="submit" value="Login" />
      
    </form>
  </div>
  <div class="w3-half w3-animate-right w3-padding-medium" >
    <h1>Register</h1>
    <form action="login_validate.php" method="post">
      <input type="text" value="register" hidden="hidden" name="type" />
      <table>
      <tr><td>Name:</td><td><input type="text" maxlength="50" required="required" name="name" placeholder="your name" /></td></tr>
      <tr><td>Surname:</td><td><input type="text" maxlength="50" required="required" name="surname" placeholder="your surname" </td></tr>
      <tr><td>Email:</td><td><input type="email" maxlength="50" required="required" name="email" placeholder="your email" /></td></tr>
      <tr><td>Password:</td><td><input type="password" maxlength="50" required="required" name="password" placeholder="your new password" /></td></tr>
      </table>
      <input class="w3-btn w3-indigo" type="submit" value="Register" />
    </form>
  </div>
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