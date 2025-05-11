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

//require 'Support_Functions.php';
include_once('CTroop.php');

$CTroop = CTroop::getInstance();

if (isset($_POST['SubmitYear'])) {
  $SelYear = $_POST['Year'];
  $_SESSION['year'] = $SelYear;
  $CTroop->SetYear($SelYear);
}

$Totals = $CTroop->GetTotals();
$NumofTroops = $CTroop->GetNumofTroops();

$data = "['Scout'," .        $Totals['Scout'] . "]," .
  "['Tenderfoot'," .   $Totals['Tenderfoot'] . "]," .
  "['Second'," . $Totals['SecondClass'] . "]," .
  "['First'," .  $Totals['FirstClass'] . "]," .
  "['Star'," .         $Totals['Star'] . "]," .
  "['Life'," .         $Totals['Life'] . "]," .
  "['Eagle'," .        $Totals['Eagle'] . "]," .
  "['Palms'," .        $Totals['Palms'] . "],";

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
        echo $data;
        ?>
      ]);

      var BarChartoptions = {
        chart: {
          title: 'District wide Troop advancement Data',
          subtitle: 'Year to date',
        },
        bars: 'vertical' // Required for Material Bar Charts.
      };

      var BarChartchart = new google.charts.Bar(document.getElementById('barchart_material'));

      BarChartchart.draw(data, google.charts.Bar.convertOptions(BarChartoptions));
    }


    function drawPieChart() {
      var piedata = google.visualization.arrayToDataTable([
        ['Troops meeting goal', 'Troops below goal'],
        <?php
        echo "['Ranks'," . $Totals['YTD'] . "],";
        echo "['Merit Badges'," . $Totals['MeritBadges'] . "],";
        echo "['Scouts'," . $Totals['Youth'] . "]";
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
    <?php $navbarTitle = 'Centennial District Troop Summary'; ?>
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
              <div class="col-5">
                <!-- <div id="barchart_material" style="width: 790px; height: 400px;"></div> -->
                <div id="barchart_material" style="margin-left: -200px; width: 600px; height: 300px;"></div>
              </div>
              <div class="col-3">
                <!-- <div id="piechart" style="width: 500px; height: 400px;"></div> -->
                <div style="margin-left: -200px;" id="piechart"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="px-lg-5 col-10">
              <div class="py-5">
                <?php
                if ($CTroop->GetNumofTroops() > 0) { ?>
                  <p style='text-align: center;padding-bottom: 5rem !important;'>District wide advancement ratio: <?php echo number_format($CTroop->GetDistrictRatio(), 2, '.', ''); ?> / District goal: <?php echo number_format($CTroop->GetDistrictGoal(), 2, '.', '') ?> </p>
                  <hr />
              </div>
            <?php }

                $CTroop->DisplayAdvancmenetDescription();
                $CTroop->DisplayUnitAdvancement();
                echo "<p style='text-align: center;'>
                Number of Troops in District: " . $CTroop->GetNumofTroops();
                $sql = sprintf("SELECT * FROM adv_troop WHERE Date=%d ORDER BY Unit ASC", $CTroop->GetYear());
                if ($result = mysqli_query($CTroop->getDbConn(), $sql)) {
                  $rowcount = mysqli_num_rows($result);
                }


                //echo $rowcount;
                if ($rowcount > 0) {
                  while ($row = $result->fetch_assoc()) {

                    $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $row["Date"]);
                    $Rank_Scout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row['Unit']);
                    $Unit = $row['Unit'];
                    $UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
                    $UnitView = sprintf(" %s%s>%s</a>", $UnitURL, $Unit, $Unit);

                    if ($Rank_Scout == 0) // Make it Bold
                      $Formatter = "<b style='color:red;'>";
                    else if ($Rank_Scout >= 2.0 && $Rank_Scout < 4.0)
                      $Formatter = "<b style='color:orange;'>";
                    else if ($Rank_Scout >= 4.0)
                      $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr>
                        <td>" .
                      $UnitView . "</td>
                        <td>" .
                      $Formatter . $row["Scout"] . "</td>
                        <td>" .
                      $Formatter . $row["Tenderfoot"] . "</td>
                        <td>" .
                      $Formatter . $row["SecondClass"] . "</td>
                        <td>" .
                      $Formatter . $row["FirstClass"] . "</td>
                        <td>" .
                      $Formatter . $row["Star"] . "</td>
                        <td>" .
                      $Formatter . $row["Life"] . "</td>
                        <td>" .
                      $Formatter . $row["Eagle"] . "</td>
                        <td>" .
                      $Formatter . $row["Palms"] . "</td>
                        <td>" .
                      $Formatter . $row["MeritBadge"] . "</td>
                        <td>" .
                      $Formatter . $row["YTD"] . "</td>
                        <td>" .
                      $Formatter . $UnitYouth . "</td>
                        <td>" .
                      $Formatter . $Rank_Scout . "</td>
                        <td>" .
                      $Formatter . $row["Date"] . "</td>
                      </tr>";
                    if ($Rank_Scout == 0) // Make it Bold
                      echo "</b>";
                  }
                  echo "</table>";
                } else {
                  echo "0 result<br />";
                }

                if ($rowcount > 0)
                  mysqli_free_result($result);
            ?>
            <p style='text-align: center;padding-bottom: 5rem !important;'><?php echo "Data last updated: " . $CTroop->GetLastUpdated("adv_troop") ?></p>

            ?>
            </div>
          </div>
      </sort_options>
    </div>
  </div>
  <!-- Footer-->
  <?php include 'Footer.php'; ?>
</body>

</html>