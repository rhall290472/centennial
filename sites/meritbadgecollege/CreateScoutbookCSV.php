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

include_once "CScout.php";
include_once "CCounselor.php";

$CScout = CScout::getInstance();
$CCounselor = CCounselor::getInstance();
// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $CScout->GotoURL("index.php");
  header("location: index.php");
  exit;
}

?>

<!-- CreateScoutbookCSV.php
    23Mar2024 - 
    The format of the file has now changed, the MBC ID is no longer part of the 
    file. So, this means we need to create a seprate file for each counselor.
    ScoutLastName	ScoutBSAMemberID	MeritBadgeName
        Smith	    12345678	        Art


     This file will create a csv file properly formatted to import in Scoutbook,
     There are four columns as show below, column names must match

     ScoutLastName	ScoutBSAMemberID	MeritBadgeName	MBCBSAMemberID
    Schmoe	           123456789	         Music	       987654321


-->
<html>

<head>
  <?php include('header.php'); ?>
</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-4">
      <a class="navbar-brand" href="#!">Centennial District Merit Badge College</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="https://mbcollege.centennialdistrict.co/index.php">Home</a></li>
          <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
          <li class="nav-item"><a class="nav-link" href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">Contact</a></li>
          <?php
          if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo '<li class="nav-item"><a class="nav-link" href="./logoff.php">Log off</a></li>';
          } else {
            echo '<li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>



  <div class="container-fluid">
    <div class="row flex-nowrap">
      <!-- Include the common side nav bar -->
      <?php include 'navbar.php'; ?>
      <div class="col py-3">
        <?php
        $CollegeYear = $CScout->getYear();

        if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
          $CollegeYear = $_POST['CollegeYear'];
          setYear($CollegeYear);
        }

        // Display college year and allow user to select.
        $CollegeYear = $CScout->getYear();
        $CScout->SelectCollegeYear($CollegeYear, "Counselor(s) Schedule", false);
        // Allow user to select a single counselor to display
        $CCounselor->SelectCounselor($CollegeYear, false);
        ?>

        <?php

        if (isset($_POST['CounselorName']) && $_POST['CounselorName'] !== '') {
          $SelectCounselor = $_POST['CounselorName'];

          $queryByMBCollege = "SELECT * FROM `college_registration` 
		    INNER JOIN college_counselors ON college_registration.MeritBadge=college_counselors.MBName AND college_registration.Period=college_counselors.MBPeriod
            AND college_registration.College=college_counselors.College
		    WHERE college_registration.College='$CollegeYear' AND college_registration.didnotattend='0' AND college_counselors.BSAId='$SelectCounselor' ORDER BY college_counselors.BSAId";

          $report_results = $CScout->doQuery($queryByMBCollege, $CollegeYear);
          if ($CScout->ReportCSV($report_results)) {
            $report_results->free_result();
          }
        } else {
          $queryByMBCollege = "SELECT * FROM `college_registration` 
		    INNER JOIN college_counselors ON college_registration.MeritBadge=college_counselors.MBName AND college_registration.Period=college_counselors.MBPeriod
            AND college_registration.College=college_counselors.College
		    WHERE college_registration.College='$CollegeYear' AND college_registration.didnotattend='0' ORDER BY college_counselors.BSAId";

          $report_results = $CScout->doQuery($queryByMBCollege, $CollegeYear);
          if ($CScout->ReportCSV($report_results)) {
            $report_results->free_result();
          }
        }
        ?>
      </div>
    </div>
  </div>
  <?php include("Footer.php"); ?>
</body>

</html>