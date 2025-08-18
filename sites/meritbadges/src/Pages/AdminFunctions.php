<?php


?>
<!DOCTYPE html>
<html lang="en">

<body>


	<?php
	//CHeader::DisplayPageHeader("Merit Badge Counselors for Centennial District", "", "");
	?><div class="my_div">
		<center>
			<?php
			//$CAdvancement = CAdvancement::getInstance();
			$CAdmin = CAdmin::getInstance();

			$function = $_GET['AdminFunction'];

			switch ($function) {
				case "ByEditCounselor":
					break;
				case "ByEditMB":
					break;
				case "ByExpireypt":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active Merit Badge counselors in the Centennial District with Expired ypt.</h2>";
					echo "</div>";
					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES'";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportExpiredypt($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByExpiredSpecialTraining":
					break;
				case "ByUntrained":
					echo "<p>This report will contain all active but untrained Merit Badge Counselors.</p>";

					$Results = AdultLeaders::GetPositionUnTrained("Merit Badge Counselor");
					if ($CAdmin->ReportUnTrained($Results)) {
						$Results->free_result();
					}
					break;
				case "ByInactive":
					$sqlInactive = "SELECT * FROM `counselors` WHERE `Active`='NO'";
					if ($Results = $CAdmin->doQuery($sqlInactive, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportInactive($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByCounselorsperBadge":
					break;
				case "ByNoID":
					echo "<p>This report will contain all active Merit Badge Counselors
			in the Centennial District with No ID.</p>";

					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES' AND `MemberID`=''";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportNoEmail($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByNoEmail":
					echo "<p>This report will contain all active Merit Badge Counselors
			in the Centennial District with No Email address.</p>";

					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES' AND `Email`=''";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportNoEmail($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "MBCnoMB":
					echo "<p>This report will contain all active Merit Badge Counselors
			in the Centennial District with No assigned Merit Badges.</p>";
					$CAdmin->ReportMBCnoMB();
					break;
				case "ByMBwithnoCounselor":
					break;
				case "ByCounselorwithNoUnit":
					echo "<p>This report will contain all active Merit Badge Counselors in the Centennial District with No assigned Unit.</p>";

					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES' AND (`Unit1`='' OR `Unit1`='0000')";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportNoUnit($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByMB15":
					echo "<p>This report will contain all active Merit Badge Counselors
			that have more than 15 merit badges.</p>";
					$sqlQuery15_1 = "SELECT * FROM counselors WHERE NumOfBadges > 15 AND counselors.Active = 'YES'";

					if ($Results = $CAdmin->doQuery($sqlQuery15_1, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportMB15($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByMBCperMB":
					echo "<p>This report will contain the number Merit Badge Counselors
			for each of the merit badges.</p>";
					$CAdmin->ReportMBCperMB();
					break;
				case "ByMBNoMBC":
					echo "<p>This report will contain the number Merit Badge with no
			Counselors.</p>";
					$CAdmin->ReportMBNoMBC();
					break;
				case "ByCounselorInfo":
					echo "<p>This report will contain the contact information for the
			Counselors.</p>";
					$CAdmin->ReportCounselorsInfo();
					break;
				case "ByCollegeHistory":
					echo "<p>This report will contain the history of past merit badge colleges.</p>";
					$CAdmin->ReportCollegeHistory();
					break;
				default:
					printf("Error: Unknow Administrator Function type requested %s", $function);
					break;
			}
			?>
	</div>
	</center>
</body>

</html>