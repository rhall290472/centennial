<?php
ob_start();
load_class(BASE_PATH . '/src/Classes/CEagle.php');
$CEagle = CEagle::getInstance();
load_class(BASE_PATH . '/src/Classes/CEmail.php');
$CEmail = CEmail::getInstance();

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!empty($_POST['website']) || !empty($_POST['fax_number'])) {
    // Bot detected – silently fail or log
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid submission. Please try again.'];
    header("Location: index.php?page=register");
    exit;
  }
  $min_seconds = 6; // adjust 5–12 seconds
  if (isset($_POST['form_start_time'])) {
    $time_taken = time() - (int)$_POST['form_start_time'];
    if ($time_taken < $min_seconds) {
      // Too fast → bot
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Please fill the form more carefully.'];
      header("Location: index.php?page=register");
      exit;
    }
  }

  // Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
    $username_err = "Username can only contain letters, numbers, and underscores.";
  } else {
    // Prepare a select statement
    $sql = "SELECT Userid FROM users WHERE username = ?";

    if ($stmt = mysqli_prepare($CEagle->getDbConn(), $sql)) {
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
    }
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Password must have atleast 6 characters.'];
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

    if ($stmt = mysqli_prepare($CEagle->getDbConn(), $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

      // Set parameters
      $param_username = $username;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

      // Attempt to execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        // 
        $msg = "Your account request has been received. An administrator will review it shortly.";
        $CEagle->function_alert($msg);

        // Prepare admin notification email
        $subject    = "New Eagle Account Request - " . htmlspecialchars($username);
        $adminEmail = "richard.hall@centennialdistrict.co";
        $bodyHtml = "
    <h2 style=\"color: #006400;\">New Registration Request</h2>
    <p>A new user has submitted an account request:</p>
    <table style=\"border-collapse: collapse; width: 100%; max-width: 600px;\">
        <tr><th style=\"text-align: left; padding: 8px; border-bottom: 1px solid #ddd;\">Field</th>
            <th style=\"text-align: left; padding: 8px; border-bottom: 1px solid #ddd;\">Value</th></tr>
        <tr><td style=\"padding: 8px;\">Username</td>
            <td style=\"padding: 8px;\"><strong>" . htmlspecialchars($username) . "</strong></td></tr>
        <tr><td style=\"padding: 8px;\">Requested at</td>
            <td style=\"padding: 8px;\">" . date('Y-m-d H:i:s') . "</td></tr>
        <tr><td style=\"padding: 8px;\">IP address</td>
            <td style=\"padding: 8px;\">" . $_SERVER['REMOTE_ADDR'] . "</td></tr>
    </table>
    <p style=\"margin-top: 20px;\">
        <a href=\"" . SITE_URL . "/index.php?page=home\" style=\"background:#006400;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;\">Review Accounts</a>
    </p>
    <hr style=\"border: 0; border-top: 1px solid #eee; margin: 20px 0;\">
    <small style=\"color: #777;\">This is an automated message from the Centennial District Eagle system.</small>";

        $sendResult = $CEmail->send(
          $adminEmail,               // to
          $subject,                  // subject
          $bodyHtml,                 // html body
          strip_tags($bodyHtml)      // plain text fallback (optional but recommended)
        );

        // Optional: log if sending failed (so you know something went wrong)
        if ($sendResult !== true) {
          error_log("Failed to send admin notification email for new registration '$username': " . $sendResult);
        }

        // Redirect to a clean landing page
        $CEagle->GotoURL("index.php");
        header("Location: index.php?page=index");
        exit;
      } else {
        // Better error handling: use session feedback instead of raw echo
        $_SESSION['feedback'] = [
          'type'    => 'danger',
          'message' => 'Oops! Something went wrong during registration. Please try again or contact support.'
        ];
        header("Location: index.php?page=register");
        exit;
      }
      // Send the email

      //   $str = sprintf("New Eagle registration, at %s\n", Date('Y-m-d H:i:s'));
      //   error_log($str, 1, "richard.hall@centennialdistrict.co");
      //   header("Location: index.php?page=index");
      // } else {
      //   echo "Oops! Something went wrong. Please try again later.";
      // }

      // Close statement
      mysqli_stmt_close($stmt);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">


<body>
  <center>
    <div class="wrapper-logon">
      <h2>Sign Up</h2>
      <p>Please fill this form to create an account.</p>
      <p>Acounts are reserved for those involved in the administration of the District advancment program. If you are not directly involved with
        the District advancement program please don't submit an account request becuase it will be denied.</p>
      <form action="index.php?page=register" method="post">
        <input type="hidden" name="form_start_time" value="<?php echo time(); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
          <span class="invalid-feedback"><?php echo $username_err; ?></span>
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

        <!-- Honeypot fields – humans should never see/fill these -->
        <div style="display:none;">
          <label for="website">Website (leave empty)</label>
          <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off">
        </div>

        <!-- Optional second one with misleading name -->
        <div style="position:absolute; left:-9999px;">
          <input type="text" name="fax_number" value="" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-group py-4">
          <input type="submit" class="btn btn-primary" value="Submit">
          <input type="reset" class="btn btn-secondary ml-2" value="Reset">
        </div>

        <p>Already have an account? <a href="index.php?page=logon">Login here</a>.</p>
      </form>
    </div>
  </center>
</body>
<?php ob_end_flush(); ?>
</html>