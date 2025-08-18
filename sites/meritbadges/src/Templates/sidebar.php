<!-- Sidebar -->
<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-light" id="sidebar">
  <a href="?page=home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
    <span class="fs-4">Menu</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto">
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
        <li><a class="dropdown-item" href="?page=troop-summary">Untrained Counselors</a></li>
        <li><a class="dropdown-item" href="?page=troop-below-goal">Expired YPT</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">Inactive Counselors</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">Counselors/Badge</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">MB > 15</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">Cousnelors No MID</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">Cousnelors No Email</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">Counselors 0 Badges</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">Special Training</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">No Counselors/Badge</a></li>
        <li><a class="dropdown-item" href="?page=troop-meeting-goal">No Unit</a></li>
      </ul>
    </li>
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
      <li class="nav-item">
        <a class="nav-link" href="?page=logout">Logout</a>
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
          <li><a class="dropdown-item" href="?page=updatedata&update=TrainedLeader">Training</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=Updateypt">YPT</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateTotals">Upload COR Data</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateFunctionalRole">Functional Roles</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateCommissioners">Pack Assigned Commissioners</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdatePack">Pack Advancements</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateAdventure">Pack Awards</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateTroop">Troop Advancements</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateCrew">Crew Advancements</a></li>
          <li><a class="dropdown-item" href="?page=updatedata&update=UpdateVenturing">Venturing</a></li>
        </ul>
      </li>
    <?php endif; ?>
  </ul>
  <button class="btn btn-outline-secondary mt-3 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
    Close Sidebar
  </button>
</div>