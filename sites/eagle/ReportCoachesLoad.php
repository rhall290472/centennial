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
  <?php include('header.php'); ?>
  <center>
    <h4>Coaches Work Load</h4>
    <?php
    $queryCoach = "SELECT * FROM `coaches` WHERE `Active`='1' ORDER BY `Last_Name`";
    $LastCoach = null;
    if (!$Coach = $cEagle->doQuery($queryCoach)) {
      $msg = "Error: doQuery()";
      $cEagle->function_alert($msg);
    }

    while ($rowCoach = $Coach->fetch_assoc()) {
      if ($LastCoach != $rowCoach['Coachesid']) {
        $LastCoach = $rowCoach['Coachesid'];
        echo "</table>";
        echo "</br>";


        $qryScouts = "SELECT * FROM `scouts` WHERE `Coach`='$rowCoach[Coachesid]' AND 
                (`Eagled` IS NULL OR `Eagled`='0') AND 
                (`AgedOut` IS NULL OR `AgedOut`='0') AND
                (`is_deleted` IS NULL OR `is_deleted`='0')
                ORDER BY 'LastName'";
        $Scout = $cEagle->doQuery($qryScouts);
        $FName = $cEagle->GetPreferredName($rowCoach);

        // Start a new table for the new Coach
        echo "</br><h5>" . $FName . " " . $rowCoach['Last_Name'] . "</h5>";
        echo "<b>Number of Scouts working with " . mysqli_num_rows($Scout) . "</b></br>";
    ?>

        <table style="width:550px;" class="tl1 tl2 tl3 table table-striped">
          <td style="width:250px">
          <td style="width:150px">
          <td style="width:150px">
            <tr>
              <th>Name</th>
              <th>Proposal Approval Date</th>
              <th>Age out Date</th>
            </tr>
        <?php
      }

      while ($rowScout = $Scout->fetch_assoc()) {
        echo "<tr><td>" .
          "<a href=./ScoutPageAll.php?Scoutid=" . $rowScout['Scoutid'] . ">" . $rowScout["FirstName"] . " " . $rowScout["LastName"] . "</a> </td><td>" .
          $rowScout["ProjectDate"] . "</td><td>" .
          $rowScout["AgeOutDate"] . "</td></tr>";
      }
    }
    echo "</table>";
        ?>
  </center>
  <?php include('Footer.php'); ?>
</body>

</html>