<?php
if (!session_id()) {
  session_start();
}

// The code below will keep someone from just typing in the URL, if they are not logged in
// as a Admin user they will be sent back to the home page.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");

  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<body>


  <?php
  $CMeritBadge = CMeritBadges::getInstance();
  $querySelectedMeritBadges = "SELECT * FROM meritbadges ORDER BY MeritName";
  $result_ByMeritBadge = $CMeritBadge->doQuery($querySelectedMeritBadges);
  ?>
  <form method=post>
  <input type='hidden' name='csrf_token' value='<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>'>
  <label for='MeritName'>Choose a MeritBadge: </label>
  <select class='selectWrapper' id='MeritBadgeName' name='MeritBadgeName' >
  <option value=""> </option>
  <?php
  while ($rowCerts = $result_ByMeritBadge->fetch_assoc()) {
    // Strip spaces out of the merit badge name
    $MeritBadgeNum = (string)$rowCerts['MB_ID'];
    if ($rowCerts['Current'] == "0")
      $Formatter = "disabled";
    else
      $Formatter = "";
    echo "<option value='$MeritBadgeNum' " . $Formatter . ">" . $rowCerts['MeritName'] . "</option>";
  }
  echo '</select>';
  echo "<input class='btn btn-primary btn-sm' type='submit' name='SubmitMeritBadge' value='Submit Merit Badge'/>";
  echo "</form>";
  // Check if user has selected a merit badge.
  if (isset($_POST['SubmitMeritBadge']) && isset($_POST['MeritBadgeName']) && $_POST['MeritBadgeName'] !== '') {
    $SelectedMeritBadge = $_POST['MeritBadgeName'];
    $queryMeritBadge = "SELECT * FROM meritbadges WHERE MB_ID LIKE ";
    //Create a sql statement to select chosen Counselor
    $sql = sprintf("%s '%s'", $queryMeritBadge, $SelectedMeritBadge);
    if ($Results = $CMeritBadge->doQuery($sql)) {
    } else {
      $msg = "Error: MeritQuery() - " . $sql;
      $CMeritBadge->function_alert($msg);
    }
    $row = $Results->fetch_assoc();
  ?>

    <div id="form_container">
      <form id="form_22772" class="appnitro" method="post" action="index.php?page=updatemeritbadge">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form_description">
        </div>
        <ul>
          <li id="li_1">
            <span>
              <?php
              $MeritName = $row['MeritName'];
              ?>
              <label class="description" for="element_1_1">Merit Name </label>
              <input type="text" size="35" name="element_1_1" value=<?php echo "'$MeritName'"; ?> />
              <label>Merit Name</label>
            </span>
            <span>
              <?php $MB_ID = isset($row['MB_ID']) ? $row['MB_ID'] : null; ?>
              <label class="description" for="element_1_2">ID </label>
              <input id="ID" name="ID" class="element text" maxlength="255" size="4" value=<?php echo "'$MB_ID'"; ?> />
              <label>ID#</label>
            </span>
            <span>
              <?php $PhampletSKU = isset($row['PhampletSKU']) ? $row['PhampletSKU'] : null; ?>
              <label class="description" for="element_1_3">Phamplet SKU </label>
              <input id="PhampletSKU" name="PhampletSKU" class="element text" type="text" maxlength="255" size="14" value=<?php echo "'$PhampletSKU'"; ?> />
              <label>SKU</label>
            </span>
            <span>
              <?php $PhampletRevised = isset($row['PhampletRevised']) ? $row['PhampletRevised'] : null; ?>
              <label class="description" for="element_1_4">Phamplet Revised </label>
              <input id="PhampletRevised" name="PhampletRevised" class="element text" type="text" maxlength="255" size="14" value=<?php echo "'$PhampletRevised'"; ?> />
              <label>Revised</label>
            </span>
            <span>
              <?php $RequirementsRevised = isset($row['RequirementsRevised']) ? $row['RequirementsRevised'] : null; ?>
              <label class="description" for="element_1_4">Requirements Revised </label>
              <input id="RequirementsRevised" name="RequirementsRevised" class="element text" type="text" maxlength="255" size="14" value=<?php echo "'$RequirementsRevised'"; ?> />
              <label>Revised</label>
            </span>
          </li>

          <li id="li_2">
            <span>
              <label class="description" for="element_2_1">Required </label>
              <select class='selectWrapper' id='element_2_1' name='element_2_1'>
                <?php
                $strSelected = !strcmp("", $row['Required']) ? "selected" : "";
                echo sprintf("<option %s value=''></option>", $strSelected);
                $strSelected = !strcmp("Y", $row['Required']) ? "selected" : "";
                echo sprintf("<option %s value='Y'>Yes</option>", $strSelected);
                $strSelected = !strcmp("N", $row['Required']) ? "selected" : "";
                echo sprintf("<option %s value='N'>No</option>", $strSelected);
                ?>
              </select>
              <label for="element_2_1">for Eagle</label>
            </span>
            <span>
              <label class="description" for="element_2_2">Current </label>
              <select class='selectWrapper' id='element_2_2' name='element_2_2'>
                <?php
                $strSelected = !strcmp("", $row['Current']) ? "selected" : "";
                echo sprintf("<option %s value=''></option>", $strSelected);
                $strSelected = !strcmp("1", $row['Current']) ? "selected" : "";
                echo sprintf("<option %s value='1'>Yes</option>", $strSelected);
                $strSelected = !strcmp("0", $row['Current']) ? "selected" : "";
                echo sprintf("<option %s value='0'>No</option>", $strSelected);
                ?>
              </select>
              <label for="element_2_2">Active</label>
            </span>
            <span>
              <label class="description" for="element_2_3">Eagle </label>
              <select class='selectWrapper' id='element_2_3' name='element_2_3'>
                <?php
                $strSelected = !strcmp("", $row['Eagle']) ? "selected" : "";
                echo sprintf("<option %s value=''></option>", $strSelected);
                $strSelected = !strcmp("Yes", $row['Eagle']) ? "selected" : "";
                echo sprintf("<option %s value='Yes'>Yes</option>", $strSelected);
                $strSelected = !strcmp("No", $row['Eagle']) ? "selected" : "";
                echo sprintf("<option %s value='No'>No</option>", $strSelected);
                ?>
              </select>
              <label for="element_2_3">Required</label>
            </span>
            <span>
              <?php $SpecialTraining1 = is_null($row['SpecialTraining1']) ? $row['SpecialTraining1'] : "NA"; ?>
              <label class="description" for="element_2_4">SpecialTraining1 </label>
              <select class='selectWrapper' id='element_2_4' name='element_2_4'>
                <?php
                $strSelected = !strcmp("", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value=''></option>", $strSelected);
                $strSelected = !strcmp("ClimbOnSafety", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='ClimbOnSafety'>Climb On Safety</option>", $strSelected);
                $strSelected = !strcmp("WinterSportsSafety", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='WinterSportsSafety'>Winter Sports Safety</option>", $strSelected);
                $strSelected = !strcmp("AquaticsInstructor", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='AquaticsInstructor'>Aquatics Instructor</option>", $strSelected);
                $strSelected = !strcmp("CanoeingInstructor", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='CanoeingInstructor'>Canoeing Instructor</option>", $strSelected);
                $strSelected = !strcmp("PaddleCraftSafetyInstructor", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='PaddleCraftSafetyInstructor'>Paddle Craft Safety Instructor</option>", $strSelected);
                $strSelected = !strcmp("KayakingInstructor", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='KayakingInstructor'>Kayaking Instructor</option>", $strSelected);
                $strSelected = !strcmp("RedCrossFirstAid", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='RedCrossFirstAid'>Red Cross First Aid/CPR/AED</option>", $strSelected);
                $strSelected = !strcmp("SafetyAfloat", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='SafetyAfloat'>Safety Afloat</option>", $strSelected);
                $strSelected = !strcmp("SafeSwimDefense", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='SafeSwimDefense'>Safe Swim Defense</option>", $strSelected);
                $strSelected = !strcmp("WhitewaterCanoeing", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='WhitewaterCanoeing'>Whitewater Canoeing</option>", $strSelected);
                $strSelected = !strcmp("USAArchery", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='USAArchery'>USA Archery</option>", $strSelected);
                $strSelected = !strcmp("NationalFieldArchery", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='NationalFieldArchery'>National Field Archery</option>", $strSelected);
                $strSelected = !strcmp("RifleShootingInstructor", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='RifleShootingInstructor'>NRA Rifle Shooting Instructor</option>", $strSelected);
                $strSelected = !strcmp("ShotgunInstructor", $row['SpecialTraining1']) ? "selected" : "";
                echo sprintf("<option %s value='ShotgunInstructor'>NRA Shotgun Instructor</option>", $strSelected);
                ?>
              </select>
            </span>
            <span>
              <?php $SpecialTraining2 = is_null($row['SpecialTraining2']) ? $row['SpecialTraining2'] : "NA"; ?>
              <label class="description" for="element_2_5">SpecialTraining2 </label>
              <select class='selectWrapper' id='element_2_5' name='element_2_5'>
                <?php
                $strSelected = !strcmp("", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value=''></option>", $strSelected);
                $strSelected = !strcmp("ClimbOnSafety", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='ClimbOnSafety'>Climb On Safety</option>", $strSelected);
                $strSelected = !strcmp("WinterSportsSafety", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='WinterSportsSafety'>Winter Sports Safety</option>", $strSelected);
                $strSelected = !strcmp("AquaticsInstructor", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='AquaticsInstructor'>Aquatics Instructor</option>", $strSelected);
                $strSelected = !strcmp("CanoeingInstructor", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='CanoeingInstructor'>Canoeing Instructor</option>", $strSelected);
                $strSelected = !strcmp("PaddleCraftSafetyInstructor", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='PaddleCraftSafetyInstructor'>Paddle Craft Safety Instructor</option>", $strSelected);
                $strSelected = !strcmp("KayakingInstructor", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='KayakingInstructor'>Kayaking Instructor</option>", $strSelected);
                $strSelected = !strcmp("RedCrossFirstAid", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='RedCrossFirstAid'>Red Cross First Aid/CPR/AED</option>", $strSelected);
                $strSelected = !strcmp("SafetyAfloat", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='SafetyAfloat'>Safety Afloat</option>", $strSelected);
                $strSelected = !strcmp("SafeSwimDefense", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='SafeSwimDefense'>Safe Swim Defense</option>", $strSelected);
                $strSelected = !strcmp("WhitewaterCanoeing", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='WhitewaterCanoeing'>Whitewater Canoeing</option>", $strSelected);
                $strSelected = !strcmp("USAArchery", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='USAArchery'>USA Archery</option>", $strSelected);
                $strSelected = !strcmp("NationalFieldArchery", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='NationalFieldArchery'>National Field Archery</option>", $strSelected);
                $strSelected = !strcmp("RifleShootingInstructor", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='RifleShootingInstructor'>NRA Rifle Shooting Instructor</option>", $strSelected);
                $strSelected = !strcmp("ShotgunInstructor", $row['SpecialTraining2']) ? "selected" : "";
                echo sprintf("<option %s value='ShotgunInstructor'>NRA Shotgun Instructor</option>", $strSelected);
                ?>
              </select>
            </span>
          </li>
          <li id="li_3">
            <span>
              <?php $URL = isset($row['URL']) ? $row['URL'] : null; ?>
              <label class="URL" for="URL">URL </label>
              <input type="text" size="85" name="element_3_1" value=<?php echo "'$URL'"; ?> />
              <label for="URL">URL for Merit Badge Requirements</label>
            </span>
          </li>
          <li id="li_4">
            <span>
              <?php $URLLogo = isset($row['URL']) ? $row['Logo'] : null; ?>
              <label class="URL" for="URL">URL </label>
              <input type="text" size="85" name="element_4_1" value=<?php echo "'$URLLogo'"; ?> />
              <label for="URL">URL for Merit Badge logo</label>
            </span>
          </li>


          <li id="li_15">
            <label class="description" for="SpecialTraining">SpecialTraining </label>
            <?php $SpecialTraining = isset($row['SpecialTraining']) ? $row['SpecialTraining'] : null; ?>
            <div>
              <textarea cols="80" rows="12" id="SpecialTraining" name="SpecialTraining"><?php echo "$SpecialTraining"; ?></textarea>
            </div>
            <label for="SpecialTraining">Wording from the Guide to Advancment</label>
          </li>




          <li id="li_16">
            <label class="description" for="Notes">Notes </label>
            <?php $Notes = isset($row['Notes_MB']) ? $row['Notes_MB'] : null; ?>
            <div>
              <textarea cols="80" rows="12" id="Notes" name="Notes"><?php echo "$Notes"; ?></textarea>
            </div>
          </li>


          <li class="buttons">
            <input type="hidden" name="form_id" value="22772" />
            <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
            <!-- <input id="saveForm" class="button_text" type="submit" name="delete" value="Delete" /> -->
          </li>

        </ul>
      </form>
      <div id="footer">
      </div>
    </div>
    <img id="bottom" src="bottom.png" alt="">

  <?php
  }
  ?>
</body>

</html>