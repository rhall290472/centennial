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

  if (isset($_POST['SubmitYear'])) {
    $year = $_POST['Year'];
    $cEagle->SetYear($year);
  }
  $cEagle->SelectYear();

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

  <center>
    <h4><?php echo "Scouts who have Eagled " . $cEagle->GetYear() ?> </h4>
    <div class="px-5">
      <table class="fixed_header table table-striped" style="width:1250px">
        <thead>
          <tr>
            <?php echo '<th style="width:100px"><a href="ReportEagleYear.php?sort=UnitNumber&order=' . $ordertype . '">Unit Type';
            if ($field == 'UnitNumber') {
              echo $sort_arrow;
            }
            echo '</a></th>' ?>
            <th style='width:50px'>Unit#</th>
            <th style='width:50px'>Gender</th>
            <?php echo '<th style="width:250px"><a href="ReportEagleYear.php?sort=Name&order=' . $ordertype . '">Name';
            if ($field == 'LastName') {
              echo $sort_arrow;
            }
            echo '</a></th>' ?>
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
          echo "</div>";

          echo "</br><b>For a total of " . mysqli_num_rows($Scout) . " Eagles this year.</b></br>";
          echo "Statistics</br>";
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
          echo $str . "</br></br>";
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