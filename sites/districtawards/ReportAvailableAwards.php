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
    <meta name="description" content="ReportAvailabeAwards.php">
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

    <?php
    $csv_hdr = "First Name, Peferred Name, Last Name, Award, Status, Member ID";
    $csv_output = "";

    ?>

    <center>
        <h4>Available District Awards</h4>
        <table>
            <thead>
                <tr>
                    <th style='width:450px'>Award</th>
                    <th style='width:50px'>AwardIDX</th>
                </tr>
            </thead>
            <?php

            $queryAwards = "SELECT * FROM `awards` ORDER BY `Award` ";

            if (!$ResultsAwards = $cDistrictAwards->doQuery($queryAwards)) {
                $msg = "Error: doQuery()";
                $cDistrictAwards->function_alert($msg);
            }

            echo "<tbody>";
            while ($rowAwards = $ResultsAwards->fetch_assoc()) {
                echo "<tr>" .
                    "<td style='width:450px'>" . $rowAwards['Award'] . "</td>" .
                    "<td style='width:50px'>" . $rowAwards['AwardIDX'] . "</td>" .
                    "</tr>";

                $csv_output .= $rowAwards["AwardIDX"] . ",";
                $csv_output .= $rowAwards["Award"] . "\n";
            }

            echo "</tbody>";
            echo "</table>";
            ?>
            <form name="export" action="../export.php" method="post" style="padding: 20px;">
                <input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
                <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
                <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
            </form>
    </center>

    <?php include("Footer.php"); ?>
</body>

</html>