<?php
// verify-life-eagle.php
// Standalone verification page for Life/Eagle scouts from Youth Member Age Report

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Ensure CEagle is loaded
load_class(BASE_PATH . '/src/Classes/CEagle.php');  // adjust path if needed

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_submit'])) {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
  } else if (isset($_FILES['youth_report']) && $_FILES['youth_report']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/Data/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileName = basename($_FILES['youth_report']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['youth_report']['tmp_name'], $targetPath)) {
      CEagle::VerifyLifeEagleFromCSV($fileName);
      $_SESSION['feedback'] = ['type' => 'success', 'message' => 'File uploaded successfully and verification completed.'];
    } else {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed to save uploaded file.'];
    }
  } else if (isset($_POST['filename']) && !empty(trim($_POST['filename']))) {
    $fileName = basename(trim($_POST['filename']));
    CEagle::VerifyLifeEagleFromCSV($fileName);
    $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Verification completed for ' . htmlspecialchars($fileName)];
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_scout'])) {
  $memberID = $_POST['memberid'];
  $fullName = $_POST['fullname'];
  $unitStr  = $_POST['unitstr'];
  CEagle::AddMissingLifeScout($memberID, $fullName, $unitStr);
  // Refresh issues list
  CEagle::VerifyLifeEagleFromCSV($stats['file_name'] ?? 'Youth_Member_Age_Report.csv');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Verify Life & Eagle Scouts</title>
  <?php load_template("/src/Templates/header.php"); // reuse your header 
  ?>
</head>

<body>
  <?php load_template("/src/Templates/navbar.php"); ?>
  <?php load_template("/src/Templates/sidebar.php"); ?>

  <main class="main-content">
    <div class="container-fluid mt-5 pt-3">
      <?php if (isset($_SESSION['feedback'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['feedback']['type']) ?> alert-dismissible fade show">
          <?= htmlspecialchars($_SESSION['feedback']['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['feedback']); ?>
      <?php endif; ?>

      <h2>Verify Life & Eagle Scouts from Youth Member Age Report</h2>
      <p>Upload a new CSV or enter the name of an existing file in the <code>Data/</code> folder.</p>

      <form method="post" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

        <div class="row">
          <div class="col-md-6">
            <label class="form-label">Upload CSV File</label>
            <input type="file" name="youth_report" class="form-control" accept=".csv">
          </div>
          <div class="col-md-6">
            <label class="form-label">OR Existing Filename</label>
            <input type="text" name="filename" class="form-control" placeholder="Youth_Member_Age_Report.csv">
          </div>
        </div>
        <button type="submit" name="verify_submit" class="btn btn-primary mt-3">Upload & Run Verification</button>
      </form>

      <?php if (isset($_SESSION['verify_issues'])):
        $issues = $_SESSION['verify_issues'];
        $stats = $_SESSION['verify_stats'] ?? [];
      ?>
        <h4>Results for <?= htmlspecialchars($stats['file_name'] ?? 'report') ?></h4>
        <div class="alert alert-info">
          Total Life/Eagle Scouts in CSV: <strong><?= $stats['total_life_eagle_in_report'] ?? 0 ?></strong><br>
          Correctly in DB: <?= $stats['found_in_db'] ?? 0 ?><br>
          <span class="text-danger">Missing from DB: <?= $stats['missing_from_db'] ?? 0 ?></span><br>
          Mismatches: <?= $stats['mismatched_marking'] ?? 0 ?>
        </div>

        <?php if (empty($issues)): ?>
          <div class="alert alert-success">✅ All Life and Eagle Scouts are present and correctly marked in the database.</div>
        <?php else: ?>
          <table class="table table-striped table-bordered">
            <thead class="table-dark">
              <tr>
                <th>Name</th>
                <th>Member ID</th>
                <th>Rank in Report</th>
                <th>Age / Grade</th>
                <th>Issue</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($issues as $row): ?>
                <tr>
                  <td>
                    <?php if (!empty($row['scoutid'])): ?>
                      <a href="index.php?page=edit-select-scout&Scoutid=<?= htmlspecialchars($row['scoutid']) ?>" target="_blank">
                        <?= htmlspecialchars($row['name']) ?>
                      </a>
                    <?php else: ?>
                      <?= htmlspecialchars($row['name']) ?>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['memberid']) ?></td>
                  <td><?= htmlspecialchars($row['rank_in_report']) ?></td>
                  <td><?= htmlspecialchars($row['age'] . ' / ' . ($row['grade'] ?? '')) ?></td>
                  <td>
                    <?= htmlspecialchars($row['issue']) ?>
                    <?php if ($row['issue'] === 'Not found in database' && strpos($row['rank_in_report'], 'Life Scout') !== false): ?>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Add this Life Scout to database?')">
                        <input type="hidden" name="add_scout" value="1">
                        <input type="hidden" name="memberid" value="<?= htmlspecialchars($row['memberid']) ?>">
                        <input type="hidden" name="fullname" value="<?= htmlspecialchars($row['name']) ?>">
                        <input type="hidden" name="unitstr" value="<?= htmlspecialchars($stats['unit_header'] ?? 'Organization Name: Troop 0012 (B)') ?>">
                        <button type="submit" class="btn btn-sm btn-success">Add Scout</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </main>

</body>

</html>