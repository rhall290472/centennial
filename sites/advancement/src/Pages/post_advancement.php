<?php require_once 'config/conn_inc.php';
require_once 'Support_Functions.php';
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

?>

<!DOCTYPE html>
<html lang="en">


<head>
	<?php include 'head.php'; ?>
</head>

<body>
	<div class="header">
		<a href="#default" class="logo">Centennial District Post Advancement</a>
	</div>


	<table style="text-align:Center">
		<td style="width:50px">
		<td style="width:50px">
		<td style="width:50px">
		<td style="width:50px">
		<td style="width:100px">
		<td style="width:100px">
		<td style="width:100px">
		<td style="width:100px">
		<td style="width:80px">
		<td style="width:80px">
		<td style="width:80px">
		<td style="width:80px">
		<td style="width:100px">
			<tr>
				<th>Star</th>
				<th>Life</th>
				<th>Eagle</th>
				<th>Palms</th>
				<th>Merit Badges</th>
				<th>Total Rank</th>
				<th>Total Youth</th>
				<th>Rank/Scout</th>
				<th>Date</th>

			</tr>
			<?php
			$var_value = $_GET['unit_name'];
			//printf("\Unit selected %s\t", $var_value);

			if (empty($var_value)) {
				// Should never get here
				//$sql = sprintf("SELECT * FROM trainedleadersleaders  WHERE District = 'Centennial - 02' AND Unit <> '' ORDER BY Unit ASC");
			} else {
				$sql = sprintf('SELECT * FROM adv_post WHERE Unit LIKE "%s%%"', $_GET['unit_name']);
				//echo $sql;

			}
			if ($result = mysqli_query($con, $sql)) {
				$rowcount = mysqli_num_rows($result);
			}


			//echo $rowcount;
			if ($rowcount > 0) {
				while ($row = $result->fetch_assoc()) {
					$UnitYouth = GetUnitTotalYouth($con, $row['Unit'], $row['Youth'], $row["Date"]);
					$Rank_Scout = GetUnitRankperScout($UnitYouth, ($row["YTD"] + $row["MeritBadge"]), $row['Unit']);

					echo "<tr><td>" .
						$row["Star"] . "</td><td>" .
						$row["Life"] . "</td><td>" .
						$row["Eagle"] . "</td><td>" .
						$row["Palms"] . "</td><td>" .
						$row["MeritBadge"] . "</td><td>" .
						$row["YTD"] . "</td><td>" .
						$UnitYouth . "</td><td>" .
						$Rank_Scout . "</td><td>" .
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
	<div class=WarningMessageContainer style="width:1024px">
		<p>This information is to be used only for authorized purposes on behalf of the Boy Scouts of America, Greater Colorado Council, Centennial District.
			Disclosing, copying, or making any inappropriate use of this information is strictly prohibited.</p>
	</div>

	<?php
	echo "<br>";
	$lastUpdated = GetLastUpdated($con, 'adv_post');
	echo "Content last changed: " . $lastUpdated;
	mysqli_close($con);
	?>

</body>

</html>