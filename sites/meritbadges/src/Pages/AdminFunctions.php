<?php
// Load configuration
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}

load_class(BASE_PATH . '/src/Classes/CAdmin.php');

?>
<!DOCTYPE html>
<html lang="en">

<body>


    <div class="my_div">
        <center>
            <?php
			//$CAdvancement = CAdvancement::getInstance();
			$CAdmin = CAdmin::getInstance();

			$function = $_GET['AdminFunction'];

			switch ($function) {
				case "ByEditCounselor":
					echo "<div class='text-center'>";
					echo "<h2>ToDO:</h2>";
					echo "</div>";
					break;
				case "ByEditMB":
					echo "<div class='text-center'>";
					echo "<h2>ToDO:</h2>";
					echo "</div>";
					break;
				case "ByExpireypt":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active Merit Badge counselors in the Centennial District with Expired YPT.</h2>";
					echo "</div>";
					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES'";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportExpiredypt($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByExpiredSpecialTraining":
					echo "<div class='text-center'>";
					echo "<h2>ToDO:</h2>";
					echo "</div>";
					break;
				case "ByUntrained":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active but untrained Merit Badge Counselors.</h2>";
					echo "</div>";
					$Results = AdultLeaders::GetPositionUnTrained("Merit Badge Counselor");
					if ($CAdmin->ReportUnTrained($Results)) {
						$Results->free_result();
					}
					break;
				case "ByInactive":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all inactive Merit Badge Counselors.</h2>";
					echo "</div>";
					$sqlInactive = "SELECT * FROM `counselors` WHERE `Active`='NO'";
					if ($Results = $CAdmin->doQuery($sqlInactive, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportInactive($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByCounselorsperBadge":
					echo "<div class='text-center'>";
					echo "<h2>ToDO:</h2>";
					echo "</div>";
					break;
				case "ByNoID":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active Merit Badge Counselors in the Centennial District with No ID.</h2>";
					echo "</div>";

					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES' AND `MemberID`=''";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportNoEmail($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByNoEmail":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active Merit Badge Counselors in the Centennial District with No Email address.</h2>";
					echo "</div>";

					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES' AND `Email`=''";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportNoEmail($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "MBCnoMB":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active Merit Badge Counselors in the Centennial District with No assigned Merit Badges.</h2>";
					echo "</div>";
					$CAdmin->ReportMBCnoMB();
					break;
				case "ByMBwithnoCounselor":
					echo "<div class='text-center'>";
					echo "<h2>ToDO:</h2>";
					echo "</div>";
					break;
				case "ByCounselorwithNoUnit":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active Merit Badge Counselors in the Centennial District with No assigned Unit.</h2>";
					echo "</div>";

					$sqlExpiredypt = "SELECT * FROM `counselors` WHERE `Active`='YES' AND (`Unit1`='' OR `Unit1`='0000')";
					if ($Results = $CAdmin->doQuery($sqlExpiredypt, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportNoUnit($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByMB15":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain all active Merit Badge Counselors
			that have more than 15 merit badges.</h2>";
					echo "</div>";
					$sqlQuery15_1 = "SELECT * FROM counselors WHERE NumOfBadges > 15 AND counselors.Active = 'YES'";

					if ($Results = $CAdmin->doQuery($sqlQuery15_1, MYSQLI_STORE_RESULT)) {
						if ($CAdmin->ReportMB15($Results)) {
							$Results->free_result();
						}
					}
					break;
				case "ByMBCperMB":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain the number Merit Badge Counselors for each of the merit badges.</h2>";
					echo "</div>";
					$CAdmin->ReportMBCperMB();
					break;
				case "ByMBNoMBC":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain the number Merit Badge with no Counselors.</h2>";
					echo "</div>";
					$CAdmin->ReportMBNoMBC();
					break;
				case "ByCounselorInfo":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain the contact information for the Counselors.</h2>";
					echo "</div>";
					$CAdmin->ReportCounselorsInfo();
					break;
				case "ByCollegeHistory":
					echo "<div class='text-center'>";
					echo "<h2>This report will contain the history of past merit badge colleges.</h2>";
					echo "</div>";
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