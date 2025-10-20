<?php

/**
 * Centennial District Advancement Data Websites
 * 
 * @author Richard Hall
 * @copyright 2017-2025 Richard Hall
 * @license Proprietary
 * @description Supports Centennial District Advancement Data reporting
 */

// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Configuration file missing.'];
  return;
}

load_class(BASE_PATH . '/src/Classes/CReports.php');

// Sanitize input
$report = htmlspecialchars(filter_input(INPUT_GET, 'ReportBy') ?? '', ENT_QUOTES, 'UTF-8');
if (empty($report)) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Report parameter missing or invalid'];
  return;
}
?>

<div class="container-fluid">
  <?php
  try {
    $reports = CReports::getInstance();

    switch ($report) {
      case 'ByMB':
        $reports->reportMeritBadges(false);
        break;

      case 'ByMB_ALL':
        $queryByMB_ALL = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		      ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
		      WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP'
		      ORDER BY
		      	counselormerit.MeritName,
		      	counselors.LastName,
		      	counselors.FirstName;";

        $results = $reports->doQuery($queryByMB_ALL);
        $reports->reportMeritBadges($results, true);
        $results->free_result();
        break;

      case 'ByCounselor':
        echo '<p class="lead">Active Merit Badge Counselors in Centennial District</p>';
        $queryByCounselors = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
      		ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName )
      		WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP'
      		ORDER BY
      			counselors.LastName,
      			counselors.FirstName,
      			counselormerit.MeritName;";

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
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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

        if (isset($_POST['Submit']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
          $meritBadge = filter_input(INPUT_POST, 'MeritName', FILTER_SANITIZE_STRING);
          if ($meritBadge) {
            $reports->reportCounselorofMB($meritBadge);
          } else {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid Merit Badge selected.'];
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
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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

        if (isset($_POST['Submit']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
          $selectedTroop = filter_input(INPUT_POST, 'UnitName', FILTER_SANITIZE_STRING);
          if ($selectedTroop) {
            $method = $report === 'ForSelectedTroop' ? 'reportofSelectedTroop' : 'reportFullSelectedTroop';
            $reports->$method($selectedTroop);
          } else {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid Unit selected.'];
          }
        }
        break;

      case 'BySelectedCounselor':
        $querySelectedCounselor1 = "SELECT DISTINCTROW counselors.LastName, counselors.FirstName, counselors.MemberID FROM counselors
        	WHERE counselors.Active='YES'
        	ORDER BY
        		counselors.LastName,
        		counselors.FirstName";

        $results = $reports->doQuery($querySelectedCounselor1);
        ?>
        <form method="post" class="mb-4">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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

        if (isset($_POST['SubmitCounselor']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
          $selectedCounselor = filter_input(INPUT_POST, 'CounselorName', FILTER_SANITIZE_STRING);
          if ($selectedCounselor) {
            $reports->reportofSelectedCounselor($selectedCounselor);
          } else {
            $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid Counselor selected.'];
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
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'An error occurred while generating the report: ' . htmlspecialchars($e->getMessage())];
  }
  ?>
</div>
