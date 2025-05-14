<?php
if (!session_id()) {
  session_start();
}
require_once 'CEagle.php';
$cEagle = CEagle::getInstance();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include('head.php'); ?>
</head>

<body>
  <?php
  include_once('header.php');
  $cEagle->GetYear(); //Set a year value to current year
  ?>

  <!-- If user is not logged in, then they can see nonething and do nonething. -->
  <?php
  if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    //include('navmenu.php');
  ?>

    <div class="container-fluid">
      <div class="row flex-nowrap">
        <div class="col-auto px-sm-2 px-0 bg-dark">
          <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
            <a href="#" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
              <span class="fs-5 d-none d-sm-inline">Menu</span>
            </a>
            <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
              <li>
                <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-heart-fill"></i> <span class="ms-1 d-none d-sm-inline text-white">Life Scouts</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
                  <li class="w-100">
                    <a href="./ScoutPage.php" class="nav-link px-0 text-white"> <span class="d-none d-sm-inline">Edit/Update Scout</span></a>
                  </li>
                  <li>
                    <a href="./ReportAllLifeScouts.php" class="nav-link px-0 text-white"> <span class="d-none d-sm-inline">All Active Life Scouts</span></a>
                  </li>
                  <li>
                    <a href="./ReportAuditScout.php" class="nav-link px-0 text-white"> <span class="d-none d-sm-inline">Audit Scout</span></a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-twitter"></i> <span class="ms-1 d-none d-sm-inline text-white">Eagles</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
                  <li class="w-100">
                    <a href="./ReportEagles.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Eagle Scouts By Unit</span></a>
                  </li>
                  <li>
                    <a href="./ReportEagleYear.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Eagle Scouts By Year</span></a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-person"></i> <span class="ms-1 d-none d-sm-inline text-white">Coaches</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
                  <li class="w-100">
                    <a href="./CoachPage.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Edit/Update Coach</span></a>
                  </li>
                  <li>
                    <a href="./ReportCoachesActive.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Active Coaches</span></a>
                  </li>
                  <li>
                    <a href="./ReportCoachesInactive.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Inactive Coaches</span></a>
                  </li>
                  <li>
                    <a href="./ReportCoachesActiveYPT.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">YPT Report</span></a>
                  </li>
                  <li>
                    <a href="./ReportCoachesLoad.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Workload Report</span></a>
                  </li>
                  <li>
                    <a href="./ReportCoachesHistory.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Workload History Report</span></a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="#submenu4" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-clipboard-data"></i> <span class="ms-1 d-none d-sm-inline text-white">Reports</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu4" data-bs-parent="#menu">
                  <li class="w-100">
                    <a href="./ReportAllScouts.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">All Scouts</span></a>
                  </li>
                  <li>
                    <a href="./ReportAgeOut.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Age Out</span></a>
                  </li>
                  <li>
                    <a href="./ReportAgedOut.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Aged Out</span></a>
                  </li>
                  <li>
                    <a href="./ReportPreview.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Did Not Attended Preview</span></a>
                  </li>
                  <li>
                    <a href="./ReportProject.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Lacking Proposal Approval</span></a>
                  </li>
                  <li>
                    <a href="./ReportApprovedProject.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Approved Proposal</span></a>
                  </li>
                  <li>
                    <a href="./ReportPendingEBOR.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Pending EBOR's</span></a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="#submenu5" data-bs-toggle="collapse" class="nav-link px-0 align-middle ">
                  <i class="fs-4 bi-bookmark-check"></i> <span class="ms-1 d-none d-sm-inline text-white">Admin</span></a>
                <ul class="collapse nav flex-column ms-1" id="submenu5" data-bs-parent="#menu">
                  <li class="w-100">
                    <a href="./DocsPage.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">Policies</span></a>
                  </li>
                  <li>
                    <a href="./ErrorLog.php" class="nav-link px-0"> <span class="d-none d-sm-inline text-white">View Error Log</span></a>
                  </li>
                </ul>
              </li>
            </ul>
            </li>
            </ul>
            <hr>
          </div>
        </div>
      </div>
    </div>



    <div class="container object-fit-scale">
      <div class="row">
      </div>
    </div>


  <?php } else { ?>

      <div class="row">
        <div class="col">
          <img class="EagleScoutimage" src="./img/EagleScout_insignia.jpg" alt="Eagle Rank" />
        </div>
      </div>
  <?php } ?>
  <?php include('Footer.php'); ?>

</body>

</html>