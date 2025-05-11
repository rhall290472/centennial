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
include_once('CTroop.php');
include_once('CCrew.php');

$CTroop = CTroop::getInstance();
$CCrew = CCrew::getInstance();

if (isset($_POST['SubmitYear'])) {
	$SelYear = $_POST['Year'];
	$_SESSION['year'] = $SelYear;
	$CTroop->SetYear($SelYear);
}

$EagleYear = 2015;
$i = 0;
$CurrentYear = date('Y');
while ($EagleYear <= $CurrentYear) {
	$Year[$i] = $EagleYear;

	$troopEagle = 0;
	$crewEagle = 0;
	$totalEagle = 0;
	$sql = sprintf('SELECT SUM(Eagle) FROM adv_troop WHERE Date=%d', $EagleYear);

	if ($result = $CTroop->doQuery($sql)) {
		$rowcount = mysqli_num_rows($result);
	}
	$row = $result->fetch_assoc();
	$troopEagle = $row['SUM(Eagle)'];

	$sql = sprintf('SELECT SUM(Eagle) FROM adv_crew WHERE Date=%d', $EagleYear);
	if ($result = $CTroop->doQuery($sql)) {
		$rowcount = mysqli_num_rows($result);
	}

	$row = $result->fetch_assoc();
	$crewEagle = $row['SUM(Eagle)'];

	$sql = sprintf('SELECT SUM(Eagle) FROM adv_post WHERE Date=%d', $EagleYear);
	if ($result = $CTroop->doQuery($sql)) {
		$rowcount = mysqli_num_rows($result);
	}

	$row = $result->fetch_assoc();
	$postEagle = $row['SUM(Eagle)'];


	$totalEagle += $troopEagle;
	$totalEagle += $crewEagle;
	$totalEagle += $postEagle;
	$TotalEagles[$i] = $totalEagle;

	$EagleYear++;
	$i++;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include 'head.php'; ?>

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		google.charts.load('current', {
			'packages': ['bar']
		});
		google.charts.setOnLoadCallback(drawChart);



		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Year', 'Eagles'],
				<?php
				$i = 0;
				while ($i < count($Year)) {
					echo "['" . $Year[$i] . "'," . $TotalEagles[$i] . "],";
					$i++;
				}
				?>
			]);

			var options = {
				chart: {
					title: 'Centennial District Eagles',
					subtitle: '2015 - 2022',
				},
				bars: 'vertical' // Required for Material Bar Charts.
			};

			var chart = new google.charts.Bar(document.getElementById('barchart_material'));

			chart.draw(data, google.charts.Bar.convertOptions(options));
		}
	</script>

</head>

<body style="padding:10px">
	<!-- Responsive navbar-->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container px-lg-5">
			<a class="navbar-brand" href="#!">Centennial District Eagle Summary</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link active" aria-current="page" href="./index.php">Home</a></li>
					<!-- <li class="nav-item"><a class="nav-link" href="#!">About</a></li> -->
					<li class="nav-item"><a class="nav-link" href="./contact.php">Contact</a></li>
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

	<div class="my_div">
		<?php $CTroop->SelectYear(); ?>

		<center>
			<div id="barchart_material" style="width: 700px; height: 500px;"></div>

			<?php
			$sql = sprintf('SELECT * FROM adv_crew WHERE Eagle>0 AND Date=%d ORDER BY `Unit` ASC', $CCrew->GetYear());
			if ($result = $CCrew->doQuery($sql)) {
				$rowcount = mysqli_num_rows($result);
			}
			if ($rowcount > 0) {
			?>
				<table class="tl1 tc2 tc3 tc4">
					<td style="width:150px">
					<td style="width:50px">
					<td style="width:50px">
					<td style="width:50px">
						<tr>
							<th>Unit-Crew</th>
							<th>Eagle</th>
							<th>Palms</th>
							<th>Date</th>

						</tr>
					<?php
					echo "<br/>";

					while ($row = $result->fetch_assoc()) {
						$Unit = $row['Unit'];
						$UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
						$UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);

						echo "<tr><td>" .
							$UnitView . "</td><td>" .
							$row["Eagle"] . "</td><td>" .
							$row["Palms"] . "</td><td>" .
							$row["Date"] . "</td></tr>";
					}
					echo "</table>";
				}

				if ($rowcount > 0)
					mysqli_free_result($result);
					?>
				</table>

				<table class="table table-striped">
					<td style="width:150px">
					<td style="width:50px">
					<td style="width:50px">
					<td style="width:50px">
						<tr>
							<th>Unit-Troop</th>
							<th>Eagle</th>
							<th>Palms</th>
							<th>Date</th>

						</tr>
						<?php
						echo "<br/>";

						$sql = sprintf('SELECT * FROM adv_troop WHERE Eagle>0 AND Date=%d ORDER BY `Unit` ASC', $CTroop->GetYear());
						if ($result = $CTroop->doQuery($sql)) {
							$rowcount = mysqli_num_rows($result);
						}

						if ($rowcount > 0) {
							while ($row = $result->fetch_assoc()) {
								$Unit = $row['Unit'];
								$UnitURL = "<a href='Unit_View.php?btn=Units&unit_name=$Unit'";
								$UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
								echo "<tr><td>" .
									$UnitView . "</td><td>" .
									$row["Eagle"] . "</td><td>" .
									$row["Palms"] . "</td><td>" .
									$row["Date"] . "</td></tr>";
							}
							echo "</table>";
						}

						if ($rowcount > 0)
							mysqli_free_result($result);
						?>
				</table>

				<?php
				$sql = sprintf('SELECT SUM(Eagle) FROM adv_troop WHERE Date=%d', $CTroop->GetYear());
				if ($result = $CTroop->doQuery($sql)) {
					$rowcount = mysqli_num_rows($result);
				}
				$row = $result->fetch_assoc();
				$troopEagle = $row['SUM(Eagle)'];


				echo "<p> Total Eagle Scouts for the Current year: " . $troopEagle . " </p>";

				$CTroop->DisplayWarning('adv_troop');
				?>
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