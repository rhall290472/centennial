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

  $result = $Scout->query($sql, [$CollegeYear, $selectedBsaId]);

  if ($result && $row = $result->fetch()) {
    $scout = [
      'FirstName'    => $row['FirstNameScout'] ?? '',
      'LastName'     => $row['LastNameScout'] ?? '',
      'Email'        => $row['email']          ?? '',
      'Phone'        => $row['Telephone']      ?? '',
      'BSAId'        => $row['BSAIdScout']     ?? '',
      'Registration' => $row['Registration']   ?? '',
      'District'     => $row['District']       ?? '',
      'UnitType'     => $row['UnitType']       ?? '',
      'UnitNumber'   => $row['UnitNumber']     ?? '',
      'Gender'       => $row['Gender']         ?? '',
    ];

    $i = 1;
    do {
      if ($i > 4) break;
      $meritBadges[$i] = [
        'Name'           => $row['MeritBadge']     ?? '',
        'Period'         => $row['Period']         ?? '',
        'CounselorFirst' => $row['FirstName']      ?? '',
        'CounselorLast'  => $row['LastName']       ?? '',
        'CounselorEmail' => $row['Email']          ?? '',
        'DidNotAttend'   => false, // load real value if stored
      ];
      $i++;
    } while ($row = $result->fetch());

    $Scout->IsSignedUp($CollegeYear, $scout['LastName'], $scout['FirstName'], $selectedBsaId);
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
            <!-- Add District, Unit Type, Unit #, Gender, Registration fields similarly -->
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
                    <select class="form-select" id="<?= $prefix ?>period" name="<?= $prefix ?>period">
                      <option value="">—</option>
                      <?= $Scout->DisplayPeriods($i, $CollegeYear) ?>
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
            <button type="submit" name="SubmitForm" class="btn btn-primary btn-lg px-5 py-3">
              Save Scout Registration
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

</body>

</html>