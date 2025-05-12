<?php
defined('IN_APP') or die('Direct access not allowed.');

?>
<aside class="col-auto col-md-3 col-xl-auto px-sm-2 px-0 bg-dark  d-print-none">
  <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
    <p class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-5 d-none d-sm-inline">Menu</span>
    </p>
    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
      <!-- Dropdown with Submenu -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-person text-white-50"></i><span class="ms-1 d-none d-sm-inline text-white-50">Adults</span>
        </a>
        <ul class="dropdown-menu">
          <li><a href="../src/Pages/Untrained.php?MemberID=&btn=ByLastName" class=" dropdown-item">Untrained Leaders</a></li>
          <li><a href="../src/Pages/YPT.php?MemberID=&btn=ByLastName" class=" dropdown-item">Expired YPT</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-emoji-surprise text-white-50"></i><span class="ms-1 d-none d-sm-inline text-white-50">Packs</span>
          <!-- <img src="./images/cub-scout.ico" alt="Cub Scouts" class="cubscout-icon text-white-50"><span class="ms-1 d-none d-sm-inline text-white-50">Packs</span> -->
        </a>
        <ul class="dropdown-menu">
          <!-- <li><a href="./pack_index.php" class="dropdown-item text-danger">Index</a></li> -->
          <li><a href="../src/Pages/pack_summary.php" class="dropdown-item">Summary</a></li>
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/pack_below_goal.php'; ?>" class="dropdown-item">Below District Goal's</a></li>
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/pack_meeting_goal.php'; ?>" class="dropdown-item">Meeting District Goal's</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-backpack4 text-white-50"></i><span class="ms-1 d-none d-sm-inline text-white-50">Troops</span>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/troop_summary.php'; ?>" class="dropdown-item">Summary</a></li>
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/troop_below_goal.php'; ?>" class="dropdown-item">Below District Goal's</a></li>
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/troop_meeting_goal.php'; ?>" class="dropdown-item">Meeting District Goal's</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-shield text-white-50"></i><span class="ms-1 d-none d-sm-inline text-white-50">Crews</span>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/crew_summary.php'; ?>" class="dropdown-item">Summary</a></li>
        </ul>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" role="button"
          data-bs-toggle="dropdown" aria-expanded="false">
          <i class="fs-4 bi-clipboard2-data text-white-50"></i><span class="ms-1 d-none d-sm-inline text-white-50">Reports</span>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/adv_report.php'; ?>" class="dropdown-item">District Advancement Report</a></li>
          <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/membership_report.php'; ?>" class="dropdown-item">Membership</a></li>
        </ul>
      </li>

      <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) { ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fs-4 bi-book text-danger"></i><span class="ms-1 d-none d-sm-inline text-danger">Admin</span>
          </a>
          <ul class="dropdown-menu">
            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"
                onclick="event.stopPropagation();">
                Upload Adult Data
              </a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=TrainedLeader'; ?>" class=" dropdown-item">Training</a></li>
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=Updateypt'; ?>" class=" dropdown-item">YPT</a></li>
              </ul>
            </li>

            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"
                onclick="event.stopPropagation();">
                Upload Membership Data
              </a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=UpdateTotals'; ?>" class=" dropdown-item">Upload COR Data</a></li>
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=FunctionalRole'; ?>" class=" dropdown-item">Functional Roles</a></li>
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=UpdateCommissioners'; ?>" class=" dropdown-item">Assigned Commissioners</a></li>
              </ul>
            </li>

            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"
                onclick="event.stopPropagation();">
                Upload Pack Data
              </a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=UpdatePack'; ?>" class=" dropdown-item">Advancements</a></li>
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=UpdateAdventure'; ?>" class=" dropdown-item">Awards</a></li>
              </ul>
            </li>

            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"
                onclick="event.stopPropagation();">
                Upload Troop Data
              </a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=UpdateTroop'; ?>" class=" dropdown-item">Advancements</a></li>
              </ul>
            </li>

            <li class="submenu submenu-md dropend">
              <a class="dropdown-item dropdown-toggle" role="button"
                data-bs-toggle="dropdown" aria-expanded="false"
                onclick="event.stopPropagation();">
                Upload Crew Data
              </a>
              <ul class="dropdown-menu">
                <li><a href="#!" class=" dropdown-item">Advancements</a></li>
                <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/UpdateData.php?Update=UpdateVenturing'; ?>" class=" dropdown-item">Venturing</a></li>
              </ul>
            </li>
            <li><a href="<?php echo SITE_URL . '/centennial/sites/advancement/src/Pages/ErrorLog.php'; ?>" class=" dropdown-item">View Error Log</a></li>
          </ul>
        </li>
      <?php } ?>
    </ul>
  </div>
</aside>

<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- Bootstrap JS and Popper.js -->
<!-- jQuery (required for Bootstrap's JavaScript plugins) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (required for Bootstrap dropdowns) -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script> -->

<!-- Bootstrap's JavaScript -->
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> -->