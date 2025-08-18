<!-- Sidebar -->
<div class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-light" id="sidebar">
  <a href="?page=home" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
    <span class="fs-4">Menu</span>
  </a>
  <hr>
  <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
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
          <li><a class="dropdown-item" href="?page=untrainedcounselors&AdminFunction=ByUntrained">Untrained Counselors</a></li>
          <li><a class="dropdown-item" href="?page=byexpireypt&AdminFunction=ByExpireypt">Expired YPT</a></li>
          <li><a class="dropdown-item" href="?page=byinactive&AdminFunction=ByInactive">Inactive Counselors</a></li>
          <li><a class="dropdown-item" href="?page=counsloresbadge&AdminFunction=ByCounselorsperBadge">Counselors/Badge</a></li>
          <li><a class="dropdown-item" href="?page=ReportMB15&AdminFunction=ByMB15">MB > 15</a></li>
          <li><a class="dropdown-item" href="?page=counselornoid&AdminFunction=ByNoID">Cousnelors No MID</a></li>
          <li><a class="dropdown-item" href="?page=counselornoemail&AdminFunction=ByNoEmail">Cousnelors No Email</a></li>
          <li><a class="dropdown-item" href="?page=counselor0badges&AdminFunction=MBCnoMB">Counselors 0 Badges</a></li>
          <li><a class="dropdown-item" href="?page=specialtraining&AdminFunction=ByExpiredSpecialTraining">Special Training</a></li>
          <li><a class="dropdown-item" href="?page=counselornobadge&AdminFunction=ByMBwithnoCounselor">No Counselors/Badge</a></li>
          <li><a class="dropdown-item" href="?page=counselornounit&AdminFunction=ByCounselorwithNoUnit">No Unit</a></li>
        </ul>
      </li>
    <?php endif; ?>
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
      <li class="nav-item">
        <a class="nav-link" href="?page=logout">Logout</a>
      </li>
    </ul>
  <?php else: ?>
    <ul>
    <li class="nav-item">
      <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a>
    </li>
    </ul>
  <?php endif; ?>


  <button class="btn btn-outline-secondary mt-3 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
    Close Sidebar
  </button>
</div>