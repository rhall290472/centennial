<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #   Copyright 2017-2024 - Richard Hall                                   #  !
!  #                                                                        #  !
!  #   The information contained herein is the property of Richard          #  !
!  #   Hall, and shall not be copied, in whole or in part, or               #  !
!  #   disclosed to others in any manner without the express written        #  !
!  #   authorization of Richard Hall.                                       #  !
!  #                                                                        #  !
!  #                                                                        #  !
! /##########################################################################\ !
!//                                                                          \\!
!/                                                                            \!
!==============================================================================!
*/

// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  die('An error occurred. Please try again later.');
}

load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');
load_class(SHARED_PATH . '/src/Classes/CUnit.php');
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');

$UNIT = UNIT::getInstance();
$cAdultLeaders = AdultLeaders::getInstance();

if (isset($_POST['btn'])) {
  if (isset($_POST['packs']))
    $_GET['unit_name'] = $_POST['packs'];
  else if (isset($_POST['troops']))
    $_GET['unit_name'] = $_POST['troops'];
  else if (isset($_POST['crews']))
    $_GET['unit_name'] = $_POST['crews'];
}
// Get Unit Name if supplied
if (isset($_GET['unit_name'])) {
  $unit_name = $_GET['unit_name'];
} else {
  $unit_name = "";
?>
  <div class="container-fluid">
    <select_unit>
      <div class="px-lg-5">
        <div class="row">
          <div class="col-2">
            <form method="post">
              <select class="form-control d-print-none" id="packs" name="packs">
                <?php
                echo "<option value=\"\"></option>";
                $ResultUnits = $UNIT->GetPackUnits();
                while ($rowUnits = $ResultUnits->fetch_assoc()) {
                  echo "<option value=\"{$rowUnits['Unit']}\">";
                  echo htmlspecialchars($rowUnits['Unit']);
                  echo "</option>";
                }
                ?>
              </select>
          </div>
          <div class="col-1">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input class="btn btn-primary btn-sm" type="submit" value="Packs" name="btn">
          </div>
          </form>

          <div class="col-2">
            <form method="post">
              <select class="form-control d-print-none" id="troops" name="troops">
                <?php
                echo "<option value=\"\"></option>";
                $ResultUnits = $UNIT->GetTroopUnits();
                while ($rowUnits = $ResultUnits->fetch_assoc()) {
                  echo "<option value=\"{$rowUnits['Unit']}\">";
                  echo htmlspecialchars($rowUnits['Unit']);
                  echo "</option>";
                }
                ?>
              </select>
          </div>
          <div class="col-1">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input class="btn btn-primary btn-sm" type="submit" value="Troops" name="btn">
          </div>
          </form>

          <div class="col-2">
            <form method="post">
              <select class="form-control d-print-none" id="crews" name="crews">
                <?php
                echo "<option value=\"\"></option>";
                $ResultUnits = $UNIT->GetCrewUnits();
                while ($rowUnits = $ResultUnits->fetch_assoc()) {
                  echo "<option value=\"{$rowUnits['Unit']}\">";
                  echo htmlspecialchars($rowUnits['Unit']);
                  echo "</option>";
                }
                ?>
              </select>
          </div>
          <div class="col-1">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input class="btn btn-primary btn-sm" type="submit" value="Crews" name="btn">
          </div>
          </form>
        </div>
      </div>
    </select_unit>
  </div>
<?php
}

if (empty($unit_name)) {
  $Direct = $cAdultLeaders->DirectTrained($unit_name, "YES");
  $NonDirect = $cAdultLeaders->DirectTrained($unit_name, "NO");
  $sql = sprintf("SELECT * FROM trainedleader WHERE Unit <> '' ORDER BY Unit ASC");
  $sqlUnTrained = sprintf("SELECT * FROM trainedleader WHERE Unit <> '' AND Trained = 'NO' ORDER BY Direct_Contact_Leader");
  $sqlCO = sprintf('SELECT * FROM membershiptotals WHERE Unit = "%s" ;', $unit_name);
  $resultQueryCO = $UNIT->getDbConn()->query($sqlCO);
  $resultCO = $resultQueryCO->fetch_assoc();
  if (isset($resultCO)) {
    $UnitExpire = $resultCO['Expire_Date'];
    $UnitCO = sprintf("Chartered Organization: %s, Expire Date: %s", $resultCO['Chartered_Org'], $UnitExpire);
    $UnitCommissioner = "";
  } else {
    $UnitCO = "";
    $UnitCommissioner = "";
  }
} else {
  $Direct = $cAdultLeaders->DirectTrained($unit_name, "YES");
  $NonDirect = $cAdultLeaders->DirectTrained($unit_name, "NO");
  $sqlCO = sprintf('SELECT * FROM membershiptotals WHERE Unit = "%s" ;', $unit_name);
  $resultQueryCO = $UNIT->getDbConn()->query($sqlCO);
  if (!$resultQueryCO) {
    $strMsg = "ERROR: getDbConn()->query(" . $sqlCO . ") in file Unit_View.php";
    error_log($strMsg);
    $UNIT->function_alert("Error on page. Error has been logged.");
    $UNIT->GotoURL("index.php");
  }
  if ($resultCO = $resultQueryCO->fetch_assoc()) {
    $UnitCO = $resultCO['Chartered_Org'];
    $UnitExpire = $resultCO['Expire_Date'];
    $Commissioner = $resultCO['Assigned_Commissioner'];
    $Contact = $resultCO['Last_Contact'];
  } else {
    $UnitCO = "";
    $UnitExpire = "";
    $Commissioner = "";
    $Contact = "";
  }
  $UnitCO = sprintf("%s</br>Expire Date: %s", htmlspecialchars($UnitCO), htmlspecialchars($UnitExpire));
  $UnitCommissioner = sprintf("Unit Commissioner: %s</br> Last Contact: %s", htmlspecialchars($Commissioner), htmlspecialchars($Contact));
  $sqlDirectUnTrained = sprintf(
    'SELECT * FROM trainedleader WHERE Unit = "%s" AND Direct_Contact_Leader = "%s" AND Trained = "NO" ',
    $unit_name,
    'YES'
  );
  $sqlNonDirectUnTrained = sprintf(
    'SELECT * FROM trainedleader WHERE Unit = "%s" AND Direct_Contact_Leader = "%s" AND Trained = "NO" ',
    $unit_name,
    'NO'
  );
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>
  <!-- Spinner CSS -->
  <style>
    .spinner-container {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1000;
    }

    .spinner-border {
      width: 3rem;
      height: 3rem;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }
  </style>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawDirectChart);
    google.charts.setOnLoadCallback(drawUnDirectChart);

    function drawDirectChart() {
      var data = google.visualization.arrayToDataTable([
        ['Trained', 'Untrained'],
        <?php
        echo "['Trained'," . $Direct['Trained'] . "],";
        echo "['Untrained'," . $Direct['Untrained'] . "]";
        ?>
      ]);
      var options = {
        title: 'Training for Direct Contact',
        slices: {
          0: {
            color: 'green'
          },
          1: {
            color: 'red'
          }
        },
        pieSliceText: 'value'
      };
      var chart = new google.visualization.PieChart(document.getElementById('piechart1'));
      chart.draw(data, options);
    }

    function drawUnDirectChart() {
      var data = google.visualization.arrayToDataTable([
        ['Trained', 'Untrained'],
        <?php
        echo "['Trained'," . $NonDirect['Trained'] . "],";
        echo "['Untrained'," . $NonDirect['Untrained'] . "]";
        ?>
      ]);
      var options = {
        title: 'Training for NonDirect Contact',
        slices: {
          0: {
            color: 'green'
          },
          1: {
            color: 'red'
          }
        },
        pieSliceText: 'value'
      };
      var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
      chart.draw(data, options);
    }
  </script>
  <!-- DataTables and Export Libraries -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
</head>

<body style="padding:10px">
  <header id="header" class="header sticky-top">
    <?php load_template('/src/Templates/navbar.php'); ?>
  </header>
  <!-- Spinner HTML -->
  <div class="overlay" id="overlay"></div>
  <div class="spinner-container" id="spinner">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div class="container-fluid">
    <sort_options>
      <div class="px-lg-5">
        <div class="row">
          <div class="col-4">
            <?php $Title = "Centennial District Advancement - " . htmlspecialchars($unit_name); ?>
            <p style="font-size: 25px;font-weight: bold;"><?php echo $Title ?></p>
            <?php if (strlen($UnitCO) > 0) { ?>
              <p style="font-size: 18px;font-weight: bold;"><?php echo $UnitCO ?></p>
            <?php } ?>
            <?php if (strlen($UnitCommissioner) > 0) { ?>
              <p style="font-size: 18px;font-weight: bold;"><?php echo $UnitCommissioner ?></p>
            <?php } ?>
          </div>
          <div class="col-3">
            <table>
              <tr>
                <td>
                  <div id="piechart1" style="width: 350px; height: 200px;"></div>
                </td>
                <td>
                  <div id="piechart2" style="width: 350px; height: 200px;"></div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </sort_options>
  </div>
  <div class="container-fluid py-5 px-lg-5">
    <table id="leaderTable" class="table table-striped">
      <thead>
        <tr>
          <th>Unit</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Member ID</th>
          <th>Position</th>
          <th>Functional Role</th>
          <th>Direct Contact</th>
          <th>Trained</th>
          <th>YPT</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sqlAdult = "SELECT * FROM trainedleader WHERE Unit = ? ORDER BY Direct_Contact_Leader DESC";
        $stmt = $cAdultLeaders->getDbConn()->prepare($sqlAdult);
        $stmt->bind_param("s", $unit_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
          echo "<tr><td colspan='9'>No leader data available for this unit.</td></tr>";
        } else {
          while ($row = $result->fetch_assoc()) {
            $row_ypt = $cAdultLeaders->GetMemberYPT($row['MemberID']);
            $Trained = $row["Trained"];
            $LastName = $row["MemberID"];
            if (!strcmp($Trained, "NO")) {
              $TrainedURL = "<a href='index.php?page=untrained&btn=MemberID&SortBy=MemberID&MemberID=" . urlencode($LastName) . "'>";
              $Trained = sprintf("%s%s</a>", $TrainedURL, htmlspecialchars($Trained));
            }
            $YPTStatus = $cAdultLeaders->GetMemberYPTStatus($row['MemberID']);
            if (!strcmp($YPTStatus, "NO")) {
              $YPTURL = "<a href='YPT.php?btn=ByLastName&SortBy=Last_Name&last_name=" . urlencode($LastName) . "'>";
              $YPTStatus = sprintf("%s%s</a>", $YPTURL, htmlspecialchars($YPTStatus));
            } else {
              $ExpiredYPT = ($YPTStatus !== null) ? htmlspecialchars($YPTStatus) : '';
            }
            echo "<tr><td>" .
              htmlspecialchars($row["Unit"] ?? '') . "</td><td>" .
              htmlspecialchars($row["First_Name"] ?? '') . "</td><td>" .
              htmlspecialchars($row["Last_Name"] ?? '') . "</td><td>" .
              htmlspecialchars($row["MemberID"] ?? '') . "</td><td>" .
              htmlspecialchars($row["Position"] ?? '') . "</td><td>" .
              htmlspecialchars($row["FunctionalRole"] ?? '') . "</td><td>" .
              htmlspecialchars($row["Direct_Contact_Leader"] ?? '') . "</td><td>" .
              $Trained . "</td><td>" .
              $ExpiredYPT . "</td></tr>";
          }
        }
        $stmt->close();
        ?>
      </tbody>
    </table>
    <p style='text-align: center;'>Content last changed: Trained Leaders <?php echo htmlspecialchars($UNIT->GetLastUpdated('trainedleader')); ?> YPT <?php echo htmlspecialchars($UNIT->GetLastUpdated('ypt')); ?></p>
  </div>
  <script>
    $(document).ready(function() {
      // Show spinner
      function showSpinner() {
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('spinner').style.display = 'block';
      }
      // Hide spinner
      function hideSpinner() {
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('spinner').style.display = 'none';
      }
      showSpinner();
      // Initialize DataTable
      if ($('#leaderTable').length) {
        $('#leaderTable').DataTable({
          dom: 'Bfrtip',
          buttons: [{
              extend: 'csv',
              className: 'btn btn-primary',
              title: 'Leader_Report_<?php echo htmlspecialchars($unit_name); ?>'
            },
            {
              extend: 'excel',
              className: 'btn btn-primary',
              title: 'Leader_Report_<?php echo htmlspecialchars($unit_name); ?>'
            },
            {
              extend: 'pdf',
              className: 'btn btn-primary',
              title: 'Leader_Report_<?php echo htmlspecialchars($unit_name); ?>'
            }
          ],
          pageLength: -1,
          paging: false, // Disable pagination controls
          order: [
            [0, 'asc']
          ],
          columnDefs: [{
              type: 'num',
              targets: [3]
            }, // Member ID as numeric
            {
              type: 'html',
              targets: [7, 8]
            } // Trained and YPT contain HTML links
          ]
        });
      }
      hideSpinner();
    });
  </script>
  <?php
  if (!empty($unit_name)) {
    echo '<p style="page-break-before: always;">&nbsp;</p>';
    if (stristr($unit_name, 'Pack')) {
      include(BASE_PATH . '/src/Pages/pack_advancement.php');
    } else if (stristr($unit_name, 'Troop')) {
      include(BASE_PATH . '/src/Pages/troop_advancement.php');
    } else if (stristr($unit_name, 'Crew')) {
      include(BASE_PATH . '/src/Pages/crew_advancement.php');
    }
  }
  ?>
</body>

</html>