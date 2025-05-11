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
    <meta name="description" content="ReportUnitHistory.php">
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
    $csv_hdr = "Unit Type,Unit#,  Gender, Name, Year, Beneficiary, Project Name, Project Hours";
    $csv_output = "";


    $FirstName = "";
    $LastName = "";
    $Nominee = "";
    //Display a selection of Nominees TODO:
    if (isset($_POST['SubmitUnit'])) {
        $Unit = $_POST['Unit'];
        $cDistrictAwards->SetUnit($Unit);
        //header('Refresh: ' . 1);
    }
    // Dispay the Nominees Select dropdown selection
    $cDistrictAwards->SelectUnit();

    ?>


    <center>
        <h4><?php echo "Unit History" ?> </h4>

        <table class="fixed_header">
            <thead>
                <tr>
                    <th style='width:150px'>Year</th>
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
            $Unit = $cDistrictAwards->GetUnit();
            if ($Unit != "")
                $queryUnit = "SELECT * FROM `district_awards` WHERE Unit='$Unit' AND NomineeIDX > 0 AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Unit`";
            else
                $queryUnit = "SELECT * FROM `district_awards` WHERE NomineeIDX > 0 AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Unit`";

            if (!$ResultUnit = $cDistrictAwards->doQuery($queryUnit)) {
                $msg = "Error: doQuery()";
                $cDistrictAwards->function_alert($msg);
            }

            ?><tbody>
                <?php
                while ($rowUnit = $ResultUnit->fetch_assoc()) {
                    $AwardName = $cDistrictAwards->GetAwardName($rowUnit['Award']);
                    $Status = $cDistrictAwards->GetAwardStatus($rowUnit['Status']);

                    echo "<tr><td style='width:150px'>" .
                        $rowUnit["Year"] . "</td><td style='width:150px'>" .
                        $rowUnit["FirstName"] . "</td><td style='width:150px'>" .
                        $rowUnit["PName"] . "</td><td style='width:150px'>" .
                        "<a href=./NomineePage.php?NomineeIDX=" . $rowUnit['NomineeIDX'] . ">" . $rowUnit['LastName'] . "</a> </td><td  style='width:500px'>" .
                        $AwardName . "</td><td style='width:50px'>" .
                        $Status . "</td><td style='width:150px'>" .
                        $rowUnit['MemberID'] . "</td><td style='width:500px'>" .
                        $rowUnit['Unit'] . "</td></tr>";

                    $csv_output .= $rowUnit["FirstName"] . ",";
                    $csv_output .= $rowUnit["PName"] . ", ";
                    $csv_output .= $rowUnit["LastName"] . ",";
                    $csv_output .= $AwardName . ",";
                    $csv_output .= $Status . ",";
                    $csv_output .= $rowUnit['MemberID'] . ",";
                    $csv_output .= $rowUnit['Unit'] . "\n";
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