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
// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  die('An error occurred. Please try again later.');
}

load_template('/src/Classes/CCrew.php');

$CCrew = CCrew::getInstance();

if (isset($_POST['SubmitYear'])) {
  $SelYear = $_POST['Year'];
  $_SESSION['year'] = $SelYear;
  $CCrew->SetYear($SelYear);
}

$Totals = $CCrew->GetTotals();
$NumofCrews = $CCrew->GetNumofCrews();


$sql = sprintf("SELECT * FROM adv_crew WHERE Date=%d ORDER BY Unit ASC", $CCrew->GetYear());
//echo $sql;
if ($result = mysqli_query($CCrew->getDbConn(), $sql)) {
  $rowcount = mysqli_num_rows($result);
}

$data = "['Discovery'," .        $Totals['Discovery'] . "]," .
  "['Pathfinder'," .   $Totals['Pathfinder'] . "]," .
  "['Summit'," . $Totals['Summit'] . "]," .
  "['Venturing'," .        $Totals['Venturing'] . "],";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar', 'corechart']
    });
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
      drawBarChart();
    }


    function drawBarChart() {
      var data = google.visualization.arrayToDataTable([
        ['Award', 'Awards'],
        <?php
        echo $data;
        ?>
      ]);

      var BarChartoptions = {
        chart: {
          title: 'District wide Crew Awards Data',
          subtitle: 'Year to date',
        },
        bars: 'vertical' // Required for Material Bar Charts.
      };

      var BarChartchart = new google.charts.Bar(document.getElementById('barchart_material'));

      BarChartchart.draw(data, google.charts.Bar.convertOptions(BarChartoptions));
    }
  </script>

</head>

<body style="padding:10px">
  <header id="header" class="header sticky-top">
    <?php $navbarTitle = 'Centennial District Crew Awards'; ?>
    <?php load_template('/src/Templates/navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php load_template('/src/Templates/sidebar.php'); ?>
      <div class="container-fluid">
        <sort_options>
          <div class="px-lg-5">
            <div class="row">
              <div class="col-1">
                <?php $SelYear = $CCrew->SelectYear(); ?>
                <!-- </div> -->
                <div class="col-4 chart_div">
                  <div id="barchart_material" style="margin-left: -200px; width: 600px; height: 300px;"></div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-10 py-5">
                <?php
                $CCrew->DisplayUnitAdvancement();

                //echo $rowcount;
                if ($rowcount > 0) {
                  while ($row = $result->fetch_assoc()) {

                    $UnitYouth = $CCrew->GetUnitTotalYouth($row['Unit'], $row["Youth"], $row["Date"]);
                    $Rank_Scout = sprintf("%.2f", ($row["YTD"] + $row['MeritBadge']) / $UnitYouth);
                    $Unit = $row['Unit'];
                    $UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
                    $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);


                    echo "<tr><td>" .
                      $UnitView . "</td><td>" .
                      $row["Star"] . "</td><td>" .
                      $row["Life"] . "</td><td>" .
                      $row["Eagle"] . "</td><td>" .
                      $row["Palms"] . "</td><td>" .
                      $row["MeritBadge"] . "</td><td>" .
                      $row["YTD"] . "</td><td>" .
                      $UnitYouth . "</td><td>" .
                      $Rank_Scout . "</td><td>" .
                      $row["discovery"] . "</td><td>" .
                      $row["pathfinder"] . "</td><td>" .
                      $row["summit"] . "</td><td>" .
                      $row["venturing"] . "</td><td>" .
                      $row["Date"] . "</td></tr>";
                  }
                  echo "</table>";
                } else {
                  echo "0 result<br/>";
                  echo $year;
                }

                if ($rowcount > 0)
                  mysqli_free_result($result);
                ?>
                </table>
                <p style='text-align: center;padding-bottom: 5rem !important;'><?php echo "Data last updated: " . $CCrew->GetLastUpdated("adv_crew") ?></p>

              </div>
            </div>
          </div>
        </sort_options>
      </div>
    </div>
  </div>

  <!-- Footer-->
  <?php load_template('/src/Templates/Footer.php'); ?>
</body>

</html>