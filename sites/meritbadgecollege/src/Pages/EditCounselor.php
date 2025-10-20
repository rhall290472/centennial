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
//include_once 'CMBCollege.php';
$CMBCollege = CMBCollege::getInstance();
?>

<html>
<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-9">
        <?php
        //*************************************************************************/
        //
        // 1. Get a list of approved merit badge counselors and display it to the user
        //
        //*************************************************************************/
        $querySelectedCounselor1 = "SELECT DISTINCTROW mbccounselors.LastName, mbccounselors.FirstName, mbccounselors.MemberID FROM mbccounselors
        WHERE mbccounselors.Active='Yes' ORDER BY mbccounselors.LastName, mbccounselors.FirstName";

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

          // $sqlMB = sprintf("%s '%s'  ORDER BY meritbadges.MeritName ASC", $querySelectedCounselor2, $SelectedCounselor);
          $sqlCounselor = "SELECT * FROM mbccounselors WHERE `Active`='Yes' ORDER BY `LastName`, `FirstName`";
          if (!$ResultsMB = $Counselor->doQuery($sqlCounselor)) {
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
          <p style="text-align:Left"><b>Counselor Information</b></p>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="edit_counselor" method="post">
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
                <label class="description" for="element_1_4">Phone # </label>
                <input type="text" name="element_1_4" class="form-control" placeholder="Phone" type="number" value=<?php echo $row['HomePhone']; ?>>
              </div>
              <div class="col">
                <label class="description" for="element_1_5">BSA Member ID </label>
                <input type="text" name="element_1_5" class="form-control" placeholder="Member ID" type="number" value=<?php echo $row['MemberID']; ?>>
              </div>
            </div>
            <div class="form-row py-1">
              <div class="col-2">
                <label class="description" for="element_2_1">Is Signed Up</label>
                <input class="form-check-input" type="hidden" name="element_2_1" value='0' />
                <input class="form-check-input" type="checkbox" name="element_2_1" value='1' <?php if ($row['Is_SignedUp'] == 1) echo "checked=checked"; ?>>
              </div>
              <div class="col-2">
                <label class="description" for="element_2_2">Is a no</label>
                <input class="form-check-input" type="hidden" name="element_2_2" value='0' />
                <input class="form-check-input" type="checkbox" name="element_2_2" value='1' <?php if ($row['Is_a_no'] == 1) echo "checked=checked"; ?>>
              </div>
              <div class="col-2">
                <label class="description" for="element_2_3">Is not MBC</label>
                <input class="form-check-input" type="hidden" name="element_2_3" value='0' />
                <input class="form-check-input" type="checkbox" name="element_2_3" value='1' <?php if ($row['Is_not_MBC'] == 1) echo "checked=checked"; ?>>
              </div>
            </div>
            <div class="row">
              <div class="col-12 py-3">
                <label class="form col-form-label" for="element_3_1">Notes </label>
                <textarea class="textarea form-control-sm" rows="10" cols="100" id="element_3_1" name="element_3_1"><?php echo $row['Notes']; ?></textarea>
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

    // Common data for each Merit Badge
    $FirstName = $Counselor->GetFormData('element_1_1');
    $LastName = $Counselor->GetFormData('element_1_2');
    $Email = $Counselor->GetFormData('element_1_3');
    $Phone = $Counselor->GetFormData('element_1_4');
    $BSAId = $Counselor->GetFormData('element_1_5');
    $IsSignedup =  $Counselor->GetFormData('element_2_1');
    $Is_a_no =  $Counselor->GetFormData('element_2_2');
    $Is_not_MBC =  $Counselor->GetFormData('element_2_3');
    $Notes = $Counselor->GetFormData('element_3_1');

    $sql = "UPDATE `mbccounselors` SET `LastName`='$LastName',`FirstName`='$FirstName',`HomePhone`='$Phone',
      `Email`='$Email',`MemberID`='$BSAId', `Notes`='$Notes', `Is_SignedUp`='$IsSignedup',`Is_a_no`='$Is_a_no',
      `Is_not_MBC`='$Is_not_MBC' WHERE `LastName`='$LastName' AND `FirstName`='$FirstName'";

    $result = $Counselor->doQuery($sql);
    if (!$result) {
      $strErr = "Error: ".$sql." ".__FILE__." ".__LINE__;
      error_log("$strErr");
      exit;
    }
    // $msg = $FirstName . " " . $LastName . " Thank you for supporting the Merit Badge College";
    // $Counselor->function_alert($msg);

    // Go show them their sign ups / schedule
    //$Counselor->GotoURL('ViewSchedule.php');
    exit;
  }
  ?>
</body>

</html>