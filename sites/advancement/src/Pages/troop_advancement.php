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

//require 'Support_Functions.php';
load_class(BASE_PATH . '/src/Classes/CTroop.php');


$CTroop = CTroop::getInstance();

if (isset($_POST['SubmitYear'])) {
  $SelYear = $_POST['Year'];
  $_SESSION['year'] = $SelYear;
  $CTroop->SetYear($SelYear);
}

$Totals = $CTroop->GetTotals();
$NumofTroops = $CTroop->GetNumofTroops();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('/src/Templates/header.php'); ?>
</head>


<?php
$Title = "Centennial District Troop Advancement - " . $_GET['unit_name'];
?>


<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-lg-5">
      <a class="navbar-brand" href="#!"><?PHP echo $Title ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    </div>
  </nav>

  <?php
  $CTroop->DisplayAdvancmenetDescription();
  $CTroop->DisplayUnitAdvancementTable();

  $var_value = $_GET['unit_name'];
  //printf("\Unit selected %s\t", $var_value);

  if (empty($var_value)) {
    // Should never get here
    exit;
  } else {
    $sql = sprintf('SELECT * FROM adv_troop WHERE Unit LIKE "%s%%"', $_GET['unit_name']);
  }
  if ($result = mysqli_query($CTroop->getDbConn(), $sql)) {
    $rowcount = mysqli_num_rows($result);
  }


  //echo $rowcount;
  if ($rowcount > 0) {
    while ($row = $result->fetch_assoc()) {

      $UnitYouth = $CTroop->GetUnitTotalYouth($row['Unit'], $row['Youth'], $row["Date"]);
      $Rank_Scout = $CTroop->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row['Unit']);

      if ($Rank_Scout == 0) // Make it Bold
        $Formatter = "<b style='color:red;'>";
      else if ($Rank_Scout >= 2.0 && $Rank_Scout < 4.0)
        $Formatter = "<b style='color:orange;'>";
      else if ($Rank_Scout >= 4.0)
        $Formatter = "<b style='color:green;'>";
      else
        $Formatter = "";

      echo "<tr><td>" .
        $Formatter . $row["Scout"] . "</td><td>" .
        $Formatter . $row["Tenderfoot"] . "</td><td>" .
        $Formatter . $row["SecondClass"] . "</td><td>" .
        $Formatter . $row["FirstClass"] . "</td><td>" .
        $Formatter . $row["Star"] . "</td><td>" .
        $Formatter . $row["Life"] . "</td><td>" .
        $Formatter . $row["Eagle"] . "</td><td>" .
        $Formatter . $row["Palms"] . "</td><td>" .
        $Formatter . $row["MeritBadge"] . "</td><td>" .
        $Formatter . $row["YTD"] . "</td><td>" .
        $Formatter . $UnitYouth . "</td><td>" .
        $Formatter . $Rank_Scout . "</td><td>" .
        $Formatter . $row["Date"] . "</td></tr>";
    }
    echo "</table>";
  } else {
    echo "0 result";
  }

  if ($rowcount > 0)
    mysqli_free_result($result);
  ?>
  </table>

  <?php echo "<p style='text-align: center;  padding-bottom: 5rem !important;' class='px-lg-5'>Data last updated: " . $CTroop->GetLastUpdated("adv_troop") . "</p>"; ?>

</body>

</html>