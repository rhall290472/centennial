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
        <?php
        $CollegeYear = $CMBCollege->getYear();
        $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details ORDER BY College DESC";
        $result_CollegeYear = $CMBCollege->doQuery($queryCollegeYear);
        ?>
        <form method=post>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
        <div class="row  d-print-none">
          <div class="col-2">
            <label for='UnitName'>&nbsp;</label>
            <select class='form-control' id='CollegeYear' name='CollegeYear'>
              <?php
              if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
                $CollegeYear = $_POST['CollegeYear'];
                setYear($CollegeYear);
              }
              ?>

              <option value=\"\" </option>
                <?php
                while ($rowCollege = $result_CollegeYear->fetch_assoc()) {
                  if (!strcmp($rowCollege['College'], $CollegeYear)) {
                    echo "<option selected value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
                  } else
                    echo "<option value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
                }
                ?>
            </select>
          </div>
          <div class="col-2 py-4">
            <input class='btn btn-primary btn-sm' type='submit' name='SubmitCollege' value='Select College' />
          </div>
        </div>
        </form>


        <?php

        // Display Location and the POC
        echo "</br>";
        $CMBCollege->DisplayCollegeDetails($CollegeYear);
        echo "</br>";
        //    if (isset($_POST['SubmitCollege']) && isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
        echo "<table class='table'  style='width:550';>";
        echo "<td style='width:50px'>";
        echo "<td style='width:50px'>";
        echo "<td style='width:50px'>";
        echo "<td style='width:50px'>";
        echo "<td style='width:50px'>";
        echo "<td style='width:50px'>";
        echo "<tr>";
        echo "<th>Period</th>";
        echo "<th>Merit badges</th>";
        echo "<th>Scouts</th>";
        echo "<th>Seats</th>";
        echo "<th>Percent taken</th>";
        echo "<th>Counselors</th>";
        echo "</tr>";

        $CMBCollege->ReportStats('A');
        $CMBCollege->ReportStats('B');
        $CMBCollege->ReportStats('AB');
        $CMBCollege->ReportStats('C');
        $CMBCollege->ReportStats('D');
        $CMBCollege->ReportStats('CD');
        $CMBCollege->ReportStats('E');
        $CMBCollege->ReportStats('F');
        $CMBCollege->ReportStats('Totals');

        echo "</table>";


        echo "<table class='table'  style='width:550';>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<tr>";
        echo "<th>District</th>";
        echo "<th>Number of Scouts</th>";
        echo "<th>Unit Type</th>";
        echo "<th>Unit Number</th>";
        echo "<th># of Scouts</th>";
        echo "</tr>";
        $CMBCollege->ReportByDistrict();
        echo "</table>";

        echo "<table class='table'  style='width:300';>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<tr>";
        echo "<th>Line Item</th>";
        echo "<th>Cost</th>";
        echo "<th>SubTotal</th>";
        echo "</tr>";
        $CMBCollege->ReportFinancials();
        echo "</table>";
        //    }
        ?>

        <br />
        <p> The foumla for the number of pizzas to order is Pizzas = 2 * (Number of Scout + Counselors) / 8 </p>

      </div>
    </div>
  </div>
</body>

</html>