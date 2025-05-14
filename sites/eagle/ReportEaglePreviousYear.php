<?php
if (!session_id()) {
  session_start();

  require_once 'CEagle.php';
  $cEagle = CEagle::getInstance();

  // This code stops anyone for seeing this page unless they have logged in and
  // they account is enabled.
  if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
    header("HTTP/1.0 403 Forbidden");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-6PCWFTPZDZ"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-6PCWFTPZDZ');
  </script>

  <meta charset="UTF-8">
  <title>Eagle Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../bootstrap-5.3.2/css/bootstrap.css">
  <link rel="stylesheet" href="css/eagle.css">
  <!--    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
-->
  <style>
    body {
      font: 14px sans-serif;
      margin-top: 140px;
    }

    .wrapper {
      width: 360px;
      padding: 20px;
    }
  </style>
</head>

<body>
  <?php include('header.php');
  $csv_hdr = "Unit Type,Unit#,  Gender, Name, Year, Project Name, Project Hours";
  $csv_output = "";
  ?>
  <center>
    <h4>Scouts who have Eagled previous Year </h4>
    <table class="tl1 tc2 tc3 tl4 tl5 tl6">
      <td style="width:100px">
      <td style="width:50px">
      <td style="width:50px">
      <td style="width:250px">
      <td style="width:100px">
      <td style="width:300px">
      <td style="width:100px">
        <tr>
          <th>Unit Type</th>
          <th>Unit#</th>
          <th>Gender</th>
          <th>Name</th>
          <th>Year</th>
          <th>Project Name</th>
          <th>Project Hours</th>
        </tr>

        <?php
        $year = date("Y") - 1; // Previous Year ..
        $queryScout = "SELECT * FROM `scouts` WHERE `Eagled`='1' AND `BOR` LIKE '%$year' ORDER BY `UnitType`, `UnitNumber`,`LastName`";

        if (!$Scout = $cEagle->doQuery($queryScout)) {
          $msg = "Error: doQuery()";
          $cEagle->function_alert($msg);
        }

        while ($rowScout = $Scout->fetch_assoc()) {
          $FirstName = $cEagle->GetScoutPreferredName($rowScout);

          echo "<tr><td>" .
            $rowScout["UnitType"] . "</td><td>" .
            $rowScout["UnitNumber"] . "</td><td>" .
            $rowScout["Gender"] . "</td><td>" .
            "<a href=./ScoutPageAll.php?Scoutid=" . $rowScout['Scoutid'] . ">" . $FirstName . " " . $rowScout["LastName"] . "</a> </td><td>" .
            $rowScout["BOR"] . "</td><td>" .
            $rowScout["ProjectName"] . "</td><td>" .
            $rowScout['ProjectHours'] . "</td></tr>";

          $csv_output .= $rowScout["UnitType"] . ",";
          $csv_output .= $rowScout["UnitNumber"] . ",";
          $csv_output .= $rowScout["Gender"] . ", ";
          $csv_output .= $FirstName . " " . $rowScout["LastName"] . ", ";
          $csv_output .= $rowScout["BOR"] . ",";
          $csv_output .= $rowScout["ProjectName"] . ",";
          $csv_output .= $rowScout['ProjectHours'] . "\n";
        }
        echo "</table>";

        echo "<b>For a total of " . mysqli_num_rows($Scout) . " Eagles this year.</b>";
        ?>
        <form name="export" action="../export.php" method="post" style="padding: 20px;">
          <input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
          <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
          <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
        </form>
  </center>
</body>

</html>