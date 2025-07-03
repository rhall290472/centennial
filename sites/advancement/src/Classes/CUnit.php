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

include_once('CAdvancement.php');
/******************************************************************************
 * ****************************************************************************
 * ****************************************************************************
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 * ****************************************************************************
 * ****************************************************************************
 */
class UNIT extends CAdvancement
{

  public static function GetUnits()
  {
    $qryunit = "SELECT DISTINCT Unit FROM membershiptotals WHERE Expire_Date LIKE '%";

    $CurrentYear = parent::GetYear();
    $NextYear = $CurrentYear + 1;
    $qry = $qryunit . $CurrentYear . "%' OR Expire_Date LIKE '%$NextYear%' ORDER BY UNIT";
    $resultunit = parent::doQuery($qry);
    return $resultunit;
  }

  public static function GetPackUnits()
  {
    $qryunit = "SELECT DISTINCT Unit FROM membershiptotals WHERE Unit Like '%Pack%' AND (Expire_Date LIKE '%";

    $CurrentYear = parent::GetYear();
    $NextYear = $CurrentYear + 1;
    $qry = $qryunit . $CurrentYear . "%' OR Expire_Date LIKE '%$NextYear%')";
    $resultunit = parent::doQuery($qry);
    return $resultunit;
  }

  public static function GetTroopUnits()
  {
    $qryunit = "SELECT DISTINCT Unit FROM membershiptotals WHERE Unit Like '%Troop%' AND (Expire_Date LIKE '%";

    $CurrentYear = parent::GetYear();
    $NextYear = $CurrentYear + 1;
    $qry = $qryunit . $CurrentYear . "%' OR Expire_Date LIKE '%$NextYear%')";
    $resultunit = parent::doQuery($qry);
    return $resultunit;
  }

  public static function DispayTable($rowcount, $result)
  {
?>
    <table style="width:1050px" class="tl1 tl2 tl3 tl4 tl5 tc6 tc7 tc8">
      <td style="width:140px">
      <td style="width:100px">
      <td style="width:100px">
      <td style="width:75px">
      <td style="width:230px">
      <td style="width:230px">
      <td style="width:50px">
      <td style="width:75px">
      <td style="width:50px">
        <tr>
          <th>Unit</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Member ID</th>
          <th>Position</th>
          <th>Functional Role</th>
          <th>Direct Contact</th>
          <th>Trained</th>
          <th>YPT</th>
        </tr>

        <?php
        if ($rowcount > 0) {
          while ($row = $result->fetch_assoc()) {
            $sql = sprintf('SELECT * FROM ypt Where Member_ID = "%s"', $row["MemberID"]);
            $result_ypt = parent::doQuery($sql);
            $row_ypt = $result_ypt->fetch_assoc();
            //echo $sql;
            //Check Trained status and if Not trained link to training record.
            $Trained = $row["Trained"];
            $LastName = $row["MemberID"];
            if (!strcmp($Trained, "NO")) {
              $TrainedURL = "<a href='Untrained.php?btn=MemberID&SortBy=MemberID&MemberID=$LastName'";
              $Trained = sprintf("%s%s>%s</a>", $TrainedURL, $Trained, $Trained);
            }
            $ExpiredYPT = $row_ypt["Status"];
            if (!strcmp($ExpiredYPT, "NO")) {
              $YPTURL = "<a href='YPT.php?btn=ByLastName&SortBy=Last_Name&last_name=$LastName'";
              $ExpiredYPT = sprintf("%s%s>%s</a>", $YPTURL, $ExpiredYPT, $ExpiredYPT);
            }
            echo "<tr><td>" .
              $row["Unit"] . "</td><td>" .
              $row["First_Name"] . "</td><td>" .
              $row["Last_Name"] . "</td><td>" .
              $row["MemberID"] . "</td><td>" .
              $row["Position"] . "</td><td>" .
              $row["FunctionalRole"] . "</td><td>" .
              $row["Direct_Contact_Leader"] . "</td><td>" .
              $Trained . "</td><td>" .
              $ExpiredYPT . "</td></tr>";
          }
        } else {
          echo "0 result";
        }
        echo "</table>";
        if ($rowcount > 0)
          mysqli_free_result($result);
        echo "</br>";
      }
      /******************************************************************************
       * 
       * This function will read in the UnitBasedmembershiptotalsReport.csv file
       * downloaded from my.scouting and insert/update the table membershiptotals.
       * 
       * This function will only be used for current year
       * 
       *****************************************************************************/
      public static function DisplayMembershipTable()
      {
        $SelectedYear = parent::GetYear();
        $CurrentYear = date("Y");
        if ($SelectedYear != $CurrentYear) {
          self::DisplayPreviousMembershipTable();
          return;
        }
        ?>

        <div class="col-12 px-5">
          <table class="table table-striped">
            <td style="width:300px"> <!-- Chartered Org -->
            <td style="width:120px"> <!-- Unit -->
            <td style="width:50px"> <!-- Total -->
            <td style="width:50px"> <!-- Adult Total -->
            <td style="width:100px"> <!-- Expire Date -->
            <td style="width:100px"> <!-- Last Contact -->
            <td style="width:50px"> <!-- Last Score -->
            <td style="width:250px"> <!-- Last Score -->
              <tr>
                <th>Chartered Org</th>
                <th>Unit</th>
                <th>Youth</th>
                <th>Adult's</th>
                <th>Expire Date</th>
                <th>Last Connection</th>
                <th>Metric</th>
                <th>Commissioner</th>
              </tr>
              <?php
              $rowcount = 0;
              //$Year = date('Y');
              //$sql = "SELECT * FROM membershiptotals WHERE `Expire_Date` LIKE '%" . parent::GetYear() . "'";
              $sql = "SELECT * FROM `membershiptotals` WHERE YEAR(`Expire_Date`) >= '" . parent::GetYear() . "' ORDER BY `Unit` ";
              //if ($result = mysqli_query($CAdvancement->getDbConn(), $sql)) {
              if ($result = parent::doQuery($sql)) {
                $rowcount = mysqli_num_rows($result);
                while ($row = $result->fetch_assoc()) {
                  $Unit = $row['Unit'];
                  $UnitURL = "<a href='https://centennialdistrict.co/Unit_View.php?btn=Units&unit_name=$Unit'";
                  $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
                  $CurrentDate = date_create("");
                  $LastContact = date_create($row["Last_Contact"]);
                  $Difference90 = date_sub($CurrentDate, date_interval_create_from_date_string("90 days"));
                  $CurrentDate = date_create("");
                  $Difference180 = date_sub($CurrentDate, date_interval_create_from_date_string("180 days"));
                  if ($Difference180 > $LastContact)
                    $FormatterContact = "<b style='color:red;'>";
                  else if ($Difference90 > $LastContact)
                    $FormatterContact = "<b style='color:orange;'>";
                  else
                    $FormatterContact = "";
                  echo "<tr><td>" .
                    $row["Chartered_Org"] . "</td><td>" .
                    $UnitView . "</td><td>" .
                    $row["Total_Youth"] . "</td><td>" .
                    $row["Total_Adults"] . "</td><td>" .
                    $row["Expire_Date"] . "</td><td>" .
                    $FormatterContact . $row["Last_Contact"] . "</td><td>" .
                    $row["Last_Assessment_Score"] . "</td><td>" .
                    $row["Assigned_Commissioner"] . "</td></tr>";
                }
                echo "</table>";
                echo "</div>";
                mysqli_free_result($result);
              } else {
                echo "0 result";
              }

              $FormatterContact = "<b style='color:red;'>";
              echo "<p style='text-align: center;'>" . $FormatterContact . "Last contact more that 180 days old.</p></b>";
              $FormatterContact = "<b style='color:orange;'>";
              echo "<p style='text-align: center;'>" . $FormatterContact . "Last contact more than 90 days old but less than 180 days old.</p></b>";
              echo "<p style='text-align: center;'>Last contact less than 90 days old.</p>";
              ?>
          </table>
        <?php
        return;
      }
      /******************************************************************************
       * 
       * This function will read in the UnitBasedmembershiptotalsReport.csv file
       * downloaded from my.scouting and insert/update the table membershiptotals.
       * 
       * This function will only be used for current year
       * 
       *****************************************************************************/
      public static function DisplayPreviousMembershipTable()
      {
        ?>
          <center>
            <table class="tl1 tl2 tc3 tc4 tc5">
              <td style="width:300px"> <!-- Chartered Org -->
              <td style="width:120px"> <!-- Unit -->
              <td style="width:50px"> <!-- Male -->
              <td style="width:50px"> <!-- Female -->
              <td style="width:50px"> <!-- Total -->
                <tr>
                  <th>Chartered Org</th>
                  <th>Unit</th>
                  <th>Male</th>
                  <th>Female</th>
                  <th>Total</th>
                </tr>
                <?php
                $rowcount = 0;
                //$Year = date('Y');
                //$sql = "SELECT * FROM membershiptotals WHERE `Expire_Date` LIKE '%" . parent::GetYear() . "'";
                $sql = "SELECT * FROM `membershiptotals` WHERE YEAR(`Expire_Date`) >= '" . parent::GetYear() . "'";
                //if ($result = mysqli_query($CAdvancement->getDbConn(), $sql)) {
                if ($result = parent::doQuery($sql)) {
                  $rowcount = mysqli_num_rows($result);
                  while ($row = $result->fetch_assoc()) {
                    $Unit = $row['Unit'];
                    $UnitURL = "<a href='https://centennialdistrict.co/Unit_View.php?btn=Units&unit_name=$Unit'";
                    $UnitView = sprintf("%s%s>%s</a>", $UnitURL, $Unit, $Unit);
                    $Formatter = "";
                    echo "<tr><td>" .
                      $row["Chartered_Org"] . "</td><td>" .
                      $UnitView . "</td><td>" .
                      $row["Male_Youth"] . "</td><td>" .
                      $row["Female_Youth"] . "</td><td>" .
                      $Formatter . $row["Total_Youth"] . "</td></tr>";
                  }
                  echo "</table>";
                  mysqli_free_result($result);
                } else {
                  echo "0 result";
                }
                ?>
            </table>
          </center>
      <?php
      }
      /******************************************************************************
       * 
       * This function replaces the UpdateTotals function. BSA remove the membership
       * report (UnitBasedMembershipTotalsReport.csv) and this data is no longer
       * available. The Report Chartered Organizations does pervoide some of the 
       * need data so we will import the membership table from this file. The BSA
       * removed the number of Girls/Boys in the programs and its just youth now.
       * I feel it so cover up the lack of Girls actually in the program.
       * 
       *****************************************************************************/
      public static function &ImportCORData($fileName)
      {
        $col_territoryname = 0;
        $col_councilname = 1;
        $col_subcouncilname = 2;
        $col_districtname = 3;
        $col_subdistrictname = 4;
        $col_unitname = 5;
        $col_unitid = 6;
        $col_genderaccepted = 7;
        $col_expirydtstr = 8;
        $col_specialinteresttypecode = 9;
        $col_specialinteresttype = 10;
        $col_communityorganization = 11;
        $col_communityorganizationtypecode = 12;
        $col_communityorganizationtype = 13;
        $col_tenure = 14;
        $col_address = 15;
        $col_citystate = 16;
        $col_zip = 17;
        $col_phone = 18;
        $col_qttyouth = 19;
        $col_qttadultvolunteers = 20;
        $col_communityorganizationtypeshort = 21;

        $DateStr = "";
        $RecordsInError = 0;
        //$filePath = "Data/" . $fileName;
        $Inserted = 0;
        $Updated = 0;
        $row = 1;


        if (($handle = fopen($fileName, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($row < 10) { // Skip the first row(s), headers.
              if ($row == 6)
                $DateStr = $data[0]; // Get the report date.
              $row++;
              continue;
            }
            // Verify the proper array size, should be $Exprire_Date + 1
            if (count($data) != ($col_communityorganizationtypeshort + 1)) {
              $strMsg = "ERROR: ImportCORData(" . $fileName . ") is incorrect size.";
              error_log($strMsg);
              parent::function_alert($strMsg);
              exit;
            }
            $Unit = parent::formatUnitNumber($data[$col_unitname], $data[$col_genderaccepted]);
            if (strstr($Unit, "0000") || $Unit == null) {
              continue;
            }  // Skip total rows
            // Test to see if data is in database and then select either INSERT or UPDATE
            $ReformattedDate = self::FormatDate($data[$col_expirydtstr]);
            // If unit is in database then just update it..
            if (self::InsertUpdateCheckTotals($Unit)) {
              $sqlUpdateTotal = "UPDATE `membershiptotals` SET `DistrictName`='$data[$col_districtname]', `SubDistrict`='$data[$col_subdistrictname]', `Unit`='$Unit', 
              `UnitID`='$data[$col_unitid]', `Gender`='$data[$col_genderaccepted]', `Expire_Date`='$ReformattedDate', `Chartered_Org`='$data[$col_communityorganization]', 
              `Total_Youth`='$data[$col_qttyouth]', `Total_Adults`='$data[$col_qttadultvolunteers]',
              `Male_Youth`=NULL, `Female_Youth`=NULL, `Youth_Last_Year`=NULL, `Male_Adults`=NULL,`Female_Adults`=NULL, `Adults_Last_Year`=NULL
              WHERE `Unit`='$Unit'";

              if (!parent::doQuery($sqlUpdateTotal)) {
                $strMsg =  "Update Error - ImportCORData(): " . $sqlUpdateTotal . "" . mysqli_error(parent::getDbConn()) . __FILE__ . " " . __LINE__;
                error_log($strMsg);
                $RecordsInError++;
              } else {
                $Updated++;
                // If the unit has NOT expired, update the adv_* tables
                //if (parent::right($data[$Exprire_Date], 4) >= parent::GetYear())
                //   self::UpdateAdvTotals($data, parent::GetYear());
              }
            } else {
              // New Unit add it to database
              $sqlInsertTotal = "INSERT INTO `membershiptotals`(`DistrictName`, `SubDistrict`, `Unit`, `UnitID`, `Gender`, 
              `Chartered_Org`, `Total_Youth`, `Total_Adults`, `Adults_Last_Year`, `Expire_Date` ) 
              VALUES ('$data[$col_districtname]','$data[$col_subdistrictname]','$Unit','$data[$col_unitid]','$data[$col_genderaccepted]',
              '$data[$col_communityorganization]', '$data[$col_qttyouth]','$data[$col_qttadultvolunteers]','$ReformattedDate')";

              if (!parent::doQuery($sqlInsertTotal)) {
                $strMsg =  "Update Error - ImportCORData(): " . $sqlInsertTotal . "" . mysqli_error(parent::getDbConn()) . __FILE__ . " " . __LINE__;
                error_log($strMsg);
                $RecordsInError++;
              } else {
                $Updated++;
                // If the unit has NOT expired, update the adv_* tables
                //if (parent::right($data[$Exprire_Date], 4) >= parent::GetYear())
                //   self::UpdateAdvTotals($data, parent::GetYear());
              }
            }
          }
        }
        parent::UpdateLastUpdated('membershiptotals', date("m/d/Y"));
        return $RecordsInError;
      }
      /******************************************************************************
       * 
       * This function will read in the UnitBasedmembershiptotalsReport.csv file
       * downloaded from my.scouting and insert/update the table membershiptotals.
       * 
       * 
       *****************************************************************************/
      public static function &UpdateTotals($fileName)
      {
        /* Defined the file columns, which change */
        /* These must also be updated in function UpdateAdvTotals */
        $Service_Territory = 0;
        $Council_Name = 1;
        $District_Name = 2;
        $Unit_Name = 3;
        $Gender = 4;
        $Chartered_Org = 5;
        $Male_Youth = 6;
        $Female_Youth = 7;
        $Total_Youth = 8;
        $Youth_Last_Year = 9;
        $Male_Adults = 10;
        $Female_Adults = 11;
        $Total_Adults = 12;
        $Adults_Last_Year = 13;
        $Exprire_Date = 14;
        $DateStr = "";
        $RecordsInError = 0;
        $filePath = "Data/" . $fileName;
        $Inserted = 0;
        $Updated = 0;
        $row = 1;
        if (($handle = fopen($filePath, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($row < 9) { // Skip the first row(s), headers.
              if ($row == 5)
                $DateStr = $data[0]; // Get the report date.
              $row++;
              continue;
            }
            // Verify the proper array size, should be $Exprire_Date + 1
            if (count($data) != ($Exprire_Date + 1)) {
              $strMsg = "ERROR: UpdateTotals(" . $fileName . ") is incorrect.";
              error_log($strMsg);
              parent::function_alert($strMsg);
              exit;
            }
            $Unit = parent::formatUnitNumber($data[$Unit_Name], $data[$Gender]);
            if (strstr($Unit, "0000") || $Unit == null) {
              continue;
            }  // Skip total rows
            // Test to see if data is in database and then select either INSERT or UPDATE
            $ReformattedDate = self::FormatDate($data[$Exprire_Date]);
            if (self::InsertUpdateCheckTotals($Unit)) {
              //Update Date
              $sqlUpdateTotal = sprintf(
                "UPDATE `membershiptotals` SET `DistrictName`='%s',`Unit`='%s',`Gender`='%s',`Chartered_Org`='%s',`Male_Youth`='%s',
  					`Female_Youth`='%s',`Total_Youth`='%s', `Youth_Last_Year`='%s', `Male_Adults`='%s',`Female_Adults`='%s',`Total_Adults`='%s',
  					`Adults_Last_Year`='%s', `Expire_Date`='%s' WHERE `Unit`='%s'",
                $data[$District_Name],
                $Unit,
                $data[$Gender],
                addslashes($data[$Chartered_Org]),
                $data[$Male_Youth],
                $data[$Female_Youth],
                $data[$Total_Youth],
                $data[$Youth_Last_Year],
                $data[$Male_Adults],
                $data[$Female_Adults],
                $data[$Total_Adults],
                $data[$Adults_Last_Year],
                $ReformattedDate,
                $Unit
              );
              if (!parent::doQuery($sqlUpdateTotal)) {
                echo "Update Error: " . $sqlUpdateTotal . "" . mysqli_error(parent::getDbConn()) . "<br />";
                $RecordsInError++;
              } else {
                $Updated++;
                // If the unit has NOT expired, update the adv_* tables
                if (parent::right($data[$Exprire_Date], 4) >= parent::GetYear())
                  self::UpdateAdvTotals($data, parent::GetYear());
              }
              //Reset String
              $sqlUpdateTotal = "";
            } else {
              //New unit data gets insert here the first time.
              $sqlTotalInsertSt = "INSERT INTO `membershiptotals` (`DistrictName`, `Unit`, `Gender`, `Chartered_Org`, `Male_Youth`, `Female_Youth`, `Total_Youth`, Youth_Last_Year,
  				`Male_Adults`, `Female_Adults`, `Total_Adults`, `Adults_Last_Year`, `Expire_Date`) VALUES (";
              // Insert Data
              $sqlInsertTotal = "";
              for ($i = $District_Name; $i < count($data); $i++) {
                $sqlInsertTotal = $sqlInsertTotal . sprintf("'%s', ", $i == $Unit_Name ? $Unit : addslashes($data[$i]));
              }
              $sqlInsertTotal = substr($sqlInsertTotal, 0, (strlen($sqlInsertTotal) - 2));
              $sqlInsertTotal =  $sqlTotalInsertSt . $sqlInsertTotal . ");";
              // Update the database
              if (!parent::doQuery($sqlInsertTotal)) {
                echo "Insert Error: " . $sqlInsertTotal . "" . mysqli_error(parent::getDbConn()) . "<br />";
                $RecordsInError++;
              } else {
                $Inserted++;
                // If the unit has NOT expired, update the adv_* tables
                if (parent::right($data[$Exprire_Date], 4) == parent::GetYear())
                  self::UpdateAdvTotals($data, parent::GetYear());
              }
              //Reset String
              $sqlInsertTotal = "";
            }
          }
          fclose($handle);
          $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
          parent::function_alert($Usermsg);
        } else {
          $Usermsg = "Failed to open file";
          parent::function_alert($Usermsg);
        }
        parent::UpdateLastUpdated('membershiptotals', $DateStr);
        return $RecordsInError;
      }
      /******************************************************************************
       * 
       * This function will read in the Assigned_Unassigned_Units.csv file
       * downloaded from my.scouting and insert/update the membershiptotals for
       * Commissioner contact and assignee
       * 
       * 
       *****************************************************************************/
      public static function &UpdateCommissioner($fileName)
      {
        /* Defined the file columns, which change */
        /* These must also be updated in function UpdateAdvTotals */
        $colTerritory_Name = 0;
        $colCouncil_Name = 1;
        $colDistrict_Name = 2;
        $colSub_District_Name = 3;
        $colUnitID = 4;
        $colUnit_Type = 5;
        $colUnit_Number = 6;
        $colGender_Accepted = 7;
        $colChartered_Organization = 8;
        $colLast_Connection_Date = 9;
        $colMetric_Summary = 10;
        $colLast_Contact = 11;
        $colLast_Assessment_Score = 12;
        $colUnit_Leader = 13;
        $colChartered_Organization_Rep = 14;
        $colNew_Unit = 15;
        $colNew_Unit_Date = 16;
        $colAssigned = 17;
        $colAssigned_Commissioner = 18;
        $colAss__Comm__Member_ID = 19;
        $colPosition = 20;
        $colExpired_Position = 21;
        $colRegistration_Expiration_Date = 22;
        $colExpired_Unit = 23;
        $colExpired_Unit_Date = 24;
            
        $Datestr = "";
        $RecordsInError = 0;
        //$filePath = "Data/" . $fileName;
        $Inserted = 0;
        $Updated = 0;
        $row = 1;
        if (($handle = fopen($fileName, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($row < 9) { // Skip the first row(s), headers.
              if ($row == 5)
                $Datestr = $data[0]; // Get the report date.
              $row++;
              continue;
            }

            // Verify the proper array size, should be $Exprire_Date + 1
            if (count($data) != ($colExpired_Unit_Date + 1)) {
              $strMsg = "ERROR: UpdateCommissioner(" . $fileName . ") is incorrect.";
              error_log($strMsg);
              parent::function_alert($strMsg);
              exit;
            }


            $Unit = parent::formatUnitNumber($data[$colUnit_Type] . " " . $data[$colUnit_Number], $data[$colGender_Accepted]);
            if (strstr($Unit, "0000") || $Unit == null) {
              continue;
            }  // Skip total rows
            // Test to see if data is in database and then select either INSERT or UPDATE
            if (self::InsertUpdateCheckTotals($Unit)) {
              //Update Date
              $timestamp = strtotime($data[$colExpired_Unit_Date]);
              $Expire_Date = date('Y-m-d', $timestamp);
              $sqlUpdateTotal = sprintf(
                "UPDATE `membershiptotals` SET `Last_Contact`='%s',`Last_Assessment_Score`='%s',`Assigned_Commissioner`='%s',
                 `Gender`='%s', `Chartered_Org`='%s', `Expire_Date`='%s'
                 WHERE `Unit`='%s'",
                self::FormatDate($data[$colLast_Connection_Date]),
                $data[$colMetric_Summary],
                $data[$colAssigned_Commissioner],
                $data[$colGender_Accepted],
                $data[$colChartered_Organization],
                $Expire_Date,
                $Unit
              );
              if (!parent::doQuery($sqlUpdateTotal)) {
                $strMsg = "ERROR: UpdateCommissioner(" . $fileName . ") - parent::doQuery(" . $sqlUpdateTotal . ") - " . mysqli_error(parent::getDbConn());
                error_log($strMsg);
                $RecordsInError++;
              } else {
                $Updated++;
              }
              //Reset String
              $sqlUpdateTotal = "";
            } else {
              //New unit data gets insert here the first time.
              $sqlTotalInsertSt = "INSERT INTO `membershiptotals` (`DistrictName`, `Unit`, `Gender`, `Chartered_Org`, `Male_Youth`, `Female_Youth`, `Total_Youth`, Youth_Last_Year,
  				`Male_Adults`, `Female_Adults`, `Total_Adults`, `Adults_Last_Year`, `Expire_Date`) VALUES (";
              // Insert Data
              $sqlInsertTotal = "";
              for ($i = $colDistrict_Name; $i < count($data); $i++) {
                $sqlInsertTotal = $sqlInsertTotal . sprintf("'%s', ", $i == $Unit ? $Unit : addslashes($data[$i]));
              }
              $sqlInsertTotal = substr($sqlInsertTotal, 0, (strlen($sqlInsertTotal) - 2));
              $sqlInsertTotal =  $sqlTotalInsertSt . $sqlInsertTotal . ");";
              // Update the database
              if (!parent::doQuery($sqlInsertTotal)) {
                $strMsg = "ERROR: UpdateCommissioner(" . $fileName . ") - parent::doQuery(" . $sqlInsertTotal . ") - " . mysqli_error(parent::getDbConn());
                error_log($strMsg);
                $RecordsInError++;
              } else {
                $Inserted++;
              }
              //Reset String
              $sqlInsertTotal = "";
            }
          }
          fclose($handle);
          $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
          //parent::function_alert($Usermsg);
        } else {
          $Usermsg = "Failed to open file";
          //parent::function_alert($Usermsg);
        }
        parent::UpdateLastUpdated('Commissioner', $Datestr);
        return $RecordsInError;
      }
      /******************************************************************************
       * This function will update the youth count in the adv_ tables, this function
       * is called when the user updated the membership totals.
       *
       *
       *****************************************************************************/
      public static function UpdateAdvTotals($data, $year)
      {
        $Service_Territory = 0;
        $Council_Name = 1;
        $District_Name = 2;
        $Unit_Name = 3;
        $Gender = 4;
        $Chartered_Org = 5;
        $Male_Youth = 6;
        $Female_Youth = 7;
        $Total_Youth = 8;
        $Male_Adults = 9;
        $Female_Adults = 10;
        $Total_Adults = 11;
        $Exprire_Date = 12;
        $UnitType = strtok($data[$Unit_Name], " ");
        $FormattedUnit = parent::formatUnitNumber($data[$Unit_Name], $data[$Gender]);
        switch ($UnitType) {
          case "Post":
          case "Pack":
          case "Troop":
          case "Crew":
            // Check if adv data for $year exists
            $sql = sprintf("SELECT * FROM `adv_%s` WHERE `Unit`='%s' AND `Date`='%s'", strtolower($UnitType), $FormattedUnit, $year);
            $query = parent::doQuery($sql);
            if (mysqli_num_rows($query) > 0) {
              //Update
              $sql = sprintf(
                "UPDATE `adv_%s` SET `Male_Youth`='%s', `Female_Youth`='%s',`Youth`='%s',`Gender`='%s' WHERE `Unit`='%s' AND `Date`='%s'",
                strtolower($UnitType),
                $data[$Male_Youth],
                $data[$Female_Youth],
                $data[$Total_Youth],
                $data[$Gender],
                $FormattedUnit,
                $year
              );
            } else {
              //Insert
              $sql = sprintf(
                "INSERT INTO `adv_%s`(`Date`, `Male_Youth`, `Female_Youth`, `Youth`, `Unit`,`Gender`) 
  					VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
                strtolower($UnitType),
                $year,
                $data[$Male_Youth],
                $data[$Female_Youth],
                $data[$Total_Youth],
                $FormattedUnit,
                $data[$Gender]
              );
            }
            $query = parent::doQuery($sql);
            if (!$query) {
              die('UpdateAdvTotals() Error: ' . mysqli_error(parent::getDbConn()));
            }
            break;
          default:
            break;
        }
      }
      /******************************************************************************
       * Check if data is already in table, if so update it else insert it.
       * In this Table their should only be one Unit, this is the master that all
       * other table are "linked" to.
       * Returns
       *   true if data is in table
       *   false if data not found in table
       *****************************************************************************/
      public static function  &InsertUpdateCheckTotals($Unit)
      {
        //$sql = sprintf( "SELECT * FROM `membershiptotals` WHERE `Unit`='$Unit' AND `Expire_Date` LIKE '%%%s'", date("Y"));
        $sql = sprintf("SELECT * FROM `membershiptotals` WHERE `Unit`='$Unit'");
        $query = parent::doQuery($sql);
        if (!$query) {
          die('InsertUpdateCheckTotals() Error: ' . mysqli_error(parent::getDbConn()));
        }
        if (mysqli_num_rows($query) > 0) {
          $result = true;
        } else {
          $result = false;
        }
        return $result;
      }
      /******************************************************************************
       * This funtion will reformat a date from mm/dd/yyyy to a format of
       * yyyy-mm-dd
       *****************************************************************************/
      public static function FormatDate($date)
      {
        $month = strtok($date, '/');
        $day  = strtok('/');
        $year  = strtok('/');
        $Newdate = $year . "-" . $month . "-" . $day;
        return $Newdate;
      }
    }
