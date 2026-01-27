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
  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
  <!-- jQuery (required for DataTables) -->
  <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
  <!-- DataTables CSS -->
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" /> -->
  <!-- Custom CSS for button styling, spinner, and layout -->
  <!-- <style>
    .dt-button.btn-primary:hover {
      background-color: #0056b3 !important;
      border-color: #004085 !important;
    }

    /* Spinner styles */
    .spinner {
      border: 4px solid rgba(255, 255, 255, 0.3);
      border-top: 4px solid #007bff;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin-right: 10px;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    .table-responsive {
      position: relative;
    }

    .wrapper {
      width: 360px;
      padding: 20px;
    }
  </style> -->
</head>

<body>
  <div class="container-fluid">
    <?php
    // Allow selection by Unit
    $qryUnits = "SELECT DISTINCTROW UnitType, UnitNumber FROM scouts WHERE `AgedOut`='1' AND (`is_deleted` IS NULL OR `is_deleted`='0') ORDER BY `UnitType` ASC, `UnitNumber` ASC";
    $cEagle->SelectUnit($qryUnits, $_SESSION['csrf_token']);
    ?>
    <?php
    // Check if user has submitted the form
    $SelectedUnit = false;
    $SelectedNum = false;
    if (isset($_POST['SubmitUnit']) && isset($_POST['Unit']) && $_POST['Unit'] !== '-') {
      $SelectedUnit = strtok($_POST['Unit'], '-'); // Get UnitType
      $SelectedNum = strtok('-'); // Get UnitNumber
    }
    ?>

    <h4 class="text-center">Scouts who have Aged out of the Scouting Program</h4>
    <div class="table-responsive">
      <!-- Custom loading overlay -->
      <div id="loadingOverlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; display: flex; justify-content: center; align-items: center;">
        <div class="spinner"></div>
        <span style="color: #fff; font-size: 16px;">Loading...</span>
      </div>
      <table id="agedOutTable" class="table table-striped">
        <thead>
          <tr>
            <th>Unit Type</th>
            <th>Unit#</th>
            <th>Name</th>
            <th>BSA ID</th>
            <th>Aged Out Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($SelectedUnit && $SelectedNum) {
            $queryScout = "SELECT * FROM `scouts` WHERE `AgedOut`='1' AND (`is_deleted` IS NULL OR `is_deleted`='0') AND
                         (`UnitType`=?) AND (`UnitNumber`=?) ORDER BY `UnitType` ASC, `UnitNumber` ASC, `Gender` ASC, `LastName` ASC";
            $stmt = mysqli_prepare($cEagle->getDbConn(), $queryScout);
            mysqli_stmt_bind_param($stmt, "ss", $SelectedUnit, $SelectedNum);
            mysqli_stmt_execute($stmt);
            $Scout = mysqli_stmt_get_result($stmt);
          } else {
            $queryScout = "SELECT * FROM `scouts` WHERE `AgedOut`='1' AND (`is_deleted` IS NULL OR `is_deleted`='0') ORDER BY `UnitType` ASC, `UnitNumber` ASC, `Gender` ASC, `LastName` ASC";
            $Scout = $cEagle->doQuery($queryScout);
          }

          if (!$Scout) {
            $msg = "Error: doQuery()";
            $cEagle->function_alert($msg);
          }

          while ($rowScout = $Scout->fetch_assoc()) {
            $FirstName = $cEagle->GetScoutPreferredName($rowScout);
            echo "<tr><td>" . htmlspecialchars($rowScout["UnitType"]) . "</td><td>" .
              htmlspecialchars($rowScout["UnitNumber"]) . "</td><td>" .
              "<a href='index.php?page=edit-select-scout&Scoutid=" . htmlspecialchars($rowScout['Scoutid']) . "'>" .
              htmlspecialchars($FirstName . " " . $rowScout["LastName"]) . "</a></td><td>" .
              htmlspecialchars($rowScout["MemberId"]) . "</td><td>" .
              htmlspecialchars($rowScout["AgeOutDate"]) . "</td></tr>";
          }

          if ($SelectedUnit && $SelectedNum) {
            mysqli_stmt_close($stmt);
          } else {
            mysqli_free_result($Scout);
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- DataTables JS and Buttons -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <!-- Moment.js and DataTables DateTime Sorting Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>

    <script>
      $(document).ready(function() {
        console.log('Starting DataTable initialization for agedOutTable');

        // Verify Bootstrap CSS is loaded
        if (typeof $.fn.tooltip === 'undefined') {
          console.warn('Bootstrap JS not loaded; button styling may be affected');
        } else {
          console.log('Bootstrap JS detected');
        }

        // Show custom loading overlay
        $('#loadingOverlay').show();
        console.log('Loading overlay shown');

        // Destroy existing DataTable instance if it exists
        if ($.fn.DataTable.isDataTable('#agedOutTable')) {
          $('#agedOutTable').DataTable().destroy();
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
        $('#agedOutTable').DataTable({
          dom: 'Bfrtip',
          buttons: [{
              extend: 'copy',
              className: 'btn btn-primary btn-sm d-print-none mt-2',
              title: 'Centennial District Aged Out Scouts Report'
            },
            {
              extend: 'csv',
              className: 'btn btn-primary btn-sm d-print-none mt-2',
              filename: 'Centennial District Aged Out Scouts Report'
            },
            {
              extend: 'excel',
              className: 'btn btn-primary btn-sm d-print-none mt-2',
              filename: 'Centennial District Aged Out Scouts Report'
            },
            {
              extend: 'pdf',
              className: 'btn btn-primary btn-sm d-print-none mt-2',
              filename: 'Centennial District Aged Out Scouts Report'
            }
          ],
          paging: false, // Display all rows
          searching: true, // Enable search
          ordering: true, // Enable sorting
          info: true, // Show table info
          autoWidth: false, // Disable auto width for Bootstrap
          columnDefs: [{
              type: 'date-us',
              targets: 4 // Age Out Date column (0-based index)
            },
            {
              orderable: true,
              targets: '_all' // Ensure all columns are sortable
            }
          ],
          initComplete: function() {
            console.log('DataTable initialization complete');
            // Verify button styling
            $('.dt-button.btn-primary').each(function() {
              console.log('Button initialized with classes:', $(this).attr('class'));
            });
            // Hide loading overlay after a minimum duration (2 seconds)
            setTimeout(function() {
              $('#loadingOverlay').hide();
              console.log('Loading overlay hidden');
            }, 2000);
          }
        });
      });
    </script>
  </div>
</body>

</html>