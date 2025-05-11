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

//require 'Support_Functions.php';
include_once('CPack.php');

$CPack = CPack::getInstance();

if (isset($_POST['SubmitYear'])) {
  $SelYear = $_POST['Year'];
  $_SESSION['year'] = $SelYear;
  $CPack->SetYear($SelYear);
}

$Totals = $CPack->GetTotals();

$PackData =  "['Lion',"    . $Totals['lion']   . "]," .
  "['Tiger',"   . $Totals['tiger']  . "]," .
  "['Wolf',"    . $Totals['wolf']   . "]," .
  "['Bear',"    . $Totals['bear']   . "]," .
  "['Webelos'," . $Totals['webelos'] . "]," .
  "['AOL',"     . $Totals['aol']    . "],";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>

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
        <?php
        echo $PackData;
        ?>
      ]);

      var BarChartoptions = {
        chart: {
          title: 'District wide Pack advancement Data',
          subtitle: 'Year to date',

        },
        bars: 'vertical' // Required for Material Bar Charts.
      };

      var BarChartchart = new google.charts.Bar(document.getElementById('barchart_material'));

      BarChartchart.draw(data, google.charts.Bar.convertOptions(BarChartoptions));
    }

    function drawPieChart() {
      var piedata = google.visualization.arrayToDataTable([
        ['Packs meeting goal', 'Packs below goal'],
        <?php
        echo "['Ranks'," . $Totals['YTD'] . "],";
        echo "['Adventure'," . $Totals['adventure'] . "],";
        echo "['Scouts'," . $Totals['youth'] . "]";
        ?>
      ]);

      var piechart_options = {
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

      var piechart = new google.visualization.PieChart(document.getElementById('piechart'));

      piechart.draw(piedata, piechart_options);

    }
  </script>
</head>

<body style="padding:10px">
  <header id="header" class="header sticky-top">
    <?php $navbarTitle = 'Centennial District Pack Advancement Summary'; ?>
    <?php include('navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include 'sidebar.php'; ?>
      <sort_options>
        <div class="px-lg-5">
          <div class="row">
            <div class="col-1">
              <?php $SelYear = $CPack->SelectYear(); ?>
              <!-- </div> -->
              <div class="col-4">
                <div id="barchart_material" style="margin-left: -200px; width: 600px; height: 300px;"></div>
              </div>
              <div class="col-3">
                <!-- <div id="piechart" style="width: 500px; height: 400px;"></div> -->
                <div id="piechart" style="margin-left: -200px;"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-10">
              <div class="py-5">
                <p style='text-align: center;padding-bottom: 5rem !important;'>District wide advancement ratio: <?php echo number_format($CPack->GetDistrictRatio(null), 2, '.', '')
                                                                                                                ?> / District goal: <?php echo number_format($CPack->GetDistrictGoal(null), 2, '.', '')
                                                                                                                                    ?> </p>
                <hr />

                <?php
                // TODO: FIxed SelYear !!
                if ($SelYear == null)
                  $SelYear = Date("Y");
                ?>
                <div class="py-5">
                  <?php
                  $CPack->DisplayAdvancmenetDescription();
                  $CPack->DisplayUnitAdvancement();
                  echo "<p style='text-align: center;'>Number of Packs in District: " . $CPack->GetNumofPacks() . "</p>";

                  if ($CPack->GetNumofPacks() > 0) {
                    $PackData = $CPack->GetPack();
                    while ($PackAdv = $PackData->fetch_assoc()) {
                      $UnitYouth = $CPack->GetUnitTotalYouth($PackAdv['Unit'], $PackAdv['Youth'], $SelYear);
                      $UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $PackAdv["YTD"] + $PackAdv["adventure"], $PackAdv["Unit"]);
                      $Unit = $PackAdv['Unit'];
                      $UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
                      $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
                      if ($UnitRankScout == 0) // Make it Bold
                        $Formatter = "<b style='color:red;'>";
                      else if ($UnitRankScout >= $CPack->GetDistrictGoal($PackAdv['Date']) && $UnitRankScout < $CPack->GetIdealGoal($PackAdv['Date']))
                        $Formatter = "<b style='color:orange;'>";
                      else if ($UnitRankScout >= $CPack->GetIdealGoal($PackAdv['Date']))
                        $Formatter = "<b style='color:green;'>";
                      else
                        $Formatter = "";
                      echo "<tr><td>" .
                        $UnitView . "</td><td>" .
                        $Formatter . $PackAdv["lion"] . "</td><td>" .
                        $Formatter . $PackAdv["tiger"] . "</td><td>" .
                        $Formatter . $PackAdv["wolf"] . "</td><td>" .
                        $Formatter . $PackAdv["bear"] . "</td><td>" .
                        $Formatter . $PackAdv["webelos"] . "</td><td>" .
                        $Formatter . $PackAdv["aol"] . "</td><td>" .
                        $Formatter . $PackAdv["YTD"] . "</td><td>" .
                        $Formatter . $UnitYouth . "</td><td>" .
                        $Formatter . $UnitRankScout . "</td><td>" .
                        $Formatter . $PackAdv["adventure"] . "</td><td>" .
                        $Formatter . $PackAdv["Date"] . "</td></tr>";
                      if ($UnitRankScout == 0) // Make it Bold
                        echo "</b>";
                    }
                    echo "</table>";
                    echo "</div>";
                  } else {
                    echo "0 result<br/>";
                    echo $_SESSION["year"];
                  }
                  if ($CPack->GetNumofPacks() > 0)
                    mysqli_free_result($PackData);
                  ?>
                  </table>
                  <?php echo "<p style='text-align: center;padding-bottom: 5rem !important;'>Data last updated: " . $CPack->GetLastUpdated("adv_pack") . "</p>"; ?>
                </div>
              </div>
            </div>
          </div>
      </sort_options>
    </div>


    <!-- Footer-->
    <?php include 'Footer.php'; ?>
</body>

</html>