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
!  #                                                                        #  !
!  #  FILE NAME   :  ScoutSchedule.php                                      #  !
!  #                                                                        #  !
!  #  DESCRIPTION :  Website to Support Merit Badge College.                #  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #  REFERENCES  :                                                         #  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #  CHANGE HISTORY ;                                                      #  !
!  #                                                                        #  !
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

    /*****************************************************************************/
/*                                                                           */
/* This file is used to show the scouts schedule the day of the event        */
/* A QR code will lead the scouts here to find their classes                 */
/*                                                                           */
/*****************************************************************************/

include_once 'CScout.php';
$CScout = CScout::getInstance();
?>
<html>

<head>
	<?php include('header.php'); ?>
</head>

<body style="padding:10px">
    <div class="header">
    </div>


        <?php
        $CollegeYear = $CScout->getYear();
        
        // Allow user to select a single scout to display,
        $CScout->SelectSingleScout($CollegeYear, false);
        ?>
    </div>

    <?php

        if (isset($_POST['ScoutName']) && $_POST['ScoutName'] !== '') {
            $SelectScout = $_POST['ScoutName'];
            $queryByMBCollege = sprintf("SELECT * FROM college_registration WHERE College='%s' AND BSAIdScout='%s' ORDER BY LastNameScout, FirstNameScout, Period", 
                $CollegeYear, $SelectScout);
        
            $report_results = $CScout->doQuery($queryByMBCollege, $CollegeYear);
            if ($CScout->ShowScoutMeritBadges($report_results, $CollegeYear)) {
                $report_results->free_result();
            }
        }
    ?>



</body>

</html>