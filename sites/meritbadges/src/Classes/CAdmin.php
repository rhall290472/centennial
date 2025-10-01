<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
	session_start([
		'cookie_httponly' => true,
		'use_strict_mode' => true,
		'cookie_secure' => isset($_SERVER['HTTPS'])
	]);
}

include_once('CMeritBadges.php');

class CAdmin extends CMeritBadges
{
	private static $instances = [];

	protected function __construct() {}

	protected function __clone() {}

	public function __wakeup()
	{
		throw new \Exception("Cannot unserialize a singleton.");
	}

	public static function getInstance()
	{
		$cls = static::class;
		if (!isset(self::$instances[$cls])) {
			self::$instances[$cls] = new static();
		}
		return self::$instances[$cls];
	}

	/*=============================================================================
	 *
	 * This function will produce a list of merit to counselors with Expired ypt
	 * 
	 *===========================================================================*/
	function ReportExpiredypt($Results)
	{

		echo "<table class='table table-striped'>";
		echo "<tr>";
		echo "<th>Unit</th>";
		echo "<th>First Name</th>";
		echo "<th>Last Name</th>";
		echo "<th>Email</th>";
		echo "<th>Member ID</th>";
		echo "<th>YPT</th>";
		echo "</tr>";

		$csv_hdr = "Unit1, First Name, Last Name, Email, Member ID, YPT";
		$csv_output = "";

		$ExpiredCount = 0;
		$TodaysDate = strtotime("now");

		$CounselorCount = mysqli_num_rows($Results);
		//echo "Counselors :".$ExpiredCount;

		while ($row = $Results->fetch_assoc()) {
			$Expired = false;
			$Counselorsypt = strtotime($row['YPT']);
			if ($TodaysDate > $Counselorsypt) {
				$Expired = true;
				$ExpiredCount++;
			}
			if ($Expired && (!strcmp($row['Active'], "YES") || !strcmp($row['Active'], "Yes"))) {

				echo "<tr><td>" .
					$row['Unit1'] . "</td><td>" .
					$row['FirstName'] . "</td><td>" .
					$row['LastName'] . "</td><td>" .
					$row['Email'] . "</td><td>" .
					$row['MemberID'] . "</td><td>" .
					$row['YPT'] . "</td><td>";

				$csv_output .= $row['Unit1'] . ", ";
				$csv_output .= $row['FirstName'] . ", ";
				$csv_output .= $row['LastName'] . ", ";
				$csv_output .= $row['Email'] . ", ";
				$csv_output .= $row['MemberID'] . ", ";
				$csv_output .= $row['YPT'] . "\n";
			}
		}
		echo "</table>";

		echo "<br/>Counselors with expired YPT: " . $ExpiredCount . " out of " . $CounselorCount . " Counselors<br/>";
?>

		<br />
		<center>
		<?php
	}

	/*=============================================================================
	 *
	 * This function will produce a list of merit to counselors with No Email
	 * 
	 *===========================================================================*/
	function ReportNoEmail($Results)
	{

		echo "<table class='table table-striped'>";
		echo "<td style='width:100px'>";
		echo "<td style='width:100px'>";
		echo "<td style='width:100px'>";
		echo "<td style='width:100px'>";
		echo "<td style='width:100px'>";
		echo "<td style='width:100px'>";
		echo "<tr>";
		echo "<th>Unit</th>";
		echo "<th>First Name</th>";
		echo "<th>Last Name</th>";
		echo "<th>Email</th>";
		echo "<th>Member ID</th>";
		echo "<th>YPT</th>";
		echo "</tr>";

		$ExpiredCount = 0;

		$ExpiredCount = mysqli_num_rows($Results);
		echo "Counselors :" . $ExpiredCount;
		$TodaysDate = strtotime("now");

		while ($row = $Results->fetch_assoc()) {
			if (!strcmp($row['Active'], "Yes")) {

				$Expired = false;
				$Counselorsypt = strtotime($row['YPT']);
				if ($TodaysDate > $Counselorsypt) {
					$Expired = true;
				}
				if ($Expired)
					$FormatterYPT = "<b style='color:red;'>";
				else
					$FormatterYPT = "";


				echo "<tr><td>" .
					$row['HomeTroop'] . "</td><td>" .
					$row['FirstName'] . "</td><td>" .
					$row['LastName'] . "</td><td>" .
					$row['Email'] . "</td><td>" .
					$row['MemberID'] . "</td><td>" .
					$FormatterYPT . $row['YPT'] . "</td><td>";
			}
		}
		echo "</table>";
	}

	/*=============================================================================
	 *
	 * This function will produce a list of merit to counselors that are
	 * Untrained
	 * 
	 *===========================================================================*/
	function ReportUnTrained($Results)
	{

		echo "<table class='table table-striped'>";
		echo "<td style='width:150px'>";
		echo "<td style='width:150px'>";
		echo "<td style='width:200px'>";
		echo "<td style='width:200px'>";
		echo "<td style='width:200px'>";
		echo "<tr>";
		echo "<th>First Name</th>";
		echo "<th>Last Name</th>";
		echo "<th>Email</th>";
		echo "<th>Member ID</th>";
		echo "<th>Trained</th>";
		echo "</tr>";

		$csv_hdr = "First Name, Last Name, Email, Member ID, D76, Trained";
		$csv_output = "";

		while ($row = $Results->fetch_assoc()) {
			$Email = "<a href='mailto:" . $row['Email'] . "?subject=Merit Badge Counselor'>" . $row['Email'] . "</a>";
			echo "<tr><td>" .
				$row['First_Name'] . "</td><td>" .
				$row['Last_Name'] . "</td><td>" .
				$Email . "</td><td>" .
				$row['MemberID'] . "</td><td>" .
				$row['Trained'] . "</td></tr>";

			$csv_output .= $row['First_Name'] . ", ";
			$csv_output .= $row['Last_Name'] . ", ";
			$csv_output .= $row['Email'] . ", ";
			$csv_output .= $row['MemberID'] . ", ";
			$csv_output .= $row['Trained'] . "\n";
		}
		echo "</table>";

		echo "Counselors Untrained :" . mysqli_num_rows($Results);
		?>

			<br />
			<center>
			<?php
		}

		/*=============================================================================
	 *
	 * This function will produce a list of merit to counselors that are
	 * Inactive
	 * 
	 *===========================================================================*/
		function ReportInactive($Results)
		{
			?>
				<div class="container-fluid">
					<p class='py-3'>This report will contain all Inactive Merit Badge Counselors.</p>
					<table class='table table-striped'>
						<!-- <td style='width:150px'> -->
						<!-- <td style='width:100px'> -->
						<!-- <td style='width:150px'> -->
						<!-- <td style='width:150px'> -->
						<!-- <td style='width:200px'> -->
						<!-- <td style='width:100px'> -->
						<!-- <td style='width:100px'> -->
						<tr>
							<th>District</th>
							<th>Unit</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Member ID</th>
							<th>Validation Date</th>
							<th>YPT</th>
							<th>Active</th>
						</tr>
						<?php

						$csv_hdr = "District, Unit, First Name, Last Name, Email, Member ID, Validation Date, YPT Date, Active";
						$csv_output = "";


						while ($row = $Results->fetch_assoc()) {
							$Email = "<a href='mailto:" . $row['Email'] . "?subject=Merit Badge Counselor'>" . $row['Email'] . "</a>";
							echo "<tr><td>" .
								$row['HomeDistrict'] . "</td><td>" .
								$row['Unit1'] . "</td><td>" .
								$row['FirstName'] . "</td><td>" .
								$row['LastName'] . "</td><td>" .
								$Email . "</td><td>" .
								$row['MemberID'] . "</td><td>" .
								$row['ValidationDate'] . "</td><td>" .
								$row['YPT'] . "</td><td>" .
								$row['Active'] . "</td><td>";

							$csv_output .= $row['Unit1'] . ", ";
							$csv_output .= $row['FirstName'] . ", ";
							$csv_output .= $row['LastName'] . ", ";
							$csv_output .= $row['Email'] . ", ";
							$csv_output .= $row['MemberID'] . ", ";
							$csv_output .= $row['ValidationDate'] . ", ";
							$csv_output .= $row['YPT'] . ",";
							$csv_output .= $row['Active'] . "\n";
						}
						?>
					</table>
					<p>Counselors Inactive :<?php echo mysqli_num_rows($Results) ?> </p>

				</div>

				<div>
					<form name="export" action="../export.php" method="post">
						<input class='btn btn-primary btm-sm' style="width:220px" type="submit" value="Export table to CSV">
						<input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
						<input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
					</form>

				</div>
				<br />

			<?php
		}

		/*=============================================================================
	 *
	 * This function will produce a list of counselors to merit badges
	 * 
	 *===========================================================================*/
		function ReportNoUnit($Results)
		{
			$Debug = false;

			echo "<table class='table table-striped'>";
			echo "<td style='width:200px'>";
			echo "<td style='width:200px'>";
			echo "<td style='width:200px'>";
			echo "<td style='width:100px'>";
			echo "<td style='width:100px'>";
			echo "<tr>";
			echo "<th>District</th>";
			echo "<th>First Name</th>";
			echo "<th>Last Name</th>";
			echo "<th>EMail</th>";
			//echo "<th>Troop Only</th>";
			echo "<th>Unit1</th>";
			echo "<th>Unit2</th>";
			echo "</tr>";

			$csv_hdr = "First Name, Last Name, EMail, Unit1 , Unit2";
			$csv_output = "";

			// TODO: Add ypt / Trained Status
			while ($row = $Results->fetch_assoc()) {
				/* Write the data out */
				$Email = "<a href='mailto:" . $row['Email'] . "?subject=Merit Badge Counselor'>" . $row['Email'] . "</a>";
				$LastName = addslashes($row['LastName']);
				echo "<tr><td>" .
					$row['HomeDistrict'] . "</td><td>" .
					$row['FirstName'] . "</td><td>" .
					$LastName . "</td><td>" .
					$Email . "</td><td>" .
					//$row['TroopOnly'] . "</td><td>" .
					$row['Unit1'] . "</td><td>" .
					$row['Unit2'];

				$csv_output .= $row['HomeDistrict'] . ", ";
				$csv_output .= $row['FirstName'] . ", ";
				$csv_output .= $row['LastName'] . ", ";
				$csv_output .= $row['Email'] . ", ";
				//$csv_output .= $row['TroopOnly'] . ", ";
				$csv_output .= $row['Unit1'] . ", ";
				$csv_output .= $row['Unit2'] . "\n";
			}
			echo "</table>";
			?>

				<br />
				<center>
					<form name="export" action="../export.php" method="post">
						<input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
						<input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
						<input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
					</form>
				</center>
				<br />
			<?php
		}

		/******************************************************************************
		 * 
		 * 
		 * 
		 *****************************************************************************/
		function ReportMB15($Results)
		{

			echo "<table class='table table-striped' style='width:60vw'>";
			echo "<td style='width:100px'>";
			echo "<td style='width:150px'>";
			echo "<td style='width:150px'>";
			echo "<td style='width:200px'>";
			echo "<td style='width:100px'>";
			echo "<td style='width:100px'>";
			echo "<td style='width:100px'>";
			echo "<tr>";
			echo "<th>Unit</th>";
			echo "<th>First Name</th>";
			echo "<th>Last Name</th>";
			echo "<th>Email</th>";
			echo "<th>Member ID</th>";
			echo "<th>Validation Date</th>";
			echo "<th>Active</th>";
			echo "<th>Number of Badges</th>";
			echo "</tr>";

			echo "Counselors with More than 15 Merit Badges :" . mysqli_num_rows($Results);

			$csv_hdr = "First Name, Last Name, Email, Member ID, Validation Date, Active, Number of Badges";
			$csv_output = "";

			$NumberofBadges = 16;

			while ($row = $Results->fetch_assoc()) {
				$sqlGetCount = sprintf(
					"SELECT * FROM counselormerit WHERE counselormerit.FirstName = '%s' AND counselormerit.LastName = '%s' AND `Status`<>'DROP'",
					$row['FirstName'],
					$row['LastName']
				);
				$ResultCount = parent::doQuery($sqlGetCount);
				$NumberofBadges = mysqli_num_rows($ResultCount);

				$Email = "<a href='mailto:" . $row['Email'] . "?subject=Merit Badge Counselor'>" . $row['Email'] . "</a>";
				echo "<tr><td>" .
					$row['Unit1'] . "</td><td>" .
					$row['FirstName'] . "</td><td>" .
					$row['LastName'] . "</td><td>" .
					$Email . "</td><td>" .
					$row['MemberID'] . "</td><td>" .
					$row['ValidationDate'] . "</td><td>" .
					$row['Active'] . "</td><td>" .
					$NumberofBadges . "</td><td>";

				$csv_output .= $row['FirstName'] . ", ";
				$csv_output .= $row['LastName'] . ", ";
				$csv_output .= $row['Email'] . ", ";
				$csv_output .= $row['MemberID'] . ", ";
				$csv_output .= $row['ValidationDate'] . ", ";
				$csv_output .= $row['Active'] . ", ";
				$csv_output .= $NumberofBadges . "\n";
			}
			echo "</table>";

			?>

				<br />
				<center>
					<form name="export" action="../export.php" method="post">
						<input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
						<input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
						<input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
					</form>
				</center>
				<br />
			<?php
		}

		/*=============================================================================
	 *
	 * This function will 
	 * 
	 *===========================================================================*/
		function ReportMBCperMB()
		{
			$Debug = false;

			echo "<table class='table table-striped' style='width:370px'>";
			echo "<td style='width:220px'>";
			echo "<td style='width:150px'>";
			echo "<tr>";
			echo "<th>Merit Badge</th>";
			echo "<th>Number of Counselors</th>";
			echo "</tr>";

			$sqlMeritBadge = "SELECT * FROM meritbadges WHERE `Current`='1' ORDER BY MeritName";
			if ($ResultMerit = parent::doQuery($sqlMeritBadge, MYSQLI_STORE_RESULT)) {
				while ($row = $ResultMerit->fetch_assoc()) {
					$MeritName = $row['MeritName'];

					$sqlGetCount = sprintf("SELECT * FROM counselors INNER JOIN counselormerit 
					ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
					WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP' AND counselormerit.MeritName = '%s'", $MeritName);

					if ($ResultCnt = parent::doQuery($sqlGetCount)) {
						$NumberofBadges = mysqli_num_rows($ResultCnt);
						echo "<tr><td>" .
							$MeritName . "</td><td>" .
							$NumberofBadges . "</td><td>";
					} else echo "Error: Get number of rows " . $sqlGetCount . "<br/>";
				}
				echo "</table>";
			}
		}

		/*=============================================================================
	 *
	 * This function will provide a list of merit badge which the district has 
	 * no Counselors for.
	 * 
	 *===========================================================================*/
		function ReportMBNoMBC()
		{
			$Debug = false;

			echo "<table class='table table-striped' style='width:370px'>";
			echo "<td style='width:220px'>";
			echo "<td style='width:150px'>";
			echo "<tr>";
			echo "<th>Merit Badge</th>";
			echo "<th>Number of Counselors</th>";
			echo "</tr>";

			$sqlMeritBadge = "SELECT * FROM meritbadges WHERE `Current`='1' ORDER BY MeritName";
			if ($ResultMerit = parent::doQuery($sqlMeritBadge, MYSQLI_STORE_RESULT)) {
				while ($row = $ResultMerit->fetch_assoc()) {
					$MeritName = $row['MeritName'];

					$sqlGetCount = sprintf("SELECT * FROM counselors INNER JOIN counselormerit 
				ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
				WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP' AND counselormerit.MeritName = '%s'", $MeritName);

					if ($Debug && !strcmp($MeritName, "Pottery"))  echo $sqlGetCount . "<br/>";
					$ResultCnt = parent::doQuery($sqlGetCount);
					$NumberofBadges = mysqli_num_rows($ResultCnt);
					if ($NumberofBadges == 0) {
						echo "<tr><td>" .
							$MeritName . "</td><td>" .
							$NumberofBadges . "</td><td>";
					}
				}
				echo "</table>";
			}
		}
		/*=============================================================================
	 *
	 * This function report the Counselors that have no Merit Badges
	 * 
	 *===========================================================================*/
		function ReportMBCnoMB()
		{
			$Debug = false;

			echo "<table class='table table-striped' style='width:370px'>";
			echo "<td style='width:220px'>";
			echo "<td style='width:150px'>";
			echo "<td style='width:150px'>";
			echo "<tr>";
			echo "<th>Last Name</th>";
			echo "<th>First Name</th>";
			echo "<th>Validation Date</th>";
			echo "</tr>";


			// First get a list of all Cousnelors
			$sqlCounselors = "SELECT * FROM counselors WHERE `Active`='YES' ORDER BY LastName";
			if ($ResultCounselors = parent::doQuery($sqlCounselors, MYSQLI_STORE_RESULT)) {
				while ($row = $ResultCounselors->fetch_assoc()) {
					$CounselorsLast = addslashes($row['LastName']);
					$CounselorsFirst = $row['FirstName'];
					$CounselorsDate = $row['ValidationDate'];

					$sqlGetCount = sprintf(
						"SELECT * FROM counselormerit 
					WHERE counselormerit.Status <> 'DROP' AND counselormerit.LastName = '%s' AND counselormerit.FirstName = '%s'",
						$CounselorsLast,
						$CounselorsFirst
					);

					if ($Debug)  echo $sqlGetCount . "<br/>";
					$ResultCnt = parent::doQuery($sqlGetCount);
					$NumberofBadges = mysqli_num_rows($ResultCnt);
					if ($NumberofBadges == 0) {
						echo "<tr><td>" .
							$CounselorsLast . "</td><td>" .
							$CounselorsFirst . "</td><td>" .
							$CounselorsDate . "</td><td>";
					}
				}
				echo "</table>";
			}
			// Then see if they appear in the Counselormerit table

			// If not report them

		}
		/*=============================================================================
		 *
		 * This function report the Counselors contact information
		 * 
		 *===========================================================================*/
		function ReportCounselorsInfo()
		{
			$Debug = false;
			echo "<center>";
			//echo "<table class='table' style='width:370px'>";
			echo "<table class='table table-striped'>";
			echo "<td style='width:140px'>"; //District
			echo "<td style='width:75px'>";  //Unit
			echo "<td style='width:170px'>"; // Last Name
			echo "<td style='width:100px'>"; //First Name
			echo "<td style='width:240px'>"; // Street
			echo "<td style='width:170px'>"; // City
			echo "<td style='width:50px'>"; // State
			echo "<td style='width:100px'>"; // Zip
			echo "<td style='width:150px'>"; // Phone
			echo "<td style='width:100px'>"; // Email
			echo "<td style='width:100px'>"; // Validation Date
			echo "<td style='width:50px'>"; // Validation Date
			echo "<tr>";
			echo "<th>District</th>";
			echo "<th>Unit</th>";
			echo "<th>Last Name</th>";
			echo "<th>First Name</th>";
			echo "<th>Street Address</th>";
			echo "<th>City</th>";
			echo "<th>State</th>";
			echo "<th>Zip</th>";
			echo "<th>Phone</th>";
			echo "<th>Email</th>";
			echo "<th>Validation Date</th>";
			echo "<th>Active</th>";
			echo "</tr>";

			$csv_hdr = "Unit, First Name, Last Name, Street Address, City, State, Zip, Phone, Email, Last Updated, Active";
			$csv_output = "";

			// First get a list of all Cousnelors
			$sqlCounselors = "SELECT * FROM counselors ORDER BY LastName";
			if ($ResultCounselors = parent::doQuery($sqlCounselors, MYSQLI_STORE_RESULT)) {
				$CounselorCount = mysqli_num_rows($ResultCounselors);

				while ($row = $ResultCounselors->fetch_assoc()) {
					$CounselorsLast = addslashes($row['LastName']);
					$CounselorsFirst = $row['FirstName'];
					$CounselorsDate = $row['ValidationDate'];
					$Phone = parent::formatPhoneNumber(NULL, $row['HomePhone']);
					$Email = parent::formatEmail($row['Email']);
					$ZipCode = $row['Zip'];
					parent::formatZipCode($ZipCode);

					echo "<tr><td>" .
						$row['HomeDistrict'] . "</td><td>" .
						$row['Unit1'] . "</td><td>" .
						$CounselorsLast . "</td><td>" .
						$CounselorsFirst . "</td><td>" .
						$row['Address'] . "</td><td>" .
						$row['City'] . "</td><td>" .
						$row['State'] . "</td><td>" .
						$ZipCode . "</td><td>" .
						$Phone . "</td><td>" .
						$Email . "</td><td>" .
						$CounselorsDate . "</td><td>" .
						$row['Active'] . "</td>";

					$csv_output .= $row['Unit1'] . ", ";
					$csv_output .= $CounselorsLast . ", ";
					$csv_output .= $CounselorsFirst . ", ";
					$csv_output .= $row['Address'] . ", ";
					$csv_output .= $row['City']  . ", ";
					$csv_output .= $row['State'] . ", ";
					$csv_output .= $ZipCode . ", ";
					$csv_output .= $Phone . ", ";
					$csv_output .= $row['Email'] . ", ";
					$csv_output .= $CounselorsDate . ", ";
					$csv_output .= $row['Active'] . "\n";
				}
			}
			echo "</table>";

			echo "<br/>Counselors count: " . $CounselorCount . "<br/>";
			?>
				<br />
				<center>
					<form name="export" action="../export.php" method="post">
						<input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
						<input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
						<input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
					</form>
				</center>
				<br />
			<?php
			echo "</center>";
		}
		/*=============================================================================
		 *
		 * This function reports the merit badges offered at past colleges
		 * 
		 *===========================================================================*/
		function ReportCollegeHistory()
		{
			echo "<center>";
			echo "<table class='table table-striped' style='width:30vw'>";
			echo "<td style='width:140px'>"; //College
			echo "<td style='width:240px'>";  //Badge
			echo "<tr>";
			echo "<th>College</th>";
			echo "<th>Merit Badge</th>";
			echo "</tr>";

			$csv_hdr = "College Year, Merit Badge";
			$csv_output = "";


			// First get the years available from the database
			$sqlColleges = "SELECT DISTINCT `College` from college_registration ORDER By `College` ASC";
			if ($ResultColleges = parent::doQuery($sqlColleges)) {
				while ($rowColleges = $ResultColleges->fetch_assoc()) {
					$College = $rowColleges['College'];
					$sqlMeritBadge = "SELECT DISTINCT `MeritBadge` FROM college_registration WHERE `College`='$College' ORDER By `MeritBadge` ASC";
					if ($ResultMeritBadge = parent::doQuery($sqlMeritBadge)) {
						while ($rowMeritBadge = $ResultMeritBadge->fetch_assoc()) {
							echo "<tr><td>" .
								$rowColleges['College'] . "</td><td>" .
								$rowMeritBadge['MeritBadge'] . "</td>";

							$csv_output .= $rowColleges['College'] . ", ";
							$csv_output .= $rowMeritBadge['MeritBadge'] . "\n";
						}
					}
				}
				echo "</table>";
			}
			?>
				<br />
				<center>
					<form name="export" action="../export.php" method="post">
						<input class='btn btn-primary btn-sm' style="width:220px" type="submit" value="Export table to CSV">
						<input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
						<input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
					</form>
				</center>
				<br />
		<?php
			echo "</center>";
		}
	}
