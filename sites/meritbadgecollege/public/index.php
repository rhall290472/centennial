<?php
ob_start();
// Custom session path – must be inside your home dir, writable by PHP
$sessionDir = __DIR__ . '/../sessions';  // creates sessions/ sibling to public/ or wherever index.php lives

if (!is_dir($sessionDir)) {
  if (!mkdir($sessionDir, 0700, true)) {
    error_log("Failed to create session dir: $sessionDir");
  }
}

if (is_writable($sessionDir)) {
  ini_set('session.save_path', $sessionDir);
  // Optional: make sessions private
  ini_set('session.cookie_httponly', '1');
  ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? '1' : '0');
  ini_set('session.use_strict_mode', '1');  // Helps prevent fixation
} else {
  error_log("Custom session path NOT writable: $sessionDir");
}

// Then your existing session_start()
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
  ]);
}


/*
 * Main entry point for the Centennial District Advancement website.
 * Handles routing, form submissions, file uploads, and includes views based on the 'page' GET parameter.
 */

//ob_start();

if (file_exists(__DIR__ . '/../config/config.php')) {
  require_once __DIR__ . '/../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}

// Load required classes for file uploads
load_class(BASE_PATH . '/src/Classes/CMBCollege.php');
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
    $post_token   = $_POST['csrf_token']   ?? '';
    $session_token = $_SESSION['csrf_token'] ?? '';

    echo "POST length:   " . strlen($post_token)   . "\n";
    echo "SESSION length: " . strlen($session_token) . "\n\n";
    echo "POST hex:   " . bin2hex($post_token)   . "\n";
    echo "SESSION hex: " . bin2hex($session_token) . "\n\n";
    echo "POST raw:   ";
    var_dump($post_token);
    echo "SESSION raw: ";
    var_dump($session_token);
    echo "\nStrict comparison result: ";
    var_dump($post_token !== $session_token);
    echo "\nAfter trim(): ";
    var_dump(trim($post_token) !== trim($session_token));

    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
    header("Location: index.php?page=$page");
    exit;
  }

  // Login
  if ($page === 'login' && isset($_POST['username']) && isset($_POST['password'])) {
    // load_class(BASE_PATH . '/src/Classes/CMBCollege.php');
    // $CMBCollege = CMBCollege::getInstance();

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
                error_log("LOGIN SUCCESS - id=$id  username=$username");
                // Very important security improvements:
                //$_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // rotate token
                //session_regenerate_id(true);
                $_SESSION["loggedin"] = true;
                $_SESSION["Userid"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["enabled"] = $enabled;
                $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Login successful.'];
                $_SESSION['Role'] = $role;
                header("Location: index.php?page=home");
              } else {
                $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid username or password or your account is not enabled.'];
                header("Location: index.php?page=login");
              }
            }
          } else {
            error_log("LOGIN FAIL - verify=" . (password_verify($password, $hashed_password) ? 'true' : 'false') .
              "  enabled=" . ($enabled ? 'true' : 'false'));
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
      error_log("index.php - Login error: " . $e->getMessage() . " " . __FILE__ . " " . __LINE__, 0);
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'An error occurred during login. Please try again later.'];
      header("Location: index.php?page=login");
      exit;
    }
    exit;
  }
}

// Handle logout
if ($page === 'logout') {
  echo "page = " . $page;
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
get_csrf_token();


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
  <?php load_template("/src/Templates/sidebar.php", ['page' => $page]);
  ?>

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
<?php ob_end_flush(); ?>

</html>