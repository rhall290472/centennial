<?php
require_once BASE_PATH . '/src/Classes/CCounselor.php';
require_once BASE_PATH . '/src/Classes/CMBCollege.php';

$Counselor = cCounselor::getInstance();
$CMBCollege = CMBCollege::getInstance();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Counselor Selection</title>
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <style>
    .form-section {
      margin-bottom: 2rem;
    }

    .merit-badge-section {
      padding: 1rem;
      border-radius: 5px;
    }

    .error-message {
      color: red;
      font-size: 0.9em;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-3">
        <?php if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) && !$Counselor->RegistrationOpen()): ?>
          <div class="text-center">
            <img src="./images/RegistrationClosed.jpg"' alt="Registration Closed" class="img-fluid" style="max-width: 270px;" />
          </div>
          <?php exit(); ?>
        <?php endif; ?>

        <div class="form-section">
          <h4>Merit Badge College Signup</h4>
          <p>The College will be set up in periods of 2, 3, or 4 hours. Please select the length you need for your badges.</p>
          <table class="table table-bordered" style="max-width: 600px;">
            <thead>
              <tr>
                <th>2 Hour Period(s)</th>
                <th>3 Hour Period(s)</th>
                <th>4 Hour Period(s)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php echo $Counselor->GetPeriodATime($Counselor->getyear()) ? "A - " . $Counselor->GetPeriodATime($Counselor->getyear()) : ''; ?></td>
                <td><?php echo $Counselor->GetPeriodETime($Counselor->getyear()) ? "E - " . $Counselor->GetPeriodETime($Counselor->getyear()) : ''; ?></td>
                <td><?php echo $Counselor->GetPeriodABTime($Counselor->getyear()) ? "AB - " . $Counselor->GetPeriodABTime($Counselor->getyear()) : ''; ?></td>
              </tr>
              <tr>
                <td><?php echo $Counselor->GetPeriodBTime($Counselor->getyear()) ? "B - " . $Counselor->GetPeriodBTime($Counselor->getyear()) : ''; ?></td>
                <td><?php echo $Counselor->GetPeriodFTime($Counselor->getyear()) ? "F - " . $Counselor->GetPeriodFTime($Counselor->getyear()) : ''; ?></td>
                <td><?php echo $Counselor->GetPeriodCDTime($Counselor->getyear()) ? "CD - " . $Counselor->GetPeriodCDTime($Counselor->getyear()) : ''; ?></td>
              </tr>
              <tr>
                <td><?php echo $Counselor->GetPeriodCTime($Counselor->getyear()) ? "C - " . $Counselor->GetPeriodCTime($Counselor->getyear()) : ''; ?></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td><?php echo $Counselor->GetPeriodDTime($Counselor->getyear()) ? "D - " . $Counselor->GetPeriodDTime($Counselor->getyear()) : ''; ?></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
          <?php if ($Counselor->GetLunchTime($Counselor->getyear())): ?>
            <p>Lunch will be served from <?php echo $Counselor->GetLunchTime($Counselor->getyear()); ?></p>
          <?php endif; ?>
        </div>

        <div class="form-section">
          <p>To limit the number of scouts in your class, enter a value (default is 15 scouts).</p>
          <p>Specify any prerequisites in the Prerequisites field.</p>
          <p>Include any material charges in the class fee.</p>
          <p>If your name is not in the Counselor list, missing a Merit Badge, want to offer a NOVA class, or need to edit your merit badges, please <a href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">contact us</a>.</p>

          <form method="post" class="mb-4">
            <div class="row g-3">
              <div class="col-md-4">
                <label for="CounselorName" class="form-label">Choose a Counselor</label>
                <select class="form-select" id="CounselorName" name="CounselorName" required>
                  <option value="">Select Counselor</option>
                  <?php
                  $query = "SELECT DISTINCT MemberID, LastName, FirstName 
                                             FROM mbccounselors 
                                             WHERE Active='Yes' AND Is_a_no='0' 
                                             ORDER BY LastName, FirstName";
                  $result = $Counselor->doQuery($query);
                  if ($result):
                    while ($row = $result->fetch_assoc()):
                      echo "<option value='{$row['MemberID']}'>{$row['LastName']} {$row['FirstName']}</option>";
                    endwhile;
                  else:
                    $Counselor->function_alert("Error fetching counselors");
                  endif;
                  ?>
                </select>
              </div>
              <div class="col-md-2 align-self-end">
                <button type="submit" name="SubmitCounselor" class="btn btn-primary">Select Counselor</button>
              </div>
            </div>
          </form>
        </div>

        <?php if (isset($_POST['SubmitCounselor']) && !empty($_POST['CounselorName'])):
          $SelectedCounselor = $_POST['CounselorName'];
          $query = "SELECT m.*, c.FirstName, c.LastName, c.Email, c.HomePhone, c.MemberID 
                             FROM mbccounselors c 
                             INNER JOIN mbccounselormerit cm ON c.FirstName = cm.FirstName AND c.LastName = cm.LastName 
                             INNER JOIN meritbadges m ON cm.MeritName = m.MeritName 
                             WHERE c.MemberID = ? 
                             ORDER BY m.MeritName";
          $stmt = $Counselor->prepareQuery($query);
          $stmt->bind_param("s", $SelectedCounselor);
          $ResultsMB = $stmt->executeQuery();
          $Counselor->IsSignedUp($Counselor->getYear(), $row['LastName'], $row['FirstName']);
          $row = $ResultsMB->fetch_assoc();
        ?>
          <div class="form-section">
            <h5>Counselor Signup Information</h5>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="add_nomination">
              <div class="row g-3 mb-3">
                <div class="col-md-2">
                  <label for="element_1_1" class="form-label">First Name</label>
                  <input type="text" name="element_1_1" class="form-control" value="<?php echo htmlspecialchars($row['FirstName']); ?>" required>
                </div>
                <div class="col-md-2">
                  <label for="element_1_2" class="form-label">Last Name</label>
                  <input type="text" name="element_1_2" class="form-control" value="<?php echo htmlspecialchars($row['LastName']); ?>" required>
                </div>
                <div class="col-md-3">
                  <label for="element_1_3" class="form-label">Email</label>
                  <input type="email" name="element_1_3" class="form-control" value="<?php echo htmlspecialchars($row['Email']); ?>" required>
                </div>
                <div class="col-md-2">
                  <label for="element_1_4" class="form-label">Phone</label>
                  <input type="tel" name="element_1_4" class="form-control" value="<?php echo htmlspecialchars($row['HomePhone']); ?>">
                </div>
                <div class="col-md-2">
                  <label for="element_1_5" class="form-label">BSAメンバーID</label>
                  <input type="text" name="element_1_5" class="form-control" value="<?php echo htmlspecialchars($row['MemberID']); ?>" required>
                </div>
              </div>

              <?php for ($i = 1; $i <= 4; $i++):
                $bgColor = $i % 2 ? 'var(--scouting-tan)' : 'var(--scouting-darktan)';
              ?>
                <div class="merit-badge-section" style="background-color: <?php echo $bgColor; ?>">
                  <h6><?php echo ordinal($i); ?> Merit Badge</h6>
                  <div class="row g-3">
                    <div class="col-md-3">
                      <label for="MB<?php echo $i; ?>Name" class="form-label">Merit Badge Name</label>
                      <select class="form-select" id="MB<?php echo $i; ?>Name" name="MB<?php echo $i; ?>Name">
                        <option value="">Select Badge</option>
                        <?php
                        mysqli_data_seek($ResultsMB, 0);
                        $FirstBadgeFound = false;
                        while ($rowCerts = $ResultsMB->fetch_assoc()):
                          $selected = $Counselor->MB_Match($rowCerts['MeritName'], $i) && !$FirstBadgeFound ? 'selected' : '';
                          if ($selected) $FirstBadgeFound = true;
                          echo "<option $selected value='" . htmlspecialchars($rowCerts['MeritName']) . "'>" . htmlspecialchars($rowCerts['MeritName']) . "</option>";
                        endwhile;
                        ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label for="MB<?php echo $i; ?>Period" class="form-label">Period</label>
                      <select class="form-select" id="MB<?php echo $i; ?>Period" name="MB<?php echo $i; ?>Period">
                        <option value="">Select Period</option>
                        <?php $Counselor->DisplayPeriods($i); ?>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label for="MB<?php echo $i; ?>CSL" class="form-label">Class Size</label>
                      <?php $Counselor->Display_ClassSize("MB{$i}CSL", $i); ?>
                    </div>
                    <div class="col-md-2">
                      <label for="MB<?php echo $i; ?>Fee" class="form-label">Class Fee</label>
                      <?php $Counselor->Display_ClassFee("MB{$i}Fee", $i); ?>
                    </div>
                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["Role"] === "Admin"): ?>
                      <div class="col-md-2">
                        <label for="MB<?php echo $i; ?>Room" class="form-label">Room</label>
                        <?php $Counselor->Display_ClassRoom("MB{$i}Room", $i); ?>
                      </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                      <label for="MB<?php echo $i; ?>Prerequisities" class="form-label">Prerequisites</label>
                      <?php $Counselor->Display_Prerequisities("MB{$i}Prerequisities", $i); ?>
                    </div>
                    <div class="col-md-3">
                      <label for="MB<?php echo $i; ?>Notes" class="form-label">Notes</label>
                      <?php $Counselor->Display_Notes("MB{$i}Notes", $i); ?>
                    </div>
                  </div>
                </div>
              <?php endfor; ?>

              <div class="text-center mt-3">
                <input type="hidden" name="form_id" value="22772">
                <button type="submit" name="SubmitForm" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php
  if (isset($_POST['SubmitForm'])) {
    $ErrorFlag = false;
    for ($i = 1; $i <= 4; $i++) {
      if (isset($_POST["MB{$i}Name"]) && !empty($_POST["MB{$i}Name"])) {
        if (empty($Counselor->GetFormData("MB{$i}Period"))) {
          $Counselor->function_alert("ERROR: A period must be selected for Merit Badge $i");
          $ErrorFlag = true;
        }
      }
    }

    if (!$ErrorFlag) {
      $FirstName = $Counselor->GetFormData('element_1_1');
      $LastName = $Counselor->GetFormData('element_1_2');
      $Email = $Counselor->GetFormData('element_1_3');
      $Phone = $Counselor->GetFormData('element_1_4');
      $BSAId = $Counselor->GetFormData('element_1_5');
      $MBCollegeName = $Counselor->getYear();

      if ($Counselor->IsSignedUp($MBCollegeName, $LastName, $FirstName)) {
        $Counselor->Delete($MBCollegeName);
      }
      $Counselor->AddInfo($FirstName, $LastName, $Email, $Phone, $BSAId, $MBCollegeName);

      for ($i = 1; $i <= 4; $i++) {
        if (isset($_POST["MB{$i}Name"]) && !empty($_POST["MB{$i}Name"])) {
          $MBName = $Counselor->GetFormData("MB{$i}Name");
          $MBPeriod = $Counselor->GetFormData("MB{$i}Period");
          $MBClassSize = $Counselor->GetFormData("MB{$i}CSL");
          $MBFee = $Counselor->GetFormData("MB{$i}Fee");
          $MBRoom = $Counselor->GetFormData("MB{$i}Room");
          $MBPrerequisities = $Counselor->RemoveNewLine(addslashes($Counselor->GetFormData("MB{$i}Prerequisities")));
          $MBNotes = $Counselor->RemoveNewLine(addslashes($Counselor->GetFormData("MB{$i}Notes")));

          $Counselor->AddMBClass($MBName, $MBPeriod, $MBClassSize, $MBFee, $MBRoom, $MBPrerequisities, $MBNotes);
        }
      }

      $Counselor->function_alert("$FirstName $LastName Thank you for supporting the Merit Badge College");
      $Counselor->GotoURL('ViewSchedule.php');
      exit;
    }
  }

  function ordinal($number)
  {
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
      return $number . 'th';
    else
      return $number . $ends[$number % 10];
  }
  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
