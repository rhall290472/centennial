      <div class="col-auto col-md-3 col-xl-auto px-sm-2 px-0 bg-dark  d-print-none">
        <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
          <p class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-5 d-none d-sm-inline">Menu</span>
          </p>
          <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
            <li class="nav-item"><a href="CounselorSelect.php" class="nav-link align-middle px-0">Counselors sign up</a></li>
            <li class="nav-item"><a href="ViewSchedule.php" class="nav-link align-middle px-0">View College Schedule</a></li>
            <li class="nav-item"><a href="ViewByBadges.php" class="nav-link align-middle px-0">View By Merit Badges</a></li>
            <li class="nav-item"><a href="ViewByCounselor.php" class="nav-link align-middle px-0">View By Merit Counselors</a><br><br /></li>

            <?php if (isset($_SESSION["Role"]) && $_SESSION["Role"] == "Admin") { ?>
              <p class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="fs-5 d-none d-sm-inline">Admin Menu</span>
              </p>
              <li class="nav-item">
                <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline">Scouts</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu1" data-bs-parent="#submenu1">
                  <li class="w-100">
                    <a href="./EnterScout.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Enter Data</span></a>
                  </li>
                  <li>
                    <a href="./ImportScout.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Import Data</span></a>
                  </li>
                  <li>
                    <a href="./ViewByScoutSchedule.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Schedule</span></a>
                  </li>
                  <li>
                    <a href="./EmailScouts.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Email Schedule</span></a>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline">Counselors</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#submenu2">
                  <li class="w-100">
                    <a href="./ImportCounselor.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Import Data</span></a>
                  </li>
                  <li>
                    <a href="./EditCounselor.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Edit</span></a>
                  </li>
                  <li>
                    <a href="./ViewByCounselorSchedule.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Schedule</span></a>
                  </li>
                  <li>
                    <a href="./EmailCounselors.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Email Schedule</span></a>
                  </li>
                  <li>
                    <a href="./CounselorsStats.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Stat's</span></a>
                  </li>

                </ul>
              </li>
              <li class="nav-item">
                <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline">Reports</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#submenu3">
                  <li class="w-100">
                    <a href="./ViewByRoom.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Room Schedule</span></a>
                  </li>
                  <li>
                    <a href="./CreateScoutbookCSV.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Create CSV File</span></a>
                  </li>
                  <li>
                    <a href="./ViewCollegeStats.php" class="nav-link px-0"> <span class="d-none d-sm-inline">College Stat's</span></a>
                  </li>
                  <li>
                    <a href="./DoubleKnot.php" class="nav-link px-0"> <span class="d-none d-sm-inline">Double Knot Signup</span></a>
                  </li>
                  <li>
                    <a href="./CollegeDetails.php" class="nav-link px-0"> <span class="d-none d-sm-inline">College Details</span></a>
                  </li>
                </ul>
              </li>

            <?php } ?>
          </ul>
        </div>
      </div>