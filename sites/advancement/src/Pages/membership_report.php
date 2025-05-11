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

load_template('/src/Classes/CPack.php');
load_template('/src/Classes/CTroop.php');
load_template('/src/Classes/CCrew.php');
//include_once('CPost.php');
load_template('/src/Classes/CUnit.php');


$CAdvancement = CAdvancement::getInstance();
$CUnit = UNIT::getInstance();
$CPack = CPack::getInstance();
$CTroop = CTroop::getInstance();
$CCrew = CCrew::getInstance();
$CPost = CPost::getInstance();

if (isset($_POST['SubmitYear'])) {
  $SelYear = $_POST['Year'];
  $_SESSION['year'] = $SelYear;
  $CAdvancement->SetYear($SelYear);
}


$SelectedYear = $CPack->GetYear();
$CurrentYear = date("Y");

if ($SelectedYear != $CurrentYear) {
  $PackMembers = $CPack->GetPreviousMemberTotals();
  $TroopMembers = $CTroop->GetPreviousMemberTotals();
  $CrewMembers = $CCrew->GetPreviousMemberTotals();
  $PostMembers = $CPost->GetPreviousMemberTotals();
} else {
  $PackMembers = $CPack->GetMemberTotals();
  $TroopMembers = $CTroop->GetMemberTotals();
  $CrewMembers = $CCrew->GetMemberTotals();
  $PostMembers = $CPost->GetMemberTotals();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawChart);



    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Unit', 'Youth'],
        <?php
        if ($PackMembers['Total_Youth'] != NULL)
          echo "['Pack',"  . $PackMembers['Total_Youth']  . "],";
        if ($TroopMembers['Total_Youth'] != NULL)
          echo "['Troop'," . $TroopMembers['Total_Youth'] . "],";
        if ($CrewMembers['Total_Youth'] != NULL)
          echo "['Crew',"  . $CrewMembers['Total_Youth']  . "],";
        if ($PostMembers['Total_Youth'] != NULL)
          echo "['Post',"  . $PostMembers['Total_Youth']  . "],";
        ?>
      ]);

      var options = {
        chart: {
          title: 'Centennial District Youth Totals',
          subtitle: '',
        },
        bars: 'vertical' // Required for Material Bar Charts.
      };

      var chart = new google.charts.Bar(document.getElementById('barchart_material'));
      chart.draw(data, google.charts.Bar.convertOptions(options));
    }
  </script>

</head>

<body style="padding:10px">
  <header id="header" class="header sticky-top">
    <?php $navbarTitle = 'Centennial District Membership Report'; ?>
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
              <div class="col-4 chart_div">
                <div id="barchart_material"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-10 py-5">
              <?php $CUnit->DisplayMembershipTable(); ?>
            </div>
          </div>
      </sort_options>
    </div>
  </div>

  <footer class="py-5">
    <?php load_template('/src/Templates/Footer.php'); ?>
  </footer>

</body>

</html>