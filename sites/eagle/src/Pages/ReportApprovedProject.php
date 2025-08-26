<?php
/*
 * 
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */

// Load classes
load_class(BASE_PATH . '/src/Classes/CEagle.php');
$cEagle = CEagle::getInstance();

// Session check
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <style>
    .wrapper {
      width: 360px;
      padding: 20px;
    }
  </style>
</head>

<body>
  <?php
  //Allow selection by Unit
  $qryUnits = "SELECT DISTINCTROW UnitType, UnitNumber FROM scouts WHERE (`ProjectApproved`='1') AND  
        (`Eagled` IS NULL OR `Eagled`='0') AND (`AgedOut` IS NULL OR `AgedOut`='0') AND (`is_deleted` IS NULL OR `is_deleted`='0')
        ORDER BY `UnitType` ASC, `UnitNumber` ASC";

  $cEagle->SelectUnit($qryUnits, $_SESSION['csrf_token']);
  ?>
  <?php
  //#####################################################
  //
  // Check to see if user as Submitted the form.
  //
  //#####################################################
  $SelectedUnit = false;
  $SelectedNum = false;
  if (isset($_POST['SubmitUnit']) && isset($_POST['Unit']) && $_POST['Unit'] !== '-') {
    $SelectedUnit = strtok($_POST['Unit'], '-'); // Get name of Counselor selected
    $SelectedNum = strtok('-');
  }

  $csv_hdr = "Unit Type, Unit#,  Gender, Name, Approval Date, Age Out Date, Coach/Mentor";
  $csv_output = "";
  ?>

  <h4 class="text-center">Scouts who have received Project Approval</h4>
  <div class="table-responsive">
    <table id="projectapprovedTable" class="table table-striped">
      <thead>
        <tr>
          <th>Unit Type</th>
          <th>Unit#</th>
          <th>Gender</th>
          <th>Name</th>
          <th>Approval Date</th>
          <th>Age Out Date</th>
          <th>Coach/Mentor</th>
        </tr>
      </thead>
      <?php

      if ($SelectedUnit & $SelectedNum) {
        $queryScout = "SELECT * FROM `scouts` 
        WHERE (`ProjectApproved`='1') AND 
        (`Eagled` IS NULL OR `Eagled`='0') AND 
        (`AgedOut` IS NULL OR `AgedOut`='0') AND
        (`is_deleted` IS NULL OR `is_deleted`='0') AND
        (`UnitType`='$SelectedUnit') AND (`UnitNumber`='$SelectedNum')
        ORDER BY `UnitType` ASC, `UnitNumber` ASC, `Gender` ASC, `LastName` ASC";
      } else {
        $queryScout = "SELECT * FROM `scouts` 
        WHERE (`ProjectApproved`='1') AND 
        (`Eagled` IS NULL OR `Eagled`='0') AND 
        (`AgedOut` IS NULL OR `AgedOut`='0') AND
        (`is_deleted` IS NULL OR `is_deleted`='0') 
        ORDER BY `UnitType` ASC, `UnitNumber` ASC, `Gender` ASC, `LastName` ASC";
      }

      if (!$Scout = $cEagle->doQuery($queryScout)) {
        $msg = "Error: doQuery()";
        $cEagle->function_alert($msg);
      }

      echo "<tbody>";
      // Display all of the Propsal approved scouts 
      while ($rowScout = $Scout->fetch_assoc()) {
        $FirstName = $cEagle->GetScoutPreferredName($rowScout);

        // If the have a coach/mentor dispay that also
        if ($rowScout['Coach'] > 0) {
          $queryCoach = "SELECT * FROM `coaches` WHERE `Coachesid`='$rowScout[Coach]'";
          if (!$Coach = $cEagle->doQuery($queryCoach)) {
            $msg = "Error: doQuery()";
            $cEagle->function_alert($msg);
          }
          $rowCoach = $Coach->fetch_assoc();
          $Coachid = $rowCoach['Coachesid'];
          $CoachFirst = $cEagle->GetPreferredName($rowCoach);
          $CoachLast = $rowCoach['Last_Name'];
        } else {
          $Coachid = -1;
          $CoachFirst = "";
          $CoachLast = "";
        }


        echo "<tr><td>" .
          $rowScout["UnitType"] . "</td><td>" .
          $rowScout["UnitNumber"] . "</td><td>" .
          $rowScout["Gender"] . "</td><td>" .
          "<a href=index.php?page=edit-select-scout&Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td>" .
          $rowScout["ProjectDate"] . "</td><td>" .
          $rowScout["AgeOutDate"] . "</td><td>" .
          "<a href=index.php?page=edit-select-coach&Coachesid=" . $Coachid . ">" . $CoachFirst . " " . $CoachLast . "</td></tr>";

        $csv_output .= $rowScout["UnitType"] . ",";
        $csv_output .= $rowScout["UnitNumber"] . ",";
        $csv_output .= $rowScout["Gender"] . ", ";
        $csv_output .= $FirstName . " " . $rowScout["LastName"] . ", ";
        $csv_output .= $rowScout["ProjectDate"] . ", ";
        $csv_output .= $rowScout["AgeOutDate"] . ", ";
        $csv_output .= $CoachFirst . " " . $CoachLast . "\n";
      } ?>
      </tbody>
    </table>

    <form class="d-print-none d-flex justify-content-center"  name="export" action="../export.php" method="post" style="padding: 20px;">
      <input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
      <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
      <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
    </form>

  </div>
  <script>
    $(document).ready(function() {
      // Custom sorting for MM/DD/YYYY date format
      $.fn.dataTable.ext.order['date-us'] = function(data) {
        if (!data || data.trim() === '') {
          return 0; // Handle empty or null dates
        }
        // Ensure date matches MM/DD/YYYY format
        var datePattern = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
        var match = data.match(datePattern);
        if (!match) {
          console.warn('Invalid date format for:', data);
          return 0; // Treat invalid dates as lowest priority
        }
        var month = match[1].padStart(2, '0');
        var day = match[2].padStart(2, '0');
        var year = match[3];
        return parseInt(year + month + day);
      };

      $('#projectapprovedTable').DataTable({
        "paging": false, // Display all rows
        "searching": true, // Enable search
        "ordering": true, // Enable sorting
        "info": true, // Show table info
        "autoWidth": false, // Disable auto width for Bootstrap
        "columnDefs": [{
            "type": "date-us",
            "targets": 4 // AgeOutDate column (0-based index)
          },
          {
            "orderable": true,
            "targets": "_all" // Ensure all columns are sortable
          }
        ]
      });
    });
  </script>
  <!-- Moment.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

  <!-- DataTables DateTime Sorting Plugin -->
  <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>

</body>

</html>