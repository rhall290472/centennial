<?php
require_once BASE_PATH . '/src/Classes/CCounselor.php';
require_once BASE_PATH . '/src/Classes/CMBCollege.php';

$Counselor = CCounselor::getInstance();  // Fixed class name case (was cCounselor)
$CMBCollege = CMBCollege::getInstance();


// Form submission handling
if (isset($_POST['SubmitForm'])) {
  $ErrorFlag = false;
  for ($i = 1; $i <= 4; $i++) {
    if (!empty($_POST["MB{$i}Name"]) && empty($Counselor->GetFormData("MB{$i}Period"))) {
      $Counselor->function_alert("ERROR: A period must be selected for Merit Badge $i");
      $ErrorFlag = true;
    }
  }

  if (!$ErrorFlag) {
    $FirstName = $Counselor->GetFormData('element_1_1');
    $LastName  = $Counselor->GetFormData('element_1_2');
    $Email     = $Counselor->GetFormData('element_1_3');
    $Phone     = $Counselor->GetFormData('element_1_4');
    $BSAId     = $Counselor->GetFormData('element_1_5');
    $MBCollegeName = $Counselor->getYear();

    if ($Counselor->IsSignedUp($MBCollegeName, $LastName, $FirstName)) {
      $Counselor->Delete($MBCollegeName);
    }

    $Counselor->AddInfo($FirstName, $LastName, $Email, $Phone, $BSAId, $MBCollegeName);

    for ($i = 1; $i <= 4; $i++) {
      if (!empty($_POST["MB{$i}Name"])) {
        $MBName        = $Counselor->GetFormData("MB{$i}Name");
        $MBPeriod      = $Counselor->GetFormData("MB{$i}Period");
        $MBClassSize   = $Counselor->GetFormData("MB{$i}CSL");
        $MBFee         = $Counselor->GetFormData("MB{$i}Fee");
        $MBRoom        = $Counselor->GetFormData("MB{$i}Room");
        $MBPrerequisities = $Counselor->RemoveNewLine(addslashes($Counselor->GetFormData("MB{$i}Prerequisities")));
        $MBNotes       = $Counselor->RemoveNewLine(addslashes($Counselor->GetFormData("MB{$i}Notes")));

        $Counselor->AddMBClass($MBName, $MBPeriod, $MBClassSize, $MBFee, $MBRoom, $MBPrerequisities, $MBNotes);
      }
    }

    if (!$ErrorFlag) {
      redirectWithMessage("index.php?page=view-schedule", "success", htmlspecialchars($FirstName . ' ' . $LastName) . ' - Thank you for supporting the Merit Badge College');
    } else {
      redirectWithMessage("index.php?page=home", "danger", 'Failed to save your reponses. Please try again.');
    }
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Counselor Selection</title>

  <!-- Bootstrap CSS (uncomment if needed) -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

  <!-- Tom Select -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

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
            <img src="./images/RegistrationClosed.jpg" alt="Registration Closed" class="img-fluid" style="max-width: 270px;" />
          </div>
          <?php exit(); ?>
        <?php endif; ?>
        <!-- Periods table section -->
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
                <td><?= $Counselor->GetPeriodATime($Counselor->getYear()) ? "A - " . $Counselor->GetPeriodATime($Counselor->getYear()) : '' ?></td>
                <td><?= $Counselor->GetPeriodETime($Counselor->getYear()) ? "E - " . $Counselor->GetPeriodETime($Counselor->getYear()) : '' ?></td>
                <td><?= $Counselor->GetPeriodABTime($Counselor->getYear()) ? "AB - " . $Counselor->GetPeriodABTime($Counselor->getYear()) : '' ?></td>
              </tr>
              <tr>
                <td><?= $Counselor->GetPeriodBTime($Counselor->getYear()) ? "B - " . $Counselor->GetPeriodBTime($Counselor->getYear()) : '' ?></td>
                <td><?= $Counselor->GetPeriodFTime($Counselor->getYear()) ? "F - " . $Counselor->GetPeriodFTime($Counselor->getYear()) : '' ?></td>
                <td><?= $Counselor->GetPeriodCDTime($Counselor->getYear()) ? "CD - " . $Counselor->GetPeriodCDTime($Counselor->getYear()) : '' ?></td>
              </tr>
              <tr>
                <td><?= $Counselor->GetPeriodCTime($Counselor->getYear()) ? "C - " . $Counselor->GetPeriodCTime($Counselor->getYear()) : '' ?></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td><?= $Counselor->GetPeriodDTime($Counselor->getYear()) ? "D - " . $Counselor->GetPeriodDTime($Counselor->getYear()) : '' ?></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>

          <?php if ($Counselor->GetLunchTime($Counselor->getYear())): ?>
            <p>Lunch will be served from <?= $Counselor->GetLunchTime($Counselor->getYear()) ?></p>
          <?php endif; ?>
        </div>

        <!-- Counselor selection form -->
        <div class="form-section">
          <p>To limit the number of scouts in your class, enter a value (default is 15 scouts).</p>
          <p>Specify any rerequisites in the rerequisites field.</p>
          <p>Include any material charges in the class fee.</p>
          <p>If your name is not in the Counselor list, missing a Merit Badge, want to offer a NOVA class, or need to edit your merit badges, please <a href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">contact us</a>.</p>

          <form method="post" class="mb-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
            <div class="row g-3">
              <div class="col-md-4">
                <label for="CounselorName" class="form-label">Choose or Type a Counselor</label>
                <select class="form-select" id="CounselorName" name="CounselorName" required>
                  <option value="">Select existing or type new...</option>
                  <?php
                  $query = "SELECT DISTINCT MemberID, LastName, FirstName 
                            FROM mbccounselors 
                            WHERE Active='Yes' 
                            ORDER BY LastName, FirstName";
                  $result = $Counselor->doQuery($query);

                  if ($result):
                    while ($row = $result->fetch_assoc()):
                      $display = htmlspecialchars($row['LastName'] . ' ' . $row['FirstName']);
                      echo "<option value=\"{$row['MemberID']}\">$display</option>";
                    endwhile;
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

        <?php if (isset($_POST['SubmitCounselor']) && !empty($_POST['CounselorName'])): ?>
          <?php
          $SelectedCounselor = trim($_POST['CounselorName']);

          $query = "SELECT m.*, c.FirstName, c.LastName, c.Email, c.HomePhone, c.MemberID 
                    FROM mbccounselors c 
                    INNER JOIN mbccounselormerit cm ON c.FirstName = cm.FirstName AND c.LastName = cm.LastName 
                    INNER JOIN meritbadges m ON cm.MeritName = m.MeritName 
                    WHERE c.MemberID = ? 
                    ORDER BY m.MeritName";

          $mysqli = $Counselor->getDbConn();  // Fixed method name (was dbConn directly)

          if (!$mysqli instanceof mysqli) {
            die("Database connection not available");
          }

          $stmt = $mysqli->prepare($query);
          if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
          }

          $stmt->bind_param("s", $SelectedCounselor);
          $stmt->execute();
          $ResultsMB = $stmt->get_result();

          $row = $ResultsMB->fetch_assoc();

          // Safe defaults
          $firstName   = $row['FirstName']   ?? '';
          $lastName    = $row['LastName']    ?? '';
          $email       = $row['Email']       ?? '';
          $homePhone   = $row['HomePhone']   ?? '';
          $memberID    = $row['MemberID']    ?? $SelectedCounselor;

          if ($row) {
            $Counselor->IsSignedUp($Counselor->getYear(), $lastName, $firstName);
          } else {
            echo '<div class="alert alert-info mt-3">This counselor has no merit badges assigned yet. You can still sign up below.</div>';
          }
          ?>
          <div class="form-section">
            <h5>Counselor Signup Information</h5>
            <form action="index.php?page=signup" method="post" id="add_nomination">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
              <div class="row g-3 mb-3">
                <div class="col-md-2">
                  <label for="element_1_1" class="form-label">First Name</label>
                  <input type="text" name="element_1_1" class="form-control" value="<?= htmlspecialchars($firstName) ?>" required>
                </div>
                <div class="col-md-2">
                  <label for="element_1_2" class="form-label">Last Name</label>
                  <input type="text" name="element_1_2" class="form-control" value="<?= htmlspecialchars($lastName) ?>" required>
                </div>
                <div class="col-md-3">
                  <label for="element_1_3" class="form-label">Email</label>
                  <input type="email" name="element_1_3" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="col-md-2">
                  <label for="element_1_4" class="form-label">Phone</label>
                  <input type="tel" name="element_1_4" class="form-control" value="<?= htmlspecialchars($homePhone) ?>">
                </div>
                <div class="col-md-2">
                  <label for="element_1_5" class="form-label">BSA ID</label>
                  <input type="text" name="element_1_5" class="form-control" value="<?= htmlspecialchars($memberID) ?>" required>
                </div>
              </div>

              <?php for ($i = 1; $i <= 4; $i++):
                $bgColor = $i % 2 ? 'var(--scouting-tan)' : 'var(--scouting-darktan)';
              ?>
                <div class="merit-badge-section" style="background-color: <?= $bgColor ?>">
                  <h5><?= ordinal($i) ?> Merit Badge</h5>

                  <!-- First row: short/select/number fields -->
                  <div class="row g-3">
                    <div class="col-md-3">
                      <label for="MB<?= $i ?>Name" class="form-label">Merit Badge Name</label>
                      <select class="form-select" id="MB<?= $i ?>Name" name="MB<?= $i ?>Name">
                        <option value="" selected disabled hidden>Select Merit Badge</option>
                        <?php
                        mysqli_data_seek($ResultsMB, 0);
                        $firstBadgeFound = false;
                        while ($rowCerts = $ResultsMB->fetch_assoc()):
                          $selected = $Counselor->MB_Match($rowCerts['MeritName'], $i) && !$firstBadgeFound ? 'selected' : '';
                          if ($selected) $firstBadgeFound = true;
                          echo "<option $selected value='" . htmlspecialchars($rowCerts['MeritName']) . "'>" .
                            htmlspecialchars($rowCerts['MeritName']) . "</option>";
                        endwhile;
                        ?>
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label for="MB<?= $i ?>Period" class="form-label">Period</label>
                      <select class="form-select" id="MB<?= $i ?>Period" name="MB<?= $i ?>Period">
                        <option value="" selected disabled hidden>Select Period</option>
                        <?= $Counselor->DisplayPeriods($i) ?>
                      </select>
                    </div>

                    <div class="col-md-2">
                      <label for="MB<?= $i ?>CSL" class="form-label">Class Size</label>
                      <?php echo $Counselor->Display_ClassSize("MB{$i}CSL", $i); ?>
                      <small class="form-text text-muted">Maximum number of Scouts for Badge</small>
                    </div>

                    <div class="col-md-2">
                      <label for="MB<?= $i ?>Fee" class="form-label">Class Fee</label>
                      <?php echo $Counselor->Display_ClassFee("MB{$i}Fee", $i); ?>
                      <small class="form-text text-muted">Include any material charges (e.g. $5.00)</small>
                    </div>

                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["Role"] === "Admin"): ?>
                      <div class="col-md-3"> <!-- Slightly wider when Room is present -->
                        <label for="MB<?= $i ?>Room" class="form-label">Room</label>
                        <?php $Counselor->Display_ClassRoom("MB{$i}Room", $i); ?>
                      </div>
                    <?php endif; ?>
                  </div>

                  <!-- Second row: wider text fields (Prerequisites + Notes) -->
                  <div class="row g-3 mt-3"> <!-- mt-3 adds nice spacing between rows -->
                    <div class="col-md-6"> <!-- Wider column so textareas have more room -->
                      <label for="MB<?= $i ?>Prerequisities" class="form-label">Prerequisites</label>
                      <?php echo $Counselor->Display_Prerequisities("MB{$i}Prerequisities", $i); ?>  
                      <small class="form-text text-muted">List any requirements scouts should complete before attending.</small>
                    </div>

                    <div class="col-md-6">
                      <label for="MB<?= $i ?>Notes" class="form-label">Notes</label>
                      <?php echo $Counselor->Display_Notes("MB{$i}Notes", $i); ?>
                      <small class="form-text text-muted">Any notes for the college staff.</small>
                    </div>
                  </div>
                </div>
              <?php endfor; ?> <div class="text-center mt-3">
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

  function ordinal($number)
  {
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) return $number . 'th';
    return $number . $ends[$number % 10];
  }

  function redirectWithMessage($url, $type, $message)
  {
    $_SESSION['feedback'] = compact('type', 'message');
    header("Location: $url");
    exit;
  }
  ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      new TomSelect('#CounselorName', {
        create: true,
        createOnBlur: true,
        persist: false,
        maxItems: 1,
        placeholder: "Type name or select existing...",
        searchField: ['text'],
        sortField: 'text',
        render: {
          option_create: function(data, escape) {
            return '<div class="create">Add new counselor: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
          }
        }
      });
    });
  </script>
</body>

</html>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Target ALL relevant selects: Merit Badge Name and Period
    const selects = document.querySelectorAll('select[name^="MB"][name$="Name"], select[name^="MB"][name$="Period"]');

    selects.forEach(select => {
      function updatePlaceholder() {
        if (select.value === '') {
          select.classList.add('placeholder-shown');
        } else {
          select.classList.remove('placeholder-shown');
        }
      }

      // Run immediately (handles pre-selected values from PHP)
      updatePlaceholder();

      // Run every time selection changes
      select.addEventListener('change', updatePlaceholder);
    });
  });
</script>