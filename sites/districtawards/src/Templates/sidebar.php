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
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Scout Awards</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=jl-year&SubmitAward=29">Junior Leader of the Year</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Adult Pack Awards</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=cm-year&SubmitAward=4">Cubmaster of the Year</a></li>
        <li><a class="dropdown-item" href="?page=rcm-year&SubmitAward=5">Rookie Cubmaster of the Year</a></li>
        <li><a class="dropdown-item" href="?page=dl-year&SubmitAward=12">Den Leader of the Year</a></li>
        <li><a class="dropdown-item" href="?page=rdl-year&SubmitAward=13">Rookie Den Leader of the Year</a></li>
        <li><a class="dropdown-item" href="?page=pcm-year&SubmitAward=20">Pack Committee Member of the Year</a></li>
        <li><a class="dropdown-item" href="?page=rpcm-year&SubmitAward=22">Rookie Pack Committee Member of the Year</a></li>
        <li><a class="dropdown-item" href="?page=outleader&SubmitAward=14">Outstanding Leaders</a></li>
        <li><a class="dropdown-item" href="?page=keyscout&SubmitAward=15">Key Scouters</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Adult Troop Awards</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=sm-year&SubmitAward=2">Scoutmaster of the Year</a></li>
        <li><a class="dropdown-item" href="?page=rsm-year&SubmitAward=3">Rookie Scoutmaster of the Year</a></li>
        <li><a class="dropdown-item" href="?page=tcm-year&SubmitAward=8">Troop Committee Member of the Year</a></li>
        <li><a class="dropdown-item" href="?page=rtcm-year&SubmitAward=9">Rookie Troop Committee Member of the Year</a></li>
        <li><a class="dropdown-item" href="?page=outleader&SubmitAward=14">Outstanding Leaders</a></li>
        <li><a class="dropdown-item" href="?page=keyscout&SubmitAward=15">Key Scouters</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Adult Crews</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=ca-year&SubmitAward=6">Crew Advisor</a></li>
        <li><a class="dropdown-item" href="?page=rca-year&SubmitAward=7">Rookie Crew Advisor</a></li>
        <li><a class="dropdown-item" href="?page=skip-year&SubmitAward=48">Skipper</a></li>
        <li><a class="dropdown-item" href="?page=rskip-year&SubmitAward=49">Rookie Skipper</a></li>
        <li><a class="dropdown-item" href="?page=cscm-year&SubmitAward=50">Crew/Ship Committee Member</a></li>
        <li><a class="dropdown-item" href="?page=rcssm-year&SubmitAward=30">Rookie Crew/Ship Committee Member</a></li>
        <li><a class="dropdown-item" href="?page=outleader&SubmitAward=14">Outstanding Leaders</a></li>
        <li><a class="dropdown-item" href="?page=keyscout&SubmitAward=15">Key Scouters</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Adult District Awards</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=beaward&SubmitAward=16">Bald Eagle Award</a></li>
        <li><a class="dropdown-item" href="?page=dam&SubmitAward=1">District Award of Merit</a></li>
        <li><a class="dropdown-item" href="?page=dco-year&SubmitAward=18">District Commissioner of the Year</a></li>
        <li><a class="dropdown-item" href="?page=rdcm-year&SubmitAward=19">Rookie District Commissioner of the Year</a></li>
        <li><a class="dropdown-item" href="?page=dcm-year&SubmitAward=25">District Committee Member of the Year</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fs-4"></i><span class="ms-1 d-none d-sm-inline">Other Awards</span>
      </a>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?page=fofs&SubmitAward=17">Friends of Scouting</a></li>
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
          <i class="fs-4 bi-clipboard2-data"></i><span class="ms-1 d-none d-sm-inline">Reports</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=rpt-nom-hist-all">Nominees History</a></li>
          <li><a class="dropdown-item" href="?page=rpt-awardees">Awardees</a></li>
          <li><a class="dropdown-item" href="?page=rpt-denials">Denials</a></li>
          <li><a class="dropdown-item" href="?page=rpt-Avail-awards">Available Awards</a></li>
          <li><a class="dropdown-item" href="?page=rpt-nom-hist">Nominee History</a></li>
          <li><a class="dropdown-item" href="?page=rpt-award-hist">Award History</a></li>
          <li><a class="dropdown-item" href="?page=rpt-unit-his">Unit Award History</a></li>
          <li><a class="dropdown-item" href="?page=rpt-nom-id">Nominees with no Member ID</a></li>
          <li><a class="dropdown-item" href="?page=rpt-ballot">Create a Ballot</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-backpack4"></i><span class="ms-1 d-none d-sm-inline text-danger">Admin</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=edit-nominee">Edit Nominee</a></li>
        </ul>
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