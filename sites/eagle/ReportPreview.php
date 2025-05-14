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
  <style>
    .wrapper {
      width: 360px;
      padding: 20px;
    }
  </style>
</head>

<body>
  <?php include('header.php');

  //Allow selection by Unit
  $qryUnits = "SELECT DISTINCTROW UnitType, UnitNumber FROM scouts WHERE (`AttendedPreview` IS NULL OR `AttendedPreview`='0') AND 
        (`Eagled` IS NULL OR `Eagled`='0') AND (`AgedOut` IS NULL OR `AgedOut`='0')";


  if (!$Units = $cEagle->doQuery($qryUnits)) {
    $msg = "Error: doQuery()";
    $cEagle->function_alert($msg);
  }

  ?>
  </br>
  <form method=post>
    <div class="form-row px-5">
      <div class="col-2">
        <label for='Unit'>Choose a Unit: </label>
        <select class='form-control' id='Unit' name='Unit'>
          <?php
          while ($rowUnits = $Units->fetch_assoc()) {
            echo "<option value=" . $rowUnits['UnitType'] . "-" . $rowUnits['UnitNumber'] . ">" . $rowUnits['UnitType'] . " " . $rowUnits['UnitNumber'] . "</option>";
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

  $csv_hdr = "Unit Type,Unit#,  Gender, Name, Age Out Date";
  $csv_output = "";
  ?>
  <center>
    <h4>Scouts who have not attended Preview</h4>
    <div class="px-5">
      <table class="fixed_header table table-striped">
        <thead>
          <tr>
            <th>Unit Type</th>
            <th>Unit#</th>
            <th>Gender</th>
            <th>Name</th>
            <th>Age Out Date</th>
          </tr>
        </thead>
        <?php
        if ($SelectedUnit & $SelectedNum) {
          $queryScout = "SELECT * FROM `scouts` 
        WHERE (`AttendedPreview` IS NULL OR `AttendedPreview`='0') AND 
        (`Eagled` IS NULL OR `Eagled`='0') AND 
        (`AgedOut` IS NULL OR `AgedOut`='0') AND
        (`UnitType`='$SelectedUnit') AND (`UnitNumber`='$SelectedNum')
        ORDER BY `UnitType` ASC, `UnitNumber` ASC,`Gender` ASC, `LastName` ASC";
        } else {
          $queryScout = "SELECT * FROM `scouts` 
        WHERE (`AttendedPreview` IS NULL OR `AttendedPreview`='0') AND 
        (`Eagled` IS NULL OR `Eagled`='0') AND 
        (`AgedOut` IS NULL OR `AgedOut`='0')
        ORDER BY `UnitType` ASC, `UnitNumber` ASC,`Gender` ASC, `LastName` ASC";
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
            $rowScout["AgeOutDate"] . "</td></tr>";

          $csv_output .= $rowScout["UnitType"] . ",";
          $csv_output .= $rowScout["UnitNumber"] . ",";
          $csv_output .= $rowScout["Gender"] . ", ";
          $csv_output .= $FirstName . " " . $rowScout["LastName"] . ", ";
          $csv_output .= $rowScout["AgeOutDate"] . "\n";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div";
        echo "<b>For a total of " . mysqli_num_rows($Scout) . "</b>";

        ?>
        <form name="export" action="../export.php" method="post" style="padding: 20px;">
          <input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
          <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
          <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
        </form>
  </center>
  <?php include('Footer.php'); ?>
</body>

</html>