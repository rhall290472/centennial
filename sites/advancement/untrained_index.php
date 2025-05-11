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

include_once('CAdvancement.php');
include_once('CUnit.php');
include_once('cAdultLeaders.php');

//$TrainedLeaders = TrainedLeaders::getInstance();
$cAdultLeaders = AdultLeaders::getInstance();
$CUnit = UNIT::getInstance();

// Reset the date, Needed ???
$_SESSION['year'] = date('Y');    // Reset back to current year.

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'head.php'; ?>
</head>

<body>
    <header id="header" class="header sticky-top">

        <!-- Responsive navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-lg-5">
                <a class="navbar-brand" href="#!">Centennial District Untrained Leaders</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
                        <li class="nav-item"><a class="nav-link" href="./contact.php">Contact</a></li>
                        <li class="nav-item"><a class="nav-link" href="./logon.php">Log on</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- Header-->
    <header class="py-5">
        <div class="container px-lg-5">
            <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
                <div class="m-4 m-lg-5">
                    <h1 class="display-5 fw-bold">Centennial District Untrained Leaders</h1>
                    <p class="fs-4">Here you will be able to review the untrained leaders for the District</p>
                    <!-- <a class="btn btn-primary btn-lg" href="#!">Call to action</a> -->
                </div>
            </div>
        </div>
    </header>
    <!-- Page Content-->
    <section class="pt-4">
        <div class="container px-lg-5">
            <!-- Page Features-->
            <div class="row gx-lg-5">
                <div class="col-lg-6 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">By Last Name</h2>
                            <form action="Untrained.php">
                                <p class="mb-0">Untrained leaders sorted by Last Name</p>
                                <select class="selectWrapper" id="MemberID" name="MemberID">
                                    <?php
                                    // First recod is blank "all"
                                    echo "<option value=\"\" </option>";
                                    $ResultName = $cAdultLeaders->GetUntrainedName();
                                    while ($rowName = $ResultName->fetch_assoc()) {
                                        echo "<option value=\"{$rowName['MemberID']}\">" . $rowName['Last_Name'] . " " . $rowName['First_Name'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type="submit" value="ByLastName" name="btn" />
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">By Position</h2>
                            <p class="mb-0">Untrained leaders sorted by Position</p>
                            <form action="Untrained.php">
                                <select class="selectWrapper" id="position_name" name="position_name">
                                    <?php
                                    // First recod is blank "all"
                                    echo "<option value=\"\" </option>";
                                    $ResultPosition = $cAdultLeaders->GetUntrainedPosition();
                                    while ($rowPosition = $ResultPosition->fetch_assoc()) {
                                        echo "<option value=\"{$rowPosition['Position']}\">";
                                        echo $rowPosition['Position'];
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type="submit" value="ByPosition" name="btn" />
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">By Unit</h2>
                            <p class="mb-0">Untrained leaders sorted by unit</p>
                            <form action="Untrained.php">
                                <select class="selectWrapper" id="Unit_Name" name="Unit">
                                    <?php
                                    // First recod is blank "all"
                                    echo "<option value=\"\" </option>";
                                    $ResultUnit = $cAdultLeaders->GetUntrainedUnit();
                                    while ($rowUnit = $ResultUnit->fetch_assoc()) {
                                        echo "<option value=\"{$rowUnit['Unit']}\">";
                                        echo $rowUnit['Unit'];
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type="submit" value="ByUnit" name="btn" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer-->
     <?php include 'Footer.php'; ?>
</body>

</html>