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
  include_once('CMBCollege.php');
$CMBCollege = CMBCollege::getInstance();

if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
  $CollegeYear = $_POST['CollegeYear'];
  $CMBCollege->SetYear($CollegeYear);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include('header.php'); ?>
</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-4 d-print-none">
      <a class="navbar-brand" href="#!">Centennial District Merit Badge College</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="https://mbcollege.centennialdistrict.co/index.php">Home</a></li>
          <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
          <li class="nav-item"><a class="nav-link" href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">Contact</a></li>
          <?php
          if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo '<li class="nav-item"><a class="nav-link" href="./logoff.php">Log off</a></li>';
          } else {
            echo '<li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <!-- Include the common side nav bar -->
      <?php include 'navbar.php'; ?>
      <div class="col-md-6 offset-md-1">
        <?php
        $CMBCollege->DisplaySelectCollegeYear();

        $qryCollegeDetails = "SELECT * FROM college_details WHERE `College`='" . $CMBCollege->GetYear() . "'";
        $ResultCollegeDetails = $CMBCollege->doQuery($qryCollegeDetails);
        $RowCollegeDetails = $ResultCollegeDetails->fetch_assoc();


        $queryCollegeYear = "SELECT DISTINCTROW College FROM college_counselors ORDER BY College DESC";
        $result_CollegeYear = $CMBCollege->doQuery($queryCollegeYear);

        $csv_hdr = "College\t First Name\t Last Name\t Email\t Phone\t Member ID\t Merit Badge\t Period\t Prequisties\t Notes\t Class Size";
        $csv_output = "";

        echo "</br><p>" . $RowCollegeDetails["College"] . " - " . $RowCollegeDetails["Location"] . " - " . $RowCollegeDetails["Address"] . "</br></br></p>";

        ?>



        <div class="flex-container" id="PeriodA" style="width:1170px">
          <?php if ($RowCollegeDetails["PeriodA"] != null) { ?>
            <div>Period A - <?php echo $RowCollegeDetails["PeriodA"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <?php
                  $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "A");
                  while ($row = $result->fetch_assoc()) {
                    $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                    if ($Registered <= 0)
                      $Formatter = "<b style='color:red;'>";
                    else if ($Registered >= $row["MBCSL"])
                      $Formatter = $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr><td>" .
                      $Formatter . $row["MBName"] . "</td><td>" .
                      $Formatter . $row["FirstName"] . " " .
                      $Formatter . $row["LastName"] . "</td><td>" .
                      $Formatter . $row["MBCSL"] . "</td><td>" .
                      $Formatter . $Registered . "</td><td>" .
                      $Formatter . $row["MBRoom"] . "</td></tr>";
                    //Now create for CSV file
                    $csv_output .= $CMBCollege->GetYear() . "\t";
                    $csv_output .= $row["FirstName"] . "\t";
                    $csv_output .= $row["LastName"] . "\t";
                    $csv_output .= $row["Email"] . "\t";
                    $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                    $csv_output .= $row["BSAId"] . "\t";
                    $csv_output .= $row["MBName"] . "\t";
                    $csv_output .= $row["MBPeriod"] . "\t";
                    $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                    $csv_output .= addslashes($row["MBNotes"]) . "\t";
                    $csv_output .= $row["MBCSL"] . "\t";
                    $csv_output .= $row["MBRoom"] . "\t";
                    $csv_output .= $Registered . "\n";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                  ?>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
          <?php if ($RowCollegeDetails["PeriodAB"] != null) { ?>
            <div>Period AB - <?php echo $RowCollegeDetails["PeriodAB"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <!-- Class Room -->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "AB");
                    while ($row = $result->fetch_assoc()) {
                      $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                      if ($Registered <= 0)
                        $Formatter = "<b style='color:red;'>";
                      else if ($Registered >= $row["MBCSL"])
                        $Formatter = $Formatter = "<b style='color:green;'>";
                      else
                        $Formatter = "";

                      echo "<tr><td>" .
                        $Formatter . $row["MBName"] . "</td><td>" .
                        $Formatter . $row["FirstName"] . " " .
                        $Formatter . $row["LastName"] . "</td><td>" .
                        $Formatter . $row["MBCSL"] . "</td><td>" .
                        $Formatter . $Registered . "</td><td>" .
                        $Formatter . $row["MBRoom"] . "</td></tr>";
                      //Now create for CSV file
                      $csv_output .= $CMBCollege->GetYear() . "\t";
                      $csv_output .= $row["FirstName"] . "\t";
                      $csv_output .= $row["LastName"] . "\t";
                      $csv_output .= $row["Email"] . "\t";
                      $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                      $csv_output .= $row["BSAId"] . "\t";
                      $csv_output .= $row["MBName"] . "\t";
                      $csv_output .= $row["MBPeriod"] . "\t";
                      $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                      $csv_output .= addslashes($row["MBNotes"]) . "\t";
                      $csv_output .= $row["MBCSL"] . "\n";
                      $csv_output .= $row["MBRoom"] . "\n";
                      $csv_output .= $Registered . "\n";
                    }
                    echo "</table>";
                    mysqli_free_result($result);
                    ?>
                  </tbody>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
        </div>

        <div class="flex-container" id="PeriodB" style="width:1170px">
          <?php if ($RowCollegeDetails["PeriodB"] != null) { ?>
            <div>Period B - <?php echo $RowCollegeDetails["PeriodB"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <?php
                  $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "B");
                  while ($row = $result->fetch_assoc()) {
                    $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                    if ($Registered <= 0)
                      $Formatter = "<b style='color:red;'>";
                    else if ($Registered >= $row["MBCSL"])
                      $Formatter = $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr><td>" .
                      $Formatter . $row["MBName"] . "</td><td>" .
                      $Formatter . $row["FirstName"] . " " .
                      $Formatter . $row["LastName"] . "</td><td>" .
                      $Formatter . $row["MBCSL"] . "</td><td>" .
                      $Formatter . $Registered . "</td><td>" .
                      $Formatter . $row["MBRoom"] . "</td></tr>";
                    //Now create for CSV file
                    $csv_output .= $CMBCollege->GetYear() . "\t";
                    $csv_output .= $row["FirstName"] . "\t";
                    $csv_output .= $row["LastName"] . "\t";
                    $csv_output .= $row["Email"] . "\t";
                    $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                    $csv_output .= $row["BSAId"] . "\t";
                    $csv_output .= $row["MBName"] . "\t";
                    $csv_output .= $row["MBPeriod"] . "\t";
                    $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                    $csv_output .= addslashes($row["MBNotes"]) . "\t";
                    $csv_output .= $row["MBCSL"] . "\n";
                    $csv_output .= $row["MBRoom"] . "\n";
                    $csv_output .= $Registered . "\n";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                  ?>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
          <?php if ($RowCollegeDetails["PeriodE"] != null) { ?>
            <div>Period E - <?php echo $RowCollegeDetails["PeriodE"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <?php
                  $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "E");
                  while ($row = $result->fetch_assoc()) {
                    $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                    if ($Registered <= 0)
                      $Formatter = "<b style='color:red;'>";
                    else if ($Registered >= $row["MBCSL"])
                      $Formatter = $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr><td>" .
                      $Formatter . $row["MBName"] . "</td><td>" .
                      $Formatter . $row["FirstName"] . " " .
                      $Formatter . $row["LastName"] . "</td><td>" .
                      $Formatter . $row["MBCSL"] . "</td><td>" .
                      $Formatter . $Registered . "</td><td>" .
                      $Formatter . $row["MBRoom"] . "</td></tr>";
                    //Now create for CSV file
                    $csv_output .= $CMBCollege->GetYear() . "\t";
                    $csv_output .= $row["FirstName"] . "\t";
                    $csv_output .= $row["LastName"] . "\t";
                    $csv_output .= $row["Email"] . "\t";
                    $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                    $csv_output .= $row["BSAId"] . "\t";
                    $csv_output .= $row["MBName"] . "\t";
                    $csv_output .= $row["MBPeriod"] . "\t";
                    $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                    $csv_output .= addslashes($row["MBNotes"]) . "\t";
                    $csv_output .= $row["MBCSL"] . "\n";
                    $csv_output .= $row["MBRoom"] . "\n";
                    $csv_output .= $Registered . "\n";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                  ?>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
        </div>

        <p style="page-break-after: always;">&nbsp;</p>


        <div class="flex-container" id="PeriodC" style="width:1170px">
          <?php if ($RowCollegeDetails["PeriodC"] != null) { ?>
            <div style="width:560px">Period C - <?php echo $RowCollegeDetails["PeriodC"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <?php
                  $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "C");
                  while ($row = $result->fetch_assoc()) {
                    $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                    if ($Registered <= 0)
                      $Formatter = "<b style='color:red;'>";
                    else if ($Registered >= $row["MBCSL"])
                      $Formatter = $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr><td>" .
                      $Formatter . $row["MBName"] . "</td><td>" .
                      $Formatter . $row["FirstName"] . " " .
                      $Formatter . $row["LastName"] . "</td><td>" .
                      $Formatter . $row["MBCSL"] . "</td><td>" .
                      $Formatter . $Registered . "</td><td>" .
                      $Formatter . $row["MBRoom"] . "</td></tr>";
                    //Now create for CSV file
                    $csv_output .= $CMBCollege->GetYear() . "\t";
                    $csv_output .= $row["FirstName"] . "\t";
                    $csv_output .= $row["LastName"] . "\t";
                    $csv_output .= $row["Email"] . "\t";
                    $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                    $csv_output .= $row["BSAId"] . "\t";
                    $csv_output .= $row["MBName"] . "\t";
                    $csv_output .= $row["MBPeriod"] . "\t";
                    $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                    $csv_output .= addslashes($row["MBNotes"]) . "\t";
                    $csv_output .= $row["MBCSL"] . "\n";
                    $Registered . "</td><td>" .
                      $row["MBRoom"] . "</td></tr>";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                  ?>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
          <?php if ($RowCollegeDetails["PeriodCD"] != null) { ?>
            <div style="width:560px">Period C-D - <?php echo $RowCollegeDetails["PeriodCD"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <?php
                  $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "CD");
                  while ($row = $result->fetch_assoc()) {
                    $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                    if ($Registered <= 0)
                      $Formatter = "<b style='color:red;'>";
                    else if ($Registered >= $row["MBCSL"])
                      $Formatter = $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr><td>" .
                      $Formatter . $row["MBName"] . "</td><td>" .
                      $Formatter . $row["FirstName"] . " " .
                      $Formatter . $row["LastName"] . "</td><td>" .
                      $Formatter . $row["MBCSL"] . "</td><td>" .
                      $Formatter . $Registered . "</td><td>" .
                      $Formatter . $row["MBRoom"] . "</td></tr>";
                    //Now create for CSV file
                    $csv_output .= $CMBCollege->GetYear() . "\t";
                    $csv_output .= $row["FirstName"] . "\t";
                    $csv_output .= $row["LastName"] . "\t";
                    $csv_output .= $row["Email"] . "\t";
                    $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                    $csv_output .= $row["BSAId"] . "\t";
                    $csv_output .= $row["MBName"] . "\t";
                    $csv_output .= $row["MBPeriod"] . "\t";
                    $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                    $csv_output .= addslashes($row["MBNotes"]) . "\t";
                    $csv_output .= $row["MBCSL"] . "\n";
                    $csv_output .= $row["MBRoom"] . "\n";
                    $csv_output .= $Registered . "\n";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                  ?>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
        </div>
        <div class="flex-container" id="PeriodD" style="width:1170px">
          <?php if ($RowCollegeDetails["PeriodD"] != null) { ?>
            <div style="width:560px">Period D - <?php echo $RowCollegeDetails["PeriodD"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <?php
                  $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "D");
                  while ($row = $result->fetch_assoc()) {
                    $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                    if ($Registered <= 0)
                      $Formatter = "<b style='color:red;'>";
                    else if ($Registered >= $row["MBCSL"])
                      $Formatter = $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr><td>" .
                      $Formatter . $row["MBName"] . "</td><td>" .
                      $Formatter . $row["FirstName"] . " " .
                      $Formatter . $row["LastName"] . "</td><td>" .
                      $Formatter . $row["MBCSL"] . "</td><td>" .
                      $Formatter . $Registered . "</td><td>" .
                      $Formatter . $row["MBRoom"] . "</td></tr>";
                    //Now create for CSV file
                    $csv_output .= $CMBCollege->GetYear() . "\t";
                    $csv_output .= $row["FirstName"] . "\t";
                    $csv_output .= $row["LastName"] . "\t";
                    $csv_output .= $row["Email"] . "\t";
                    $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                    $csv_output .= $row["BSAId"] . "\t";
                    $csv_output .= $row["MBName"] . "\t";
                    $csv_output .= $row["MBPeriod"] . "\t";
                    $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                    $csv_output .= addslashes($row["MBNotes"]) . "\t";
                    $csv_output .= $row["MBCSL"] . "\n";
                    $csv_output .= $row["MBRoom"] . "\n";
                    $csv_output .= $Registered . "\n";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                  ?>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
          <?php if ($RowCollegeDetails["PeriodF"] != null) { ?>
            <div style="width:560px">Period F - <?php echo $RowCollegeDetails["PeriodF"]; ?>
              <table class="table table-light" style="width:650px" class="tl1 tl2 tl3 tc4 tc5">
                <!--<table class="tl1 tl2 tc3 tc4 tc5">-->
                <td style="width:250px">
                  <!-- Merit Badge Name -->
                <td style="width:200px">
                  <!-- Counselor Name -->
                <td style="width:30px">
                  <!-- Class Size -->
                <td style="width:30px">
                  <!-- Scouts Registered -->
                <td style="width:100px">
                  <!-- Class Room -->
                  <thead>
                    <tr>
                      <th>Merit Badge</th>
                      <th>Counselor</th>
                      <th>Size</th>
                      <th>Reg</th>
                      <th>Room</th>
                    </tr>
                  </thead>
                  <?php
                  $result = $CMBCollege->GetMBCollegeClasses($CMBCollege->GetYear(), "F");
                  while ($row = $result->fetch_assoc()) {
                    $Registered = $CMBCollege->GetRegisteredScouts($row["MBName"], $row['MBPeriod']);
                    if ($Registered <= 0)
                      $Formatter = "<b style='color:red;'>";
                    else if ($Registered >= $row["MBCSL"])
                      $Formatter = $Formatter = "<b style='color:green;'>";
                    else
                      $Formatter = "";

                    echo "<tr><td>" .
                      $Formatter . $row["MBName"] . "</td><td>" .
                      $Formatter . $row["FirstName"] . " " .
                      $Formatter . $row["LastName"] . "</td><td>" .
                      $Formatter . $row["MBCSL"] . "</td><td>" .
                      $Formatter . $Registered . "</td><td>" .
                      $Formatter . $row["MBRoom"] . "</td></tr>";
                    //Now create for CSV file
                    $csv_output .= $CMBCollege->GetYear() . "\t";
                    $csv_output .= $row["FirstName"] . "\t";
                    $csv_output .= $row["LastName"] . "\t";
                    $csv_output .= $row["Email"] . "\t";
                    $csv_output .= $CMBCollege->formatPhoneNumber(null, $row["Phone"]) . "\t";
                    $csv_output .= $row["BSAId"] . "\t";
                    $csv_output .= $row["MBName"] . "\t";
                    $csv_output .= $row["MBPeriod"] . "\t";
                    $csv_output .= addslashes($row["MBPrerequisities"]) . "\t";
                    $csv_output .= addslashes($row["MBNotes"]) . "\t";
                    $csv_output .= $row["MBCSL"] . "\n";
                    $csv_output .= $row["MBRoom"] . "\n";
                    $csv_output .= $Registered . "\n";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                  ?>
              </table>
              <p>&nbsp;</p>
            </div>
          <?php } ?>
        </div>


        <br />
        <?php
        // If not adim user do not show export button
        if (isset($_SESSION['Role']) && !strcmp($_SESSION['Role'], "Admin")) {
        ?>
          <center>
            <form name="export" action="export.php" method="post">
              <input class='RoundButton' style="width:220px" type="submit" value="Export table to CSV">
              <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
              <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
            </form>
          </center>
          <br />
          <center />
        <?php
        }
        ?>
      </div>
    </div>
  </div>
  <?php include("Footer.php"); ?>

</body>

</html>