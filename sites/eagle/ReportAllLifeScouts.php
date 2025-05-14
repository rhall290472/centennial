<?php
if (!session_id()) {
  session_start();
}

require_once 'CEagle.php';
$cEagle = CEagle::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include('head.php'); ?>
</head>

<body>
  <?php
  include('header.php');
  //include('navmenu.php');

  //Allow selection by Unit
  $qryUnits = "SELECT DISTINCTROW UnitType, UnitNumber FROM scouts WHERE (`ProjectApproved`IS NULL OR `ProjectApproved`='0') AND 
        (`Eagled` IS NULL OR `Eagled`='0') AND (`AgedOut` IS NULL OR `AgedOut`='0') AND (`is_deleted` IS NULL OR `is_deleted`='0')
        ORDER BY `UnitType` ASC, `UnitNumber` ASC";


  if (!$Units = $cEagle->doQuery($qryUnits)) {
    $msg = "Error: doQuery()";
    $cEagle->function_alert($msg);
  }
  ?>
  <form method=post>
    <div class="form-row px-5 d-print-none">
      <div class="col-2">
        <label for='Unit'>Choose a Unit: </label>
        <select class='form-control' id='Unit' name='Unit'>
          <?php
          while ($rowUnits = $Units->fetch_assoc()) {
            echo "<option value=" . $rowUnits['UnitType'] . "-" . $rowUnits['UnitNumber'] . ">" . $rowUnits['UnitType'] . " " . $rowUnits['UnitNumber'] . "</option>";
            //echo "option value=".$rowUnits['UnitType'] . " " . $rowUnits['UnitNumber'] .">".$rowUnits['UnitNumber']."/option";
          }
          ?>
        </select>
        </div>
        <div class="col-2 py-4">
        <input class='btn btn-primary btn-sm' type='submit' name='SubmitUnit' value='Select Unit' />
        </div>
      </div>
    </div>
  </form>
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

  $csv_hdr = "Unit, Gender, Name, Age Out Date, Scout Email, ULName, ELEMail, CCName, CCEmail, Project Approval";
  $csv_output = "";

  ?>

  <center>
    <h4>Report of all life scouts in database</h4>
    <table class="fixed_header table table-striped" style="width:1250px">
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
      <?php

      if ($SelectedUnit & $SelectedNum) {
        $queryScout = "SELECT * FROM `scouts` 
                                        WHERE (`is_deleted` IS NULL OR `is_deleted`='0') AND
                                        (`UnitType`='$SelectedUnit') AND (`UnitNumber`='$SelectedNum') AND
                                        (`Eagled` IS NULL OR `Eagled`='0') AND (`AgedOut` IS NULL OR `AgedOut`='0')
                                        ORDER BY  STR_TO_DATE(`AgeOutDate`, '%m/%d/%Y') ASC, `Gender`";
      } else {
        $queryScout = "SELECT * FROM `scouts` 
                                        WHERE (`is_deleted` IS NULL OR `is_deleted`='0') AND
                                        (`Eagled` IS NULL OR `Eagled`='0') AND (`AgedOut` IS NULL OR `AgedOut`='0')
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
          "<a href=./ScoutPageAll.php?Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td>" .
          $rowScout["AgeOutDate"] . "</td><td>" .
          $rowScout["ProjectDate"] . "</td></tr>";

        $csv_output .= $rowScout["UnitType"] . " " . $rowScout["UnitNumber"] . ", ";
        $csv_output .= $rowScout["Gender"] . ", ";
        $csv_output .=  $FirstName . " " . $rowScout["LastName"] . ", ";
        $csv_output .= $rowScout["AgeOutDate"] . ", ";
        $csv_output .= $rowScout["Email"] . ", ";
        $csv_output .= $rowScout["ULFirst"] . " " . $rowScout["ULLast"] . ", ";
        $csv_output .= $rowScout["ULEmail"] . ", ";
        $csv_output .= $rowScout["CCFirst"] . " " . $rowScout["CCLast"] . ", ";
        $csv_output .= $rowScout["CCEmail"] . ", ";
        $csv_output .= $rowScout["ProjectDate"] . "\n";
      }
      echo "</tbody>";
      echo "</table>";
      $csv_output .= "\n";
      echo "<b>For a total of " . mysqli_num_rows($Scout) . "</b>";

      ?>

      <form class=" d-print-none" name="export" action="../export.php" method="post" style="padding: 20px;">
        <input class='btn btn-primary btn-sm d-print-none' style="width:220px" type="submit" value="Export table to CSV">
        <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
        <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
      </form>
  </center>

  </div>
  <?php include('Footer.php'); ?>
</body>

</html>