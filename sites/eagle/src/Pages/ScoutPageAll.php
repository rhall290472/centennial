<?php

load_class(BASE_PATH . '/../Classes/CEagle.php');
$cEagle = CEagle::getInstance();
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');
$cLeaders = AdultLeaders::getInstance();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <style>
    /*    body {
        font: 12px sans-serif;
		margin-top: 140px;
    }
*/
    .wrapper {
      width: 360px;
      padding: 20px;
    }
  </style>
</head>

<body>
  <?php

  //#####################################################
  //
  // Check to see if user as Submitted the form.
  //
  //#####################################################
  if (isset($_POST['SubmitForm'])) {
    if ($_POST['SubmitForm'] == "Cancel")
      exit;


    // if Scoutid == -1 this will be a INSERT else it's an UPDATE
    // Are we updating or insrting a new scout?
    // New scout will have a id of -1
    $SelectedScout = $_POST['Scoutid'];
    $queryScout = "SELECT * FROM `scouts` WHERE Scoutid='$SelectedScout'";

    if (!$Scout = $cEagle->doQuery($queryScout)) {
      $msg = "Error: doQuery()";
      $cEagle->function_alert($msg);
    }
    $rowScout = $Scout->fetch_assoc();
    if ($rowScout == null)
      exit;

    // Save New data..From the user form
    $FormData = array();
    //Line 1
    $FormData['Scoutid'] = $rowScout['Scoutid'];
    $FormData['FirstName'] =  $cEagle->GetFormData('element_1_1');
    $FormData['PreferredName'] = $cEagle->GetFormData('element_1_2');
    $FormData['MiddleName'] =  $cEagle->GetFormData('element_1_3');
    $FormData['LastName'] =  $cEagle->GetFormData('element_1_4');
    $FormData['is_deleted'] = $cEagle->GetFormData('element_1_5');
    //Line 2
    $FormData['Email'] =  $cEagle->GetFormData('element_2_1');
    $FormData['Phone_Home'] =  $cEagle->GetFormData('element_2_2');
    $FormData['Phone_Mobile'] =  $cEagle->GetFormData('element_2_3');
    //Line 3
    $FormData['Street_Address'] = $cEagle->GetFormData('element_3_1');
    $FormData['City'] = $cEagle->GetFormData('element_3_2');
    $FormData['State'] =  $cEagle->GetFormData('element_3_3');
    $FormData['Zip'] =  $cEagle->GetFormData('element_3_4');
    //Line 4
    $FormData['UnitType'] =  $cEagle->GetFormData('element_4_1');
    $FormData['UnitNumber'] =  $cEagle->GetFormData('element_4_2');
    $FormData['District'] =  $cEagle->GetFormData('element_4_3');
    $FormData['Gender'] =  $cEagle->GetFormData('element_4_4');
    $FormData['AgeOutDate'] =  $cEagle->GetFormData('element_4_5');
    $FormData['MemberId'] =  $cEagle->GetFormData('element_4_6');
    //Line 5
    $FormData['ULFirst'] = $cEagle->GetFormData('element_5_1');
    $FormData['ULLast'] =  $cEagle->GetFormData('element_5_2');
    $FormData['ULPhone'] =  $cEagle->GetFormData('element_5_3');
    $FormData['ULEmail'] =  $cEagle->GetFormData('element_5_4');
    //Line 6
    $FormData['CCFirst'] = $cEagle->GetFormData('element_6_1');
    $FormData['CCLast'] =  $cEagle->GetFormData('element_6_2');
    $FormData['CCPhone'] =  $cEagle->GetFormData('element_6_3');
    $FormData['CCEmail'] =  $cEagle->GetFormData('element_6_4');
    //Line 6a
    $FormData['GuardianFirst'] = $cEagle->GetFormData('element_6_1a');
    $FormData['GuardianLast'] =  $cEagle->GetFormData('element_6_2a');
    $FormData['GuardianPhone'] =  $cEagle->GetFormData('element_6_3a');
    $FormData['GuardianEmail'] =  $cEagle->GetFormData('element_6_4a');
    $FormData['GuardianRelationship'] =  $cEagle->GetFormData('element_6_5a');
    //Line 7
    $FormData['AgedOut'] = $cEagle->GetFormData('element_9_4');
    $FormData['AttendedPreview'] =  $cEagle->GetFormData('element_7_2');
    $FormData['ProjectApproved'] =  $cEagle->GetFormData('element_7_3');
    $FormData['ProjectDate'] =  $cEagle->GetFormData('element_7_4');
    //If project/propsal date is entered it must then be approved
    if (strlen($FormData['ProjectDate']) > 1)
      $FormData['ProjectApproved'] = 1;
    $FormData['Coach'] =  $cEagle->GetFormData('element_7_5');
    $FormData['ProjectHours'] =  $cEagle->GetFormData('element_7_6');
    //Line 8
    $FormData['Beneficiary'] = $cEagle->GetFormData('element_8_1');
    $FormData['ProjectName'] =  $cEagle->GetFormData('element_8_2');
    //Line 9
    $FormData['BOR'] = $cEagle->GetFormData('element_9_1');
    $FormData['BOR_Member'] =  $cEagle->GetFormData('element_9_2');
    $FormData['Eagled'] =  $cEagle->GetFormData('element_9_3');
    //Line 10
    $FormData['Notes'] =  $cEagle->GetFormData('element_10_1');

    if ($cEagle->UpdateScoutRecord($FormData)) {
      // Record has been updated in database now create a audit trail
      $cEagle->CreateAudit($rowScout, $FormData, 'Scoutid');
    }
  }
  ?>

  <center>
    <?php

    $queryScouts = "SELECT DISTINCTROW LastName, MiddleName, FirstName, Scoutid FROM scouts 
		WHERE (`Eagled` IS NULL OR `Eagled`='1') OR 
        	(`AgedOut` IS NULL OR `AgedOut`='1') AND
        	(`is_deleted` IS NULL OR `is_deleted`='0')
     		ORDER BY LastName, FirstName";

    $result_ByScout = $cEagle->doQuery($queryScouts);
    if (!$result_ByScout) {
      $cEagle->function_alert("ERROR: $cEagle->doQuery($queryScouts)");
    }
    $cEagle->SelectScout();
    //#####################################################
    //
    // Check to see if user as Submitted the form.
    //
    //#####################################################
    if ((isset($_POST['SubmitScout']) && isset($_POST['ScoutID']) && $_POST['ScoutID'] !== '') ||
      (isset($_GET['Scoutid']))
    ) {

      if (isset($_POST['ScoutID']))
        $SelectedScout = $_POST['ScoutID']; // Get id of Scout selected
      else if (isset($_GET['Scoutid']))
        $SelectedScout = $_GET['Scoutid']; // Get id of Scout selected

      // Go get the Scout data
      $queryScout = "SELECT * FROM `scouts` WHERE `Scoutid`=$SelectedScout ";

      if (!$Scout = $cEagle->doQuery($queryScout)) {
        $msg = "Error: doQuery()";
        $cEagle->function_alert($msg);
      }
      $rowScout = $Scout->fetch_assoc();

      $Street = $rowScout['Street_Address'];
      $Beneficiary = addslashes($rowScout['Beneficiary']);
      $ProjectName = addslashes($rowScout['ProjectName']);

      if (!$rowScout['Eagled'] && !$rowScout['AgedOut']) {
        // Need to update Unit leader information.
        $UnitFormatted = $rowScout['UnitType'] . " " . $rowScout['UnitNumber'];
        $UnitFormatted = $cLeaders->formatUnitNumber($UnitFormatted, $rowScout['Gender']);
        $UnitLeader = $cLeaders->GetUnitLeader($UnitFormatted);
        $rowScout['ULFirst'] = $UnitLeader['FirstName'];
        $rowScout['ULLast'] = $UnitLeader['LastName'];
        $rowScout['ULPhone'] = str_replace(' ', '', $UnitLeader['Phone']);
        $rowScout['ULEmail'] = $UnitLeader['Email'];
        $CommitteeChair = $cLeaders->GetCommitteeChair($UnitFormatted);
        $rowScout['CCFirst'] = $CommitteeChair['FirstName'];
        $rowScout['CCLast'] = $CommitteeChair['LastName'];
        $rowScout['CCPhone'] = str_replace(' ', '', $CommitteeChair['Phone']);
        $rowScout['CCEmail'] = $CommitteeChair['Email'];
      }
      require('ScoutForm.php');
    }
    ?>
  </center>
  </div>
</body>
</header>