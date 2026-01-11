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
load_class(BASE_PATH.'/src/Classes/CScout.php');
$Scout = CScout::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
    header('Location: index.php?page=login');
    exit;
}

$CollegeYear = $_SESSION['year'];
if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
  $CollegeYear = $_POST['CollegeYear'];
  $GLOBALS["MBCollegeYear"] = $CollegeYear;
  $_SESSION['year'] = $CollegeYear;
}

?>

<html>
<body>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-3">

        <?php
        $MB1FirstName = null;
        $MB1LastName  = null;
        $MB1Email = null;
        $MB1Name = null;
        $MB1Period  = null;
        $MB2FirstName = null;
        $MB2LastName  = null;
        $MB2Email = null;
        $MB2Name = null;
        $MB2Period  = null;
        $MB3FirstName = null;
        $MB3LastName  = null;
        $MB3Email = null;
        $MB3Name = null;
        $MB3Period  = null;
        $MB4FirstName = null;
        $MB4LastName  = null;
        $MB4Email = null;
        $MB4Name = null;
        $MB4Period  = null;
        // $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details ORDER BY College DESC";
        // $result_CollegeYear = $Scout->doQuery($queryCollegeYear);
        $Scout->SelectCollegeYearandScout($CollegeYear, "Enter Scout Data", false);

        //#####################################################
        //
        // Wait for user to select a Scout
        //
        //#####################################################
        if (isset($_POST['SubmitScout']) && isset($_POST['ScoutName']) && $_POST['ScoutName'] !== '') {
          $SelectedScout = $_POST['ScoutName']; // Get name of Counselor selected

          //$CollegeYear = getYear();
          if ($SelectedScout != -1) {
            $queryScout = "SELECT * FROM `college_registration`
        INNER JOIN college_counselors ON college_registration.MeritBadge=college_counselors.MBName AND college_registration.Period=college_counselors.MBPeriod
        AND college_registration.College=college_counselors.College
        WHERE college_registration.College='$CollegeYear' AND BSAIdScout='$SelectedScout' ORDER BY `Period` ASC";

            if (! $ScoutsMBs = $Scout->doQuery($queryScout)) {
              $msg = "Error: MeritQuery()";
              $Scout->function_alert($msg);
            }
            $rowScoutsMBs = $ScoutsMBs->fetch_assoc();

            //*********************************************************************/
            //
            // Now check to see if they have already signed up once, if so allow them
            // to edit their signup.
            //
            //*********************************************************************/
            $Scout->IsSignedUp($CollegeYear, $rowScoutsMBs['LastNameScout'], $rowScoutsMBs['FirstNameScout'], $_POST['ScoutName']);
            $FirstNameScout = $rowScoutsMBs['FirstNameScout'];
            $LastNameScout = $rowScoutsMBs['LastNameScout'];
            $EmailScout = $rowScoutsMBs['email'];
            $PhoneScout = $rowScoutsMBs['Telephone'];
            $BSAIdScout = $rowScoutsMBs['BSAIdScout'];
            // Line 1a
            $Registration = $rowScoutsMBs['Registration'];
            $District = $rowScoutsMBs['District'];
            $UnitType = $rowScoutsMBs['UnitType'];
            $UnitNumber = $rowScoutsMBs['UnitNumber'];
            $Gender = $rowScoutsMBs['Gender'];


            //Counselor Data - MB1
            $MB1FirstName = $rowScoutsMBs['FirstName'];
            $MB1LastName = $rowScoutsMBs['LastName'];
            $MB1Email = $rowScoutsMBs['Email'];
            $MB1Name = $rowScoutsMBs['MeritBadge'];
            $MB1Period = $rowScoutsMBs['Period'];

            //Counselor Data - MB2
            if($rowScoutsMBs = $ScoutsMBs->fetch_assoc()){
              $MB2FirstName = $rowScoutsMBs['FirstName'];
              $MB2LastName = $rowScoutsMBs['LastName'];
              $MB2Email = $rowScoutsMBs['Email'];
              $MB2Name = $rowScoutsMBs['MeritBadge'];
              $MB2Period = $rowScoutsMBs['Period'];
            }

            //Counselor Data - MB3
            if($rowScoutsMBs = $ScoutsMBs->fetch_assoc()){
              $MB3FirstName = $rowScoutsMBs['FirstName'];
              $MB3LastName = $rowScoutsMBs['LastName'];
              $MB3Email = $rowScoutsMBs['Email'];
              $MB3Name = $rowScoutsMBs['MeritBadge'];
              $MB3Period = $rowScoutsMBs['Period'];
            }

            //Counselor Data - MB4
            if($rowScoutsMBs = $ScoutsMBs->fetch_assoc()){
              $MB4FirstName = $rowScoutsMBs['FirstName'];
              $MB4LastName = $rowScoutsMBs['LastName'];
              $MB4Email = $rowScoutsMBs['Email'];
              $MB4Name = $rowScoutsMBs['MeritBadge'];
              $MB4Period = $rowScoutsMBs['Period'];
              }
            
          } else {
            $ScoutsMBs = null;
            $FirstNameScout = '';
            $LastNameScout = '';
            $EmailScout = '';
            $PhoneScout = '';
            $BSAIdScout = '';
            $Registration = '';
            $District = '';
            $UnitType = '';
            $UnitNumber = '';
            $Gender = '';

            //Counselor Data
            $FirstName = '';
            $LastName = '';
            $Email = '';
          }

          // Only display Merit Badge avaiable at the College
          $sqlMB = "SELECT DISTINCTROW `MBName` FROM college_counselors WHERE college='$CollegeYear' ORDER BY `MBName` ASC";
          if (!$CollegeMBs = $Scout->doQuery($sqlMB)) {
            $msg = "Error: MeritQuery()";
            $Scout->function_alert($msg);
          }
        ?>



          <hr class=" d-print-none" />
          <form id=" add_scout" class="appnitro" method="post">
          <div class="row">
            <div class="col-1">
              <label class="description" for="element_1_1">Scout </label>
              <input id="element_1_1" name="element_1_1" class="form-control" maxlength="255" size="8"
                <?php if (strlen($FirstNameScout) > 0) echo "value=" . $FirstNameScout; ?> />
              <label>First</label>
            </div>
            <div class="col-1">
              <label class="description" for="element_1_2">Name </label>
              <input id="element_1_2" name="element_1_2" class="form-control" maxlength="255" size="14"
                <?php if (strlen($LastNameScout) > 0) echo "value=" . $LastNameScout; ?> />
              <label>Last</label>
            </div>
            <div class="col-2">
              <label class="description" for="element_1_3">Scout Email </label>
              <input id="element_1_3" name="element_1_3" class="form-control" type="email" maxlength="255" size="50"
                <?php if (strlen($EmailScout) > 0) echo "value=" . $EmailScout; ?> />
              <label>Email</label>
            </div>
            <div class="col-2">
              <label class="description" for="element_1_4">Phone </label>
              <input id="element_1_4" name="element_1_4" class="form-control" type="number" maxlength="10" size="10"
                <?php if (strlen($PhoneScout) > 0) echo "value=" . $PhoneScout; ?> />
              <label>Best Contact Number</label>
            </div>
            <div class="col-1">
              <label class="description" for="element_1_5">Member ID</label>
              <input id="element_1_5" name="element_1_5" class="form-control" type="number" maxlength="10" size="10"
                <?php if (strlen($BSAIdScout) > 0) echo "value=" . $BSAIdScout; ?> />
              <label>BSA ID</label>
            </div>
          </div>
          <div class="row">
            <div class="col-1">
              <label class="description" for="element_1_6">Registration # </label>
              <input id="element_1_6" name="element_1_6" class="form-control" maxlength="255" size="8"
                <?php if (strlen($Registration) > 0) echo "value=" . $Registration; ?> />
            </div>
            <div class="col-2">
              <label class="description" for="element_1_7">District </label>
              <select class='form-select' id='element_1_7' name='element_1_7'>
                <option value=""> </option>
                <?php $Scout->DisplayDistrict($District); ?>
              </select>
            </div>
            <div class="col-1">
              <label class="description" for="element_1_8">Unit Type </label>
              <select class='form-select' id='element_1_8' name='element_1_8'>
                <option value=""> </option>
                <?php $Scout->DisplayUnitType($UnitType); ?>
              </select>
            </div>
            <div class="col-1">
              <label class="description" for="element_1_9">Unit Number </label>
              <input id="element_1_9" name="element_1_9" class="form-control" type="number" maxlength="1" size="1"
                <?php if (strlen($UnitNumber) > 0) echo "value=" . $UnitNumber; ?> />
            </div>
            <div class="col-1">
              <label class="description" for="element_1_10">Gender</label>
              <select class='form-select' id='element_1_10' name='element_1_10'>
                <option value=""> </option>
                <?php $Scout->DisplayGender($Gender); ?>
              </select>
            </div>
          </div>
          <!--

          Row two of Scout Data

          -->
          <hr />
          <div class="row">
            <div class="col-2">
              <label class="description" for="MB1Name">Merit Badge Name </label>
              <select class='form-select' id='MB1Name' name='MB1Name'>
                <?php
                mysqli_data_seek($CollegeMBs, 0);
                echo "<option value=\"\" </option>"; //First selection is blank
                $FirstBadgeFound = false;

                while ($rowCollegeMBs = $CollegeMBs->fetch_assoc()) {
                  if ($Scout->MB_Match($rowCollegeMBs['MBName'], 1) && !$FirstBadgeFound) {
                    $FirstBadgeFound = true;
                    echo "<option selected value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                  } else
                    echo "<option value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                }
                ?>
              </select>
              <label>First Merit Badge</label>
            </div>
            <div class="col-2">
              <label class="description" for="MB1Period">Period </label>
              <select class='form-select' id='MB1Period' name='MB1Period'>
                <option value=""> </option>
                <?php $Scout->DisplayPeriods(1, $CollegeYear); ?>
              </select>
              <label>Time Slot</label>
            </div>
            <div class="col-1">
              <label class="description" for="element_2_3">Counselor</label>
              <input id="element_2_3" name="element_2_3" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB1FirstName) > 0) echo "value=" . $MB1FirstName; ?> />
              <label>First</label>
            </div>
            <div class="col-1">
              <label class="description" for="element_2_2"> Name</label>
              <input id="element_2_3" name="element_2_3" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB1LastName) > 0) echo "value=" . $MB1LastName; ?> />
              <label>Last</label>
            </div>
            <div class="col-2">
              <label class="description" for="element_2_3">Counselor Email </label>
              <input id="element_2_3" name="element_2_3" class="form-control large" type="email" maxlength="255" size="50"
                <?php if (strlen($MB1Email) > 0) echo "value=" . $MB1Email; ?> />
              <label>Email</label>
            </div>
            <div class="col-1">
              <label class="description" for="MB1Attend">Did Not Attend</label>
              <input class="form-check" type="checkbox" id="MB1Attend" name="MB1Attend" <?php echo $Scout->GetAttend(1); ?> />
            </div>
          </div>
          <!--

          Row three of Scout Data

          -->
          <?php //list($FirstName, $LastName, $Email) = $Scout->GetCounselorData($ScoutsMBs, 1); ?>
          <div class="row">
            <div class=col-2>
              <label class="description" for="MB2Name">Merit Badge Name </label>
              <select class='form-select' id='MB2Name' name='MB2Name'>
                <?php
                mysqli_data_seek($CollegeMBs, 0);
                echo "<option value=\"\" </option>"; //First selection is blank
                $FirstBadgeFound = false;
                // Here we will display a list of all merit badges the counselor is approved to
                // teach and if they are editing they inputs selected the previous entered badge.
                while ($rowCollegeMBs = $CollegeMBs->fetch_assoc()) {
                  // First check to see if the counselor has signed up for this badge.
                  if ($Scout->MB_Match($rowCollegeMBs['MBName'], 2) && !$FirstBadgeFound) {
                    $FirstBadgeFound = true;
                    echo "<option selected value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                  } else
                    echo "<option value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                }
                ?>
              </select>
              <label>Second Merit Badge</label>
            </div>
            <div class=col-2>
              <label class="description" for="MB2Period">Period </label>
              <select class='form-select' id='MB2Period' name='MB2Period'>
                <option value=""> </option>
                <?php $Scout->DisplayPeriods(2, $CollegeYear); ?>
              </select>
              <label>Time Slot</label>
            </div>
            <div class=col-1>
              <label class="description" for="element_3_3">Counselor</label>
              <input id="element_3_3" name="element_3_3" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB2FirstName) > 0) echo "value=" . $MB2FirstName; ?> />
              <label>First</label>
            </div>
            <div class=col-1>
              <label class="description" for="element_3_4"> Name</label>
              <input id="element_3_4" name="element_3_4" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB2LastName) > 0) echo "value=" . $MB2LastName; ?> />
              <label>Last</label>
            </div>
            <div class=col-2>
              <label class="description" for="element_3_5">Counselor Email </label>
              <input id="element_3_5" name="element_3_5" class="form-control" type="email" maxlength="255" size="50"
                <?php if (strlen($MB2Email) > 0) echo "value=" . $MB2Email; ?> />
              <label>Email</label>
            </div>
            <div class=col-1>
              <label class="description" for="MB2Attend">Did Not Attend</label>
              <input class="form-check" type="checkbox" id="MB2Attend" name="MB2Attend" <?php echo $Scout->GetAttend(2); ?> />
            </div>
          </div>
          <!--

          Row four of Scout Data

          -->
          <?php //list($FirstName, $LastName, $Email) = $Scout->GetCounselorData($ScoutsMBs, 2); ?>
          <div class="row">
            <div class=col-2>
              <label class="description" for="MB3Name">Merit Badge Name </label>
              <select class='form-select' id='MB3Name' name='MB3Name'>
                <?php
                mysqli_data_seek($CollegeMBs, 0);
                echo "<option value=\"\" </option>"; //First selection is blank
                $FirstBadgeFound = false;
                // Here we will display a list of all merit badges the counselor is approved to
                // teach and if they are editing they inputs selected the previous entered badge.
                while ($rowCollegeMBs = $CollegeMBs->fetch_assoc()) {
                  // First check to see if the counselor has signed up for this badge.
                  if ($Scout->MB_Match($rowCollegeMBs['MBName'], 3) && !$FirstBadgeFound) {
                    $FirstBadgeFound = true;
                    echo "<option selected value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                  } else
                    echo "<option value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                }
                ?>
              </select>
              <label>Third Merit Badge</label>
            </div>
            <div class=col-2>
              <label class="description" for="MB3Period">Period </label>
              <select class='form-select' id='MB3Period' name='MB3Period'>
                <option value=""> </option>
                <?php $Scout->DisplayPeriods(3, $CollegeYear); ?>
              </select>
              <label>Time Slot</label>
            </div>
            <div class=col-1>
              <label class="description" for="element_4_3">Counselor</label>
              <input id="element_4_3" name="element_4_3" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB3FirstName) > 0) echo "value=" . $MB3FirstName; ?> />
              <label>First</label>
            </div>
            <div class=col-1>
              <label class="description" for="element_4_4"> Name</label>
              <input id="element_4_4" name="element_4_4" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB3LastName) > 0) echo "value=" . $MB3LastName; ?> />
              <label>Last</label>
            </div>
            <div class=col-2>
              <label class="description" for="element_4_5">Counselor Email </label>
              <input id="element_4_5" name="element_4_5" class="form-control large" type="email" maxlength="255" size="50"
                <?php if (strlen($MB3Email) > 0) echo "value=" . $MB3Email; ?> />
              <label>Email</label>
            </div>
            <div class=col-1>
              <label class="description" for="MB3Attend">Did Not Attend</label>
              <input class="form-check" type="checkbox" id="MB3Attend" name="MB3Attend" <?php echo $Scout->GetAttend(3); ?> />
            </div>
          </div>
          <!--

          Row four of Scout Data

          -->
          <?php //list($FirstName, $LastName, $Email) = $Scout->GetCounselorData($ScoutsMBs, 3); ?>
          <div class="row">
            <div class="col-2">
              <label class="description" for="MB4Name">Merit Badge Name </label>
              <select class='form-select' id='MB4Name' name='MB4Name'>
                <?php
                mysqli_data_seek($CollegeMBs, 0);
                echo "<option value=\"\" </option>"; //First selection is blank
                $FirstBadgeFound = false;
                // Here we will display a list of all merit badges the counselor is approved to
                // teach and if they are editing they inputs selected the previous entered badge.
                while ($rowCollegeMBs = $CollegeMBs->fetch_assoc()) {
                  // First check to see if the counselor has signed up for this badge.
                  if ($Scout->MB_Match($rowCollegeMBs['MBName'], 4) && !$FirstBadgeFound) {
                    $FirstBadgeFound = true;
                    echo "<option selected value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                  } else
                    echo "<option value= '" . $rowCollegeMBs['MBName'] . "'>" . $rowCollegeMBs['MBName'] . "</option>";
                }
                ?>
              </select>
              <label>Fourth Merit Badge</label>
            </div>
            <div class="col-2">
              <label class="description" for="MB4Period">Period </label>
              <select class='form-select' id='MB4Period' name='MB4Period'>
                <option value=""> </option>
                <?php $Scout->DisplayPeriods(4, $CollegeYear); ?>
              </select>
              <label>Time Slot</label>
            </div>
            <div class="col-1">
              <label class="description" for="element_5_3">Counselor</label>
              <input id="element_5_3" name="element_5_3" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB4FirstName) > 0) echo "value=" . $MB4FirstName; ?> />
              <label>First</label>
            </div>
            <div class="col-1">
              <label class="description" for="element_5_4"> Name</label>
              <input id="element_5_4" name="element_5_4" class="form-control" maxlength="255" size="8"
                <?php if (strlen($MB4LastName) > 0) echo "value=" . $MB4LastName; ?> />
              <label>Last</label>
            </div>
            <div class="col-2">
              <label class="description" for="element_5_5">Counselor Email </label>
              <input id="element_5_5" name="element_5_5" class="form-control large" type="email" maxlength="255" size="50"
                <?php if (strlen($MB4Email) > 0) echo "value=" . $MB4Email; ?> />
              <label>Email</label>
            </div>
            <div class="col-1">
              <label class="description" for="MB4Attend">Did Not Attend</label>
              <input class="form-check" type="checkbox" id="MB4Attend" name="MB4Attend" <?php echo $Scout->GetAttend(4); ?> />
            </div>
          </div>
          <!--

          Row four of Scout Data

          -->
          <div class="row  d-print-none">
            <div class="col py-4" style="text-align: center;">
              <input type="hidden" name="form_id" value="22772" />
              <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="SubmitForm" />
            </div>
          </div>
          </form>
      </div> <!-- <div class="col py-3"> -->
    </div> <!-- <div class="row flex-nowrap">  -->
  </div> <!--<div class="container-fluid"> -->

<?php }
        //#####################################################
        //
        // Check to see if user as Submitted the form.
        //
        //#####################################################
        if (isset($_POST['SubmitForm'])) {

          // Common data for each Merit Badge
          $FirstNameScout =  $Scout->GetFormData('element_1_1');
          $LastNameScout =  $Scout->GetFormData('element_1_2');
          $Email =  $Scout->GetFormData('element_1_3');
          $Phone =  $Scout->GetFormData('element_1_4');
          $BSAId =  $Scout->GetFormData('element_1_5');
          $Registration =  $Scout->GetFormData('element_1_6');
          $District     =  $Scout->GetFormData('element_1_7');
          $UnitType     =  $Scout->GetFormData('element_1_8');
          $UnitNumber   =  $Scout->GetFormData('element_1_9');
          $Gender       =  $Scout->GetFormData('element_1_10');
          $MBCollegeName = $Scout->getYear();
          $MBName = '';
          $MBPeriod = '';
          $MBClassSize = '';
          $MBPrerequisities = '';
          $MBNotes = '';
          $MBAttended = '';

          // If no BSA ID entered set to smallest value in the database (should be negative number)
          if (empty($BSAId)) {
            $sqlSmallest = "SELECT MIN(BSAIdScout) from college_registration";
            $resultSmallest = $Scout->doQuery($sqlSmallest);
            $Smallest = $resultSmallest->fetch_assoc();
            $BSAId = $Smallest['MIN(BSAIdScout)'];
            $BSAId--;
          }
          // If the user is editing the data we first need to delete the old data
          //$Scout = new Scout;
          if ($Scout->IsSignedUp($MBCollegeName, $LastNameScout, $FirstNameScout, $BSAId))
            $Scout->Delete();
          $Scout->AddInfo(
            $FirstNameScout,
            $LastNameScout,
            $Email,
            $Phone,
            $BSAId,
            $MBCollegeName,
            $Registration,
            $District,
            $UnitType,
            $UnitNumber,
            $Gender
          );

          //Check to see if first Merit Badge is selected.
          if (isset($_POST['MB1Name']) && strlen($_POST['MB1Name']) > 0) {
            $MBName =  $Scout->GetFormData('MB1Name');
            $MBPeriod =  $Scout->GetFormData('MB1Period');
            if (isset($_POST['MB1Attend']))
              $MBAttended = $Scout->GetFormData('MB1Attend');
            $Scout->AddMBClass($MBName, $MBPeriod, $MBAttended === "on" ? 1 : 0);
          }

          //Check to see if second Merit Badge is selected.
          $MBAttended = '';
          if (isset($_POST['MB2Name']) && strlen($_POST['MB2Name']) > 0) {
            $MBName =  $Scout->GetFormData('MB2Name');
            $MBPeriod =  $Scout->GetFormData('MB2Period');
            if (isset($_POST['MB2Attend']))
              $MBAttended = $Scout->GetFormData('MB2Attend');
            $Scout->AddMBClass($MBName, $MBPeriod, $MBAttended === "on" ? 1 : 0);
          }

          //Check to see if third Merit Badge is selected.
          $MBAttended = '';
          if (isset($_POST['MB3Name']) && strlen($_POST['MB3Name']) > 0) {
            $MBName =  $Scout->GetFormData('MB3Name');
            $MBPeriod =  $Scout->GetFormData('MB3Period');
            if (isset($_POST['MB3Attend']))
              $MBAttended = $Scout->GetFormData('MB3Attend');
            $Scout->AddMBClass($MBName, $MBPeriod, $MBAttended === "on" ? 1 : 0);
          }

          //Check to see if fourth Merit Badge is selected.
          $MBAttended = '';
          if (isset($_POST['MB4Name']) && strlen($_POST['MB4Name']) > 0) {
            $MBName = $Scout->GetFormData('MB4Name');
            $MBPeriod =  $Scout->GetFormData('MB4Period');
            if (isset($_POST['MB4Attend']))
              $MBAttended = $Scout->GetFormData('MB4Attend');
            $Scout->AddMBClass($MBName, $MBPeriod, $MBAttended === "on" ? 1 : 0);
          }

          $Scout->GotoURL('EnterScout.php');
          exit;
        }


        //#####################################################
        //
        // Wait for user to select a Scout
        //
        //#####################################################
        if (isset($_POST['SubmitScout']) && isset($_POST['ScoutName']) && $_POST['ScoutName'] !== '') {
          $SelectedScout = $_POST['ScoutName']; // Get name of Counselor selected

          //$CollegeYear = getYear();
          if ($SelectedScout != -1) {
            $queryScout = "SELECT * FROM `college_registration` 
			INNER JOIN college_counselors ON college_registration.MeritBadge=college_counselors.MBName AND college_registration.Period=college_counselors.MBPeriod
        	AND college_registration.College=college_counselors.College
			WHERE college_registration.College='$CollegeYear' AND BSAIdScout='$SelectedScout'  ORDER BY `Period` ASC";

            if (! $ScoutsMBs = $Scout->doQuery($queryScout)) {
              $msg = "Error: MeritQuery()";
              $Scout->function_alert($msg);
            }
            $rowScoutsMBs = $ScoutsMBs->fetch_assoc();

            //*********************************************************************/
            //
            // Now check to see if they have already signed up once, if so allow them
            // to edit their signup.
            //
            //*********************************************************************/
            $Scout->IsSignedUp($CollegeYear, $rowScoutsMBs['LastNameScout'], $rowScoutsMBs['FirstNameScout'], $_POST['ScoutName']);
            $FirstNameScout = $rowScoutsMBs['FirstNameScout'];
            $LastNameScout = $rowScoutsMBs['LastNameScout'];
            $EmailScout = $rowScoutsMBs['email'];
            $PhoneScout = $rowScoutsMBs['Telephone'];
            $BSAIdScout = $rowScoutsMBs['BSAIdScout'];
            // Line 1a
            $Registration = $rowScoutsMBs['Registration'];
            $District = $rowScoutsMBs['District'];
            $UnitType = $rowScoutsMBs['UnitType'];
            $UnitNumber = $rowScoutsMBs['UnitNumber'];
            $Gender = $rowScoutsMBs['Gender'];


            //Counselor Data
            $FirstName = $rowScoutsMBs['FirstName'];
            $LastName = $rowScoutsMBs['LastName'];
            $Email = $rowScoutsMBs['Email'];

            //TODO: Need to get all of the Merit badge names and perios
            // Line 2
            $MB1Name = $rowScoutsMBs['MeritBadge'];
            $MB1Period = $rowScoutsMBs['Period'];
          } else {
            $ScoutsMBs = null;
            $FirstNameScout = '';
            $LastNameScout = '';
            $EmailScout = '';
            $PhoneScout = '';
            $BSAIdScout = '';
            $Registration = '';
            $District = '';
            $UnitType = '';
            $UnitNumber = '';
            $Gender = '';

            //Counselor Data
            $FirstName = '';
            $LastName = '';
            $Email = '';
          }

          // Only display list of Merit Badges available at the College
          $sqlMB = "SELECT DISTINCTROW `MBName` FROM college_counselors WHERE college='$CollegeYear' ORDER BY `MBName` ASC";
          if (!$CollegeMBs = $Scout->doQuery($sqlMB)) {
            $msg = "Error: MeritQuery()";
            $Scout->function_alert($msg);
          }


?>



<?php
        }
?>
</body>

</html>