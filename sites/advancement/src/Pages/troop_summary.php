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

  $data = "['Scout'," . $Totals['Scout'] . "]," .
    "['Tenderfoot'," . $Totals['Tenderfoot'] . "]," .
    "['Second'," . $Totals['SecondClass'] . "]," .
    "['First'," . $Totals['FirstClass'] . "]," .
    "['Star'," . $Totals['Star'] . "]," .
    "['Life'," . $Totals['Life'] . "]," .
    "['Eagle'," . $Totals['Eagle'] . "]," .
    "['Palms'," . $Totals['Palms'] . "],";
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading troop data: ' . $e->getMessage()];
  error_log("Troops.php - Error: " . $e->getMessage(), 0);
  $Totals = ['Scout' => 0, 'Tenderfoot' => 0, 'SecondClass' => 0, 'FirstClass' => 0, 'Star' => 0, 'Life' => 0, 'Eagle' => 0, 'Palms' => 0, 'YTD' => 0, 'MeritBadge' => 0, 'Youth' => 0];
  $NumofTroops = 0;
  $data = "";
}
?>

<sort_options>
  <div class="px-lg-5">
    <div class="row">
      <div class="col-2">
        <form action="index.php?page=troops" method="POST">
          <p class="mb-0">Select Year</p>
          <?php
          try {
            $CTroop->SelectYear();
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
          <?php
          try {
            if ($NumofTroops > 0) {
              echo "<p style='text-align: center;'>District wide advancement ratio: " . number_format($CTroop->GetDistrictRatio(), 2, '.', '') . " / District goal: " . number_format($CTroop->GetDistrictGoal(), 2, '.', '') . "</p>";
              echo "<hr>";
            }

            $CTroop->DisplayAdvancmenetDescription();
            //$CTroop->DisplayUnitAdvancement();
            echo "<p style='text-align: center;'>Number of Troops in District: $NumofTroops</p>";

            if ($NumofTroops > 0) {
              echo '<table class="table table-striped"><tbody>' .
                '<th>Unit</th><th>Scout</th><th>Tenderfoot</th><th>Second Class</th><th>First Class</th><th>Star</th><th>Life</th><th>Eagle</th><th>Palms</th><th>Merit Badges</th><th>Total Rank</th><th>Youth</th><th>Rank/Scout</th><th>Date</th></tr></thead><tbody>';
              $sql = sprintf("SELECT * FROM adv_troop WHERE Date=%d ORDER BY Unit ASC", $CTroop->GetYear());
              if ($result = mysqli_query($CTroop->getDbConn(), $sql)) {
                while ($row = $result->fetch_assoc()) {
                  $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $row["Date"]);
                  $Rank_Scout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row['Unit']);
                  $Unit = $row['Unit'];
                  $UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'>";
                  $UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
                  $Formatter = "";
                  if ($Rank_Scout == 0) {
                    $Formatter = "<b style='color:red;'>";
                  } elseif ($Rank_Scout >= 2.0 && $Rank_Scout < 4.0) {
                    $Formatter = "<b style='color:orange;'>";
                  } elseif ($Rank_Scout >= 4.0) {
                    $Formatter = "<b style='color:green;'>";
                  }
                  echo "<tr><td>$UnitView</td><td>$Formatter" . htmlspecialchars($row["Scout"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["Tenderfoot"]) . "</td><td>$Formatter" . htmlspecialchars($row["SecondClass"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["FirstClass"]) . "</td><td>$Formatter" . htmlspecialchars($row["Star"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["Life"]) . "</td><td>$Formatter" . htmlspecialchars($row["Eagle"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["Palms"]) . "</td><td>$Formatter" . htmlspecialchars($row["MeritBadge"]) . "</td><td>$Formatter" .
                    htmlspecialchars($row["YTD"]) . "</td><td>$Formatter" . htmlspecialchars($UnitYouth) . "</td><td>$Formatter" .
                    htmlspecialchars($Rank_Scout) . "</td><td>$Formatter" . htmlspecialchars($row["Date"]) . "</td></tr>";
                  if ($Formatter) echo "</b>";
                }
                mysqli_free_result($result);
              } else {
                throw new Exception("Database query failed: " . mysqli_error($CTroop->getDbConn()));
              }
              echo "</tbody></table>";
            } else {
              echo "<p>No troop data available for $SelYear.</p>";
            }
            echo "<p style='text-align: center;'>Data last updated: " . htmlspecialchars($CTroop->GetLastUpdated("adv_troop")) . "</p>";
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying troop data: ' . $e->getMessage()];
            error_log("Troops.php - Error: " . $e->getMessage(), 0);
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</sort_options>

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
      <?php echo $data; ?>
    ]);

    var options = {
      chart: {
        title: 'District wide Troop advancement Data',
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
      ['Merit Badges', <?php echo $Totals['MeritBadge']; ?>],
      ['Scouts', <?php echo $Totals['Youth']; ?>]
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
</script>