<?php

load_class(BASE_PATH . '/src/Classes/CCounselor.php');
$Counselor = cCounselor::getInstance();

$CMBCollege = CMBCollege::getInstance();

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
  header('Location: index.php?page=login');
  exit;
}

// Handle form submission
$bPreview = isset($_POST['Preview']);
$collegeYearSet = false;


// Handle form submission
$action = $_POST['action'] ?? null;
$bPreview = ($action === 'preview') || isset($_POST['Preview']);
$collegeYearSet = false;

if (isset($_POST['CollegeYear']) && !empty($_POST['CollegeYear'])) {
  $CollegeYear = $_POST['CollegeYear'];
  setYear($CollegeYear);
  $collegeYearSet = true;
}

// Only proceed if form was submitted with a valid action
if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === ($_SESSION['csrf_token'] ?? '')) {
  if ($action === 'preview' || $action === 'send') {
    if ($collegeYearSet || !empty($_POST['CounselorName'])) {
      // Build safe query
      $dbc = $Counselor->getDbConn();
      $query = "SELECT * FROM college_counselors WHERE College = ? ";
      $params = [$CollegeYear ?? $CMBCollege->GetYear()];
      $types = "s";

      if (!empty($_POST['CounselorName'])) {
        $CounselorID = $_POST['CounselorName'];
        $query .= "AND BSAId = ? ";
        $params[] = $CounselorID;
        $types .= "s";
      }

      $query .= "ORDER BY LastName, FirstName, MBPeriod";

      // Escape values safely
      $stmt_query = $query;
      foreach ($params as $param) {
        $stmt_query = preg_replace('/\?/', "'" . $dbc->real_escape_string($param) . "'", $stmt_query, 1);
      }

      $results = $Counselor->doQuery($stmt_query);

      if ($results && $results->num_rows > 0) {
        $Counselor->EmailCounselors($results, $bPreview);

        if ($action === 'send' && !$bPreview) {
          $_SESSION['feedback'] = [
            'type' => 'success',
            'message' => 'Emails have been sent successfully!'
          ];
        } elseif ($action === 'preview') {
          $_SESSION['feedback'] = [
            'type' => 'info',
            'message' => 'Preview mode: Emails displayed above (not sent).'
          ];
        }

        $results->free();
      } else {
        $_SESSION['feedback'] = [
          'type' => 'warning',
          'message' => 'No counselors found matching your selection.'
        ];
      }
    } else {
      $_SESSION['feedback'] = [
        'type' => 'danger',
        'message' => 'Please select a college year or counselor.'
      ];
    }
  }
}
?>

<div class="row justify-content-center mt-4">
  <div class="col-lg-10 col-xl-8">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
          <i class="fas fa-envelope me-2"></i>Email Counselors
        </h4>
      </div>
      <div class="card-body p-4 p-md-5">
        <div class="alert alert-warning mb-4">
          <strong>Warning:</strong> Clicking "Send Emails" will immediately send emails to all selected counselors.
          Use the <strong>Preview Emails</strong> option first to review content.
        </div>

        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">

          <!-- College Year Selector -->
          <?php $Counselor->SelectCollegeYear($CMBCollege->GetYear(), "Email Counselors", false); ?>

          <hr class="my-4">

          <!-- Optional: Single Counselor Selector -->
          <?php $Counselor->SelectCounselor($CMBCollege->GetYear(), false); ?>

          <hr class="my-4">

          <!-- Preview Option -->
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="Preview" id="Preview" value="1" <?php echo $bPreview ? 'checked' : ''; ?>>
            <label class="form-check-label text-primary fw-semibold" for="Preview">
              <i class="fas fa-eye me-2"></i>Preview Emails Only (No emails will be sent)
            </label>
          </div>

          <div class="d-grid gap-3 d-md-flex justify-content-md-end">
            <button type="submit" name="action" value="preview" class="btn btn-outline-secondary btn-lg">
              <i class="fas fa-eye me-2"></i>Preview & Review
            </button>
            <button type="submit" name="action" value="send" class="btn btn-danger btn-lg"
              onclick="return confirm('Are you SURE you want to send emails now? This cannot be undone.');">
              <i class="fas fa-paper-plane me-2"></i>Send Emails Now
            </button>
          </div>
        </form>

        <?php if (isset($_SESSION['feedback'])): ?>
          <div class="alert alert-<?php echo htmlspecialchars($_SESSION['feedback']['type']); ?> mt-4">
            <?php echo htmlspecialchars($_SESSION['feedback']['message']); ?>
          </div>
          <?php unset($_SESSION['feedback']); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>