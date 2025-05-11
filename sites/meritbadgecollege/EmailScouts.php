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

include_once 'CScout.php';
$CScout = CScout::getInstance();
// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $CScout->GotoURL("index.php");
  exit;
}

?>
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
        <div class="row">
          <?php
          if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
            $CollegeYear = $_POST['CollegeYear'];
            setYear($CollegeYear);
          }

          $CollegeYear = $CScout->getYear();
          $CScout->SelectCollegeYear($CollegeYear, "Scout(s) Schedule", true);
          // Allow user to select a single counselor to display
          $CScout->SelectSingleScout($CollegeYear, true);

          ?>
        </div>

        <div class="row">
          <div class="col py-3">
            <p class="text-bg-danger">WARNING: Once you select "Select College" or "Select Scout" the emails WILL be sent! Unless to check the Preview emails box</p>
            <?php
            $bPreview = false;
            if (isset($_POST['Preview']))
              $bPreview = true;

            if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
              $CollegeYear = $_POST['CollegeYear'];
              setYear($CollegeYear);
              $queryByMBCollege = sprintf("SELECT * FROM college_registration WHERE College='%s' ORDER BY LastNameScout, FirstNameScout, Period", $CollegeYear);
              $report_results = $CScout->doQuery($queryByMBCollege, $CollegeYear);

              if ($CScout->EmailScouts($report_results, $bPreview)) {
                $report_results->free_result();
              }
            }
            if (isset($_POST['SubmitScout']) && $_POST['SubmitScout'] !== '') {
              $ScoutID = $_POST['ScoutName'];
              $queryByMBCollege = sprintf("SELECT * FROM college_registration WHERE College='%s' AND BSAIdScout='%s' ORDER BY LastNameScout, FirstNameScout, Period", $CollegeYear, $ScoutID);
              $report_results = $CScout->doQuery($queryByMBCollege, $CollegeYear);
              if ($CScout->EmailScouts($report_results, $bPreview)) {
                $report_results->free_result();
              }
            }
            ?>
          </div> <!-- <div class="row flex-nowrap"> -->
        </div> <!-- <div class="container-fluid"> -->
      </div>
    </div>
  </div>

        <?php include("Footer.php"); ?>
</body>

</html>