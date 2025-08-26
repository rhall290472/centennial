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

<body>
  <?php
  $csv_hdr = "First Name, Last Name, Email, Home Phone, Mobile Phone, Trained, Position";
  $csv_output = "";
  ?>

  <h4 class="text-center">Active Coaches</h4>

  <div class="table-responsive">
    <table id="activeCoachesTable" class="table table-striped">
      <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Home Phone</th>
        <th>Mobile Phone</th>
        <th>Trained</th>
        <th>Position</th>
      </tr>
      </thead>
      <tbody>
      <?php
      $queryCoach = "SELECT * FROM `coaches` WHERE `Active`='1' ORDER BY `Last_Name`";

      if (!$Coach = $cEagle->doQuery($queryCoach)) {
        $msg = "Error: doQuery()";
        $cEagle->function_alert($msg);
      }

      while ($rowCoach = $Coach->fetch_assoc()) {
        $FName = $cEagle->GetPreferredName($rowCoach);
        $Trained = $rowCoach["Trained"] ? "Yes" : "No";

        echo "<tr>" .
          "<td>" . "<a href=index.php?page=edit-select-coach&Coachesid=" . $rowCoach['Coachesid'] . ">" . $FName . " " . $rowCoach['Last_Name'] . "</a> </td>" .
          "<td>" . $cEagle->formatEmail($rowCoach["Email_Address"])  . "</td>" .
          "<td>" . $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Home"])  . "</td>" .
          "<td>" . $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Mobile"]) . "</td>" . 
          "<td>" . $Trained . "</td>" .
          "<td>" . $rowCoach['Position'] . "</td>" .
        "</tr>";

        $csv_output .= $FName . ",";
        $csv_output .= $rowCoach['Last_Name'] . ",";
        $csv_output .= $rowCoach["Email_Address"] . ",";
        $csv_output .= $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Home"]) . ",";
        $csv_output .= $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Mobile"]) . ",";
        $csv_output .= $Trained . ",";
        $csv_output .= $rowCoach['Position'] . "\n";
      } ?>
      </tbody>
    </table>

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

      $('#activeCoachesTable').DataTable({
        "paging": false, // Display all rows
        "searching": true, // Enable search
        "ordering": true, // Enable sorting
        "info": true, // Show table info
        "autoWidth": false, // Disable auto width for Bootstrap
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