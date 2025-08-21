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
    <meta name="description" content="ReportDeniedYear.php">
</head>

<body>

    <?php
    $csv_hdr = "Year, First Name, Preferred Name, Last Name, Award, Status, Member ID, Unit";
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
        <h4><?php echo "Nominated, but not awarded " . $cDistrictAwards->GetYear() ?> </h4>
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
            if ($year == "")
                $queryNominee = "SELECT * FROM `district_awards` WHERE `Status`='3' AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Award`, `Year` DESC";
            else
                $queryNominee = "SELECT * FROM `district_awards` WHERE `Year`='$year' AND `Status`='3' AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `Award`";

            if (!$ResultNominee = $cDistrictAwards->doQuery($queryNominee)) {
                $msg = "Error: doQuery()";
                $cDistrictAwards->function_alert($msg);
            }

            ?><tbody>
                <?php
                while ($rowNominee = $ResultNominee->fetch_assoc()) {
                    $AwardName = $cDistrictAwards->GetAwardName($rowNominee['Award']);
                    $Status = $cDistrictAwards->GetAwardStatus($rowNominee['Status']);

                    echo "<tr><td style='width:150px'>" .
                        $rowNominee["Year"] . "</td><td style='width:150px'>" .
                        $rowNominee["FirstName"] . "</td><td style='width:150px'>" .
                        $rowNominee["PName"] . "</td><td style='width:150px'>" .
                        "<a href=./NomineePage.php?NomineeIDX=" . $rowNominee['NomineeIDX'] . ">" . $rowNominee['LastName'] . "</a> </td><td  style='width:500px'>" .
                        $AwardName . "</td><td style='width:50px'>" .
                        $Status . "</td><td style='width:100px'>" .
                        $rowNominee['MemberID'] . "</td><td style='width:300px'>" .
                        $rowNominee['Unit'] . "</td></tr>";

                    $csv_output .= $rowNominee["Year"] . ",";
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
                //
                //                                                    echo "</br><b>For a total of " . mysqli_num_rows($Scout) . " Eagles this year.</b></br>";
                //                                                    echo "Statistics</br>";
                //                                                    $Eagled = mysqli_num_rows($Scout);
                //                                                    $AgedOut = $cDistrictAwards->AgedOutByYear($year);
                //                                                    $PreviewAged = $cDistrictAwards->AttendPreviewAgedOut($year);
                //                                                    $PreviewEagle = $cDistrictAwards->AttendPreviewEagled($year);
                //                                                    $ApprovedProject = $cDistrictAwards->ApprovedProject($year);
                //                                                    $str = sprintf(
                //                                                        "%d Scouts Aged out, of which %d Attended Eagle Preview, %d Had Approved Projects, Number of Eagles that attend Preview %d",
                //                                                        $AgedOut,
                //                                                        $PreviewAged,
                //                                                        $ApprovedProject,
                //                                                        $PreviewEagle
                //                    );
                //                                                    echo $str . "</br></br>";
                //                                                    
                ?>
                <form name="export" action="../export.php" method="post" style="padding: 20px;">
                    <input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
                    <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
                    <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
                </form>
    </center>

</body>

</html>