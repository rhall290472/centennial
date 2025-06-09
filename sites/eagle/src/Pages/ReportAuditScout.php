<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          \\!
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

// Load CEagle class (aligned with index.php's load_class)
load_class(__DIR__ . '/../Classes/CEagle.php');
$cEagle = CEagle::getInstance();
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');
$cLeaders = AdultLeaders::getInstance();

// Session check (handled in index.php, but included for standalone testing)
if (!session_id()) {
	session_start([
		'cookie_httponly' => true,
		'use_strict_mode' => true,
		'cookie_secure' => isset($_SERVER['HTTPS'])
	]);
}

// Authentication check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
	header("HTTP/1.0 403 Forbidden");
	exit;
}

// Ensure CSRF token is set
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>


<!DOCTYPE html>
<html lang="en">

<body>
	<center>
		<?php

		// Get a life of active life scouts..
		$queryScouts = "SELECT DISTINCTROW LastName, MiddleName, FirstName, Scoutid FROM scouts 
		WHERE (`Eagled` IS NULL OR `Eagled`='0') AND 
        	(`AgedOut` IS NULL OR `AgedOut`='0') AND
        	(`is_deleted` IS NULL OR `is_deleted`='0')
		ORDER BY LastName, FirstName";

		$result_ByScout = $cEagle->doQuery($queryScouts);
		if (!$result_ByScout) {
			$cEagle->function_alert("ERROR: $cEagle->doQuery($queryScouts)");
		}
		?>
		<form method=post>
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
			<div class="form-row px-5">
				<div class="col-2">
					<label for='ScoutName'>Choose a Scout: </label>
					<select class='form-control' id='ScoutName' name='ScoutName'>
						<option value=\"\" </option>
							<?php
							while ($rowScouts = $result_ByScout->fetch_assoc()) {
								echo "<option value=" . $rowScouts['Scoutid'] . ">" . $rowScouts['LastName'] . " " . $rowScouts['FirstName'] . "</option>";
								echo "option value=" . $rowScouts['LastName'] . ">" . $rowScouts['LastName'] . "/option";
							}
							?>
						<option value=-1>Add New</option>
					</select>
				</div>
				<div class="col-2 py-4">
					<input class='btn btn-primary btn-sm' type='submit' name='SubmitScout' value='Select Scout' />
				</div>
			</div>
			</div>
		</form>
		<?php
		//#####################################################
		//
		// Check to see if user as Submitted the form.
		//
		//#####################################################
		if (isset($_POST['SubmitScout']) && isset($_POST['ScoutName']) && $_POST['ScoutName'] !== '') {
			$SelectedScout = $_POST['ScoutName']; // Get name of Counselor selected

			// If new scout selected must create a record in the database for them.
			// There is a blank record in the database with Scoutid set to -1 for this.
			// Go get the Scout data
			$queryScout = "SELECT * FROM `scouts_audit_trail` WHERE Scoutid='$SelectedScout'";

			if (!$Scout = $cEagle->doQuery($queryScout)) {
				$msg = "Error: doQuery()";
				$cEagle->function_alert($msg);
			}

		?>
			<div class="px-5">

				<table class="fixed_header table table-striped">
					<thead>
						<tr>
							<th> ScoutID </th>
							<th> Column_name </th>
							<th> old_value </th>
							<th> new_value </th>
							<th> Done By </th>
							<th> Date/Time </th>
						</tr>
					</thead>

				<?php
				while ($rowScout = $Scout->fetch_assoc()) {
					echo "<tr><td>" .
						$rowScout["Scoutid"] . "</td><td>" .
						$rowScout["column_name"] . "</td><td>" .
						$rowScout["old_value"] . "</td><td>" .
						$rowScout["new_value"] . "</td><td>" .
						$rowScout["done_by"] . "</td><td>" .
						$rowScout["done_at"] . "</td></tr>";
				}
			}
				?>
				</table>
			</div>
	</center>
	</div>
</body>
</header>