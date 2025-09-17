<?php
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
!  #   Copyright2024 - Richard Hall                                         #  !
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

load_class(BASE_PATH . '/src/Classes/CDistrictAwards.php');
load_class(BASE_PATH . '/src/Classes/CAwards.php');
$cDistrictAwards = cDistrictAwards::getInstance();
$cAwards = CAwards::getInstance();
$MemberID = "";

//#####################################################
//
// Check to see if user as Submitted the form. If so, save the data..
//
//#####################################################
if (isset($_POST['SubmitForm'])) {
  if ($_POST['SubmitForm'] == "Cancel") {
    header("Location: index.php");
    exit();
  }

  // Are we updating or insrting a new nomiee?
  // New nomiee will have a id of -1
  $SelectedNominee = $_POST['NomineeIDX'];
  if (!$SelectedNominee) {
    $strmsg = "ERROR: $_POST[NomineeIDX] from " . __FILE__ . ", " . __LINE__;
    error_log($strmsg);
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => "Unable to find Nominee"];
    header("Location: index.php");
    exit();
  }
  $queryNominee = "SELECT * FROM `district_awards` WHERE NomineeIDX='$SelectedNominee'";

  if (!$Nominee = $cDistrictAwards->doQuery($queryNominee)) {
    $msg = "Error: doQuery(" . $queryNominee . ") from " . __FILE__ . ", " . __LINE__;
    error_log($msg);
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => "Error in sql statement"];
    header("Location: index.php");
    exit();
  }
  $rowNominee = $Nominee->fetch_assoc();
  if ($rowNominee == null) {
     $_SESSION['feedback'] = ['type' => 'danger', 'message' => "Nominee index error"];
    header("Location: index.php");
    exit();
  }

  // Save New data..From the user form
  $FormData = array();
  $FormData['NomineeIDX'] = $rowNominee['NomineeIDX'];
  $FormData['FirstName'] =  $cDistrictAwards->GetFormData('element_1_1');
  $FormData['PName'] =  $cDistrictAwards->GetFormData('element_1_2');
  $FormData['MName'] =  $cDistrictAwards->GetFormData('element_1_3');
  $FormData['LastName'] =  $cDistrictAwards->GetFormData('element_1_4');

  $FormData['Position'] =  $cDistrictAwards->GetFormData('element_2_1');
  $FormData['Unit'] =  $cDistrictAwards->GetFormData('element_2_2');
  $FormData['MemberID'] =  $cDistrictAwards->GetFormData('element_2_3');
  $FormData['IsDeleted'] =  $cDistrictAwards->GetFormData('element_2_4');

  $FormData['Year'] =  $cDistrictAwards->GetFormData('element_3_1');
  $FormData['Award'] =  $cDistrictAwards->GetFormData('element_3_2');
  $FormData['Status'] =  $cDistrictAwards->GetFormData('element_3_3');


  // Get Disitrict of Merit data ...
  if ($FormData['Award'] == cDistrictAwards::$DistrictAwardofMerit) {
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


  if ($cDistrictAwards->UpdateNomineeRecord($FormData)) {
    // Record has been updated in database now create a audit trail
    $cDistrictAwards->CreateAudit($rowNominee, $FormData, 'NomineeIDX');
  }
  $_SESSION['feedback'] = ['type' => 'sucess', 'message' => "Nominee updated."];
  header("Location: index.php");
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="description" content="NomineePage.php">
  <script>
    function growTextarea(i, elem) {
      var elem = $(elem);
      var resizeTextarea = function(elem) {
        var scrollLeft = window.pageXOffset || (document.documentElement || document.body.parentNode || document.body).scrollLeft;
        var scrollTop = window.pageYOffset || (document.documentElement || document.body.parentNode || document.body).scrollTop;
        elem.css('height', 'auto').css('height', elem.prop('scrollHeight'));
        window.scrollTo(scrollLeft, scrollTop);
      };
      elem.on('input', function() {
        resizeTextarea($(this));
      });
      resizeTextarea($(elem));
    }

    $('.growTextarea').each(growTextarea);
  </script>
</head>

<body>

  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <p class="fs-4 d-print-none">Edit Nomination</p>
        </div>
        <!-- Page Features-->
        <?php
        $queryNominee = "SELECT DISTINCTROW FirstName, LastName, Award, Year, NomineeIDX FROM district_awards 
        WHERE NomineeIDX > 0 ORDER BY LastName, FirstName";

        $result_ByNominee = $cDistrictAwards->doQuery($queryNominee);
        if (!$result_ByNominee) {
          $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByNominees)");
        }
        ?>
        <form action="index.php?page=edit-nominee" method=post>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <label class='d-print-none' for='NomineeName'>Choose a Nominee: </label>
          <select class='form-control d-print-none' id='NomineeName' name='NomineeName'>
            <option value=\"\" </option>
            <option value=-1>Add New</option>
            <?php
            while ($rowNomineeName = $result_ByNominee->fetch_assoc()) {
              $queryAward = "SELECT * FROM awards WHERE `AwardIDX`='$rowNomineeName[Award]'";
              $ResultsAward = $cDistrictAwards->doQuery($queryAward);
              $rowAward = $ResultsAward->fetch_assoc();
              if ($rowAward)
                $Award = $rowAward['Award'];
              else {
                $strMsg = "Error: SELECT * FROM awards WHERE AwardIDX=rowNomineeName[" . $Award . "]. from " . __FILE__ . ", " . __LINE__;
                error_log($strMsg);
                exit();
              }
              echo "<option value=" . $rowNomineeName['NomineeIDX'] . ">" . $rowNomineeName['LastName'] . " " . $rowNomineeName['FirstName'] . " " . $rowNomineeName['Year'] . " " . $Award . "</option>";
              //echo "option value=" . $rowNomineeName['LastName'] . ">" . $rowNomineeName['FirstName'] . "/option";
            }
            ?>
          </select>
          <div class=py-3>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input class='btn btn-primary btn-lg d-print-none py-3' type='submit' name='SubmitNominee' value='Select Nominee' />
        </form>
      </div>
      <?php
      //#####################################################
      //
      // Check to see if user selected a Nominee.
      //
      //#####################################################
      if (
        isset($_POST['SubmitNominee']) && isset($_POST['NomineeName']) && $_POST['NomineeName'] !== '' ||
        (isset($_GET['NomineeIDX']))
      ) {

        if (isset($_POST['NomineeName']))
          $SelectedNominee = $_POST['NomineeName']; // Get name of Counselor selected
        else if (isset($_GET['NomineeIDX']))
          $SelectedNominee = $_GET['NomineeIDX'];
        // If new Nominee is selected must create a record in the database for them.
        // There is a blank record in the database with Coachid set to -1 for this.
        // Go get the Scout data

        // Go get the Nominees data
        $queryNominee = "SELECT * FROM `district_awards` WHERE NomineeIDX='$SelectedNominee'";

        if (!$Nominee = $cDistrictAwards->doQuery($queryNominee)) {
          $msg = "Error: doQuery(" . $queryNominee . ") from " . __FILE__ . ", " . __LINE__;
          error_log($msg);
          $cDistrictAwards->GotoURL("Reports.php");
          exit();
        }
        $rowNominee = $Nominee->fetch_assoc();
        $MemberID = $rowNominee['MemberID']; // Needed for History report.
        if ($rowNominee['Award'] == $cAwards::$DistrictAwardofMerit) {
          // Need to get District Award of Merit - Award dates.
          $queryNomineeDate = "SELECT * FROM `AwardofMerit` WHERE NomineeIDX='$SelectedNominee'";

          if (!$NomineeDate = $cDistrictAwards->doQuery($queryNomineeDate)) {
            $msg = "Error: doQuery(" . $queryNomineeDate . ") from " . __FILE__ . ", " . __LINE__;
            error_log($msg);
            $cDistrictAwards->GotoURL("Reports.php");
            exit();
          }
          $rowNomineeDate = $NomineeDate->fetch_assoc();
        }
      ?>
        <div class="form-nominee">
          <p style="text-align:Left"><b>Nominee Information</b></p>
          <form action="index.php?page=edit-nominee" id="add_nomination" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-row">
              <div class="col-3">
                <label>First</label>
                <input type="text" name="element_1_1" class="form-control" <?php if (strlen($rowNominee['FirstName']) > 0) echo "value=" . $rowNominee['FirstName']; ?>>
              </div>
              <div class="col-3">
                <label>Preferred Name</label>
                <input type="text" name="element_1_2" class="form-control" <?php if (strlen($rowNominee['PName']) > 0) echo "value=" . $rowNominee['PName']; ?>>
              </div>
              <div class="col">
                <label>Middle</label>
                <input type="text" name="element_1_3" class="form-control" <?php if (strlen($rowNominee['MName']) > 0) echo "value=" . $rowNominee['MName']; ?>>
              </div>
              <div class="col">
                <label>Last</label>
                <input type="text" name="element_1_4" class="form-control" <?php if (strlen($rowNominee['LastName']) > 0) echo "value=" . $rowNominee['LastName']; ?>>
              </div>
            </div>

            <div class="form-row">
              <div class="col-3">
                <label>Scouting Position</label>
                <?php
                $cDistrictAwards->GetScoutingPosition('element_2_1', $rowNominee['Position']);
                ?>
                <!-- <input type="text" name="element_6_1" class="form-control" placeholder="Currently registered in Scouting as:"> -->
              </div>
              <div class="col-4">
                <label>Unit</label>
                <?php
                // Make Unit selection a dropdown of active units in the District.
                $cDistrictAwards->GetDistrictUnits('element_2_2', $rowNominee['Unit']);
                ?>
                <!-- <input type="text" name="element_6_2" class="form-control" placeholder="Unit Type & Number i.e Troop 0317-BT"> -->
              </div>
              <div class="col-3">
                <label>BSA ID</label>
                <input type="text" name="element_2_3" class="form-control" <?php if (strlen($rowNominee['MemberID']) > 0) echo "value=" . $rowNominee['MemberID']; ?>>
              </div>
              <div class="form-check">
                <label class="form-check-label d-print-none" for="flexCheckDefault">Deleted</label><br>
                <input class="form-check-input d-print-none" type="checkbox" name="element_2_5" type="hidden" value='0' />
                <input class="form-check-input d-print-none" type="checkbox" name="element_2_4" value='1' <?php if ($rowNominee['IsDeleted'] == 1) echo "checked=checked"; ?>>
              </div>


            </div>

            <div class="form-row">
              <div class="col">
                <label>Award Year</label>
                <input type="number" name="element_3_1" class="form-control" <?php if (strlen($rowNominee['Year']) > 0) echo "value=" . $rowNominee['Year']; ?>>
              </div>
              <div class="col">
                <label>Award</label>
                <select class='form-control' id='element_3_2' name='element_3_2'>
                  <option value=""> </option>
                  <?php $cDistrictAwards->DisplayAwardsList($rowNominee['Award']); ?>
                </select>
              </div>
              <div class="col">
                <label>Status</label>
                <select class='form-control' id='element_3_3' name='element_3_3'>
                  <option value=""> </option>
                  <?php $cDistrictAwards->DisplayAwardsStatus($rowNominee['Status']); ?>
                </select>
              </div>
            </div>


            <?php if ($rowNominee['Award'] == $cAwards::$DistrictAwardofMerit) { ?>
              <p style="text-align:Left"><b>If the nominee has earned the following (please provide dates):</b></p>
              <div class="form-row">
                <div class="col-4">
                  <label>Den Leader’s Training Award or Den Leader Award</label>
                  <input type="text" name="element_7_1" class="form-control" <?php if ($rowNomineeDate && $rowNomineeDate && strlen($rowNomineeDate['DLAward']) > 0) echo "value=" . $rowNomineeDate['DLAward']; ?>>
                </div>
                <div class="col-4">
                  <label>Scouter’s Religious Award</label>
                  <input type="text" name="element_7_2" class="form-control" <?php if ($rowNomineeDate && $rowNomineeDate && strlen($rowNomineeDate['SRAward']) > 0) echo "value=" . $rowNomineeDate['SRAward']; ?>>
                </div>
                <div class="col-4">
                  <label>Scouter’s Training Award</label>
                  <input type="text" name="element_7_3" class="form-control" <?php if ($rowNomineeDate && $rowNomineeDate && $rowNomineeDate && strlen($rowNomineeDate['STAward']) > 0) echo "value=" . $rowNomineeDate['STAward']; ?>>
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <label>Den Leader Coach’s Training Award/Coach Award</label>
                  <input type="text" name="element_8_1" class="form-control" <?php if ($rowNomineeDate && $rowNomineeDate && strlen($rowNomineeDate['CoachAward']) > 0) echo "value=" . $rowNomineeDate['CoachAward']; ?>>
                </div>
                <div class="col-4">
                  <label>Silver Beaver</label>
                  <input type="text" name="element_8_2" class="form-control" <?php if ($rowNomineeDate && $rowNomineeDate && strlen($rowNomineeDate['SilverBeaver']) > 0) echo "value=" . $rowNomineeDate['SilverBeaver']; ?>>
                </div>
                <div class="col-4">
                  <label>Scouter’s Key</label>
                  <input type="text" name="element_8_3" class="form-control" <?php if ($rowNomineeDate && $rowNomineeDate && strlen($rowNomineeDate['ScouterKey']) > 0) echo "value=" . $rowNomineeDate['ScouterKey']; ?>>
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <label>Cubmaster Award</label>
                  <input type="text" name="element_9_1" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['CMAward']) > 0) echo "value=" . $rowNomineeDate['CMAward']; ?>>
                </div>
                <div class="col-4">
                  <label>Order of the Arrow</label>
                  <input type="text" name="element_9_2" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['OAAward']) > 0) echo "value=" . $rowNomineeDate['OAAward']; ?>>
                </div>
                <div class="col-4">
                  <label>Venturing Awards</label>
                  <input type="text" name="element_9_3" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['VAward']) > 0) echo "value=" . $rowNomineeDate['VAward']; ?>>
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <label>Cub Scouter Award</label>
                  <input type="text" name="element_10_1" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['CSAward']) > 0) echo "value=" . $rowNomineeDate['CSAward']; ?>>
                </div>
                <div class="col-4">
                  <label>Wood Badge</label>
                  <input type="text" name="element_10_2" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['WoodBadge']) > 0) echo "value=" . $rowNomineeDate['WoodBadge']; ?>>
                </div>
                <div class="col-4">
                  <label>Distinguished Commissioner Service Award</label>
                  <input type="text" name="element_10_3" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['DCSA']) > 0) echo "value=" . $rowNomineeDate['DCSA']; ?>>
                </div>
              </div>

              <div class="form-row">
                <div class="col-4">
                  <label>Webelos Den Leader Award</label>
                  <input type="text" name="element_11_1" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['WDLAward']) > 0) echo "value=" . $rowNomineeDate['WDLAward']; ?>>
                </div>
                <div class="col-4">
                  <label>Other (specify)</label>
                  <input type="text" name="element_11_2" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['Other1']) > 0) echo "value=" . $rowNomineeDate['Other1']; ?>>
                </div>
                <div class="col-4">
                  <label>Other (specify)</label>
                  <input type="text" name="element_11_3" class="form-control" <?php if ($rowNomineeDate && strlen($rowNomineeDate['Other2']) > 0) echo "value=" . $rowNomineeDate['Other2']; ?>>
                </div>
              </div>

              <div class="form-row">
              </div>
            <?php } ?>

            </br>
            <p><b>The noteworthy service upon which this nomination is based on:</b></p>
            <p class="d-print-none">(Furnish as much information as possible. For example: president, Rotary Club; vestryman, St. Paul’s Church; chairman, Red
              Cross campaign; vice-president, PTA; medical director, hospital; Cubmaster, 3 years; Scoutmaster, 4 years; Venturing Advisor,
              3 years; commissioner, etc.)</p>

            <div class="form-row">
              <div class="col">
                <label>Notes</label>
                <textarea name="element_14_1" class="form-control growTextarea" id="Notes" rows="10" style="height:100%;"><?php if (strlen($rowNominee['Notes']) > 0) echo $rowNominee['Notes']; ?></textarea>
              </div>
            </div>

            <div class="form-row py-4">
              <div class="col-5">
                <label>Nominated By</label>
                <?php if (strlen($rowNominee['NominatedBy']) > 0) $strBy = $rowNominee['NominatedBy'];
                else $strBy = ""; ?>
                <input type="text" name="element_15_1" class="form-control" value="<?php echo $strBy ?>">
              </div>
              <div class="col-3">
                <label>Nominated By Unit</label>
                <?php
                $cDistrictAwards->GetDistrictUnits('element_15_2', $rowNominee['NominatedByUnit']);
                ?>
              </div>
              <div class="col-3">
                <label>Nominated By Position</label>
                <?php
                $cDistrictAwards->GetScoutingPosition('element_15_3', $rowNominee['NominatedByPosition']);
                ?>
              </div>
            </div>

            <!-- save the Award IDX here is a hidden control -->
            <div class="form-row">
              <div class="col-3">
                <input type="hidden" name="element_16_1" class="form-control" value=<?php echo $rowNominee['Award']; ?>>
              </div>
            </div>
            <div class="py-3">
              <?php echo '<input type="hidden" name="NomineeIDX" value="' . $rowNominee['NomineeIDX'] . '"/>'; ?>
              <input id="saveForm2" class="btn btn-primary btn-lg d-print-none" type="submit" name="SubmitForm" value="Save" />
              <input id="saveForm2" class="btn btn-primary btn-lg d-print-none" type="submit" name="SubmitForm" value="Cancel" />
            </div>
          </form>
        </div>
      <?php } ?>
    </div>
    </div>
    <div>
      <center>
        <h4><?php echo "Nomination History" ?> </h4>

        <table>
          <thead>
            <tr>
              <th> Year</th>
              <th> First Name</th>
              <th> Last Name</th>
              <th> Award</th>
              <th> Status</th>
              <th> Unit</th>
            </tr>
          </thead>
          <?php
          if ($MemberID != "") {
            $queryNominees = "SELECT * FROM `district_awards` WHERE MemberID='$MemberID' AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Award`";
            // else
            //   $queryNominees = "SELECT * FROM `district_awards` WHERE NomineeIDX > 0 AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Award`";

            if (!$ResultNominees = $cDistrictAwards->doQuery($queryNominees)) {
              $msg = "Error: doQuery()";
              $cDistrictAwards->function_alert($msg);
            }

          ?>
          <?php
            while ($rowNominee = $ResultNominees->fetch_assoc()) {
              $AwardName = $cDistrictAwards->GetAwardName($rowNominee['Award']);
              $Status = $cDistrictAwards->GetAwardStatus($rowNominee['Status']);

              echo "<tr><td style='width:150px'>" .
                $rowNominee["Year"] . "</td><td style='width:150px'>" .
                $rowNominee["FirstName"] . "</td><td style='width:150px'>" .
                "<a href=index.php?page=edit-nominee&NomineeIDX=" . $rowNominee['NomineeIDX'] . ">" . $rowNominee['LastName'] . "</a> </td><td  style='width:500px'>" .
                $AwardName . "</td><td style='width:50px'>" .
                $Status . "</td><td style='width:150px'>" .
                $rowNominee['Unit'] . "</td></tr>";
            }
            echo "</table>";
          }
          ?>
        </table>
      </center>
    </div>
  </header>
</body>