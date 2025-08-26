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

//Allow selection by Unit
$qryUnits = "SELECT DISTINCTROW UnitType, UnitNumber FROM scouts WHERE (`ProjectApproved`IS NULL OR `ProjectApproved`='0') AND 
        (`Eagled` IS NULL OR `Eagled`='0') AND (`AgedOut` IS NULL OR `AgedOut`='0') AND (`is_deleted` IS NULL OR `is_deleted`='0')
        ORDER BY `UnitType` ASC, `UnitNumber` ASC";
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
  $_SESSION['feedback'] = ['type' => 'success', 'message' => "Unit {$SelectedUnit} {$SelectedNum} selected."];
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Refresh CSRF token
}elseif (isset($_POST['SubmitUnit']) && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
}

// Store and clear feedback
$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
unset($_SESSION['feedback']);

$csv_hdr = "Unit, Gender, Name, Age Out Date, Scout Email, ULName, ELEMail, CCName, CCEmail, Project Approval";
$csv_output = "";

?>
<div class="container-fluid">
  <!-- Display Feedback -->
  <?php if (!empty($feedback)): ?>
    <div class="alert alert-<?php echo htmlspecialchars($feedback['type']); ?> alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($feedback['message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif;
  $cEagle->SelectUnit($qryUnits, $_SESSION['csrf_token']);

  ?>

  <h4 class="text-center">Report of all scouts in database</h4>
  <div class="table-responsive">
    <table id="allScoutsTable" class="table table-striped">
      <thead>
        <tr>
          <th>Unit Type</th>
          <th>Unit#</th>
          <th>Gender</th>
          <th>Name</th>
          <th>BSA ID</th>
          <th>Age Out Date</th>
          <th>Eagled Date</th>
          <th>Project Approval</th>
        </tr>
      </thead>
      <?php

      if ($SelectedUnit & $SelectedNum) {
        $queryScout = "SELECT * FROM `scouts` 
                                        WHERE (`is_deleted` IS NULL OR `is_deleted`='0') AND
                                        (`UnitType`='$SelectedUnit') AND (`UnitNumber`='$SelectedNum')
                                        ORDER BY  STR_TO_DATE(`AgeOutDate`, '%m/%d/%Y') ASC, `Gender`";
      } else {
        $queryScout = "SELECT * FROM `scouts` 
                                        WHERE (`is_deleted` IS NULL OR `is_deleted`='0')
                                        ORDER BY  STR_TO_DATE(`AgeOutDate`, '%m/%d/%Y') ASC, `Gender`";
      }

      if (!$Scout = $cEagle->doQuery($queryScout)) {
        $msg = "Error: doQuery()";
        $cEagle->function_alert($msg);
      }

      echo "<tbody>";
      while ($rowScout = $Scout->fetch_assoc()) {
        $FirstName = $cEagle->GetScoutPreferredName($rowScout);

        echo "<tr><td>" .
          $rowScout["UnitType"] . "</td><td>" .
          $rowScout["UnitNumber"] . "</td><td>" .
          $rowScout["Gender"] . "</td><td>" .
          "<a href=index.php?page=edit-select-scout&Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td>" .
          $rowScout["MemberId"] . "</td><td>" .
          $rowScout["AgeOutDate"] . "</td><td>" .
          $rowScout["BOR"] . "</td><td>" .
          $rowScout["ProjectDate"] . "</td></tr>";

        $csv_output .= $rowScout["UnitType"] . " " . $rowScout["UnitNumber"] . ", ";
        $csv_output .= $rowScout["Gender"] . ", ";
        $csv_output .=  $FirstName . " " . $rowScout["LastName"] . ", ";
        $csv_output .= $rowScout["MemberId"] . ", ";
        $csv_output .= $rowScout["AgeOutDate"] . ", ";
        $csv_output .= $rowScout["BOR"] . ", ";
        $csv_output .= $rowScout["Email"] . ", ";
        $csv_output .= $rowScout["ULFirst"] . " " . $rowScout["ULLast"] . ", ";
        $csv_output .= $rowScout["ULEmail"] . ", ";
        $csv_output .= $rowScout["CCFirst"] . " " . $rowScout["CCLast"] . ", ";
        $csv_output .= $rowScout["CCEmail"] . ", ";
        $csv_output .= $rowScout["ProjectDate"] . "\n";
      }
      echo "</tbody>";
      echo "</table>";
      echo "</div>";
      $csv_output .= "\n";
      echo "<p class='text-center'><b>For a total of " . mysqli_num_rows($Scout) . "</b></p>";

      ?>

      <form class="d-print-none d-flex justify-content-center" name="export" action="../export.php" method="post" style="padding: 20px;">
        <input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
        <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
        <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
      </form>

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

          $('#allScoutsTable').DataTable({
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
  </div>