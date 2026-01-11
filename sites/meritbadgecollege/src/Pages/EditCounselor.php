<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

load_class(BASE_PATH . '/src/Classes/CCounselor.php');
$Counselor = cCounselor::getInstance();

$CMBCollege = CMBCollege::getInstance();

// Redirect if not logged in
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Access denied, You must be logged in to access this page.'];
  header("Location: index.php?page=home");
  exit;
}
?>

<head>
  <!-- Tom Select CSS (Bootstrap 5 theme) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css">

  <!-- Tom Select JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
</head>
<div class="row justify-content-center mt-4">
  <div class="col-lg-10 col-xl-8">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
          <i class="fas fa-user-edit me-2"></i>Edit Counselor Information
        </h4>
      </div>
      <div class="card-body p-4 p-md-5">
        <?php
        // Display counselor selection dropdown
        $querySelectedCounselor1 = "SELECT DISTINCTROW mbccounselors.LastName, mbccounselors.FirstName, mbccounselors.MemberID 
                                            FROM mbccounselors 
                                            WHERE mbccounselors.Active='Yes' 
                                            ORDER BY mbccounselors.LastName, mbccounselors.FirstName";
        $result_ByCounselor = $Counselor->doQuery($querySelectedCounselor1);

        if (!$result_ByCounselor) {
          $Counselor->function_alert("ERROR: MeritQuery($querySelectedCounselor1)");
          exit;
        }
        ?>

        <form method="post" class="mb-5">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label for="CounselorName" class="form-label fw-semibold">Select Counselor</label>
              <select class="form-select form-select-lg"
                id="CounselorName"
                name="CounselorName"
                placeholder="Type to search counselors..."
                required>
                <option value="">-- Choose a Counselor --</option>
                <?php while ($rowCerts = $result_ByCounselor->fetch_assoc()): ?>
                  <option value="<?php echo htmlspecialchars($rowCerts['MemberID']); ?>">
                    <?php echo htmlspecialchars($rowCerts['LastName'] . ' ' . $rowCerts['FirstName']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="submit" name="SubmitCounselor" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-search me-2"></i>Load Counselor Details
              </button>
            </div>
          </div>
        </form>

        <?php
        // If counselor selected, load and display edit form
        if (isset($_POST['SubmitCounselor']) && !empty($_POST['CounselorName'])) {
          $SelectedCounselor = $_POST['CounselorName'];

          $dbc = $Counselor->getDbConn();
          $SelectedCounselorEscaped = $dbc->real_escape_string($SelectedCounselor);

          $sql = "SELECT * FROM mbccounselors 
        INNER JOIN (meritbadges INNER JOIN mbccounselormerit ON meritbadges.MeritName = mbccounselormerit.MeritName)
        ON (mbccounselors.FirstName = mbccounselormerit.FirstName) AND (mbccounselors.LastName = mbccounselormerit.LastName)
        WHERE mbccounselors.MemberID = '$SelectedCounselorEscaped'
        ORDER BY meritbadges.MeritName ASC";
          $Results = $Counselor->doQuery($sql);
          if (!$Results) {
            $Counselor->function_alert("Error loading counselor details.");
          } else {
            $row = $Results->fetch_assoc();

            // Load existing signup data if any
            $Counselor->IsSignedUp($CMBCollege->GetYear(), $row['LastName'], $row['FirstName']);
          }
        ?>

          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="edit_counselor">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <h5 class="mt-4 mb-3">Counselor Information</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="element_1_1" class="form-label">First Name</label>
                <input type="text" class="form-control form-control-lg" id="element_1_1" name="element_1_1"
                  value="<?php echo htmlspecialchars($row['FirstName'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label for="element_1_2" class="form-label">Last Name</label>
                <input type="text" class="form-control form-control-lg" id="element_1_2" name="element_1_2"
                  value="<?php echo htmlspecialchars($row['LastName'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label for="element_1_3" class="form-label">Email</label>
                <input type="email" class="form-control form-control-lg" id="element_1_3" name="element_1_3"
                  value="<?php echo htmlspecialchars($row['Email'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label for="element_1_4" class="form-label">Phone Number</label>
                <input type="tel" class="form-control form-control-lg" id="element_1_4" name="element_1_4"
                  value="<?php echo htmlspecialchars($row['HomePhone'] ?? ''); ?>">
              </div>
              <div class="col-md-6">
                <label for="element_1_5" class="form-label">BSA Member ID</label>
                <input type="text" class="form-control form-control-lg" id="element_1_5" name="element_1_5"
                  value="<?php echo htmlspecialchars($row['MemberID'] ?? ''); ?>">
              </div>
            </div>

            <h5 class="mt-4 mb-3">Status Flags</h5>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="element_2_1" name="element_2_1" value="1"
                    <?php echo ($row['Is_SignedUp'] == 1) ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="element_2_1">Signed Up for MBC</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="element_2_2" name="element_2_2" value="1"
                    <?php echo ($row['Is_a_no'] == 1) ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="element_2_2">Is a No</label>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="element_2_3" name="element_2_3" value="1"
                    <?php echo ($row['Is_not_MBC'] == 1) ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="element_2_3">Not MBC</label>
                </div>
              </div>
            </div>

            <h5 class="mt-4 mb-3">Notes</h5>
            <div class="mb-4">
              <textarea class="form-control" id="element_3_1" name="element_3_1" rows="8"
                placeholder="Enter any additional notes..."><?php echo htmlspecialchars($row['Notes'] ?? ''); ?></textarea>
            </div>

            <div class="d-grid">
              <button type="submit" name="SubmitForm" class="btn btn-success btn-lg">
                <i class="fas fa-save me-2"></i>Save Counselor Changes
              </button>
            </div>
          </form>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect('#CounselorName', {
            sortField: { field: 'text', direction: 'asc' },  // Optional: sort alphabetically
            maxOptions: null,                                // Show all options (no limit)
            searchField: ['text'],                           // Search by name
            placeholder: 'Type to search counselors...'
        });
    });
</script>