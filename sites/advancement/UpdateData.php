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
include('CAdvancement.php');

// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
	CAdvancement::GotoURL("index.php");
	header("location: index.php");
	exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'head.php'; ?>
</head>

<body class="body" style="padding:20px">
	<!-- Responsive navbar-->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container px-lg-5">
			<a class="navbar-brand" href="#!">Import Advancement Data</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
					<!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
					<li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
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

	<!-- Header-->
	<header class="py-5">
		<div class="container px-lg-5">
			<div class="p-4 p-lg-5 bg-light rounded-3 text-center">
				<div class="m-4 m-lg-5">
					<h1 class="display-5 fw-bold">Import Advancement Data</h1>
					<?php
					// $update = $_POST['Update'];
					$update = $_GET['Update'];

					switch ($update) {
						case "UpdateTotals":
					?>
							<p class="fs-4">Update Membership Totals</p>
							<p>The Membership Totals Report returns a detail list of each active unit within the structure of the district or council
								By unit, the reports give you demographic data such as the number of youth and adults, male and female along with the chartered
								organization and charter expiration date.</p>
							<p> Select the Membership Totals report file (UnitBasedmembershiptotalsReport.csv) which is downloaded from my.scouting.org</p>


							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdateTotals">
							</form>
						<?php break;
						case "UpdatePack":
						?>
							<p class="fs-4">Update Pack Advancement</p>
							<p>This report is a summary report by district, showing total advancement in Cub Packs reflected Month to Date totals and Year to
								Date totals.</p>
							<p> Select the cub scout advancement report file (DetailedAdvancementReportCubScout.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdatePack">
							</form>
						<?php break;
						case "UpdateTroop":
						?>
							<p class="fs-4">Update Troop Advancement</p>
							<p>This report is a summary report by district, showing total advancement in Scouts BSA troops reflected Month to Date totals
								and Year to Date totals.</p>
							<p> Select the troop advancment report file (DetailedAdvancementReportScoutsBSA.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdateTroop">
							</form>
						<?php break;
						case "UpdateCrew":
						?>
							<p class="fs-4">Update Crew Advancement</p>
							<p>This function will insert/update the BSA Advancement Data</p>
							<p> Select the troop advancment report file (DetailedAdvancementReportVenturing_Data.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdateCrew">
							</form>
						<?php break;
						case "TrainedLeader":
						?>
							<p class="fs-4">Update Trained Leader</p>
							<p>Lists each member's position trained leader status in the selected organization.</p>
							<p>This function will insert/update the trained leader data</p>
							<p> Select the Trained Leader report file (TrainedLeader_Centennial_02.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="TrainedLeader">
							</form>
						<?php break;
						case "Updateypt":
						?>
							<p class="fs-4">Update YPT</p>
							<p>Lists all members whose YPT have expired or never taken under the selected org.</p>
							<p>This function will insert/update the YPT status Data</p>
							<p> Select the YPT report file (YPT_Centennial_02.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="Updateypt">
							</form>

						<?php break;
						case "UpdateVenturing":
						?>
							<p class="fs-4">Update Venturing</p>
							<p>This report is a summary report by district, showing total advancement in Venture Crews reflected Month to Date totals and
								Year to Date totals.</p>
							<p>This function will insert/update the Venturing award Data</p>
							<p> Select the Venturing award report file (DetailedAdvancementReportVenturing.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdateVenturing">
							</form>
						<?php break;
						case "UpdateAdventure":
						?>
							<p class="fs-4">Update Adventures</p>
							<p>This report is a summary report by district, showing total Cub Scout Adventures in each pack and reflects Month to Date totals
								and Year to Date totals.</p>
							<p>This function will insert/update the Adventure award Data</p>
							<p> Select the Adventure award report file (DetailedAdvancementAwardsReportCubScout.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdateAdventure">
							</form>
						<?php break;
						case "UpdateCommissioners":
						?>
							<p class="fs-4">Update Commissioners</p>
							<p>Displays list of all units, including expired units, with assigned/unassigned commissioner status.</p>
							<p> Select the report file (Assigned_Unassigned_Units.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdateCommissioners">
							</form>
						<?php break;
						case "FunctionalRole":
						?>
							<p class="fs-4">Update Functional Roles</p>
							<p>Update Functional Role Assignements for Adult Scouters.</p>
							<p> Select the report file (FunctionalRoleAssignmentReport.csv) which is downloaded from my.scouting.org</p>

							<form action="FileUpload.php" method="post" enctype="multipart/form-data">
								<!-- Upload a file -->
								<input class='btn btn-primary btn-sm' style="width:500px; height:40px ! important" type="file" size="255" name="the_file" id=fileToUpload">
								<input class='btn btn-primary btn-sm' style="width:200;  height:40px  ! important" type="submit" name="submit" value="UpdateFunctionalRole">
							</form>
						<?php break;
						default:
						?>

					<?php break;
					}; ?>

				</div>
			</div>
		</div>
	</header>
	</div>
	<!-- Footer-->
   <?php include 'Footer.php' ?>
</body>

</html>