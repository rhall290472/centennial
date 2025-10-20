<?php
load_class(BASE_PATH . '/src/Classes/CCounselor.php');
$Counselor = cCounselor::getInstance();
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
$CMBCollege = CMBCollege::getInstance();
?>

<html>
<body>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-9">

        <?php
        // Ensure that the college is open for the Counselors to sign up. This is done with use of the
        // Open variable Open in the college_details table. Setting Open to a 1 allows counselors to
        // sign up for the college.
        if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
          if (!$Counselor->RegistrationOpen()) { ?>
            <div style="display: flex; justify-content: center;">
              <img src="./images/RegistrationClosed.jpg" alt="Registration Closed" class="center" height="250" width="270" />
            </div>
        <?php
            exit();
          }
        }
        ?>

        <div> The College will be setup in periods of 2 hours and 3 or 4 hours length, please select the length you need for your badges.</br></br> </div>
        <?php
        echo "<table style='width:550';>";
        echo "<tr>";
        //		echo "<th>2 Hour Period(s)</th>";
        echo "<th>2 Hour Period(s)</th>";
        echo "<th>3 Hour Period(s)</th>";
        echo "<th>4 Hour Period(s)</th>";
        echo "</tr>";

        // First Row of classes
        echo "<tr>";
        if ($Counselor->GetPeriodATime($Counselor->getyear()) != null)
          echo "<td>" . "A - " . $Counselor->GetPeriodATime($Counselor->getyear()) . "</td>";
          if ($Counselor->GetPeriodETime($Counselor->getyear()) != null)
          echo "<td>" . "E - " . $Counselor->GetPeriodETime($Counselor->getyear()) . "</td>";
        if ($Counselor->GetPeriodABTime($Counselor->getyear()) != null)
          echo "<td>" . "AB - " . $Counselor->GetPeriodABTime($Counselor->getyear()) . "</td>";
        echo "</tr>";

        // Second Row of classes
        echo "<tr>";
        if ($Counselor->GetPeriodBTime($Counselor->getyear()) != null)
          echo "<td>" . "B - " . $Counselor->GetPeriodBTime($Counselor->getyear()) . "</td>";
          if ($Counselor->GetPeriodFTime($Counselor->getyear()) != null)
          echo "<td>" . "F - " . $Counselor->GetPeriodFTime($Counselor->getyear()) . "</td>";
        if($Counselor->GetPeriodCDTime($Counselor->getyear()) != null)
        	echo "<td>"."CD - " . $Counselor->GetPeriodCDTime($Counselor->getyear())."</td>";
        echo "</tr>";

        // Third Row of classes
        echo "<tr>";
        if ($Counselor->GetPeriodCTime($Counselor->getyear()) != null)
          echo "<td>" . "C - " . $Counselor->GetPeriodCTime($Counselor->getyear()) . "</td>";
        echo "</tr>";

        // Fourth Row of classes
        echo "<tr>";
        if ($Counselor->GetPeriodDTime($Counselor->getyear()) != null)
          echo "<td>" . "D - " . $Counselor->GetPeriodDTime($Counselor->getyear()) . "</td>";
        echo "<td></td>";
        echo "</tr>";
        echo "</table>";
        ?>
        </br>
        <?php
        // If we are having lunch, list times. 
        if ($Counselor->GetLunchTime($Counselor->getyear()) != null)
          echo "Lunch with be served from " . $Counselor->GetLunchTime($Counselor->getyear()) . "</br>";
        ?>
        <hr />
        <div>
          <p>If you desire to limit the number of scouts in your class please enter that value or the default will be 15
            scouts.</p>
          <p>If scouts need to complete anything before the class please enter that into the Prerequisities field.</p>
          <p>If you have a charge for materials for your merit badge class please include it in the class fee.</p>
          <p>If your name is not in the Counselor list or you are missing a Merit Badge or would like to offer a NOVA
            class OR you would like to edit your merit badges that you have
            already signed up for, please click here:
            <a href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">Contact</a>
          </p>
          <?php
          //*************************************************************************/
          //
          // 1. Get a list of approved merit badge counselors and display it to the user
          //
          //*************************************************************************/
          $querySelectedCounselor1 = "SELECT DISTINCTROW mbccounselors.LastName, mbccounselors.FirstName, mbccounselors.MemberID FROM mbccounselors
        WHERE mbccounselors.Active='Yes' AND mbccounselors.Active='Yes' AND mbccounselors.Is_a_no='0' ORDER BY mbccounselors.LastName, mbccounselors.FirstName";

          $result_ByCounselor = $Counselor->doQuery($querySelectedCounselor1);
          if (!$result_ByCounselor) {
            $Counselor->function_alert("ERROR: MeritQuery($querySelectedCounselor1)");
            exit;
          }
          ?>

          <form method=post>
            <div class="form-row px-1 d-print-none">
              <div class="col-3">
                <label for='UnitName'>Choose a Counselor: </label>
                <select class='form-select' id='CounselorName' name='CounselorName'>
                  <option value=""> </option>
                    <?php while ($rowCerts = $result_ByCounselor->fetch_assoc()) {
                      echo "<option value=" . $rowCerts['MemberID'] . ">" . $rowCerts['LastName'] . " " . $rowCerts['FirstName'] . "</option>";
                    } ?>
                </select>
              </div>
              <div class="col-3 py-4">
                <input class='btn btn-primary btn-sm' type='submit' name='SubmitCounselor' value='Select Counselor' />
              </div>
            </div>
          </form>

          <?php

          //#####################################################
          //
          // Wait for user to select a Counselor, once selected we
          // will go pull all of the merit badges that this counselor 
          // is approved for and allow them to sign up only for these
          // badges.
          //
          //#####################################################
          if (isset($_POST['SubmitCounselor']) && isset($_POST['CounselorName']) && $_POST['CounselorName'] !== '') {
            $SelectedCounselor = $_POST['CounselorName']; // Get name of Counselor selected

            $queryCounselors = "SELECT * FROM mbccounselors INNER JOIN(meritbadges INNER JOIN mbccounselormerit ON meritbadges.MeritName = mbccounselormerit.MeritName)
			      ON (mbccounselors.FirstName = mbccounselormerit.FirstName) AND (mbccounselors.LastName = mbccounselormerit.LastName ) 
			      WHERE mbccounselors.MemberID LIKE ";
            // Only display list of Merit Badges that Counselor is signed up for.
            $querySelectedCounselor2 = "SELECT * FROM mbccounselors INNER JOIN(meritbadges INNER JOIN mbccounselormerit ON meritbadges.MeritName = mbccounselormerit.MeritName)
		        ON (mbccounselors.LastName = mbccounselormerit.LastName) AND (mbccounselors.FirstName = mbccounselormerit.FirstName)
		        WHERE
			      mbccounselors.MemberID LIKE";

            $sqlMB = sprintf("%s '%s' ORDER BY meritbadges.MeritName ASC", $querySelectedCounselor2, $SelectedCounselor);
            if (!$ResultsMB = $Counselor->doQuery($sqlMB)) {
              $msg = "Error: MeritQuery()";
              $Counselor->function_alert($msg);
            }

            //*********************************************************************/
            //
            // Now check to see if they have already signed up once, if so allow them
            // to edit their signup.
            //
            //*********************************************************************/
            //wCreate a sql statement to select chosen Counselor
            $sql = sprintf("%s '%s' ORDER BY meritbadges.MeritName ASC", $queryCounselors, $SelectedCounselor);

            if (!$Results = $Counselor->doQuery($sql)) {
              $msg = "Error: MeritQuery()";
              $Counselor->function_alert($msg);
            }
            $row = $Results->fetch_assoc();

            $Counselor->IsSignedUp($Counselor->getYear(), $row['LastName'], $row['FirstName']);

          ?>
            <!-- Display the sign up form to the user, if the counselor is editing their signup, display the 
         Data they lasted entered. -->
            <p style="text-align:Left"><b>Counselor Signup Information</b></p>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="add_nomination" method="post">
              <!-- Counselor Information -->
              <div class="form-row">
                <div class="col-3">
                  <label class="description" for="element_1_1">First Name </label>
                  <input type="text" name="element_1_1" class="form-control" placeholder="First Name" value=<?php echo $row['FirstName']; ?>>
                </div>
                <div class="col">
                  <label class="description" for="element_1_2">Last Name </label>
                  <input type="text" name="element_1_2" class="form-control" placeholder="Last" value=<?php echo $row['LastName']; ?>>
                </div>
                <div class="col">
                  <label class="description" for="element_1_3">Email </label>
                  <input type="text" name="element_1_3" class="form-control" placeholder="Email" type="email" value=<?php echo $row['Email']; ?>>
                </div>
                <div class="col">
                  <label class="description" for="element_1_4">Best Phone # </label>
                  <input type="text" name="element_1_4" class="form-control" placeholder="Phone" type="number" value=<?php echo $row['HomePhone']; ?>>
                </div>
                <div class="col">
                  <label class="description" for="element_1_5">BSA Member ID </label>
                  <input type="text" name="element_1_5" class="form-control" placeholder="Member ID" type="number" value=<?php echo $row['MemberID']; ?>>
                </div>
              </div>
              <!-- First Merit Badge Information -->
              <hr />
              <div class="form-row" style="background-color: var(--scouting-tan);">
                <div class="col-2">
                  <label class="description" for="MB1Name">Merit Badge Name </label>
                  <select class='form-select col-sm-10' id='MB1Name' name='MB1Name'>
                    <?php
                    mysqli_data_seek($ResultsMB, 0);
                    echo "<option value=\"\" </option>"; //First selection is blank
                    // Here we will display a list of all merit badges the counselor is approved to
                    // teach and if they are editing they inputs selected the previous entered badge.
                    $FirstBadgeFound = false;
                    while ($rowCerts = $ResultsMB->fetch_assoc()) {
                      // First check to see if the counselor has signed up for this badge.
                      if ($Counselor->MB_Match($rowCerts['MeritName'], 1) && !$FirstBadgeFound) {
                        $FirstBadgeFound = true;
                        echo "<option selected value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                      } else
                        echo "<option value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                    }
                    ?>
                  </select>
                  <label>First Merit Badge</label>
                </div>
                <div class="col-2">
                  <label class="description" for="MB1Period">Period </label>
                  <select class='form-select' id='MB1Period' name='MB1Period'>
                    <option value=""> </option>
                    <?php $Counselor->DisplayPeriods(1); ?>
                  </select>
                  <label>Time Slot</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB1CSL">Class Size </label>
                  <?php $Counselor->Display_ClassSize("MB1CSL", 1); ?>
                  <label>Limit Size</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB1Fee">Class Fee </label>
                  <?php $Counselor->Display_ClassFee("MB1Fee", 1); ?>
                  <label>Fee for MB</label>
                </div>
                  <?php if ((isset($_SESSION["loggedin"]) && $_SESSION["Role"] === "Admin")) { ?>
                    <div class="col-1">
                    <label class='description' for='MB1Room'>Room</label>
                    <?php $Counselor->Display_ClassRoom("MB1Room", 1); ?>
                    <label>Room for MB</label>
                    </div>
                  <?php  }   ?>
                <div class="col">
                  <label class="description" for="MB1Prerequisities">Prerequisities </label>
                  <div>
                    <?php $Counselor->Display_Prerequisities("MB1Prerequisities", 1); ?>
                  </div>
                </div>
                <div class="col">
                  <label class="description" for="MB1Notes">Notes </label>
                  <div>
                    <?php $Counselor->Display_Notes("MB1Notes", 1); ?>
                  </div>
                </div>
              </div>
              <!-- Second Merit Badge Information -->
              <hr />
              <div class="form-row" style="background-color: var(--scouting-darktan);">
                <div class="col-2">
                  <label class="form_description" for="MB2Name">Merit Badge Name</label>
                  <select class='form-select' id='MB2Name' name='MB2Name'>
                    <?php
                    mysqli_data_seek($ResultsMB, 0);
                    echo "<option value=\"\" </option>"; //First selection is blank
                    // Here we will display a list of all merit badges the counselor is approved to
                    // teach and if they are editing they inputs selected the previous entered badge.
                    $FirstBadgeFound = false;
                    while ($rowCerts = $ResultsMB->fetch_assoc()) {
                      // First check to see if the counselor has signed up for this badge.
                      if ($Counselor->MB_Match($rowCerts['MeritName'], 2) && !$FirstBadgeFound) {
                        $FirstBadgeFound = true;
                        echo "<option selected value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                      } else
                        echo "<option value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                    }
                    ?>
                  </select>
                  <label>Second Merit Badge</label>
                </div>
                <div class="col-2">
                  <label class="description" for="MB2Period">Period </label>
                  <select class='form-select' id='MB2Period' name='MB2Period'>
                    <option value=""> </option>
                    <?php $Counselor->DisplayPeriods(2); ?>
                  </select>
                  <label>Time Slot</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB2CSL">Class Size </label>
                  <?php $Counselor->Display_ClassSize("MB2CSL", 2); ?>
                  <label>Limit Size</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB2Fee">Class Fee </label>
                  <?php $Counselor->Display_ClassFee("MB2Fee", 2); ?>
                  <label>Fee for MB</label>
                </div>
                <?php if ((isset($_SESSION["loggedin"]) && $_SESSION["Role"] === "Admin")) { ?>
                    <div class="col-1">
                    <label class='description' for='MB2Room'>Room</label>
                    <?php $Counselor->Display_ClassRoom("MB2Room", 2); ?>
                    <label>Room for MB</label>
                    </div>
                  <?php  }   ?>
                <div class="col">
                  <label class="description" for="MB2Prerequisities">Prerequisites </label>
                  <div>
                    <?php $Counselor->Display_Prerequisities("MB2Prerequisities", 2); ?>
                  </div>
                </div>
                <div class="col">
                  <label class="description" for="MB2Notes">Notes </label>
                  <div>
                    <?php $Counselor->Display_Notes("MB2Notes", 2); ?>
                  </div>
                </div>
              </div>
              <!-- Third Merit Badge Information -->
              <hr />
              <div class="form-row" style="background-color: var(--scouting-tan);">
                <div class="col-2">
                  <label class="description" for="MB3Name">Merit Badge Name </label>
                  <select class='form-select' id='MB3Name' name='MB3Name'>
                    <?php
                    mysqli_data_seek($ResultsMB, 0);
                    echo "<option value=\"\" </option>"; //First selection is blank
                    // Here we will display a list of all merit badges the counselor is approved to
                    // teach and if they are editing they inputs selected the previous entered badge.
                    $FirstBadgeFound = false;
                    while ($rowCerts = $ResultsMB->fetch_assoc()) {
                      // First check to see if the counselor has signed up for this badge.
                      if ($Counselor->MB_Match($rowCerts['MeritName'], 3) && !$FirstBadgeFound) {
                        $FirstBadgeFound = true;
                        echo "<option selected value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                      } else
                        echo "<option value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                    }
                    ?>
                  </select>
                  <label>Third Merit Badge</label>
                </div>
                <div class="col-2">
                  <label class="description" for="MB3Period">Period </label>
                  <select class='form-select' id='MB3Period' name='MB3Period'>
                    <option value=""> </option>
                    <?php $Counselor->DisplayPeriods(3); ?>
                  </select>
                  <label>Time Slot</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB3CSL">Class Size </label>
                  <?php $Counselor->Display_ClassSize("MB3CSL", 3); ?>
                  <label>Limit Size</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB3Fee">Class Fee </label>
                  <?php $Counselor->Display_ClassFee("MB3Fee", 3); ?>
                  <label>Fee for MB</label>
                </div>
                <?php if ((isset($_SESSION["loggedin"]) && $_SESSION["Role"] === "Admin")) { ?>
                    <div class="col-1">
                    <label class='description' for='MB3Room'>Room</label>
                    <?php $Counselor->Display_ClassRoom("MB3Room", 3); ?>
                    <label>Room for MB</label>
                    </div>
                  <?php  }   ?>
                <div class="col">
                  <label class="description" for="MB3Prerequisities">Prerequisites </label>
                  <div>
                    <?php $Counselor->Display_Prerequisities("MB3Prerequisities", 3); ?>
                  </div>
                </div>
                <div class="col">
                  <label class="description" for="MB3Notes">Notes </label>
                  <div>
                    <?php $Counselor->Display_Notes("MB3Notes", 3); ?>
                  </div>
                </div>
              </div>
              <!-- Fourth Merit Badge Information -->
              <hr />
              <div class="form-row" style="background-color: var(--scouting-darktan);">
                <div class="col-2">
                  <label class="description" for="MB4Name">Merit Badge Name </label>
                  <select class='form-select' id='MB4Name' name='MB4Name'>
                    <?php
                    mysqli_data_seek($ResultsMB, 0);
                    echo "<option value=\"\" </option>"; //First selection is blank
                    // Here we will display a list of all merit badges the counselor is approved to
                    // teach and if they are editing they inputs selected the previous entered badge.
                    $FirstBadgeFound = false;
                    while ($rowCerts = $ResultsMB->fetch_assoc()) {
                      // First check to see if the counselor has signed up for this badge.
                      if ($Counselor->MB_Match($rowCerts['MeritName'], 4) && !$FirstBadgeFound) {
                        $FirstBadgeFound = true;
                        echo "<option selected value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                      } else
                        echo "<option value= '" . $rowCerts['MeritName'] . "'>" . $rowCerts['MeritName'] . "</option>";
                    }
                    ?>
                  </select>
                  <label>Fourth Merit Badge</label>
                </div>
                <div class="col-2">
                  <label class="description" for="MB4Period">Period </label>
                  <select class='form-select' id='MB4Period' name='MB4Period'>
                    <option value=""> </option>
                    <?php $Counselor->DisplayPeriods(4); ?>
                  </select>
                  <label>Time Slot</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB4CSL">Class Size </label>
                  <?php $Counselor->Display_ClassSize("MB4CSL", 4); ?>
                  <label>Limit Size</label>
                </div>
                <div class="col-1">
                  <label class="description" for="MB4Fee">Class Fee </label>
                  <?php $Counselor->Display_ClassFee("MB4Fee", 4); ?>
                  <label>Fee for MB</label>
                </div>
                <?php if ((isset($_SESSION["loggedin"]) && $_SESSION["Role"] === "Admin")) { ?>
                    <div class="col-1">
                    <label class='description' for='MB4Room'>Room</label>
                    <?php $Counselor->Display_ClassRoom("MB4Room", 4); ?>
                    <label>Room for MB</label>
                    </div>
                  <?php  }   ?>
                <div class="col">
                  <label class="description" for="MB4Prerequisities">Prerequisites </label>
                  <div>
                    <?php $Counselor->Display_Prerequisities("MB4Prerequisities", 4); ?>
                  </div>
                </div>
                <div class="col">
                  <label class="description" for="MB4Notes">Notes </label>
                  <div>
                    <?php $Counselor->Display_Notes("MB4Notes", 4); ?>
                  </div>
                </div>
              </div>
              <hr />
              <div class="form-row" style="text-align: center;">
                <div class="col">
                  <input type="hidden" name="form_id" value="22772" />
                  <input id="saveForm" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="SubmitForm" />
                </div>
              </div>
            </form>
        </div>
      <?php
          }
      ?>

      </div>
    </div>


    <?php
    //#####################################################
    //
    // This if statement will get executed when the user depress the
    // submit button on this page.
    //
    // Check to see if user as Submitted the form.
    //
    //#####################################################
    if (isset($_POST['SubmitForm'])) {

      $ErrorFlag = false;
      // Check to ensure user selected Periods.
      if (isset($_POST['MB1Name']) && strlen($_POST['MB1Name']) > 0) {
        $MBPeriod = $Counselor->GetFormData('MB1Period');
        if ($MBPeriod == null) {
          $Counselor->function_alert("ERROR: A period must be selected for Merit Badge 1");
          $ErrorFlag = true;
        }
      } else if (isset($_POST['MB2Name']) && strlen($_POST['MB2Name']) > 0) {
        $MBPeriod = $Counselor->GetFormData('MB2Period');
        if ($MBPeriod == null) {
          $Counselor->function_alert("ERROR: A period must be selected for Merit Badge 2");
          $ErrorFlag = true;
        }
      } else if (isset($_POST['MB3Name']) && strlen($_POST['MB3Name']) > 0) {
        $MBPeriod = $Counselor->GetFormData('MB3Period');
        if ($MBPeriod == null) {
          $Counselor->function_alert("ERROR: A period must be selected for Merit Badge 3");
          $ErrorFlag = true;
        }
      } else if (isset($_POST['MB4Name']) && strlen($_POST['MB4Name']) > 0) {
        $MBPeriod = $Counselor->GetFormData('MB4Period');
        if ($MBPeriod == null) {
          $Counselor->function_alert("ERROR: A period must be selected for Merit Badge 4");
          $ErrorFlag = true;
        }
      }

      // No errros detected, go enter the counselor into the database.
      if (!$ErrorFlag) {
        // Common data for each Merit Badge
        $FirstName = $Counselor->GetFormData('element_1_1');
        $LastName = $Counselor->GetFormData('element_1_2');
        $Email = $Counselor->GetFormData('element_1_3');
        $Phone = $Counselor->GetFormData('element_1_4');
        $BSAId = $Counselor->GetFormData('element_1_5');
        $MBCollegeName = $Counselor->getYear();
        $MBName = '';
        $MBPeriod = '';
        $MBClassSize = '';
        $MBPrerequisities = '';
        $MBNotes = '';

        // If the user is editing the data we first need to delete the old data
        //$Counselor = new cCounselor;
        if ($Counselor->IsSignedUp($MBCollegeName, $LastName, $FirstName))
          $Counselor->Delete($MBCollegeName);
        $Counselor->AddInfo($FirstName, $LastName, $Email, $Phone, $BSAId, $MBCollegeName);

        //Check to see if first Merit Badge is selected.
        if (isset($_POST['MB1Name']) && strlen($_POST['MB1Name']) > 0) {
          $MBName = $Counselor->GetFormData('MB1Name');
          $MBPeriod = $Counselor->GetFormData('MB1Period');
          $MBClassSize = $Counselor->GetFormData('MB1CSL');
          $MBFee = $Counselor->GetFormData('MB1Fee');
          $MBRoom =  $Counselor->GetFormData('MB1Room');
          $MBPrerequisities = $Counselor->GetFormData('MB1Prerequisities');
          $MBPrerequisities = addslashes($MBPrerequisities);
          $MBPrerequisities = $Counselor->RemoveNewLine($MBPrerequisities);
          $MBNotes = $Counselor->GetFormData('MB1Notes');
          $MBNotes = addslashes($MBNotes);
          $MBNotes = $Counselor->RemoveNewLine($MBNotes);

          $Counselor->AddMBClass($MBName, $MBPeriod, $MBClassSize, $MBFee, $MBRoom, $MBPrerequisities, $MBNotes);
        }

        //Check to see if second Merit Badge is selected.
        if (isset($_POST['MB2Name']) && strlen($_POST['MB2Name']) > 0) {
          $MBName = $Counselor->GetFormData('MB2Name');
          $MBPeriod = $Counselor->GetFormData('MB2Period');
          $MBClassSize = $Counselor->GetFormData('MB2CSL');
          $MBFee = $Counselor->GetFormData('MB2Fee');
          $MBRoom =  $Counselor->GetFormData('MB2Room');
          $MBPrerequisities = $Counselor->GetFormData('MB2Prerequisities');
          $MBPrerequisities = addslashes($MBPrerequisities);
          $MBPrerequisities = $Counselor->RemoveNewLine($MBPrerequisities);
          $MBNotes = $Counselor->GetFormData('MB2Notes');
          $MBNotes = addslashes($MBNotes);
          $MBNotes = $Counselor->RemoveNewLine($MBNotes);

          $Counselor->AddMBClass($MBName, $MBPeriod, $MBClassSize, $MBFee, $MBRoom, $MBPrerequisities, $MBNotes);
        }

        //Check to see if third Merit Badge is selected.
        if (isset($_POST['MB3Name']) && strlen($_POST['MB3Name']) > 0) {
          $MBName = $Counselor->GetFormData('MB3Name');
          $MBPeriod = $Counselor->GetFormData('MB3Period');
          $MBClassSize = $Counselor->GetFormData('MB3CSL');
          $MBFee = $Counselor->GetFormData('MB3Fee');
          $MBRoom =  $Counselor->GetFormData('MB3Room');
          $MBPrerequisities = $Counselor->GetFormData('MB3Prerequisities');
          $MBPrerequisities = addslashes($MBPrerequisities);
          $MBPrerequisities = $Counselor->RemoveNewLine($MBPrerequisities);
          $MBNotes = $Counselor->GetFormData('MB3Notes');
          $MBNotes = addslashes($MBNotes);
          $MBNotes = $Counselor->RemoveNewLine($MBNotes);

          $Counselor->AddMBClass($MBName, $MBPeriod, $MBClassSize, $MBFee, $MBRoom, $MBPrerequisities, $MBNotes);
        }

        //Check to see if fourth Merit Badge is selected.
        if (isset($_POST['MB4Name']) && strlen($_POST['MB4Name']) > 0) {
          $MBName = $Counselor->GetFormData('MB4Name');
          $MBPeriod = $Counselor->GetFormData('MB4Period');
          $MBClassSize = $Counselor->GetFormData('MB4CSL');
          $MBFee = $Counselor->GetFormData('MB4Fee');
          $MBRoom =  $Counselor->GetFormData('MB4Room');
          $MBPrerequisities = $Counselor->RemoveNewLine($MBPrerequisities);
          $MBPrerequisities = addslashes($MBPrerequisities);
          $MBPrerequisities = $Counselor->GetFormData('MB4Prerequisities');
          $MBNotes = $Counselor->GetFormData('MB4Notes');
          $MBNotes = addslashes($MBNotes);
          $MBNotes = $Counselor->RemoveNewLine($MBNotes);

          $Counselor->AddMBClass($MBName, $MBPeriod, $MBClassSize, $MBFee, $MBRoom, $MBPrerequisities, $MBNotes);
        }

        $msg = $FirstName . " " . $LastName . " Thank you for supporting the Merit Badge College";
        $Counselor->function_alert($msg);

        // Go show them their sign ups / schedule
        $Counselor->GotoURL('ViewSchedule.php');
        exit;
      }
    }
    ?>
</body>

</html>