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
$CMBCollege = CMBCollege::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
  header('Location: index.php?page=login');
  exit;
}

?>

<!-- 
    This file will create a csv file that maybe sent to Council for the 
    doubleknot sign up.

-->
<html>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-3">
        <?php
        $CollegeYear = $CMBCollege->getYear();
        $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details ORDER BY College DESC";
        $result_CollegeYear = $CMBCollege->doQuery($queryCollegeYear);
        ?>
        <form method=post>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
        <a href='https://www.denverboyscouts.org/districts/centennial/' class='logo'>Merit Badge College</a>
        <label for='UnitName'>&nbsp;</label>
        <select class='selectWrapper' id= 'CollegeYear' name='CollegeYear' >
        <?php
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
</body>

</html>