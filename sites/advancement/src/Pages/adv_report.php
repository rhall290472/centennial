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

load_template('/src/Classes/CPack.php');
load_template('/src/Classes/CTroop.php');
load_template('/src/Classes/CUnit.php');

$CPack = CPack::getInstance();
$CTroop = CTroop::getInstance();
$CUnit = UNIT::getInstance();

try {
  $SelYear = isset($_SESSION['year']) ? $_SESSION['year'] : date("Y");
  $CPack->SetYear($SelYear);
  $CTroop->SetYear($SelYear);

  $PackTotals = $CPack->GetTotals();
  $NumberofPacks = $CPack->GetNumofPacks();
  $PackRatio = $CPack->GetDistrictRatio(null);

  $TroopTotals = $CTroop->GetTotals();
  $NumberofTroops = $CTroop->GetNumofTroops();
  $TroopRatio = $CTroop->GetDistrictRatio();

  // Validate PackTotals
  $requiredPackKeys = ['lion', 'tiger', 'wolf', 'bear', 'webelos', 'aol', 'YTD', 'adventure', 'youth'];
  foreach ($requiredPackKeys as $key) {
    if (!isset($PackTotals[$key]) || !is_numeric($PackTotals[$key])) {
      throw new Exception("Invalid PackTotals data for key: $key");
    }
  }

  // Validate TroopTotals
  $requiredTroopKeys = ['Scout', 'Tenderfoot', 'SecondClass', 'FirstClass', 'Star', 'Life', 'Eagle', 'Palms', 'MeritBadge', 'YTD', 'Youth'];
  foreach ($requiredTroopKeys as $key) {
    if (!isset($TroopTotals[$key]) || !is_numeric($TroopTotals[$key])) {
      throw new Exception("Invalid TroopTotals data for key: $key");
    }
  }

  // Calculate Packs below goal
  $sqlPack = "SELECT Unit, Youth, YTD, adventure, Date FROM adv_pack WHERE Date = ?";
  $stmt = mysqli_prepare($CPack->getDbConn(), $sqlPack);
  mysqli_stmt_bind_param($stmt, "i", $SelYear);
  mysqli_stmt_execute($stmt);
  $resultPack = mysqli_stmt_get_result($stmt);
  $PacksUnderGoal = 0;
  if ($resultPack) {
    while ($row = $resultPack->fetch_assoc()) {
      $UnitYouth = $CPack->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CPack->GetYear());
      $UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $row["YTD"] + $row['adventure'], $row["Unit"]);
      if ($UnitYouth == 0 || floatval($UnitRankScout) < $CPack->GetDistrictGoal($row["Date"])) {
        $PacksUnderGoal++;
      }
    }
    mysqli_free_result($resultPack);
  } else {
    throw new Exception("Pack query failed: " . mysqli_error($CPack->getDbConn()));
  }
  $PacksAboveGoal = $NumberofPacks - $PacksUnderGoal;

  // Calculate Troops below goal
  $sqlTroop = "SELECT Unit, Youth, YTD, MeritBadge, Date FROM adv_troop WHERE Date = ?";
  $stmt = mysqli_prepare($CTroop->getDbConn(), $sqlTroop);
  mysqli_stmt_bind_param($stmt, "i", $SelYear);
  mysqli_stmt_execute($stmt);
  $resultTroop = mysqli_stmt_get_result($stmt);
  $TroopsBelow = 0;
  if ($resultTroop) {
    while ($row = $resultTroop->fetch_assoc()) {
      $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CTroop->GetYear());
      $UnitRankScout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row["Unit"]);
      if (floatval($UnitRankScout) < $CTroop->GetDistrictGoal($row["Date"])) {
        $TroopsBelow++;
      }
    }
    mysqli_free_result($resultTroop);
  } else {
    throw new Exception("Troop query failed: " . mysqli_error($CTroop->getDbConn()));
  }
  $TroopsAbove = $NumberofTroops - $TroopsBelow;

  // Eagle Scout total
  $sqlEagle = "SELECT SUM(Eagle) AS total_eagle FROM adv_troop WHERE Date = ?";
  $stmt = mysqli_prepare($CTroop->getDbConn(), $sqlEagle);
  mysqli_stmt_bind_param($stmt, "i", $SelYear);
  mysqli_stmt_execute($stmt);
  $resultEagle = mysqli_stmt_get_result($stmt);
  $troopEagle = 0;
  if ($resultEagle) {
    $row = $resultEagle->fetch_assoc();
    $troopEagle = $row['total_eagle'] ?? 0;
    mysqli_free_result($resultEagle);
  }
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading report data: ' . $e->getMessage()];
  error_log("adv_report.php - Error: " . $e->getMessage(), 0);
  $PackTotals = ['lion' => 0, 'tiger' => 0, 'wolf' => 0, 'bear' => 0, 'webelos' => 0, 'aol' => 0, 'YTD' => 0, 'adventure' => 0, 'youth' => 0];
  $TroopTotals = ['Scout' => 0, 'Tenderfoot' => 0, 'SecondClass' => 0, 'FirstClass' => 0, 'Star' => 0, 'Life' => 0, 'Eagle' => 0, 'Palms' => 0, 'MeritBadge' => 0, 'YTD' => 0, 'Youth' => 0];
  $NumberofPacks = 0;
  $NumberofTroops = 0;
  $PackRatio = 0;
  $TroopRatio = 0;
  $PacksUnderGoal = 0;
  $PacksAboveGoal = 0;
  $TroopsBelow = 0;
  $TroopsAbove = 0;
  $troopEagle = 0;
}
?>

<sort_options>
  <div class="px-lg-5">
    <h1>Centennial District Advancement Report</h1>
    <div class="row">
      <div class="col-2">
        <form action="index.php?page=adv-report" method="POST">
          <p class="mb-0">Select Year</p>
          <?php
          try {
            $CTroop->SelectYear();
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading year selector: ' . $e->getMessage()];
            echo '<select class="form-control" name="Year"><option value="' . date("Y") . '">' . date("Y") . '</option></select>';
          }
          ?>
          <!-- <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>"> -->
          <!-- <input class="btn btn-primary btn-sm mt-2" type="submit" name="SubmitYear" value="Set Year"> -->
        </form>
      </div>
    </div>
    <!-- CSS for chart containers -->
    <style>
      #PackChart,
      #Packpiechart,
      #TroopChart,
      #Trooppiechart {
        min-height: 400px;
        width: 100%;
        display: block;
      }
    </style>
    <!-- Pack Section -->
    <div class="row mt-4">
      <div class="col-12">
        <h2>Pack Advancement</h2>
        <div class="row">
          <div class="col-md-5">
            <div id="PackChart"></div>
          </div>
          <div class="col-md-5">
            <div id="Packpiechart"></div>
          </div>
          <div class="row py-6">
            <p class="text-center">
              District wide advancement ratio: <?php echo number_format($PackRatio, 2); ?> /
              District goal: <?php echo number_format($CPack->GetDistrictGoal(null), 2); ?>
            </p>
          </div>
        </div>
        <p class="text-center">Number of units <b>below</b> goal: <?php echo htmlspecialchars($PacksUnderGoal); ?> Out of: <?php echo htmlspecialchars($NumberofPacks); ?> Packs</p>
        <?php
        try {
          $sqlPack = "SELECT * FROM adv_pack WHERE Date = ? ORDER BY Unit ASC";
          $stmt = mysqli_prepare($CPack->getDbConn(), $sqlPack);
          mysqli_stmt_bind_param($stmt, "i", $SelYear);
          mysqli_stmt_execute($stmt);
          $resultPack = mysqli_stmt_get_result($stmt);
          if ($resultPack) {
            if (mysqli_num_rows($resultPack) > 0) {
              echo '<table class="table table-striped"><thead><tr>' .
                '<th>Unit</th><th>Lion</th><th>Tiger</th><th>Wolf</th><th>Bear</th><th>Webelos</th><th>AOL</th><th>Total Rank</th><th>Total Youth</th><th>Rank/Scout</th><th>Adventures</th><th>Date</th></tr></thead><tbody>';
              while ($row = $resultPack->fetch_assoc()) {
                $UnitYouth = $CPack->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CPack->GetYear());
                $UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $row["YTD"] + $row['adventure'], $row["Unit"]);
                if (floatval($UnitRankScout) >= $CPack->GetDistrictGoal($row["Date"])) {
                  continue;
                }
                $Unit = $row['Unit'];
                $UnitURL = "<a href='" . htmlspecialchars(SITE_URL . '/centennial/sites/advancement/src/Pages/Unit_View.php?btn=Units&unit_name=' . urlencode($Unit)) . "'>";
                $UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
                $Formatter = $UnitRankScout == 0 ? "<b style='color:red;'>" : ($UnitRankScout >= $CPack->GetDistrictGoal($row["Date"]) && $UnitRankScout < $CPack->GetIdealGoal($row["Date"]) ? "<b style='color:orange;'>" : ($UnitRankScout >= $CPack->GetIdealGoal($row["Date"]) ? "<b style='color:green;'>" : ""));
                echo "<tr><td>$UnitView</td><td>$Formatter" . htmlspecialchars($row["lion"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["tiger"]) . "</td><td>$Formatter" . htmlspecialchars($row["wolf"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["bear"]) . "</td><td>$Formatter" . htmlspecialchars($row["webelos"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["aol"]) . "</td><td>$Formatter" . htmlspecialchars($row["YTD"]) . "</td><td>$Formatter" .
                  htmlspecialchars($UnitYouth) . "</td><td>$Formatter" . htmlspecialchars($UnitRankScout) . "</td><td>$Formatter" .
                  htmlspecialchars($row["adventure"]) . "</td><td>$Formatter" . htmlspecialchars($row["Date"]) . "</td></tr>";
                if ($Formatter) echo "</b>";
              }
              echo "</tbody></table>";
              echo "<p class='text-center'>Data last updated: " . htmlspecialchars($CPack->GetLastUpdated("adv_pack")) . "</p>";
            } else {
              echo "<p>No pack data available for $SelYear.</p>";
            }
            mysqli_free_result($resultPack);
          } else {
            throw new Exception("Pack table query failed: " . mysqli_error($CPack->getDbConn()));
          }
        } catch (Exception $e) {
          $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying pack data: ' . $e->getMessage()];
          error_log("adv_report.php - Error: " . $e->getMessage(), 0);
        }
        ?>
      </div>
    </div>
    <!-- Troop Section -->
    <div class="row mt-4">
      <div class="col-12">
        <h2>Troop Advancement</h2>
        <div class="row">
          <div class="col-md-5">
            <div id="TroopChart" style="width: 100%; height: 400px;"></div>
          </div>
          <div class="col-md-5">
            <div id="Trooppiechart" style="width: 100%; height: 400px;"></div>
            <?php if ($TroopTotals['Youth'] > 0): ?>
              <p class="text-center">
                District wide advancement ratio: <?php echo number_format($TroopRatio, 2); ?> /
                District goal: <?php echo number_format($CTroop->GetDistrictGoal($SelYear), 2); ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
        <p class="text-center">Number of units <b>below</b> goal: <?php echo htmlspecialchars($TroopsBelow); ?> Out of: <?php echo htmlspecialchars($NumberofTroops); ?> Troops</p>
        <?php
        try {
          $sqlTroop = "SELECT * FROM adv_troop WHERE Date = ? ORDER BY Unit ASC";
          $stmt = mysqli_prepare($CTroop->getDbConn(), $sqlTroop);
          mysqli_stmt_bind_param($stmt, "i", $SelYear);
          mysqli_stmt_execute($stmt);
          $resultTroop = mysqli_stmt_get_result($stmt);
          if ($resultTroop) {
            if (mysqli_num_rows($resultTroop) > 0) {
              echo '<table class="table table-striped"><thead><tr>' .
                '<th>Unit</th><th>Scout</th><th>Tenderfoot</th><th>Second Class</th><th>First Class</th><th>Star</th><th>Life</th><th>Eagle</th><th>Palms</th><th>Merit Badges</th><th>Total Rank</th><th>Total Youth</th><th>Rank/Scout</th><th>Date</th></tr></thead><tbody>';
              while ($row = $resultTroop->fetch_assoc()) {
                $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CTroop->GetYear());
                $UnitRankScout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row["Unit"]);
                if (floatval($UnitRankScout) >= $CTroop->GetDistrictGoal($row["Date"])) {
                  continue;
                }
                $Unit = $row['Unit'];
                $UnitURL = "<a href='" . htmlspecialchars(SITE_URL . '/centennial/sites/advancement/src/Pages/Unit_View.php?btn=Units&unit_name=' . urlencode($Unit)) . "'>";
                $UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
                $Formatter = $UnitRankScout == 0 ? "<b style='color:red;'>" : ($UnitRankScout >= $CTroop->GetDistrictGoal($row["Date"]) && $UnitRankScout < $CTroop->GetIdealGoal($row["Date"]) ? "<b style='color:orange;'>" : ($UnitRankScout >= $CTroop->GetIdealGoal($row["Date"]) ? "<b style='color:green;'>" : ""));
                echo "<tr><td>$UnitView</td><td>$Formatter" . htmlspecialchars($row["Scout"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["Tenderfoot"]) . "</td><td>$Formatter" . htmlspecialchars($row["SecondClass"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["FirstClass"]) . "</td><td>$Formatter" . htmlspecialchars($row["Star"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["Life"]) . "</td><td>$Formatter" . htmlspecialchars($row["Eagle"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["Palms"]) . "</td><td>$Formatter" . htmlspecialchars($row["MeritBadge"]) . "</td><td>$Formatter" .
                  htmlspecialchars($row["YTD"]) . "</td><td>$Formatter" . htmlspecialchars($UnitYouth) . "</td><td>$Formatter" .
                  htmlspecialchars($UnitRankScout) . "</td><td>$Formatter" . htmlspecialchars($row["Date"]) . "</td></tr>";
                if ($Formatter) echo "</b>";
              }
              echo "</tbody></table>";
              echo "<p class='text-center'>Data last updated: " . htmlspecialchars($CTroop->GetLastUpdated("adv_troop")) . "</p>";
            } else {
              echo "<p>No troop data available for $SelYear.</p>";
            }
            mysqli_free_result($resultTroop);
          } else {
            throw new Exception("Troop table query failed: " . mysqli_error($CTroop->getDbConn()));
          }
        } catch (Exception $e) {
          $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying troop data: ' . $e->getMessage()];
          error_log("adv_report.php - Error: " . $e->getMessage(), 0);
        }
        ?>
      </div>
    </div>
    <!-- Membership Section -->
    <div class="row mt-4">
      <div class="col-12">
        <h2>Membership Report</h2>
        <?php
        try {
          $CUnit->DisplayMembershipTable();
        } catch (Exception $e) {
          $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying membership data: ' . $e->getMessage()];
          error_log("adv_report.php - Error: " . $e->getMessage(), 0);
        }
        ?>
      </div>
    </div>
    <!-- Eagle Section -->
    <div class="row mt-4">
      <div class="col-12">
        <h2>Eagle Scout Report</h2>
        <?php
        try {
          $sqlEagle = "SELECT Unit, Eagle, Palms, Date FROM adv_troop WHERE Eagle > 0 AND Date = ? ORDER BY Unit ASC";
          $stmt = mysqli_prepare($CTroop->getDbConn(), $sqlEagle);
          mysqli_stmt_bind_param($stmt, "i", $SelYear);
          mysqli_stmt_execute($stmt);
          $resultEagle = mysqli_stmt_get_result($stmt);
          if ($resultEagle) {
            if (mysqli_num_rows($resultEagle) > 0) {
              echo '<table class="table table-striped"><thead><tr>' .
                '<th>Unit-Troop</th><th>Eagle</th><th>Palms</th><th>Date</th></tr></thead><tbody>';
              while ($row = $resultEagle->fetch_assoc()) {
                $Unit = $row['Unit'];
                $UnitURL = "<a href='" . htmlspecialchars(SITE_URL . '/centennial/sites/advancement/src/Pages/Unit_View.php?btn=Units&unit_name=' . urlencode($Unit)) . "'>";
                $UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
                echo "<tr><td>$UnitView</td><td>" . htmlspecialchars($row["Eagle"]) . "</td><td>" .
                  htmlspecialchars($row["Palms"]) . "</td><td>" . htmlspecialchars($row["Date"]) . "</td></tr>";
              }
              echo "</tbody></table>";
            } else {
              echo "<p>No Eagle Scout data for $SelYear.</p>";
            }
            mysqli_free_result($resultEagle);
          } else {
            throw new Exception("Eagle query failed: " . mysqli_error($CTroop->getDbConn()));
          }
          echo "<p class='text-center'>Total Eagle Scouts for $SelYear: " . htmlspecialchars($troopEagle) . "</p>";
        } catch (Exception $e) {
          $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying Eagle Scout data: ' . $e->getMessage()];
          error_log("adv_report.php - Error: " . $e->getMessage(), 0);
        }
        ?>
      </div>
    </div>
  </div>
</sort_options>

<!-- Google Charts for Bar and Pie Charts -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  if (typeof google === 'undefined' || typeof google.charts === 'undefined') {
    console.error('Google Charts library failed to load.');
    <?php error_log('Google Charts library failed to load.'); ?>
    document.getElementById('PackChart').innerHTML = '<p>Error: Unable to load chart library.</p>';
    document.getElementById('Packpiechart').innerHTML = '<p>Error: Unable to load chart library.</p>';
    document.getElementById('TroopChart').innerHTML = '<p>Error: Unable to load chart library.</p>';
    document.getElementById('Trooppiechart').innerHTML = '<p>Error: Unable to load chart library.</p>';
  } else {
    google.charts.load('current', {
      packages: ['corechart']
    });
    google.charts.setOnLoadCallback(drawCharts);
  }


  function drawCharts() {
    try {
      // Verify chart containers exist
      const containers = ['PackChart', 'Packpiechart', 'TroopChart', 'Trooppiechart'];
      for (let id of containers) {
        if (!document.getElementById(id)) {
          console.error(`Chart container ${id} not found.`);
          return;
        }
      }

      // Check if Pack data is all zeros
      const packValues = [
        <?php echo (int)$PackTotals['lion']; ?>,
        <?php echo (int)$PackTotals['tiger']; ?>,
        <?php echo (int)$PackTotals['wolf']; ?>,
        <?php echo (int)$PackTotals['bear']; ?>,
        <?php echo (int)$PackTotals['webelos']; ?>,
        <?php echo (int)$PackTotals['aol']; ?>
      ];
      if (packValues.every(val => val === 0)) {
        <?php error_log("PackTotals: " . print_r($PackTotals, true)); ?>
        document.getElementById('PackChart').innerHTML = '<p>No Pack data available for <?php echo $SelYear; ?>.</p>';
        document.getElementById('Packpiechart').innerHTML = '<p>No Pack data available for <?php echo $SelYear; ?>.</p>';
      } else {
        // Pack Bar Chart
        var Packdata = google.visualization.arrayToDataTable([
          ['Rank', 'Awarded', {
            role: 'style'
          }],
          ['Lion', <?php echo (int)$PackTotals['lion']; ?>, 'yellow'],
          ['Tiger', <?php echo (int)$PackTotals['tiger']; ?>, 'orange'],
          ['Wolf', <?php echo (int)$PackTotals['wolf']; ?>, 'red'],
          ['Bear', <?php echo (int)$PackTotals['bear']; ?>, 'lightblue'],
          ['Webelos', <?php echo (int)$PackTotals['webelos']; ?>, 'green'],
          ['AOL', <?php echo (int)$PackTotals['aol']; ?>, 'lightgreen']
        ]);

        var Packview = new google.visualization.DataView(Packdata);
        Packview.setColumns([0, 1, {
          calc: 'stringify',
          sourceColumn: 1,
          type: 'string',
          role: 'annotation'
        }, 2]);
        var Packoptions = {
          title: 'District wide Pack Advancement Data',
          width: '100%',
          height: 400,
          bar: {
            groupWidth: '95%'
          },
          legend: {
            position: 'none'
          },
          bars: 'vertical'
        };

        var Packchart = new google.visualization.BarChart(document.getElementById('PackChart'));
        Packchart.draw(Packview, Packoptions);

        // Pack Pie Chart
        var piePackdata = google.visualization.arrayToDataTable([
          ['Category', 'Count'],
          ['Ranks', <?php echo (int)$PackTotals['YTD']; ?>],
          ['Adventures', <?php echo (int)$PackTotals['adventure']; ?>],
          ['Scouts', <?php echo (int)$PackTotals['youth']; ?>]
        ]);

        var piePackchart_options = {
          title: 'Ranks Earned vs. Scouts',
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
          pieSliceText: 'value',
          width: '100%',
          height: 400
        };

        var piePackchart = new google.visualization.PieChart(document.getElementById('Packpiechart'));
        piePackchart.draw(piePackdata, piePackchart_options);
      }

      // Check if Troop data is all zeros
      const troopValues = [
        <?php echo (int)$TroopTotals['Scout']; ?>,
        <?php echo (int)$TroopTotals['Tenderfoot']; ?>,
        <?php echo (int)$TroopTotals['SecondClass']; ?>,
        <?php echo (int)$TroopTotals['FirstClass']; ?>,
        <?php echo (int)$TroopTotals['Star']; ?>,
        <?php echo (int)$TroopTotals['Life']; ?>,
        <?php echo (int)$TroopTotals['Eagle']; ?>,
        <?php echo (int)$TroopTotals['Palms']; ?>
      ];
      if (troopValues.every(val => val === 0)) {
        <?php error_log("TroopTotals: " . print_r($TroopTotals, true)); ?>
        document.getElementById('TroopChart').innerHTML = '<p>No Troop data available for <?php echo $SelYear; ?>.</p>';
        document.getElementById('Trooppiechart').innerHTML = '<p>No Troop data available for <?php echo $SelYear; ?>.</p>';
      } else {
        // Troop Bar Chart
        var Troopdata = google.visualization.arrayToDataTable([
          ['Rank', 'Awarded', {
            role: 'style'
          }],
          ['Scout', <?php echo (int)$TroopTotals['Scout']; ?>, 'lightgray'],
          ['Tenderfoot', <?php echo (int)$TroopTotals['Tenderfoot']; ?>, 'blue'],
          ['Second Class', <?php echo (int)$TroopTotals['SecondClass']; ?>, 'lightgray'],
          ['First Class', <?php echo (int)$TroopTotals['FirstClass']; ?>, 'blue'],
          ['Star', <?php echo (int)$TroopTotals['Star']; ?>, 'lightgray'],
          ['Life', <?php echo (int)$TroopTotals['Life']; ?>, 'blue'],
          ['Eagle', <?php echo (int)$TroopTotals['Eagle']; ?>, 'lightgray'],
          ['Palms', <?php echo (int)$TroopTotals['Palms']; ?>, 'blue']
        ]);

        var Troopview = new google.visualization.DataView(Troopdata);
        Troopview.setColumns([0, 1, {
          calc: 'stringify',
          sourceColumn: 1,
          type: 'string',
          role: 'annotation'
        }, 2]);
        var Troopoptions = {
          title: 'District wide Troop Advancement Data',
          width: '100%',
          height: 400,
          bar: {
            groupWidth: '95%'
          },
          legend: {
            position: 'none'
          },
          bars: 'vertical'
        };

        var Troopchart = new google.visualization.BarChart(document.getElementById('TroopChart'));
        Troopchart.draw(Troopview, Troopoptions);

        // Troop Pie Chart
        var pieTroopdata = google.visualization.arrayToDataTable([
          ['Category', 'Count'],
          ['Ranks', <?php echo (int)$TroopTotals['YTD']; ?>],
          ['Merit Badges', <?php echo (int)$TroopTotals['MeritBadge']; ?>],
          ['Scouts', <?php echo (int)$TroopTotals['Youth']; ?>]
        ]);

        var pieTroopchart_options = {
          title: 'Ranks Earned vs. Scouts',
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
          pieSliceText: 'value',
          width: '100%',
          height: 400
        };

        var Trooppiechart = new google.visualization.PieChart(document.getElementById('Trooppiechart'));
        Trooppiechart.draw(pieTroopdata, pieTroopchart_options);
      }
    } catch (e) {
      console.error('Error drawing charts: ', e);
      <?php error_log('Error drawing charts:'); ?>
      document.getElementById('PackChart').innerHTML = '<p>Error rendering chart: ' + e.message + '</p>';
      document.getElementById('Packpiechart').innerHTML = '<p>Error rendering chart: ' + e.message + '</p>';
      document.getElementById('TroopChart').innerHTML = '<p>Error rendering chart: ' + e.message + '</p>';
      document.getElementById('Trooppiechart').innerHTML = '<p>Error rendering chart: ' + e.message + '</p>';
    }
  }
</script>