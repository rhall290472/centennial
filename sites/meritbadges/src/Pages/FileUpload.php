<?php

/**
 * Centennial District Advancement Data Websites
 * 
 * @author Richard Hall
 * @copyright 2017-2025 Richard Hall
 * @license Proprietary
 * @description Supports Centennial District Advancement Data reporting
 */

// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Configuration file missing.'];
  return;
}

load_class(BASE_PATH . '/src/Classes/CReports.php');
load_class(BASE_PATH . '/src/Classes/CMeritBadges.php');
// Load dependencies (use Composer autoloader if possible)
//require_once __DIR__ . '/sqlStatements.php';
//require_once __DIR__ . '/CMeritBadges.php';


// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
  http_response_code(403);
  die('Invalid CSRF token');
}

try {
  $CMeritBadges = CMeritBadges::getInstance();
} catch (Exception $e) {
  error_log('Failed to initialize CMeritBadges: ' . $e->getMessage() .'-'.__FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}

// Configuration
const ALLOWED_EXTENSIONS = ['csv'];
const VALID_REPORT_TYPES = ['Counselors'];

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  // Validate file upload
  if (!isset($_FILES['the_file']) || $_FILES['the_file']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'File upload failed. Please try again.';
  } else {
    $fileName = $_FILES['the_file']['name'];
    $fileSize = $_FILES['the_file']['size'];
    $fileTmpName = $_FILES['the_file']['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    //$uploadPath = UPLOAD_DIRECTORY . basename($fileName);
    $uploadDir .=  basename($fileName);

    // Validate file
    if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
      $errors[] = 'Only CSV files are allowed.';
    }
    if ($fileSize > MAX_FILE_SIZE) {
      $errors[] = 'File exceeds maximum size (4MB).';
    }
    if (!isValidFileName($fileName)) {
      $errors[] = 'Invalid file name. Use alphanumeric characters, underscores, or hyphens.';
    }
    // if (file_exists($uploadPath)) {
    // $errors[] = 'A file with this name already exists.';
    // }

    // Validate MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($fileTmpName);

    if ($mimeType !== 'text/plain') {
      $errors[] = 'Invalid file type. Please upload a valid CSV file.';
    }
    // Process file if no errors
    if (empty($errors)) {
      if (move_uploaded_file($fileTmpName, $uploadDir)) {
        $reportType = $_POST['submit'];
        try {
          switch ($reportType) {
            case 'Counselors':
              $CMeritBadges->UpdateCouncilList($uploadDir);
              $successMessage = 'Counselors list updated successfully.';
              break;
            default:
              $errors[] = 'Unknown report type requested.';
              break;
          }
        } catch (Exception $e) {
          $errors[] = 'Failed to process the file: ' . $e->getMessage();
          error_log('File processing error: ' . $e->getMessage() .'-'.__FILE__ . ' ' . __LINE__);
        }
      } else {
        $errors[] = 'Failed to upload the file. Please contact the administrator.';
      }
    }
  }
}

/**
 * Validate file name for security.
 *
 * @param string $fileName
 * @return bool
 */
function isValidFileName(string $fileName): bool
{
  return preg_match('/^[a-zA-Z0-9_\-\.]+$/', $fileName) === 1;
}


?>

<!DOCTYPE html>
<html lang="en">

<body>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php //include __DIR__ . '/sidebar.php'; 
      ?>
      <div class="col py-3">
        <div class="container px-lg-5">
          <div class="p-4 p-lg-5 bg-light rounded-3">
            <h1 class="display-5 fw-bold text-center">File Upload</h1>
            <p class="fs-5 text-center">Upload a CSV file for processing (Counselors, ...).</p>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger" role="alert">
                <ul>
                  <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
              <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($successMessage) ?>
              </div>
            <?php endif; ?>

            <form action="index.php?page=uploadcounselors" method="post" enctype="multipart/form-data" aria-describedby="form-instructions">
              <div class="mb-3">
                <label for="fileToUpload" class="form-label visually-hidden">Select CSV file</label>
                <input class="form-control" type="file" name="the_file" id="fileToUpload" accept=".csv" required>
                <div id="form-instructions" class="form-text">Upload a valid CSV file (max 4MB).</div>
              </div>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
              <button class="btn btn-primary" type="submit" name="submit" value="Counselors">Upload File</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="/bootstrap-5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>