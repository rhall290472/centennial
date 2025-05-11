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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include("header.php"); ?>
  <meta name="description" content="index.php">

</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container px-lg-4">
      <a class="navbar-brand" href="#!">Centennial District Awards</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="#!">Home</a></li>
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
  <header>
    <?php
    if (!(isset($_SESSION["loggedin"]) && $_SESSION["role"] == "Admin") && true) {
    //if(true){
    ?>
      <div style="display: flex; justify-content: center;">
        <img src="./img/Closed.png" alt="Registration Closed" class="center" height="270" width="270" />
      </div>
    <?php
    } else {
    ?>
      <div class="container-fluid">
        <div class="row flex-nowrap">
          <!-- Include the common side nav bar -->
          <?php if (isset($_SESSION["loggedin"]) && $_SESSION["role"] == "Admin") { 
            include 'navbar.php'; 
          }?>
          <div class="col py-3">

            <div class="container px-sm-5">
              <div class="p-4 p-sm-5 bg-light rounded-3 text-center">
                <div class="m-4 m-sm-5">
                  <h1 class="display-5 fw-bold">Centennial District Awards</h1>
                  <p class="fs-4">Here you will be able to submit a Nomination for District Awards</p>
                  <img src="./img/DistrictAwards.png" alt="District Awards" class="center"">
        </div>
        <div class=" py-1">
                  <a class=" btn btn-primary btn-lg" href="./OnLineNomination.php">Submit a on-line Nomination</a>
                  <a class="btn btn-primary btn-lg" href="./DocsPage.php">Download Nomination Form</a>
                </div>
              </div>
            </div>
  </header>
<?php } ?>
<!-- Page Content-->
<?php
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
?>
  <section class="pt-4">
    <div class="container px-lg-5">
      <!-- Page Features-->
      <div class="row gx-lg-5">

        <div class="col-lg-5 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
              <h2 class="fs-4 fw-bold">Reports</h2>
              <p class="mb-0">Reports available for the District Award database</p>
              <a class="btn btn-primary btn-sm" href="./Reports.php">Reports</a>
            </div>
          </div>
        </div>
        <?php if (isset($_SESSION["loggedin"]) && $_SESSION["role"] == "Admin") { ?>
          <div class="col-lg-5 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-exclamation-triangle"></i></div>
                <h2 class="fs-4 fw-bold">Show Error Log</h2>
                <p class="mb-0">View the php_errors.log file on the server</p>
                <a class="btn btn-primary btn-sm" href="./ErrorLog.php">View</a>
              </div>
            </div>
          </div>
        <?php } ?>

        <div class="col-lg-5 col-xxl-4 mb-5">
          <div class="card bg-light border-0 h-100">
            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
              <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-clipboard-data"></i></div>
              <h2 class="fs-4 fw-bold">Edit Nominee</h2>
              <p class="mb-0">Edit a nomination</p>
              <a class="btn btn-primary btn-sm" href="./NomineePage.php">Edit</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php
}
?>


<?php include("Footer.php"); ?>
</body>

</html>