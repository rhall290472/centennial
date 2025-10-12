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

load_class(BASE_PATH . '/src/Classes/CPack.php');

$CPack = CPack::getInstance();

try {
  $SelYear = isset($_SESSION['year']) ? $_SESSION['year'] : date("Y");
  $CPack->SetYear($SelYear);
  $Totals = $CPack->GetTotals();

  $PackData = "['Lion'," . $Totals['lion'] . "]," .
    "['Tiger'," . $Totals['tiger'] . "]," .
    "['Wolf'," . $Totals['wolf'] . "]," .
    "['Bear'," . $Totals['bear'] . "]," .
    "['Webelos'," . $Totals['webelos'] . "]," .
    "['AOL'," . $Totals['aol'] . "],";
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading pack data: ' . $e->getMessage()];
  error_log("Home.php - Error: " . $e->getMessage(), 0);
  $Totals = ['lion' => 0, 'tiger' => 0, 'wolf' => 0, 'bear' => 0, 'webelos' => 0, 'aol' => 0, 'YTD' => 0, 'adventure' => 0, 'youth' => 0];
  $PackData = "";
}
?>

<sort_options>
  <div class="px-lg-5">
    <div class="row">
      <div class="col-2">
        <form action="index.php?page=home" method="POST">
          <p class="mb-0 d-print-none">Select Year</p>
          <?php
          try {
            $CPack->SelectYear();
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading year selector: ' . $e->getMessage()];
            echo '<select class="form-control" name="Year"><option value="' . date("Y") . '">' . date("Y") . '</option></select>';
          }
          ?>
          <!-- <input class="btn btn-primary btn-sm mt-2" type="submit" name="SubmitYear" value="Set Year"> -->
        </form>
      </div>
      <!-- CSS for chart containers -->
      <style>
        #piechart,
        #barchart_material {
          min-height: 400px;
          width: 100%;
          display: block;
        }
      </style>
    </div>
    <div class="row mt-4">
      <div class="col-md-5">
        <div id="barchart_material"></div>
      </div>
      <div class="col-md-5">
        <div id="piechart"></div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="py-5">
          <p style="text-align: center;">District wide advancement ratio: <?php echo number_format($CPack->GetDistrictRatio(null), 2, '.', ''); ?> / District goal: <?php echo number_format($CPack->GetDistrictGoal(null), 2, '.', ''); ?></p>
          <hr>
          <div class="py-5">
            <?php
            try {
              $CPack->DisplayAdvancmenetDescription();
              echo "<p style='text-align: center;'>Number of Packs in District: " . $CPack->GetNumofPacks() . "</p>";

              if ($CPack->GetNumofPacks() > 0) {
                echo '<table id="packTable" class="table table-striped"><thead>' .
                  '<tr><th>Unit</th><th>Lion</th><th>Tiger</th><th>Wolf</th><th>Bear</th><th>Webelos</th><th>AOL</th><th>YTD</th><th>Youth</th><th>Rank/Scout</th><th>Adventure</th><th>Date</th></tr></thead><tbody>';
                $PackDataResult = $CPack->GetPack();
                while ($PackAdv = $PackDataResult->fetch_assoc()) {
                  $UnitYouth = $CPack->GetUnitTotalYouth($PackAdv['Unit'], $PackAdv['Youth'], $SelYear);
                  $UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $PackAdv["YTD"] + $PackAdv["adventure"], $PackAdv["Unit"]);
                  $Unit = $PackAdv['Unit'];
                  $UnitDisplay = explode(' ', $Unit)[0];
                  $URLPath = 'index.php?page=unitview&btn=Units&unit_name=' . urlencode($Unit);
                  $UnitURL = "<a href=\"$URLPath\">";
                  $UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
                  $Formatter = "";
                  if ($UnitRankScout == 0) {
                    $Formatter = "<b style='color:red;'>";
                  } elseif ($UnitRankScout >= $CPack->GetDistrictGoal($PackAdv['Date']) && $UnitRankScout < $CPack->GetIdealGoal($PackAdv['Date'])) {
                    $Formatter = "<b style='color:orange;'>";
                  } elseif ($UnitRankScout >= $CPack->GetIdealGoal($PackAdv['Date'])) {
                    $Formatter = "<b style='color:green;'>";
                  }
                  echo "<tr><td>$UnitView</td><td>$Formatter" . htmlspecialchars($PackAdv["lion"]) . "</td><td>$Formatter" .
                    htmlspecialchars($PackAdv["tiger"]) . "</td><td>$Formatter" . htmlspecialchars($PackAdv["wolf"]) . "</td><td>$Formatter" .
                    htmlspecialchars($PackAdv["bear"]) . "</td><td>$Formatter" . htmlspecialchars($PackAdv["webelos"]) . "</td><td>$Formatter" .
                    htmlspecialchars($PackAdv["aol"]) . "</td><td>$Formatter" . htmlspecialchars($PackAdv["YTD"]) . "</td><td>$Formatter" .
                    htmlspecialchars($UnitYouth) . "</td><td>$Formatter" . htmlspecialchars($UnitRankScout) . "</td><td>$Formatter" .
                    htmlspecialchars($PackAdv["adventure"]) . "</td><td>$Formatter" . htmlspecialchars($PackAdv["Date"]) . "</td></tr>";
                  if ($Formatter) echo "</b>";
                }
                echo "</tbody></table>";
                mysqli_free_result($PackDataResult);
              } else {
                echo "<p>No pack data available for $SelYear.</p>";
              }
              echo "<p style='text-align: center;'>Data last updated: " . htmlspecialchars($CPack->GetLastUpdated("adv_pack")) . "</p>";
            } catch (Exception $e) {
              $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying pack data: ' . $e->getMessage()];
              error_log("Home.php - Error: " . $e->getMessage(), 0);
            }
            ?>
          </div>
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
<!-- Google Charts for Bar and Pie Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {
    'packages': ['bar', 'corechart']
  });
  google.charts.setOnLoadCallback(drawCharts);

  function drawCharts() {
    drawBarChart();
    drawPieChart();
  }

  function drawBarChart() {
    var data = google.visualization.arrayToDataTable([
      ['Rank', 'Ranks'],
      <?php echo $PackData; ?>
    ]);

    var options = {
      chart: {
        title: 'District wide Pack advancement Data',
        subtitle: 'Year to date',
      },
      bars: 'vertical'
    };

    var chart = new google.charts.Bar(document.getElementById('barchart_material'));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  }

  function drawPieChart() {
    var data = google.visualization.arrayToDataTable([
      ['Category', 'Count'],
      ['Ranks', <?php echo $Totals['YTD']; ?>],
      ['Adventure', <?php echo $Totals['adventure']; ?>],
      ['Scouts', <?php echo $Totals['youth']; ?>]
    ]);

    var options = {
      title: 'Rank earned vs. Scouts',
      slices: {
        0: {
          color: 'green'
        },
        1: {
          color: 'blue'
        },
        2: {
          color: 'red'
        }
      },
      pieSliceText: 'value'
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
  }

  // Initialize DataTables with export buttons and custom spinner
  $(document).ready(function() {
    console.log('Starting DataTable initialization for packTable');

    // Show custom loading overlay
    $('#loadingOverlay').show();
    console.log('Loading overlay shown');

    $('#packTable').DataTable({
      dom: 'Bfrtip',
      buttons: [{
          extend: 'copy',
          className: 'btn btn-primary btn-sm d-print-none mt-2'
        },
        {
          extend: 'csv',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'Centennial District Pack Summary'
        },
        {
          extend: 'excel',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'Centennial District Pack Summary'
        },
        {
          extend: 'pdf',
          className: 'btn btn-primary btn-sm d-print-none mt-2',
          filename: 'Centennial District Pack Summary'
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