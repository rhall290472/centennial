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

load_class(BASE_PATH . '/src/Classes/CCrew.php');

$CCrew = CCrew::getInstance();

try {
  $SelYear = isset($_SESSION['year']) ? $_SESSION['year'] : date("Y");
  $CCrew->SetYear($SelYear);

  $Totals = $CCrew->GetTotals();
  $NumofCrews = $CCrew->GetNumofCrews();
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading crew data: ' . $e->getMessage()];
  error_log("crew_summary.php - Error: " . $e->getMessage(), 0);
  $Totals = ['Discovery' => 0, 'Pathfinder' => 0, 'Summit' => 0, 'Venturing' => 0, 'YTD' => 0, 'MeritBadge' => 0, 'Youth' => 0];
  $NumofCrews = 0;
}

$data = "['Discovery'," . $Totals['Discovery'] . "]," .
  "['Pathfinder'," . $Totals['Pathfinder'] . "]," .
  "['Summit'," . $Totals['Summit'] . "]," .
  "['Venturing'," . $Totals['Venturing'] . "],";
?>

<sort_options>
  <div class="px-lg-5">
    <div class="row">
      <div class="col-2">
        <form action="index.php?page=crew-summary" method="POST">
          <p class="mb-0">Select Year</p>
          <?php
          try {
            $CCrew->SelectYear();
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading year selector: ' . $e->getMessage()];
            echo '<select class="form-control" name="Year"><option value="' . date("Y") . '">' . date("Y") . '</option></select>';
          }
          ?>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
          <!-- <input class="btn btn-primary btn-sm mt-2" type="submit" name="SubmitYear" value="Set Year"> -->
        </form>
      </div>
      <div class="col-5">
        <div id="barchart_material" style="width: 600px; height: 300px;"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="py-5">
          <?php
          try {
            //$CCrew->DisplayUnitAdvancement();
            $sql = sprintf("SELECT * FROM adv_crew WHERE Date=%d ORDER BY Unit ASC", $CCrew->GetYear());
            $result = mysqli_query($CCrew->getDbConn(), $sql);
            if ($result) {
              $rowcount = mysqli_num_rows($result);
              if ($rowcount > 0) {
                echo '<table class="table table-striped"><tbody>' .
                  '<th>Unit</th><th>Star</th><th>Life</th><th>Eagle</th><th>Palms</th><th>Merit Badges</th><th>YTD</th><th>Youth</th><th>Rank/Scout</th><th>Discovery</th><th>Path Finder</th>
                  <th>Summit</th><th>Venturing</th><th>Date</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                  $UnitYouth = $CCrew->GetUnitTotalYouth($row['Unit'], $row['Youth'], $row['Date']);
                  $Rank_Scout = sprintf("%.2f", ($row["YTD"] + $row['MeritBadge']) / max($UnitYouth, 1));
                  $Unit = $row['Unit']; // e.g., "Pack 0127-BP"
                  // Extract only "Pack" from $Unit (assuming "Pack" is the part before the space or hyphen)
                  $UnitDisplay = explode(' ', $Unit)[0]; // Gets "Pack" by splitting on space
                  // Alternatively, if you want to split on hyphen: $UnitDisplay = explode('-', $Unit)[0];

                  $URLPath = 'index.php?page=unitview&btn=Units&unit_name=' . urlencode($Unit); // Use urlencode for safety
                  $UnitURL = "<a href=\"$URLPath\">"; // Double quotes for cleaner string
                  $UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
                  echo "<tr><td>$UnitView</td><td>" . htmlspecialchars($row['Star']) . "</td><td>" .
                    htmlspecialchars($row['Life']) . "</td><td>" . htmlspecialchars($row['Eagle']) . "</td><td>" .
                    htmlspecialchars($row['Palms']) . "</td><td>" . htmlspecialchars($row['MeritBadge']) . "</td><td>" .
                    htmlspecialchars($row['YTD']) . "</td><td>" . htmlspecialchars($UnitYouth) . "</td><td>" .
                    htmlspecialchars($Rank_Scout) . "</td><td>" . htmlspecialchars($row['discovery']) . "</td><td>" .
                    htmlspecialchars($row['pathfinder']) . "</td><td>" . htmlspecialchars($row['summit']) . "</td><td>" .
                    htmlspecialchars($row['venturing']) . "</td><td>" . htmlspecialchars($row['Date']) . "</td></tr>";
                }
                echo "</tbody></table>";
              } else {
                echo "<p>No crew data available for $SelYear.</p>";
              }
              mysqli_free_result($result);
            } else {
              throw new Exception("Database query failed: " . mysqli_error($CCrew->getDbConn()));
            }
            echo "<p style='text-align: center;'>Number of Crews in District: $NumofCrews</p>";
            echo "<p style='text-align: center;'>Data last updated: " . htmlspecialchars($CCrew->GetLastUpdated("adv_crew")) . "</p>";
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying crew data: ' . $e->getMessage()];
            error_log("crew_summary.php - Error: " . $e->getMessage(), 0);
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</sort_options>

<!-- Google Charts for Bar Chart -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {
    'packages': ['bar']
  });
  google.charts.setOnLoadCallback(drawBarChart);

  function drawBarChart() {
    var data = google.visualization.arrayToDataTable([
      ['Award', 'Awards'],
      <?php echo $data; ?>
    ]);

    var options = {
      chart: {
        title: 'District wide Crew Awards Data',
        subtitle: 'Year to date',
      },
      bars: 'vertical'
    };

    var chart = new google.charts.Bar(document.getElementById('barchart_material'));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  }
</script>