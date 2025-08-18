<?php

/**
 * File: logon.php
 * Description: User login page for Centennial District Merit Badges
 * Author: Richard Hall
 * License: Proprietary Software, Copyright 2024 Richard Hall
 */

//defined('IN_APP') or die('Direct access not allowed.');

// Load configuration
require_once 'config.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

// Redirect if already logged in
if (!empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header('Location: ' . SITE_URL . '/MeritBadges/index.php');
  exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$username = $password = '';
$username_err = $password_err = $login_err = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validate CSRF token
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $login_err = 'Invalid request. Please try again.';
  } else {
    // Validate username
    $username = trim($_POST['username'] ?? '');
    if (empty($username)) {
      $username_err = 'Please enter a username.';
    }

    // Validate password
    $password = trim($_POST['password'] ?? '');
    if (empty($password)) {
      $password_err = 'Please enter a password.';
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err) && empty($login_err)) {
      require_once BASE_PATH . 'CMeritBadges.php';
      $CMeritBadges = CMeritBadges::getInstance();
      $conn = $CMeritBadges->getDbConn();

      if (!$conn) {
        error_log('Database connection failed in logon.php');
        $login_err = 'An error occurred. Please try again later.';
      } else {
        $sql = 'SELECT Userid, username, password, enabled, Role FROM users WHERE username = ?';
        if ($stmt = mysqli_prepare($conn, $sql)) {
          mysqli_stmt_bind_param($stmt, 's', $username);
          if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) === 1) {
              mysqli_stmt_bind_result($stmt, $Userid, $username, $hashed_password, $enabled, $Role);
              if (mysqli_stmt_fetch($stmt) && password_verify($password, $hashed_password) && $enabled) {
                // Update LastLogin
                $sql = 'UPDATE users SET LastLogin = ? WHERE Userid = ?';
                if ($stmt_update = mysqli_prepare($conn, $sql)) {
                  $last_login = date('Y-m-d H:i:s');
                  mysqli_stmt_bind_param($stmt_update, 'si', $last_login, $Userid);
                  mysqli_stmt_execute($stmt_update);
                  mysqli_stmt_close($stmt_update);
                }

                // Store session data
                $_SESSION['loggedin'] = true;
                $_SESSION['Userid'] = $Userid;
                $_SESSION['username'] = $username;
                $_SESSION['enabled'] = $enabled;
                $_SESSION['Role'] = $Role;

                header('Location: ' . SITE_URL . '/MeritBadges/index.php');
                exit;
              } else {
                $login_err = 'Invalid username, password, or account not enabled.';
              }
            } else {
              $login_err = 'Invalid username or password.';
            }
          } else {
            error_log('Query execution failed in logon.php');
            $login_err = 'An error occurred. Please try again later.';
          }
          mysqli_stmt_close($stmt);
        } else {
          error_log('Statement preparation failed in logon.php');
          $login_err = 'An error occurred. Please try again later.';
        }
      }
    }
  }
}

// Template loader
function load_template($file)
{
  $path = BASE_PATH . $file;
  if (file_exists($path)) {
    require_once $path;
  } else {
    error_log("Template $file is missing.");
    die('An error occurred. Please try again later.');
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('head.php'); ?>
  <title>Login - <?php echo PAGE_TITLE; ?></title>
  <meta name="description" content="Log in to access Centennial District Merit Badges data.">
</head>

<body>
  <header id="header" class="header sticky-top" role="banner">
    <?php load_template('navbar.php'); ?>
  </header>

  <main id="main-content" class="container py-5" role="main">
    <div class="col-md-6 mx-auto">
      <h1 class="display-5 fw-bold text-center mb-4">Login</h1>
      <p class="text-center fs-4 mb-4">Please fill in your credentials to log in.</p>
      <?php if (!empty($login_err)): ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($login_err); ?></div>
      <?php endif; ?>
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" id="username" name="username" class="form-control <?php echo $username_err ? 'is-invalid' : ''; ?>"
            value="<?php echo htmlspecialchars($username); ?>" required>
          <div class="invalid-feedback"><?php echo $username_err; ?></div>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control <?php echo $password_err ? 'is-invalid' : ''; ?>" required>
          <div class="invalid-feedback"><?php echo $password_err; ?></div>
        </div>
        <div class="text-center py-3">
          <button type="submit" class="btn btn-primary">Log In</button>
        </div>
        <p class="text-center">Don't have an account? <a href="<?php echo SITE_URL; ?>register.php">Sign up now</a>.</p>
      </form>
    </div>
  </main>

  <?php load_template('Footer.php'); ?>
</body>

</html>