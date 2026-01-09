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

// Load required classes for file uploads
load_class(__DIR__ . '/../src/Classes/CMBCollege.php');

$CMBCollege = CMBCollege::getInstance();


// Simple routing based on 'page' GET parameter
$page = filter_input(INPUT_GET, 'page') ?? 'home';
$page = strtolower(trim($page));
$valid_pages = [
  'home',
  'signup',
  'view-schedule',
  'view-badges',
  'view-counselors',

  'scout-data',
  'scout-import',
  'scout-schedule',
  'scout-emails',

  'counselor-import',
  'counselor-data',
  'counselor-schedule',
  'counselor-emails',
  'counselor-stats',

  'rpt-roomschedule',
  'rpt-csvfile',
  'rpt-stats',
  'rpt-doubleknot',
  'rpt-details',

  'fileupload',

  'logout',
  'login'
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
    load_class(__DIR__ . '/../src/Classes/CMBCollege.php');
    $CMBCollege = CMBCollege::getInstance();
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
      $sql = "SELECT Userid, username, password, enabled, role FROM users WHERE username = ?";
      if ($stmt = mysqli_prepare($CMBCollege->getDbConn(), $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $enabled, $role);
            if (mysqli_stmt_fetch($stmt)) {
              if (password_verify($password, $hashed_password) && $enabled) {
                $_SESSION["loggedin"] = true;
                $_SESSION["Userid"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["enabled"] = $enabled;
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Login successful.'];
                $_SESSION['Role'] = $role;
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
          throw new Exception("Database query failed: " . mysqli_error($CMBCollege->getDbConn()));
        }
        mysqli_stmt_close($stmt);
      } else {
        throw new Exception("Failed to prepare statement: " . mysqli_error($CMBCollege->getDbConn()));
      }
    } catch (Exception $e) {
      error_log("index.php - Login error: " . $e->getMessage(), 0);
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'An error occurred during login. Please try again later.'];
      header("Location: index.php?page=login");
    }
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
              <!-- <p class="fs-4"><?php echo PAGE_DESCRIPTION; ?></p> -->
              <hr>

              <h3 style="text-align: center" ;>Centennial Districts will be holding a Merit Badge College on <?php echo $CMBCollege->GetDate($CMBCollege->getyear()); ?> </br>at
                <?php echo $CMBCollege->GetLocation($CMBCollege->getyear()) . " " . $CMBCollege->GetAddress($CMBCollege->getyear()); ?>,
                from <?php echo $CMBCollege->GetStartTime($CMBCollege->getyear()) . " to " . $CMBCollege->GetEndTime($CMBCollege->getyear()) ?>.</h3>
              <hr />
              <p>The Districts would like to welcome all Merit Badge counselors to please consider helping with this advancement
                opportunity for the Scouts.</p>
              <p>The purpose of the Merit Badge College (MBC) is to offer Scouts an opportunity to meet with highly
                qualified professionals to learn and foster development of lifelong interests. Particular emphasis is given
                to Eagle Required, career and hobby oriented Merit Badges (MB) especially those MB&apos;s with a limited
                availability of counselors.</p>

              <h4>Counselors:</h4>
              <p>Please view the <a href='./index.php?page=view-schedule'>College schedule </a> to see what Merit Badges and period(s) have already been selected. You may offer a duplicate merit
                badge but just at a different time.</p>

              <p>To sign up to support this district event please select the counselors sign up link to the left and complete
                the sign up form.</p>

              <p>Once you select your name from the counselors drop down, only the Merit badges that you are approved for will be shown</p>



            </div>
          </div>
      <?php
          break;
        case 'signup':
          include('../src/Pages/CounselorSelect.php');
          break;
        case 'view-schedule':
          include('../src/Pages/ViewSchedule.php');
          break;
        case 'view-badges':
          include('../src/Pages/ViewByBadges.php');
          break;
        case 'view-counselors':
          include('../src/Pages/ViewByCounselor.php');
          break;
        case 'scout-data':
          include('../src/Pages/EnterScout.php');
          break;
        case 'scout-import':
          include('../src/Pages/ImportScout.php');
          break;
        case 'scout-schedule':
          include('../src/Pages/ViewByScoutSchedule.php');
          break;
        case 'scout-emails':
          include('../src/Pages/EmailScouts.php');
          break;

        case 'counselor-import':
          include('../src/Pages/ImportCounselor.php');
          break;
        case 'counselor-data':
          include('../src/Pages/EditCounselor.php');
          break;
        case 'counselor-schedule':
          include('../src/Pages/ViewByCounselorSchedule.php');
          break;
        case 'counselor-emails':  
          include('../src/Pages/EmailCounselors.php');
          break;
        case 'counselor-stats':
          include('../src/Pages/CounselorsStats.php');
          break;

        case 'rpt-roomschedule':
          include('../src/Pages/ViewByRoom.php');
          break;
        case 'rpt-csvfile':
          include('../src/Pages/CreateScoutbookCSV.php');
          break;
        case 'rpt-stats':
          include('../src/Pages/ViewCollegeStats.php');
          break;
        case 'rpt-doubleknot':
          include('../src/Pages/DoubleKnot.php');
          break;
        case 'rpt-details': 
          include('../src/Pages/CollegeDetails.php');
          break;

        case 'fileupload':
          include('../src/Pages/FileUpload.php');
          break;

        case 'login':
          include('login.php');
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