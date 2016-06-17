<?php
  require 'functions.php';
  // this page does not require authentication
  checkAuthentication(false);
  // if user already logged in, redirect to homepage
  if($authenticated) {
    goToPage($homePage);
  }
?>
<!DOCTYPE html>
<html>

<head>
  <title>Machine reservations - Login</title>
  <link rel="stylesheet" type="text/css" href="lib/w3.css" />
  <link rel="stylesheet" type="text/css" href="mystyle.css" />
  <script src="lib/jquery-1.12.4.min.js"></script>
</head>

<body>
  <div class="w3-container w3-indigo w3-center topbar">
    <h1>Login</h1>
  </div>
  <div class="placeholder">i am not visible</div>
  <div class="w3-sidenav w3-light-blue w3-card-8 w3-animate-left" style="width:25%">
      <?php sidenavPrint(); ?>
  </div>
  <div class="w3-padding-medium">
    <div class="w3-row" style="margin-left:25%">
      <h1 class="w3-red"><noscript>warning: Javascript is disabled, some functions may not work</noscript></h1>
      <?php
        if(isset($_REQUEST['error'])) {
          $error = $_REQUEST['error'];
          echo '<div id="error" class="w3-padding-medium"><h2>';
          echo 'Error: '.htmlentities($error);
          echo '</h2>';
          echo '<button class="w3-btn w3-indigo" type="button" onclick="hideError()">OK</button>';
          echo '</div>';
          echo '<div id="login_form" class="hidden">';
        } else {
          $error = false;
          echo '<div id="login_form" class="visible">';
        }
      ?>
        <div class="w3-half w3-animate-left w3-padding-small">
          <div class="w3-card-8">
            <h1 class="w3-container w3-blue">Login</h1>
            <form action="login_validate.php" method="post" class="w3-padding-medium">
              <input type="text" value="login" hidden="hidden" name="type" />
              <p>
                <input type="email" maxlength="50" required="required" name="email" placeholder="your email" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Username (email)</label>
              </p>
              <p>
                <input type="password" maxlength="50" required="required" name="password" placeholder="password" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Password</label>
              </p>
              <p>
                <input class="w3-btn w3-indigo" type="submit" value="Login" />
              </p>
            </form>
          </div>
        </div>
        <div class="w3-half w3-animate-right w3-padding-small">
          <div class="w3-card-8">
            <h1 class="w3-container w3-blue">Register</h1>
            <form action="login_validate.php" method="post" class="w3-padding-medium">
              <input type="text" value="register" hidden="hidden" name="type" />
              <p>
                <input type="text" maxlength="50" required="required" name="name" placeholder="your name" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Name</label>
              </p>
              <p>
                <input type="text" maxlength="50" required="required" name="surname" placeholder="your surname" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Surname</label>
              </p>
              <p>
                <input type="email" maxlength="50" required="required" name="email" placeholder="your email" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Email</label>
              </p>
              <p>
                <input type="password" maxlength="50" required="required" name="password" placeholder="your new password" class="w3-input w3-hover-light-grey" />
                <label class="w3-label w3-validate">Password</label>
              </p>
              <p>
                <input class="w3-btn w3-indigo" type="submit" value="Register" />
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    function hideError() {
      $('#error').addClass('hidden');
      $('#login_form').removeClass('hidden');
    }
  </script>
</body>

</html>