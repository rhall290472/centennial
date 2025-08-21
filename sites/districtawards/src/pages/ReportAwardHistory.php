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
    <meta name="description" content="ReportAwardHistory.php">
</head>

<body>

    <?php
    $csv_hdr = "Unit Type,Unit#,  Gender, Name, Year, Beneficiary, Project Name, Project Hours";
    $csv_output = "";


    $FirstName = "";
    $LastName = "";
    $Award = "";
    //Display a selection of Nominees TODO:
    if (isset($_POST['SubmitAward'])) {
        $Award = $_POST['Award'];
        $cDistrictAwards->SetAward($Award);
        //header('Refresh: ' . 1);
    }
    // Dispay the Nominees Select dropdown selection
    $cDistrictAwards->SelectAward();

    ?>


    <center>
        <h4><?php echo "Award History" ?> </h4>

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
            $Award = $cDistrictAwards->GetAward();
            if ($Award != "")
                $queryAward = "SELECT * FROM `district_awards` WHERE Award='$Award' AND NomineeIDX > 0 AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Year` DESC";
            else
                $queryAward = "SELECT * FROM `district_awards` WHERE NomineeIDX > 0 AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Year` DESC";

            if (!$ResultAward = $cDistrictAwards->doQuery($queryAward)) {
                $msg = "Error: doQuery()";
                $cDistrictAwards->function_alert($msg);
            }

            ?><tbody>
                <?php
                while ($rowAward = $ResultAward->fetch_assoc()) {
                    $AwardName = $cDistrictAwards->GetAwardName($rowAward['Award']);
                    $Status = $cDistrictAwards->GetAwardStatus($rowAward['Status']);

                    echo "<tr><td style='width:150px'>" .
                        $rowAward["Year"] . "</td><td style='width:150px'>" .
                        $rowAward["FirstName"] . "</td><td style='width:150px'>" .
                        $rowAward["PName"] . "</td><td style='width:150px'>" .
                        "<a href=./NomineePage.php?NomineeIDX=" . $rowAward['NomineeIDX'] . ">" . $rowAward['LastName'] . "</a> </td><td  style='width:500px'>" .
                        $AwardName . "</td><td style='width:50px'>" .
                        $Status . "</td><td style='width:150px'>" .
                        $rowAward['MemberID'] . "</td><td style='width:500px'>" .
                        $rowAward['Unit'] . "</td></tr>";

                    $csv_output .= $rowAward["FirstName"] . ",";
                    $csv_output .= $rowAward["PName"] . ", ";
                    $csv_output .= $rowAward["LastName"] . ",";
                    $csv_output .= $AwardName . ",";
                    $csv_output .= $Status . ",";
                    $csv_output .= $rowAward['MemberID'] . ",";
                    $csv_output .= $rowAward['Unit'] . "\n";
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