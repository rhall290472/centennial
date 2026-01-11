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

//include_once('CMBCollege.php');

//$CAdvancement = CAdvancement::getInstance();
$CMBCollege = CMBCollege::getInstance();


if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
  $CollegeYear = $_POST['CollegeYear'];
  $CMBCollege->SetYear($CollegeYear);
}
?>
<html>
<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-3">
        <?php
        CMBCollege::DisplaySelectCollegeYear();

        $queryByMBCollege = sprintf("SELECT * FROM college_counselors WHERE College='%s' ORDER BY MBRoom, MBPeriod", $CMBCollege->GetYear());
        $report_results = $CMBCollege->doQuery($queryByMBCollege, $CMBCollege->GetYear());
        if ($CMBCollege->ReportMeritBadgesRoom($report_results)) {
          $report_results->free_result();
        }
        //    }
        ?>

      </div>
    </div>
  </div>
</body>

</html>