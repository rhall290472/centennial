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

load_class(BASE_PATH . '/src/Classes/CPack.php');

$CPack = CPack::getInstance();

try {
	$SelYear = isset($_SESSION['year']) ? $_SESSION['year'] : date("Y");
	$CPack->SetYear($SelYear);

	// Get pack counts
	$TotalPacks = $CPack->GetNumofPacks();
	$PacksAboveGoal = $CPack->GetNumofPacksAboveGoal();
	$PacksMeetingGoal = $TotalPacks - $PacksAboveGoal;
} catch (Exception $e) {
	$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading pack data: ' . $e->getMessage()];
	error_log("PacksAboveGoal.php - Error: " . $e->getMessage(), 0);
	$TotalPacks = 0;
	$PacksAboveGoal = 0;
	$PacksMeetingGoal = 0;
}
?>

<sort_options>
	<div class="px-lg-5">
		<div class="row">
			<div class="col-2">
				<form action="index.php?page=packs_above_goal" method="POST">
					<p class="mb-0">Select Year</p>
					<?php
					try {
						$CPack->SelectYear();
					} catch (Exception $e) {
						$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error loading year selector: ' . $e->getMessage()];
						echo '<select class="form-control" name="Year"><option value="' . date("Y") . '">' . date("Y") . '</option></select>';
					}
					?>
					<!-- <input class="btn btn-primary btn-sm mt-2" type="submit" name="SubmitYear" value="Set Year"> -->
				</form>
			</div>
			<div class="col-4">
				<div id="piechart" style="width: 500px; height: 400px;"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="py-5">
					<?php
					try {
						$CPack->DisplayAdvancmenetDescription();
						//$CPack->DisplayUnitAdvancement();
						echo "<p style='text-align: center;'>Number of units meeting goal: " . $PacksAboveGoal . " Out of: " . $TotalPacks . " Packs</p>";

						if ($TotalPacks > 0) {
							echo '<table class="table table-striped"><tbody>' .
								'<th>Unit</th><th>Lion</th><th>Tiger</th><th>Wolf</th><th>Bear</th><th>Webelos</th><th>AOL</th><th>YTD</th><th>Youth</th><th>Rank/Scout</th><th>Adventure</th><th>Date</th></tr></thead><tbody>';;
							$sql = sprintf("SELECT * FROM adv_pack WHERE Date=%s ORDER BY Unit ASC", $CPack->GetYear());
							if ($result = mysqli_query($CPack->getDbConn(), $sql)) {
								while ($row = $result->fetch_assoc()) {
									$UnitYouth = $CPack->GetUnitTotalYouth($row['Unit'], $row['Youth'], $CPack->GetYear());
									$UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $row["YTD"] + $row["adventure"], $row["Unit"]);
									if (floatval($UnitRankScout) < $CPack->GetDistrictGoal($row["Date"])) {
										continue;
									}
									$Unit = $row['Unit']; // e.g., "Pack 0127-BP"
									// Extract only "Pack" from $Unit (assuming "Pack" is the part before the space or hyphen)
									$UnitDisplay = explode(' ', $Unit)[0]; // Gets "Pack" by splitting on space
									// Alternatively, if you want to split on hyphen: $UnitDisplay = explode('-', $Unit)[0];

									$URLPath = 'index.php?page=unitview&btn=Units&unit_name=' . urlencode($Unit); // Use urlencode for safety
									$UnitURL = "<a href=\"$URLPath\">"; // Double quotes for cleaner string
									$UnitView = sprintf("%s%s</a>", $UnitURL, htmlspecialchars($Unit));
									$Formatter = "";
									if ($UnitRankScout == 0) {
										$Formatter = "<b style='color:red;'>";
									} elseif ($UnitRankScout >= $CPack->GetDistrictGoal($row['Date']) && $UnitRankScout < $CPack->GetIdealGoal($row['Date'])) {
										$Formatter = "<b style='color:orange;'>";
									} elseif ($UnitRankScout >= $CPack->GetIdealGoal($row['Date'])) {
										$Formatter = "<b style='color:green;'>";
									}
									echo "<tr><td>$UnitView</td><td>$Formatter" . htmlspecialchars($row["lion"]) . "</td><td>$Formatter" .
										htmlspecialchars($row["tiger"]) . "</td><td>$Formatter" . htmlspecialchars($row["wolf"]) . "</td><td>$Formatter" .
										htmlspecialchars($row["bear"]) . "</td><td>$Formatter" . htmlspecialchars($row["webelos"]) . "</td><td>$Formatter" .
										htmlspecialchars($row["aol"]) . "</td><td>$Formatter" . htmlspecialchars($row["YTD"]) . "</td><td>$Formatter" .
										htmlspecialchars($UnitYouth) . "</td><td>$Formatter" . htmlspecialchars($UnitRankScout) . "</td><td>$Formatter" .
										htmlspecialchars($row["adventure"]) . "</td><td>$Formatter" . htmlspecialchars($row["Date"]) . "</td></tr>";
									if ($Formatter) echo "</b>";
								}
								mysqli_free_result($result);
							} else {
								throw new Exception("Database query failed: " . mysqli_error($CPack->getDbConn()));
							}
							echo "</tbody></table>";
						} else {
							echo "<p>No pack data available for $SelYear.</p>";
						}
						echo "<p style='text-align: center;'>Data last updated: " . htmlspecialchars($CPack->GetLastUpdated("adv_pack")) . "</p>";
					} catch (Exception $e) {
						$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error displaying pack data: ' . $e->getMessage()];
						error_log("PacksAboveGoal.php - Error: " . $e->getMessage(), 0);
					}
					?>
				</div>
			</div>
		</div>
	</div>
</sort_options>

<!-- Google Charts for Pie Chart -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', {
		'packages': ['corechart']
	});
	google.charts.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Category', 'Count'],
			<?php
			try {
				echo $CPack->DisplayPacksAboveData();
			} catch (Exception $e) {
				echo "['Packs meeting goal', 0],['Packs below goal', 0]";
			}
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