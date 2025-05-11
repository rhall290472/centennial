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

include('CAdvancement.php');
include_once('cAdultLeaders.php');

$SortBy = "";
if (isset($_GET['btn'])) {
  $SortBy = $_GET['btn'];
}

$cAdultLeaders = AdultLeaders::getInstance();
$cAdvancement = CAdvancement::getInstance();

//echo $SortBy;Untrained.php
switch ($SortBy) {
  case "MemberID":
    $mID = $_GET['MemberID'];
    $UnTrainedCount = $cAdultLeaders->GetUnTrainedIDCount($mID);
    $TotalCount = $cAdultLeaders->GetTotalIDCount($mID);
    $ResultUnTrained = $cAdultLeaders->GetResultIDUnTrained($mID);
    break;
  case "ByLastName":
    $mID = $_GET['MemberID'];
    $UnTrainedCount = $cAdultLeaders->GetUnTrainedIDCount($mID);
    $TotalCount = $cAdultLeaders->GetTotalIDCount($mID);
    $ResultUnTrained = $cAdultLeaders->GetResultIDUnTrained($mID);
    break;
  case "ByPosition":
    $position = $_GET['position_name'];
    $UnTrainedCount = $cAdultLeaders->GetUnTrainedPositionCount($position);
    $TotalCount = $cAdultLeaders->GetTotalPositionCount($position);
    $ResultUnTrained = $cAdultLeaders->GetResultPositionUnTrained($position);
    break;
  case "ByUnit":
    $unit = $_GET['Unit'];
    //$Sql = $cAdultLeaders->GetByUnit($unit);
    $UnTrainedCount = $cAdultLeaders->GetUnTrainedUnitCount($unit);
    $TotalCount = $cAdultLeaders->GetTotalUnitCount($unit);
    $ResultUnTrained = $cAdultLeaders->GetResultUnitUnTrained($unit);
    break;
  default:
    $SqlUnTrained = "Default case reached";
    $UnTrainedCount = 0;
    $TotalCount = 0;
    $strError = "Untrained.php - switch($SortBy) reached default case";
    error_log($strError, 0);
    trigger_error("Untrained.php - switch($SortBy) reached default case", E_USER_ERROR);
}

$Trained = $TotalCount - $UnTrainedCount;

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>

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
        echo "['Trained'," . $Trained . "],";
        echo "['UnTrained'," . $UnTrainedCount . "]";

        ?>
      ]);

      var options = {
        title: 'Trained Leaders',
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

      google.visualization.events.addListener(chart, 'ready', function() {
        var chartDiv = document.getElementById('chart_div');
        chartDiv.style.margin = '0 auto';
      });

    }
  </script>

</head>

<body>
  <header id="header" class="header sticky-top">
    <?php $navbarTitle = 'Centennial District Untrained Leaders'; ?>
    <?php include('navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include 'sidebar.php'; ?>
      <sort_options>
        <div class="px-lg-5">
          <div class="row">
            <div class="col-2">
              <form action="Untrained.php">
                <p class="mb-0">Last Name</p>
                <select class="form-control" id="MemberID" name="MemberID">
                  <?php
                  // First recod is blank "all"
                  echo "<option value=\"\" </option>";
                  $ResultName = $cAdultLeaders->GetUntrainedName();
                  while ($rowName = $ResultName->fetch_assoc()) {
                    if (isset($mID) && $rowName['MemberID'] == $mID) {
                      echo "<option value=\"{$rowName['MemberID']}\" selected>" . $rowName['Last_Name'] . " " . $rowName['First_Name'] . "</option>";
                    } else {
                      echo "<option value=\"{$rowName['MemberID']}\">" . $rowName['Last_Name'] . " " . $rowName['First_Name'] . "</option>";
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
              <form action="Untrained.php">
                <p class="mb-0">Position</p>
                <select class="form-control" id="position_name" name="position_name">
                  <?php
                  // First recod is blank "all"
                  echo "<option value=\"\" </option>";
                  $ResultPosition = $cAdultLeaders->GetUntrainedPosition();
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
              <form action="Untrained.php">
                <p class="mb-0">Unit</p>
                <select class="form-control" id="Unit_Name" name="Unit">
                  <?php
                  // First recod is blank "all"
                  //echo "<option value=\"\" </option>";
                  $ResultUnit = $cAdultLeaders->GetUntrainedUnit();
                  while ($rowUnit = $ResultUnit->fetch_assoc()) {
                    if (isset($position) && $rowUnit['Unit'] == $unit) {
                      echo "<option value=\"{$rowUnit['Unit']}\" selected>" . $rowUnit['Unit'] . "</option>";
                    } else {
                      echo "<option value=\"{$rowUnit['Unit']}\">" . $rowUnit['Unit'] . "</option>";
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
              <div id="piechart"></div>
            </div>
          </div>
        </div>

          <div class="px-lg-0">
            <div class="row">
              <div class="col-9">
                <?php
                if (isset($mID) || $_GET['btn'] == 'ByLastName') $sort = "By Last Name " . $mID;
                else if (isset($position) || $_GET['btn'] == 'ByPosition') $sort = "By Position " . $position;
                else if (isset($unit) || $_GET['btn'] == 'ByUnit') $sort =  "By Unit" . $unit;
                ?>
                <p style='text-align: center;'>Data sorted by: <?php echo $sort; ?></p>
                <div class="px-5 py-5">
                  <table class="table table-striped">
                    <td style="width:150px" />
                    <td style="width:10px" />
                    <td style="width:100px" />
                    <td style="width:100px" />
                    <td style="width:80px" />
                    <td style="width:220px" />
                    <td style="width:50px" />
                    <td style="width:50px" />
                    <td style="width:540px" />
                    <tr>
                      <th>Unit</th>
                      <th>Direct Contact</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Member ID</th>
                      <th>Position</th>
                      <th>Mandatory</th>
                      <th>Classroom</th>
                      <th>Online</th>
                    </tr>
                    <?php

                    if ($UnTrainedCount > 0) {
                      while ($row = $ResultUnTrained->fetch_assoc()) {
                        echo "<tr><td>" .
                          $row["Unit"] . "</td><td>" .
                          $row["Direct_Contact_Leader"] . "</td><td>" .
                          $row["First_Name"] . "</td><td>" .
                          $row["Last_Name"] . "</td><td>" .
                          $row["MemberID"] . "</td><td>" .
                          $row["Position"] . "</td><td>" .
                          $row["Incomplete_Mandatory"] . "</td><td>" .
                          $row["Incomplete_Classroom"] . "</td><td>" .
                          $row["Incomplete_Online"] . "</td></tr>";
                      }
                    } else {
                      echo "0 UnTrained";
                    }

                    ?>
                  </table>

                  <?php echo "<p style='text-align: center;' class='px-lg-5'>Data last updated: " . $cAdvancement->GetLastUpdated("trainedleaders") . "</p>"; ?>
                </div>
              </div>
            </div>
          </div>
      </sort_options>
    </div>
  </div>

  <!-- Footer-->
  <?php include 'Footer.php' ?>
</body>

</html>