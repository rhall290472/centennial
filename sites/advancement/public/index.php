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

// Load configuration
if (file_exists(__DIR__ . '/../config/config.php')) {
  require_once __DIR__ . '/../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}

// Define SITE_URL fallback if not set
if (!defined('SITE_URL')) {
  define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/centennial/sites/advancement');
}

// Load required classes for file uploads
load_class(SHARED_PATH . 'src/Classes/CUnit.php');
load_class(__DIR__ . '/../src/Classes/CPack.php');
load_class(__DIR__ . '/../src/Classes/CTroop.php');
load_class(__DIR__ . '/../src/Classes/CCrew.php');
//load_class(__DIR__ . '/../src/Classes/CAdvancement.php');
load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');

//load_class(__DIR__ . '/../src/Classes/cAdultLeaders.php');
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');

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
      //return $uniqueFileName;
      return $uploadPath;
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
  'ypt',
  'untrained',
  'pack-summary',
  'pack-below-goal',
  'pack-meeting-goal',
  'troop-summary',
  'troop-below-goal',
  'troop-meeting-goal',
  'crew-summary',
  'adv-report',
  'membership-report',
  'login',
  'logout',
  'register',
  'changepassword',
  'updatedata',
  'unitview'
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
    //load_class(__DIR__ . '/../src/Classes/CAdvancement.php');
    load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');

    $CAdvancement = CAdvancement::getInstance();
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
      $sql = "SELECT id, username, password, enabled FROM users WHERE username = ?";
      if ($stmt = mysqli_prepare($CAdvancement->getDbConn(), $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $enabled);
            if (mysqli_stmt_fetch($stmt)) {
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
          throw new Exception("Database query failed: " . mysqli_error($CAdvancement->getDbConn()));
        }
        mysqli_stmt_close($stmt);
      } else {
        throw new Exception("Failed to prepare statement: " . mysqli_error($CAdvancement->getDbConn()));
      }
    } catch (Exception $e) {
      error_log("index.php - Login error: " . $e->getMessage(), 0);
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'An error occurred during login. Please try again later.'];
      header("Location: index.php?page=login");
    }
    exit;
  }

  // File upload for updatedata
  if ($page === 'updatedata' && isset($_FILES['the_file']) && isset($_POST['submit'])) {
    // Authentication check
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to upload files.'];
      header("Location: index.php?page=login");
      exit;
    }

    $Update = filter_input(INPUT_POST, 'submit');
    $allowed_updates = [
      'UpdateTotals',
      'UpdatePack',
      'UpdateTroop',
      'TrainedLeader',
      'Updateypt',
      'UpdateVenturing',
      'UpdateAdventure',
      'UpdateCommissioners',
      'UpdateFunctionalRole'
    ];

    if (!in_array($Update, $allowed_updates)) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid update type.'];
      header("Location: index.php?page=updatedata&update=" . urlencode($Update));
      exit;
    }

    $errors = [];
    $uploader = new FileUploader(UPLOAD_DIRECTORY);
    $classMap = [
      'UpdateTotals' => UNIT::class,
      'UpdatePack' => CPack::class,
      'UpdateTroop' => CTroop::class,
      'TrainedLeader' => AdultLeaders::class,
      'Updateypt' => AdultLeaders::class,
      'UpdateVenturing' => CCrew::class,
      'UpdateAdventure' => CPack::class,
      'UpdateCommissioners' => UNIT::class,
      'UpdateFunctionalRole' => AdultLeaders::class,
    ];

    $updateMethods = [
      'UpdateTotals' => ['ImportCORData'],
      'UpdatePack' => ['UpdatePack'],
      'UpdateTroop' => ['UpdateTroop'],
      'TrainedLeader' => ['TrainedLeader'],
      'Updateypt' => ['Updateypt'],
      'UpdateVenturing' => ['UpdateVenturing'],
      'UpdateAdventure' => ['UpdateAdventure'],
      'UpdateCommissioners' => ['UpdateCommissioner'],
      'UpdateFunctionalRole' => ['UpdateFunctionalRole'],
    ];

    $instance = $classMap[$Update]::getInstance();
    $uploadedFile = $uploader->uploadFile($_FILES['the_file'], $errors);

    if (empty($errors) && $uploadedFile) {
      try {
        $RecordsInError = call_user_func([$instance, $updateMethods[$Update][0]], $uploadedFile);
        //unlink(UPLOAD_DIRECTORY . $uploadedFile); // Clean up
        unlink($uploadedFile); // Clean up
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
  <?php load_template("/src/Templates/navbar.php", ['page' => $page]); ?>

  <!-- Sidebar -->
  <?php load_template("/src/Templates/sidebar.php", ['page' => $page]); ?>

  
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
              <h1 class="display-5 fw-bold">Centennial District Advancement</h1>
              <p class="fs-4">Here you will be able to review advancement data for the Centennial District</p>
              <hr>
              <iframe src="https://www.google.com/maps/d/embed?mid=1Hj3PV-LAAKDU5-IenX9esVcbfx1_Ruc&ehbc=2E312F" width="100%" height="800px"></iframe>
            </div>
          </div>
      <?php
          break;
        case 'untrained':
          include('../src/Pages/Untrained.php');
          break;
        case 'ypt':
          include('../src/Pages/YPT.php');
          break;
        case 'pack-summary':
          include('../src/Pages/pack_summary.php');
          break;
        case 'pack-below-goal':
          include('../src/Pages/pack_below_goal.php');
          break;
        case 'pack-meeting-goal':
          include('../src/Pages/pack_meeting_goal.php');
          break;
        case 'troop-summary':
          include('../src/Pages/troop_summary.php');
          break;
        case 'troop-below-goal':
          include('../src/Pages/troop_below_goal.php');
          break;
        case 'troop-meeting-goal':
          include('../src/Pages/troop_meeting_goal.php');
          break;
        case 'crew-summary':
          include('../src/Pages/crew_summary.php');
          break;
        case 'adv-report':
          include('../src/Pages/adv_report.php');
          break;
        case 'membership-report':
          include('../src/Pages/membership_report.php');
          break;
        case 'login':
          include('login.php');
          break;
        case 'updatedata':
          include('../src/Pages/UpdateData.php');
          break;
        case 'unitview':
          include('../src/Pages/Unit_View.php');
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