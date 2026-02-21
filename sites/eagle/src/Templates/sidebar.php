<?php
// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  die('An error occurred. Please try again later.');
}
$page = $page ?? 'home';
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
      <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "Admin"): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Admin</span>
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?page=viewuser">View Users</a></li>
          </ul>
        </li>
      <?php endif ?>

      <li class="nav-item">
        <a class="nav-link" href="?page=policy">Policies</a>
      </li>
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
  <p class="text-muted small" id="versionInfo">
    <em>Loading version...</em>
  </p>

  <div class="text-muted small">
  <script>
    // Customize these:
    const repo = 'rhall290472/centennial';
    const ref = 'main'; // ← your GitHub Pages branch (usually 'main' or 'gh-pages')

    const versionInfo = document.getElementById('versionInfo');

    fetch(`https://api.github.com/repos/${repo}/git/ref/heads/${ref}`)
      .then(r => {
        if (!r.ok) throw new Error('Failed to fetch branch ref');
        return r.json();
      })
      .then(data => {
        const sha = data.object.sha;
        const shortSha = sha.slice(0, 7);

        // Now check for tags that point exactly to this commit SHA
        return fetch(`https://api.github.com/repos/${repo}/tags?per_page=100`)
          .then(r => {
            if (!r.ok) return []; // fallback if tags fetch fails
            return r.json();
          })
          .then(tags => {
            // Find the first tag (GitHub returns tags in reverse-chronological order ≈ most recent first)
            const matchingTag = tags.find(tag => tag.commit.sha === sha);
            return {
              sha,
              shortSha,
              tag: matchingTag ? matchingTag.name : null
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
                      <strong>Version:</strong> 
                      <a href="${link}" target="_blank" class="text-decoration-none">
                        ${version}
                      </a>
                      <code class="text-muted">(${shortSha})</code>
                      | <strong>Last Updated:</strong> ${date}
                    `;
      })
      .catch(err => {
        console.error(err);
        versionInfo.innerHTML = '<em>Version info unavailable</em>';
      });
  </script>
  <?php echo "Copyright &copy; " . date('Y') . " " . $_SERVER['HTTP_HOST']; ?>
  </div>

  </div>
</div>