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
!  #                                                                        #  !
!  #  FILE NAME   :  index.php                                              #  !
!  #                                                                        #  !
!  #  DESCRIPTION :  Website to Support Centennial District Advacncement    #  !
!  #                 Data                                                   #  !
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

include_once('CAdvancement.php');
include_once('CUnit.php');
include_once('cAdultLeaders.php');     

require("header.php");

//$TrainedLeaders = TrainedLeaders::getInstance();
$cAdultLeaders = AdultLeaders::getInstance();
$CUnit = UNIT::getInstance();

// Reset the date, Needed ???
$_SESSION['year'] = date('Y');	// Reset back to current year.


?>
<!DOCTYPE html>
<html lang='en'>

<head style="width: 100vw;">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6PCWFTPZDZ"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-6PCWFTPZDZ');
    </script>

	<title>Centennial District Advancement Data</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--<link rel="stylesheet" href="css/bootstrap.css">-->
	<link rel="stylesheet" href="./bootstrap-5.3.2/css/bootstrap.css">
	<link rel="stylesheet" href="css/centennial.css">
	<link rel="icon" type="image/x-icon" href="./images/centennial.ico">
</head>

<?php
CHeader::DisplayHomeHeader();
?>

<body style="width: 100vw;">

	<div class='my_div'>
		<p>From the below selection you can view all of the untrained leaders and either select by last name,
			position or unit. If a name, position or unit does not show up in the selection then that selection
			has no untrained positions.</p>
		<p>This same logic hold true for leaders with invalid Youth Protection Training.</p>
		<p>The unit view will list all adult leaders and their training/ypt status along with the units
			advancement history.</p>
		<center>
			<div class="flex-container" style="min-width:1196px;">
			<!-----------------------------------------------------------------------------
			//
			// Start of Untrained Leader colum
			//
			------------------------------------------------------------------------------>
				<div style="width:27vw;">
					<p style="text-align:Center;font-size: 25px;">Untrained Leaders</p>
					<form action="Untrained.php">
						<ul>
							<li>
								<input class='RoundButton' type="submit" value="ByLastName" name="btn" />
								<select class="selectWrapper" id="MemberID" name="MemberID">
									<?php
									// First recod is blank "all"
									echo "<option value=\"\" </option>";
									$ResultName = $cAdultLeaders->GetUntrainedName();
									while ($rowName = $ResultName->fetch_assoc()) {
										echo "<option value=\"{$rowName['MemberID']}\">" . $rowName['Last_Name'] . " " . $rowName['First_Name'] . "</option>";
									}
									?>
								</select>
							</li>
							<li>
								<input class='RoundButton' type="submit" value="ByPosition" name="btn" />
								<select class="selectWrapper" id="position_name" name="position_name">
									<?php
									// First recod is blank "all"
									echo "<option value=\"\" </option>";
									$ResultPosition = $cAdultLeaders->GetUntrainedPosition();
									while ($rowPosition = $ResultPosition->fetch_assoc()) {
										echo "<option value=\"{$rowPosition['Position']}\">";
										echo $rowPosition['Position'];
										echo "</option>";
									}
									?>
								</select>
							</li>
							<li>
								<input class='RoundButton' type="submit" value="ByUnit" name="btn" />
								<select class="selectWrapper" id="Unit_Name" name="Unit">
									<?php
									// First recod is blank "all"
									echo "<option value=\"\" </option>";
									$ResultUnit = $cAdultLeaders->GetUntrainedUnit();
									while ($rowUnit = $ResultUnit->fetch_assoc()) {
										echo "<option value=\"{$rowUnit['Unit']}\">";
										echo $rowUnit['Unit'];
										echo "</option>";
									}
									?>
								</select>
							</li>
						</ul>
					</form><br>
					<a style="font-size: 20px" href="https://www.scouting.org/wp-content/uploads/2021/04/Position-Trained-Requirements-April-2021.pdf">Training Requirements</a>

				</div>
				<!-----------------------------------------------------------------------------
				//
				// Start of YPT colum
				//
				------------------------------------------------------------------------------>
				<div style="width:25vw;">
					<p style="text-align:center;font-size: 25px;">Youth Protection</p>
					<form action="YPT.php">
						<ul>
							<li>
								<input class='RoundButton' type="submit" value="ByLastName" name="btn">
								<select class="selectWrapper" id="MemberID" name="MemberID">
									<?php
									// First recod is blank "all"
									echo "<option value=\"\" </option>";
									$ResultYPTLastName = $cAdultLeaders->GetYPTLastName();
									while ($rowYPTLastName = $ResultYPTLastName->fetch_assoc()) {
										echo "<option value=\"{$rowYPTLastName['Member_ID']}\">" . $rowYPTLastName['Last_Name'] . " " . $rowYPTLastName['First_Name'] . "</option>";
									}
									?>
								</select>
							</li>
						</ul>
						<ul>
							<li>
								<input class='RoundButton' type="submit" value="ByPosition" name="btn">
								<select class="selectWrapper" id="position_name" name="position_name">
									<?php
									// First recod is blank "all"
									echo "<option value=\"\" </option>";
									$ResultYPTPosition = $cAdultLeaders->GetYPTPositon();
									while ($rowYPTPosition = $ResultYPTPosition->fetch_assoc()) {
										echo "<option value=\"{$rowYPTPosition['Position']}\">";
										echo $rowYPTPosition['Position'];
										echo "</option>";
									}
									?>
								</select>
							</li>
						<ul>
							<li>
								<input class='RoundButton' type="submit" value="ByUnit" name="btn">
								<select class="selectWrapper" id="Unit_Name" name="Unit_Number">
									<?php
									// First recod is blank "all"
									echo "<option value=\"\" </option>";
									$ResultYPTUnit = $cAdultLeaders->GetYPTUnit();
									while ($rowYPTUnit = $ResultYPTUnit->fetch_assoc()) {
										$FormmatedUnit = $rowYPTUnit['Unit_Number'];
										echo "<option value=\"{$rowYPTUnit['Unit_Number']}\">" . $FormmatedUnit . "</option>";
									}
									?>
								</select>
							</li>
						</ul>
					</form>
				</div>
				<!-----------------------------------------------------------------------------
				//
				// Start of Unit colum
				//
				------------------------------------------------------------------------------>
				<div style="width:40vw;">
					<p style="text-align:center;font-size: 25px;">Unit View</p>

					<form action="Unit_View.php">
						<input class="RoundButton" type="submit" value="Units" name="btn">
						<h6>Unit:</h6>
						<select class="selectWrapper" id="unit_name" name="unit_name">
							<?php
							// First recod is blank "all"
							echo "<option value=\"\" </option>";
							$ResultUnits = $CUnit->GetUnits();
							while ($rowUnits = $ResultUnits->fetch_assoc()) {
								echo "<option value=\"{$rowUnits['Unit']}\">";
								echo $rowUnits['Unit'];
								echo "</option>";
							}
							?>
						</select>
					</form>
					<button class='RoundButton' onclick="window.location.href ='pack_summary.php';">Pack Summary </button>
					<button class='RoundButton' onclick="window.location.href ='pack_below_goal.php';">Pack Below</button>
					<button class='RoundButton' onclick="window.location.href ='pack_meeting_goal.php';">Pack Meeting</button></br>
					<button class='RoundButton' onclick="window.location.href ='troop_summary.php';">Troop Summary </button>
					<button class='RoundButton' onclick="window.location.href ='troop_below_goal.php';">Troop Below</button>
					<button class='RoundButton' onclick="window.location.href ='troop_meeting_goal.php';">Troop Meeting</button></br>
					<button class='RoundButton' onclick="window.location.href ='crew_summary.php';">Crew Summary </button>
					<button class='RoundButton' onclick="window.location.href ='membership_report.php';">Membership</button>
					<button class='RoundButton' onclick="window.location.href ='eagle_summary.php';">Eagle Report </button></br>
					<button class='RoundButton' onclick="window.location.href ='adv_report.php';">Adv Report </button>
				</div>
			</div>
		</center>
	</div>
	<br>


	<?php
	/* phpinfo(); */
	?>


</body>

<body style="width: 100vw;">
<iframe src="https://www.google.com/maps/d/embed?mid=1Hj3PV-LAAKDU5-IenX9esVcbfx1_Ruc&ehbc=2E312F" width="100%" height="800px"></iframe>
</body>
</html>