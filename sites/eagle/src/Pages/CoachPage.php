<?php
/*
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */
load_class(BASE_PATH . '/src/Classes/CEagle.php');
$cEagle = CEagle::getInstance();
$SelectedCoach = null;

if (!session_id()) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}

if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Determine Coachesid from either POST or GET
$SelectedCoach = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Coachesid'])) {
  // Validate CSRF token for POST requests
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
    $cEagle->GotoURL("Location: index.php?page=coach-edit");
    exit;
  }
  $SelectedCoach = (int)$_POST['Coachesid'];
} elseif (isset($_GET['Coachesid'])) {
  $SelectedCoach = (int)$_GET['Coachesid'];
}

// Handle POST requests for coach selection or form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle coach selection
  if (isset($_POST['SubmitCoach'], $_POST['Coachesid'])) {
    if ($SelectedCoach === -1) {
      // Create a new coach record
      $dbConn = $cEagle->getDbConn();
      if ($dbConn === null) {
        error_log("Error: Database connection is null after insert in coach table.");
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed to connect to database after creating coach record.'];
        $cEagle->GotoURL('index.php?page=coach-edit');
        exit;
      }
      $queryInsert = "INSERT INTO coaches (is_deleted) VALUES (0)";
      $result = $dbConn->query($queryInsert);
      if ($result) {
        $SelectedCoach = mysqli_insert_id($dbConn);
        $_SESSION['selected_coach_id'] = $SelectedCoach;
        if ($SelectedCoach === 0) {
          error_log("Error: mysqli_insert_id returned 0 for query: $queryInsert. Connection ID: " . spl_object_id($dbConn));
          $checkQuery = "SELECT Coachesid FROM coaches WHERE is_deleted = 0 ORDER BY Coachesid DESC LIMIT 1";
          $checkResult = $cEagle->doQuery($checkQuery);
          if ($checkResult && $row = $checkResult->fetch_assoc()) {
            error_log("Found Coachesid: " . $row['Coachesid']);
            $SelectedCoach = $row['Coachesid'];
          } else {
            error_log("No record found for recent insert.");
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'New coach record created, but failed to retrieve Coachesid. Check if Coachesid is set to AUTO_INCREMENT.'];
            $cEagle->GotoURL('index.php?page=coach-edit');
            exit;
          }
        }
      } else {
        $error = mysqli_error($cEagle->getDbConn());
        error_log("Error: INSERT query failed: $queryInsert, Error: $error");
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed to create new coach record: ' . $error];
        $cEagle->GotoURL('index.php?page=coach-edit');
        exit;
      }
    }
    $_SESSION['selected_coach_id'] = $SelectedCoach;
  }

  // Handle edit form submission
  if (isset($_POST['SubmitForm'])) {
    if ($_POST['SubmitForm'] === 'Cancel') {
      unset($_SESSION['selected_coach_id']);
      $_SESSION['feedback'] = ['type' => 'info', 'message' => 'Form submission cancelled.'];
      header("Location: index.php?page=coach-edit");
      exit;
    }

    $FormData = [
      'Coachesid' => $SelectedCoach,
      'First_Name' => htmlspecialchars(trim($cEagle->GetFormData('element_1_1'))),
      'PreferredName' => htmlspecialchars(trim($cEagle->GetFormData('element_1_1a'))),
      'Middle_Name' => htmlspecialchars(trim($cEagle->GetFormData('element_1_2'))),
      'Last_Name' => htmlspecialchars(trim($cEagle->GetFormData('element_1_3'))),
      'Member_ID' => htmlspecialchars(trim($cEagle->GetFormData('element_1_4'))),
      'Email_Address' => filter_var($cEagle->GetFormData('element_2_1'), FILTER_SANITIZE_EMAIL),
      'Phone_Home' => htmlspecialchars(trim($cEagle->GetFormData('element_2_2'))),
      'Phone_Mobile' => htmlspecialchars(trim($cEagle->GetFormData('element_2_3'))),
      'Street_Address' => htmlspecialchars(trim($cEagle->GetFormData('element_3_1'))),
      'City' => htmlspecialchars(trim($cEagle->GetFormData('element_3_2'))),
      'State' => htmlspecialchars(trim($cEagle->GetFormData('element_3_3'))),
      'Zip' => htmlspecialchars(trim($cEagle->GetFormData('element_3_4'))),
      'Position' => htmlspecialchars(trim($cEagle->GetFormData('element_4_1'))),
      'District' => htmlspecialchars(trim($cEagle->GetFormData('element_4_2'))),
      'YPT_Expires' => htmlspecialchars(trim($cEagle->GetFormData('element_4_3'))),
      'Gender' => htmlspecialchars(trim($cEagle->GetFormData('element_4_4'))),
      'Trained' => $cEagle->GetFormData('element_4_5') ? 1 : 0,
      'Active' => $cEagle->GetFormData('element_4_6') ? 1 : 0,
      'Notes' => htmlspecialchars(trim($cEagle->GetFormData('Notes')))
    ];

    if ($cEagle->UpdateCoachRecord($FormData)) {
      $cEagle->CreateAudit($rowCoach, $FormData, 'Coachesid');
      $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Coach updated successfully.'];
    } else {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed to update coach.'];
    }
    unset($_SESSION['selected_coach_id']);
    $cEagle->GotoURL('index.php?page=coach-edit');
    exit;
  }
}

// Fetch coaches for dropdown
$stmt = $cEagle->getDbConn()->prepare("SELECT DISTINCT Coachesid, Last_Name, First_Name FROM coaches ORDER BY Last_Name, First_Name");
$stmt->execute();
$result_ByCoaches = $stmt->get_result();

// Fetch selected coach data
$rowCoach = null;
if ($SelectedCoach) {
  $stmt = $cEagle->getDbConn()->prepare("SELECT * FROM coaches WHERE Coachesid = ?");
  $stmt->bind_param("i", $SelectedCoach);
  $stmt->execute();
  $Coach = $stmt->get_result();
  $rowCoach = $Coach->fetch_assoc();
  $stmt->close();
  if (!$rowCoach) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Coach not found.'];
    unset($_SESSION['selected_coach_id']);
    header("Location: index.php?page=coach-edit");
    exit;
  }
  $Street = $rowCoach['Street_Address'] ?? '';
}
?>

<!-- HTML remains unchanged -->
<div class="container-fluid mt-5 pt-3">
  <!-- Display Feedback from index.php -->
  <?php if (!empty($_SESSION['feedback'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['feedback']['type']); ?> alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($_SESSION['feedback']['message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['feedback']); ?>
  <?php endif; ?>

  <h4>Select or Type Coach</h4>
  <form action="index.php?page=coach-edit" method="post">
    <div class="form-row px-5 d-print-none">
      <div class="col-auto"> <!-- slightly wider for typing comfort -->
        <label for="CoachInput">Choose or type a Coach:</label>

        <!-- Visible typing field with suggestions -->
        <input type="text"
          class="form-control"
          id="CoachInput"
          name="CoachDisplay"
          list="coaches-list"
          placeholder="Type last name or select..."
          autocomplete="off"
          value="<?php echo htmlspecialchars($SelectedCoachName ?? ''); ?>" 
        required>

        <!-- Hidden field that carries the actual Coachesid (-1 = new / custom) -->
        <input type="hidden" name="Coachesid" id="Coachesid" value="<?php echo htmlspecialchars($SelectedCoach ?? ''); ?>">

        <!-- Datalist with suggestions -->
        <datalist id="coaches-list">
          <?php
          // Reset pointer in case result was already iterated
          $result_ByCoaches->data_seek(0);
          while ($row = $result_ByCoaches->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars(trim($row['Last_Name'] . ' ' . $row['First_Name'])); ?>"
              data-id="<?php echo htmlspecialchars($row['Coachesid']); ?>">
            </option>
          <?php endwhile;
          $result_ByCoaches->free(); ?>

          <!-- Explicit "Add New" option (optional but helpful) -->
          <option value="Add New Coach" data-id="-1"></option>
        </datalist>
      </div>

      <div class="col-3 py-45"> <!-- adjusted py-4 for better alignment -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input class="btn btn-primary btn-sm" type="submit" name="SubmitCoach" value="Select Coach">
      </div>
    </div>
  </form>

  <!-- JavaScript to sync visible name → hidden ID -->
  <script>
    document.getElementById('CoachInput').addEventListener('input', function() {
      const inputValue = this.value.trim();
      const hiddenInput = document.getElementById('Coachesid');
      const datalist = document.getElementById('coaches-list');

      hiddenInput.value = ''; // reset first

      // Look for exact match in datalist options
      let found = false;
      for (const option of datalist.options) {
        if (option.value === inputValue) {
          const id = option.getAttribute('data-id');
          if (id) {
            hiddenInput.value = id;
            found = true;
            break;
          }
        }
      }

      // If no exact match and user typed something → treat as new/custom
      if (!found && inputValue !== '') {
        hiddenInput.value = '-1';
      }
    });
  </script>

  <?php if (isset($rowCoach)): ?>
    <div class="form-coach px-5" style="background-color: var(--scouting-lighttan);">
      <p><b>Coach Information</b></p>
      <form action="index.php?page=coach-edit" method="post" id="coach-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="Coachesid" value="<?php echo htmlspecialchars($rowCoach['Coachesid']); ?>">

        <!-- Rest of the form remains unchanged -->
        <div class="form-row mb-3">
          <div class="col-2">
            <label for="element_1_1">First Name</label>
            <input type="text" name="element_1_1" class="form-control" value="<?php echo htmlspecialchars($rowCoach['First_Name'] ?? ''); ?>" required>
          </div>
          <div class="col-2">
            <label for="element_1_1a">Preferred Name</label>
            <input type="text" name="element_1_1a" class="form-control" value="<?php echo htmlspecialchars($rowCoach['PreferredName'] ?? ''); ?>">
          </div>
          <div class="col-2">
            <label for="element_1_2">Middle Name</label>
            <input type="text" name="element_1_2" class="form-control" value="<?php echo htmlspecialchars($rowCoach['Middle_Name'] ?? ''); ?>">
          </div>
          <div class="col-2">
            <label for="element_1_3">Last Name</label>
            <input type="text" name="element_1_3" class="form-control" value="<?php echo htmlspecialchars($rowCoach['Last_Name'] ?? ''); ?>" required>
          </div>
        </div>

        <div class="form-row mb-3">
          <div class="col-2">
            <label for="element_2_1">Email</label>
            <input type="email" name="element_2_1" class="form-control" value="<?php echo htmlspecialchars($rowCoach['Email_Address'] ?? ''); ?>">
          </div>
          <div class="col-2">
            <label for="element_2_2">Home Phone</label>
            <input type="tel" name="element_2_2" class="form-control" value="<?php echo htmlspecialchars($rowCoach['Phone_Home'] ?? ''); ?>" />
          </div>
          <div class="col-2">
            <label for="element_2_3">Mobile Phone</label>
            <input type="tel" name="element_2_3" class="form-control" value="<?php echo htmlspecialchars($rowCoach['Phone_Mobile'] ?? ''); ?>">
          </div>
          <div class="col-2">
            <label for="element_1_4">BSA ID</label>
            <input type="text" name="element_1_4" class="form-control" value="<?php echo htmlspecialchars($rowCoach['Member_ID'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-row mb-3">
          <div class="col-3">
            <label for="element_3_1">Street Address</label>
            <input type="text" name="element_3_1" class="form-control" value="<?php echo htmlspecialchars($Street); ?>">
          </div>
          <div class="col-2">
            <label for="element_3_2">City</label>
            <input type="text" name="element_3_2" class="form-control" value="<?php echo htmlspecialchars($rowCoach['City'] ?? ''); ?>">
          </div>
          <div class="col-1">
            <label for="element_3_3">State</label>
            <input type="text" name="element_3_3" class="form-control" maxlength="2" value="<?php echo htmlspecialchars($rowCoach['State'] ?? ''); ?>">
          </div>
          <div class="col-1">
            <label for="element_3_4">Zip</label>
            <input type="text" name="element_3_4" class="form-control" value="<?php echo htmlspecialchars($rowCoach['Zip'] ?? ''); ?>" />
          </div>
        </div>

        <div class="form-row mb-3">
          <div class="col-2">
            <label for="element_4_1">Position</label>
            <select class="form-control" name="element_4_1">
              <option value="">Select</option>
              <?php $cEagle->DisplayPosition($rowCoach['Position'] ?? ''); ?>
            </select>
          </div>
          <div class="col-1">
            <label for="element_4_2">District</label>
            <select class="form-control" name="element_4_2">
              <option value="">Select</option>
              <?php $cEagle->DisplayDistrict($rowCoach['District'] ?? ''); ?>
            </select>
          </div>
          <div class="col-2">
            <label for="element_4_3">YPT Expires</label>
            <input type="date" name="element_4_3" class="form-control" value="<?php echo htmlspecialchars($rowCoach['YPT_Expires'] ?? ''); ?>">
          </div>
          <div class="col-1">
            <label for="element_4_4">Gender</label>
            <select class="form-control" name="element_4_4">
              <option value="">Select</option>
              <?php $cEagle->DisplayGender($rowCoach['Gender'] ?? ''); ?>
            </select>
          </div>
          <div class="form-check py-4 px-4">
            <input type="checkbox" name="element_4_5" id="element_4_5" class="form-check-input" value="1" <?php echo ($rowCoach['Trained'] ?? 0) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="element_4_5">Trained</label>
          </div>
          <div class="form-check py-4">
            <input type="checkbox" name="element_4_6" id="element_4_6" class="form-check-input" value="1" <?php echo ($rowCoach['Active'] ?? 0) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="element_4_6">Active</label>
          </div>
        </div>

        <div class="form-row mb-3">
          <div class="col-8">
            <label for="Notes">Notes</label>
            <textarea class="form-control" name="Notes" rows="10"><?php echo htmlspecialchars($rowCoach['Notes'] ?? ''); ?></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="col-10 py-5">
            <input type="submit" name="SubmitForm" value="Save" class="btn btn-primary btn-sm">
            <input type="submit" name="SubmitForm" value="Cancel" class="btn btn-secondary btn-sm">
          </div>
        </div>
      </form>
    </div>
  <?php endif; ?>
</div>