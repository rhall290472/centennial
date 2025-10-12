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

load_class(BASE_PATH . '/src/Classes/CTroop.php');

$CTroop = CTroop::getInstance();

try {
  $SelYear = isset($_SESSION['year']) ? $_SESSION['year'] : date("Y");
  $CTroop->SetYear($SelYear);

  $Totals = $CTroop->GetTotals();
  $NumofTroops = $CTroop->GetNumofTroops();
  $TroopAbove = $CTroop->GetTroopsAboveGoal();
  $TroopBelow = $NumofTroops - $TroopAbove;
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading troop data: ' . $e->getMessage()];
  error_log("troop_below_goal.php - Error: " . $e->getMessage(), 0);
  $Totals = ['YTD' => 0, 'MeritBadges' => 0, 'Youth' => 0];
  $NumofTroops = 0;
  $TroopAbove = 0;
  $TroopBelow = 0;
}
?>

<sort_options>
  <div class="px-lg-5">
    <div class="row">
      <div class="col-2">
        <form action="index.php?page=troop-below-goal" method="POST">
          <p class="mb-0">Select Year</p>
          <?php
          try {
            $CTroop->SelectYear();
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading year selector: ' . $e->getMessage()];
            echo '<select class="form-control" name="Year"><option value="' . date("Y") . '">' . date("Y") . '</option></select>';
          }
          ?>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
          <!-- <input class="btn btn-primary btn-sm mt-2" type="submit" name="SubmitYear" value="Set Year"> -->
        </form>
      </div>
      <div class="col-4">
        <div id="piechart" style="width: 500px; height: 400px;"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="py-5">
          <?php
          try {
            $CTroop->DisplayAdvancmenetDescription();
            echo "<p style='text-align: center;'>Number of units below goal: $TroopBelow Out of: $NumofTroops Troops</p>";

            if ($NumofTroops > 0) {
              echo '<div id="loadingOverlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; display: flex; justify-content: center; align-items: center;">' .
                '<div class="spinner"></div>' .
                '<span style="color: #fff; font-size: 16px;">Loading...</span>' .
                '</div>' .
                '<table id="troopsBelowGoalTable" class="table table-striped"><thead><tr>' .
                '<th>Unit</th><th>Scout</th><th>Tenderfoot</th><th>Second Class</th><th>First Class</th><th>Star</th><th>Life</th><th>Eagle</th><th>Palms</th><th>Merit Badges</th><th>Total Rank</th><th>Youth</th><th>Rank/Scout</th><th>Date</th></tr></thead><tbody>';
              $sql = sprintf("SELECT * FROM adv_troop WHERE Date=%d ORDER BY Unit ASC", $CTroop->GetYear());
              $result = $CTroop->doQuery($sql);
              if ($result) {
                while ($row = $result->fetch_assoc()) {
                  $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CTroop->GetYear());
                  $UnitRankScout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row['Unit']);
                  if (floatval($UnitRankScout) >= $CTroop->GetDistrictGoal($row["Date"])) {
                    continue;
                  }
                  $Unit = $row['Unit'];
                  $UnitDisplay = explode(' ', $Unit)[0];
                  $URLPath = 'index.php?page=unitview&btn=Units&unit_name=' . urlencode($Unit);
                  $UnitURL = "<a href=\"$URLPath\">";
                  $UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
                  $Formatter = "";
                  if ($UnitRankScout == 0) {
                    $Formatter = "<b style='color:red;'>";
                  } elseif ($UnitRankScout > 0 && $UnitRankScout < $CTroop->GetDistrictGoal($row['Date'])) {
                    $Formatter = "<b style='color:orange;'>";
                  }
                  echo "<tr><td>$UnitView</td><td>$Formatter" . htmlspecialchars($row["Scout"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["Tenderfoot"]) . "</td><td>$Formatter" . htmlspecialchars($row["SecondClass"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["FirstClass"]) . "</td><td>$Formatter" . htmlspecialchars($row["Star"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["Life"]) . "</td><td>$Formatter" . htmlspecialchars($row["Eagle"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["Palms"]) . "</td><td>$Formatter" . htmlspecialchars($row["MeritBadge"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["YTD"]) . "</td><td>$Formatter" . htmlspecialchars($UnitYouth) . "</td><td>$Formatter" .
                    htmlspecialchars($UnitRankScout) . "</td><td>$Formatter" . htmlspecialchars($row["Date"]) . "</td></tr>";
                  if ($Formatter) echo "</b>";
                }
                mysqli_free_result($result);
              } else {
                throw new Exception("Database query failed.");
              }
              echo "</tbody></table>";
            } else {
              echo "<p>No troop data available for $SelYear.</p>";
            }
            echo "<p style='text-align: center;'>Data last updated: " . htmlspecialchars($CTroop->GetLastUpdated("adv_troop")) . "</p>";
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying troop data: ' . $e->getMessage()];
            error_log("troop_below_goal.php - Error: " . $e->getMessage(), 0);
          }
          ?>
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
      ['Category', 'Count'],
      ['Below', <?php echo $TroopBelow; ?>],
      ['Above', <?php echo $TroopAbove; ?>]
    ]);

    var options = {
      title: 'Troops below district goal',
      slices: {
        0: {
          color: 'red'
        },
        1: {
          color: 'green'
        }
      },
      pieSliceText: 'value'
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
  }

  // Initialize DataTables with export buttons and custom spinner
  $(document).ready(function() {
    console.log('Starting DataTable initialization for troopsBelowGoalTable');

    // Show custom loading overlay
    $('#loadingOverlay').show();
    console.log('Loading overlay shown');

    $('#troopsBelowGoalTable').DataTable({
      dom: 'Bfrtip',
      buttons: [{
          extend: 'copy',
          className: 'btn btn-primary btn-sm d-print-none mt-2'
        },
        {
          extend: 'csv',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'Centennial District Troops Below Goal'
        },
        {
          extend: 'excel',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'Centennial District Troops Below Goal'
        },
        {
          extend: 'pdf',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'Centennial District Troops Below Goal'
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