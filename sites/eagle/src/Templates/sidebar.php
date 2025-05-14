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
          <i class="fs-4 bi-person"></i><span class="ms-1 d-none d-sm-inline">Life Scouts</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=edit-scout">Edit/Update Scout</a></li>
          <li><a class="dropdown-item" href="?page=active-life">All Active Life Scouts</a></li>
          <li><a class="dropdown-item" href="?page=audit-scout">Audit Scout</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-emoji-surprise"></i><span class="ms-1 d-none d-sm-inline">Eagles</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=pack-summary">Eagle Scouts By Unit</a></li>
          <li><a class="dropdown-item" href="?page=pack-below-goal">Eagle Scouts By Year</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-backpack4"></i><span class="ms-1 d-none d-sm-inline">Coaches</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=troop-summary">Edit/Update Coach</a></li>
          <li><a class="dropdown-item" href="?page=troop-below-goal">Active Coaches</a></li>
          <li><a class="dropdown-item" href="?page=troop-meeting-goal">Inactive Coaches</a></li>
          <li><a class="dropdown-item" href="?page=troop-meeting-goal">YPT Report</a></li>
          <li><a class="dropdown-item" href="?page=troop-meeting-goal">Workload Report</a></li>
          <li><a class="dropdown-item" href="?page=troop-meeting-goal">Workload History</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-shield"></i><span class="ms-1 d-none d-sm-inline">Reports</span>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="?page=crew-summary">All Scouts</a></li>
          <li><a class="dropdown-item" href="?page=crew-summary">Age Out</a></li>
          <li><a class="dropdown-item" href="?page=crew-summary">Aged Out</a></li>
          <li><a class="dropdown-item" href="?page=crew-summary">Did Not Attend Preview</a></li>
          <li><a class="dropdown-item" href="?page=crew-summary">Lacking Proposal Approval</a></li>
          <li><a class="dropdown-item" href="?page=crew-summary">Approved Proposal</a></li>
          <li><a class="dropdown-item" href="?page=crew-summary">Pending EBOR</a></li>
        </ul>
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
</div>