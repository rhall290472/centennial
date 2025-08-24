<?php
/*
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */
load_class(__DIR__ . '/../Classes/CEagle.php');
$cEagle = CEagle::getInstance();

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

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
    header("Location: index.php?page=coach-edit");
    exit;
  }

  // Handle coach selection
  if (isset($_POST['SubmitCoach'], $_POST['CoachName'])) {
    $SelectedCoach = (int)$_POST['CoachName'];
    if ($SelectedCoach === -1) {
      $stmt = $cEagle->getDbConn()->prepare("INSERT INTO coaches (Active) VALUES (0)");
      $stmt->execute();
      $SelectedCoach = $cEagle->getDbConn()->insert_id;
      $stmt->close();
    }
    $_SESSION['selected_coach_id'] = $SelectedCoach;
    $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Coach selected successfully.'];
    // header("Location: index.php?page=coach-edit");
    // exit;
  }

  // Handle edit form submission
  if (isset($_POST['SubmitForm'])) {
    if ($_POST['SubmitForm'] === 'Cancel') {
      unset($_SESSION['selected_coach_id']);
      $_SESSION['feedback'] = ['type' => 'info', 'message' => 'Form submission cancelled.'];
      header("Location: index.php?page=coach-edit");
      exit;
    }

    $SelectedCoach = (int)$_POST['Coachesid'];
    $stmt = $cEagle->getDbConn()->prepare("SELECT * FROM coaches WHERE Coachesid = ?");
    $stmt->bind_param("i", $SelectedCoach);
    $stmt->execute();
    $Coach = $stmt->get_result();
    $rowCoach = $Coach->fetch_assoc();
    $stmt->close();

    if (!$rowCoach) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Coach not found.'];
      header("Location: index.php?page=coach-edit");
      exit;
    }

    $FormData = [
      'Coachesid' => $rowCoach['Coachesid'],
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

    //header("Location: index.php?page=coach-edit");
    exit;
  }
}

// Fetch coaches for dropdown
$stmt = $cEagle->getDbConn()->prepare("SELECT DISTINCT Coachesid, Last_Name, First_Name FROM coaches ORDER BY Last_Name, First_Name");
$stmt->execute();
$result_ByCoaches = $stmt->get_result();

// Fetch selected coach data
$SelectedCoach = isset($_SESSION['selected_coach_id']) ? (int)$_SESSION['selected_coach_id'] : (isset($_GET['Coachesid']) ? (int)$_GET['Coachesid'] : null);
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

<div class="container-fluid mt-5 pt-3">
  <!-- Display Feedback from index.php -->
  <?php if (!empty($_SESSION['feedback'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['feedback']['type']); ?> alert-dismissible fade show" role="alert">
      <?php echo htmlspecialchars($_SESSION['feedback']['message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['feedback']); ?>
  <?php endif; ?>

  <form action="index.php?page=coach-edit" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <div class="form-row">
      <div class="col-3">
        <label for="CoachName">Choose a Coach:</label>
        <select class="form-control" id="CoachName" name="CoachName">
          <?php while ($row = $result_ByCoaches->fetch_assoc()): ?>
            <option value="<?php echo $row['Coachesid']; ?>" <?php echo ($row['Coachesid'] == $SelectedCoach) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($row['Last_Name'] . " " . $row['First_Name']); ?>
            </option>
          <?php endwhile;
          $result_ByCoaches->free(); ?>
          <option value="-1">Add New</option>
        </select>
      </div>
      <div class="col-3 py-4">
        <input class="btn btn-primary btn-sm" type="submit" name="SubmitCoach" value="Select Coach">
      </div>
    </div>
  </form>

  <?php if (isset($rowCoach)): ?>
    <div class="form-coach px-5" style="background-color: var(--scouting-lighttan);">
      <p><b>Coach Information</b></p>
      <form action="index.php?page=coach-edit" method="post" id="coach-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="Coachesid" value="<?php echo htmlspecialchars($rowCoach['Coachesid']); ?>">

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
            <input type="tel" name="element_2_2" class="form-control" pattern="[0-9]" value="<?php echo htmlspecialchars($rowCoach['Phone_Home'] ?? ''); ?>">
          </div>
          <div class="col-2">
            <label for="element_2_3">Mobile Phone</label>
            <input type="tel" name="element_2_3" class="form-control" pattern="[0-9]" value="<?php echo htmlspecialchars($rowCoach['Phone_Mobile'] ?? ''); ?>">
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
            <input type="text" name="element_3_4" class="form-control" pattern="[0-9]{5}" value="<?php echo htmlspecialchars($rowCoach['Zip'] ?? ''); ?>">
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
          <div class="col-1">
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
          <div class="form-check py-4 px-3">
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