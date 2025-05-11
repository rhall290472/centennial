<?php
  if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
  } else {
    echo __DIR__.'</br>';
    die('An error occurred. Please try again later. @' . __FILE__ . ' ' . __LINE__);
  }

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
  // Template loader function
  function load_template($file)
  {
    $path = BASE_PATH . $file;
    if (file_exists($path)) {
      require_once $path;
    } else {
      error_log("Template $file is missing.");
      if (defined('ENV') && ENV === 'development') {
        echo 'Template ' . $path . ' is missing.</br>';
        die('Template $file is missing.');
      } else
        die('An error occurred. Please try again later.');
    }
  }


  // Load configuration
  if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
  } else {
    die('An error occurred. Please try again later.');
  }

  ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
</head>

<body>

  <header id="header" class="header sticky-top">
      <!-- Responsive navbar-->
       <?php $navbarTitle = 'Centennial District Advancement'; ?>
       <?php include('navbar.php'); ?>

    <div class="container-fluid">
      <div class="row flex-nowrap">
        <?php include 'sidebar.php'; ?>
        <div class="col py-3">
        <div class="container px-lg-5">
      <div class="p-0 p-lg-0 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-3">
          <h1 class="display-5 fw-bold">Centennial District Advancement</h1>
          <p class="fs-4">Here you will be able to review advancment reports for the Centennial District</p>
          <!-- <a class="btn btn-primary btn-lg" href="./advancement_index.php">Advancement Data</a> -->
          </hr>
          <iframe src="https://www.google.com/maps/d/embed?mid=1Hj3PV-LAAKDU5-IenX9esVcbfx1_Ruc&ehbc=2E312F" width="100%" height="800px"></iframe>
        </div>
      </div>
    </div>

  </header>


  <!-- Footer-->
  <?php include 'Footer.php' ?>

  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
</body>

</html>