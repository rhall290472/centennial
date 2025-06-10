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
  'updatedata'
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

  // Login
  if ($page === 'login' && isset($_POST['username']) && isset($_POST['password'])) {
    load_class(__DIR__ . '/../src/Classes/CAdvancement.php');
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

  $Update = filter_input(INPUT_POST, 'submit');
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

  if (!in_array($Update, $allowed_updates)) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid update type.'];
    header("Location: index.php?page=updatedata&update=" . urlencode($Update));
    exit;
  }

  $errors = [];
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
  <style>
    .full-container-img {
      width: 50%;
      height: auto;
    }

    .center {
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <?php load_template("/src/Templates/navbar.php"); ?>

  <!-- Sidebar -->
  <?php load_template("/src/Templates/sidebar.php"); ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container-fluid">
      <div class="row flex-nowrap">
        <div class="col py-3">

        </div>
      </div>
      <!-- </div> -->


      <!-- <div class="container-fluid mt-5 pt-3"> -->
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
          <?php
          $imagePath = defined('BASE_PATH') ? BASE_PATH . '/src/pages/img/DistrictAwards.png' : '/centennial/sites/districtawards/src/pages/img/DistrictAwards.png';
          $imagePath = "../src/pages/img/DistrictAwards.png";
          //debug_to_console($imagePath, 'Image Path');
          ?>
          <div class="container px-sm-5">
            <div class="p-4 p-sm-5 bg-light rounded-3 text-center">
              <div class="m-4 m-sm-5">
                <h1 class="display-5 fw-bold">Centennial District Awards</h1>
                <p class="fs-4">Submit a nomination for District Awards</p>
                <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
                  alt="Centennial District Awards Logo"
                  class="center">
              </div>
              <div class="py-1">
                <a class="btn btn-primary btn-lg" href="./OnLineNomination.php">Submit an Online Nomination</a>
                <a class="btn btn-primary btn-lg" href="./DocsPage.php">Download Nomination Form</a>
              </div>
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