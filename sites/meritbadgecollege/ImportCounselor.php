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
require_once('CScout.php');
$Scout = cScout::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
	$Scout->GotoURL("index.php");
	exit;
}

if (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] !== '') {
	$CollegeYear = $Scout->getYear();
	$GLOBALS["MBCollegeYear"] = $CollegeYear;
}

?>

<html>

<head>
	<?php include('header.php'); ?>
</head>

<body>
	<!-- Responsive navbar-->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
		<div class="container px-lg-4">
			<a class="navbar-brand" href="#!">Centennial District Merit Badge College</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="https://mbcollege.centennialdistrict.co/index.php">Home</a></li>
          <!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
          <li class="nav-item"><a class="nav-link" href="mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College">Contact</a></li>
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



	<div class="container-fluid">
		<div class="row flex-nowrap">
			<!-- Include the common side nav bar -->
			<?php include 'navbar.php'; ?>
			<div class="col">
				<p> Merit Badge College - Import Counselors </p>

				This function will import the Counselors from a Merit Badge List from my.scouting.org

				<form action="FileUpload.php" method="post" enctype="multipart/form-data">
					<!-- Upload a file -->
					<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
        </div>
        <div class="col">
					<input class='btn btn-primary btn-sm' style="width:200;  height:40px; margin-bottom:15px ! important" type="submit" name="submit" value="ImportCounselor">
        </div>
				</form>
				<?php
				?>

			</div>
		</div>
	</div>

	<?php include("Footer.php"); ?>
</body>

</html>