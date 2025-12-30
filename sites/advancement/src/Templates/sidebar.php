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
    <span class="fs-4">Menu <?php echo isset($_SESSION['year']) ? $_SESSION['year'] : ""; ?></span>
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
        <li><a class="dropdown-item" href="?page=unitview">Unit View</a></li>
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
        <li><a class="dropdown-item" href="?page=unitview">Unit View</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4 bi-shield"></i><span class="ms-1 d-none d-sm-inline">Crews</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=crew-summary">Summary</a></li>
        <li><a class="dropdown-item" href="?page=unitview">Unit View</a></li>
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
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-backpack4"></i><span class="ms-1 d-none d-sm-inline text-danger">Upload Data</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=updatedata&update=TrainedLeader">Training</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=Updateypt">YPT</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateTotals">Upload COR Data</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateFunctionalRole">Functional Roles</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateCommissioners">Assigned Commissioners</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdatePack">Pack Advancements</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateAdventure">Pack Awards</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateTroop">Troop Advancements</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateVenturing">Venturing</a></li>
        </ul>
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