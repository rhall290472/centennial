<?php
  // Secure session start
  if (session_status() === PHP_SESSION_NONE) {
    session_start([
      'cookie_httponly' => true,
      'use_strict_mode' => true,
      'cookie_secure' => isset($_SERVER['HTTPS'])
    ]);
  }
  
  /*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #   Copyright 2017-2024 - Richard Hall                                   #  !
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

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = $confirm_relationship = "";
$username_err = $password_err = $confirm_password_err = $email_err = $confirm_relationship_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get users IP address
  $ip = isset($_SERVER['HTTP_CLIENT_IP'])
    ? $_SERVER['HTTP_CLIENT_IP']
    : (isset($_SERVER['HTTP_X_FORWARDED_FOR'])
      ? $_SERVER['HTTP_X_FORWARDED_FOR']
      : $_SERVER['REMOTE_ADDR']);

  // Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
    $username_err = "Username can only contain letters, numbers, and underscores.";
  } else {
    // Prepare a select statement
    $sql = "SELECT Userid FROM users WHERE username = ?";

    if ($stmt = mysqli_prepare($CMBCollege->getDbConn(), $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_username);

      // Set parameters
      $param_username = trim($_POST["username"]);

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        /* store result */
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
          $username_err = "This username is already taken.";
        } else {
          $username = trim($_POST["username"]);
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    } else {
      $strErr = "ERROR: mysqli_prepare() failed - " . $sql . " " . __FILE__ . ", " . __LINE__;
      error_log($strErr);
      $CMBCollege::function_alert("Internal Error, this has been reported.");
      $CMBCollege->GotoURL('./index.php');
      exit();
    }
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have atleast 6 characters.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Validate confirm password
  if (empty(trim($_POST["confirm_password"]))) {
    $confirm_password_err = "Please confirm password.";
  } else {
    $confirm_password = trim($_POST["confirm_password"]);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "Password did not match.";
    }
  }

  // Check input errors before inserting in database
  if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

    // Prepare an insert statement
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

    if ($stmt = mysqli_prepare($con, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

      // Set parameters
      $param_username = $username;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        // 
        $msg = "Once your account has been enabled you will be able to log in.";
        $CMBCollege->function_alert($msg);
        $str = sprintf("New Merit Badge College registration, at %s\n", Date('Y-m-d H:i:s'));
        error_log($str, 1, "richard.hall@centennialdistrict.co");
        $CMBCollege->GotoURL("index.php");
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    } else {
      $strErr = "ERROR: mysqli_prepare() failed - " . $sql . " " . __FILE__ . ", " . __LINE__;
      error_log($strErr);
      $CMBCollege::function_alert("Internal Error, this has been reported.");
      $CMBCollege->GotoURL('./index.php');
      exit();
    }
  }

  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter a email.";
  } elseif (strlen(trim($_POST["email"])) < 6) {
    $email_err = "email must have atleast 6 characters.";
  } else {
    $email = trim($_POST["email"]);
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have atleast 6 characters.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Validate confirm password
  if (empty(trim($_POST["confirm_password"]))) {
    $confirm_password_err = "Please confirm password.";
  } else {
    $confirm_password = trim($_POST["confirm_password"]);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "Password did not match.";
    }
  }

    // Validate confirm relatiosnhip to Scouting
    if (empty(trim($_POST["confirm_relationship"]))) {
      $confirm_relationship_err = "Please enter relationship.";
    } else {
      $confirm_relationship = trim($_POST["confirm_relationship"]);
      }
  
  // Check input errors before inserting in database
  if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && 
      empty($email_err) && empty($confirm_relationship_err) ){

    // Prepare an insert statement
    $sql = "INSERT INTO users (username, password, email, ip) VALUES (?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($CMBCollege->getDbConn(), $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $param_email, $param_ip);

      // Set parameters
      $param_username = $username;
      $param_email = $email;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      $param_ip = $ip;

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        // 
        $msg = "You can now log in.";
        CMBCollege::function_alert($msg);
        $str = sprintf("New GOAT registration, on %s - User: %s - Password: %s \n", Date('Y-m-d H:i:s'),
          $param_username, $param_password);
        error_log($str, 1, "richard.hall@centennialdistrict.co");
        CMBCollege::GotoURL("./logon.php");
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Close connection
  mysqli_close($CMBCollege->getDbConn());

}
?>





<!DOCTYPE html>
<html lang="en">

<head>
  <?php include('head.php'); ?>
</head>

<body>
<?php include 'header.php'; ?>
<center>
    <div class="wrapper-logon">
      <h2>Sign Up</h2>
      <p>Please fill this form to create an account.</p>
      </br>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
          <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
          <span class="invalid-feedback"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
          <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
          <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>
        <div class="form-group">
          <label>What is your relationship to Scouting?</label>
          <select class='form-control' name='confirm_relationship'required>
          <option value=""></option>
          <option value="Counselor">Counselor</option>
          <option value="Youth">Youth</option>
          <option value="Parent">Parent</option>
          </select>

          <!-- <input type="relationship" name="confirm_relationship" class="form-control <?php echo (!empty($confirm_relationship_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_relationship; ?>"> -->
          <span class="invalid-feedback"><?php echo $confirm_relationship_err; ?></span>
        </div>
        <div class="form-group py-3">
          <input type="submit" class="btn btn-primary" value="Submit">
          <input type="reset" class="btn btn-secondary ml-2" value="Reset">
        </div>
        <p>Already have an account? <a href="logon.php">Login here</a>.</p>
      </form>
    </div>
    </div>
  </center>

  <?php include('Footer.php'); ?>
</body>

</html>