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
load_class(BASE_PATH . '/src/Classes/CCounselor.php');
$CCounselor = CCounselor::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $CCounselor->GotoURL("index.php");
  exit;
}

?>
<html>
<body>
 <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-3">
        <?php
        if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
          $CollegeYear = $_POST['CollegeYear'];
          setYear($CollegeYear);
        }

        // Display college year and allow user to select.
        $CollegeYear = $CCounselor->getYear();
        $CCounselor->SelectCounselor($CollegeYear, false);
        ?>

        <?php

        if (isset($_POST['CounselorName']) && $_POST['CounselorName'] !== '') {
          $SelectCounselor = $_POST['CounselorName'];
          $queryByMBCollege = sprintf(
            "SELECT * FROM college_counselors WHERE College='%s' AND BSAId='%s' ORDER BY LastName, FirstName, MBPeriod",
            $CollegeYear,
            $SelectCounselor,
            false
          );
        } else
          $queryByMBCollege = sprintf("SELECT * FROM college_counselors WHERE College='%s' ORDER BY LastName, FirstName, MBPeriod", $CollegeYear);
        $report_results = $CCounselor->doQuery($queryByMBCollege, $CollegeYear);
        if ($CCounselor->ReportCounselorSchedule($report_results, $CollegeYear)) {
          $report_results->free_result();
        }
        ?>


      </div>
    </div>
  </div>
</body>

</html>