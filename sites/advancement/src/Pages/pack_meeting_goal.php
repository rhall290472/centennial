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
// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
	require_once __DIR__ . '/../../config/config.php';
} else {
	die('An error occurred. Please try again later.');
}

//require 'Support_Functions.php';
load_template('/src/Classes/CPack.php');

$CPack = CPack::getInstance();

if (isset($_POST['SubmitYear'])) {
	$SelYear = $_POST['Year'];
	$_SESSION['year'] = $SelYear;
	$CPack->SetYear($SelYear);
}

//First get total number of Pack in district
$TotalPacks = $CPack->GetNumofPacks();
$PacksAboveGoal = $CPack->GetNumofPacksAboveGoal();
$PacksMeetingGoal = $TotalPacks - $PacksAboveGoal;
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php load_template('/src/Templates/header.php'); ?>

	<!-- Pie chart for meeting/not meeting goals -->
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		google.charts.load('current', {
			'packages': ['corechart']
		});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {

			var data = google.visualization.arrayToDataTable([
				['Packs meeting goal', 'Packs below goal'],
				<?php
				echo $CPack->DisplayPacksAboveData();
				?>
			]);

			var options = {
				title: 'Packs Meeting ideal goal',
				slices: {
					0: {
						color: 'green'
					},
					1: {
						color: 'red'
					}
				},
				pieSliceText: 'value'
			};

			var chart = new google.visualization.PieChart(document.getElementById('piechart'));

			chart.draw(data, options);
		}
	</script>
</head>

<body style="padding:10px">
	<header id="header" class="header sticky-top">
		<?php $navbarTitle = 'Pack(s) meeting District goal of 3.6 Rank/Scout'; ?>
		<?php load_template('/src/Templates/navbar.php'); ?>
	</header>

	<div class="container-fluid">
		<div class="row flex-nowrap">
			<?php load_template('/src/Templates/sidebar.php'); ?>
			<sort_options>
				<div class="px-lg-5">
					<div class="row">
						<div class="col-1">
							<?php $SelYear = $CPack->SelectYear(); ?>
							<!-- </div> -->
							<div class="col-4">
								<div id="piechart"></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="px-lg-5 col-10">
							<div class="py-5">
								<?php
								$CPack->DisplayAdvancmenetDescription();
								$CPack->DisplayUnitAdvancement();

								echo "<p style='text-align: center;padding-bottom: 5rem !important;'>Number of units meeting goal: " . $PacksAboveGoal . " Out of: " . $TotalPacks . " Packs </p>";
								$sql = sprintf("SELECT * FROM adv_pack WHERE Date=%s ORDER BY Unit ASC", $CPack->GetYear());
								$result = mysqli_query($CPack->getDbConn(), $sql);
								while ($row = $result->fetch_assoc()) {
									$UnitYouth = $CPack->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CPack->GetYear());
									$UnitRankScout = $CPack->GetUnitRankperScout($row["Youth"], $row["YTD"] + $row["adventure"], $row["Unit"]);
									$Unit = $row['Unit'];
									$UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
									$UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
									if (floatval($UnitRankScout) < $CPack->GetDistrictGoal($row["Date"])) {
										continue;
									}

									if ($UnitRankScout == 0) // Make it Bold
										$Formatter = "<b style='color:red;'>";
									else if ($UnitRankScout >= $CPack->GetDistrictGoal($row['Date']) && $UnitRankScout < $CPack->GetIdealGoal($row['Date']))
										$Formatter = "<b style='color:orange;'>";
									else if ($UnitRankScout >= $CPack->GetIdealGoal($row['Date']))
										$Formatter = "<b style='color:green;'>";
									else
										$Formatter = "";

									echo "<tr><td>" .
										$Formatter . $UnitView . "</td><td>" .
										$Formatter . $row["lion"] . "</td><td>" .
										$Formatter . $row["tiger"] . "</td><td>" .
										$Formatter . $row["wolf"] . "</td><td>" .
										$Formatter . $row["bear"] . "</td><td>" .
										$Formatter . $row["webelos"] . "</td><td>" .
										$Formatter . $row["aol"] . "</td><td>" .
										$Formatter . $row["YTD"] . "</td><td>" .
										$Formatter . $UnitYouth . "</td><td>" .
										$Formatter . $UnitRankScout . "</td><td>" .
										$Formatter . $row["adventure"] . "</td><td>" .
										$Formatter . $row["Date"] . "</td></tr>";
								}
								echo "</table>";
								?>
								</table>
								<?php echo "<p style='text-align: center;padding-bottom: 5rem !important;'>Data last updated: " . $CPack->GetLastUpdated("adv_pack") . "</p>"; ?>
							</div>
						</div>
					</div>
				</div>
			</sort_options>
		</div>
	</div>

	<!-- Footer-->
	<?php load_template('/src/Templates/Footer.php'); ?>
</body>

</html>