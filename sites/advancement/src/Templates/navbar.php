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

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo in_array($page, ['untrained', 'ypt', 'unitview']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Adults
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?page=untrained">Untrained Leaders</a></li>
            <li><a class="dropdown-item" href="?page=ypt">Expired YPT</a></li>
          </ul>
        </li>


        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo in_array($page, ['pack-summary', 'pack-below-goal', 'pack-meeting-goal', 'unitview']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Packs
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?page=pack-summary">Summary</a></li>
            <li><a class="dropdown-item" href="?page=pack-below-goal">Below District Goal</a></li>
            <li><a class="dropdown-item" href="?page=pack-meeting-goal">Meeting District Goal</a></li>
            <li><a class="dropdown-item" href="?page=unitview">Unit View</a></li>
          </ul>
        </li>


        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo in_array($page, ['troop-summary', 'troop-below-goal', 'troop-meeting-goal', 'unitview']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Troops
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?page=troop-summary">Summary</a></li>
            <li><a class="dropdown-item" href="?page=troop-below-goal">Below District Goal</a></li>
            <li><a class="dropdown-item" href="?page=troop-meeting-goal">Meeting District Goal</a></li>
            <li><a class="dropdown-item" href="?page=unitview">Unit View</a></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo in_array($page, ['crew-summary', 'unitview']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Crews
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?page=crew-summary">Summary</a></li>
            <li><a class="dropdown-item" href="?page=unitview">Unit View</a></li>
          </ul>
        </li>


        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo in_array($page, ['adv-report', 'membership-report']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Reports
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="?page=adv-report">Advancement Report</a></li>
            <li><a class="dropdown-item" href="?page=membership-report">Membership Report</a></li>
          </ul>
        </li>


        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="text-danger">Admin</span>
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
              <li><a class="dropdown-item" href="?page=updatedata&update=UpdateCrew">Crew Advancements</a></li>
              <li><a class="dropdown-item" href="?page=updatedata&update=UpdateVenturing">Venturing</a></li>
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