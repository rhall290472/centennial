<!-- Sidebar -->
<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-light" id="sidebar">
  <a href="?page=home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
    <span class="fs-4">Menu</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4 bi-person"></i><span class="ms-1 d-none d-sm-inline">Counselors</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=signup">Sign Up</a></li>
        <li><a class="dropdown-item" href="?page=view-schedule">View College Schedule</a></li>
        <li><a class="dropdown-item" href="?page=view-badges">View Merit Badges</a></li>
        <li><a class="dropdown-item" href="?page=view-counselors">View Counselors</a></li>
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
          <i class="fs-4 bi-backpack4"></i><span class="ms-1 d-none d-sm-inline text-danger">Admin</span>
        </a>
        <ul class="dropdown-menu">
          <!-- Scouts Submenu -->
          <li class="dropdown-submenu">
            <a href="#submenu1" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fs-5 bi-book"></i><span class="ms-1">Scouts</span>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="index.php?page=scout-data">Enter Data</a></li>
              <li><a class="dropdown-item" href="index.php?page=scout-import">Import Data</a></li>
              <li><a class="dropdown-item" href="index.php?page=scout-schedule">Schedule</a></li>
              <li><a class="dropdown-item" href="index.php?page=scout-emails">Email Schedule</a></li>
            </ul>
          </li>
          <!-- Counselors Submenu -->
          <li class="dropdown-submenu">
            <a href="#submenu2" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fs-5 bi-person"></i><span class="ms-1">Counselors</span>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="index.php?page=counselor-import">Import Data</a></li>
              <li><a class="dropdown-item" href="index.php?page=counselor-data">Edit</a></li>
              <li><a class="dropdown-item" href="index.php?page=counselor-schedule">Schedule</a></li>
              <li><a class="dropdown-item" href="index.php?page=counselor-emails">Email Schedule</a></li>
              <li><a class="dropdown-item" href="index.php?page=counselor-stats">Stats</a></li>
            </ul>
          </li>
          <!-- Reports Submenu -->
          <li class="dropdown-submenu">
            <a href="#submenu3" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fs-5 bi-book"></i><span class="ms-1">Reports</span>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="index.php?page=rpt-roomschedule">Room Schedule</a></li>
              <li><a class="dropdown-item" href="index.php?page=rpt-csvfile">Create CSV File</a></li>
              <li><a class="dropdown-item" href="index.php?page=rpt-stats">College Stats</a></li>
              <li><a class="dropdown-item" href="index.php?page=rpt-doubleknot">Black Pug Signup</a></li>
              <li><a class="dropdown-item" href="index.php?page=rpt-details">College Details</a></li>
            </ul>
          </li>
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
    } ?>
    <?php echo "Copyright &copy; " . date('Y') . " " . $_SERVER['HTTP_HOST']; ?>
  </div>
</div>
<!-- Additional CSS for nested dropdowns -->
<style>
  /* Base positioning for submenus (opens to the right) */
  .dropdown-submenu {
    position: relative;
  }

  /*  */
  .dropdown-submenu .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -1px;
    min-width: 220px;
    /* Wider to fully show longer menu items */
    z-index: 1050;
    /* Higher than typical Bootstrap elements */
  }

  /* Show submenu on hover (desktop) */
  .dropdown-submenu:hover>.dropdown-menu {
    display: block;
  }

  /*  */
  /* Optional: Slight shadow and border for visual separation */
  .dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(0, 0, 0, 0.1);
  }

  /*  */
  /* Ensure sidebar doesn't clip children */
  #sidebar {
    overflow: visible !important;
  }

  /*  */
  /* Ensure main content doesn't overlap */
  .main-content {
    overflow: visible !important;
  }
</style>
<!--  -->
<!-- JavaScript to handle nested dropdowns -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Show main Admin dropdown on hover (optional – improves UX)
    const adminDropdown = document.querySelector('.nav-item.dropdown > .dropdown-toggle');
    const adminMenu = adminDropdown?.nextElementSibling;

    if (adminDropdown && adminMenu) {
      adminDropdown.parentElement.addEventListener('mouseenter', () => {
        adminMenu.classList.add('show');
        adminDropdown.setAttribute('aria-expanded', 'true');
      });
      adminDropdown.parentElement.addEventListener('mouseleave', () => {
        adminMenu.classList.remove('show');
        adminDropdown.setAttribute('aria-expanded', 'false');
      });
    }

    // Handle nested submenus (click to toggle, prevent parent close)
    document.querySelectorAll('.dropdown-submenu .dropdown-toggle').forEach(function(element) {
      element.addEventListener('click', function(e) {
        e.stopPropagation(); // Prevent closing parent dropdown
        e.preventDefault();

        const submenu = this.nextElementSibling;
        if (submenu && submenu.classList.contains('dropdown-menu')) {
          submenu.classList.toggle('show');
        }
      });
    });

    // Optional: Close submenus when clicking elsewhere
    document.addEventListener('click', function() {
      document.querySelectorAll('.dropdown-submenu .dropdown-menu.show').forEach(function(openSubmenu) {
        openSubmenu.classList.remove('show');
      });
    });
  });
</script>