<?php
/*
 * Main entry point for the Centennial District Advancement website.
 * Handles routing, form submissions, file uploads, and includes views based on the 'page' GET parameter.
 */

// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}
ob_start();

// Load configuration
if (file_exists(__DIR__ . '/../config/config.php')) {
  require_once __DIR__ . '/../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}

$files = [
  SHARED_PATH . 'src/Classes/cAdultLeaders.php',
  __DIR__ . '/../src/Classes/CAdmin.php',
  __DIR__ . '/../src/Classes/CMeritBadges.php'
];
foreach ($files as $file) {
  if (!file_exists($file)) {
    error_log("Missing file: $file");
  }
}


// Define SITE_URL fallback if not set
if (!defined('SITE_URL')) {
  define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/centennial/sites/advancement');
}

// Load required classes for file uploads
load_class(__DIR__ . '/../src/Classes/CMeritBadges.php');

// FileUploader class for secure file uploads
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

// Simple routing based on 'page' GET parameter
$page = filter_input(INPUT_GET, 'page') ?? 'home';
$page = strtolower(trim($page));
$valid_pages = [
  'home',
  'counselorsperbadge',
  'allcounselorsperbadge',
  'bycounselor',
  'bytroop',
  'counselorofmb',
  'forselectedtroop',
  'byselectedcounselor',
  'byfullselectedtroop',
  'uploadcounselors',
  'untrainedcounselors',
  'byexpireypt',
  'byinactive',
  'counsloresbadge',
  'reportmb15',
  'counselornoid',

  'counselornoemail',
  'counselor0badges',
  'specialtraining',
  'counselornobadge',
  'counselornounit',

  'editmeritbadge',
  'updatemeritbadge',

  'login',
  'logout',
  'register',
  'changepassword',
  ''
];
if (!in_array($page, $valid_pages)) {
  $page = 'home'; // Default to home if page is invalid
}

// Handle POST form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
    header("Location: index.php?page=$page");
    exit;
  }

  // Year selection
  if (isset($_POST['SubmitYear']) && in_array($page, $valid_pages)) {
    $SelYear = filter_input(INPUT_POST, 'Year', FILTER_SANITIZE_NUMBER_INT);
    if ($SelYear && is_numeric($SelYear) && $SelYear >= 2000 && $SelYear <= date("Y")) {
      $_SESSION['year'] = $SelYear;
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Refresh token
      $_SESSION['feedback'] = ['type' => 'success', 'message' => "Year set to $SelYear."];
    } else {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid year selected. Please choose a year between 2000 and ' . date("Y") . '.'];
    }
    header("Location: index.php?page=$page");
    exit;
  }

  // Login
  if ($page === 'login' && isset($_POST['username']) && isset($_POST['password'])) {
    load_class(__DIR__ . '/../src/Classes/CMeritBadges.php');
    $CMeritBadges = CMeritBadges::getInstance();
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    if (empty($username)) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Please enter username.'];
      header("Location: index.php?page=login");
      exit;
    }
    if (empty($password)) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Please enter your password.'];
      header("Location: index.php?page=login");
      exit;
    }
    try {
      $sql = "SELECT Userid, username, password, enabled FROM users WHERE username = ?";
      if ($stmt = mysqli_prepare($CMeritBadges->getDbConn(), $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $enabled);
            if (mysqli_stmt_fetch($stmt)) {
              //$newPassword = password_hash($password, PASSWORD_DEFAULT);
              if (password_verify($password, $hashed_password) && $enabled) {
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["enabled"] = $enabled;
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Login successful.'];
                header("Location: index.php?page=home");
              } else {
                $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid username or password or your account is not enabled.'];
                header("Location: index.php?page=login");
              }
            }
          } else {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid username or password.'];
            header("Location: index.php?page=login");
          }
        } else {
          throw new Exception("Database query failed: " . mysqli_error($CMeritBadges->getDbConn()));
        }
        mysqli_stmt_close($stmt);
      } else {
        throw new Exception("Failed to prepare statement: " . mysqli_error($CMeritBadges->getDbConn()));
      }
    } catch (Exception $e) {
      error_log("index.php - Login error: " . $e->getMessage() . " " . __FILE__ . " " . __LINE__, 0);
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'An error occurred during login. Please try again later.'];
      header("Location: index.php?page=login");
    }
    exit;
  }

  // Handle report form submissions
  if ($page === 'counselorsperbadge' && (isset($_POST['Submit']) || isset($_POST['SubmitCounselor']))) {
    $reportBy = filter_input(INPUT_GET, 'ReportBy', FILTER_DEFAULT) ?? filter_input(INPUT_POST, 'ReportBy', FILTER_DEFAULT);
    $reportBy = is_string($reportBy) ? htmlspecialchars(strip_tags(trim($reportBy)), ENT_QUOTES, 'UTF-8') : '';
    //$reportBy = filter_input(INPUT_GET, 'ReportBy', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_POST, 'ReportBy', FILTER_SANITIZE_STRING);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Refresh CSRF token
    header("Location: index.php?page=counselorsperbadge&ReportBy=" . urlencode($reportBy));
    exit;
  }
}

// Handle logout
if ($page === 'logout') {
  $_SESSION = [];
  session_destroy();
  $_SESSION['feedback'] = ['type' => 'success', 'message' => 'You have been logged out.'];
  header("Location: index.php?page=home");
  exit;
}

// Store form feedback
$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
unset($_SESSION['feedback']);

// Set CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php load_template("/src/Templates/header.php"); ?>
</head>

<body>
  <!-- Navbar -->
  <?php load_template("/src/Templates/navbar.php"); ?>

  <!-- Sidebar -->
  <?php load_template("/src/Templates/sidebar.php"); ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container-fluid mt-5 pt-3">
      <!-- Display Feedback -->
      <?php if (!empty($feedback)): ?>
        <div class="alert alert-<?php echo htmlspecialchars($feedback['type']); ?> alert-dismissible fade show" role="alert">
          <?php echo htmlspecialchars($feedback['message']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php
      switch ($page) {
        case 'home':
      ?>
          <div class="p-0 p-lg-0 bg-light rounded-3 text-center">
            <div class="m-4 m-lg-3">
              <h1 class="display-5 fw-bold"><?php echo PAGE_TITLE; ?></h1>
              <p class="fs-4"><?php echo PAGE_DESCRIPTION; ?></p>
              <hr>
              <iframe src="https://www.google.com/maps/d/embed?mid=1Hj3PV-LAAKDU5-IenX9esVcbfx1_Ruc&ehbc=2E312F" width="100%" height="800px"></iframe>
            </div>
          </div>
      <?php
          break;
        case 'counselorsperbadge':
        case 'bycounselor':
        case 'bytroop':
        case 'counselorofmb':
        case 'forselectedtroop':
        case 'byselectedcounselor':
        case 'byfullselectedtroop':
        case 'allcounselorsperbadge':
          include('../src/Pages/reports.php');
          break;
        case 'uploadcounselors':
          include('../src/Pages/FileUpload.php');
          break;
          // Load CMeritBadges to get database connection
          load_class(__DIR__ . '/../src/Classes/CMeritBadges.php');
          $CMeritBadges = CMeritBadges::getInstance();
          // Query to get counselors with more than 15 merit badges
          $sql = "SELECT counselors.Unit1, counselors.FirstName, counselors.LastName, counselors.Email, counselors.MemberID, counselors.ValidationDate, counselors.Active
            FROM counselors
            WHERE counselors.Active = 'Yes'
            AND (
              SELECT COUNT(*) 
              FROM counselormerit 
              WHERE counselormerit.FirstName = counselors.FirstName 
              AND counselormerit.LastName = counselors.LastName 
              AND counselormerit.Status <> 'DROP'
            ) > 15";
          $results = $CMeritBadges->doQuery($sql);
          if ($results) {
            $CAdmin = CAdmin::getInstance();
            $CAdmin->ReportMB15($results);
          } else {
            echo '<div class="alert alert-danger">Error fetching report data: ' . htmlspecialchars(mysqli_error($CMeritBadges->getDbConn())) . '</div>';
          }
          break;


        case 'untrainedcounselors':
        case "byexpireypt":
        case 'byinactive':
        case 'counsloresbadge';
        case 'reportmb15':
        case 'counselornoid':
        case 'counselornoemail':
        case 'counselor0badges':
        case 'specialtraining':
        case 'counselornobadge':
        case 'counselornounit':
          include('../src/Pages/AdminFunctions.php');
          break;

        case 'editmeritbadge':
          include('../src/Pages/MeritBadgeform/MeritBadgeform.php');
          break;
        case 'updatemeritbadge':
          include('../src/Pages/MeritBadgeform/UpdateMeritBadge.php');
          break;


        case 'login':
          include('login.php');
          break;

        case 'register':
            include('register.php');
            break;
         case 'changepassword':
            include('../src/Pages/changepassword.php');
            break;

        default:
          echo '<h1>404</h1><p>Page not found.</p>';
      }
      ?>
    </div>
  </main>

  <!-- Bootstrap 5 JS and Popper -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
  <!-- Custom JS -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      if (window.innerWidth < 992) {
        sidebar.classList.add('collapse');
      }
      window.addEventListener('resize', function() {
        if (window.innerWidth < 992) {
          sidebar.classList.add('collapse');
        } else {
          sidebar.classList.remove('collapse');
        }
      });
    });
  </script>



</body>

</html>