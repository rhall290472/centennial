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
load_class(BASE_PATH . '/src/Classes/CScout.php');
$CScout = CScout::getInstance();
// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
    header('Location: index.php?page=login');
    exit;
}
?>
<html>
<body>
	<div class="container-fluid">
		<div class="row flex-nowrap">
			<div class="col py-3">
				<?php
				if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
					$CollegeYear = $_POST['CollegeYear'];
					setYear($CollegeYear);
				}

				// Display college year and allow user to select.
				$CollegeYear = $CScout->getYear();
				$CScout->SelectCollegeYearandScout($CollegeYear, "Enter Scout Data", false);
				?>

				<?php

				if (isset($_POST['ScoutName']) && $_POST['ScoutName'] !== '') {
					$SelectScout = $_POST['ScoutName'];
					$queryByMBCollege = sprintf(
						"SELECT * FROM college_registration WHERE College='%s' AND BSAIdScout='%s' ORDER BY LastNameScout, FirstNameScout, Period",
						$CollegeYear,
						$SelectScout
					);
				} else
					$queryByMBCollege = sprintf("SELECT * FROM college_registration WHERE College='%s' ORDER BY LastNameScout, FirstNameScout, Period", $CollegeYear);

				$report_results = $CScout->doQuery($queryByMBCollege, $CollegeYear);
				if ($CScout->ReportScoutMeritBadges($report_results, $CollegeYear)) {
					$report_results->free_result();
				}
				?>

			</div>
		</div>
	</div>
</body>

</html>