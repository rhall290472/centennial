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
  <center>
    <h4>Coaches History</h4>
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


        $qryScouts = "SELECT * FROM `scouts` WHERE `Coach`='$rowCoach[Coachesid]' OR `BOR_Member`='$rowCoach[Coachesid]' ORDER BY 'LastName'";
        $Scout = $cEagle->doQuery($qryScouts);
        $FName = $cEagle->GetPreferredName($rowCoach);

        // Start a new table for the new Coach
        echo "</br><h5>" . $FName . " " . $rowCoach['Last_Name'] . "</h5>";
        //style="width:800px;"
    ?>

        <table s class="tl1 tl2 tl3 tl4 tl5 table table-striped" style="width:400px"">
                    <td style=" width:250px">
          <td style="width:75px">
          <td style="width:75px">
            <tr>
              <th>Name</th>
              <th>Eagled</th>
              <th>Aged Out</th>
            </tr>
        <?php
      }
      while ($rowScout = $Scout->fetch_assoc()) {
        echo "<tr><td>" .
          "<a href=index.php?page=edit-select-scout&Scoutid=" . $rowScout['Scoutid'] . ">" . $rowScout["FirstName"] . " " . $rowScout["LastName"] . "</a> </td><td>" .
          $rowScout['Eagled'] . "</td><td>" .
          $rowScout['AgedOut'] . "</td></tr>";
      }
      echo "<b>Number of Scouts worked with " . mysqli_num_rows($Scout) . "</b></br>";
    }
    echo "</table>";
        ?>
  </center>
</body>

</html>