<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}
load_class(SHARED_CLASS_PATH . '/CMeritBadges.php');
//include_once('cAdultLeaders.php');

class CReports extends CMeritBadges
{
  // Template loader function
  public static function load_template($file)
  {
    $path = BASE_PATH . $file;
    if (file_exists($path)) {
      require_once $path;
    } else {
      error_log("Template $file is missing.");
      die('An error occurred. Please try again later.');
    }
  }

  /*=============================================================================
     *
     * This function will produce a list of merit badge to counselors 
     * if the flag All is set it will include counselors with the DoNotPublish flag.
     * 
     *===========================================================================*/
  public static function ReportMeritBadges($All)
  {
    $MeritBadge = "";
    $SpecialTraining = "";
    $Phone = "";
    $CounselorCount = 0;

    if (!$All)
      $sqlByMBCount = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit 
    			ON meritbadges.MeritName = counselormerit.MeritName)
    			ON (counselors.FirstName = counselormerit.FirstName) AND(counselors.LastName = counselormerit.LastName)
    			WHERE counselors.Active = 'Yes' AND counselors.DoNotPublish = 'No' 
    			AND counselormerit.Status <> 'DROP' AND counselormerit.MeritName = ";
    else
      $sqlByMBCount = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
    		ON (counselors.FirstName = counselormerit.FirstName) AND(counselors.LastName = counselormerit.LastName) 
    		WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP' AND counselormerit.MeritName = ";

?>
    <div class="container-fluid">
      <div class="row flex-nowrap">
        <?php //self::load_template('sidebar.php'); 
        ?>
        <main id="main-content" class="col-11 py-3" role="main">

          <?php if (!$All) { ?>
            <div class="text-center">
              <h2>This report will contain all active Merit Badge Counselors in the Centennial District.</h2>
            </div>
          <?php } else { ?>
            <div class='alert alert-danger text-center' role='alert'>
              This report will contain all active Merit Badge Counselors
              in the Centennial District. This list include Counselor that DO NOT wish
              to have their information published. <p style='color:red;'><b>THIS LIST WILL NOT TO BE PUBLISHED</b></p>
            </div>
          <?php } ?>


          <div class="d-flex justify-content-center align-items-center">
            <table>
              <tr>
                <th style='width:250px'>TBD</th>
                <th style='width:250px'>Richard Hall</th>
              </tr>
              <tr>
                <td>Merit Badge College Dean</td>
                <td>District Advancement Chair</td>
              </tr>
              <tr>
                <td><a href='mailto:TDB?subject=Merit Badge Counselor'>TDB</a></td>
                <td><a href='mailto:richard.hall@centennialdistrict.co?subject=Merit Badge Counselor'>richard.hall@centennialdistrict.co</a></td>
              </tr>
              <tr>
                <td>TDB</td>
                <td>720.324.4235</td>
              </tr>
            </table>
          </div>


          <?php
          $TodaysDate = strtotime("now");

          $queryByMB = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		ON(counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
		WHERE counselormerit.Status <> 'DROP' AND counselors.Active = 'Yes' AND counselors.DoNotPublish = 'No'
		ORDER BY
			counselormerit.MeritName,
			counselors.LastName,
			counselors.FirstName;";

          $report_results = self::doQuery($queryByMB);

          if (!$report_results) {
            // Report Error
            error_log("ERROR: ReportMeritBadges() : " . $queryByMB);
            exit();
          }
          while ($row = $report_results->fetch_assoc()) {

            //$Phone = formatPhoneNumber($row, NULL);
            $Phone = self::formatPhoneNumber($row, null);
            $ZipCode = $row['Zip'];
            self::formatZipCode($ZipCode);

            $Email = self::formatEmail($row['Email']);

            $Expired = false;
            $Counselorsypt = strtotime($row['YPT']);
            if ($TodaysDate > $Counselorsypt) {
              $Expired = true;
            }
            if ($Expired)
              $FormatterYPT = "<b style='color:red;'>";
            else
              $FormatterYPT = "";

            $Trained = AdultLeaders::IsTrained($row['FirstName'], addslashes($row['LastName']), "Merit Badge Counselor");
            if (!strcmp($Trained, "YES") || !strcmp($Trained, "Yes")) {
              $FormatterTrain = "";
            } else {
              $FormatterTrain = "<b style='color:red;'>";
            }



            if ($MeritBadge != $row['MeritName']) {
              $sql = sprintf("%s '%s'", $sqlByMBCount, $row['MeritName']);
              $CounselorCount = self::MeritQueryRows($sql);
          ?>
              </table>
              <div>
                <?php echo $SpecialTraining; ?>
              </div>
              <br>
              <?php
              //        echo '<pre>';
              //        var_dump($row['Logo'], $row['URL'], $row['RequirementsRevised'], $CounselorCount);
              //        echo '</pre>';
              ?>
              <h2 class="text-center" style='background-color: var(--scouting-paleblue);'>
                <a href="<?php echo !empty($row['URL']) ? htmlspecialchars($row['URL']) : '#'; ?>"><?php echo htmlspecialchars($row['MeritName']); ?> </a>
              </h2>
              <table class="table" style="text-align: center;">
                <tr>
                  <td style="vertical-align: middle;">
                    <a href="<?php echo !empty($row['URL']) ? htmlspecialchars($row['URL']) : '#'; ?>" title="Visit website">
                      <!-- <img src="<?php //echo !empty($row['Logo']) ? htmlspecialchars($row['Logo']) : 'images/default-logo.png'; ?>" alt="Merit Badge logo" style="display: block; object-fit: contain;"> -->
                    </a>
                  </td>
                  <td style="vertical-align: middle;">
                    <p>Requirements: <?php echo isset($row['RequirementsRevised']) ? htmlspecialchars($row['RequirementsRevised']) : 'N/A'; ?> - Counselor Count = <?php echo isset($CounselorCount) ? htmlspecialchars($CounselorCount) : 'N/A'; ?></p>
                  </td>
                </tr>
              </table>
              <?php
              $MeritBadge = $row['MeritName'];
              ?>
              <br>
              <table class='table table-striped'>
                <td style='width:70px'>
                <td style='width:70px'>
                <td style='width:120px'>
                <td style='width:100px'>
                <td style='width:100px'>
                <td style='width:150px'>
                <td style='width:120px'>
                <td style='width:100px'>
                <td style='width:50px'>
                  <tr>
                    <th>Unit1</th>
                    <th>Unit2</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Zip Code</th>
                    <th>Phone</th>
                    <th>EMail</th>
                    <th>YPT Valid Until</th>
                    <th>Trained</th>
                  </tr>
              <?php
            }
            echo "<tr><td>" .
              $row['Unit1'] . "</td><td>" .
              $row['Unit2'] . "</td><td>" .
              $row['FirstName'] . "</td><td>" .
              $row['LastName'] . "</td><td>" .
              $ZipCode . "</td><td>" .
              $Phone . "</td><td>" .
              $Email . "</td><td>" .
              $FormatterYPT . $row['YPT'] . "</td><td>" .
              $FormatterTrain . $Trained . "</td>";

            if (!empty($row['SpecialTraining']))
              $SpecialTraining = "<p class='alert alert-warning' role='alert'>" . $row['SpecialTraining'] . "</p>";
            else
              $SpecialTraining = "";
          }
              ?>
              </table>
        </main>
      </div>
    </div>

  <?php
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function &MeritQueryRows($sql)
  {
    $Debug = false;

    try {
      if ($Results = self::doQuery($sql, MYSQLI_STORE_RESULT)) {
        $rows = mysqli_num_rows($Results);
        if ($Debug) printf("Number of rows: %d", $rows);
      }
    } catch (Exception $e) {
      echo 'Message: ' . $e->getMessage();
    }

    return $rows;
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  /*=============================================================================
     *
     * This function will produce a list of counselors to merit badges
     * 
     *===========================================================================*/
  function ReportCounselors($report_results)
  {
    $MemberID = "";
    $Expired = false;
    $TodaysDate = strtotime("now");


    // TODO: Add ypt / Trained Status
    while ($row = $report_results->fetch_assoc()) {
      /* If we have switch to a new Counselor print out the Header for that Counselor */
      if ($MemberID != $row['MemberID']) {
        echo "</table>";
        echo "</div>";
        echo '<div class="container px-xl-5">';

        echo "<h2 class='text-center' style='background-color: var(--scouting-paleblue);'>", $row['FirstName'], " ", $row['LastName'], "</h2>";
        echo "<p class='text-center'>Number of Badges:", $row['NumOfBadges'], "</p>";
        $Expired = false;
        $Counselorsypt = AdultLeaders::GetYPTByID($row['MemberID']);
        if (isset($Counselorsypt))
          $Counselorsypttime = strtotime($Counselorsypt['Y01_Expires']);
        else
          $Counselorsypt['Y01_Expires'] = "Unknow";
        if ($TodaysDate > $Counselorsypttime) {
          $Expired = true;
        }
        if ($Expired)
          $yptValid = "<p class='text-center' style='color:red; font-weight:bold;'>YPT valid until: " . $Counselorsypt['Y01_Expires'] . "</p>";
        else
          $yptValid = "<p class='text-center' style='color:#212529'>YPT valid until: " . $Counselorsypt['Y01_Expires'] . "</p>";

        $Trained = AdultLeaders::IsTrained($row['FirstName'], addslashes($row['LastName']), "Merit Badge Counselor");
        if (!strcmp($Trained, "YES")) {
          $Trained = "<p class='text-center' style='color:#212529'>Trained: " . $Trained . "</p>";
        } else {
          $Trained = "<p class='text-center'  style='color:red; font-weight:bold;'>Trained: " . $Trained . "</p>";
        }


        //$WorkWith = $row['WillToWorkWith'] ? "Willing to work with: " . $row['WillToWorkWith'] . " Scouts" : "";
        $Phone = $row['HomePhone'];
        $PhoneNum = self::formatPhoneNumber(NULL, $Phone);
        $HomePhone = $row['HomePhone'] ? "<b> Home Phone: </b>" . $PhoneNum : "";

        $Phone = $row['MobilePhone'];
        $PhoneNum = self::formatPhoneNumber(NULL, $Phone);
        $MobilePhone = $row['MobilePhone'] ? "<b> Mobile Phone: </b>" . $PhoneNum : "";

        echo $yptValid . " " . $Trained;
        $Email = self::formatEmail($row['Email']);
        echo "<p class='text-center'>" . $HomePhone . " " . $MobilePhone . "  Email: " . $Email . "</p>";

        $MemberID = $row['MemberID'];

        echo "<table class='table table-striped'>";
        echo "<td style='width:50px'>";
        echo "<td style='width:50px'>";
        echo "<td style='width:30px'>";
        echo "<td style='width:100px'>";
        echo "<tr>";
        echo "<th>Unit1</th>";
        echo "<th>Unit2</th>";
        echo "<th>Eagle</th>";
        echo "<th>Merit Badge</th>";
        echo "</tr>";
      }
      /* Write the data out */
      //$Unit = AdultLeaders::FindMemberUnit($row['FirstName'], addslashes($row['LastName']), "Troop");

      echo "<tr><td>" .
        $row['Unit1'] . "</td><td>" .
        $row['Unit2'] . "</td><td>" .
        $row['Eagle'] . "</td><td>" .
        $row['MeritName'] . "</td>";
    }
    echo "</table>";
  }

  /*=============================================================================
     *
     * This function will produce a list of merit to Troop
     * 
     *===========================================================================*/
  function ReportTroop()
  {
    $sqlByMBCount = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
    	ON (counselors.FirstName = counselormerit.FirstName) AND(counselors.LastName = counselormerit.LastName)
    	WHERE counselors.Active = 'Yes' AND counselors.DoNotPublish = 'No' AND counselormerit.Status <> 'DROP' AND counselormerit.MeritName = ";

    $queryByTroop = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
		WHERE counselors.Active = 'Yes' AND counselormerit.Status <> 'DROP'
		ORDER BY
			counselors.Unit1,
			meritbadges.MeritName,
			counselors.LastName;";

    $report_results = self::doQuery($queryByTroop);

    $Troop = "-1";
    $SpecialTraining = "";
    $Phone = "";

    $TodaysDate = strtotime("now");

    while ($row = $report_results->fetch_assoc()) {

      $Phone = self::formatPhoneNumber($row, NULL);
      $ZipCode = $row['Zip'];
      self::formatZipCode($ZipCode);

      $Email = self::formatEmail($row['Email']);

      $Counselorsypt = AdultLeaders::GetYPTByID($row['MemberID']);
      $Counselorsypttime = strtotime($Counselorsypt['Y01_Expires']);
      $Expired = false;
      if ($TodaysDate > $Counselorsypttime) {
        $Expired = true;
      }
      if ($Expired)
        $FormatterYPT = "<b style='color:red;'>";
      else
        $FormatterYPT = "";


      if ($Troop != $row['Unit1']) {
        $sql = sprintf("%s '%s'", $sqlByMBCount, $row['MeritName']);
        $CounselorCount = self::MeritQueryRows($sql);
        echo "</table>";
        if (!empty($row['Unit1']) && strpos($row['Unit1'], "-NA"))
          echo "<h2 class='text-center' style='background-color: var(--scouting-paleblue);'> Crew ", $row['Unit1'], "</h2>";
        else if (!empty($row['Unit1']))
          echo "<h2 class='text-center' style='background-color: var(--scouting-paleblue);'> Troop ", $row['Unit1'], "</h2>";
        else
          echo "<h2 class='text-center' style='background-color: var(--scouting-paleblue);'> Unknow Unit </h2>";
        $Troop = $row['Unit1'];

        echo "<br>";
        echo "<table class='table table-striped'>";
        echo "<td style='width:50px'>";    // Eagle
        echo "<td style='width:180px'>";  //Merit badge
        echo "<td style='width:110px'>";  //First
        echo "<td style='width:130px'>";  //Last
        echo "<td style='width:110px'>";  //Zip
        echo "<td style='width:170px'>";  //Phone
        echo "<td style='width:100px'>";  //Emal
        echo "<td style='width:170px'>";  //YPT
        echo "<td style='width:210px'>";
        echo "<tr style='width:1024px'>";
        echo "<th>Eagle</th>";
        echo "<th>Merit Badge</th>";
        echo "<th>First Name</th>";
        echo "<th>Last Name</th>";
        echo "<th>Zip Code</th>";
        echo "<th>Phone</th>";
        echo "<th>EMail</th>";
        echo "<th>YPT Valid Until</th>";
        echo "</tr>";
      }
      echo "<tr><td>" .
        $row['Eagle'] . "</td><td>" .
        $row['MeritName'] . "</td><td>" .
        $row['FirstName'] . "</td><td>" .
        $row['LastName'] . "</td><td>" .
        $ZipCode . "</td><td>" .
        $Phone . "</td><td>" .
        $Email . "</td><td>" .
        $FormatterYPT . $Counselorsypt['Y01_Expires'] . "</td>";
    }
    echo "</table>";
  }

  /*=============================================================================
     *
     * This function will 
     * 
     *===========================================================================*/
  function ReportCounselorofMB($MeritBadge)
  {

    $CounslorofMB = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit 
		ON meritbadges.MeritName = counselormerit.MeritName)
		ON (counselors.FirstName = counselormerit.FirstName) AND (counselors.LastName = counselormerit.LastName)
		WHERE
			counselormerit.MeritName LIKE ";


    //Create a sql statement to select chosen Merit Badge
    $sql = sprintf("%s '%s'%s", $CounslorofMB, $MeritBadge, " AND counselors.Active = 'Yes'");
    $result_meritbadges = self::doQuery($sql);
    if (!$result_meritbadges) {
      $ErrorMsg = "ERROR: ReportCounselorofMB(" . $MeritBadge . ") - " . $sql;
      error_log($ErrorMsg, 0);
      exit();
    }

    $SpecialTraining = "";
    $Phone = "";

    $TodaysDate = strtotime("now");
    $csv_hdr = "Badge, Troop, Second Troop, Last Name, First Name, Zip Code, Phone, Email, YPT";
    $csv_output = "";

    $MeritBadge = ""; // This is silly but makes the old code work.

    while ($row = $result_meritbadges->fetch_assoc()) {
      $Phone = self::formatPhoneNumber($row, NULL);
      $ZipCode = $row['Zip'];
      self::formatZipCode($ZipCode);

      $Email = self::formatEmail($row['Email']);

      $Counselorsypt = strtotime($row['YPT']);
      if ($TodaysDate > $Counselorsypt) {
        $FormatterYPT = "<b style='color:red;'>";
      } else {
        $FormatterYPT = "";
      }

      $sqlByMBCount = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
			ON (counselors.FirstName = counselormerit.FirstName) AND(counselors.LastName = counselormerit.LastName)
			WHERE counselors.Active = 'Yes' AND counselors.DoNotPublish = 'No' AND counselormerit.Status <> 'DROP' AND counselormerit.MeritName = ";

      $sqlMBCnt = sprintf("%s '%s'", $sqlByMBCount, $MeritBadge);

      if (!empty($row['SpecialTraining']))
        $SpecialTraining = "<p class='alert alert-warning' role='alert'>" . $row['SpecialTraining'] . "</p>";
      else
        $SpecialTraining = "";

      if ($MeritBadge != $row['MeritName']) {
        //$sql = sprintf("%s '%s'", $sqlByMBCount, $row['MeritName']);
        //echo $sql."<br/>";


        $CounselorCount = self::MeritQueryRows($sqlMBCnt);
        ?>
        </table>
        <div class='a' style='width:1200px'>
      </php echo $SpecialTraining; ?>
        </dev>
        <br>
        <?php echo "<h2 class='text-center' style='background-color: var(--scouting-paleblue);'>", $row['MeritName'], "</h2>", "Requirments: ", $row['RequirementsRevised'],
        " - Counselor Count =", $CounselorCount, "<a href='" . $row['URL'] . "'>" .  "'</a>";
        $MeritBadge = $row['MeritName']; ?>
        <br>
        <table class='table table-striped'>
        <tr>
        <th>Troop</th>
        <th>#</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Zip Code</th>
        <th>Phone</th>
        <th>EMail</th>
        <th>YPT Valid Until</th>
        </tr>
        <?php
      }

      echo "<tr><td>" .
        $row['Unit1'] . "</td><td>" .
        $row['Unit2'] . "</td><td>" .
        $row['LastName'] . "</td><td>" .
        $row['FirstName'] . "</td><td>" .
        $ZipCode . "</td><td>" .
        $Phone . "</td><td>" .
        $Email . "</td><td>" .
        $FormatterYPT . $row['YPT'] . "</td>";


      $csv_output .= $row['MeritName'] . ", ";
      $csv_output .= $row['Unit1'] . ", ";
      $csv_output .= $row['Unit2'] . ", ";
      $csv_output .= $row['LastName'] . ", ";
      $csv_output .= $row['FirstName'] . ", ";
      $csv_output .= $ZipCode . ", ";
      $csv_output .= $Phone . ", ";
      $csv_output .= $row['Email'] . ", ";
      $csv_output .= $row['YPT'] . "\n";
    }
    echo "</table>";
  ?>

    <br />
    <center>
      <form name="export" action="../export.php" method="post">
        <input class='btn btn-primary btm-sm' style="width:220px" type="submit" value="Export table to CSV">
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
  function ReportofSelectedTroop($SelectedTroop)
  {
    $sqlQuery = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		ON (counselors.LastName = counselormerit.LastName) AND (counselors.FirstName = counselormerit.FirstName)
		WHERE
			counselors.Unit1 LIKE";

    $sqlByMBCount = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
    	ON (counselors.FirstName = counselormerit.FirstName) AND(counselors.LastName = counselormerit.LastName)
    	WHERE counselors.Active = 'Yes' counselormerit.Status <> 'DROP' AND counselormerit.MeritName = ";

    //Create a sql statement to select chosen Merit Badge
    $sql = sprintf("%s '%s'%s", $sqlQuery, $SelectedTroop, " AND counselors.Active = 'Yes' ORDER BY counselormerit.MeritName");
    //$sql = sprintf( "%s '%s'", $sqlQuery, $SelectedTroop);
    $result_meritbadges = self::doQuery($sql, MYSQLI_STORE_RESULT);

    $MeritBadge = "";
    $SpecialTraining = "";
    $Phone = "";

    $TodaysDate = strtotime("now");

    while ($row = $result_meritbadges->fetch_assoc()) {
      $Phone = self::formatPhoneNumber($row, NULL);
      $ZipCode = $row['Zip'];
      self::formatZipCode($ZipCode);

      $Email = self::formatEmail($row['Email']);

      $Counselorsypt = strtotime($row['YPT']);
      if ($TodaysDate > $Counselorsypt) {
        $FormatterYPT = "<b style='color:red;'>";
      } else {
        $FormatterYPT = "";
      }

      if ($MeritBadge != $row['MeritName']) {
        $sql = sprintf(
          "%s '%s' AND counselormerit.MeritName = '%s'",
          $sqlQuery,
          $SelectedTroop,
          $row['MeritName']
        );
        $CounselorCount = self::MeritQueryRows($sql);
        echo "</table class='table'>";
        echo "<div class='a' style='width:1200px'>";
        echo $SpecialTraining;
        echo "</div>";
        echo "<br>";
        echo "<div class='text-center'><h2 style='background-color: var(--scouting-paleblue);'>", $row['MeritName'], "</h2>", "Requirments: ", $row['RequirementsRevised'],
        " - Counselor Count =", $CounselorCount, "<a href='" . $row['URL'] . "'></a></div>";
        $MeritBadge = $row['MeritName'];
        echo "<br>";
        echo "<table class='table table-striped'>";
        echo "<td style='width:70px'>";
        echo "<td style='width:70px'>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<td style='width:100px'>";
        echo "<td style='width:130px'>";
        echo "<td style='width:120px'>";
        echo "<td style='width:120px'>";
        echo "<tr>";
        echo "<th>Troop</th>";
        echo "<th>#</th>";
        echo "<th>Last Name</th>";
        echo "<th>First Name</th>";
        echo "<th>Zip Code</th>";
        echo "<th>Phone</th>";
        echo "<th>EMail</th>";
        echo "<th>YPT Valid Until</th>";
        echo "</tr>";
      }
      echo "<tr><td>" .
        $row['Unit1'] . "</td><td>" .
        $row['Unit2'] . "</td><td>" .
        $row['LastName'] . "</td><td>" .
        $row['FirstName'] . "</td><td>" .
        $ZipCode . "</td><td>" .
        $Phone . "</td><td>" .
        $Email . "</td><td>" .
        $FormatterYPT . $row['YPT'];
      $SpecialTraining = $row['SpecialTraining'];
    }
    echo "</table>";
    echo $SpecialTraining;

  ?>
  <?php

  }

  /******************************************************************************
   * 
   * This function will list out the data for a single Counselor by Member ID
   * 
   *****************************************************************************/
  function ReportofSelectedCounselor($SelectedCounselor)
  {
  ?>
    <div class="container-fluid">
      <?php
      $sqlQuery = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		ON (counselors.LastName = counselormerit.LastName) AND (counselors.FirstName = counselormerit.FirstName)
		WHERE
			counselors.MemberID LIKE";


      $Expired = false;
      $TodaysDate = strtotime("now");


      $Fname = "";
      $Lname = "";


      //Create a sql statement to select chosen Merit Badge
      $sql = sprintf("%s '%s'%s", $sqlQuery, $SelectedCounselor, " AND counselors.Active = 'Yes' ORDER BY counselormerit.MeritName");
      //$sql = sprintf( "%s '%s'", $sqlQuery, $SelectedTroop);
      $ResultCounselor = self::doQuery($sql);
      $MBCount = mysqli_num_rows($ResultCounselor);
      if ($MBCount == 0) {
        echo "Merit Badge Counselor has No (0) Merit Badges<br/>";
      }

      // TODO: Add ypt / Trained Status
      while ($row = $ResultCounselor->fetch_assoc()) {
        /* If we have switch to a new Counselor print out the Header for that Counselor */
        if (($Fname != $row['FirstName']) && ($Lname != $row['LastName'])) {
          echo "</table>";
          echo "<br>";
          echo "<h2 class='text-center' style='background-color: var(--scouting-paleblue);'>", $row['FirstName'], " ", $row['LastName'], "</h2>";
          if ($row['NumOfBadges'] > 15)
            $strNumBadges = "<span style='color:red; font-weight:bold;'>Number of Badges: " . $row['NumOfBadges'] . "</span><br/>";
          else
            $strNumBadges = "<span style='color:#212529'>Number of Badges: " . $row['NumOfBadges'] . "</span><br/>";
          echo $strNumBadges;
          $Expired = false;

          $Counselorsypt = AdultLeaders::GetYPTByID($row['MemberID']);
          $Counselorsypttime = strtotime($Counselorsypt['Y01_Expires']);
          if ($TodaysDate > $Counselorsypttime) {
            $Expired = true;
          }
          if ($Expired)
            $yptValid = "<span style='color:red; font-weight:bold;'>YPT valid until: " . $Counselorsypt['Y01_Expires'] . "</span><br/>";
          else
            $yptValid = "<span style='color:#212529'>YPT valid until: " . $Counselorsypt['Y01_Expires'] . "</span><br/>";

          $Trained = AdultLeaders::IsTrained($row['FirstName'], addslashes($row['LastName']), "Merit Badge Counselor");
          if (!strcmp($Trained, "YES")) {
            $Trained = "<span style='color:#212529'>Trained: " . $Trained . "</span><br/>";
          } else {
            $Trained = "<span style='color:red; font-weight:bold;'>Trained: " . $Trained . "</span><br/>";
          }


          //$WorkWith = $row['WillToWorkWith'] ? "Willing to work with: " . $row['WillToWorkWith'] . " Scouts" : "";
          $Phone = $row['HomePhone'];
          $Phone = self::formatPhoneNumber(null, $Phone);
          $HomePhone = $row['HomePhone'] ? "<b> Home Phone: </b>" . $Phone : "";

          $Phone = $row['MobilePhone'];
          $Phone = self::formatPhoneNumber(null, $Phone);
          $MobilePhone = $row['MobilePhone'] ? "<b> Mobile Phone: </b>" . $Phone : "";

          echo $yptValid . " " . $Trained;
          $Email = self::formatEmail($row['Email']);
          //echo $WorkWith, "<br>", $HomePhone, $MobilePhone, "<b> Email: </b>", $Email;
          echo $HomePhone, $MobilePhone, "<b> Email: </b>", $Email;

          $Fname = $row['FirstName'];
          $Lname = $row['LastName'];

          echo "<table class='table table-striped'>";
          // echo "<td style='width:30px'>";
          // echo "<td style='width:30px'>";
          // echo "<td style='width:50px'>";
          // echo "<td style='width:200px'>";
          // echo "<td style='width:100px'>";
          // echo "<td style='width:100px'>";
          echo "<tr>";
          echo "<th>Unikt1</th>";
          echo "<th>Unit2</th>";
          echo "<th>Eagle</th>";
          echo "<th>Merit Badge</th>";
          echo "<th>Status</th>";
          echo "<th>Status Date</th>";
          echo "</tr>";
        }
        /* Write the data out */
        echo "<tr><td>" .
          $row['Unit1'] . "</td><td>" .
          $row['Unit2'] . "</td><td>" .
          $row['Eagle'] . "</td><td>" .
          $row['MeritName'] . "</td><td>" .
          $row['Status'] . "</td><td>" .
          $row['StatusDate'] . "</td>";
      }
      ?>
      </table><br /><br />
    </div>


  <?php

  }
  /*=============================================================================
     *
     * This function will 
     * 
     *===========================================================================*/
  function ReportFullSelectedTroop($SelectedTroop)
  {
    $sqlQuery = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
		ON (counselors.LastName = counselormerit.LastName) AND (counselors.FirstName = counselormerit.FirstName)
		WHERE
			counselors.Unit1 LIKE";


    echo "<table class='table table-striped'>";
    echo "<td style='width:50px'>";
    echo "<td style='width:150px'>";
    echo "<td style='width:50px'>";
    echo "<td style='width:100px'>";
    echo "<td style='width:100px'>";
    echo "<tr>";
    echo "<th>Troop</th>";
    echo "<th>Merit Badge</th>";
    echo "<th>Eagle</th>";
    echo "<th>Last Name</th>";
    echo "<th>First Name</th>";
    echo "</tr>";

    $csv_hdr = "Troop, Merit Badge, Eagle, Last Name, First Name";
    $csv_output = "";


    $Formatter = "<b style='color:red;'>";
    // First create SQL to get a list of all active merit badges
    $sqlMB = "SELECT * FROM `meritbadges` WHERE `Current`='1'";
    $ResultMB = self::doQuery($sqlMB);

    while ($RowMB = $ResultMB->fetch_assoc()) {
      // Now find any counselor in Selected Troop for this Badge.
      $sqlCounselor = "SELECT * FROM counselors INNER JOIN(meritbadges INNER JOIN counselormerit ON meritbadges.MeritName = counselormerit.MeritName)
				ON (counselors.FirstName = counselormerit.FirstName) AND(counselors.LastName = counselormerit.LastName)
				WHERE counselors.Unit1='$SelectedTroop' AND counselors.Active='Yes' AND meritbadges.MeritName='$RowMB[MeritName]'";

      $ResultCounselor = self::doQuery($sqlCounselor);
      if (!$ResultCounselor) {
        $strErrMsg = "ERROR: ReportFullSelectedTroop(" . $SelectedTroop . ") - " . $sqlCounselor;
        error_log($strErrMsg);
        exit();
      }
      while ($RowCounselor = $ResultCounselor->fetch_assoc()) {
        $MBLink = "<a href='" . $RowCounselor['URL'] . "'>" . $RowCounselor['MeritName'] . "</a>";
        echo "<tr><td>" .
          $RowCounselor['Unit1'] . "</td><td>" .
          $MBLink . "</td><td>" .
          $RowCounselor['Eagle'] . "</td><td>" .
          $RowCounselor['LastName'] . "</td><td>" .
          $RowCounselor['FirstName'] . "</td></tr>";

        $csv_output .= $RowCounselor['Unit1'] . ", ";
        $csv_output .= $RowCounselor['MeritName'] . " , ";
        $csv_output .= $RowCounselor['Eagle'] . ",";
        $csv_output .= $RowCounselor['LastName'] . ", ";
        $csv_output .= $RowCounselor['FirstName'] . "\n";
      }
      if ($ResultCounselor->num_rows == 0) {
        $MBLink = "<a href='" . $RowMB['URL'] . "'>" . $RowMB['MeritName'] . "</a>";
        echo "<tr><td>" .
          $Formatter . $SelectedTroop . "</td><td>" .
          $MBLink . "</td><td>" .
          $Formatter . $RowMB['Eagle'] . "</td><td>" .
          $Formatter . "None" . "</td><td>" .
          $Formatter . "None" . "</td></tr>";

        $csv_output .= $SelectedTroop . ", ";
        $csv_output .= $RowMB['MeritName'] . " , ";
        $csv_output .= $RowMB['Eagle'] . ",";
        $csv_output .= "None" . ", ";
        $csv_output .= "None" . "\n";
      }
    }

    echo "</table>";
  ?>

    <br />
    <center>
      <form name="export" action="../export.php" method="post">
        <input class='btn btn-primary btm-sm' style="width:220px" type="submit" value="Export table to CSV">
        <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
        <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
      </form>
    </center>
    <br />
    <?php

    ?>
<?php

  }
}
