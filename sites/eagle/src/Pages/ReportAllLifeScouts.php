<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          \\!
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

// Load CEagle class (aligned with index.php's load_class)
load_class(__DIR__ . '/../Classes/CEagle.php');
$cEagle = CEagle::getInstance();

// Session check (handled in index.php, but included for standalone testing)
if (!session_id()) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

// Authentication check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}

// Ensure CSRF token is set
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Unit selection query
$qryUnits = "SELECT DISTINCT UnitType, UnitNumber FROM scouts 
             WHERE (`ProjectApproved` IS NULL OR `ProjectApproved`='0') 
             AND (`Eagled` IS NULL OR `Eagled`='0') 
             AND (`AgedOut` IS NULL OR `AgedOut`='0') 
             AND (`is_deleted` IS NULL OR `is_deleted`='0')
             ORDER BY `UnitType` ASC, `UnitNumber` ASC";

if (!$Units = $cEagle->doQuery($qryUnits)) {
  $msg = "Error: doQuery()";
  $cEagle->function_alert($msg);
}

// Check for form submission
$SelectedUnit = false;
$SelectedNum = false;
if (isset($_POST['SubmitUnit']) && isset($_POST['Unit']) && $_POST['Unit'] !== '-' && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
  $SelectedUnit = strtok($_POST['Unit'], '-');
  $SelectedNum = strtok('-');
  $_SESSION['feedback'] = ['type' => 'success', 'message' => "Unit {$SelectedUnit} {$SelectedNum} selected."];
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Refresh CSRF token
} elseif (isset($_POST['SubmitUnit']) && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
}

// Store and clear feedback
$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
unset($_SESSION['feedback']);

$csv_hdr = "Unit,Gender,Name,Age Out Date,Scout Email,ULName,ELEmail,CCName,CCEmail,Project Approval";
$csv_output = "";
?>

<div class="container-fluid mt-5 pt-3">
  <!-- Display Feedback -->
  <?php if (!empty($feedback)): ?>
    <div class="alert alert-<?php echo htmlspecialchars($feedback['type']); ?> alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($feedback['message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <div class="form-row px-5 d-print-none">
      <div class="col-2">
        <label for="Unit">Choose a Unit: </label>
        <select class="form-control" id="Unit" name="Unit">
          <option value="-">All Units</option>
          <?php
          while ($rowUnits = $Units->fetch_assoc()) {
            echo "<option value='{$rowUnits['UnitType']}-{$rowUnits['UnitNumber']}'>{$rowUnits['UnitType']} {$rowUnits['UnitNumber']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-2 py-4">
        <input class="btn btn-primary btn-sm" type="submit" name="SubmitUnit" value="Select Unit" />
      </div>
    </div>
  </form>

  <center>
    <h4>Report of All Life Scouts in Database</h4>
    <div class="table-responsive">
      <table id="lifeScoutsTable" class="table table-striped">
        <thead>
          <tr>
            <th>Unit Type</th>
            <th>Unit#</th>
            <th>Gender</th>
            <th>Name</th>
            <th>Age Out Date</th>
            <th>Project Approval</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $queryScout = "SELECT * FROM `scouts` 
                               WHERE (`is_deleted` IS NULL OR `is_deleted`='0') 
                               AND (`Eagled` IS NULL OR `Eagled`='0') 
                               AND (`AgedOut` IS NULL OR `AgedOut`='0')";
          if ($SelectedUnit && $SelectedNum) {
            $queryScout .= " AND (`UnitType`='$SelectedUnit') AND (`UnitNumber`='$SelectedNum')";
          }
          $queryScout .= " ORDER BY STR_TO_DATE(`AgeOutDate`, '%m/%d/%Y') ASC, `Gender`";

          if (!$Scout = $cEagle->doQuery($queryScout)) {
            $msg = "Error: doQuery()";
            $cEagle->function_alert($msg);
          }

          while ($rowScout = $Scout->fetch_assoc()) {
            $FirstName = $cEagle->GetScoutPreferredName($rowScout);
            echo "<tr><td>" . htmlspecialchars($rowScout["UnitType"]) . "</td><td>" .
              htmlspecialchars($rowScout["UnitNumber"]) . "</td><td>" .
              htmlspecialchars($rowScout["Gender"]) . "</td><td>" .
              "<a href=index.php?page=edit-select-scout&Scoutid=" . htmlspecialchars($rowScout['Scoutid']) . ">" .
              htmlspecialchars($FirstName . " " . $rowScout["LastName"]) . "</a></td><td>" .
              htmlspecialchars($rowScout["AgeOutDate"]) . "</td><td>" .
              htmlspecialchars($rowScout["ProjectDate"] ?? '') . "</td></tr>";

            $csv_output .= htmlspecialchars($rowScout["UnitType"]) . " " . htmlspecialchars($rowScout["UnitNumber"]) . "," .
              htmlspecialchars($rowScout["Gender"]) . "," .
              htmlspecialchars($FirstName . " " . $rowScout["LastName"]) . "," .
              htmlspecialchars($rowScout["AgeOutDate"]) . "," .
              htmlspecialchars($rowScout["Email"] ?? '') . "," .
              htmlspecialchars($rowScout["ULFirst"] . " " . $rowScout["ULLast"] ?? '') . "," .
              htmlspecialchars($rowScout["ULEmail"] ?? '') . "," .
              htmlspecialchars($rowScout["CCFirst"] . " " . $rowScout["CCLast"] ?? '') . "," .
              htmlspecialchars($rowScout["CCEmail"] ?? '') . "," .
              htmlspecialchars($rowScout["ProjectDate"] ?? '') . "\n";
          }
          ?>
        </tbody>
      </table>
    </div>
    <b>For a total of <?php echo mysqli_num_rows($Scout); ?> scouts</b>

    <form class="d-print-none" name="export" action="export.php" method="post" style="padding: 20px;">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
      <input class="btn btn-primary btn-sm" style="width:220px" type="submit" value="Export table to CSV">
      <input type="hidden" value="<?php echo htmlspecialchars($csv_hdr); ?>" name="csv_hdr">
      <input type="hidden" value="<?php echo htmlspecialchars($csv_output); ?>" name="csv_output">
    </form>
  </center>


  <script>
    $(document).ready(function() {
      $('#lifeScoutsTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "columnDefs": [{
            "type": "date",
            "targets": 4, // AgeOutDate column
            "render": function(data, type, row) {
              if (type === 'sort') {
                return moment(data, 'MM/DD/YYYY').format('YYYYMMDD');
              }
              return data;
            }
          },
          {
            "orderable": true,
            "targets": "_all"
          }
        ]
      });
    });
  </script>
</div>