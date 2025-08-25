<?php
/*
 * 
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */

// Load classes
load_class(__DIR__ . '/../Classes/CEagle.php');
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


  <h4 class="text-center">Scouts with Pending EBOR's</h4>
  <div class="table-responsive">
    <table id="pendingEBORTable" class="table table-striped">
      <tr>
        <th>Unit Type</th>
        <th>Unit#</th>
        <th>Gender</th>
        <th>Name</th>
        <th>District Member</th>
        <th>EBOR Date</th>
        <th>District Board Member</th>
      </tr>


      <?php
      $queryScout = "SELECT * FROM `scouts`  WHERE 
        (`BOR`<>'') AND
        (`Eagled` IS NULL OR `Eagled`='0') AND 
        (`AgedOut` IS NULL OR `AgedOut`='0') 
        ORDER BY `UnitType`, `UnitNumber`,`LastName`";

      if (!$Scout = $cEagle->doQuery($queryScout)) {
        $msg = "Error: doQuery(" . $queryScout . ") Returned an error";
        error_log($msg);
        exit();
      }

      while ($rowScout = $Scout->fetch_assoc()) {
        $queryCoaches = "SELECT * FROM `coaches` WHERE `Coachesid`='$rowScout[BOR_Member]'";
        $result_ByCoaches = $cEagle->doQuery($queryCoaches);
        $rowCoach = $result_ByCoaches->fetch_assoc();
        if ($rowCoach) {
          $Coachid = $rowCoach['Coachesid'];
          $CoachFirst = $cEagle->GetPreferredName($rowCoach);
          $CoachLast = $rowCoach['Last_Name'];
        } else {
          $Coachid = -1;
          $CoachFirst = "";
          $CoachLast = "";
        }

        $FirstName = $cEagle->GetScoutPreferredName($rowScout);
        echo "<tr><td>" .
          $rowScout["UnitType"] . "</td><td>" .
          $rowScout["UnitNumber"] . "</td><td>" .
          $rowScout["Gender"] . "</td><td>" .
          "<a href=index.php?page=edit-select-scout&Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td>" .
          $rowCoach['Last_Name'] . " " . $rowCoach['First_Name']  . "</td><td>" .
          $rowScout["BOR"] . "</td><td>" .
          "<a href=./CoachPage.php?Coachesid=" . $Coachid . ">" . $CoachFirst . " " . $CoachLast . "</td></tr>";
      } ?>
    </table>


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

        $('#pendingEBORTable').DataTable({
          "paging": false, // Display all rows
          "searching": true, // Enable search
          "ordering": true, // Enable sorting
          "info": true, // Show table info
          "autoWidth": false, // Disable auto width for Bootstrap
          "columnDefs": [{
              "type": "date-us",
              "targets": 5 // AgeOutDate column (0-based index)
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