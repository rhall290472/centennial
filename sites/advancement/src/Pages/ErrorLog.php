<?php
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
// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  die('An error occurred. Please try again later.');
}

load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');

$CAdvancement = CAdvancement::getInstance();

    // Secure session start
    if (session_status() === PHP_SESSION_NONE) {
      session_start([
        'cookie_httponly' => true,
        'use_strict_mode' => true,
        'cookie_secure' => isset($_SERVER['HTTPS'])
      ]);
    }
    if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $CAdvancement->GotoURL("index.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>
</head>

<body>
  <?php load_template('/src/Templates/navbar.php'); ?>

  <body style="padding:10px">
    <div class="my_div">
      <div>
        <p>Below is a list of recorded errors found.
        </p>
      </div>
      <?php
      $errorlog = file_get_contents('https://centennialdistrict.co/php_errors.log');
      if (false == $errorlog) {
        $cDistrictAwards->function_alert("Unable to read php_errors.log");
      } else {
        echo nl2br($errorlog);
      }

      ?>
    </div>


    <!-- Footer-->
    <?php load_template('/src/Templates/Footer.php'); ?>
  </body>

</html>