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
} elseif (isset($_POST['SubmitUnit']) && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
}

// Store and clear feedback
$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
unset($_SESSION['feedback']);
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
    <!-- Custom loading overlay -->
    <div id="loadingOverlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; display: flex; justify-content: center; align-items: center;">
      <div class="spinner"></div>
      <span style="color: #fff; font-size: 16px;">Loading...</span>
    </div>
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

      if ($SelectedUnit && $SelectedNum) {
        $queryScout = "SELECT * FROM `scouts` 
                                        WHERE (`is_deleted` IS NULL OR `is_deleted`='0') AND
                                        (`UnitType`=?) AND (`UnitNumber`=?) 
                                        ORDER BY `Gender`, LastName ASC";
        $stmt = mysqli_prepare($cEagle->getDbConn(), $queryScout);
        mysqli_stmt_bind_param($stmt, "ss", $SelectedUnit, $SelectedNum);
        mysqli_stmt_execute($stmt);
        $Scout = mysqli_stmt_get_result($stmt);
      } else {
        $queryScout = "SELECT * FROM `scouts` 
                                        WHERE (`is_deleted` IS NULL OR `is_deleted`='0')
                                        ORDER BY `Gender`, LastName ASC";
        $Scout = $cEagle->doQuery($queryScout);
      }

      if (!$Scout) {
        $msg = "Error: doQuery()";
        $cEagle->function_alert($msg);
      }

      echo "<tbody>";
      while ($rowScout = $Scout->fetch_assoc()) {
        $FirstName = $cEagle->GetScoutPreferredName($rowScout);

        echo "<tr><td>" . htmlspecialchars($rowScout["UnitType"] ?? '') . "</td><td>" .
          htmlspecialchars($rowScout["UnitNumber"] ?? '') . "</td><td>" .
          htmlspecialchars($rowScout["Gender"] ?? '') . "</td><td>" .
          "<a href='index.php?page=edit-select-scout&Scoutid=" . htmlspecialchars($rowScout['Scoutid'] ?? '') . "'>" .
          htmlspecialchars(($FirstName ?? '') . " " . ($rowScout["LastName"] ?? '')) . "</a></td><td>" .
          htmlspecialchars($rowScout["MemberId"] ?? '') . "</td><td>" .
          htmlspecialchars($rowScout["AgeOutDate"] ?? '') . "</td><td>" .
          htmlspecialchars($rowScout["BOR"] ?? '') . "</td><td>" .
          htmlspecialchars($rowScout["ProjectDate"] ?? '') . "</td></tr>";

      if ($SelectedUnit && $SelectedNum) {
        mysqli_stmt_close($stmt);
      } else {
        mysqli_free_result($Scout);
      }
      ?>
      </tbody>
    </table>
  </div>

  
  <script>
    $(document).ready(function() {
      console.log('Starting DataTable initialization for allScoutsTable');

      // Show custom loading overlay
      $('#loadingOverlay').show();
      console.log('Loading overlay shown');

      // Destroy existing DataTable instance if it exists
      if ($.fn.DataTable.isDataTable('#allScoutsTable')) {
        $('#allScoutsTable').DataTable().destroy();
        console.log('Previous DataTable instance destroyed');
      }

      // Custom sorting for MM/DD/YYYY date format
      $.fn.dataTable.ext.order['date-us'] = function(data) {
        if (!data || data.trim() === '') {
          return 0; // Handle empty or null dates
        }
        // Remove any HTML tags if present
        var cleanData = data.replace(/<[^>]+>/g, '');
        var datePattern = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
        var match = cleanData.match(datePattern);
        if (!match) {
          console.warn('Invalid date format for:', cleanData);
          return 0; // Treat invalid dates as lowest priority
        }
        var month = match[1].padStart(2, '0');
        var day = match[2].padStart(2, '0');
        var year = match[3];
        return parseInt(year + month + day);
      };

      // Initialize DataTable with export buttons and custom sorting
      $('#allScoutsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'copy',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            title: 'Centennial District All Scouts Report'
          },
          {
            extend: 'csv',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            filename: 'Centennial District All Scouts Report'
          },
          {
            extend: 'excel',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            filename: 'Centennial District All Scouts Report'
          },
          {
            extend: 'pdf',
            className: 'btn btn-primary btn-sm d-print-none mt-2',
            filename: 'Centennial District All Scouts Report'
          }
        ],
        paging: false, // Display all rows
        searching: true, // Enable search
        ordering: true, // Enable sorting
        info: true, // Show table info
        autoWidth: false, // Disable auto width for Bootstrap
        columnDefs: [{
            type: 'date-us',
            targets: [5, 6] // Age Out Date (5) and Eagled Date (6) columns (0-based index)
          },
          {
            orderable: true,
            targets: '_all' // Ensure all columns are sortable
          }
        ],
        initComplete: function() {
          console.log('DataTable initialization complete');
          // Hide loading overlay after a minimum duration (2 seconds)
          setTimeout(function() {
            $('#loadingOverlay').hide();
            console.log('Loading overlay hidden');
          }, 2000);
        }
      });
    });
  </script>
  </body>

  </html>