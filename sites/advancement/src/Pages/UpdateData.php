<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #   Copyright 2017-2024 - Richard Hall                                   #  !
!  #   The information contained herein is the property of Richard          #  !
!  #   Hall, and shall not be copied, in whole or in part, or               #  !
!  #   disclosed to others in any manner without the express written        #  !
!  #   authorization of Richard Hall.                                       #  !
!  #                                                                        #  !
! /##########################################################################\ !
!//                                                                          \\!
!/                                                                            \!
!==============================================================================!
*/

load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');

// Redirect non-logged-in users
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
	$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Please log in to access this page.'];
	header("Location: index.php?page=login");
	exit;
}

$allowed_updates = [
	'UpdateTotals' => [
		'title' => 'Update Membership Totals',
		'description' => 'BSA removed the membership report that broke down membership by gender so, we will use the Chartered Organization report which does not provide gender.',
		'file' => 'CharteredOrganizations.csv'
	],
	'UpdatePack' => [
		'title' => 'Update Pack Advancement',
		'description' => 'This report is a summary report by district, showing total advancement in Cub Packs reflected Month to Date totals and Year to Date totals.',
		'file' => 'DetailedAdvancementReportCubScout.csv'
	],
	'UpdateTroop' => [
		'title' => 'Update Troop Advancement',
		'description' => 'This report is a summary report by district, showing total advancement in Scouts BSA troops reflected Month to Date totals and Year to Date totals.',
		'file' => 'DetailedAdvancementReportScoutsBSA.csv'
	],
	'UpdateCrew' => [
		'title' => 'Update Crew Advancement',
		'description' => 'This function will insert/update the BSA Advancement Data.',
		'file' => 'DetailedAdvancementReportVenturing_Data.csv'
	],
	'TrainedLeader' => [
		'title' => 'Update Trained Leader',
		'description' => 'Lists each member\'s position trained leader status in the selected organization. This function will insert/update the trained leader data.',
		'file' => 'TrainedLeader_Centennial_02.csv'
	],
	'Updateypt' => [
		'title' => 'Update YPT',
		'description' => 'Lists all members whose YPT have expired or never taken under the selected org. This function will insert/update the YPT status Data.',
		'file' => 'SYT_Centennial_02.csv'
	],
	'UpdateVenturing' => [
		'title' => 'Update Venturing',
		'description' => 'This report is a summary report by district, showing total advancement in Venture Crews reflected Month to Date totals and Year to Date totals. This function will insert/update the Venturing award Data.',
		'file' => 'DetailedAdvancementReportVenturing.csv'
	],
	'UpdateAdventure' => [
		'title' => 'Update Adventures',
		'description' => 'This report is a summary report by district, showing total Cub Scout Adventures in each pack and reflects Month to Date totals and Year to Date totals. This function will insert/update the Adventure award Data.',
		'file' => 'DetailedAdvancementAwardsReportCubScout.csv'
	],
	'UpdateCommissioners' => [
		'title' => 'Update Commissioners',
		'description' => 'Displays list of all units, including expired units, with assigned/unassigned commissioner status.',
		'file' => 'Assigned_Unassigned_Units.csv'
	],
	'UpdateFunctionalRole' => [
		'title' => 'Update Functional Roles',
		'description' => 'Update Functional Role Assignments for Adult Scouters.',
		'file' => 'FunctionalRoleAssignmentReport.csv'
	]
];

$update = filter_input(INPUT_GET, 'update');
if (!$update || !array_key_exists($update, $allowed_updates)) {
	$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid or missing update type.'];
	header("Location: index.php?page=home");
	exit;
}
?>

<div class="container px-lg-5 py-5">
	<div class="p-4 p-lg-5 bg-light rounded-3 text-center">
		<h1 class="display-5 fw-bold">Import Advancement Data</h1>
		<h2 class="fs-4"><?php echo htmlspecialchars($allowed_updates[$update]['title']); ?></h2>
		<p><?php echo htmlspecialchars($allowed_updates[$update]['description']); ?></p>
		<p>Select the report file (<strong><?php echo htmlspecialchars($allowed_updates[$update]['file']); ?></strong>) which is downloaded from my.scouting.org</p>

		<form action="index.php?page=updatedata" method="post" enctype="multipart/form-data">
			<div class="mb-3">
				<input type="file" name="the_file" id="fileToUpload" class="form-control" accept=".csv" required>
			</div>
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))); ?>">
			<input type="hidden" name="submit" value="<?php echo htmlspecialchars($update); ?>">
			<button type="submit" class="btn btn-primary">Upload File</button>
		</form>
	</div>
</div>