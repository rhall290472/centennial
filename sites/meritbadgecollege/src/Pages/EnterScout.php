<?php
/*
==============================================================================
    Proprietary Software of Richard Hall
    Copyright 2017-2026 - Richard Hall
    Do not copy or disclose without written permission
==============================================================================
*/

require_once BASE_PATH . '/src/Classes/CScout.php';
$Scout = CScout::getInstance();

// ────────────────────────────────────────────────
// Authentication check
// ────────────────────────────────────────────────
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to access this page.'];
  header('Location: index.php?page=login');
  exit;
}

// ────────────────────────────────────────────────
// College Year
// ────────────────────────────────────────────────
$CollegeYear = $_SESSION['year'] ?? date('Y');
if (!empty($_POST['CollegeYear'])) {
  $CollegeYear = trim($_POST['CollegeYear']);
  $_SESSION['year'] = $CollegeYear;
  $GLOBALS["MBCollegeYear"] = $CollegeYear;
}

$Scout->SelectCollegeYearandScout($CollegeYear, "Enter Scout Data", false);

// ────────────────────────────────────────────────
// Fetch available merit badges (once)
// ────────────────────────────────────────────────
$mbQuery = "SELECT DISTINCTROW `MBName` FROM college_counselors WHERE college = ? ORDER BY `MBName` ASC";
$CollegeMBs = $Scout->query($mbQuery, [$CollegeYear]); // ← ideally → real prepared statement
$allMeritBadges = $CollegeMBs->fetchAll(PDO::FETCH_COLUMN);

if (!$CollegeMBs) {
  $Scout->function_alert("Cannot load merit badge list");
  exit;
}

// ────────────────────────────────────────────────
// Default / empty data structures
// ────────────────────────────────────────────────
$scout = [
  'FirstName'    => '',
  'LastName'     => '',
  'Email'        => '',
  'Phone'        => '',
  'BSAId'        => '',
  'Registration' => '',
  'District'     => '',
  'UnitType'     => '',
  'UnitNumber'   => '',
  'Gender'       => '',
];

$meritBadges = array_fill(1, 4, [
  'Name'           => '',
  'Period'         => '',
  'CounselorFirst' => '',
  'CounselorLast'  => '',
  'CounselorEmail' => '',
  'DidNotAttend'   => false,
]);

// ────────────────────────────────────────────────
// Load existing registration if scout selected
// ────────────────────────────────────────────────
if (!empty($_POST['SubmitScout']) && !empty($_POST['ScoutName']) && $_POST['ScoutName'] !== '-1') {
  $selectedBsaId = $_POST['ScoutName'];

  $sql = "
        SELECT cr.*, cc.FirstName, cc.LastName, cc.Email,
               cc.MBName AS MeritBadge, cc.MBPeriod AS Period
        FROM college_registration cr
        INNER JOIN college_counselors cc
           ON cr.MeritBadge = cc.MBName
          AND cr.Period     = cc.MBPeriod
          AND cr.College    = cc.College
        WHERE cr.College = ? AND cr.BSAIdScout = ?
        ORDER BY cr.Period ASC
    ";

  try {
    $result = $Scout->query($sql, [$CollegeYear, $selectedBsaId]);

    if (!$result) {
      // Query failed (prepare/execute error)
      error_log("Failed to execute scout load query for BSA ID $selectedBsaId in year $CollegeYear");
      $_SESSION['feedback'] = [
        'type'    => 'danger',
        'message' => 'Database error while loading scout data. Please try again or contact support.'
      ];
      // Optionally redirect or continue with empty form
      // header('Location: index.php?page=scout-data'); exit;
    } elseif ($result->rowCount() === 0) {
      // No records found — not necessarily an error, but worth informing
      $_SESSION['feedback'] = [
        'type'    => 'warning',
        'message' => 'No registration found for this scout in ' . $CollegeYear . '. You can create a new one below.'
      ];
      // Proceed with empty/default $scout and $meritBadges
    } else {
      // ── At least one row exists ───────────────────────────────
      $firstRow = $result->fetch(PDO::FETCH_ASSOC);

      if (!$firstRow) {
        error_log("rowCount() > 0 but fetch() returned false – possible cursor issue");
        $_SESSION['feedback'] = [
          'type'    => 'danger',
          'message' => 'Error reading scout registration data.'
        ];
      } else {
        // Populate scout info from first row
        $scout = [
          'FirstName'    => $firstRow['FirstNameScout'] ?? '',
          'LastName'     => $firstRow['LastNameScout'] ?? '',
          'Email'        => $firstRow['email']          ?? '',
          'Phone'        => $firstRow['Telephone']      ?? '',
          'BSAId'        => $firstRow['BSAIdScout']     ?? '',
          'Registration' => $firstRow['Registration']   ?? '',
          'District'     => $firstRow['District']       ?? '',
          'UnitType'     => $firstRow['UnitType']       ?? '',
          'UnitNumber'   => $firstRow['UnitNumber']     ?? '',
          'Gender'       => $firstRow['Gender']         ?? '',
        ];

        // Reset merit badges
        $meritBadges = array_fill(1, 4, [
          'Name'           => '',
          'Period'         => '',
          'CounselorFirst' => '',
          'CounselorLast'  => '',
          'CounselorEmail' => '',
          'DidNotAttend'   => false,
        ]);

        $i = 1;

        // Use first row + continue fetching remaining ones
        do {
          if ($i > 4) {
            error_log("Scout $selectedBsaId has more than 4 merit badges in $CollegeYear – only first 4 loaded");
            break;
          }

          $meritBadges[$i] = [
            'Name'           => $firstRow['MeritBadge']  ?? '',
            'Period'         => $firstRow['Period']      ?? '',
            'CounselorFirst' => $firstRow['FirstName']   ?? '',
            'CounselorLast'  => $firstRow['LastName']    ?? '',
            'CounselorEmail' => $firstRow['Email']       ?? '',
            'DidNotAttend'   => false, // ← replace with real column when available
          ];

          $i++;
        } while ($firstRow = $result->fetch(PDO::FETCH_ASSOC));

        // Optional: check if we actually loaded any merit badges
        if ($i === 1) {
          $_SESSION['feedback'] ??= ['type' => 'info', 'message' => 'Scout found, but no merit badge registrations loaded.'];
        }

        // You might want to verify consistency here
        $Scout->IsSignedUp($CollegeYear, $scout['LastName'], $scout['FirstName'], $selectedBsaId);
      }
    }
  } catch (Exception $e) {
    error_log("Exception while loading scout data (BSA ID: $selectedBsaId, Year: $CollegeYear): " . $e->getMessage());
    $_SESSION['feedback'] = [
      'type'    => 'danger',
      'message' => 'An unexpected error occurred while loading scout data. Please try again.'
    ];
  }
}
// ────────────────────────────────────────────────
// FORM SUBMIT ─ Save / Update
// ────────────────────────────────────────────────
if (!empty($_POST['SubmitForm'])) {
  // Basic CSRF protection
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== get_csrf_token()) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Security check failed. Please try again.'];
    header('Location: index.php?page=scout-data');
    exit;
  }

  // ── Collect scout data ───────────────────────────────
  $scout = [
    'FirstName'    => trim($_POST['fname']    ?? ''),
    'LastName'     => trim($_POST['lname']    ?? ''),
    'Email'        => filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '',
    'Phone'        => preg_replace('/[^0-9+()-]/', '', $_POST['phone'] ?? ''),
    'BSAId'        => filter_var($_POST['bsa_id'] ?? '', FILTER_VALIDATE_INT) ?: null,
    'Registration' => trim($_POST['registration'] ?? ''),
    'District'     => trim($_POST['district'] ?? ''),
    'UnitType'     => trim($_POST['unit_type'] ?? ''),
    'UnitNumber'   => trim($_POST['unit_number'] ?? ''),
    'Gender'       => trim($_POST['gender']   ?? ''),
  ];

  if (empty($scout['BSAId'])) {
    $minRow = $Scout->doQuery("SELECT MIN(BSAIdScout) AS minid FROM college_registration")->fetch_assoc();
    $scout['BSAId'] = ($minRow['minid'] ?? -1000) - 1;
  }

  // Remove old records if editing
  if ($Scout->IsSignedUp($CollegeYear, $scout['LastName'], $scout['FirstName'], $scout['BSAId'])) {
    $Scout->Delete();
  }

  $Scout->AddInfo(
    $scout['FirstName'],
    $scout['LastName'],
    $scout['Email'],
    $scout['Phone'],
    $scout['BSAId'],
    $CollegeYear,
    $scout['Registration'],
    $scout['District'],
    $scout['UnitType'],
    $scout['UnitNumber'],
    $scout['Gender']
  );

  // ── Save merit badges ────────────────────────────────
  for ($i = 1; $i <= 4; $i++) {
    $name   = trim($_POST["mb{$i}_name"]   ?? '');
    $period = trim($_POST["mb{$i}_period"] ?? '');
    $attend = !empty($_POST["mb{$i}_attend"]);

    if ($name !== '' && $period !== '') {
      $Scout->AddMBClass($name, $period, $attend ? 1 : 0);
    }
  }

  $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Scout registration saved.'];
  header('Location: index.php?page=scout-data');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Enter Scout Data</title>
  <!-- Assuming you have Bootstrap or similar already included via layout -->
</head>

<body>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-4">

        <?php if (isset($_SESSION['feedback'])): ?>
          <div class="alert alert-<?= htmlspecialchars($_SESSION['feedback']['type']) ?>">
            <?= htmlspecialchars($_SESSION['feedback']['message']) ?>
          </div>
          <?php unset($_SESSION['feedback']); ?>
        <?php endif; ?>

        <form action="index.php?page=scout-data" method="post" class="needs-validation" novalidate>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">

          <!-- Scout Information -->
          <h4 class="mb-3">Scout Information</h4>
          <div class="row g-3 mb-5">
            <div class="col-md-3">
              <label for="fname" class="form-label">First Name</label>
              <input type="text" class="form-control" id="fname" name="fname" value="<?= htmlspecialchars($scout['FirstName']) ?>" required>
            </div>
            <div class="col-md-4">
              <label for="lname" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="lname" name="lname" value="<?= htmlspecialchars($scout['LastName']) ?>" required>
            </div>
            <div class="col-md-5">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($scout['Email']) ?>">
            </div>
            <div class="col-md-4">
              <label for="phone" class="form-label">Phone</label>
              <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($scout['Phone']) ?>">
            </div>
            <div class="col-md-3">
              <label for="bsa_id" class="form-label">BSA ID</label>
              <input type="text" class="form-control" id="bsa_id" name="bsa_id" value="<?= htmlspecialchars($scout['BSAId']) ?>">
            </div>
            <div class="col-md-3">
              <label for="registration" class="form-label">Registration #</label>
              <input type="text" class="form-control" id="registration" name="registration" value="<?= htmlspecialchars($scout['Registration']) ?>">
            </div>
            <div class="col-md-3">
              <label class="description" for="district">District </label>
              <select class='form-select' id='district' name='district'>
                <option value=""> </option>
                <?php $Scout->DisplayDistrict($scout['District']); ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="description" for="unit_type">Unit Type </label>
              <select class='form-select' id='unit_type' name='unit_type'>
                <option value=""> </option>
                <?php $Scout->DisplayUnitType($scout['UnitType']); ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="unit_number" class="form-label">Unit Number</label>
              <input type="text" class="form-control" id="unit_number" name="unit_number" value="<?= htmlspecialchars($scout['UnitNumber']) ?>">
            </div>
            <div class="col-1">
              <label class="description" for="gender">Gender</label>
              <select class='form-select' id='gender' name='gender'>
                <option value=""> </option>
                <?php $Scout->DisplayGender($scout['Gender']); ?>
              </select>
            </div>
          </div>

          <hr class="my-5">

          <!-- Merit Badges -->
          <h4 class="mb-3">Merit Badge Selections</h4>

          <?php for ($i = 1; $i <= 4; $i++):
            $mb = $meritBadges[$i];
            $prefix = "mb{$i}_";
          ?>
            <div class="card mb-4 shadow-sm" style="--bs-card-bg: var(--scouting-lighttan);">
              <div class="card-body">
                <h5 class="card-title">Merit Badge #<?= $i ?></h5>
                <div class="row g-3">
                  <div class="col-md-5">
                    <label for="<?= $prefix ?>name" class="form-label">Merit Badge</label>
                    <select name="mb<?= $i ?>_name" class="form-select">
                      <option value="">— Select —</option>
                      <?php foreach ($allMeritBadges as $mbName): ?>
                        <option value="<?= htmlspecialchars($mbName) ?>"
                          <?= $mbName === ($meritBadges[$i]['Name'] ?? '') ? ' selected' : '' ?>>
                          <?= htmlspecialchars($mbName) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label for="<?= $prefix ?>period" class="form-label">Period</label>
                    <select class="form-select" id="mb<?= $i ?>_period" name="mb<?= $i ?>_period">
                      <option value="">— Select —</option>
                      <?php
                      $savedPeriod = $meritBadges[$i]['Period'] ?? '';
                      $Scout->DisplayPeriods($i, $CollegeYear, $savedPeriod);
                      ?>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check mt-4 pt-1">
                      <input class="form-check-input" type="checkbox" name="<?= $prefix ?>attend" id="<?= $prefix ?>attend"
                        <?= $mb['DidNotAttend'] ? 'checked' : '' ?>>
                      <label class="form-check-label" for="<?= $prefix ?>attend">Did Not Attend</label>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Counselor First</label>
                    <input type="text" class="form-control" name="<?= $prefix ?>counselor_first" value="<?= htmlspecialchars($mb['CounselorFirst']) ?>">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Counselor Last</label>
                    <input type="text" class="form-control" name="<?= $prefix ?>counselor_last" value="<?= htmlspecialchars($mb['CounselorLast']) ?>">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Counselor Email</label>
                    <input type="email" class="form-control" name="<?= $prefix ?>counselor_email" value="<?= htmlspecialchars($mb['CounselorEmail']) ?>">
                  </div>
                </div>
              </div>
            </div>
          <?php endfor; ?>

          <div class="text-center mt-5">
            <button type="submit" name="SubmitForm" value="SaveScout" class="btn btn-primary btn-lg px-5 py-3">
              Save Scout Registration
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

</body>

</html>