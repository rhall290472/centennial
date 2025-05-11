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
$TroopAbove = $CTroop->GetTroopsAboveGoal();
$TroopBelow = $NumofTroops - $TroopAbove;

$sql = sprintf('SELECT * FROM adv_troop WHERE Date=%d ORDER BY Unit ASC', $CTroop->GetYear());
$result = $CTroop->doQuery($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = google.visualization.arrayToDataTable([
        ['Troops below goal', 'Troops above goal'],
        <?php
        echo "['Above'," . $TroopAbove . "],";
        echo "['Below'," . $TroopBelow . "]";

        ?>
      ]);

      var options = {
        title: 'Troops meeting ideal goal',
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
    <?php $navbarTitle = 'Centennial District Troop(s) Meeting Goal of 2 Rank/Scout'; ?>
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
              <div class="col-9">
                <table>
                  <tr>
                    <td>
                      <!-- <div id="piechart" style="width: 500px; height: 400px;"></div> -->
                      <div id="piechart"></div>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="px-lg-5 col-10">
              <div class="py-5">
                <?php
                $CTroop->DisplayAdvancmenetDescription();
                $CTroop->DisplayUnitAdvancement();
                echo "<p  style='text-align: center;'>Number of units above goal: " . $TroopAbove . " Out of: " . $NumofTroops . " Troops </p>";
                //echo $rowcount;
                while ($row = $result->fetch_assoc()) {
                  $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CTroop->GetYear());
                  $UnitRankScout = $CTroop->GetUnitRankperScout($row["Youth"], ($row["YTD"] + $row["MeritBadge"]), $row["Unit"]);
                  $Unit = $row['Unit'];
                  $UnitURL = "<a href='https://centennialdistrict.co/Unit_View.php?btn=Units&unit_name=$Unit'";
                  $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
                  if (floatval($UnitRankScout) < $CTroop->GetDistrictGoal($row["Date"])) {
                    continue;
                  }

                  if ($UnitRankScout == 0) // Make it Bold
                    $Formatter = "<b style='color:red;'>";
                  else if ($UnitRankScout >= $CTroop->GetDistrictGoal($row['Date']) && $UnitRankScout < $CTroop->GetIdealGoal($row['Date']))
                    $Formatter = "<b style='color:orange;'>";
                  else if ($UnitRankScout >= $CTroop->GetIdealGoal($row['Date']))
                    $Formatter = "<b style='color:green;'>";
                  else
                    $Formatter = "";

                  echo "<tr><td>" .
                    $Formatter . $UnitView . "</td><td>" .
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
                    $Formatter . $UnitRankScout . "</td><td>" .
                    $Formatter . $row["Date"] . "</td></tr>";
                }
                echo "</table>";

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
  <?php include 'Footer.php'; ?>
</body>

</html>