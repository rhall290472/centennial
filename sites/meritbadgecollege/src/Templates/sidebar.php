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
          <!-- Scouts Submenu -->
          <li class="dropdown-submenu">
            <a href="#submenu1" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fs-5 bi-book"></i><span class="ms-1">Scouts</span>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="./EnterScout.php">Enter Data</a></li>
              <li><a class="dropdown-item" href="./ImportScout.php">Import Data</a></li>
              <li><a class="dropdown-item" href="./ViewByScoutSchedule.php">Schedule</a></li>
              <li><a class="dropdown-item" href="./EmailScouts.php">Email Schedule</a></li>
            </ul>
          </li>
          <!-- Counselors Submenu -->
          <li class="dropdown-submenu">
            <a href="#submenu2" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fs-5 bi-person"></i><span class="ms-1">Counselors</span>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="./ImportCounselor.php">Import Data</a></li>
              <li><a class="dropdown-item" href="./EditCounselor.php">Edit</a></li>
              <li><a class="dropdown-item" href="./ViewByCounselorSchedule.php">Schedule</a></li>
              <li><a class="dropdown-item" href="./EmailCounselors.php">Email Schedule</a></li>
              <li><a class="dropdown-item" href="./CounselorsStats.php">Stats</a></li>
            </ul>
          </li>
          <!-- Reports Submenu -->
          <li class="dropdown-submenu">
            <a href="#submenu3" class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fs-5 bi-book"></i><span class="ms-1">Reports</span>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="./ViewByRoom.php">Room Schedule</a></li>
              <li><a class="dropdown-item" href="./CreateScoutbookCSV.php">Create CSV File</a></li>
              <li><a class="dropdown-item" href="./ViewCollegeStats.php">College Stats</a></li>
              <li><a class="dropdown-item" href="./DoubleKnot.php">Double Knot Signup</a></li>
              <li><a class="dropdown-item" href="./CollegeDetails.php">College Details</a></li>
            </ul>
          </li>
        </ul>
      </li>
    <?php endif; ?>
  </ul>
  <button class="btn btn-outline-secondary mt-3 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-expanded="false" aria-controls="sidebar">
    Close Sidebar
  </button>
</div>

<!-- Additional CSS for nested dropdowns -->
<style>
  .dropdown-submenu {
    position: relative;
  }

  .dropdown-submenu .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -1px;
  }

  .dropdown-submenu:hover>.dropdown-menu {
    display: block;
  }
</style>

<!-- JavaScript to handle nested dropdowns -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-submenu .dropdown-toggle').forEach(function(element) {
      element.addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        let submenu = this.nextElementSibling;
        if (submenu.classList.contains('dropdown-menu')) {
          submenu.classList.toggle('show');
        }
      });
    });
  });
</script>