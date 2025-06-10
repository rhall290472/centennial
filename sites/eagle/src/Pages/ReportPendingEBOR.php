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
    <h4>Scouts with Pending EBOR's</h4>
    <table class="tl1 tc2 tc3 tl4 tl5 tl6 table table-striped">
      <td style="width:75x">
      <td style="width:50px">
      <td style="width:100px">
      <td style="width:250px">
      <td style="width:150px">
      <td style="width:100px">
      <td style="width:250px">
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
            "<a href=./ScoutPageAll.php?Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td>" .
            $rowCoach['Last_Name'] . " " . $rowCoach['First_Name']  . "</td><td>" .
            $rowScout["BOR"] . "</td><td>" .
            "<a href=./CoachPage.php?Coachesid=" . $Coachid . ">" . $CoachFirst . " " . $CoachLast . "</td></tr>";
        }
        echo "</table>";

        echo "<b>For a total of " . mysqli_num_rows($Scout) . "</b>";

        ?>
  </center>
</body>

</html>