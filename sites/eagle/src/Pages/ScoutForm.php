<?php
/*
 * ScoutForm.php: Form for editing scout details in the Centennial District Advancement website.
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */

// Ensure variables are defined
$rowScout = $rowScout ?? [];
$Street = $Street ?? '';
$Beneficiary = $Beneficiary ?? '';
$ProjectName = $ProjectName ?? '';
?>

<div class="px-5">
  <div class="px-5" style="background-color: var(--scouting-lighttan);">
    <div class="form-nominee">
      <p style="text-align:left"><b>Scout Information</b></p>
      <form action="index.php?page=edit-scout" id="form-scout" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
        <input type="hidden" name="Scoutid" value="<?php echo htmlspecialchars($rowScout['Scoutid'] ?? ''); ?>">

        <div class="form-row">
          <div class="col-3">
            <label>First</label>
            <input type="text" name="element_1_1" class="form-control" value="<?php echo htmlspecialchars($rowScout['FirstName'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Preferred Name</label>
            <input type="text" name="element_1_2" class="form-control" value="<?php echo htmlspecialchars($rowScout['PreferredName'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Middle</label>
            <input type="text" name="element_1_3" class="form-control" value="<?php echo htmlspecialchars($rowScout['MiddleName'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>Last</label>
            <input type="text" name="element_1_4" class="form-control" value="<?php echo htmlspecialchars($rowScout['LastName'] ?? ''); ?>" />
          </div>
          <div class="col-1 py-4">
            <div class="form-check">
              <label class="form-check-label" for="element_1_5">Deleted</label>
              <input class="form-check-input" type="hidden" name="element_1_5" value="0" />
              <input class="form-check-reverse" type="checkbox" name="element_1_5" id="element_1_5" value="1" <?php if (($rowScout['is_deleted'] ?? 0) == 1) echo "checked"; ?> />
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="col-4">
            <label>Email</label>
            <input type="email" name="element_2_1" class="form-control" value="<?php echo htmlspecialchars($rowScout['Email'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>Home Phone Number</label>
            <input type="tel" name="element_2_2" class="form-control" value="<?php echo htmlspecialchars($rowScout['Phone_Home'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>Mobile Phone Number</label>
            <input type="tel" name="element_2_3" class="form-control" value="<?php echo htmlspecialchars($rowScout['Phone_Mobile'] ?? ''); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="col-3">
            <label>Street Address</label>
            <input type="text" name="element_3_1" class="form-control" value="<?php echo htmlspecialchars($Street); ?>" />
          </div>
          <div class="col-3">
            <label>City</label>
            <input type="text" name="element_3_2" class="form-control" value="<?php echo htmlspecialchars($rowScout['City'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>State</label>
            <input type="text" name="element_3_3" class="form-control" value="<?php echo htmlspecialchars($rowScout['State'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Zip</label>
            <input type="text" name="element_3_4" class="form-control" value="<?php echo htmlspecialchars($rowScout['Zip'] ?? ''); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label>Unit Type</label>
            <?php $cEagle->DisplayUnitType("element_4_1", $rowScout['UnitType'] ?? ''); ?>
          </div>
          <div class="col-2">
            <label>Unit Number</label>
            <input type="text" name="element_4_2" class="form-control" value="<?php echo htmlspecialchars($rowScout['UnitNumber'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>District</label>
            <select class="form-control" name="element_4_3">
              <?php $cEagle->DisplayDistrict($rowScout['District'] ?? ''); ?>
            </select>
          </div>
          <div class="col-2">
            <label>Gender</label>
            <select class="form-control" name="element_4_4">
              <?php $cEagle->DisplayGender("element_4_4", $rowScout['Gender'] ?? ''); ?>
            </select>
          </div>
          <div class="col-2">
            <label>Age out Date</label>
            <input type="text" name="element_4_5" class="form-control" value="<?php echo htmlspecialchars($rowScout['AgeOutDate'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Member ID</label>
            <input type="text" name="element_4_6" class="form-control" value="<?php echo htmlspecialchars($rowScout['MemberId'] ?? ''); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label>Unit Leader First</label>
            <input type="text" name="element_5_1" class="form-control" value="<?php echo htmlspecialchars($rowScout['ULFirst'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Unit Leader Last</label>
            <input type="text" name="element_5_2" class="form-control" value="<?php echo htmlspecialchars($rowScout['ULLast'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>Unit Leader Phone</label>
            <input type="tel" name="element_5_3" class="form-control" value="<?php echo htmlspecialchars($rowScout['ULPhone'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>Unit Leader Email</label>
            <input type="email" name="element_5_4" class="form-control" value="<?php echo htmlspecialchars($rowScout['ULEmail'] ?? ''); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label>CC First</label>
            <input type="text" name="element_6_1" class="form-control" value="<?php echo htmlspecialchars($rowScout['CCFirst'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>CC Last</label>
            <input type="text" name="element_6_2" class="form-control" value="<?php echo htmlspecialchars($rowScout['CCLast'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>CC Phone</label>
            <input type="tel" name="element_6_3" class="form-control" value="<?php echo htmlspecialchars($rowScout['CCPhone'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>CC Email</label>
            <input type="email" name="element_6_4" class="form-control" value="<?php echo htmlspecialchars($rowScout['CCEmail'] ?? ''); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label>Guardian First</label>
            <input type="text" name="element_6_1a" class="form-control" value="<?php echo htmlspecialchars($rowScout['GuardianFirst'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Guardian Last</label>
            <input type="text" name="element_6_2a" class="form-control" value="<?php echo htmlspecialchars($rowScout['GuardianLast'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Guardian Phone</label>
            <input type="tel" name="element_6_3a" class="form-control" value="<?php echo htmlspecialchars($rowScout['GuardianPhone'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Guardian Email</label>
            <input type="email" name="element_6_4a" class="form-control" value="<?php echo htmlspecialchars($rowScout['GuardianEmail'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label>Relationship</label>
            <select class="form-control" name="element_6_5a">
              <?php $cEagle->DisplayGuardianRelationship($rowScout['GuardianRelationship'] ?? ''); ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="col-2 py-2">
            <div class="form-check">
              <label class="form-check-label" for="element_7_2">Attended Preview</label>
              <input class="form-check-input" type="hidden" name="element_7_2" value="0" />
              <input class="form-check-reverse" type="checkbox" name="element_7_2" id="element_7_2" value="1" <?php if (($rowScout['AttendedPreview'] ?? 0) == 1) echo "checked"; ?> />
            </div>
          </div>
          <div class="col-2 py-2">
            <div class="form-check">
              <label class="form-check-label" for="element_7_3">Project Approved</label>
              <input class="form-check-input" type="hidden" name="element_7_3" value="0" />
              <input class="form-check-reverse" type="checkbox" name="element_7_3" id="element_7_3" value="1" <?php if (($rowScout['ProjectApproved'] ?? 0) == 1) echo "checked"; ?> />
            </div>
          </div>
          <div class="col-3">
            <label>Proposal Approved Date</label>
            <input type="text" name="element_7_4" class="form-control" value="<?php echo htmlspecialchars($rowScout['ProjectDate'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>Project Coach/Mentor</label>
            <?php $cEagle->DisplayCoach("element_7_5", $rowScout['Coach'] ?? ''); ?>
          </div>
          <div class="col-2">
            <label>Project Hours</label>
            <input type="text" name="element_7_6" class="form-control" value="<?php echo htmlspecialchars($rowScout['ProjectHours'] ?? ''); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="col-4">
            <label>Beneficiary</label>
            <input type="text" name="element_8_1" class="form-control" value="<?php echo htmlspecialchars($Beneficiary); ?>" />
          </div>
          <div class="col-8">
            <label>Project Name</label>
            <input type="text" name="element_8_2" class="form-control" value="<?php echo htmlspecialchars($ProjectName); ?>" />
          </div>
        </div>

        <div class="form-row">
          <div class="col-2">
            <label>BOR</label>
            <input type="text" name="element_9_1" class="form-control" value="<?php echo htmlspecialchars($rowScout['BOR'] ?? ''); ?>" />
          </div>
          <div class="col-3">
            <label>District BOR Member</label>
            <?php $cEagle->DisplayCoach("element_9_2", $rowScout['BOR_Member'] ?? ''); ?>
          </div>
          <div class="col-2 py-4">
            <div class="form-check">
              <label class="form-check-label" for="element_9_3">Eagled</label>
              <input class="form-check-input" type="hidden" name="element_9_3" value="0" />
              <input class="form-check-reverse" type="checkbox" name="element_9_3" id="element_9_3" value="1" <?php if (($rowScout['Eagled'] ?? 0) == 1) echo "checked"; ?> />
            </div>
          </div>
          <div class="col-2 py-4">
            <div class="form-check">
              <label class="form-check-label" for="element_9_4">Aged Out</label>
              <input class="form-check-input" type="hidden" name="element_9_4" value="0" />
              <input class="form-check-reverse" type="checkbox" name="element_9_4" id="element_9_4" value="1" <?php if (($rowScout['AgedOut'] ?? 0) == 1) echo "checked"; ?> />
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="col-12">
            <label>Notes</label>
            <textarea name="element_10_1" class="form-control" id="Notes" rows="10"><?php echo htmlspecialchars($rowScout['Notes'] ?? ''); ?></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="col-10 py-5">
            <input id="saveForm" class="btn btn-primary btn-sm" type="submit" name="SubmitForm" value="Save" />
            <input id="cancelForm" class="btn btn-secondary btn-sm" type="submit" name="SubmitForm" value="Cancel" />
            <?php
            $Subject = "Eagle Rank";
            $body = "";
            $fixed_body = htmlspecialchars($body);
            $email = implode(';', array_filter([
              $rowScout['Email'] ?? '',
              $rowScout['ULEmail'] ?? '',
              $rowScout['CCEmail'] ?? '',
              $rowScout['GuardianEmail'] ?? ''
            ]));
            if ($email) {
              echo "<a href=\"mailto:$email?subject=$Subject&body=$fixed_body\" class=\"ml-3\">Email Scout and Leaders</a>";
            }
            ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>