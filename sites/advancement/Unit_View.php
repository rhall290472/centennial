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

include_once('CAdvancement.php');
include_once('CUnit.php');
include_once('cAdultLeaders.php');


$UNIT = UNIT::getInstance();
$cAdultLeaders = AdultLeaders::getInstance();

// Get Unit Name if supplied..
if (isset($_GET['unit_name'])) {
  $unit_name = $_GET['unit_name'];
} else
  $unit_name = "";

if (empty($unit_name)) {
  $Direct = $cAdultLeaders->DirectTrained($unit_name, "YES");
  $NonDirect = $cAdultLeaders->DirectTrained($unit_name, "NO");

  $sql = sprintf("SELECT * FROM trainedleaders  WHERE Unit <> '' ORDER BY Unit ASC");
  $sqlUnTrained  = sprintf("SELECT * FROM trainedleaders WHERE Unit <> '' AND Trained = 'NO' ORDER BY Direct_Contact_Leader");
  $sqlCO = sprintf('SELECT * FROM membershiptotals WHERE Unit = "%s" ;', $unit_name);
  $resultQueryCO = $UNIT->getDbConn()->query($sqlCO);
  $resultCO =  $resultQueryCO->fetch_assoc();
  if (isset($resultCO)) {
    $UnitExpire = $resultCO['Expire_Date'];
    $UnitCO = sprintf("Chartered Organization: %s, Expire Date: %s", $UnitCO, $UnitExpire);
    $UnitCO = $resultCO['Chartered_Org'];
  } else {
    $UnitCO = "";
    $UnitCommissioner = "";
  }
} else {
  // Get data for selected unit.
  $Direct = $cAdultLeaders->DirectTrained($unit_name, "YES");
  $NonDirect = $cAdultLeaders->DirectTrained($unit_name, "NO");

  $sqlCO = sprintf('SELECT * FROM membershiptotals WHERE Unit = "%s" ;', $unit_name);
  $resultQueryCO = $UNIT->getDbConn()->query($sqlCO);
  if (!$resultQueryCO) {
    $strMsg = "ERROR: getDbConn()->query(" . $sqlCO . ") in file Unit_View.php";
    error_log($strMsg);
    $UNIT->function_alert("Error on page. Error has been logged.");
    $UNIT->GotoURL("index.php");
  }
  if ($resultCO =  $resultQueryCO->fetch_assoc()) {
    $UnitCO = $resultCO['Chartered_Org'];
    $UnitExpire = $resultCO['Expire_Date'];
    $Commissioner = $resultCO['Assigned_Commissioner'];
    $Contact = $resultCO['Last_Contact'];
  } else {
    $UnitCO = "";
    $UnitExpire = "";
    $Commissioner = "";
    $Contact = "";
  }
  $UnitCO = sprintf("%s, Expire Date: %s", $UnitCO, $UnitExpire);
  $UnitCommissioner = sprintf("Unit Commissioner: %s Last Contact: %s", $Commissioner, $Contact);
  // Get the Number of Trained vs. Untrained leaders
  $sqlDirectUnTrained  = sprintf(
    'SELECT * FROM trainedleaders WHERE Unit = "%s" AND Direct_Contact_Leader = "%s" AND Trained = "NO" ',
    $unit_name,
    'YES'
  );

  $sqlNonDirectUnTrained  = sprintf(
    'SELECT * FROM trainedleaders WHERE Unit = "%s" AND Direct_Contact_Leader = "%s" AND Trained = "NO" ',
    $unit_name,
    'NO'
  );
}
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
    google.charts.setOnLoadCallback(drawDirectChart);
    google.charts.setOnLoadCallback(drawUnDirectChart);

    function drawDirectChart() {

      var data = google.visualization.arrayToDataTable([
        ['Trained', 'Untrained'],
        <?php
        echo "['Trained'," . $Direct['Trained'] . "],";
        echo "['Untrained'," . $Direct['Untrained'] . "]";
        ?>
      ]);

      var options = {
        title: 'Training for Direct Contact',
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

      var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

      chart.draw(data, options);
    }

    function drawUnDirectChart() {
      var data = google.visualization.arrayToDataTable([
        ['Trained', 'Untrained'],
        <?php
        echo "['Trained'," . $NonDirect['Trained'] . "],";
        echo "['Untrained'," . $NonDirect['Untrained'] . "]";
        ?>
      ]);

      var options = {
        title: 'Training for NonDirect Contact',
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

      var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

      chart.draw(data, options);
    }
  </script>

</head>

<body style="padding:10px">
  <header id="header" class="header sticky-top">
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container px-lg-5">
        <a class="navbar-brand" href="#!">Centennial District Advancement</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
            <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
            <li class="nav-item"><a class="nav-link" href="./contact.php">Contact</a></li>
            <?php
            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
              echo '<li class="nav-item"><a class="nav-link" href="./logoff.php">Log off</a></li>';
            } else {
              echo '<li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>';
            }
            ?>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <div class="container-fluid">
    <sort_options>
      <div class="px-lg-5">
        <div class="row">
          <div class="col-4">
            <?php $Title = "Centennial District Advancement - " . $unit_name; ?>
            <p style="font-size: 25px;font-weight: bold;"><?php echo $Title ?></p>

            <?php
            if (strlen($UnitCO) > 0) ?>
            <p style="font-size: 18px;font-weight: bold;"><?php echo $UnitCO ?></p> <?php
                                                                                    if (strlen($UnitCommissioner) > 0) ?>
            <p style="font-size: 18px;font-weight: bold;"><?php echo $UnitCommissioner ?></p>
          </div>
          <div class="col-3">
            <table>
              <tr>
                <td>
                  <div id="piechart1" style="width: 350px; height: 200px;"></div>
                </td>
                <td>
                  <div id="piechart2" style="width: 350px; height: 200px;"></div>
                </td>
              </tr>
            </table>
          </div>
          <!-- <div class="col-3" id="piechart2"> -->
          <!-- </div> -->
        </div>
      </div>
    </sort_options>
  </div>




  <div class="container-fluid py-5 px-lg-5">

    <table class="table table-striped">
      <!-- <td style="width:140px">
      <td style="width:100px">
      <td style="width:100px">
      <td style="width:75px">
      <td style="width:230px">
      <td style="width:230px">
      <td style="width:50px">
      <td style="width:75px">
      <td style="width:50px"> -->
      <tr>
        <th>Unit</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Member ID</th>
        <th>Position</th>
        <th>Functional Role</th>
        <th>Direct Contact</th>
        <th>Trained</th>
        <th>YPT</th>
      </tr>

      <?php
      $sqlAdult = "SELECT * FROM trainedleaders WHERE Unit = '$unit_name' ORDER BY Direct_Contact_Leader DESC";
      $result = $cAdultLeaders->doQuery($sqlAdult);

      while ($row = $result->fetch_assoc()) {
        $row_ypt = $cAdultLeaders->GetMemberYPT($row['MemberID']);
        $Trained = $row["Trained"];
        $LastName = $row["MemberID"];
        if (!strcmp($Trained, "NO")) {
          $TrainedURL = "<a href='Untrained.php?btn=MemberID&SortBy=MemberID&MemberID=$LastName'";
          $Trained = sprintf("%s%s>%s</a>", $TrainedURL, $Trained, $Trained);
        }

        $YPTStatus = $cAdultLeaders->GetMemberYPTStatus($row['MemberID']);
        if (!strcmp($YPTStatus, "NO")) {
          $YPTURL = "<a href='YPT.php?btn=ByLastName&SortBy=Last_Name&last_name=$LastName'";
          $YPTStatus = sprintf("%s%s>%s</a>", $YPTURL, $YPTStatus, $YPTStatus);
        } else
          $ExpiredYPT = $YPTStatus;

        echo "<tr><td>" .
          $row["Unit"] . "</td><td>" .
          $row["First_Name"] . "</td><td>" .
          $row["Last_Name"] . "</td><td>" .
          $row["MemberID"] . "</td><td>" .
          $row["Position"] . "</td><td>" .
          $row["FunctionalRole"] . "</td><td>" .
          $row["Direct_Contact_Leader"] . "</td><td>" .
          $Trained . "</td><td>" .
          $ExpiredYPT . "</td></tr>";
      }
      ?>
    </table>
  </div>


  <?php


  echo "<br>";
  $lastTrainedUpdated = $UNIT->GetLastUpdated('trainedleaders');
  $lastYPTUpdated = $UNIT->GetLastUpdated('ypt');
  echo "<p style='text-align: center;'>Content last changed: Trained Leaders " . $lastTrainedUpdated . " YPT " . $lastYPTUpdated . "</p>";
  echo '<p style="page-break-before: always;">&nbsp;</p>';

  ?>

  <?php
  if (!empty($unit_name)) {
    //$unit_name = $var_value;
    if (stristr($unit_name, 'Pack')) {
      include 'pack_advancement.php';
    } else if (stristr($unit_name, 'Troop')) {
      include 'troop_advancement.php';
    } else if (stristr($unit_name, 'Crew')) {
      include 'crew_advancement.php';
    } else if (stristr($unit_name, 'Post')) {
      // No advancement data for Post's
      // include 'post_advancement.php';
    }
  }
  ?>

  <!-- Footer-->
  <?php include 'Footer.php' ?>

</body>

</html>