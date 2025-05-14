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
</head>

<body>
  <?php include('header.php');

  //#####################################################
  //
  // Check to see if user as Submitted the form. If so, save the data..
  //
  //#####################################################
  if (isset($_POST['SubmitForm'])) {
    if ($_POST['SubmitForm'] == "Cancel")
      exit;

    // Are we updating or insrting a new coach?
    // New coach will have a id of -1
    $SelectedCoach = $_POST['Coachesid'];
    $queryCoach = "SELECT * FROM `coaches` WHERE Coachesid='$SelectedCoach'";

    if (!$Coach = $cEagle->doQuery($queryCoach)) {
      $msg = "Error: doQuery()";
      $SccEagleout->function_alert($msg);
    }
    $rowCoach = $Coach->fetch_assoc();
    if ($rowCoach == null)
      exit;

    // Save New data..From the user form
    $FormData = array();
    $FormData['Coachesid'] = $rowCoach['Coachesid'];
    $FormData['First_Name'] =  $cEagle->GetFormData('element_1_1');
    $FormData['PreferredName'] =  $cEagle->GetFormData('element_1_1a');
    $FormData['Middle_Name'] =  $cEagle->GetFormData('element_1_2');
    $FormData['Last_Name'] =  $cEagle->GetFormData('element_1_3');
    $FormData['Member_ID'] =  $cEagle->GetFormData('element_1_4');
    $FormData['Email_Address'] =  $cEagle->GetFormData('element_2_1');
    $FormData['Phone_Home'] =  $cEagle->GetFormData('element_2_2');
    $FormData['Phone_Mobile'] =  $cEagle->GetFormData('element_2_3');

    $FormData['Street_Address'] = $cEagle->GetFormData('element_3_1');
    $FormData['City'] = $cEagle->GetFormData('element_3_2');
    $FormData['State'] =  $cEagle->GetFormData('element_3_3');
    $FormData['Zip'] =  $cEagle->GetFormData('element_3_4');
    $FormData['Position'] =  $cEagle->GetFormData('element_4_1');
    $FormData['District'] =  $cEagle->GetFormData('element_4_2');
    $FormData['YPT_Expires'] =  $cEagle->GetFormData('element_4_3');
    $FormData['Gender'] = $cEagle->GetFormData('element_4_4');
    $FormData['Trained'] =  $cEagle->GetFormData('element_4_5');
    $FormData['Active'] =  $cEagle->GetFormData('element_4_6');
    $FormData['Notes'] =  $cEagle->GetFormData('Notes');

    if ($cEagle->UpdateCoachRecord($FormData)) {
      // Record has been updated in database now create a audit trail
      $cEagle->CreateAudit($rowCoach, $FormData, 'Coachesid');
    }
  }
  ?>

  <?php

  $queryCoaches = "SELECT DISTINCTROW Last_Name, First_Name, Coachesid FROM coaches ORDER BY Last_Name, First_Name";

  $result_ByCoaches = $cEagle->doQuery($queryCoaches);
  if (!$result_ByCoaches) {
    $cEagle->function_alert("ERROR: $cEagle->doQuery($result_ByCoaches)");
  }
  ?>
  </br></br>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method=post>
    <div class="form-row px-5">
      <div class="col-3">
        <label for='CoachName'>Choose a Coach: </label>
        <select class='form-control' id='CoachName' name='CoachName'>
          <?php
          while ($rowCoach = $result_ByCoaches->fetch_assoc()) {
            echo "<option value=" . $rowCoach['Coachesid'] . ">" . $rowCoach['Last_Name'] . " " . $rowCoach['First_Name'] . "</option>";
            echo "option value=" . $rowCoach['Last_Name'] . ">" . $rowCoach['First_Name'] . "/option";
          }
          ?>
          <option value=-1>Add New</option>
        </select>
      </div>
      <div class="col-3 py-4">
        <input class="btn btn-primary btn-sm" type='submit' name='SubmitCoach' value='Select Coach'>
      </div>
    </div>
  </form>
  <?php
  //#####################################################
  //
  // Check to see if user selected a coach.
  //
  //#####################################################
  if (
    isset($_POST['SubmitCoach']) && isset($_POST['CoachName']) && $_POST['CoachName'] !== '' ||
    (isset($_GET['Coachesid']))
  ) {

    if (isset($_POST['CoachName']))
      $SelectedCoach = $_POST['CoachName']; // Get name of Counselor selected
    else if (isset($_GET['Coachesid']))
      $SelectedCoach = $_GET['Coachesid'];
    // If new coach is selected must create a record in the database for them.
    // There is a blank record in the database with Coachid set to -1 for this.
    // Go get the Scout data

    // Go get the coaches data
    $queryCoach = "SELECT * FROM `coaches` WHERE Coachesid='$SelectedCoach'";

    if (!$Coach = $cEagle->doQuery($queryCoach)) {
      $msg = "Error: doQuery()";
      $SccEagleout->function_alert($msg);
    }
    $rowCoach = $Coach->fetch_assoc();
    $Street = $rowCoach['Street_Address'];
  ?>



    </br>

    <div class="form-coach px-5" style="background-color: var(--scouting-lighttan);">
      <p style="text-align:Left"><b>Coach Information</b></p>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="coach-form" method="post">

        <div class="form-row">
          <div class="col-2">
            <label>First</label>
            <input type="text" name="element_1_1" class="form-control" <?php if (strlen($rowCoach['First_Name']) > 0) echo "value=" . $rowCoach['First_Name']; ?> />
          </div>
          <div class="col-2">
            <label for=element_1_1a>Preferred</label>
            <input type="text" name="element_1_1a" class="form-control" <?php if (strlen($rowCoach['PreferredName']) > 0) echo "value=" . $rowCoach['PreferredName']; ?> />
          </div>
          <div class="col-2">
            <label for=element_1_2>Middle</label>
            <input type="text" name="element_1_2" class="form-control" <?php if (strlen($rowCoach['Middle_Name']) > 0) echo "value=" . $rowCoach['Middle_Name']; ?> />
          </div>
          <div class="col-2">
            <label for=element_1_3>Last</label>
            <input type="text" name="element_1_3" class="form-control" <?php if (strlen($rowCoach['Last_Name']) > 0) echo "value=" . $rowCoach['Last_Name']; ?> />
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label for=element_2_1>Email</label>
            <input type="text" name="element_2_1" class="form-control" type="email" size="35" <?php if (strlen($rowCoach['Email_Address']) > 0) echo "value=" . $rowCoach['Email_Address']; ?> />
          </div>
          <div class="col-2">
            <label for=element_2_2>Home Phone Number</label>
            <input type="text" name="element_2_2" class="form-control" type="number" maxlength="10" <?php if (strlen($rowCoach['Phone_Home']) > 0) echo "value=" . $rowCoach['Phone_Home']; ?> />
          </div>
          <div class="col-2">
            <label for=element_2_3>Mobile Phone Number</label>
            <input type="text" name="element_2_3" class="form-control" type="number" maxlength="10" <?php if (strlen($rowCoach['Phone_Mobile']) > 0) echo "value=" . $rowCoach['Phone_Mobile']; ?> />
          </div>
          <div class="col-2">
            <label for=element_1_4>BSA ID</label>
            <input type="text" name="element_1_4" class="form-control" <?php if (strlen($rowCoach['Member_ID']) > 0) echo "value=" . $rowCoach['Member_ID']; ?> />
          </div>
        </div>

        <div class="form-row">
          <div class="col-3">
            <label for=element_3_1>Street Address</label>
            <input type="text" name="element_3_1" class="form-control" <?php if (strlen($Street) > 0) echo "value='" . $Street . "'"; ?> />
          </div>
          <div class="col-2">
            <label for=element_3_2>City</label>
            <input type="text" name="element_3_2" class="form-control" <?php if (strlen($rowCoach['City']) > 0) echo "value=" . $rowCoach['City']; ?> />
          </div>
          <div class="col-1">
            <label for=element_3_3>State</label>
            <input type="text" name="element_3_3" class="form-control" <?php if (strlen($rowCoach['State']) > 0) echo "value=" . $rowCoach['State']; ?> />
          </div>
          <div class="col-1">
            <label for=element_3_4>Zip</label>
            <input type="text" name="element_3_4" class="form-control" <?php if (strlen($rowCoach['Zip']) > 0) echo "value=" . $rowCoach['Zip']; ?> />
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label for=element_4_1>Posiiton</label>
            <select class='form-control' name='element_4_1'>
              <option value=""> </option>
              <?php $cEagle->DisplayPosition($rowCoach['Position']); ?>
            </select>
          </div>
          <div class="col-1">
            <label for=element_4_2>District</label>
            <select class='form-control' name='element_4_2'>
              <option value=""> </option>
              <?php $cEagle->DisplayDistrict($rowCoach['District']); ?>
            </select>
          </div>
          <div class="col-1">
            <label for=element_4_3>YPT Expires</label>
            <input type="text" id="element_4_3" class="form-control" <?php if (strlen($rowCoach['YPT_Expires']) > 0) echo "value=" . $rowCoach['YPT_Expires']; ?> />
          </div>
          <div class="col-1">
            <label for=element_4_4>Gender</label>
            <select class='form-control' name='element_4_4' name='element_4_4'>
              <option value=""> </option>
              <?php $cEagle->DisplayGender($rowCoach['Gender']); ?>
            </select>
          </div>
          <div class="form-check py-4 px-4">
            <label class="form-check-label" for="element_4_5">Trained</label>
            <!--<input class="form-check-input" type="checkbox" id="element_4_5" type="hidden" value='0' />-->
            <input class="form-check-input" type="checkbox" name="element_4_5" value='1' <?php if ($rowCoach['Trained'] == 1) echo "checked=checked"; ?>>
          </div>
          <div class="form-check py-4">
            <label class="form-check-label" for="element_4_6">Active</label>
            <!--<input class="form-check-input" type="checkbox" id="element_4_6" type="hidden" value='0' />-->
            <input class="form-check-input" type="checkbox" name="element_4_6" value='1' <?php if ($rowCoach['Active'] == 1) echo "checked=checked"; ?>>
          </div>
        </div>


        <div class="form-row">
          <div class="col-8">
            <label for=Notes>Notes</label>
            <textarea class="form-control" name="Notes" rows="10" style="height:100%;"><?php if (strlen($rowCoach['Notes']) > 0) echo $rowCoach['Notes']; ?></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="col-10 py-5">
            <?php $ID = $rowCoach['Coachesid']; ?>
            <?php echo '<input type="hidden" name="Coachesid" value="' . $rowCoach['Coachesid'] . '"/>'; ?>
            <input id="saveForm3" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Save" />
            <input id="saveForm4" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Cancel" />
          </div>
        </div>
      </form>
    </div>

  <?php } ?>
  <?php include('Footer.php'); ?>
</body>

</html>