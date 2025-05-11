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
    <meta name="description" content="ReportMemberID.php">
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container px-lg-5">
            <a class="navbar-brand" href="#!">Centennial District Awards</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="./Reports.php">Back</a></li>
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

    if (isset($_POST['SubmitYear'])) {
        $year = $_POST['Year'];
        $cDistrictAwards->SetYear($year);
        //header('Refresh: ' . 1);
    }
    // Dispay the Year Select dropdown selection
    $cDistrictAwards->SelectYear();

    ?>

    <center>
        <h4>Nominees with no Member ID</h4>
        <table class="fixed_header">
            <thead>
                <tr>
                    <th style='width:150px'>First Name</th>
                    <th style='width:150px'>Perferred Name</th>
                    <th style='width:150px'>Last Name</th>
                    <th style='width:500px'>Award</th>
                    <th style='width:50px'>Status</th>
                    <th style='width:150px'>Member ID</th>
                    <th style='width:500px'>Unit</th>
                </tr>
            </thead>
            <?php

            $year = $cDistrictAwards->GetYear();
            $queryNominees = "SELECT * FROM `district_awards` WHERE (`MemberID` IS NULL OR `MemberID`='0') AND NomineeIDX > '0' AND `Year`='$year' AND (`IsDeleted` IS NULL || `IsDeleted` <>'1')
            ORDER BY `LastName` ";

            if (!$Nominees = $cDistrictAwards->doQuery($queryNominees)) {
                $msg = "Error: doQuery()";
                $cDistrictAwards->function_alert($msg);
            }

            echo "<tbody>";
            while ($rowNominee = $Nominees->fetch_assoc()) {
                $AwardName = $cDistrictAwards->GetAwardName($rowNominee['Award']);
                $Status = $cDistrictAwards->GetAwardStatus($rowNominee['Status']);


                //$PeferredName = $cDistrictAwards->GetScoutPreferredName($rowScout);
                echo "<tr><td style='width:150px'>" .
                    $rowNominee["FirstName"] . "</td><td style='width:150px'>" .
                    $rowNominee["PName"] . "</td><td style='width:150px'>" .
                    "<a href=./NomineePage.php?NomineeIDX=" . $rowNominee['NomineeIDX'] . ">" . $rowNominee['LastName'] . "</a> </td><td  style='width:500px'>" .
                    $AwardName . "</td><td style='width:50px'>" .
                    $Status . "</td><td style='width:150px'>" .
                    $rowNominee['MemberID'] . "</td><td style='width:500px'>" .
                    $rowNominee['Unit'] . "</td></tr>";

                $csv_output .= $rowNominee["FirstName"] . ",";
                $csv_output .= $rowNominee["PName"] . ", ";
                $csv_output .= $rowNominee["LastName"] . ",";
                $csv_output .= $AwardName . ",";
                $csv_output .= $Status . ",";
                $csv_output .= $rowNominee['MemberID'] . ",";
                $csv_output .= $rowNominee['Unit'] . "\n";
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