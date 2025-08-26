<?php
/*
 * ReportEagles.php: Page for Eagles in the Centennial District website.
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */

/// Load classes
load_class(BASE_PATH . '/../Classes/CEagle.php');
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
  if (isset($_POST['SubmitYear'])) {
    $year = $_POST['Year'];
    $cEagle->SetYear($year);
  }
  $cEagle->SelectYear($_SESSION['csrf_token']);
  $csv_hdr = "Unit Type,Unit#,  Gender, Name, Year, Beneficiary, Project Name, Project Hours";
  $csv_output = "";
  // Sortable by column header..
  if (!isset($_GET['sort']))
    $field = 'UnitType, UnitNumber, Gender, LastName';
  else {
    if ($_GET['sort'] == 'UnitNumber')
      $field = 'UnitNumber';

    else if ($_GET['sort'] == 'Name')
      $field = 'LastName';
  }

  if (!isset($_GET['order']))
    $ordertype = 'asc';
  else {
    $ordertype = ($_GET['order'] == 'desc') ? 'asc' : 'desc';
    if ($_GET['order'] == 'asc') {
      $sort_arrow =  '<img src="./img/sorting-arrow-desc.png" />';
    } else if ($_GET['order'] == 'desc') {
      $sort_arrow =  '<img src="./img/sorting-arrow-asc.png" />';
    } else {
      $sort_arrow =  '<img src="./img/sorting-arrow-desc.png" />';
    }
  }
  ?>

  <h4 class="text-center"><?php echo "Scouts who have Eagled " . $cEagle->GetYear() ?> </h4>
  <div class="table-responsive">
    <table id="eagleYearTable" class="table table-striped">
      <thead>
        <tr>
          <th style="width:100px">Unit Type</th>
          <th style='width:50px'>Unit#</th>
          <th style='width:50px'>Gender</th>
          <th style="width:250px">Name</th>
          <th style='width:100px'>Year</th>
          <th style='width:300px'>Beneficiary</th>
          <th style='width:300px'>Project Name</th>
          <th style='width:100px'>Project Hours</th>
        </tr>
      </thead>
      <?php
      $year = $cEagle->GetYear();
      $queryScout = "SELECT * FROM `scouts` WHERE `Eagled`='1' AND `BOR` LIKE '%$year' ORDER BY $field $ordertype";

      if (!$Scout = $cEagle->doQuery($queryScout)) {
        $msg = "Error: doQuery()";
        $cEagle->function_alert($msg);
      }

      ?><tbody style='height:calc(100vh - 400px)'>
        <?php
        while ($rowScout = $Scout->fetch_assoc()) {
          $FirstName = $cEagle->GetScoutPreferredName($rowScout);

          echo "<tr><td style='width:100px'>" .
            $rowScout["UnitType"] . "</td><td style='width:50px'>" .
            $rowScout["UnitNumber"] . "</td><td style='width:50px'>" .
            $rowScout["Gender"] . "</td><td style='width:250px'>" .
            "<a href=index.php?page=edit-select-scout&Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td style='width:100px'>" .
            $rowScout["BOR"] . "</td><td style='width:300px'>" .
            $rowScout["Beneficiary"] . "</td><td style='width:300px'>" .
            $rowScout["ProjectName"] . "</td><td style='width:100px'>" .
            $rowScout['ProjectHours'] . "</td></tr>";

          $csv_output .= $rowScout["UnitType"] . ",";
          $csv_output .= $rowScout["UnitNumber"] . ",";
          $csv_output .= $rowScout["Gender"] . ", ";
          $csv_output .= $FirstName . " " . $rowScout["LastName"] . ", ";
          $csv_output .= $rowScout["BOR"] . ",";
          $csv_output .= $rowScout["Beneficiary"] . ",";
          $csv_output .= $rowScout["ProjectName"] . ",";
          $csv_output .= $rowScout['ProjectHours'] . "\n";
        }
        echo "</tbody>";
        echo "</table>";


        $Eagled = mysqli_num_rows($Scout);
        $AgedOut = $cEagle->AgedOutByYear($year);
        $PreviewAged = $cEagle->AttendPreviewAgedOut($year);
        $PreviewEagle = $cEagle->AttendPreviewEagled($year);
        $ApprovedProject = $cEagle->ApprovedProject($year);
        $str = sprintf(
          "%d Scouts Aged out, of which %d Attended Eagle Preview, %d Had Approved Projects, Number of Eagles that attend Preview %d",
          $AgedOut,
          $PreviewAged,
          $ApprovedProject,
          $PreviewEagle
        );
        echo "Statistics: ".$str;?>
        </br></br>

      
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

            $('#eagleYearTable').DataTable({
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