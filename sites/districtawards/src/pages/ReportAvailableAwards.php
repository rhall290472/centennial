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
    <meta name="description" content="ReportAvailabeAwards.php">
</head>

<body>

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

</body>

</html>