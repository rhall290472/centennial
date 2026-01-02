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

include_once('../CMeritBadges.php');
include_once('../../cAdultLeaders.php');

// This code stops anyone for seeing this page unless they have logged in and
// their account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
	header("HTTP/1.0 403 Forbidden");

	exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("../header.php"); ?>
</head>

<body>
	<!-- Responsive navbar-->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container px-lg-5">
			<a class="navbar-brand" href="#!">Centennial District Merit Badges</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link active" aria-current="page" href="../admin_index.php">Back</a></li>
					<li class="nav-item"><a class="nav-link" href="#!">About</a></li>
					<li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
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

	<?php
	$CMeritBadge = CMeritBadges::getInstance();
	$querySelectedCounselor1 = "SELECT DISTINCTROW counselors.LastName, counselors.FirstName, counselors.MemberID FROM counselors
	ORDER BY
		counselors.LastName,
		counselors.FirstName";

	$result_ByCounselor = $CMeritBadge->doQuery($querySelectedCounselor1);

	echo "<form method=post>";
	echo "<label for='UnitName'>Choose a Counselor: </label>";
	echo "<select class='selectWrapper' id= 'CounselorName' name='CounselorName' >";
	echo "<option value=\"\" </option>";
	while ($rowCerts = $result_ByCounselor->fetch_assoc()) {
		echo "<option value=" . $rowCerts['MemberID'] . ">" . $rowCerts['LastName'] . " " . $rowCerts['FirstName'] . "</option>";
	}
	echo '</select>';
	echo "<input class='btn btn-primary btn-sm' type='submit' name='SubmitCounselor' value='Submit Counselor'/>";
	echo "</form>";



	//if( isset($_POST['SubmitCounselor'])){
	if (isset($_POST['SubmitCounselor']) && isset($_POST['CounselorName']) && $_POST['CounselorName'] !== '') {
		$SelectedCounselor = $_POST['CounselorName']; // Get name of Counselor selected

		//ReportofSelectedCounselor($SelectedCounselor, $querySelectedCounselor2, $con);

		$queryCounselors = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
			ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName ) 
			WHERE counselors.MemberID LIKE ";
		//Create a sql statement to select chosen Counselor
		$sql = sprintf("%s '%s'", $queryCounselors, $SelectedCounselor);

		$Results = $CMeritBadge->doQuery($sql);
		$row = $Results->fetch_assoc();
		if ($row == null) {
			echo "No Data";
			exit;
		} else if ($row == false) {
			echo "Failure";
			exit;
		}

		//Break the phone number up into pieces
		//$Work_area_code   =  $CMeritBadge->left($row['WorkPhone'], 3);
		//$Work_prefix      =  $CMeritBadge->mid($row['WorkPhone'], 3, 3);
		//$Work_line_number =  $CMeritBadge->right($row['WorkPhone'], 4);

		$Home_area_code   =  $CMeritBadge->left($row['HomePhone'], 3);
		$Home_prefix      =  $CMeritBadge->mid($row['HomePhone'], 3, 3);
		$Home_line_number  =  $CMeritBadge->right($row['HomePhone'], 4);

		$Mobile_area_code   =  $CMeritBadge->left($row['MobilePhone'], 3);
		$Mobile_prefix      =  $CMeritBadge->mid($row['MobilePhone'], 3, 3);
		$Mobile_line_number =  $CMeritBadge->right($row['MobilePhone'], 4);

	?>


		<div id="form_container">

			<!--<h1><a>Update Counselor Data</a></h1>-->
			<form id="form_22772" class="appnitro" method="post" action="UpdateCounselor.php">
				<div class="form_description">
				</div>
				<ul>
					<li id="li_1">

						<span>
							<?php $FirstName = $row['FirstName']; ?>
							<label class="description" for="element_1">Name </label>
							<input id="FristName" name="FristName" class="element text" maxlength="255" size="8" value=<?php echo "'$FirstName'"; ?> />
							<label>First</label>
						</span>
						<span>
							<label class="description" for="element_2">Name </label>
							<input id="LastName" name="LastName" class="element text" maxlength="255" size="14" value=<?php echo $row['LastName']; ?> />
							<label>Last</label>
						</span>

						<span>
							<label class="description" for="Email">Email </label>
							<input id="Email" name="Email" class="element text large" type="text" maxlength="255" size="50" value=<?php echo $row['Email']; ?> />
							<label>Email</label>
						</span>
					</li>

					<li id="li_2">

						<!-- BSA has removed the address fields from the report -->
						<!-- BSA added it back 08/21/2021 -->

						<!--
						<span>
						<?php
						//$Address = $row['Address'];
						//if($Debug) echo $Address;
						?>
						<input id="element_2_1" name="element_2_1" class="element text large" value=<?php echo "'$Address'"; ?> type="text" />
						<label for="element_2_1">Street Address</label>
					</span>
					
					<span>
						<input id="element_2_3" name="element_2_3" class="element text medium" value=<?php echo $row['City']; ?> type="text">
						<label for="element_2_3">City</label>
					</span>

					<span>
						<input id="YPT" name="YPT" class="element text small" maxlength="2"value=<?php echo $row['State']; ?> type="text">
						<label for="YPT">State</label>
					</span>
					-->
						<span>
							<label class="description" for="YPT">YPT</label>
							<input id="YPT" name="YPT" class="element text large" maxlength="10" size="10" value=<?php echo $row['YPT']; ?>>
							<label for="YPT">YPT</label>
						</span>


						<span>
							<label class="description" for="Zip">Zip Code </label>
							<?php $FormattedZip = $CMeritBadge->formatZipCode($row['Zip']); ?>
							<input id="Zip" name="Zip" class="element text large" maxlength="10" size="10" value=<?php echo $FormattedZip; ?>>
							<label for="Zip">Postal / Zip Code</label>
						</span>

					</li>

					<li id="li_3">
						<span>
							<label class="description" for="element_5">Home Phone </label>
							<span>
								<input id="element_3_4" name="element_5_1" class="element text" size="3" maxlength="3" <?php if (strlen($Home_area_code) > 0) echo "value=" . $Home_area_code; ?>> -
								<label for="element_3_4">(###)</label>
							</span>
							<span>
								<input id="element_3_5" name="element_5_2" class="element text" size="3" maxlength="3" <?php if (strlen($Home_prefix) > 0) echo "value=" . $Home_prefix; ?>> -
								<label for="element_3_5">###</label>
							</span>
							<span>
								<input id="element_3_6" name="element_5_3" class="element text" size="4" maxlength="4" <?php if (strlen($Home_line_number) > 0) echo "value=" . $Home_line_number; ?>> -
								<label for="element_3_6">####</label>
							</span>
						</span>

						<label class="description" for="element_6">Mobile Phone </label>
						<span>
							<input id="element_3_7" name="element_6_1" class="element text" size="3" maxlength="3" <?php if (strlen($Mobile_area_code) > 0) echo "value=" . $Mobile_area_code; ?>> -
							<label for="element_3_7">(###)</label>
						</span>
						<span>
							<input id="element_3_8" name="element_6_2" class="element text" size="3" maxlength="3" <?php if (strlen($Mobile_prefix) > 0) echo "value=" . $Mobile_prefix; ?>> -
							<label for="element_3_8">###</label>
						</span>
						<span>
							<input id="element_3_9" name="element_6_3" class="element text" size="4" maxlength="4" <?php if (strlen($Mobile_line_number) > 0) echo "value=" . $Mobile_line_number; ?>> -
							<label for="element_3_9">####</label>
						</span>

					</li>

					<li id="li_8">
						<span>
							<label class="description" for="ValidationDate">Last Updated </label>
							<input id="ValidationDate" name="ValidationDate" type="text" size="11" value=<?php echo $row['ValidationDate']; ?> />
						</span>
						<span>
							<?php $District = $row['HomeDistrict']; ?>
							<label class="description" for="HomeDistrict">District </label>
							<input id="HomeDistrict" name="HomeDistrict" class="element text medium" type="text" size="25" maxlength="50" value=<?php echo "'$District'"; ?> />
						</span>
						<span>
							<label class="description" for="Registration">Registration #</label>
							<input id="Registration" name="Registration" class="element text medium" type="text" maxlength="255" value=<?php echo $row['MemberID']; ?> />
						</span>
						<span>
							<label class="description" for="HomeTroop">Unit1</label>
							<?php
							$HomeTroop = (isset($row['Unit1']) && $row['Unit1'] !== '' && $row['Unit1'] !== "0000") ? $row['Unit1'] : "";
							?>
							<input id="Unit1" name="Unit1" type="text" size="7" value=<?php if (strlen($HomeTroop > 0)) echo $HomeTroop ?> />
						</span>
						<span>
							<label class="description" for="SecTroop">Unit2</label>
							<?php
							$SecTroop = (isset($row['Unit2']) && $row['Unit2'] !== '') ? $row['Unit2'] : "";
							?>
							<input id="Unit2" name="Unit2" type="text" size="7" value=<?php if (strlen($SecTroop > 0)) echo $SecTroop ?> />
						</span>
					</li>


					<li id="li_14">

						<span>
							<label class="description" for="Active">Active </label>
							<?php
							if (!strcmp($row['Active'], "Yes") || !strcmp($row['Active'], "YES"))
								$Active = "checked";
							else
								$Active = "";
							?>

							<input id="Active" name="Active" class="element checkbox" type="checkbox" value="1" <?php echo $Active; ?> />
							<label class="choice" for="Active">Active</label>

						</span>

						<span>
							<?php
							$Trained = AdultLeaders::IsTrained($row['FirstName'], addslashes($row['LastName']), "Merit Badge Counselor");
							if (!strcmp($Trained, "Yes") || !strcmp($Trained, "YES"))
								$Trained = "checked";
							else
								$Trained = "";
							?>
							<label class="description" for="Trained">Trained </label>
							<input id="Trained" name="Trained" class="element checkbox" type="checkbox" value="Trained" <?php echo $Trained; ?> />
							<label class="choice" for="Trained">Trained</label>
						</span>

						<span>
							<?php
							if (!strcmp($row['DoNotPublish'], "Yes") || !strcmp($row['DoNotPublish'], "YES"))
								$DoNotPublish = "checked";
							else
								$DoNotPublish = "";
							?>
							<label class="description" for="DoNotPublish">Do Not Publish </label>
							<input id="DoNotPublish" name="DoNotPublish" class="element checkbox" type="checkbox" value="1" <?php echo $DoNotPublish; ?> />
							<label class="choice" for="DoNotPublish">Do Not Publish</label>
						</span>
					</li>

					<li id="li_15">
						<label class="description" for="Note">Notes </label>
						<div>
							<textarea cols="80" rows="12" id="Note" name="Note"><?php echo $row['Notes']; ?></textarea>
						</div>
					</li>

					<li class="buttons">
						<input type="hidden" name="form_id" value="22772" />
						<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
						<input id="saveForm" class="button_text" type="submit" name="delete" value="Delete" />
					</li>
				</ul>
		</div>
		</form>
		<div id="footer">
		</div>
		<img id="bottom" src="bottom.png" alt="">
		</div>
		<?php include("../Footer.php"); ?>
</body>
<?php
	}
?>

</html>