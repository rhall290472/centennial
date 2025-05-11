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

        // Only display Merit Badge avaiable at the College
        $sqlMB = "SELECT DISTINCTROW `MBName` FROM college_counselors WHERE college='$CollegeYear' ORDER BY `MBName` ASC";
        if (!$CollegeMBs = $Scout->doQuery($sqlMB)) {
        $msg = "Error: MeritQuery()";
        $Scout->function_alert($msg);
        }
        ?>
        <hr />
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
          <div class="col-1">
            <label class="description" for="element_1_3">Scout Email </label>
            <input id="element_1_3" name="element_1_3" class="form-control" type="email" maxlength="255" size="50"
              <?php if (strlen($EmailScout) > 0) echo "value=" . $EmailScout; ?> />
            <label>Email</label>
          </div>
          <div class="col-1">
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
          <div class="col-1">
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
              <?php if (strlen($FirstName) > 0) echo "value=" . $FirstName; ?> />
            <label>First</label>
          </div>
          <div class="col-1">
            <label class="description" for="element_2_2"> Name</label>
            <input id="element_2_3" name="element_2_3" class="form-control" maxlength="255" size="8"
              <?php if (strlen($FirstName) > 0) echo "value=" . $LastName; ?> />
            <label>Last</label>
          </div>
          <div class="col-1">
            <label class="description" for="element_2_3">Counselor Email </label>
            <input id="element_2_3" name="element_2_3" class="form-control large" type="email" maxlength="255" size="50"
              <?php if (strlen($FirstName) > 0) echo "value=" . $Email; ?> />
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
        <?php list($FirstName, $LastName, $Email) = $Scout->GetCounselorData($ScoutsMBs, 1); ?>
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
              <?php if (strlen($FirstName) > 0) echo "value=" . $FirstName; ?> />
            <label>First</label>
          </div>
          <div class=col-1>
            <label class="description" for="element_3_4"> Name</label>
            <input id="element_3_4" name="element_3_4" class="form-control" maxlength="255" size="8"
              <?php if (strlen($LastName) > 0) echo "value=" . $LastName; ?> />
            <label>Last</label>
          </div>
          <div class=col-1>
            <label class="description" for="element_3_5">Counselor Email </label>
            <input id="element_3_5" name="element_3_5" class="form-control" type="email" maxlength="255" size="50"
              <?php if (strlen($Email) > 0) echo "value=" . $Email; ?> />
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
        <?php list($FirstName, $LastName, $Email) = $Scout->GetCounselorData($ScoutsMBs, 2); ?>
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
              <?php if (strlen($FirstName) > 0) echo "value=" . $FirstName; ?> />
            <label>First</label>
          </div>
          <div class=col-1>
            <label class="description" for="element_4_4"> Name</label>
            <input id="element_4_4" name="element_4_4" class="form-control" maxlength="255" size="8"
              <?php if (strlen($FirstName) > 0) echo "value=" . $LastName; ?> />
            <label>Last</label>
          </div>
          <div class=col-1>
            <label class="description" for="element_4_5">Counselor Email </label>
            <input id="element_4_5" name="element_4_5" class="form-control large" type="email" maxlength="255" size="50"
              <?php if (strlen($LastName) > 0) echo "value=" . $Email; ?> />
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
        <?php list($FirstName, $LastName, $Email) = $Scout->GetCounselorData($ScoutsMBs, 3); ?>
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
              <?php if (strlen($FirstName) > 0) echo "value=" . $FirstName; ?> />
            <label>First</label>
          </div>
          <div class="col-1">
            <label class="description" for="element_5_4"> Name</label>
            <input id="element_5_4" name="element_5_4" class="form-control" maxlength="255" size="8"
              <?php if (strlen($LastName) > 0) echo "value=" . $LastName; ?> />
            <label>Last</label>
          </div>
          <div class="col-1">
            <label class="description" for="element_5_5">Counselor Email </label>
            <input id="element_5_5" name="element_5_5" class="form-control large" type="email" maxlength="255" size="50"
              <?php if (strlen($Email) > 0) echo "value=" . $Email; ?> />
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
        <div class="row">
          <div class="col-1" style="text-align: center;">
            <input type="hidden" name="form_id" value="22772" />
            <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="SubmitForm" />
          </div>
        </div>