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
!  #  FILE NAME   :  ImportScout.php                                        #  !
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
load_class(BASE_PATH . '/src/Classes/CScout.php');
$Scout = cScout::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
	$Scout->GotoURL("index.php");
	exit;
}

if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
	$CollegeYear = $Scout->GetYear();
	$GLOBALS["MBCollegeYear"] = $CollegeYear;
}

?>

<html>
<body>
	<div class="container-fluid">
		<div class="row flex-nowrap">
			<div class="col py-3">
				<p> Merit Badge College - Import Scouts </p>
				This function will import the scouts from the Double-Knot signup

				<form action="FileUpload.php" method="post" enctype="multipart/form-data">
					<!-- Upload a file -->
					<input class='RoundButton' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
					<input class='RoundButton' style="width:200;  height:40px; margin-bottom:15px ! important" type="submit" name="submit" value="ImportScouts">
				</form>
			</div>
		</div>
	</div>
</body>

</html>