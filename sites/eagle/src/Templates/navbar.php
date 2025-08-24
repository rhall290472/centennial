<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="?page=home"><?php echo PAGE_TITLE; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>" href="?page=home">Home</a>
        </li>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): { ?>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Life Scouts
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=edit-scout">Edit/Update Scout</a></li>
                <li><a class="dropdown-item" href="?page=active-life">All Active Life Scouts</a></li>
                <li><a class="dropdown-item" href="?page=audit-scout">Audit Scout</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Eagles
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?page=eagle-unit">Eagle Scouts By Unit</a></li>
                <li><a class="dropdown-item" href="?page=eagle-year">Eagle Scouts By Year</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Coaches
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
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Reports
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
          <?php }
        else: { ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a>
            </li>
        <?php }
        endif; ?>
      </ul>
    </div>
  </div>
</nav>