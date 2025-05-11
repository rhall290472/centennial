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
!  #   Copyright 2024 - Richard Hall                                        #  !
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
require_once 'CAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();
$cAwards = cAwards::getInstance();
$rowNominee = NULL;
$AwardIDX;

//#####################################################
//
// Check to see if user as Submitted the form. If so, save the data..
//
//#####################################################
if (isset($_POST['SubmitForm'])) {
  if ($_POST['SubmitForm'] == "Cancel") {
    $cDistrictAwards->GotoURL("./OnLineNomination.php");
    exit();
  }
  $FormData = array();
  $FormData['NomineeIDX'] = -1; // New recorded
  $FormData['FirstName'] =  $cDistrictAwards->GetFormData('element_1_1');
  $FormData['PName'] =  $cDistrictAwards->GetFormData('element_1_2');
  $FormData['MName'] =  $cDistrictAwards->GetFormData('element_1_3');
  $FormData['LastName'] = $cDistrictAwards->GetFormData('element_1_4');
  // If this is for Outstanding Leaders then gather all four names.
  if ($_POST['AwardIDX'] == $cDistrictAwards::$OutStandingLeaders) {
    if (!empty($_POST['element_2_1'])) {
      $FormData['FirstName2'] =  $cDistrictAwards->GetFormData('element_2_1');
      $FormData['PName2'] =  $cDistrictAwards->GetFormData('element_2_2');
      $FormData['MName2'] =  $cDistrictAwards->GetFormData('element_2_3');
      $FormData['LastName2'] = $cDistrictAwards->GetFormData('element_2_4');
    }

    if (!empty($_POST['element_3_1'])) {
      $FormData['FirstName3'] =  $cDistrictAwards->GetFormData('element_3_1');
      $FormData['PName3'] =  $cDistrictAwards->GetFormData('element_3_2');
      $FormData['MName3'] =  $cDistrictAwards->GetFormData('element_3_3');
      $FormData['LastName3'] = $cDistrictAwards->GetFormData('element_3_4');
    }

    if (!empty($_POST['element_4_1'])) {
      $FormData['FirstName4'] =  $cDistrictAwards->GetFormData('element_4_1');
      $FormData['PName4'] =  $cDistrictAwards->GetFormData('element_4_2');
      $FormData['MName4'] =  $cDistrictAwards->GetFormData('element_4_3');
      $FormData['LastName4'] = $cDistrictAwards->GetFormData('element_4_4');
    }
  }

  if(!empty($_POST['element_6_1']))
    $FormData['Position'] =  $cDistrictAwards->GetFormData('element_6_1');
  else
    $FormData['Position'] = "";
  $FormData['Unit'] =  $cDistrictAwards->GetFormData('element_6_2');
  if(!empty($_POST['element_6_3']))
    $FormData['MemberID'] =  $cDistrictAwards->GetFormData('element_6_3');

  // Get Disitrict of Merit data ...
  if ($_POST['AwardIDX'] == $cDistrictAwards::$DistrictAwardofMerit) {
    $FormData['DLAward'] =  $cDistrictAwards->GetFormData('element_7_1');
    $FormData['SRAward'] =  $cDistrictAwards->GetFormData('element_7_2');
    $FormData['STAward'] =  $cDistrictAwards->GetFormData('element_7_3');

    $FormData['CoachAward'] =  $cDistrictAwards->GetFormData('element_8_1');
    $FormData['SilverBeaver'] =  $cDistrictAwards->GetFormData('element_8_2');
    $FormData['ScouterKey'] =  $cDistrictAwards->GetFormData('element_8_3');

    $FormData['CSAward'] =  $cDistrictAwards->GetFormData('element_9_1');
    $FormData['WoodBadge'] =  $cDistrictAwards->GetFormData('element_9_2');
    $FormData['DCSA'] =  $cDistrictAwards->GetFormData('element_9_3');

    $FormData['WDLAward'] =  $cDistrictAwards->GetFormData('element_10_1');
    $FormData['Other1'] =  $cDistrictAwards->GetFormData('element_10_2');
    $FormData['Other2'] =  $cDistrictAwards->GetFormData('element_10_3');
  }

  $FormData['Notes'] =  $cDistrictAwards->GetFormData('element_14_1');

  $FormData['NominatedBy'] =  $cDistrictAwards->GetFormData('element_15_1');
  $FormData['NominatedByUnit'] =  $cDistrictAwards->GetFormData('element_15_2');
  $FormData['NominatedByPosition'] =  $cDistrictAwards->GetFormData('element_15_3');

  $FormData['Year'] =  $cDistrictAwards->GetYear();
  $FormData['Award'] =  $cDistrictAwards->GetFormData('element_16_1');
  $FormData['Status'] =  2;   // Nominated
  $FormData['IsDeleted'] = 0; // No
  $FormData['created_by'] = "On-line form";


  // Go add the new Nominee Data.
  if ($cDistrictAwards->UpdateNomineeRecord($FormData)) {
    // Record has been updated in database now create a audit trail
    $cDistrictAwards->CreateAudit($rowNominee, $FormData, 'NomineeIDX');
  }
  $cDistrictAwards->GotoURL('OnLineNomination.php');
}


//#####################################################
//
// Check to see if user has selected an award
//
//#####################################################
if (isset($_POST['SubmitAward']) && ($_POST['SubmitAward'] == "Select Award")) {
  // User has selected an award. Go display award requirements and then get Nominee data
  if (isset($_POST['AwardIDX']))
    $AwardIDX = $_POST['AwardIDX'];
  else {
    $srtMsg = "Error: $_POST[AwardIDX] is not set from " . __FILE__ . ", " . __LINE__;
    error_log($strMsg);
    exit();
  }
  $htmlMessage = $cAwards->AwardNomination($AwardIDX);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
  <meta name="description" content="NominationPage.php">

</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-5">
      <a class="navbar-brand" href="#!">Centennial District Awards</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="./OnLineNomination.php">Back</a></li>
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
  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <p class="fs-4"><?php echo $htmlMessage; ?></p>
        </div>
        <!-- Page Features-->
        <div class="form-nominee">
          <p style="text-align:Left"><b>Nominee Information</b></p>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" s id="add_nomination" method="post">

            <div class="form-row">
              <div class="col-3">
                <input type="text" name="element_1_1" class="form-control" placeholder="First Name">
              </div>
              <div class="col-3">
                <input type="text" name="element_1_2" class="form-control" placeholder="Preferred Name">
              </div>
              <div class="col">
                <input type="text" name="element_1_3" class="form-control" placeholder="Middle">
              </div>
              <div class="col">
                <input type="text" name="element_1_4" class="form-control" placeholder="Last">
              </div>
            </div>

            <?php if ($_POST['AwardIDX'] == $cAwards::$OutStandingLeaders) { ?>
              <div class="form-row">
                <div class="col-3">
                  <input type="text" name="element_2_1" class="form-control" placeholder="First Name">
                </div>
                <div class="col-3">
                  <input type="text" name="element_2_2" class="form-control" placeholder="Preferred Name">
                </div>
                <div class="col">
                  <input type="text" name="element_2_3" class="form-control" placeholder="Middle">
                </div>
                <div class="col">
                  <input type="text" name="element_2_4" class="form-control" placeholder="Last">
                </div>
              </div>

              <div class="form-row">
                <div class="col-3">
                  <input type="text" name="element_3_1" class="form-control" placeholder="First Name">
                </div>
                <div class="col-3">
                  <input type="text" name="element_3_2" class="form-control" placeholder="Preferred Name">
                </div>
                <div class="col">
                  <input type="text" name="element_3_3" class="form-control" placeholder="Middle">
                </div>
                <div class="col">
                  <input type="text" name="element_3_4" class="form-control" placeholder="Last">
                </div>
              </div>

              <div class="form-row">
                <div class="col-3">
                  <input type="text" name="element_4_1" class="form-control" placeholder="First Name">
                </div>
                <div class="col-3">
                  <input type="text" name="element_4_2" class="form-control" placeholder="Preferred Name">
                </div>
                <div class="col">
                  <input type="text" name="element_4_3" class="form-control" placeholder="Middle">
                </div>
                <div class="col">
                  <input type="text" name="element_4_4" class="form-control" placeholder="Last">
                </div>
              </div>

            <?php } ?>


            <div class="form-row">
              <?php if ($_POST['AwardIDX'] != $cAwards::$OutStandingLeaders) { ?>
                <div class="col-3">
                  <?php
                  $cDistrictAwards->GetScoutingPosition('element_6_1', null);
                  ?>
                  <!-- <input type="text" name="element_6_1" class="form-control" placeholder="Currently registered in Scouting as:"> -->
                </div>
              <?php } ?>
              <div class="col-4">
                <?php
                // Make Unit selection a dropdown of active units in the District.
                $cDistrictAwards->GetDistrictUnits('element_6_2', null);
                ?>
                <!-- <input type="text" name="element_6_2" class="form-control" placeholder="Unit Type & Number i.e Troop 0317-BT"> -->
              </div>
              <?php if ($_POST['AwardIDX'] != $cAwards::$OutStandingLeaders) { ?>
                <div class="col-3">
                  <input type="text" name="element_6_3" class="form-control" placeholder="BSA ID if know">
                </div>
              <?php } ?>
            </div>

            <?php if ($_POST['AwardIDX'] == $cAwards::$DistrictAwardofMerit) { ?>
              <p style="text-align:Left"><b>If the nominee has earned the following (please provide dates):</b></p>
              <div class="form-row">
                <div class="col-4">
                  <input type="text" name="element_7_1" class="form-control" placeholder="Den Leader’s Training Award or Den Leader Award">
                </div>
                <div class="col-4">
                  <input type="text" name="element_7_2" class="form-control" placeholder="Scouter’s Religious Award:">
                </div>
                <div class="col-4">
                  <input type="text" name="element_7_3" class="form-control" placeholder="Scouter’s Training Award">
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <input type="text" name="element_8_1" class="form-control" placeholder="Den Leader Coach’s Training Award/Coach Award">
                </div>
                <div class="col-4">
                  <input type="text" name="element_8_2" class="form-control" placeholder="Silver Beaver">
                </div>
                <div class="col-4">
                  <input type="text" name="element_8_3" class="form-control" placeholder="Scouter’s Key">
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <input type="text" name="element_9_1" class="form-control" placeholder="Cubmaster Award">
                </div>
                <div class="col-4">
                  <input type="text" name="element_9_2" class="form-control" placeholder="Order of the Arrow">
                </div>
                <div class="col-4">
                  <input type="text" name="element_9_3" class="form-control" placeholder="Venturing Awards">
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <input type="text" name="element_10_1" class="form-control" placeholder="Cub Scouter Award">
                </div>
                <div class="col-4">
                  <input type="text" name="element_10_2" class="form-control" placeholder="Wood Badge">
                </div>
                <div class="col-4">
                  <input type="text" name="element_10_3" class="form-control" placeholder="Distinguished Commissioner Service Award">
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <input type="text" name="element_11_1" class="form-control" placeholder="Webelos Den Leader Award">
                </div>
                <div class="col-4">
                  <input type="text" name="element_11_2" class="form-control" placeholder="Other (specify)">
                </div>
                <div class="col-4">
                  <input type="text" name="element_11_3" class="form-control" placeholder="Other (specify)">
                </div>
              </div>

              <div class="form-row">
              </div>
            <?php } ?>

            </br>
            <p style="text-align:Left"><b>The noteworthy service upon which this nomination is based on:</b></p>
            <p>(Furnish as much information as possible. For example: president, Rotary Club; vestryman, St. Paul’s Church; chairman, Red
              Cross campaign; vice-president, PTA; medical director, hospital; Cubmaster, 3 years; Scoutmaster, 4 years; Venturing Advisor,
              3 years; commissioner, etc.)</p>

            <div class="form-row">
              <div class="col">
                <textarea name="element_14_1" class="form-control" id="Notes" rows="10" placeholder="Notes"></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="col-5">
                <input type="text" name="element_15_1" class="form-control" placeholder="Your Name">
              </div>
              <div class="col-3">
                <?php
                // Make Unit selection a dropdown of active units in the District.
                $cDistrictAwards->GetDistrictUnits('element_15_2', null);
                ?>
                <!-- <input type="text" name="element_15_2" class="form-control" placeholder="Unit Type & Number"> -->
              </div>
              <div class="col-3">
                <?php
                $cDistrictAwards->GetScoutingPosition('element_15_3', null);
                ?>
                <!-- <input type="text" name="element_15_3" class="form-control" placeholder="Your Scouting Position"> -->
              </div>
            </div>

            <!-- save the Award IDX here is a hidden control -->
            <div class="form-row">
              <div class="col-3">
                <input type="hidden" name="element_16_1" class="form-control" value=<?php echo $AwardIDX; ?>>
              </div>
            </div>


            <?php
            $ID = -1;   // New record
            echo '<input type="hidden" name="AwardIDX" value="' . $AwardIDX . '"/>';
            ?>
            <input id="saveForm2" class="btn btn-primary btn-lg" type="submit" name="SubmitForm" value="Save" />
            <input id="saveForm2" class="btn btn-primary btn-lg" type="submit" name="SubmitForm" value="Cancel" />

          </form>
        </div>
      </div>
    </div>
  </header>
  <?php include("Footer.php"); ?>
</body>

</html>