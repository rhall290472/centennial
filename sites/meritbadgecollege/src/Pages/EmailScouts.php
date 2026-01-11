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
load_class(BASE_PATH . '/src/Classes/CScout.php');
$CScout = CScout::getInstance();
// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
    header('Location: index.php?page=login');
    exit;
}

?>
<html>
<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
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
</body>

</html>