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

// Simple routing based on 'page' GET parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
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
  'updatedata',
  'settings'
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
  if (isset($_POST['SubmitYear']) && in_array($page, ['home', 'pack-summary', 'pack-below-goal', 'pack-meeting-goal', 'troop-summary', 'troop-below-goal', 'troop-meeting-goal', 'crew-summary', 'adv-report', 'membership-report'])) {
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
  if ($page === 'login' && isset($_POST['username']) && isset($_POST['password'])) {
    load_template('/src/Classes/CAdvancement.php');
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
  if ($page === 'updatedata' && isset($_FILES['the_file']) && isset($_POST['submit'])) {
    $allowed_updates = [
      'UpdateTotals',
      'UpdatePack',
      'UpdateTroop',
      'UpdateCrew',
      'TrainedLeader',
      'Updateypt',
      'UpdateVenturing',
      'UpdateAdventure',
      'UpdateCommissioners',
      'UpdateFunctionalRole'
    ];
    $update = filter_input(INPUT_POST, 'submit');
    if (!in_array($update, $allowed_updates)) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid update type.'];
      header("Location: index.php?page=updatedata&update=" . urlencode($update));
      exit;
    }
    if ($_FILES['the_file']['error'] !== UPLOAD_ERR_OK) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'File upload failed: Error code ' . $_FILES['the_file']['error'] . '.'];
      header("Location: index.php?page=updatedata&update=" . urlencode($update));
      exit;
    }
    $allowed_extensions = ['csv'];
    $file_ext = strtolower(pathinfo($_FILES['the_file']['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_extensions)) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid file type. Only CSV files are allowed.'];
      header("Location: index.php?page=updatedata&update=" . urlencode($update));
      exit;
    }
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($_FILES['the_file']['size'] > $max_size) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'File too large. Maximum size is 5MB.'];
      header("Location: index.php?page=updatedata&update=" . urlencode($update));
      exit;
    }
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0755, true);
    }
    $upload_path = $upload_dir . uniqid() . '.' . $file_ext;
    try {
      if (move_uploaded_file($_FILES['the_file']['tmp_name'], $upload_path)) {
        // TODO: Process the CSV file based on $update (replace FileUpload.php logic)
        // Placeholder: Assume processing succeeds
        $_SESSION['feedback'] = ['type' => 'success', 'message' => "File uploaded successfully for $update."];
        // Delete the file after processing (in real implementation, process first)
        unlink($upload_path);
      } else {
        throw new Exception("Failed to move uploaded file.");
      }
    } catch (Exception $e) {
      error_log("index.php - File upload error for $update: " . $e->getMessage(), 0);
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'An error occurred during file upload. Please try again later.'];
    }
    header("Location: index.php?page=updatedata&update=" . urlencode($update));
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
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="?page=home">Centennial District Advancement</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="?page=home">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo in_array($page, ['pack-summary', 'pack-below-goal', 'pack-meeting-goal']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Packs
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="?page=pack-summary">Summary</a></li>
              <li><a class="dropdown-item" href="?page=pack-below-goal">Below District Goal</a></li>
              <li><a class="dropdown-item" href="?page=pack-meeting-goal">Meeting District Goal</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo in_array($page, ['troop-summary', 'troop-below-goal', 'troop-meeting-goal']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Troops
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="?page=troop-summary">Summary</a></li>
              <li><a class="dropdown-item" href="?page=troop-below-goal">Below District Goal</a></li>
              <li><a class="dropdown-item" href="?page=troop-meeting-goal">Meeting District Goal</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo $page === 'crew-summary' ? 'active' : ''; ?>" href="?page=crew-summary">Crews</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo in_array($page, ['adv-report', 'membership-report']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Reports
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="?page=adv-report">Advancement Report</a></li>
              <li><a class="dropdown-item" href="?page=membership-report">Membership Report</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>" href="?page=settings">Settings</a>
          </li>
          <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
            <li class="nav-item">
              <a class="nav-link" href="?page=logout">Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-light" id="sidebar">
    <a href="?page=home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
      <span class="fs-4">Menu</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-person"></i><span class="ms-1 d-none d-sm-inline">Adults</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=untrained">Untrained Leaders</a></li>
          <li><a class="dropdown-item" href="?page=ypt">Expired YPT</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-emoji-surprise"></i><span class="ms-1 d-none d-sm-inline">Packs</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=pack-summary">Summary</a></li>
          <li><a class="dropdown-item" href="?page=pack-below-goal">Below District Goal</a></li>
          <li><a class="dropdown-item" href="?page=pack-meeting-goal">Meeting District Goal</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-backpack4"></i><span class="ms-1 d-none d-sm-inline">Troops</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=troop-summary">Summary</a></li>
          <li><a class="dropdown-item" href="?page=troop-below-goal">Below District Goal</a></li>
          <li><a class="dropdown-item" href="?page=troop-meeting-goal">Meeting District Goal</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-shield"></i><span class="ms-1 d-none d-sm-inline">Crews</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=crew-summary">Summary</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-clipboard2-data"></i><span class="ms-1 d-none d-sm-inline">Reports</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=adv-report">District Advancement Report</a></li>
          <li><a class="dropdown-item" href="?page=membership-report">Membership</a></li>
        </ul>
      </li>
      <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
        <li class="nav-item">
          <a class="nav-link" href="?page=logout">Logout</a>
        </li>
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a>
        </li>
      <?php endif; ?>
      <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo $page === 'updatedata' ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fs-4 bi-book text-danger"></i><span class="ms-1 d-none d-sm-inline text-danger">Admin</span>
          </a>
          <ul class="dropdown-menu">
            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                Upload Adult Data
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=updatedata&update=TrainedLeader">Training</a></li>
                <li><a class="dropdown-item" href="?page=updatedata&update=Updateypt">YPT</a></li>
              </ul>
            </li>
            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                Upload Membership Data
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdateTotals">Upload COR Data</a></li>
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdateFunctionalRole">Functional Roles</a></li>
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdateCommissioners">Assigned Commissioners</a></li>
              </ul>
            </li>
            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                Upload Pack Data
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdatePack">Advancements</a></li>
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdateAdventure">Awards</a></li>
              </ul>
            </li>
            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                Upload Troop Data
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdateTroop">Advancements</a></li>
              </ul>
            </li>
            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                Upload Crew Data
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdateCrew">Advancements</a></li>
                <li><a class="dropdown-item" href="?page=updatedata&update=UpdateVenturing">Venturing</a></li>
              </ul>
            </li>
            <li><a class="dropdown-item" href="<?php echo htmlspecialchars(SITE_URL . '/centennial/sites/advancement/src/Pages/ErrorLog.php'); ?>">View Error Log</a></li>
          </ul>
        </li>
      <?php endif; ?>
    </ul>
    <button class="btn btn-outline-secondary mt-3 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
      Close Sidebar
    </button>
  </div>

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
              <p class="fs-4">Here you will be able to review advancement reports for the Centennial District</p>
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
          include('../src/Pages/Ypt.php');
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
          include('../public/login.php');
          break;
        case 'updatedata':
          include('../src/Pages/UpdateData.php');
          break;
        case 'settings':
          echo '<h1>Settings</h1><p>Configure your application settings here.</p>';
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