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

//require 'Support_Functions.php';
load_template('/src/Classes/CTroop.php');

$CTroop = CTroop::getInstance();

if (isset($_POST['SubmitYear'])) {
  $SelYear = $_POST['Year'];
  $_SESSION['year'] = $SelYear;
  $CTroop->SetYear($SelYear);
}

$Totals = $CTroop->GetTotals();
$NumofTroops = $CTroop->GetNumofTroops();
$TroopAbove = $CTroop->GetTroopsAboveGoal();
$TroopBelow = $NumofTroops - $TroopAbove;

$sql = sprintf('SELECT * FROM adv_troop WHERE Date=%d ORDER BY Unit ASC', $CTroop->GetYear());
$result = $CTroop->doQuery($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>


  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = google.visualization.arrayToDataTable([
        ['Above', 'Below'],
        <?php
        echo "['Above'," . $TroopAbove . "],";
        echo "['Below'," . $TroopBelow . "]";
        ?>
      ]);

      var options = {
        title: 'Troops meeting District goal',
        slices: {
          0: {
            color: 'green'
          },
          1: {
            color: 'red'
          }
        },
        pieSliceText: 'value'
      };

      var chart = new google.visualization.PieChart(document.getElementById('piechart'));

      chart.draw(data, options);
    }
  </script>
</head>

<body style="padding:10px">
  <header id="header" class="header sticky-top">
    <?php $navbarTitle = 'Centennial District Troop(s) Below Goal of 2 Rank/Scout'; ?>
    <?php load_template('/src/Templates/navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php load_template('/src/Templates/sidebar.php'); ?>
      <sort_options>
        <div class="px-lg-5">
          <div class="row">
            <div class="col-1">
              <?php $SelYear = $CTroop->SelectYear(); ?>
              <!-- </div> -->
              <div class="col-4">
                <div id="piechart"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="px-lg-5 col-10">
              <div class="py-5">
                <?php
                $CTroop->DisplayAdvancmenetDescription();
                $CTroop->DisplayUnitAdvancement();

                echo "<p  style='text-align: center;'>Number of units below goal: " . $TroopBelow . " Out of: " . $NumofTroops . " Troops </p>";
                //echo $TroopsBelow;
                if ($TroopBelow > 0) {
                  while ($row = $result->fetch_assoc()) {
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
                  }
                  echo "</table>";
                } else {
                  echo "0 result";
                }

                if ($TroopBelow > 0)
                  mysqli_free_result($result);
                ?>
                </table>
                <?php echo "<p style='text-align: center;padding-bottom: 5rem !important;'>Data last updated: " . $CTroop->GetLastUpdated("adv_troop") . "</p>"; ?>

              </div>
            </div>
          </div>
        </div>
      </sort_options>
    </div>
  </div>

  <!-- Footer-->
  <?php load_template('/src/Templates/Footer.php'); ?>
</body>

</html>