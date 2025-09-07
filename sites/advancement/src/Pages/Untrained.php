<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #                                                                        #  !
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

load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');

$SortBy = isset($_GET['btn']) ? $_GET['btn'] : 'ByLastName';
$mID = isset($_GET['MemberID']) ? $_GET['MemberID'] : -1;
$position = isset($_GET['position_name']) ? $_GET['position_name'] : '';
$unit = isset($_GET['Unit']) ? $_GET['Unit'] : '';

$cAdultLeaders = AdultLeaders::getInstance();
$cAdvancement = CAdvancement::getInstance();

try {
  switch ($SortBy) {
    case 'MemberID':
      $UnTrainedCount = $cAdultLeaders->GetUnTrainedIDCount($mID);
      $TotalCount = $cAdultLeaders->GetTotalIDCount($mID);
      $ResultUnTrained = $cAdultLeaders->GetResultIDUnTrained($mID);
      break;
    case 'ByLastName':
      $UnTrainedCount = $cAdultLeaders->GetUnTrainedIDCount($mID);
      $TotalCount = $cAdultLeaders->GetTotalIDCount($mID);
      $ResultUnTrained = $cAdultLeaders->GetResultIDUnTrained($mID);
      break;
    case 'ByPosition':
      $UnTrainedCount = $cAdultLeaders->GetUnTrainedPositionCount($position);
      $TotalCount = $cAdultLeaders->GetTotalPositionCount($position);
      $ResultUnTrained = $cAdultLeaders->GetResultPositionUnTrained($position);
      break;
    case 'ByUnit':
      $UnTrainedCount = $cAdultLeaders->GetUnTrainedUnitCount($unit);
      $TotalCount = $cAdultLeaders->GetTotalUnitCount($unit);
      $ResultUnTrained = $cAdultLeaders->GetResultUnitUnTrained($unit);
      break;
    default:
      $UnTrainedCount = 0;
      $TotalCount = 0;
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid sort option selected.'];
      error_log("Untrained.php - switch($SortBy) reached default case", 0);
  }
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error processing request: ' . $e->getMessage()];
  error_log("Untrained.php - Error: " . $e->getMessage(), 0);
}

$Trained = $TotalCount - $UnTrainedCount;
?>

<sort_options>
  <div class="px-lg-5">
    <div class="row">
      <div class="col-2">
        <form action="index.php?page=untrained" method="GET">
          <input type="hidden" name="page" value="untrained">
          <p class="mb-0">Last Name</p>
          <select class="form-control" id="MemberID" name="MemberID">
            <?php
            echo "<option value=\"\">All</option>";
            $ResultName = $cAdultLeaders->GetUntrainedName();
            while ($rowName = $ResultName->fetch_assoc()) {
              $selected = ($mID == $rowName['MemberID']) ? 'selected' : '';
              echo "<option value=\"{$rowName['MemberID']}\" $selected>" . htmlspecialchars($rowName['Last_Name'] . " " . $rowName['First_Name']) . "</option>";
            }
            ?>
          </select>
          <input class="btn btn-primary btn-sm d-print-none mt-2" type="submit" value="ByLastName" name="btn" />
        </form>
      </div>
      <div class="col-2">
        <form action="index.php?page=untrained" method="GET">
          <input type="hidden" name="page" value="untrained">
          <p class="mb-0">Position</p>
          <select class="form-control" id="position_name" name="position_name">
            <?php
            echo "<option value=\"\">All</option>";
            $ResultPosition = $cAdultLeaders->GetUntrainedPosition();
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
        <form action="index.php?page=untrained" method="GET">
          <input type="hidden" name="page" value="untrained">
          <p class="mb-0">Unit</p>
          <select class="form-control" id="Unit_Name" name="Unit">
            <?php
            $ResultUnit = $cAdultLeaders->GetUntrainedUnit();
            while ($rowUnit = $ResultUnit->fetch_assoc()) {
              $selected = ($unit == $rowUnit['Unit']) ? 'selected' : '';
              echo "<option value=\"{$rowUnit['Unit']}\" $selected>" . htmlspecialchars($rowUnit['Unit']) . "</option>";
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
      <div class="col-12">
        <?php
        $sort = '';
        if ($SortBy == 'ByLastName' || $SortBy == 'MemberID') {
          $sort = "By Last Name " . ($mID != -1 ? $mID : 'All');
        } elseif ($SortBy == 'ByPosition') {
          $sort = "By Position " . ($position ?: 'All');
        } elseif ($SortBy == 'ByUnit') {
          $sort = "By Unit " . ($unit ?: 'All');
        }
        ?>
        <p style="text-align: center;">Data sorted by: <?php echo htmlspecialchars($sort); ?></p>
        <div class="px-5 py-5">
          <table class="table table-striped">
            <thead>
              <tr>
                <th style="width:130px">Unit</th>
                <th>Direct Contact</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Member ID</th>
                <th style="width:180px">Position</th>
                <th>Mandatory</th>
                <th>Classroom</th>
                <th>Online</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($UnTrainedCount > 0) {
                while ($row = $ResultUnTrained->fetch_assoc()) {
                  echo "<tr><td>" . htmlspecialchars($row["Unit"]) . "</td><td>" .
                    htmlspecialchars($row["Direct_Contact_Leader"]) . "</td><td>" .
                    htmlspecialchars($row["First_Name"]) . "</td><td>" .
                    htmlspecialchars($row["Last_Name"]) . "</td><td>" .
                    htmlspecialchars($row["MemberID"]) . "</td><td>" .
                    htmlspecialchars($row["Position"]) . "</td><td>" .
                    htmlspecialchars($row["Incomplete_Mandatory"]) . "</td><td>" .
                    htmlspecialchars($row["Incomplete_Classroom"]) . "</td><td>" .
                    htmlspecialchars($row["Incomplete_Online"]) . "</td></tr>";
                }
              } else {
                echo "<tr><td colspan=\"9\">No untrained leaders found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
          <p style="text-align: center;" class="px-lg-5">Data last updated: <?php echo htmlspecialchars($cAdvancement->GetLastUpdated("trainedleaders")); ?></p>
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
      ['Trained', <?php echo $Trained; ?>],
      ['UnTrained', <?php echo $UnTrainedCount; ?>]
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
  }
</script>