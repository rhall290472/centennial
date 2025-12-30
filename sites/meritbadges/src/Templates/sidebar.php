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
          <i class="fs-4 bi-clipboard2-data"></i><span class="ms-1 d-none d-sm-inline">Reports</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=counselorsperbadge&ReportBy=ByMB">Counselors per Badge</a></li>
          <li><a class="dropdown-item" href="?page=allcounselorsperbadge&ReportBy=ByMB_ALL">All Counselors per Badge</a></li>
          <li><a class="dropdown-item" href="?page=ByCounselor&ReportBy=ByCounselor">Counselors</a></li>
          <li><a class="dropdown-item" href="?page=ByTroop&ReportBy=ByTroop">By Unit</a></li>
          <li><a class="dropdown-item" href="?page=CounselorofMB&ReportBy=CounselorofMB">By Merit Badge</a></li>
          <li><a class="dropdown-item" href="?page=ForSelectedTroop&ReportBy=ForSelectedTroop">By Troop</a></li>
          <li><a class="dropdown-item" href="?page=BySelectedCounselor&ReportBy=BySelectedCounselor">By Counselor</a></li>
          <li><a class="dropdown-item" href="?page=ByFullSelectedTroop&ReportBy=ByFullSelectedTroop">All for Unit</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi bi-upload"></i><span class="ms-1 d-none d-sm-inline">Uploads</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=uploadcounselors">Counselor List</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-backpack4"></i><span class="ms-1 d-none d-sm-inline">Functions</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=untrainedcounselors&AdminFunction=ByUntrained">Untrained Counselors</a></li>
          <li><a class="dropdown-item" href="?page=byexpireypt&AdminFunction=ByExpireypt">Expired YPT</a></li>
          <li><a class="dropdown-item" href="?page=byinactive&AdminFunction=ByInactive">Inactive Counselors</a></li>
          <li><a class="dropdown-item" href="?page=counsloresbadge&AdminFunction=ByCounselorsperBadge">Counselors per Badge</a></li>
          <li><a class="dropdown-item" href="?page=ReportMB15&AdminFunction=ByMB15">MB > 15</a></li>
          <li><a class="dropdown-item" href="?page=counselornoid&AdminFunction=ByNoID">Cousnelors with No MID</a></li>
          <li><a class="dropdown-item" href="?page=counselornoemail&AdminFunction=ByNoEmail">Counselors with No Email</a></li>
          <li><a class="dropdown-item" href="?page=counselor0badges&AdminFunction=MBCnoMB">Counselors with 0 Badges</a></li>
          <li><a class="dropdown-item" href="?page=specialtraining&AdminFunction=ByExpiredSpecialTraining">Special Training</a></li>
          <li><a class="dropdown-item" href="?page=counselornobadge&AdminFunction=ByMBwithnoCounselor">MB with no Counselor</a></li>
          <li><a class="dropdown-item" href="?page=counselornounit&AdminFunction=ByCounselorwithNoUnit">Counselor with No Unit</a></li>
        </ul>
      </li>
    <?php endif; ?>
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
      <li class="nav-item">
        <a class="nav-link" href="index.php?page=changepassword">Change Password</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="index.php?page=logout">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
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
    $owner = "rhall290472";
    $repo  = "centennial";
    //$cache_file = __DIR__ . "/cache/last_commit_cache.txt";
    $cache_duration = 3600; // 1 hour

    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_duration) {
      $commit_date = file_get_contents($cache_file);
    } else {
      // This endpoint is public, no auth needed, and rarely rate-limited
      $feed_url = "https://github.com/{$owner}/{$repo}/commits/main.atom";

      $ch = curl_init($feed_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "PHP-Commit-Date/1.0 (rhall290472@gmail.com)"); // Required by GitHub
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

      $feed = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);


      if ($feed === false) {
        // Hard cURL error (network, timeout, etc.)
        error_log("cURL error: " . curl_error($ch));
        // fall back to cache
      } elseif ($http_code !== 200) {
        // GitHub returned an error page (403, 404, 429, etc.)
        error_log("GitHub returned HTTP $http_code for $feed_url");
        // fall back to cache
      } elseif (empty(trim($feed))) {
        // Empty response (shouldn't happen, but safety)
        error_log("Empty response from GitHub");
        // fall back to cache
      } else {
        // Success! Parse the Atom feed
        if (preg_match('/<entry[^>]*>.*?<updated>(.*?)<\/updated>/is', $feed, $matches)) {
          $commit_date = $matches[1];
          file_put_contents($cache_file, $commit_date);
        }
      }

      // Fallback: if not found yet, try to load from cache anyway
      if (!$commit_date && file_exists($cache_file)) {
        $commit_date = file_get_contents($cache_file);
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