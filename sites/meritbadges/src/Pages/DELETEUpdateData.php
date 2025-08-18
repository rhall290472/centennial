<?php

declare(strict_types=1);

// Start session with secure settings
session_start([
  'cookie_httponly' => true,
  'cookie_secure' => true, // Enable if using HTTPS
  'use_strict_mode' => true,
]);

/**
 * File: index.php
 * Description: Website to support Centennial District Advancement Data
 * Author: Richard Hall
 * Copyright: 2017-2024 Richard Hall
 * License: Proprietary, see LICENSE file for details
 */

//use App\CAdmin; // Assuming a PSR-4 namespace structure

// Load dependencies (use Composer autoloader if possible)
require_once './config.php';
require_once './sqlStatements.php';
require_once './CAdmin.php';

$cAdmin = CAdmin::getInstance();

// Secure session check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $cAdmin->GotoURL('/index.php');
  exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
  $CAdmin = CAdmin::getInstance();
} catch (Exception $e) {
  // Log error and show user-friendly message
  error_log('Failed to initialize CAdmin: ' . $e->getMessage());
  die('An error occurred. Please try again later.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include './head.php'; ?>
  <link rel="stylesheet" href="/css/styles.css"> <!-- External CSS -->
</head>

<body>
  <?php include './navbar.php'; ?>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include './sidebar.php'; ?>
      <div class="col py-3">
        <div class="container px-lg-5">
          <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
            <div class="m-4 m-lg-5">
              <h1 class="display-5 fw-bold">Upload Counselor List</h1>
              <p class="fs-5">Import the district list of Counselors from the Council Merit Badge Counselor Listing Report (CouncilMeritBadgeCounselorListing.csv).</p>
              <form action="./FileUpload.php" method="post" enctype="multipart/form-data" aria-describedby="form-instructions">
                <div class="mb-3">
                  <label for="fileToUpload" class="form-label visually-hidden">Select CSV file</label>
                  <input class="form-control" type="file" name="the_file" id="fileToUpload" accept=".csv" required>
                  <div id="form-instructions" class="form-text">Please upload a valid CSV file from my.scouting.org.</div>
                </div>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="CouncilList" value="Update">
                <button class="btn btn-primary" type="submit" name="submit" value="CouncilList">Upload List</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include './Footer.php'; ?>
</body>

</html>