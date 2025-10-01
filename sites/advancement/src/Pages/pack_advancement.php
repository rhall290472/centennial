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
load_class(BASE_PATH.'/src/Classes/CPack.php');

$CPack = CPack::getInstance();

if (isset($_POST['SubmitYear'])) {
	$SelYear = $_POST['Year'];
	$_SESSION['year'] = $SelYear;
	$CPack->SetYear($SelYear);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php load_template('/src/Templates/header.php'); ?>
</head>


<?php
$Title = "Centennial District Pack Advancement - " . $_GET['unit_name'];
?>

<body>
	<!-- Responsive navbar-->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container px-lg-5">
			<a class="navbar-brand" href="#!"><?PHP echo $Title ?></a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
		</div>
	</nav>


	<?php
	$CPack->DisplayAdvancmenetDescription();
	$CPack->DisplayAdvancementTable();
	$var_value = $_GET['unit_name'];

	if (empty($var_value)) {
		exit();
		// Should never get here
	} else {
		$sql = sprintf('SELECT * FROM adv_pack WHERE Unit LIKE "%s%%"', $_GET['unit_name']);
	}
	if ($result = mysqli_query($CPack->getDbConn(), $sql)) {
		$rowcount = mysqli_num_rows($result);
	}



	if ($rowcount > 0) {
		while ($row = $result->fetch_assoc()) {
			$UnitYouth = $CPack->GetUnitTotalYouth($row['Unit'], $row['Youth'], $row["Date"]);
			$UnitRankScout = $CPack->GetUnitRankperScout($UnitYouth, $row["YTD"] + $row["adventure"], $row["Unit"]);

			if ($UnitRankScout == 0) // Make it Bold
				$Formatter = "<b style='color:red;'>";
			else if ($UnitRankScout >= $CPack->GetDistrictGoal($row["Date"]) && $UnitRankScout < $CPack->GetIdealGoal($row["Date"]))
				$Formatter = "<b style='color:orange;'>";
			else if ($UnitRankScout >= $CPack->GetIdealGoal($row["Date"]))
				$Formatter = "<b style='color:green;'>";
			else
				$Formatter = "";

			echo "<tr><td>" .
				$Formatter . $row["lion"] . "</td><td>" .
				$Formatter . $row["tiger"] . "</td><td>" .
				$Formatter . $row["bobcat"] . "</td><td>" .
				$Formatter . $row["wolf"] . "</td><td>" .
				$Formatter . $row["bear"] . "</td><td>" .
				$Formatter . $row["webelos"] . "</td><td>" .
				$Formatter . $row["aol"] . "</td><td>" .
				$Formatter . $row["YTD"] . "</td><td>" .
				$Formatter . $UnitYouth . "</td><td>" .
				$Formatter . $UnitRankScout . "</td><td>" .
				$Formatter . $Formatter . $row["adventure"] . "</td><td>" .
				$Formatter . $row["Date"] . "</td></tr>";
		}
		echo "</table>";
	} else {
		echo "0 result";
	}

	if ($rowcount > 0)
		mysqli_free_result($result);
	?>
	</table>
	<?php echo "<p style='text-align: center;  padding-bottom: 5rem !important;' class='px-lg-5'>Data last updated: " . $CPack->GetLastUpdated("adv_pack") . "</p>"; ?>

</body>

</html>