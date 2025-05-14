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
  $csv_hdr = "Unit Type,Unit#,  Gender, Name, Year, Beneficiary, Project Name, Project Hours";
  $csv_output = "";


  $cEagle->SelectUnit();

  if (isset($_POST['SubmitUnit'])) {
    $UnitType = strtok($_POST['Unit'], ",");
    $UnitNum  = strtok(",");
  }

  ?>
  <center>
    <h4>Scouts who have Eagled (since 2017)</h4>
    <table class="fixed_header table table-striped" style="width:1250px">
      <thead>
        <tr>
          <th style="width:100px">Unit Type</th>
          <th style="width:50px">Unit#</th>
          <th style="width:50px">Gender</th>
          <th style="width:250px">Name</th>
          <th style="width:100px">Year</th>
          <th style="width:300px">Beneficiary</th>
          <th style="width:300px">Project Name</th>
          <th style="width:100px">Project Hours</th>
        </tr>
      </thead>
      <?php
      if (!empty($UnitType) && !empty($UnitNum)) {
        $queryScout = "SELECT * FROM `scouts` WHERE `Eagled`='1' AND `UnitType`='$UnitType' AND `UnitNumber`='$UnitNum' ORDER BY `LastName`";
      } else {
        $queryScout = "SELECT * FROM `scouts` WHERE `Eagled`='1' ORDER BY `UnitType`, `UnitNumber`,`LastName`";
      }

      if (!$Scout = $cEagle->doQuery($queryScout)) {
        $msg = "Error: doQuery()";
        $cEagle->function_alert($msg);
      }

      echo "<tbody>";
      while ($rowScout = $Scout->fetch_assoc()) {
        $FirstName = $cEagle->GetScoutPreferredName($rowScout);

        echo "<tr><td style='width:100px'>" .
          $rowScout["UnitType"] . "</td><td style='width:50px'>" .
          $rowScout["UnitNumber"] . "</td><td style='width:50px'>" .
          $rowScout["Gender"] . "</td><td style='width:250px'>" .
          "<a href=./ScoutPageAll.php?Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td style='width:100px'>" .
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