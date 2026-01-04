<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="?page=home"><?php echo PAGE_TITLE; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo ($page ?? '') === 'home' ? 'active' : ''; ?>" href="?page=home">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo in_array($page, [
                                                  'counselorsperbadge',
                                                  'allcounselorsperbadge',
                                                  'bycounselor',
                                                  'bytroop',
                                                  'counselorofmb',
                                                  'forselectedtroop',
                                                  'byselectedcounselor',
                                                  'byfullselectedtroop'
                                                ]) ? 'active' : ''; ?>"
              href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Reports
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
            <a class="nav-link dropdown-toggle <?php echo in_array($page, ['uploadcounselors']) ? 'active' : ''; ?>"
              href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Uploads
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="?page=uploadcounselors">Counselor List</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo in_array($page, [
                                                  'untrainedcounselors',
                                                  'byexpireypt',
                                                  'byinactive',
                                                  'counsloresbadge',
                                                  'reportmb15',
                                                  'counselornoid',
                                                  'counselornoemail',
                                                  'counselor0badges',
                                                  'specialtraining',
                                                  'counselornobadge',
                                                  'counselornounit']) ? 'active' : ''; ?>"
              href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Functions
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
            <a class="nav-link" href="?page=logout">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>" href="?page=login">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>