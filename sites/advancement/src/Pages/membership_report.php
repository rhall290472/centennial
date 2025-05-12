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
load_template('/src/Classes/CCrew.php');
//load_template('/src/Classes/CPost.php');
load_template('/src/Classes/CUnit.php');

$CPack = CPack::getInstance();
$CTroop = CTroop::getInstance();
$CCrew = CCrew::getInstance();
$CPost = CPost::getInstance();
$CUnit = UNIT::getInstance();

try {
  $SelectedYear = isset($_SESSION['year']) ? $_SESSION['year'] : date("Y");
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
} catch (Exception $e) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading membership data: ' . $e->getMessage()];
  error_log("membership_report.php - Error: " . $e->getMessage(), 0);
  $PackMembers = ['Total_Youth' => 0];
  $TroopMembers = ['Total_Youth' => 0];
  $CrewMembers = ['Total_Youth' => 0];
  $PostMembers = ['Total_Youth' => 0];
}
?>

<sort_options>
  <div class="px-lg-5">
    <h1>Centennial District Membership Report</h1>
    <div class="row">
      <div class="col-md-2">
        <form action="index.php?page=membership-report" method="POST">
          <p class="mb-0">Select Year</p>
          <?php
          try {
            $CTroop->SelectYear();
          } catch (Exception $e) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading year selector: ' . $e->getMessage()];
            echo '<select class="form-control" name="Year"><option value="' . date("Y") . '">' . date("Y") . '</option></select>';
          }
          ?>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
          <input class="btn btn-primary btn-sm mt-2" type="submit" name="SubmitYear" value="Set Year">
        </form>
      </div>
      <div class="col-md-6">
        <div id="barchart_material" style="width: 100%; height: 400px;"></div>
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-12">
        <?php
        try {
          $CUnit->DisplayMembershipTable();
        } catch (Exception $e) {
          $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying membership table: ' . $e->getMessage()];
          error_log("membership_report.php - Error: " . $e->getMessage(), 0);
          echo "<p>No membership data available for $SelectedYear.</p>";
        }
        ?>
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
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Unit', 'Youth'],
      <?php
      if ($PackMembers['Total_Youth'] !== null) echo "['Pack', " . $PackMembers['Total_Youth'] . "],";
      if ($TroopMembers['Total_Youth'] !== null) echo "['Troop', " . $TroopMembers['Total_Youth'] . "],";
      if ($CrewMembers['Total_Youth'] !== null) echo "['Crew', " . $CrewMembers['Total_Youth'] . "],";
      if ($PostMembers['Total_Youth'] !== null) echo "['Post', " . $PostMembers['Total_Youth'] . "],";
      ?>
    ]);

    var options = {
      chart: {
        title: 'Centennial District Youth Totals',
        subtitle: 'Youth by Unit Type for <?php echo htmlspecialchars($SelectedYear); ?>'
      },
      bars: 'vertical',
      width: '100%',
      height: 400
    };

    var chart = new google.charts.Bar(document.getElementById('barchart_material'));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  }
</script>