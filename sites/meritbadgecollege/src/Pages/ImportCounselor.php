<?php
// ------------------------------------------------------------------
// src/Pages/ImportCounselor.php
// Improved, professional layout that fits within index.php structure
// ------------------------------------------------------------------
load_class(BASE_PATH . '/src/Classes/CScout.php');
$Scout = cScout::getInstance();

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
  header('Location: index.php?page=login');
  exit;
}

// Optional: Set college year if posted (your original logic)
if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
  $CollegeYear = $Scout->getYear();
  $GLOBALS["MBCollegeYear"] = $CollegeYear;
}
?>

<div class="row justify-content-center mt-4">
  <div class="col-lg-8 col-xl-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
          <i class="fas fa-upload me-2"></i>Import Merit Badge Counselors
        </h4>
      </div>
      <div class="card-body p-4 p-md-5">
        <p class="text-muted mb-4">
          This tool allows you to import a list of Merit Badge Counselors exported from
          <strong>my.scouting.org</strong> (typically a CSV or Excel file).
        </p>

        <form action="index.php?page=FileUpload" method="post" enctype="multipart/form-data">
          <!-- CSRF Token (required by your index.php POST handling) -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

          <div class="mb-4">
            <label for="the_file" class="form-label fw-semibold">
              Counselor List File
            </label>
            <input
              type="file"
              class="form-control form-control-lg"
              name="the_file"
              id="the_file"
              accept=".csv,.xlsx,.xls"
              required>
            <div class="form-text">
              Supported formats: CSV, XLSX, XLS.
              The file must be exported from the Merit Badge Counselor roster in my.scouting.org.
            </div>
          </div>

          <div class="d-grid">
            <button
              type="submit"
              name="submit"
              value="ImportCounselor"
              class="btn btn-primary btn-lg">
              <i class="fas fa-file-import me-2"></i>Import Counselors
            </button>
          </div>
        </form>

        <hr class="my-4">

        <div class="alert alert-info small">
          <strong>Tip:</strong> After importing, you can review and edit counselor data
          under <strong>Counselor Data → Edit Counselors</strong> in the admin menu.
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// Optional: Add Font Awesome if not already loaded in header.php
// If your header.php doesn't include Font Awesome, uncomment the line below:
// <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
?>