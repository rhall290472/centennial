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

load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');

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
          <!-- Custom loading overlay -->
          <div id="loadingOverlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; display: flex; justify-content: center; align-items: center;">
            <div class="spinner"></div>
            <span style="color: #fff; font-size: 16px;">Loading...</span>
          </div>
          <table id="yptTable" class="table table-striped">
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

<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" />
<!-- Custom CSS for button styling and spinner -->
<style>
  .dt-button.btn-primary {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
  }

  .dt-button.btn-primary:hover {
    background-color: #0056b3 !important;
    border-color: #004085 !important;
  }

  /* Spinner styles */
  .spinner {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid #007bff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin-right: 10px;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>
<!-- DataTables JS and Buttons -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
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

  // Initialize DataTables with export buttons and custom spinner
  $(document).ready(function() {
    console.log('Starting DataTable initialization for yptTable');

    // Show custom loading overlay
    $('#loadingOverlay').show();
    console.log('Loading overlay shown');

    $('#yptTable').DataTable({
      dom: 'Bfrtip',
      buttons: [{
          extend: 'copy',
          className: 'btn btn-primary btn-sm d-print-none mt-2'
        },
        {
          extend: 'csv',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'YPT_Leaders_Report'
        },
        {
          extend: 'excel',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'YPT_Leaders_Report'
        },
        {
          extend: 'pdf',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'YPT_Leaders_Report'
        }
      ],
      pageLength: -1, // Show all rows
      paging: false, // Disable pagination controls
      ordering: true, // Ensure sorting is enabled
      responsive: true,
      initComplete: function() {
        console.log('DataTable initialization complete');
        // Hide loading overlay after a minimum duration (e.g., 2 seconds)
        setTimeout(function() {
          $('#loadingOverlay').hide();
          console.log('Loading overlay hidden');
        }, 2000);
      }
    });
  });
</script>