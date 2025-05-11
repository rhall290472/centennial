<?php
if (!session_id()) {
  session_start();
}
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #   Copyright 2024 - Richard Hall                                        #  !
!  #                                                                        #  !
!  #   The information contained herein is the property of Richard          #  !
!  #   Hall, and shall not be copied, in whole or in part, or               #  !
!  #   disclosed to others in any manner without the express written        #  !
!  #   authorization of Richard Hall.                                       #  !
!  #                                                                        #  !
!  #                                                                        #  !
! /##########################################################################\ !
!//                                                                          \\!
!/                                                                            \!
!==============================================================================!
*/
include_once "CMBCollege.php";
$CMBCollege = CMBCollege::getInstance();

/* Check if the user is already logged in, if yes then redirect him to welcome page */
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  CMBCollege::GotoURL("./index.php");
  exit;
}

/* Define variables and initialize with empty values */
$username = $password = "";
$username_err = $password_err = $login_err = "";
$enabled = false;

/* Processing form data when form is submitted */
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["SubmitForm"]) {

  // Check if username is empty
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter username.";
  } else {
    $username = trim($_POST["username"]);
  }

  // Check if password is empty
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Validate credentials
  if (empty($username_err) && empty($password_err)) {
    // Prepare a select statement
    $sql = "SELECT Userid, username, password, enabled, Role FROM users WHERE username = ?";

    if ($stmt = mysqli_prepare($CMBCollege->getDbConn(), $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_username);

      // Set parameters
      $param_username = $username;

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);

        // Check if username exists, if yes then verify password
        if (mysqli_stmt_num_rows($stmt) == 1) {
          // Bind result variables
          mysqli_stmt_bind_result($stmt, $Userid, $username, $hashed_password, $enabled, $Role);
          if (mysqli_stmt_fetch($stmt)) {
            //if (password_verify($password, $hashed_password) && $enabled == true) {
            if (password_verify($password, $hashed_password)) {
              // Password is correct, so start a new session
              if (!session_id()) {
                session_start();
              }
              // Store data in session variables
              $_SESSION["loggedin"] = true;
              $_SESSION["Userid"] = $Userid;
              $_SESSION["username"] = $username;
              $_SESSION["enabled"] = $enabled;
              $_SESSION["Role"] = $Role;

              $datetime = new DateTime();
              $Last = $datetime->format('Y-m-d H:i:s');
              $sqlLastLogedin = "UPDATE `users` SET `LastLogin`='$Last' WHERE `username`='$username'";
              $CMBCollege->doQuery($sqlLastLogedin);

              // Redirect user to welcome page
              CMBCollege::GotoURL("./index.php");
            } else {
              // Password is not valid, display a generic error message
              $login_err = "Invalid username or password or your account has not been enabled";
            }
          }
        } else {
          // Username doesn't exist, display a generic error message
          $login_err = "Invalid username or password.";
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
</head>

<body>
  <?php include 'header.php'; ?>
  <center>
    <div class="wrapper-logon">
      <h2>Login</h2>
      <p>Please fill in your credentials to login.</p>


      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
          <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
          <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group py-3">
          <input type="submit" class="btn btn-primary" name="SubmitForm" value="Login">
        </div>
        <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
      </form>
    </div>
  </center>

  <?php include('Footer.php'); ?>
</body>

</html>

