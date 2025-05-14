    <div class="px-5">
      <div class="px-5" style="background-color: var(--scouting-lighttan);">
        <div class="form-nominee">
          <p style="text-align:Left"><b>Scout Information</b></p>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="form-scout" method="post">

            <div class="form-row">
              <div class="col-2">
                <label>First</label>
                <input type="text" name="element_1_1" class="form-control" <?php if (strlen($rowScout['FirstName']) > 0) echo "value=" . $rowScout['FirstName']; ?> />
              </div>
              <div class="col-2">
                <label>Preferred Name</label>
                <input type="text" name="element_1_2" class="form-control" <?php if (strlen($rowScout['PreferredName']) > 0) echo "value=" . $rowScout['PreferredName']; ?> />
              </div>
              <div class="col-2">
                <label>Middle</label>
                <input type="text" name="element_1_3" class="form-control" <?php if (strlen($rowScout['MiddleName']) > 0) echo "value=" . $rowScout['MiddleName']; ?> />
              </div>
              <div class="col-2">
                <label>Last</label>
                <input type="text" name="element_1_4" class="form-control" <?php if (strlen($rowScout['LastName']) > 0) echo "value=" . $rowScout['LastName']; ?> />
              </div>
              <div class="col-1 py-4">
                <div class="form-check">
                  <label class="form-check-label" for="element_1_5">Deleted</label>
                  <input class="form-check-input" type="hidden" name="element_1_5" value='0' />
                  <input class="form-check-input" type="checkbox" name="element_1_5" value='1' <?php if ($rowScout['is_deleted'] == 1) echo "checked=checked"; ?>>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="col-4">
                <label>Email</label>
                <input type="text" name="element_2_1" class="form-control" <?php if (strlen($rowScout['Email']) > 0) echo "value=" . $rowScout['Email']; ?> />
              </div>
              <div class="col-2">
                <label>Home Phone Number</label>
                <input type="text" name="element_2_2" class="form-control" <?php if (strlen($rowScout['Phone_Home']) > 0) echo "value=" . $rowScout['Phone_Home']; ?> />
              </div>
              <div class="col-2">
                <label>Mobile Phone Number</label>
                <input type="text" name="element_2_3" class="form-control" <?php if (strlen($rowScout['Phone_Mobile']) > 0) echo "value=" . $rowScout['Phone_Mobile']; ?> />
              </div>
            </div>

            <div class="form-row">
              <div class="col-3">
                <label>Street Address</label>
                <input type="text" name="element_3_1" class="form-control" <?php if (strlen($Street) > 0) echo "value='" . $Street . "'"; ?> />
              </div>
              <div class="col-3">
                <label>City</label>
                <input type="text" name="element_3_2" class="form-control" <?php if (strlen($rowScout['City']) > 0) echo "value='" . $rowScout['City'] . "'"; ?> />
              </div>
              <div class="col-1">
                <label>State</label>
                <input type="text" name="element_3_3" class="form-control" <?php if (strlen($rowScout['State']) > 0) echo "value=" . $rowScout['State']; ?> />
              </div>
              <div class="col-1">
                <label>Zip</label>
                <input type="text" name="element_3_4" class="form-control" <?php if (strlen($rowScout['Zip']) > 0) echo "value=" . $rowScout['Zip']; ?> />
              </div>
            </div>

            <div class="form-row">
              <div class="col-1">
                <label>Unit Type</label>
                <?php $cEagle->DisplayUnitType("element_4_1", $rowScout['UnitType']); ?>
              </div>
              <div class="col-1">
                <label>Unit Number</label>
                <input type="text" name="element_4_2" class="form-control" <?php if (strlen($rowScout['UnitNumber']) > 0) echo "value=" . $rowScout['UnitNumber']; ?> />
              </div>
              <div class="col-2">
                <label>District</label>
                <select class="form-control" name="element_4_3">
                  <?php $cEagle->DisplayDistrict($rowScout['District']); ?>
                </select>
              </div>
              <div class=" col-2">
                <label>Gender</label>
                <select class="form-control" name="element_4_4">
                  <?php $cEagle->DisplayGender("element_4_4", $rowScout['Gender']); ?>
                </select>
              </div>
              <div class="col-1">
                <label>Age out Date</label>
                <input type="text" name="element_4_5" class="form-control" <?php if (strlen($rowScout['AgeOutDate']) > 0) echo "value=" . $rowScout['AgeOutDate']; ?> />
              </div>
              <div class="col-1">
                <label>Member ID</label>
                <input type="text" name="element_4_6" class="form-control" <?php if (strlen($rowScout['MemberId']) > 0) echo "value=" . $rowScout['MemberId']; ?> />
              </div>
            </div>

            <div class="form-row">
              <div class="col-2">
                <label>Unit Leader First</label>
                <input type="text" name="element_5_1" class="form-control" <?php if (strlen($rowScout['ULFirst']) > 0) echo "value=" . $rowScout['ULFirst']; ?> />
              </div>
              <div class="col-2">
                <label>Unit Leader Last</label>
                <input type="text" name="element_5_2" class="form-control" <?php if (strlen($rowScout['ULLast']) > 0) echo "value=" . $rowScout['ULLast']; ?> />
              </div>
              <div class="col-2">
                <label>Unit Leader Phone</label>
                <input type="text" name="element_5_3" class="form-control" <?php if (strlen($rowScout['ULPhone']) > 0) echo "value=" . $rowScout['ULPhone']; ?> />
              </div>
              <div class="col-2">
                <label>Unit Leader Email</label>
                <input type="text" name="element_5_4" class="form-control" <?php if (strlen($rowScout['ULEmail']) > 0) echo "value=" . $rowScout['ULEmail']; ?> />
              </div>
            </div>

            <div class="form-row">
              <div class="col-2">
                <label>CC First</label>
                <input type="text" name="element_6_1" class="form-control" <?php if (strlen($rowScout['CCFirst']) > 0) echo "value=" . $rowScout['CCFirst']; ?> />
              </div>
              <div class="col-2">
                <label>CC Last</label>
                <input type="text" name="element_6_2" class="form-control" <?php if (strlen($rowScout['CCLast']) > 0) echo "value=" . $rowScout['CCLast']; ?> />
              </div>
              <div class="col-2">
                <label>CC Phone</label>
                <input type="text" name="element_6_3" class="form-control" <?php if (strlen($rowScout['CCPhone']) > 0) echo "value=" . $rowScout['CCPhone']; ?> />
              </div>
              <div class="col-2">
                <label>CC Email</label>
                <input type="text" name="element_6_4" class="form-control" <?php if (strlen($rowScout['CCEmail']) > 0) echo "value=" . $rowScout['CCEmail']; ?> />
              </div>
            </div>

            <div class="form-row">
              <div class="col-2">
                <label>Guardian First</label>
                <input type="text" name="element_6_1a" class="form-control" <?php if (strlen($rowScout['GuardianFirst']) > 0) echo "value=" . $rowScout['GuardianFirst']; ?> />
              </div>
              <div class="col-2">
                <label>Guardian Last</label>
                <input type="text" name="element_6_2a" class="form-control" <?php if (strlen($rowScout['GuardianLast']) > 0) echo "value=" . $rowScout['GuardianLast']; ?> />
              </div>
              <div class="col-2">
                <label>Guardian Phone</label>
                <input type="text" name="element_6_3a" class="form-control" <?php if (strlen($rowScout['GuardianPhone']) > 0) echo "value=" . $rowScout['GuardianPhone']; ?> />
              </div>
              <div class="col-2">
                <label>Guardian Email</label>
                <input type="text" name="element_6_4a" class="form-control" <?php if (strlen($rowScout['GuardianEmail']) > 0) echo "value=" . $rowScout['GuardianEmail']; ?> />
              </div>
              <div class="col-2">
                <label>Relationship</label>
                <select class='form-control' name='element_6_5a'>
                  <?php $cEagle->DisplayGuardianRelationship($rowScout['GuardianRelationship']); ?>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="col-1 py-4">
                <div class="form-check">
                  <label class="form-check-label" for="element_7_2">Attended Preview</label>
                  <input class="form-check-input" type="hidden" name="element_7_2" value='0' />
                  <input class="form-check-input" type="checkbox" name="element_7_2" value='1' <?php if ($rowScout['AttendedPreview'] == 1) echo "checked=checked"; ?>>
                </div>
              </div>
              <div class="col-1 py-4">
                <div class="form-check">
                  <label class="form-check-label" for="element_7_3">Project Approved</label>
                  <input class="form-check-input" type="hidden" name="element_7_3" value='0' />
                  <input class="form-check-input" type="checkbox" name="element_7_3" value='1' <?php if ($rowScout['ProjectApproved'] == 1) echo "checked=checked"; ?>>
                </div>
              </div>
              <div class="col-2">
                <label>Prospsal Approved Date</label>
                <input type="text" name="element_7_4" class="form-control" <?php if (strlen($rowScout['ProjectDate']) > 0) echo "value=" . $rowScout['ProjectDate']; ?> />
              </div>
              <div class="col-2">
                <label>Project Coach/Mentor</label>
                <?php $cEagle->DisplayCoach("element_7_5", $rowScout['Coach']); ?>
              </div>
              <div class="col-2">
                <label>Project Hours</label>
                <input type="text" name="element_7_6" class="form-control" <?php if (strlen($rowScout['ProjectHours']) > 0) echo "value=" . $rowScout['ProjectHours']; ?> />
              </div>
            </div>

            <div class="form-row">
              <div class="col-4">
                <label>Beneficiary</label>
                <input type="text" name="element_8_1" class="form-control" <?php if (strlen($rowScout['Beneficiary']) > 0) echo "value='" . $rowScout['Beneficiary'] . "'"; ?> />
              </div>
              <div class="col-4">
                <label>Project Name</label>
                <input type="text" name="element_8_2" class="form-control" <?php if (strlen($rowScout['ProjectName']) > 0) echo "value='" . $rowScout['ProjectName'] . "'"; ?> />
              </div>
            </div>

            <div class="form-row">
              <div class="col-2">
                <label>BOR</label>
                <input type="text" name="element_9_1" class="form-control" <?php if (strlen($rowScout['BOR']) > 0) echo "value=" . $rowScout['BOR']; ?> />
              </div>
              <div class="col-2">
                <label>District BOR member</label>
                <?php $cEagle->DisplayCoach("element_9_2", $rowScout['BOR_Member']); ?>
              </div>
              <div class="col-1 py-4">
                <div class="form-check">
                  <label class="form-check-label" for="element_9_3">Eagled</label>
                  <input class="form-check-input" type="hidden" name="element_9_3" value='0' />
                  <input class="form-check-input" type="checkbox" name="element_9_3" value='1' <?php if ($rowScout['Eagled'] == 1) echo "checked=checked"; ?>>
                </div>
              </div>
              <div class="col-1 py-4">
                <div class="form-check">
                  <label class="form-check-label" for="element_9_4">Aged Out</label>
                  <input class="form-check-input" type="hidden" name="element_9_4" value='0' />
                  <input class="form-check-input" type="checkbox" name="element_9_4" value='1' <?php if ($rowScout['AgedOut'] == 1) echo "checked=checked"; ?>>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="col-8">
                <label>Notes</label>
                <textarea name="element_10_1" class="form-control" id="Notes" rows="10" style="height:100%;"><?php if (strlen($rowScout['Notes']) > 0) echo $rowScout['Notes']; ?></textarea>
              </div>
            </div>

            <div class="form-row">
              <div class="col-10 py-5">
                <?php $ID = $rowScout['Scoutid']; ?>
                <?php echo '<input type="hidden" name="Scoutid" value="' . $rowScout['Scoutid'] . '"/>'; ?>
                <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value=" Save " />
                <input id="saveForm2" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Cancel" />
                <?php
                $Subject = "Eagle Rank";
                $body = "";
                $fixed_body = htmlspecialchars($body);
                $email = $rowScout['Email'] . ';' . $rowScout['ULEmail'] . ';' . $rowScout['CCEmail'] . ';' . $rowScout['GuardianEmail'];
                echo <<<EOL
                    <a href="mailto:$email?subject=$Subject">Email Scout and Leaders</a>
                    EOL;
                $Mailto = "mailto:$email?body=$fixed_body";
                ?>
              </div>
            </div>



          </form>
        </div>
      </div>
    </div>