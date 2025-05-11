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

//require 'Support_Functions.php';
include_once('CCrew.php');


$CCrew = CCrew::getInstance();

if (isset($_POST['SubmitYear'])) {
	$SelYear = $_POST['Year'];
	$_SESSION['year'] = $SelYear;
	$CCrew->SetYear($SelYear);
}

$Totals = $CCrew->GetTotals();
$NumofCrews = $CCrew->GetNumofCrews();

$var_value = $_GET['unit_name'];

$Title = "Centennial District Crew Advancement - " . $_GET['unit_name'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'head.php'; ?>
</head>

<body>
	<!-- Responsive navbar-->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container px-lg-5">
			<a class="navbar-brand" href="#!"><?PHP echo $Title ?></a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
		</div>
	</nav>

	<center>
		<?php
		$CCrew->DisplayUnitAdvancementTable();

		$var_value = $_GET['unit_name'];
		//printf("\Unit selected %s\t", $var_value);

		if (empty($var_value)) {
			// Should never get here
			//$sql = sprintf("SELECT * FROM trainedleadersleaders  WHERE District = 'Centennial - 02' AND Unit <> '' ORDER BY Unit ASC");
		} else {
			$sql = sprintf('SELECT * FROM adv_crew WHERE Unit LIKE "%s%%"', $_GET['unit_name']);
			//echo $sql;

		}
		if ($result = mysqli_query($CCrew->getDbConn(), $sql)) {
			$rowcount = mysqli_num_rows($result);
		}


		//echo $rowcount;
		if ($rowcount > 0) {
			while ($row = $result->fetch_assoc()) {
				$UnitYouth = $CCrew->GetUnitTotalYouth($row['Unit'], $row['Youth'], $row["Date"]);
				$Rank_Scout = $CCrew->GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row['Unit']);

				echo "<tr><td>" .
					$row["Star"] . "</td><td>" .
					$row["Life"] . "</td><td>" .
					$row["Eagle"] . "</td><td>" .
					$row["Palms"] . "</td><td>" .
					$row["MeritBadge"] . "</td><td>" .
					$row["YTD"] . "</td><td>" .
					$UnitYouth . "</td><td>" .
					$Rank_Scout . "</td><td>" .
					$row["discovery"] . "</td><td>" .
					$row["pathfinder"] . "</td><td>" .
					$row["summit"] . "</td><td>" .
					$row["venturing"] . "</td><td>" .
					$row["Date"] . "</td></tr>";
			}
			echo "</table>";
		} else {
			echo "0 result";
		}

		if ($rowcount > 0)
			mysqli_free_result($result);
		?>
		</table>

		<?php $CCrew->DisplayWarning('adv_crew'); ?>

	</center>
	<!-- Footer-->
	<footer class="py-5 bg-dark">
		<div class="container">
			<p class="m-0 text-center text-white">Copyright &copy; centennialdistirct.co 2024</p>
		</div>
	</footer>
	<!-- Bootstrap core JS-->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Core theme JS-->
	<script src="js/scripts.js"></script>

</body>

</html>