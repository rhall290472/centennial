<?php
if (!session_id()) {
  session_start();
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

require_once 'CDistrictAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();


// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $cDistrictAwards->GotoURL("index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
  <meta name="description" content="ReportBallot.php">
  <style>
    * {
      box-sizing: border-box;
    }

    .row {
      margin-left: -5px;
      margin-right: -5px;
    }

    .column {
      float: left;
      width: 50%;
      padding: 5px;
    }

    /* Clearfix (clear floats) */
    .row::after {
      content: "";
      clear: both;
      display: table;
    }

    table {
      border-collapse: collapse;
      border-spacing: 0;
      width: 100%;
      border: 1px solid #ddd;
    }

    th,
    td {
      text-align: left;
      padding: 16px;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    /* Responsive layout - makes the two columns stack on top of each other instead of next to each other on screens that are smaller than 600 px */
    @media screen and (max-width: 600px) {
      .column {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-5">
      <a class="navbar-brand" href="#!">Centennial District Awards</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="./Reports.php">Back</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
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

  <?php
  $csv_hdr = "Unit Type,Unit#,  Gender, Name, Year, Beneficiary, Project Name, Project Hours";
  $csv_output = "";

  if (isset($_POST['SubmitYear'])) {
    $year = $_POST['Year'];
    $cDistrictAwards->SetYear($year);
    //header('Refresh: ' . 1);
  }
  // Dispay the Year Select dropdown selection
  $cDistrictAwards->SelectYear();
  ?>

  <center>
    <h4><?php echo "Nominees" ?> </h4>
    <div class="row" >
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">District Award of Merit</h4>
        <?php $cDistrictAwards->GetDistrictNominees('1'); ?>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Friends of Scouting</h4>
        <?php $cDistrictAwards->GetDistrictNominees('17'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Scoutmaster of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('2'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Scoutmaster of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('3'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Cubmaster of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('4'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Cubmaster of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('5'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Crew Advisor of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('6'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Crew Advisor of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('7'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Pack Committee Chair of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('31'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Pack Committee Chair of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('34'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Pack Committee Member of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('20'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Pack Committee Member of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('22'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Pack Den Leader of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('13'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Pack Den Leader of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('7'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Troop Committee Chair of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('35'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Troop Committee Chair of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('39'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Troop Committee Member of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('21'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Troop Committee Member of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('23'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Commissioner Member of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('18'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Commissionerof the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('19'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Unit Commissioner Member of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('27'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Rookie Unit Commissionerof the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('38'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Bald Eagle Award</h4>
        <?php $cDistrictAwards->GetDistrictNominees('16'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Junior Leader of the Year</h4>
        <?php $cDistrictAwards->GetDistrictNominees('29'); ?>
      </div>
    </div>

    <div class="row">
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Outstanding Leaders</h4>
        <?php $cDistrictAwards->GetDistrictNominees('14'); ?>
        </table>
      </div>
      <div class="column">
        <h4 style="background-color: var(--scouting-lighttan);">Key Scouter</h4>
        <?php $cDistrictAwards->GetDistrictNominees('15'); ?>
      </div>
    </div>

    <center>
      <form name="export" action="../export.php" method="post" style="padding: 20px;">
        <input class='btn btn-primary btn-sm d-print-none' style="width:220px" type="submit" value="Export table to CSV">
        <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
        <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
      </form>
    </center>

    <?php include("Footer.php"); ?>
</body>

</html>