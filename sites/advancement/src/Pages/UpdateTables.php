<?php
  // Secure session start
  if (session_status() === PHP_SESSION_NONE) {
    session_start([
      'cookie_httponly' => true,
      'use_strict_mode' => true,
      'cookie_secure' => isset($_SERVER['HTTPS'])
    ]);
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
!  #   Copyright 2017-2024 - Richard Hall                                   #  !
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


  require_once 'config/conn_inc.php';
if (!session_id()) {
  session_start();

  if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
    $CAdvancement->GotoURL("index.php");
    exit;
  }
}

load_class(SHARED_PATH.'/src/Classes/CAdvancement.php');
$CAdvancement = CAdvancement::getInstance();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'head.php'; ?>
</head>

<body>
  <!-- Responsive navbar-->
  <?php load_template('/src/Templates/navbar.php'); ?>
  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <h1 class="display-5 fw-bold">Centennial District Update Advancement Data</h1>
          <p class="fs-4">Here you be able to import advancement data</p>
          <p>You need to update the membership totals first, this function will also update the youth in
            unit tables. Then when you update the advancment data it will calcaute a new rank/scout value.
          </p>
          <?php
          // This will set what year the data is entered for
          if (isset($_POST['SubmitYear'])) {
            $SelectedYear = $_POST['Year'];
            //echo $SelectedYear;
            $_SESSION['year'] = $SelectedYear;
            //echo $_SESSION['year'];
            header('Refresh: ' . 1);
          }

          $CAdvancement->SelectYear();
          ?>


        </div>
      </div>
    </div>
  </header>
  <form action="UpdateData.php" method="get" enctype="multipart/form-data">
    <!-- Page Content-->
    <section class="pt-4">
      <div class="container px-lg-5">
        <!-- Page Features-->
        <div class="row gx-lg-5">
          <!-- <div class="col-lg-9 col-xxl-4 mb-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                                <h2 class="fs-4 fw-bold">Import Membership</h2>
                                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="UpdateTotals" name="Update">
                            </div>
                        </div>
                    </div> -->
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Trained Leader</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="TrainedLeader" name="Update">
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import YPT</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="Updateypt" name="Update"><br />
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Pack Advancement</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="UpdatePack" name="Update">

              </div>
            </div>
          </div>
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Troop Advancement</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="UpdateTroop" name="Update">
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Crew Advancement</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="UpdateCrew" name="Update"><br />
              </div>
            </div>
          </div>

          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Adventures</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="UpdateAdventure" name="Update">
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Venturing</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="UpdateVenturing" name="Update">
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Commissioners</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="UpdateCommissioners" name="Update"><br />

              </div>
            </div>
          </div>
          <div class="col-lg-9 col-xxl-4 mb-5">
            <div class="card bg-light border-0 h-100">
              <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-upload"></i></div>
                <h2 class="fs-4 fw-bold">Import Functional Roles</h2>
                <input class="btn btn-primary btn-sm" style="margin-bottom:10px;" type="submit" value="FunctionalRole" name="Update">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </form>
  <!-- Footer-->
  <?php include 'Footer.php' ?>

</body>

</html>