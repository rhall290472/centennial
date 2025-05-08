<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

/**
 * File: index.php
 * Description: Website to support Centennial District Merit Badge Counselors
 * Author: Richard Hall
 * License: Proprietary Software
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';


use Dotenv\Dotenv;
use meritbadges\Classes\User;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../..');
$dotenv->load();

// Load configs
//$sharedConfig = require __DIR__ . '/../../../shared/config/database.php';
//$siteConfig = require __DIR__ . '/../../config/database.php';
//$config = array_merge($sharedConfig, $siteConfig);

// Initialize classes
//$user = new User($config);

// Route request
//require __DIR__ . '/../../src/routes.php';





// Require critical dependencies
if (file_exists(BASE_PATH . './src/Classes/CMeritBadges.php')) {
  //use CMeritBadges.php;
  //require_once BASE_PATH . './src/Classes/CMeritBadges.php';
} else {
  error_log('Critical dependency CMeritBadges.php is missing.');
  die('An error occurred. Please try again later.');
}

// Template loader function
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
</head>

<body>
  <header id="header" class="header sticky-top" role="banner">
    <?php load_template('navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php load_template('sidebar.php'); ?>
      <main id="main-content" class="col-10 py-3" role="main">
        <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
          <h1 class="display-5 fw-bold">Centennial District Merit Badges</h1>
          <p class="fs-4">Review Merit Badge Counselors for the Centennial District</p>
          </hr>
          <iframe src="https://www.google.com/maps/d/embed?mid=1Hj3PV-LAAKDU5-IenX9esVcbfx1_Ruc&ehbc=2E312F" width="100%" height="800px"></iframe>
        </div>
      </main>
    </div>
  </div>

  <?php load_template('Footer.php'); ?>
</body>

</html>