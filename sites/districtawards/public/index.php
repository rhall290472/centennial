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
  'jl-year',

  'cm-year',
  'rcm-year',
  'dl-year',
  'rdl-year',
  'pcm-year',
  'rpcm-year',
  'outleader',
  'keyscout',

  'sm-year',
  'rsm-year',
  'tcm-year',
  'rtcm-year',
  'outleader',
  'keyscout',

  'ca-year',
  'rca-year',
  'skip-year',
  'rskip-year',
  'cscm-year',
  'rcssm-year',
  'keyscout',

  'beaward',
  'dam',
  'dco-year',
  'rdcm-year',
  'dcm-year',

  'fofs',

  'rpt-nom-hist-all',
  'rpt-awardees',
  'rpt-denials',
  'rpt-avail-awards',
  'rpt-nom-hist',
  'rpt-award-hist',
  'rpt-unit-his',
  'rpt-nom-id',
  'rpt-ballot',

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
    load_class(BASE_PATH . '/src/Classes/CDistrictAwards.php');
    $CAdvancement = CDistrictAwards::getInstance();
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
        max-width: 800px;
        /* Prevent the image from becoming too large */
        height: 50%;
        max-height: 600px;
        display: block;
        /* Ensure the image is a block element for centering */
    }

    .center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: fit-content;
        /* Ensure the container respects the image's width */
    }

    @media (max-width: 576px) {
        .full-container-img {
            width: 80%;
            /* Make the image larger relative to the container on small screens */
            max-width: 300px;
            /* Cap the size for very small screens */
        }
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
            <div class="alert alert-<?php echo htmlspecialchars($feedback['type']); ?> alert-dismissible fade show"
                role="alert">
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

            <div class="container px-lg-5 py-5">
                <div class="p-4 p-sm-5 bg-light rounded-3 text-center">
                    <div class="m-4 m-sm-5">
                        <h1 class="display-5 fw-bold">Centennial District Awards</h1>
                        <p class="fs-4">Submit a nomination for District Awards</p>
                    </div>
                    <div>
                      <h4>Rookies of the Year:</h4><p>(Cubmaster, Scoutmaster, Crew Advisor, Skipper and
                          Troop, Crew, Skipper and Pack Committee Member)
                          This is given to the new registered “Rookie” Leader who has done an outstanding job in
                          their first year of service.</p>
                      <h4>Leaders of the Year:</h4><p>(Cubmaster, Scoutmaster, Crew Advisor, Skipper and
                        This recognition is given to the experienced registered Scouting leader who has given
                        exceptional service throughout the past year towards the success of their unit.
                        (Crew, Skipper, Pack and Troop Committee Member)</p>
                      <h4>Commissioner and Rookie Commissioner of the Year Award:</h4><p>
                        This award recognizes a Commissioner and Rookies who has made a tremendous impact
                        with the units he/she has been working with.<p>
                      <h4>District Committee Member of the Year:</h4><p>
                        This award recognizes that District Committee member who warrants merit for service
                        and dedication given over the past year.</p>
                      <h4>Bald Eagle Award:</h4><p>
                        This award is presented to an individual registered in the Boy Scouts of America –
                        Centennial District who has had no youth involved for the past 5 years. They need to have
                        served in a variety of positions, and have countless hours of service to Scouting.<p>
                      <h4>Friends of Scouting Award:</h4><p>
                        This award is presented to an individual/group/company not registered in the Boy Scouts
                        of America – Centennial District who has had significantly supported Centennial District
                        and its youth. They may have donated time, materials or other resources to enable our
                        youth programs.</p>
                      <h4>Junior Leader Award:</h4><p>
                      This award is given to a registered Scout(s) under the age of 21 who has shown
                      exceptional leadership ability above and beyond that of his/her peers.<p>                    
                    </div>

                    <!-- <div>
                <img class="full-container-img"
                  src="<?php //echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
                  alt="Centennial District Awards Logo">
              </div> -->
                </div>
            </div>

            <?php
        break;
        case 'jl-year':

        case 'cm-year':
        case 'rcm-year':
        case 'dl-year':
        case 'rdl-year':
        case 'pcm-year':
        case 'rpcm-year':
        case 'outleader':
        case 'keyscout':

        case 'sm-year':
        case 'rsm-year':
        case 'tcm-year':
        case 'rtcm-year':

        case 'ca-year':
        case 'rca-year':
        case 'skip-year':
        case 'rskip-year':
        case 'cscm-year':
        case 'rcssm-year':
        case 'keyscout':

        case 'beaward':
        case 'dam':
        case 'dco-year':
        case 'rdcm-year':
        case 'dcm-year':

        case 'fofs':

          include('../src/Pages/NominationPage.php');
          break;



        case 'login':
          include('login.php');
          break;

        case 'rpt-nom-hist-all':
          include('../src/Pages/ReportAwardYear.php');
          break;
        case 'rpt-awardees':
          include('../src/Pages/ReportAwardedYear.php');
          break;
        case 'rpt-denials':
          include('../src/Pages/ReportDeniedYear.php');
          break;
        case 'rpt-avail-awards':
          include('../src/Pages/ReportAvailableAwards.php');
          break;
        case 'rpt-nom-hist':
          include('../src/Pages/ReportNomineeHistory.php');
          break;
        case 'rpt-award-hist':
          include('../src/Pages/ReportAwardHistory.php');
          break;
        case 'rpt-unit-his':
          include('../src/Pages/ReportUnitHistory.php');
          break;
        case 'rpt-nom-id':
          include('../src/Pages/ReportMemberID.php');
          break;
        case 'rpt-ballot':
          include('../src/Pages/ReportBallot.php');
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