<?php
/*
 * ReportEagles.php: Page for Eagles in the Centennial District website.
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

// Initialize variables
$UnitType = '';
$UnitNum = '';
$csv_hdr = "Unit Type,Unit Number,Gender,Name,Year,Beneficiary,Project Name,Project Hours\n";
$csv_output = '';
$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
unset($_SESSION['feedback']);

// Handle unit selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['SubmitUnit'], $_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
  $Unit = filter_input(INPUT_POST, 'Unit' ?? '');
  if ($Unit) {
    $parts = array_map('trim', explode('-', $Unit, 2));

    $UnitType = $parts[0] ?? '';
    $UnitNum  = $parts[1] ?? '';   // or null, '0', 0, etc. depending on what you need
  }
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
</head>

<body>
  <div class="container-fluid mt-5 pt-3 content-center">
    <!-- Display Feedback -->
    <?php if (!empty($feedback)): ?>
      <div class="alert alert-<?php echo htmlspecialchars($feedback['type']); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($feedback['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif;

    $sqlUnits = "SELECT DISTINCT `UnitType`, `UnitNumber` FROM `scouts` ORDER BY `UnitType`, `UnitNumber`";
    $cEagle->SelectUnit($sqlUnits, $_SESSION['csrf_token']); ?>

    <h4 class="text-center">Scouts who have Eagled (since 2017)</h4>
    <div class="table-responsive">
      <table id="eagleTable" class="table table-striped">
        <thead>
          <tr>
            <th>Unit Type</th>
            <th>Unit#</th>
            <th>Gender</th>
            <th>Name</th>
            <th>Year</th>
            <th>Beneficiary</th>
            <th>Project Name</th>
            <th>Project Hours</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $queryScout = "SELECT * FROM `scouts` WHERE `Eagled` = 1";
          $params = [];
          $types = '';
          if (!empty($UnitType) && !empty($UnitNum)) {
            $queryScout .= " AND `UnitType` = ? AND `UnitNumber` = ?";
            $params[] = $UnitType;
            $params[] = $UnitNum;
            $types .= 'si';
          }
          $queryScout .= " ORDER BY `LastName`";

          $stmt = mysqli_prepare($cEagle->getDbConn(), $queryScout);
          if ($params) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
          }
          mysqli_stmt_execute($stmt);
          $Scout = mysqli_stmt_get_result($stmt);

          if (!$Scout) {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error retrieving scout data.'];
            header("Location: index.php?page=Ereport");
            exit;
          }

          while ($rowScout = mysqli_fetch_assoc($Scout)) {
            $FirstName = htmlspecialchars($cEagle->GetScoutPreferredName($rowScout));
            $LastName = htmlspecialchars($rowScout['LastName'] ?? '');
            $Scoutid = htmlspecialchars($rowScout['Scoutid'] ?? ''); // Fixed case
            $UnitTypeVal = htmlspecialchars($rowScout['UnitType'] ?? '');
            $UnitNumberVal = htmlspecialchars($rowScout['UnitNumber'] ?? '');
            $GenderVal = htmlspecialchars($rowScout['Gender'] ?? '');
            $BORVal = htmlspecialchars($rowScout['BOR'] ?? '');
            $BeneficiaryVal = htmlspecialchars($rowScoutapas['Beneficiary'] ?? '');
            $ProjectNameVal = htmlspecialchars($rowScout['ProjectName'] ?? '');
            $ProjectHoursVal = htmlspecialchars($rowScout['ProjectHours'] ?? '');

            echo "<tr>
                        <td>$UnitTypeVal</td>
                        <td>$UnitNumberVal</td>
                        <td>$GenderVal</td>
                        <td><a href=index.php?page=edit-select-scout&Scoutid=$Scoutid\">$FirstName $LastName</a></td>
                        <td>$BORVal</td>
                        <td>$BeneficiaryVal</td>
                        <td>$ProjectNameVal</td>
                        <td>$ProjectHoursVal</td>
                    </tr>";

            // Escape CSV values
            $csv_output .= sprintf(
              '"%s","%s","%s","%s","%s","%s","%s","%s"%s',
              str_replace('"', '""', $UnitTypeVal),
              str_replace('"', '""', $UnitNumberVal),
              str_replace('"', '""', $GenderVal),
              str_replace('"', '""', "$FirstName $LastName"),
              str_replace('"', '""', $BORVal),
              str_replace('"', '""', $BeneficiaryVal),
              str_replace('"', '""', $ProjectNameVal),
              str_replace('"', '""', $ProjectHoursVal),
              "\n"
            );
          }
          mysqli_stmt_close($stmt);

          ?>
        </tbody>
      </table>

      <form class="d-print-none d-flex justify-content-center" name="export" action="export.php" method="post" class="mt-4">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="csv_hdr" value="<?php echo htmlspecialchars($csv_hdr); ?>">
        <input type="hidden" name="csv_output" value="<?php echo htmlspecialchars($csv_output); ?>">
        <input class="btn btn-primary btn-sm" type="submit" value="Export to CSV">
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

          $('#eagleTable').DataTable({
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

</body>

</html>