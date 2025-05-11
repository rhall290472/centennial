<?php
if (!session_id()) {
  session_start();
}
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #   Copyright 2024 - Richard Hall                                        #  !
!  #                                                                        #  !
!  #   The information contained herein is the property of Richard          #  !
!  #   Hall, and shall not be copied, in whole or in part, or               #  !
!  #   disclosed to others in any manner without the express written        #  !
!  #   authorization of Richard Hall.                                       #  !
!  #                                                                        #  !
!  #                                                                        #  !
! /##########################################################################\ !
!//                                                                          \\!
!/                                                                            \!
!==============================================================================!
*/

require_once 'CDistrictAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  $cDistrictAwards->GotoURL("index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
  <meta name="description" content="Reports.php">
  </head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-5">
      <a class="navbar-brand" href="#!">Centennial District Awards</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
          <?php
          if (isset($_SESSION["loggedin"]) && $_SESSION["role"] == "Admin")
            echo '<li class="nav-item"><a class="nav-link" href="./ViewUsers.php">Users</a></li>';
          if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
            echo '<li class="nav-item"><a class="nav-link" href="./logoff.php">Log off</a></li>';
          } else {
            echo '<li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <h1 class="display-5 fw-bold">Centennial District Awards</h1>
          <p class="fs-4">Reports avaiable for the District Award database</p>
        </div>
      </div>
    </div>
  </header>


  <section class="pt-4">
    <div class="container px-lg-5">
      <!-- Page Features-->
      <div class="row gx-lg-5">
        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">Awards</h2>
              <p class="mb-0">Nominees, regardless of Status, by year</p>
              <a class="btn btn-primary btn-sm" href="./ReportAwardYear.php">Reports</a>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">Awards</h2>
              <p class="mb-0">Awards Given, by year</p>
              <a class="btn btn-primary btn-sm" href="./ReportAwardedYear.php">Reports</a>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">Awards</h2>
              <p class="mb-0">Denials, by year</p>
              <a class="btn btn-primary btn-sm" href="./ReportDeniedYear.php">Reports</a>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">Awards</h2>
              <p class="mb-0">Available Awards</p>
              <a class="btn btn-primary btn-sm" href="./ReportAvailableAwards.php">Reports</a>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">History</h2>
              <p class="mb-0">Nominee History, by Nominee</p>
              <a class="btn btn-primary btn-sm" href="./ReportNomineeHistory.php">Reports</a>
            </div>
          </div>
        </div>


        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">History</h2>
              <p class="mb-0">Award History, by Award</p>
              <a class="btn btn-primary btn-sm" href="./ReportAwardHistory.php">Reports</a>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">History</h2>
              <p class="mb-0">Unit Award History, by Unit</p>
              <a class="btn btn-primary btn-sm" href="./ReportUnitHistory.php">Reports</a>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">Validations</h2>
              <p class="mb-0">Nominees with no Member ID, by year</p>
              <a class="btn btn-primary btn-sm" href="./ReportMemberID.php">Reports</a>
            </div>
          </div>
        </div>

        <div class="col-lg-9 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">Ballot</h2>
              <p class="mb-0">Create a Ballot for Award seclection</p>
              <a class="btn btn-primary btn-sm" href="./ReportBallot.php">Reports</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>


  <?php include("Footer.php"); ?>
</body>

</html>