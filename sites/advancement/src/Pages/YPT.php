<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #   Copyright 2017-2024 - Richard Hall                                   #  !
!  #   The information contained herein is the property of Richard          #  !
!  #   Hall, and shall not be copied, in whole or in part, or               #  !
!  #   disclosed to others in any manner without the express written        #  !
!  #   authorization of Richard Hall.                                       #  !
!  #                                                                        #  !
! /##########################################################################\ !
!//                                                                          \\!
!/                                                                            \!
!==============================================================================!
*/

load_class(BASE_PATH . '/src/Classes/CAdvancement.php');
load_class(BASE_PATH . '/src/Classes/cAdultLeaders.php');

$SortBy = isset($_GET['btn']) ? $_GET['btn'] : 'ByLastName';
$mID = isset($_GET['MemberID']) ? $_GET['MemberID'] : '';
$position = isset($_GET['position_name']) ? $_GET['position_name'] : '';
$unit = isset($_GET['Unit_Number']) ? $_GET['Unit_Number'] : '';

$cAdultLeaders = AdultLeaders::getInstance();
$cAdvancement = CAdvancement::getInstance();

try {
  switch ($SortBy) {
    case 'ByLastName':
      $ExpiredyptCount = $mID ? $cAdultLeaders->GetYPTIDCount($mID) : $cAdultLeaders->GetYPTIDCount(null);
      $ValidyptCount = $mID ? $cAdultLeaders->GetYPTTotalIDCount($mID) : $cAdultLeaders->GetYPTTotalIDCount(null);
      $Resultypt = $cAdultLeaders->GetResultIDYPT($mID);
      break;
    case 'ByPosition':
      $ExpiredyptCount = $position ? $cAdultLeaders->GetYPTPositionCount($position) : $cAdultLeaders->GetYPTPositionCount(null);
      $ValidyptCount = $position ? $cAdultLeaders->GetYPTTotalPositionCount($position) : $cAdultLeaders->GetYPTTotalPositionCount(null);
      $Resultypt = $cAdultLeaders->GetResultPositionYPT($position);
      break;
    case 'ByUnit':
      $ExpiredyptCount = $unit ? $cAdultLeaders->GetYPTUnitCount($unit) : $cAdultLeaders->GetYPTUnitCount(null);
      $ValidyptCount = $unit ? $cAdultLeaders->GetYPTTotalUnitCount($unit) : $cAdultLeaders->GetYPTTotalUnitCount(null);
      $Resultypt = $cAdultLeaders->GetResultUnitYPT($unit);
      break;
    default:
      $ExpiredyptCount = 0;
      $ValidyptCount = 0;
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid sort option selected.'];
      error_log("Ypt.php - switch($SortBy) reached default case", 0);
  }
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error processing request: ' . $e->getMessage()];
  error_log("Ypt.php - Error: " . $e->getMessage(), 0);
}

$TotalCount = $ValidyptCount - $ExpiredyptCount;
?>

<sort_options>
  <div class="px-lg-5">
    <div class="row">
      <div class="col-2">
        <form action="index.php?page=ypt" method="GET">
          <input type="hidden" name="page" value="ypt">
          <p class="mb-0">By Last Name</p>
          <select class="form-control" id="MemberID" name="MemberID">
            <?php
            echo "<option value=\"\">All</option>";
            $ResultName = $cAdultLeaders->GetYPTLastName();
            while ($rowName = $ResultName->fetch_assoc()) {
              $selected = ($mID == $rowName['Member_ID']) ? 'selected' : '';
              echo "<option value=\"{$rowName['Member_ID']}\" $selected>" . htmlspecialchars($rowName['Last_Name'] . " " . $rowName['First_Name']) . "</option>";
            }
            ?>
          </select>
          <input class="btn btn-primary btn-sm d-print-none mt-2" type="submit" value="ByLastName" name="btn" />
        </form>
      </div>
      <div class="col-2">
        <form action="index.php?page=ypt" method="GET">
          <input type="hidden" name="page" value="ypt">
          <p class="mb-0">By Position</p>
          <select class="form-control" id="position_name" name="position_name">
            <?php
            echo "<option value=\"\">All</option>";
            $ResultPosition = $cAdultLeaders->GetYPTPositon();
            while ($rowPosition = $ResultPosition->fetch_assoc()) {
              $selected = ($position == $rowPosition['Position']) ? 'selected' : '';
              echo "<option value=\"{$rowPosition['Position']}\" $selected>" . htmlspecialchars($rowPosition['Position']) . "</option>";
            }
            ?>
          </select>
          <input class="btn btn-primary btn-sm d-print-none mt-2" type="submit" value="ByPosition" name="btn" />
        </form>
      </div>
      <div class="col-2">
        <form action="index.php?page=ypt" method="GET">
          <input type="hidden" name="page" value="ypt">
          <p class="mb-0">By Unit</p>
          <select class="form-control" id="Unit_Name" name="Unit_Number">
            <?php
            echo "<option value=\"\">All</option>";
            $ResultYPTUnit = $cAdultLeaders->GetYPTUnit();
            while ($rowYPTUnit = $ResultYPTUnit->fetch_assoc()) {
              $selected = ($unit == $rowYPTUnit['Unit_Number']) ? 'selected' : '';
              echo "<option value=\"{$rowYPTUnit['Unit_Number']}\" $selected>" . htmlspecialchars($rowYPTUnit['Unit_Number']) . "</option>";
            }
            ?>
          </select>
          <input class="btn btn-primary btn-sm d-print-none mt-2" type="submit" value="ByUnit" name="btn" />
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
      <div class="col-12 py-5">
        <?php
        $sort = '';
        if ($SortBy == 'ByLastName') {
          $sort = "By Last Name " . ($mID ?: 'All');
        } elseif ($SortBy == 'ByPosition') {
          $sort = "By Position " . ($position ?: 'All');
        } elseif ($SortBy == 'ByUnit') {
          $sort = "By Unit " . ($unit ?: 'All');
        }
        ?>
        <p style="text-align: center;">Sorted by: <?php echo htmlspecialchars($sort); ?>. Number of Leaders: <?php echo $ValidyptCount; ?></p>
        <div class="px-5 py-5">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Unit</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Member ID</th>
                <th>Position</th>
                <th>Y01 Completed</th>
                <th>Y01 Expired</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($ExpiredyptCount > 0) {
                while ($row = $Resultypt->fetch_assoc()) {
                  echo "<tr><td>" . htmlspecialchars($row["Unit_Number"]) . "</td><td>" .
                    htmlspecialchars($row["First_Name"]) . "</td><td>" .
                    htmlspecialchars($row["Last_Name"]) . "</td><td>" .
                    htmlspecialchars($row["Member_ID"]) . "</td><td>" .
                    htmlspecialchars($row["Position"]) . "</td><td>" .
                    htmlspecialchars($row["Y01_Completed"]) . "</td><td>" .
                    htmlspecialchars($row["Y01_Expires"]) . "</td></tr>";
                }
              } else {
                echo "<tr><td colspan=\"7\">No expired YPT records found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
          <p style="text-align: center;" class="px-lg-5">Data last updated: <?php echo htmlspecialchars($cAdvancement->GetLastUpdated("ypt")); ?></p>
        </div>
      </div>
    </div>
  </div>
</sort_options>

<!-- Google Charts for Pie Chart -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {
    'packages': ['corechart']
  });
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Status', 'Count'],
      ['Valid YPT', <?php echo $TotalCount; ?>],
      ['Expired YPT', <?php echo $ExpiredyptCount; ?>]
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