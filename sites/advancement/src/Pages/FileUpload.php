<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

require_once __DIR__ . '/../config/config.php';
require_once 'CUnit.php';
require_once 'CPack.php';
require_once 'CTroop.php';
require_once 'CCrew.php';
require_once 'CAdvancement.php';
require_once 'cAdultLeaders.php';

class FileUploader
{
  private $allowedExtensions = ALLOWED_FILE_EXTENSIONS;
  private $maxFileSize = MAX_FILE_SIZE;
  private $uploadDir;

  public function __construct($uploadDir)
  {
    $this->uploadDir = rtrim($uploadDir, '/') . '/';
    if (!is_dir($this->uploadDir)) {
      mkdir($this->uploadDir, 0755, true);
    }
  }

  public function uploadFile($file, &$errors)
  {
    $uploadErrors = [
      UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the maximum size allowed by the server.",
      UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the maximum size allowed by the form.",
      UPLOAD_ERR_PARTIAL => "The file was only partially uploaded.",
      UPLOAD_ERR_NO_FILE => "No file was uploaded.",
      UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
      UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
      UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
    ];

    if ($file['error'] !== UPLOAD_ERR_OK) {
      $errors[] = $uploadErrors[$file['error']] ?? "Unknown file upload error.";
      return false;
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $this->allowedExtensions)) {
      $errors[] = "Invalid file extension. Only CSV files are allowed.";
      return false;
    }

    if ($file['size'] > $this->maxFileSize) {
      $errors[] = "File exceeds maximum size (4MB).";
      return false;
    }

    if ($file['type'] !== 'text/csv' && $file['type'] !== 'application/vnd.ms-excel') {
      $errors[] = "Invalid file type. Only CSV files are allowed.";
      return false;
    }

    $fileHandle = fopen($file['tmp_name'], 'r');
    $firstLine = fgetcsv($fileHandle);
    fclose($fileHandle);
    if ($firstLine === false || empty($firstLine)) {
      $errors[] = "File is not a valid CSV.";
      return false;
    }

    $uniqueFileName = uniqid('upload_', true) . '.csv';
    $uploadPath = $this->uploadDir . $uniqueFileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
      return $uniqueFileName;
    }

    $errors[] = "Failed to move uploaded file.";
    return false;
  }
}

// Authentication check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to upload files.'];
  header("Location: index.php?page=login");
  exit;
}

// CSRF token validation
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (isset($_POST['submit']) && (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
  header("Location: index.php?page=updatedata&update=" . urlencode($_POST['submit']));
  exit;
}

$errors = [];
$Update = filter_input(INPUT_POST, 'submit');

$classMap = [
  'UpdateTotals' => CUnit::class,
  'UpdatePack' => CPack::class,
  'UpdateTroop' => CTroop::class,
  'UpdateCrew' => CCrew::class,
  'TrainedLeader' => AdultLeaders::class,
  'Updateypt' => AdultLeaders::class,
  'UpdateVenturing' => CCrew::class,
  'UpdateAdventure' => CPack::class,
  'UpdateCommissioners' => CUnit::class,
  'UpdateFunctionalRole' => AdultLeaders::class,
];

$updateMethods = [
  'UpdateTotals' => ['ImportCORData'],
  'UpdatePack' => ['UpdatePack'],
  'UpdateTroop' => ['UpdateTroop'],
  'UpdateCrew' => ['UpdateCrew'],
  'TrainedLeader' => ['TrainedLeader'],
  'Updateypt' => ['Updateypt'],
  'UpdateVenturing' => ['UpdateVenturing'],
  'UpdateAdventure' => ['UpdateAdventure'],
  'UpdateCommissioners' => ['UpdateCommissioner'],
  'UpdateFunctionalRole' => ['UpdateFunctionalRole'],
];

if (isset($_POST['submit']) && isset($classMap[$Update])) {
  $uploader = new FileUploader(UPLOAD_DIRECTORY);
  $instance = $classMap[$Update]::getInstance();
  $uploadedFile = $uploader->uploadFile($_FILES['the_file'], $errors);

  if (empty($errors) && $uploadedFile) {
    try {
      $RecordsInError = call_user_func([$instance, $updateMethods[$Update][0]], $uploadedFile);
      unlink(UPLOAD_DIRECTORY . $uploadedFile); // Clean up
      if (in_array($Update, ['TrainedLeader', 'Updateypt'])) {
        CAdvancement::getInstance()->UpdateLastUpdated(strtolower(str_replace('Update', '', $Update)), '');
      }
      $_SESSION['feedback'] = [
        'type' => $RecordsInError == 0 ? 'success' : 'warning',
        'message' => $RecordsInError == 0 ? 'Data updated successfully.' : "$RecordsInError record(s) had errors."
      ];
    } catch (Exception $e) {
      error_log("Processing error for $Update: " . $e->getMessage(), 0);
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'An error occurred during processing.'];
    }
  } else {
    error_log("File upload error: " . implode(', ', $errors), 0);
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => implode(' ', $errors)];
  }
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Refresh CSRF token
  header("Location: index.php?page=updatedata&update=" . urlencode($Update));
  exit;
} else {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid update type.'];
  header("Location: index.php?page=updatedata");
  exit;
}
