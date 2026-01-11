<?php

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
  header('Location: index.php?page=login');
  exit;
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
$CMBCollege = CMBCollege::getInstance();

if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
  $CollegeYear = $_POST['CollegeYear'];
  $CMBCollege->SetYear($CollegeYear);
}

?>
<!DOCTYPE html>
<html lang="en">

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col-md-6 offset-md-1">

        <?php
        CMBCollege::DisplaySelectCollegeYear();

        $queryByMBCollege = sprintf("SELECT * FROM college_counselors WHERE College='%s' ORDER BY LastName, MBPeriod", $CMBCollege->GetYear());
        $report_results = $CMBCollege->doQuery($queryByMBCollege, $CMBCollege->GetYear());
        if ($CMBCollege->ReportMeritBadges1($report_results)) {
          $report_results->free_result();
        }
        ?>
      </div>
</body>

</html>