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
require_once 'CAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();
$cAwards = cAwards::getInstance();

$queryPackAward = "SELECT DISTINCTROW Award, AwardIDX, UnitLevel FROM awards WHERE `OnLine`='1' AND (`AwardType`='P' OR `AwardType`='A') ORDER BY UnitLevel, Award";
$result_ByPack = $cDistrictAwards->doQuery($queryPackAward);
if (!$result_ByPack) {
    $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByPack)");
}

$queryTroopAward = "SELECT DISTINCTROW Award, AwardIDX, UnitLevel FROM awards WHERE `OnLine`='1'  AND (`AwardType`='T' OR `AwardType`='A') ORDER BY UnitLevel, Award";
$result_ByTroop = $cDistrictAwards->doQuery($queryTroopAward);
if (!$result_ByTroop) {
    $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByTroop)");
}

$queryCrewAward = "SELECT DISTINCTROW Award, AwardIDX, UnitLevel FROM awards WHERE `OnLine`='1' AND (`AwardType`='C' OR `AwardType`='A') ORDER BY UnitLevel, Award";
$result_ByCrew = $cDistrictAwards->doQuery($queryCrewAward);
if (!$result_ByCrew) {
    $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByCrew)");
}

$queryDistrictAward = "SELECT DISTINCTROW Award, AwardIDX, UnitLevel FROM awards WHERE `OnLine`='1' AND `AwardType`='D' ORDER BY UnitLevel, Award";
$result_ByDistrict = $cDistrictAwards->doQuery($queryDistrictAward);
if (!$result_ByDistrict) {
    $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByDistrict)");
}

$queryYouthAward = "SELECT DISTINCTROW Award, AwardIDX, UnitLevel FROM awards WHERE `OnLine`='1' AND `AwardType`='Y' ORDER BY UnitLevel, Award";
$result_ByYouth = $cDistrictAwards->doQuery($queryYouthAward);
if (!$result_ByYouth) {
    $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByYouth)");
}

$queryOtherAward = "SELECT DISTINCTROW Award, AwardIDX, UnitLevel FROM awards WHERE `OnLine`='1' AND `AwardType`='O' ORDER BY UnitLevel, Award";
$result_ByOther = $cDistrictAwards->doQuery($queryOtherAward);
if (!$result_ByOther) {
    $cDistrictAwards->function_alert("ERROR: $cDistrictAwards->doQuery($result_ByOther)");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("header.php"); ?>
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
                    <p class="fs-4">Below select the type of award you would like to make a nomination for.</p>
                </div>
            </div>
        </div>
    </header>
    <!-- Page Content-->
    <section class="pt-4">
        <div class="container px-lg-5">
            <!-- Page Features-->
            <div class="row gx-lg-5">
                <div class="col-lg-5 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">Pack Awards</h2>
                            <p class="mb-0"></p>

                            <form action="./NominationPage.php" method=post>
                                <label for='AwardIDX'>Choose an Award: </label>
                                <select class='selectWrapper' id='AwardIDX' name='AwardIDX'>
                                    <option value=\"\" </option>
                                        <?php while ($rowAward = $result_ByPack->fetch_assoc()) {
                                            echo "<option value=" . $rowAward['AwardIDX'] . ">" . $rowAward['Award'] . "</option>";
                                        }
                                        ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type='submit' name='SubmitAward' value='Select Award' />
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">Troop Awards</h2>
                            <p class="mb-0"></p>

                            <form action="./NominationPage.php" method=post>
                                <label for='AwardIDX'>Choose an Award: </label>
                                <select class='selectWrapper' id='AwardIDX' name='AwardIDX'>
                                    <option value=\"\" </option>
                                        <?php while ($rowAward = $result_ByTroop->fetch_assoc()) {
                                            echo "<option value=" . $rowAward['AwardIDX'] . ">" . $rowAward['Award'] . "</option>";
                                        }
                                        ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type='submit' name='SubmitAward' value='Select Award' />
                            </form>

                            <!-- <a class="btn btn-primary btn-sm" href="./ypt_index.php">Reports</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">Crew / Ship Awards</h2>
                            <p class="mb-0"></p>

                            <form action="./NominationPage.php" method=post>
                                <label for='AwardIDX'>Choose an Award: </label>
                                <select class='selectWrapper' id='AwardIDX' name='AwardIDX'>
                                    <option value=\"\" </option>
                                        <?php while ($rowAward = $result_ByCrew->fetch_assoc()) {
                                            echo "<option value=" . $rowAward['AwardIDX'] . ">" . $rowAward['Award'] . "</option>";
                                        }
                                        ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type='submit' name='SubmitAward' value='Select Award' />
                            </form>

                            <!-- <a class="btn btn-primary btn-sm" href="./adv_report.php">Report</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">District Awards</h2>
                            <p class="mb-0"></p>

                            <form action="./NominationPage.php" method=post>
                                <label for='AwardIDX'>Choose an Award: </label>
                                <select class='selectWrapper' id='AwardIDX' name='AwardIDX'>
                                    <option value=\"\" </option>
                                        <?php while ($rowAward = $result_ByDistrict->fetch_assoc()) {
                                            echo "<option value=" . $rowAward['AwardIDX'] . ">" . $rowAward['Award'] . "</option>";
                                        }
                                        ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type='submit' name='SubmitAward' value='Select Award' />
                            </form>

                            <!-- <a class="btn btn-primary btn-sm" href="./pack_index.php">Reports</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">Youth Awards</h2>
                            <p class="mb-0"></p>

                            <form action="./NominationPage.php" method=post>
                                <label for='AwardIDX'>Choose an Award: </label>
                                <select class='selectWrapper' id='AwardIDX' name='AwardIDX'>
                                    <option value=\"\" </option>
                                        <?php while ($rowAward = $result_ByYouth->fetch_assoc()) {
                                            echo "<option value=" . $rowAward['AwardIDX'] . ">" . $rowAward['Award'] . "</option>";
                                        }
                                        ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type='submit' name='SubmitAward' value='Select Award' />
                            </form>

                            <!-- <a class="btn btn-primary btn-sm" href="./troop_index.php">Reports</a> -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-xxl-4 mb-5">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">
                            <div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="bi bi-card-heading"></i></div>
                            <h2 class="fs-4 fw-bold">Other Awards</h2>
                            <p class="mb-0"></p>

                            <form action="./NominationPage.php" method=post>
                                <label for='AwardIDX'>Choose an Award: </label>
                                <select class='selectWrapper' id='AwardIDX' name='AwardIDX'>
                                    <option value=\"\" </option>
                                        <?php while ($rowAward = $result_ByOther->fetch_assoc()) {
                                            echo "<option value=" . $rowAward['AwardIDX'] . ">" . $rowAward['Award'] . "</option>";
                                        }
                                        ?>
                                </select>
                                <input class='btn btn-primary btn-sm' type='submit' name='SubmitAward' value='Select Award' />
                            </form>

                            <!-- <a class="btn btn-primary btn-sm" href="./troop_index.php">Reports</a> -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php include("Footer.php"); ?>
</body>

</html>