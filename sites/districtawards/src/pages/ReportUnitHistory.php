<?php
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

load_class(BASE_PATH . '/src/Classes/CDistrictAwards.php');
$cDistrictAwards = cDistrictAwards::getInstance();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="description" content="ReportUnitHistory.php">
</head>

<body>

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
                        "<a href=index.php?page=edit-nominee&NomineeIDX=" . $rowUnit['NomineeIDX'] . ">" . $rowUnit['LastName'] . "</a> </td><td  style='width:500px'>" .
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

</body>

</html>