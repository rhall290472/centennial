<?php
if (!session_id()) {
  session_start();
}

require_once('CEagle.php');
require('../cAdultLeaders.php');

$cEagle = CEagle::getInstance();
$classAdultLeader = AdultLeaders::getInstance();

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
  $csv_hdr = "First Name, Last Name, Email, Home Phone, Mobile Phone, YPT, Position";
  $csv_output = "";
  ?>
  <center>
    <h4>Coaches YPT status (Red is expired)</h4>
    <div class="px-5">
      <table class="tl1 tl2 tl3 tl4 tl5 tl6 tl7 table table-striped">
        <td style="width:250px">
        <td style="width:150px">
        <td style="width:150px">
        <td style="width:150px">
        <td style="width:150px">
        <td style="width:75px">
        <td style="width:250px">
          <tr>
            <th>Name</th>
            <th>Member ID</th>
            <th>Email</th>
            <th>Home Phone</th>
            <th>Mobile Phone</th>
            <th>YPT</th>
            <th>Position</th>
          </tr>

          <?php
          $queryCoach = "SELECT * FROM `coaches` WHERE `Active`='1' ORDER BY `Last_Name`";

          if (!$Coach = $cEagle->doQuery($queryCoach)) {
            $msg = "Error: doQuery()";
            $cEagle->function_alert($msg);
          }

          $ToDate = date("m/d/Y");

          while ($rowCoach = $Coach->fetch_assoc()) {
            // 
            // TODO: Pull YPT date from the Adult_leaders database ypt table.
            //
            $YPT_Date = $classAdultLeader->GetMemberYPT($rowCoach['Member_ID']);
            $FName = $cEagle->GetPreferredName($rowCoach);

            //$YPT_Date = date($rowCoach["YPT_Expires"]);
            if (strtotime($ToDate) > strtotime($YPT_Date)) {
              $Formatter = "<b style='color:red;'>";
            } else {
              $Formatter = "";
            }


            echo "<tr><td>" .
              "<a href=./CoachPage.php?Coachesid=" . $rowCoach['Coachesid'] . ">" . $FName . " " . $rowCoach['Last_Name'] . "</a> </td><td>" .
              $rowCoach["Member_ID"] . "</td><td>" .
              $cEagle->formatEmail($rowCoach["Email_Address"])  . "</td><td>" .
              $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Home"])  . "</td><td>" .
              $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Mobile"]) . "</td><td>" .
              $Formatter . $YPT_Date  . "</td><td>" .
              $rowCoach['Position'] . "</td></tr>";

            $csv_output .= $FName . ",";
            $csv_output .= $rowCoach['Last_Name'] . ",";
            $csv_output .= $rowCoach["Email_Address"] . ",";
            $csv_output .= $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Home"]) . ",";
            $csv_output .= $cEagle->formatPhoneNumber(null, $rowCoach["Phone_Mobile"]) . ",";
            $csv_output .= $YPT_Date . ",";
            $csv_output .= $rowCoach['Position'] . "\n";
          }
          echo "</table>";
          echo "</div>";
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