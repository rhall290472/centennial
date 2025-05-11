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

load_template('/src/Classes/CAdvancement.php');
load_template('/src/Classes/cAdultLeaders.php');


$SqlExpiredypt = "";
if (isset($_GET['btn']))
  $SortBy = $_GET['btn'];

$cAdultLeaders = AdultLeaders::getInstance();
$cAdvancement = CAdvancement::getInstance();
$ExpiredyptCount = 0;
$ValidyptCount = 0;

switch ($SortBy) {
  case "ByLastName":
    if (isset($_GET['MemberID'])) {
      $mID = $_GET['MemberID'];
      $ExpiredyptCount = $cAdultLeaders->GetYPTIDCount($mID);
      $ValidyptCount = $cAdultLeaders->GetYPTTotalIDCount($mID);
      $Resultypt = $cAdultLeaders->GetResultIDYPT($mID);
    }
    break;
  case "ByPosition":
    if (isset($_GET['position_name'])) {
      $position = $_GET['position_name'];
      $ExpiredyptCount = $cAdultLeaders->GetYPTPositionCount($position);
      $ValidyptCount = $cAdultLeaders->GetYPTTotalPositionCount($position);
      $Resultypt = $cAdultLeaders->GetResultPositionYPT($position);
    }
    break;
  case "ByUnit":
    if (isset($_GET['Unit_Number'])) {
      $unit = $_GET['Unit_Number'];
      $ExpiredyptCount = $cAdultLeaders->GetYPTUnitCount($unit);
      $ValidyptCount = $cAdultLeaders->GetYPTTotalUnitCount($unit);
      $Resultypt = $cAdultLeaders->GetResultUnitYPT($unit);
    }
    break;
  default:
    $SqlExpiredypt = "Default case reached";
    $ExpiredyptCount = 0;
    $ValidyptCount = 0;
    $strError = "YPT.php - switch($SortBy) reached default case";
    error_log($strError, 0);
}

$TotalCount = $ValidyptCount - $ExpiredyptCount;


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>

  <!-- Pie chart for meeting/not meeting goals -->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = google.visualization.arrayToDataTable([
        ['Trained', 'UnTrained'],
        <?php
        echo "['Valid YPT'," . $TotalCount . "],";
        echo "['Expired YPT'," . $ExpiredyptCount . "]";

        ?>
      ]);

      var options = {
        title: 'YPT Status',
        slices: {
          0: {
            color: 'green'
          },
          1: {
            color: 'red'
          }
        }
      };

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(data, options);
    }
  </script>
</head>

<body>
  <header id="header" class="header sticky-top">
    <?php $navbarTitle = 'Centennial District - Adults with expired YPT'; ?>
    <?php load_template('/src/Templates/navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php load_template('/src/Templates/sidebar.php'); ?>
      <sort_options>
        <div class="px-lg-5">
          <div class="row">
            <div class="col-2">
              <form action="YPT.php">
                <p class="mb-0">By Last Name</p>
                <select class="form-control" id="MemberID" name="MemberID">
                  <?php
                  // First recod is blank "all"
                  echo "<option value=\"\" </option>";
                  $ResultName = $cAdultLeaders->GetYPTLastName();
                  while ($rowName = $ResultName->fetch_assoc()) {
                    if (isset($mID) && $rowName['Member_ID'] == $mID) {
                      echo "<option value=\"{$rowName['Member_ID']}\" selected>" . $rowName['Last_Name'] . " " . $rowName['First_Name'] . "</option>";
                    } else {
                      echo "<option value=\"{$rowName['Member_ID']}\">" . $rowName['Last_Name'] . " " . $rowName['First_Name'] . "</option>";
                    }
                  }
                  ?>
                </select>
            </div>
            <div class="col-1 py-4">
              <input class='btn btn-primary btn-sm d-print-none' type="submit" value="ByLastName" name="btn" />
              </form>
            </div>
            <div class="col-2">
              <form action="YPT.php">
                <p class="mb-0">By Position</p>
                <select class="form-control" id="position_name" name="position_name">
                  <?php
                  // First recod is blank "all"
                  echo "<option value=\"\" </option>";
                  $ResultPosition = $cAdultLeaders->GetYPTPositon();
                  while ($rowPosition = $ResultPosition->fetch_assoc()) {
                    if (isset($position) && $rowPosition['Position'] == $position) {
                      echo "<option value=\"{$rowPosition['Position']}\" selected>" . $rowPosition['Position'] . "</option>";
                    } else {
                      echo "<option value=\"{$rowPosition['Position']}\">" . $rowPosition['Position'] . "</option>";
                    }
                  }
                  ?>
                </select>
            </div>
            <div class="col-1 py-4">
              <input class='btn btn-primary btn-sm d-print-none' type="submit" value="ByPosition" name="btn" />
              </form>
            </div>

            <div class="col-2">
              <form action="YPT.php">
                <p class="mb-0">By unit</p>
                <select class="form-control" id="Unit_Name" name="Unit_Number">
                  <?php
                  // First recod is blank "all"
                  //echo "<option value=\"\" </option>";
                  $ResultYPTUnit = $cAdultLeaders->GetYPTUnit();
                  while ($rowYPTUnit = $ResultYPTUnit->fetch_assoc()) {
                    $FormmatedUnit = $rowYPTUnit['Unit_Number'];
                    if (isset($unit) && $rowYPTUnit['Unit_Number'] == $unit) {
                      echo "<option value=\"{$rowYPTUnit['Unit_Number']}\" selected>" . $FormmatedUnit . "</option>";
                    } else {
                      echo "<option value=\"{$rowYPTUnit['Unit_Number']}\">" . $FormmatedUnit . "</option>";
                    }
                  }
                  ?>
                </select>
            </div>
            <div class="col-1 py-4">
              <input class='btn btn-primary btn-sm d-print-none' type="submit" value="ByUnit" name="btn" />
              </form>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <!-- <div class="chart_div" style="width: 500px; height: 300px;"> -->
              <!-- <div id="piechart" style="margin: 0 auto"></div> -->
              <div id="piechart"></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-9 py-5">
            <p style='text-align: center;'>Sorted by <?php echo $SortBy; ?>. Number of Leaders: <?php echo $ValidyptCount; ?></p>
            <div class="px-5 py-5">
              <table class="table table-striped">
                <tr>
                  <th>Unit</th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Member ID</th>
                  <th>Position</th>
                  <th>Y01 Completed</th>
                  <th>Y01 Expired</th>
                </tr>
                <?php
                if ($ExpiredyptCount > 0) {
                  while ($row = $Resultypt->fetch_assoc()) {
                    echo "<tr><td>" .
                      $row["Unit_Number"] . "</td><td>" .
                      $row["First_Name"] . "</td><td>" .
                      $row["Last_Name"] . "</td><td>" .
                      $row["Member_ID"] . "</td><td>" .
                      $row["Position"] . "</td><td>" .
                      $row["Y01_Completed"] . "</td><td>" .
                      $row["Y01_Expires"] . "</td><td>"  . "</td></tr>";
                  }
                } else {
                  echo "0 result";
                }
                ?>
              </table>

              <?php echo "<p style='text-align: center;' class='px-lg-5'>Data last updated: " . $cAdvancement->GetLastUpdated("ypt") . "</p></br></br>"; ?>
            </div>
          </div>
        </div>
    </div>
    </sort_options>
  </div>
  </div>

  <!-- Footer-->
  <?php load_template('/src/Templates/Footer.php'); ?>
</body>

</html>