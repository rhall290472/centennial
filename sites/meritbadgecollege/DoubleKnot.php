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

// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $CMBCollege->GotoURL("index.php");
  exit;
}

?>

<!-- 
    This file will create a csv file that maybe sent to Council for the 
    doubleknot sign up.

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
        $CollegeYear = $CMBCollege->getYear();
        $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details ORDER BY College DESC";
        $result_CollegeYear = $CMBCollege->doQuery($queryCollegeYear);

        echo "<form method=post>";
        echo "<a href='https://www.denverboyscouts.org/districts/centennial/' class='logo'>Merit Badge College</a>";
        echo "<label for='UnitName'>&nbsp;</label>";
        echo "<select class='selectWrapper' id= 'CollegeYear' name='CollegeYear' >";
        if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
          $CollegeYear = $_POST['CollegeYear'];
          $_SESSION['year'] = $_POST['CollegeYear'];
        }

        echo "<option value=\"\" </option>";    //First line is blank
        while ($rowCollege = $result_CollegeYear->fetch_assoc()) {
          if (!strcmp($rowCollege['College'], $CollegeYear)) {
            echo "<option selected value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
          } else
            echo "<option value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
        }
        echo '</select>';
        echo "<input class='rounded' type='submit' name='SubmitCollege' value='Select College'/>";
        echo "</form>";
        ?>

        <?php
        $qryDK = "SELECT * FROM college_counselors WHERE `College`='$CollegeYear' ORDER BY `MBPeriod`,`MBName`";

        $report_results = $CMBCollege->doQuery($qryDK, $CollegeYear);
        if ($CMBCollege->ReportDoubleKnot($report_results)) {
          $report_results->free_result();
        }
        ?>

      </div>
    </div>
  </div>
  <?php include("Footer.php"); ?>
</body>

</html>