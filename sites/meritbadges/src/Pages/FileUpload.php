<?php

declare(strict_types=1);

// Start session with secure settings
session_start([
  'cookie_httponly' => true,
  'cookie_secure' => true, // Enable if using HTTPS
  'use_strict_mode' => true,
]);

/**
 * File: FileUpload.php
 * Description: Handles file uploads for Centennial District Advancement Data
 * Author: Richard Hall
 * Copyright: 2017-2024 Richard Hall
 * License: Proprietary, see LICENSE file for details
 */

//use App\CMeritBadges; // Assuming PSR-4 namespace

// Load dependencies (use Composer autoloader if possible)
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/sqlStatements.php';
require_once __DIR__ . '/CMeritBadges.php';

// Secure session check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: /index.php', true, 403);
  exit;
}

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
  http_response_code(403);
  die('Invalid CSRF token');
}

try {
  $CMeritBadges = CMeritBadges::getInstance();
} catch (Exception $e) {
  error_log('Failed to initialize CMeritBadges: ' . $e->getMessage());
  die('An error occurred. Please try again later.');
}

// Configuration
const UPLOAD_DIR = __DIR__ . '/Data/';
const ALLOWED_EXTENSIONS = ['csv'];
const MAX_FILE_SIZE = 4 * 1024 * 1024; // 4MB
const VALID_REPORT_TYPES = ['Counselors', 'YPT', 'CouncilList'];

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
    $uploadPath = UPLOAD_DIR . basename($fileName);

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
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $fileTmpName);
    finfo_close($fileInfo);
    // if ($mimeType !== 'text/csv' && $mimeType !== 'application/csv') {
    if ($mimeType !== 'text/plain') {
      $errors[] = 'Invalid file type. Please upload a valid CSV file.';
    }

    // Process file if no errors
    if (empty($errors)) {
      if (move_uploaded_file($fileTmpName, $uploadPath)) {
        $reportType = $_POST['submit'];
        try {
          switch ($reportType) {
            case 'Counselors':
              $CMeritBadges->UpdateCounselors($fileName);
              $successMessage = 'Counselors list updated successfully.';
              break;
            case 'YPT':
              $CMeritBadges->UpdateYPT($fileName);
              $successMessage = 'YPT list updated successfully.';
              break;
            case 'CouncilList':
              $CMeritBadges->UpdateCouncilList($fileName);
              $successMessage = 'Council list updated successfully.';
              break;
            default:
              $errors[] = 'Unknown report type requested.';
              break;
          }
        } catch (Exception $e) {
          $errors[] = 'Failed to process the file: ' . $e->getMessage();
          error_log('File processing error: ' . $e->getMessage());
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

// Template loader function
function load_template($file)
{
  $path = BASE_PATH . $file;
  if (file_exists($path)) {
    require_once $path;
  } else {
    error_log("Template $file is missing.");
    die('An error occurred. Please try again later.');
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template('head.php'); ?>
</head>

<body>
  <header id="header" class="header sticky-top" role="banner">
    <?php load_template('navbar.php'); ?>
  </header>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <?php include __DIR__ . '/sidebar.php'; ?>
      <div class="col py-3">
        <div class="container px-lg-5">
          <div class="p-4 p-lg-5 bg-light rounded-3">
            <h1 class="display-5 fw-bold text-center">File Upload</h1>
            <p class="fs-5 text-center">Upload a CSV file for processing (Counselors, YPT, or Council List).</p>

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

            <form action="$_SERVER['PHP_SELF']" method="post" enctype="multipart/form-data" aria-describedby="form-instructions">
              <div class="mb-3">
                <label for="fileToUpload" class="form-label visually-hidden">Select CSV file</label>
                <input class="form-control" type="file" name="the_file" id="fileToUpload" accept=".csv" required>
                <div id="form-instructions" class="form-text">Upload a valid CSV file (max 4MB).</div>
              </div>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
              <div class="mb-3">
                <label class="form-label">Report Type</label>
                <select class="form-select" name="submit" required>
                  <option value="Counselors">Counselors</option>
                  <option value="YPT">YPT</option>
                  <option value="CouncilList">Council List</option>
                </select>
              </div>
              <button class="btn btn-primary" type="submit">Upload File</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include __DIR__ . '/Footer.php'; ?>
  <script src="/bootstrap-5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>