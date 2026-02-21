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

  <!-- Your version footer – cleaned up & using relative units -->
  <p class="text-muted small" id="versionInfo">
    <em>Loading version...</em>
  </p>

  <!-- Your script (with fixes – see notes below) -->
  <script>
    const repo = 'rhall290472/centennial';
    const ref = 'main';
    const versionInfo = document.getElementById('versionInfo');

    fetch(`https://api.github.com/repos/${repo}/git/ref/heads/${ref}`)
      .then(r => {
        if (!r.ok) throw new Error('Failed to fetch ref');
        return r.json();
      })
      .then(data => {
        const sha = data.object.sha;
        const shortSha = sha.slice(0, 7);

        return fetch(`https://api.github.com/repos/${repo}/tags?per_page=100`)
          .then(r => r.ok ? r.json() : [])
          .then(tags => {
            const matchingTag = tags.find(t => t.commit.sha === sha);
            return {
              sha,
              shortSha,
              tag: matchingTag?.name ?? null
            };
          });
      })
      .then(({
        sha,
        shortSha,
        tag
      }) => {
        const version = tag || shortSha;
        const link = `https://github.com/${repo}/commit/${sha}`;
        const date = new Date().toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });

        versionInfo.innerHTML = `
        <em>
          <strong>Version:</strong>
          <a href="${link}" target="_blank" class="text-decoration-none">${version}</a>
          <code class="text-muted">(${shortSha})</code>
          | <strong>Last Updated:</strong> ${date}
        </em>`;
      })
      .catch(err => {
        console.error(err);
        versionInfo.innerHTML = '<em>Version info unavailable</em>';
      });
  </script>

  <?php
  echo '<em class="text-muted">Copyright &copy; ' . date('Y') . ' ' . htmlspecialchars($_SERVER['HTTP_HOST']) . '</em>';
  ?>
    
  </div>
</div>