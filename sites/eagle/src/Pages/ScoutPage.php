<?php
/*
 * ScoutPage.php: Page for editing or adding scout records in the Centennial District Advancement website.
 * Handles scout selection, form submission, and updates to scout data.
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */

/// Load classes
load_class(__DIR__ . '/../Classes/CEagle.php');
$cEagle = CEagle::getInstance();
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');
$cLeaders = AdultLeaders::getInstance();

// Ensure CSRF token is set
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$rowScout = [];
$Street = '';
$Beneficiary = '';
$ProjectName = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['SubmitForm'], $_POST['csrf_token'])) {
	if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
		header("Location: index.php?page=edit-scout");
		exit;
	}

	if ($_POST['SubmitForm'] === 'Cancel') {
		$_SESSION['feedback'] = ['type' => 'info', 'message' => 'Form submission cancelled.'];
		header("Location: index.php?page=edit-scout");
		exit;
	}

	$SelectedScout = filter_input(INPUT_POST, 'Scoutid', FILTER_SANITIZE_NUMBER_INT);
	$queryScout = "SELECT * FROM `scouts` WHERE Scoutid = ?";
	$stmt = mysqli_prepare($cEagle->getDbConn(), $queryScout);
	mysqli_stmt_bind_param($stmt, 'i', $SelectedScout);
	mysqli_stmt_execute($stmt);
	$Scout = mysqli_stmt_get_result($stmt);
	$rowScout = mysqli_fetch_assoc($Scout) ?: [];
	mysqli_stmt_close($stmt);

	if (!$rowScout) {
		$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid scout selected.'];
		header("Location: index.php?page=edit-scout");
		exit;
	}

	// Collect form data
	$FormData = [
		'Scoutid' => $rowScout['Scoutid'],
		'FirstName' => $cEagle->GetFormData('element_1_1'),
		'PreferredName' => $cEagle->GetFormData('element_1_2'),
		'MiddleName' => $cEagle->GetFormData('element_1_3'),
		'LastName' => $cEagle->GetFormData('element_1_4'),
		'is_deleted' => $cEagle->GetFormData('element_1_5'),
		'Email' => $cEagle->GetFormData('element_2_1'),
		'Phone_Home' => $cEagle->GetFormData('element_2_2'),
		'Phone_Mobile' => $cEagle->GetFormData('element_2_3'),
		'Street_Address' => $cEagle->GetFormData('element_3_1'),
		'City' => $cEagle->GetFormData('element_3_2'),
		'State' => $cEagle->GetFormData('element_3_3'),
		'Zip' => $cEagle->GetFormData('element_3_4'),
		'UnitType' => $cEagle->GetFormData('element_4_1'),
		'UnitNumber' => $cEagle->GetFormData('element_4_2'),
		'District' => $cEagle->GetFormData('element_4_3'),
		'Gender' => $cEagle->GetFormData('element_4_4'),
		'AgeOutDate' => $cEagle->GetFormData('element_4_5'),
		'MemberId' => $cEagle->GetFormData('element_4_6'),
		'ULFirst' => $cEagle->GetFormData('element_5_1'),
		'ULLast' => $cEagle->GetFormData('element_5_2'),
		'ULPhone' => $cEagle->GetFormData('element_5_3'),
		'ULEmail' => $cEagle->GetFormData('element_5_4'),
		'CCFirst' => $cEagle->GetFormData('element_6_1'),
		'CCLast' => $cEagle->GetFormData('element_6_2'),
		'CCPhone' => $cEagle->GetFormData('element_6_3'),
		'CCEmail' => $cEagle->GetFormData('element_6_4'),
		'GuardianFirst' => $cEagle->GetFormData('element_6_1a'),
		'GuardianLast' => $cEagle->GetFormData('element_6_2a'),
		'GuardianPhone' => $cEagle->GetFormData('element_6_3a'),
		'GuardianEmail' => $cEagle->GetFormData('element_6_4a'),
		'GuardianRelationship' => $cEagle->GetFormData('element_6_5a'),
		'AgedOut' => $cEagle->GetFormData('element_9_4'),
		'AttendedPreview' => $cEagle->GetFormData('element_7_2'),
		'ProjectApproved' => $cEagle->GetFormData('element_7_3'),
		'ProjectDate' => $cEagle->GetFormData('element_7_4'),
		'Coach' => $cEagle->GetFormData('element_7_5'),
		'ProjectHours' => $cEagle->GetFormData('element_7_6'),
		'Beneficiary' => $cEagle->GetFormData('element_8_1'),
		'ProjectName' => $cEagle->GetFormData('element_8_2'),
		'BOR' => $cEagle->GetFormData('element_9_1'),
		'BOR_Member' => $cEagle->GetFormData('element_9_2'),
		'Eagled' => $cEagle->GetFormData('element_9_3'),
		'Notes' => $cEagle->GetFormData('element_10_1'), // Fixed element ID
	];

	if (strlen($FormData['ProjectDate']) > 1) {
		$FormData['ProjectApproved'] = 1;
	}

	if ($cEagle->UpdateScoutRecord($FormData)) {
		$cEagle->CreateAudit($rowScout, $FormData, 'Scoutid');
		$_SESSION['feedback'] = ['type' => 'success', 'message' => 'Scout record updated successfully.'];
	} else {
		$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed to update scout record.'];
	}
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	header("Location: index.php?page=home");
	exit;
}

// Scout selection query
$queryScouts = "SELECT DISTINCT LastName, MiddleName, FirstName, Scoutid FROM scouts 
                WHERE (`Scoutid` IS NOT NULL)
                AND (`Eagled` IS NULL OR `Eagled` = 0) 
                AND (`AgedOut` IS NULL OR `AgedOut` = 0)
                AND (`is_deleted` IS NULL OR `is_deleted` = 0)
                ORDER BY LastName, FirstName";
$result = $cEagle->doQuery($queryScouts);
if (!$result) {
	$cEagle->function_alert("ERROR: Query failed: " . mysqli_error($cEagle->getDbConn()));
}

// Handle scout selection
if (isset($_POST['SubmitScout'], $_POST['ScoutID'], $_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
	$SelectedScout = filter_input(INPUT_POST, 'ScoutID', FILTER_VALIDATE_INT);
	if ($SelectedScout === -1) {
		// Create a new scout record
		$queryInsert = "INSERT INTO scouts (is_deleted) VALUES (0)";
		if ($cEagle->doQuery($queryInsert)) {
			$SelectedScout = mysqli_insert_id($cEagle->getDbConn());
		} else {
			$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed to create new scout record.'];
			header("Location: index.php?page=edit-scout");
			exit;
		}
	}

	$queryScout = "SELECT * FROM `scouts` WHERE Scoutid = ?";
	$stmt = mysqli_prepare($cEagle->getDbConn(), $queryScout);
	mysqli_stmt_bind_param($stmt, 'i', $SelectedScout);
	mysqli_stmt_execute($stmt);
	$Scout = mysqli_stmt_get_result($stmt);
	$rowScout = mysqli_fetch_assoc($Scout) ?: [];
	mysqli_stmt_close($stmt);

	if ($rowScout) {
		$Street = htmlspecialchars($rowScout['Street_Address'] ?? '');
		$Beneficiary = htmlspecialchars($rowScout['Beneficiary'] ?? '');
		$ProjectName = htmlspecialchars($rowScout['ProjectName'] ?? '');

		// Update unit leader and committee information
		$UnitFormatted = trim(($rowScout['UnitType'] ?? '') . ' ' . ($rowScout['UnitNumber'] ?? ''));
		$UnitFormatted = $cLeaders->formatUnitNumber($UnitFormatted, $rowScout['Gender']);
		$UnitLeader = $cLeaders->GetUnitLeader($UnitFormatted);

		if ($UnitLeader) {
			//$UnitFormatted = $cLeaders->getUnitName($UnitFormatted, $rowScout['UnitType'] ?? '');
			// $UnitLeader = $cLeaders->getUnitLeader($SelectedScout);
			$rowScout['ULFirst'] = trim($UnitLeader['FirstName'] ?? '');
			$rowScout['ULLast'] = trim($UnitLeader['LastName'] ?? '');
			$rowScout['ULPhone'] = str_replace(' ', '', $UnitLeader['Phone'] ?? '');
			$rowScout['ULEmail'] = trim($UnitLeader['Email'] ?? '');
			$CommitteeChair = $cLeaders->GetCommitteeChair($UnitFormatted);
			$rowScout['CCFirst'] = trim($CommitteeChair['FirstName'] ?? '');
			$rowScout['CCLast'] = trim($CommitteeChair['LastName'] ?? '');
			$rowScout['CCPhone'] = str_replace(' ', '', $CommitteeChair['Phone'] ?? '');
			$rowScout['CCEmail'] = trim($CommitteeChair['Email'] ?? '');
		}

		$_SESSION['feedback'] = ['type' => 'success', 'message' => 'Scout selected successfully.'];
	} else {
		$_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid scout selected.'];
		$rowScout = []; // Ensure $rowScout is empty if no valid scout
	}
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
unset($_SESSION['feedback']);
?>

<div class="container-fluid">
	<!-- Display Feedback -->
	<?php if (!empty($feedback)): ?>
		<div class="alert alert-<?php echo htmlspecialchars($feedback['type']); ?> alert-dismissible fade show"
			role="alert">
			<?php echo htmlspecialchars($feedback['message']); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<h4>Select Scout to Edit</h4>
	<form method="post">
		<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
		<div class="form-row px-5 d-print-none">
			<div class="col-3">
				<label for="ScoutID">Choose a Scout: </label>
				<select class="form-control" id="ScoutID" name="ScoutID">
					<option value="">-- Select Scout --</option>
					<?php while ($row = $result->fetch_assoc()): ?>
						<?php if ($row['Scoutid'] != -1) { ?>
							<option value="<?php echo htmlspecialchars($row['Scoutid']); ?>">
								<?php echo htmlspecialchars(trim($row['LastName'] . ', ' . $row['FirstName'])); ?>
							</option>
						<?php } ?>
					<?php endwhile; ?>
					<option value="-1">Add New Scout</option>
				</select>
			</div>
			<div class="col-1 py-4">
				<input class="btn btn-primary btn-sm" type="submit" name="SubmitScout" value="Select Scout" />
			</div>
		</div>
	</form>

	<?php if (!empty($rowScout)): ?>
		<?php require('ScoutForm.php'); ?>
	<?php endif; ?>
</div>