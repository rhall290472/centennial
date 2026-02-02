<?php

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
    header('Location: index.php?page=login');
    exit;
}

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
        // 1. Get the number of counselors in the Database
        // 2. Get the Number signed uo
        // 3. Get the Number of No's
        // 4. Get the number of those no longer a MBC
        //
        //*************************************************************************/
        $qryTotalCounselors = "SELECT COUNT(*) FROM mbccounselors WHERE '1'";
        $qrySignedUp = "SELECT COUNT(*) FROM `mbccounselors` WHERE `Is_SignedUp`='1'";
        $qryIsaNo = "SELECT COUNT(*) FROM `mbccounselors` WHERE `Is_a_no`='1'";
        $qryIsNotMBC = "SELECT COUNT(*) FROM `mbccounselors` WHERE `Is_not_MBC`='1'";

        $resultTotal = $Counselor->doQuery($qryTotalCounselors);
        if (!$resultTotal) {
          exit;
        }
        $rowTotal = $resultTotal->fetch_assoc();
        $resultSignedUp = $Counselor->doQuery($qrySignedUp);
        if (!$resultSignedUp) {
          exit;
        }
        $resultIsaNo = $Counselor->doQuery($qryIsaNo);
        if (!$resultIsaNo) {
          exit;
        }
        $resultIsNotMBC = $Counselor->doQuery($qryIsNotMBC);
        if (!$resultIsNotMBC) {
          exit;
        }

        ?>

        <table class='table table-light tl1 tl2 tl3 tc4' style='width:600px;'>
          <thead>
            <th>Total</th>
            <th>Signed Up</th>
            <th>No's</th>
            <th>Not a MBC</th>
          </thead>
          <tbody>

          </tbody>
        </table>
        <!-- while ($row = $report_results->fetch_assoc()) { -->
<!--  -->
<!--  -->
        <!-- if ($MBName != $row['MBName']) { -->
        <!-- $MBName = $row['MBName']; -->
        <!-- $sqlMBName = "SELECT * FROM meritbadges WHERE MeritName ='$MBName'"; -->
        <!-- echo "</table class='table'>"; -->
        <!-- $Result_MB = self::doQuery($sqlMBName); -->
<!--  -->
<!--  -->
        <!-- while ($rowMB = $Result_MB->fetch_assoc()) { -->
<!--  -->
        <!-- echo "<h3>", $rowMB['MeritName'], "</h3>", "Requirments: ", $rowMB['RequirementsRevised'], -->
        <!-- "<a href='" . $rowMB[' URL'] . "'>" . "<img src='" . $rowMB['Logo'] . "'" . " width='50' height='50'></a>" ; -->
          <!-- } -->
          <!-- echo "<br>" ; -->
          <!-- echo "<table class='table table-light tl1 tl2 tl3 tc4 tc5' style='width:600px';>" ; -->
          <!-- echo "<td style='width:50px'>" ; -->
          <!-- echo "<td style='width:250px'>" ; -->
          <!-- echo "<td style='width:150px'>" ; -->
          <!-- echo "<td style='width:50px'>" ; -->
          <!-- echo "<td style='width:50px'>" ; -->
          <!-- echo "<td style='width:50px'>" ; -->
          <!-- echo "<tr>" ; -->
          <!-- echo "<th>Period</th>" ; -->
          <!-- echo "<th>Merit badge</th>" ; -->
          <!-- echo "<th>Counselor</th>" ; -->
          <!-- // Don't display on this page. echo "<th>Email</th>" ; -->
          <!-- echo "<th>Size</th>" ; -->
          <!-- echo "<th>Reg</th>" ; -->
          <!-- echo "<th>Room</th>" ; -->
          <!-- echo "</tr>" ; -->
          <!-- } -->
          <!-- // Get Number registered for each of the Periods. -->
          <!-- $Registered=self::GetRegisteredScouts($MBName, $row['MBPeriod']); -->
          <!-- echo "<tr><td>" . -->
<!--  -->
          <!-- $row['MBPeriod'] . "</td><td>" . -->
          <!-- $row['MBName'] . "</td><td>" . -->
          <!-- $row['FirstName'] . " " . $row['LastName'] . "</td><td>" . -->
          <!-- $row['MBCSL'] . "</td><td>" . -->
          <!-- $Registered . "</td><td>" . -->
          <!-- $row['MBRoom'] . "</td><td>" ; -->
          <!-- } -->
          <!-- echo "</table>" ; -->

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
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
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
</body>

</html>