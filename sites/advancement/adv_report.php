<?php
if (!session_id()) {
  session_start();
}
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

include_once('CPack.php');
include_once('CTroop.php');
include_once('CUnit.php');

$CPack = CPack::getInstance();
$CTroop = CTroop::getInstance();
$CUnit = UNIT::getInstance();

if (isset($_POST['SubmitYear'])) {
  $SelYear = $_POST['Year'];
  $_SESSION['year'] = $SelYear;
  $CPack->SetYear($SelYear);
  $CTroop->SetYear($SelYear);
}

$PackTotals = $CPack->GetTotals();
$NumberofPacks = $CPack->GetNumofPacks();
$PackRatio = $CPack->GetDistrictRatio(null);

$TroopTotals = $CTroop->GetTotals();
$NumberofTroops = $CTroop->GetNumofTroops();
$TroopRatio = $CTroop->GetDistrictRatio();


// First get total number of troops
$sql = sprintf("SELECT * FROM adv_troop WHERE Date=%d ORDER BY Youth DESC", $CTroop->GetYear());
if ($resultTroop = mysqli_query($CTroop->getDbConn(), $sql)) {

  $TroopsBelow = 0;
  while ($row = $resultTroop->fetch_assoc()) {
    $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CTroop->GetYear());
    $UnitRankScout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row["Unit"]);
    if (floatval($UnitRankScout) <= $CTroop->GetDistrictGoal()) {
      $TroopsBelow++;
    }
  }
  mysqli_free_result($resultTroop);
}

$sqlTroop = sprintf('SELECT * FROM adv_troop WHERE Date=%d ORDER BY Unit ASC', $CTroop->GetYear());
$resultTroop = mysqli_query($CTroop->getDbConn(), $sqlTroop);

$TroopsAbove = $NumberofTroops - $TroopsBelow;


//First get total number of Pack in district
$sql = sprintf("SELECT * FROM adv_pack WHERE Date=%s", $CPack->GetYear());
if ($result = mysqli_query($CTroop->getDbConn(), $sql)) {
  $TotalPacks = mysqli_num_rows($result);
}
//Now get number below goal	
$PacksUnderGoal = 0;
$sqlPack = sprintf("SELECT * FROM adv_pack WHERE Date=%s ORDER BY Unit ASC", $CPack->GetYear());
if ($result = mysqli_query($CPack->getDbConn(), $sql)) {
  while ($row = $result->fetch_assoc()) {
    $UnitYouth = $CPack->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CPack->GetYear());
    $UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $row["YTD"] + $row['adventure'], $row["Unit"]);

    if ($UnitYouth == 0) {
      $PacksUnderGoal++;
    } elseif (floatval($UnitRankScout) <= $CPack->GetDistrictGoal(null)) {
      $PacksUnderGoal++;
    }
  }
  mysqli_free_result($result);
}
$PacksAboveGoal = $TotalPacks - $PacksUnderGoal;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("head.php"); ?>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {
      packages: ["corechart"]
    });
    google.charts.setOnLoadCallback(drawPackCharts);

    function drawPackCharts() {
      drawBarChart();
      drawPackPieChart();
    }


    function drawBarChart() {
      var Packdata = google.visualization.arrayToDataTable([
        ["Rank", "Awarded", {
          role: "style"
        }],
        <?php
        $strDebug = "['Lion',   " . $PackTotals['lion'] .      ",'yellow']," .
          "['Tiger',  " . $PackTotals['tiger'] .     ",'orange']," .
          "['Wolf',   " . $PackTotals['wolf'] .         ",'red']," .
          "['Bear',   " . $PackTotals['bear'] .   ",'lightblue']," .
          "['Webelos'," . $PackTotals['webelos'] .    ",'green']," .
          "['AOL',    " . $PackTotals['aol'] .   ",'lightgreen'],";
        echo $strDebug;
        ?>
      ]);

      var Packview = new google.visualization.DataView(Packdata);
      Packview.setColumns([0, 1,
        {
          calc: "stringify",
          sourceColumn: 1,
          type: "string",
          role: "annotation"
        },
        2
      ]);
      var Packoptions = {
        title: "District wide Pack advancement Data",
        width: 600,
        height: 300,
        bar: {
          groupWidth: "95%"
        },
        legend: {
          position: "none"
        },
        bars: 'vertical' // Required for Material Bar Charts.
      };

      var Packchart = new google.visualization.BarChart(document.getElementById("PackChart"));
      Packchart.draw(Packview, Packoptions);






      var Troopdata = google.visualization.arrayToDataTable([
        ["Rank", "Awarded", {
          role: "style"
        }],
        <?php
        echo "['Scout',         " . $TroopTotals['Scout'] .       ",'lightgray']," .
          "['Tenderfoot',    " . $TroopTotals['Tenderfoot'] .       ",'blue']," .
          "['Second Class',  " . $TroopTotals['SecondClass'] . ",'lightgray']," .
          "['First Class',   " . $TroopTotals['FirstClass'] .       ",'blue']," .
          "['Star',          " . $TroopTotals['Star'] .        ",'lightgray']," .
          "['Life',          " . $TroopTotals['Life'] .             ",'blue']," .
          "['Eagle',         " . $TroopTotals['Eagle'] .       ",'lightgray']," .
          "['Palms',         " . $TroopTotals['Palms'] .            ",'blue']";
        ?>
      ]);

      var Troopview = new google.visualization.DataView(Troopdata);
      Troopview.setColumns([0, 1,
        {
          calc: "stringify",
          sourceColumn: 1,
          type: "string",
          role: "annotation"
        },
        2
      ]);
      var Troopoptions = {
        title: "District wide Troop advancement Data",
        width: 600,
        height: 300,
        bar: {
          groupWidth: "95%"
        },
        legend: {
          position: "none"
        },
        bars: 'vertical' // Required for Material Bar Charts.
      };

      var Troopchart = new google.visualization.BarChart(document.getElementById("TroopChart"));
      Troopchart.draw(Troopview, Troopoptions);
    }

    function drawPackPieChart() {
      var piePackdata = google.visualization.arrayToDataTable([
        ['Packs meeting goal', 'Packs below goal'],
        <?php
        $PackRanks = $PackTotals['YTD'] + $PackTotals['adventure'];
        echo "['Ranks'," . $PackTotals['YTD'] . "],";
        echo "['Adventure'," . $PackTotals['adventure'] . "],";
        echo "['Scouts'," . $PackTotals['youth'] . "]";
        ?>
      ]);
      var piePackchart_options = {
        title: 'Rank eraned .vs. Scouts',
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
      var piePackchart = new google.visualization.PieChart(document.getElementById('Packpiechart'));
      piePackchart.draw(piePackdata, piePackchart_options);


      var pieTroopdata = google.visualization.arrayToDataTable([
        ['Troops meeting goal', 'Troops below goal'],
        <?php
        echo "['Ranks'," . $TroopTotals['YTD'] . "],";
        echo "['Merit Bagdes'," . $TroopTotals['MeritBadges'] . "],";
        echo "['Scouts'," . $TroopTotals['Youth'] . "]";
        ?>
      ]);

      var pieTroopchart_options = {
        title: 'Rank eraned .vs. Scouts',
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

      var Troopchart = new google.visualization.PieChart(document.getElementById('Trooppiechart'));

      Troopchart.draw(pieTroopdata, pieTroopchart_options);

    }
  </script>

</head>

<body>
  <header id="header" class="header sticky-top">
    <?php $navbarTitle = 'Centennial District Advancment Report'; ?>
    <?php include('navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include 'sidebar.php'; ?>
      <sort_options>
        <div class="px-lg-5">
          <div class="row">
            <div class="col-1">
              <?php $SelYear = $CTroop->SelectYear(); ?>
              <!-- </div> -->
              <div>
                <table>
                  <!-- Pack Data -->
                  <tr>
                    <td>
                      <div id="PackChart" style="width: 500px; height: 500px;"></div>
                    </td>
                    <td>
                      <div id="Packpiechart" style="width: 500px; height: 400px;"></div>
                      <p>District wide advancement ratio:
                        <?php echo number_format($CPack->GetDistrictRatio(null), 2, '.', '') ?> / District goal:
                        <?php echo number_format($CPack->GetDistrictGoal(null), 2, '.', '') ?> </p>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-10">
              <?php
              //CHeader::DisplayPageHeader("Centennial District Pack Advancmenet Report", "", "");
              echo "<p style='text-align: center;'>Number of units <b>below</b> goal: " . $PacksUnderGoal . " Out of: " . $TotalPacks . " Packs</p>"; ?>
              <div class="px-5">
                <table class="table table-striped">
                  <td style="width:120px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                  <td style="width:50px">
                    <tr>
                      <th>Unit</th>
                      <th>Lion</th>
                      <th>Tiger</th>
                      <th>Wolf</th>
                      <th>Bear</th>
                      <th>WEBLOS</th>
                      <th>AOL</th>
                      <th>Total Rank</th>
                      <th>Total Youth</th>
                      <th>Rank /Scout</th>
                      <th>Adventures</th>
                      <th>Date</th>
                    </tr>
                    <?php
                    if ($result = mysqli_query($CPack->getDbConn(), $sqlPack)) {
                      while ($row = $result->fetch_assoc()) {
                        $UnitYouth = $CPack->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CPack->GetYear());
                        $UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $row["YTD"] + $row['adventure'], $row["Unit"]);
                        $Unit = $row['Unit'];
                        $UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
                        $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
                        if (floatval($UnitRankScout) >= $CPack->GetDistrictGoal($row["Date"])) {
                          continue;
                        }

                        if ($UnitRankScout == 0) // Make it Bold
                          $Formatter = "<b style='color:red;'>";
                        else if ($UnitRankScout >= $CPack->GetDistrictGoal($row["Date"]) && $UnitRankScout < $CPack->GetIdealGoal($row["Date"]))
                          $Formatter = "<b style='color:orange;'>";
                        else if ($UnitRankScout >= $CPack->GetIdealGoal($row["Date"]))
                          $Formatter = "<b style='color:green;'>";
                        else
                          $Formatter = "";


                        echo "<tr><td>" .
                          $UnitView . "</td><td>" .
                          $Formatter . $row["lion"] . "</td><td>" .
                          $Formatter . $row["tiger"] . "</td><td>" .
                          $Formatter . $row["wolf"] . "</td><td>" .
                          $Formatter . $row["bear"] . "</td><td>" .
                          $Formatter . $row["webelos"] . "</td><td>" .
                          $Formatter . $row["aol"] . "</td><td>" .
                          $Formatter . $row["YTD"] . "</td><td>" .
                          $Formatter . $UnitYouth . "</td><td>" .
                          $Formatter . $UnitRankScout . "</td><td>" .
                          $Formatter . $row["adventure"] . "</td><td>" .
                          $Formatter . $row["Date"] . "</td></tr>";
                      }
                      echo "</table>";
                      echo "<p style='text-align: center;padding-bottom: 5rem !important;'>Data last updated: " . $CPack->GetLastUpdated("adv_pack") . "</p>";
                      echo "</div>";
                    } else {
                      echo "0 result<br/>";
                      echo $year;
                    }

                    if ($PacksUnderGoal > 0)
                      mysqli_free_result($result);
                    ?>
                </table>
                <!-- Now display the Troop data.. -->
                <p style="page-break-after: always;">&nbsp;</p>
                <!----------------------------------------------->
                <!-- Membership Data                           -->
                <!----------------------------------------------->
                <div class="px-5">
                  <?php $navbarTitle = 'Centennial District Troop Advancement Report'; ?>
                  <?php include('navbar.php'); ?>
                </div>

                <table>
                  <!-- Troop Data -->
                  <tr>
                    <td>
                      <div id="TroopChart" style="width: 500px; height: 500px;"></div>
                    </td>
                    <td>
                      <div id="Trooppiechart" style="width: 500px; height: 400px;"></div>
                      <?php if ($TroopTotals['Youth'] > 0) { ?>
                        <p>District wide advancement ratio:
                          <?php echo number_format(($TroopTotals['YTD'] + $TroopTotals['MeritBadges']) / $TroopTotals['Youth'], 2, '.', ''); ?>
                          / District goal: <?php echo number_format($CTroop->GetDistrictGoal(), 2, '.', '') ?> </p>
                      <?php } ?>
                    </td>
                  </tr>

                </table>
                <?php echo "<p style='text-align: center;'>Number of units <b>below</b> goal: " . $CTroop->GetTroopsBelowGoal() . " Out of: " . $NumberofTroops . " Troops</p>"; ?>

                <div class="px-5">
                  <table class="table table-striped">
                    <tr>
                      <th>Unit</th>
                      <th>Scout</th>
                      <th>Tenderfoot</th>
                      <th>Second Class</th>
                      <th>First Class</th>
                      <th>Star</th>
                      <th>Life</th>
                      <th>Eagle</th>
                      <th>Palms</th>
                      <th>Merit Badges</th>
                      <th>Total Rank</th>
                      <th>Total Youth</th>
                      <th>Rank /Scout</th>
                      <th>Date</th>

                    </tr>
                    <?php
                    //echo $TroopsBelow;
                    if ($TroopsBelow > 0) {
                      while ($row = $resultTroop->fetch_assoc()) {
                        $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CTroop->GetYear());
                        $UnitRankScout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row["Unit"]);
                        $Unit = $row['Unit'];
                        $UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
                        $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
                        if (floatval($UnitRankScout) >= $CTroop->GetDistrictGoal()) {
                          continue;
                        }

                        if ($UnitRankScout == 0) // Make it Bold
                          $Formatter = "<b style='color:red;'>";
                        else if ($UnitRankScout >= $CTroop->GetDistrictGoal() && $UnitRankScout < $CTroop->GetIdealGoal())
                          $Formatter = "<b style='color:orange;'>";
                        else if ($UnitRankScout >= $CTroop->GetIdealGoal())
                          $Formatter = "<b style='color:green;'>";
                        else
                          $Formatter = "";

                        echo "<tr><td>" .
                          $UnitView . "</td><td>" .
                          $Formatter . $row["Scout"] . "</td><td>" .
                          $Formatter . $row["Tenderfoot"] . "</td><td>" .
                          $Formatter . $row["SecondClass"] . "</td><td>" .
                          $Formatter . $row["FirstClass"] . "</td><td>" .
                          $Formatter . $row["Star"] . "</td><td>" .
                          $Formatter . $row["Life"] . "</td><td>" .
                          $Formatter . $row["Eagle"] . "</td><td>" .
                          $Formatter . $row["Palms"] . "</td><td>" .
                          $Formatter . $row["MeritBadge"] . "</td><td>" .
                          $Formatter . $row["YTD"] . "</td><td>" .
                          $Formatter . $UnitYouth . "</td><td>" .
                          $Formatter . $UnitRankScout  . "</td><td>" .
                          $Formatter . $row["Date"] . "</td></tr>";
                      } ?>
                      </table>
                      <p style='text-align: center;padding-bottom: 5rem !important;'><?php echo "Data last updated: " . $CTroop->GetLastUpdated("adv_troop") ?></p>
                      </div>
                      <?php
                    } else {
                      echo "0 result";
                    }

                    //if ($TroopsBelow > 0)
                    //	mysqli_free_result($resultTroop);
                    //
                    ?>
                  </table>

                  <!--  Now display the Youth data.. -->
                  <p style="page-break-after: always;">&nbsp;</p>
                  <!----------------------------------------------->
                  <!-- Membership Data                           -->
                  <!----------------------------------------------->
                  <div class="px-5">
                    <?php $navbarTitle = 'Centennial District Membership Report'; ?>
                    <?php include('navbar.php'); ?>
                  </div>

                  <?php

                  $CUnit->DisplayMembershipTable();

                  ?>
                  <!-- </div> -->

                  <p style="page-break-after: always;">&nbsp;</p>

                  <!----------------------------------------------->
                  <!-- Eagle Data                                -->
                  <!----------------------------------------------->
                  <div class="px-5">
                    <?php $navbarTitle = 'Centennial District Eagle Report'; ?>
                    <?php include('navbar.php'); ?>
                  </div>

                  <div class="px-5">
                    <table class="table table-striped">
                      <td style="width:150px">
                      <td style="width:50px">
                      <td style="width:50px">
                      <td style="width:50px">
                        <tr>
                          <th>Unit-Troop</th>
                          <th>Eagle</th>
                          <th>Palms</th>
                          <th>Date</th>

                        </tr>
                        <?php
                        echo "<br/>";

                        $sql = sprintf('SELECT * FROM adv_troop WHERE Eagle>0 AND Date=%d ORDER BY `Unit` ASC', $CTroop->GetYear());
                        if ($result = $CTroop->doQuery($sql)) {
                          $rowcount = mysqli_num_rows($result);
                        }

                        if ($rowcount > 0) {
                          while ($row = $result->fetch_assoc()) {
                            $Unit = $row['Unit'];
                            $UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
                            $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
                            echo "<tr><td>" .
                              $UnitView . "</td><td>" .
                              $row["Eagle"] . "</td><td>" .
                              $row["Palms"] . "</td><td>" .
                              $row["Date"] . "</td></tr>";
                          }
                          echo "</table>";
                          echo "</div>";
                        }

                        if ($rowcount > 0)
                          mysqli_free_result($result);
                        ?>
                    </table>

                    <?php
                    $sql = sprintf('SELECT SUM(Eagle) FROM adv_troop WHERE Date=%d', $CTroop->GetYear());

                    if ($result = $CTroop->doQuery($sql)) {
                      $rowcount = mysqli_num_rows($result);
                    }
                    $row = $result->fetch_assoc();
                    $troopEagle = $row['SUM(Eagle)'];



                    echo "<p  style='text-align: center;padding-bottom: 5rem !important;'> Total Eagle Scouts for the Current year: " . $troopEagle . " </p>";
                    ?>

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