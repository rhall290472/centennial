<?php
// Define BASE_PATH fallback if not set in config.php
if (!defined('BASE_PATH')) {
  define('BASE_PATH', __DIR__ . '/');
}

// Secure session configuration
session_start([
  'cookie_httponly' => true,
  'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
  'use_strict_mode' => true,
  'cookie_samesite' => 'Strict'
]);

// Authentication check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('HTTP/1.1 403 Forbidden');
  exit('Access Denied');
}

/**
 * Centennial District Advancement Data Website
 * 
 * @author Richard Hall
 * @copyright 2017-2025 Richard Hall
 * @license Proprietary
 * @description Supports Centennial District Advancement Data reporting
 */

//require_once __DIR__ . '/vendor/autoload.php'; // Use Composer for dependency management

// Load configuration and dependencies
$requiredFiles = [
  'config.php' => 'Configuration file missing',
  'sqlStatements.php' => 'SQL statements file missing',
  'CReports.php' => 'Reports class file missing'
];

foreach ($requiredFiles as $file => $errorMessage) {
  $path = BASE_PATH . $file;
  if (!file_exists($path)) {
    error_log("Missing dependency: $file");
    http_response_code(500);
    exit('Internal Server Error');
  }
  require_once $path;
}

// Template loader with error handling
function loadTemplate(string $file): void
{
  $path = BASE_PATH . $file;
  if (!file_exists($path)) {
    error_log("Template not found: $file");
    http_response_code(500);
    exit('Internal Server Error');
  }
  require_once $path;
}

// Sanitize input
$report = filter_input(INPUT_GET, 'ReportBy', FILTER_SANITIZE_STRING) ?? '';
if (empty($report)) {
  error_log('Report parameter missing or invalid');
  http_response_code(400);
  exit('Invalid Request');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php loadTemplate('head.php'); ?>
</head>

<body>
  <header id="header" class="header sticky-top">
    <?php loadTemplate('navbar.php'); ?>
  </header>

  <main class="container-fluid">
    <?php
    try {
      $reports = CReports::getInstance();
      $report = $_GET['ReportBy'];

      switch ($report) {
        case 'ByMB':
          $reports->reportMeritBadges(false);
          break;

        case 'ByMB_ALL':
          $results = $reports->doQuery($queryByMB_ALL);
          $reports->reportMeritBadges($results, true);
          $results->free_result();
          break;

        case 'ByCounselor':
          echo '<p class="lead">Active Merit Badge Counselors in Centennial District</p>';
          $results = $reports->doQuery($queryByCounselors);
          $reports->reportCounselors($results);
          $results->free_result();
          break;

        case 'ByTroop':
          echo '<p class="lead">Active Merit Badge Counselors sorted by Troop</p>';
          $reports->reportTroop();
          break;

        case 'CounselorofMB':
          $meritBadges = $reports->doQuery("SELECT * FROM meritbadges WHERE Current = '1'");
    ?>
          <form method="post" class="mb-4">
            <div class="form-group">
              <label for="MeritName">Choose a Merit Badge:</label>
              <select class="form-control" id="MeritName" name="MeritName">
                <option value="">Select a Merit Badge</option>
                <?php
                while ($row = $meritBadges->fetch_assoc()) {
                  $name = htmlspecialchars($row['MeritName'], ENT_QUOTES, 'UTF-8');
                  echo "<option value=\"$name\">$name</option>";
                }
                ?>
              </select>
            </div>
            <button type="submit" name="Submit" class="btn btn-primary">Submit</button>
          </form>
          <?php
          $meritBadges->free_result();

          if (isset($_POST['Submit'])) {
            $meritBadge = filter_input(INPUT_POST, 'MeritName');
            if ($meritBadge) {
              $reports->reportCounselorofMB($meritBadge);
            }
          }
          break;

        case 'ForSelectedTroop':
        case 'ByFullSelectedTroop':
          $query = "SELECT DISTINCT counselors.Unit1 
                             FROM counselors 
                             INNER JOIN counselormerit ON counselors.LastName = counselormerit.LastName 
                             AND counselors.FirstName = counselormerit.FirstName
                             INNER JOIN meritbadges ON meritbadges.MeritName = counselormerit.MeritName
                             WHERE counselors.Active = 'Yes'
                             ORDER BY counselors.Unit1";
          $results = $reports->doQuery($query);
          ?>
          <form method="post" class="mb-4">
            <div class="form-group">
              <label for="UnitName">Choose a Unit:</label>
              <select class="form-control" id="UnitName" name="UnitName">
                <option value="">Select a Unit</option>
                <?php
                while ($row = $results->fetch_assoc()) {
                  $unit = htmlspecialchars($row['Unit1'], ENT_QUOTES, 'UTF-8');
                  echo "<option value=\"$unit\">$unit</option>";
                }
                ?>
              </select>
            </div>
            <button type="submit" name="Submit" class="btn btn-primary">Submit</button>
          </form>
          <?php
          $results->free_result();

          if (isset($_POST['Submit'])) {
            $selectedTroop = filter_input(INPUT_POST, 'UnitName', FILTER_SANITIZE_STRING);
            if ($selectedTroop) {
              $method = $report === 'ForSelectedTroop' ? 'reportofSelectedTroop' : 'reportFullSelectedTroop';
              $reports->$method($selectedTroop);
            }
          }
          break;

        case 'BySelectedCounselor':
          $results = $reports->doQuery($querySelectedCounselor1);
          ?>
          <form method="post" class="mb-4">
            <div class="form-group">
              <label for="CounselorName">Choose a Counselor:</label>
              <select class="form-control" id="CounselorName" name="CounselorName">
                <option value="">Select a Counselor</option>
                <?php
                while ($row = $results->fetch_assoc()) {
                  $id = htmlspecialchars($row['MemberID'], ENT_QUOTES, 'UTF-8');
                  $name = htmlspecialchars("{$row['LastName']} {$row['FirstName']}", ENT_QUOTES, 'UTF-8');
                  echo "<option value=\"$id\">$name</option>";
                }
                ?>
              </select>
            </div>
            <button type="submit" name="SubmitCounselor" class="btn btn-primary">Submit Counselor</button>
          </form>
    <?php
          $results->free_result();

          if (isset($_POST['SubmitCounselor'])) {
            $selectedCounselor = filter_input(INPUT_POST, 'CounselorName', FILTER_SANITIZE_STRING);
            if ($selectedCounselor) {
              $reports->reportofSelectedCounselor($selectedCounselor);
            }
          }
          break;

        case 'CounselorReport':
          // Implement or remove if not needed
          break;

        default:
          throw new InvalidArgumentException("Unknown report type: $report");
      }
    } catch (Exception $e) {
      error_log("Report error: " . $e->getMessage());
      http_response_code(500);
      echo '<div class="alert alert-danger">An error occurred while generating the report.</div>';
    }
    ?>
  </main>

  <?php loadTemplate('Footer.php'); ?>
</body>

</html>