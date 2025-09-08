<?php
// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  die('An error occurred. Please try again later.');
}
?>
<!-- Sidebar -->
<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-light" id="sidebar">
  <a href="?page=home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
    <span class="fs-4">Menu</span>
  </a>
  <hr>

  <ul class="nav nav-pills flex-column mb-auto">
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Life Scouts</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=edit-scout">Edit/Update Scout</a></li>
          <li><a class="dropdown-item" href="?page=active-life">All Active Life Scouts</a></li>
          <li><a class="dropdown-item" href="?page=audit-scout">Audit Scout</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Eagles</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=eagle-unit">Eagle Scouts By Unit</a></li>
          <li><a class="dropdown-item" href="?page=eagle-year">Eagle Scouts By Year</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Coaches</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=coach-edit">Edit/Update Coach</a></li>
          <li><a class="dropdown-item" href="?page=coach-active">Active Coaches</a></li>
          <li><a class="dropdown-item" href="?page=coach-inactive">Inactive Coaches</a></li>
          <li><a class="dropdown-item" href="?page=coach-ypt">YPT Report</a></li>
          <li><a class="dropdown-item" href="?page=coach-report">Workload Report</a></li>
          <li><a class="dropdown-item" href="?page=coach-history">Workload History</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Reports</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=report-allscouts">All Scouts</a></li>
          <li><a class="dropdown-item" href="?page=report-ageout">Age Out</a></li>
          <li><a class="dropdown-item" href="?page=report-agedout">Aged Out</a></li>
          <li><a class="dropdown-item" href="?page=report-nopreview">Did Not Attend Preview</a></li>
          <li><a class="dropdown-item" href="?page=report-noproposal">Lacking Proposal Approval</a></li>
          <li><a class="dropdown-item" href="?page=report-proposal">Approved Proposal</a></li>
          <li><a class="dropdown-item" href="?page=report-ebor">Pending EBOR</a></li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="?page=policy">Policies</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="?page=logout">Logout</a>
      </li>
    <?php else: ?>
      <li class="nav-item">
        <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a>
      </li>
    <?php endif; ?>
  </ul>

  <button class="btn btn-outline-secondary mt-3 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
    Close Sidebar
  </button>

  <!-- Footer with GitHub repository commit date -->
  <div class="mt-auto text-muted small">
    <?php
    $cache_file = 'last_updated.txt';
    $cache_duration = 24 * 60 * 60; // 24 hours in seconds
    $commit_date = null;
    $http_code = 0; // Initialize to track HTTP status

    // Check if cache exists and is recent
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_duration) {
      $commit_date = file_get_contents($cache_file);
    } else {
      // GitHub API settings
      $owner = "rhall290472";
      $repo = "centennial";
      $api_url = "https://api.github.com/repos/$owner/$repo/commits?per_page=1";
      $token = defined('GITHUB_TOKEN') ? GITHUB_TOKEN : ''; // Load token from config.php

      $ch = curl_init($api_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "PHP-App/1.0");
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/vnd.github.v3+json"
      ]);
      if (!empty($token)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Accept: application/vnd.github.v3+json",
          "Authorization: token $token"
        ]);
      }
      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($http_code == 200 && $response) {
        $commits = json_decode($response, true);
        if (!empty($commits)) {
          $commit_date = $commits[0]['commit']['committer']['date'];
          // Save to cache
          file_put_contents($cache_file, $commit_date);
        }
      } else {
        // Log error for debugging
        error_log("GitHub API error: HTTP $http_code, Response: $response, URL: $api_url, Token used: " . (empty($token) ? 'none' : 'provided'));
      }
    }

    // Display the date
    if ($commit_date) {
      $formatted_date = date("F j, Y", strtotime($commit_date));
      echo "Last updated: " . htmlspecialchars($formatted_date);
    } else {
      if ($http_code == 403) {
        echo "Last updated: Unknown (token lacks permissions or organization restrictions)";
      } elseif ($http_code == 401) {
        echo "Last updated: Unknown (invalid or missing token)";
      } elseif ($http_code == 404) {
        echo "Last updated: Unknown (repository not found)";
      } else {
        echo "Last updated: Unknown (API error)";
      }
    }
    ?>

    <?php echo "Copyright &copy; " . date('Y') . " " . $_SERVER['HTTP_HOST']; ?>

  </div>
</div>