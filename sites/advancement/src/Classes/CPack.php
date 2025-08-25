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

//include_once('CAdvancement.php');
load_class(SHARED_PATH . '/src/Classes/CAdvancement.php');

/******************************************************************************
 * ****************************************************************************
 * ****************************************************************************
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 * ****************************************************************************
 * ****************************************************************************
 */
class CPack extends CAdvancement
{
  private static $PackTotal = array();
  private static $MemberTotal = array();
  // 2020 and earlier goals (only rank)
  private static $RankDistrictGoal = 0.6;
  private static $RankIdealGoal = 1.2;
  // 2021 and later goals (rnak and adventures)
  private static $AdventuresDistrictGoal = 3.6;
  private static $AdventuresIdealGoal = 7.2;


  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetDistrictRatio()
  {
    return ((self::$PackTotal['YTD'] + self::$PackTotal['adventure']) / self::$PackTotal['youth']);
  }
  /**************************************************************************
   **
   ** Retuern the district goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetDistrictGoal($year)
  {
    $Goal = 0;

    if (!isset($year))
      $year = parent::GetYear();
    if ($year <= "2020")
      $Goal = self::$RankDistrictGoal;
    else
      $Goal = self::$AdventuresDistrictGoal;

    return $Goal;
  }
  /**************************************************************************
   **
   ** Retuern the Ideal goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetIdealGoal($year)
  {
    if (parent::GetYear() <= "2020")
      return self::$RankIdealGoal;
    else
      return self::$AdventuresIdealGoal;
  }

  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetNumofPacks()
  {
    $NumOfPacks = 0;

    $sql = sprintf('SELECT * FROM adv_pack WHERE Date=%d ORDER BY Unit ASC', self::GetYear());

    if ($result = parent::doQuery($sql)) {
      $NumOfPacks = mysqli_num_rows($result);
    }
    return $NumOfPacks;
  }
  /**************************************************************************
   **
   ** Return the number of unit which have meet or exceeded the District Goals
   **
   *************************************************************************/
  public static function GetNumofPacksAboveGoal()
  {
    $PacksAboveGoal = 0;

    //Now get number meeting goal		
    $sql = sprintf("SELECT * FROM adv_pack WHERE Date=%s ORDER BY Unit ASC", parent::GetYear());

    $i = 0;
    if ($result = parent::doQuery($sql)) {
      while ($row = $result->fetch_assoc()) {
        $UnitRatio = self::GetUnitRankperScout($row['Youth'], $row['YTD'] + $row["adventure"], $row['Unit']);
        if ($row['Youth'] == 0)
          $i++;
        else if ($UnitRatio > self::GetDistrictGoal(null)) {
          $PacksAboveGoal++;
        }
      }
      mysqli_free_result($result);
    }

    return $PacksAboveGoal;
    //    	if ($result = mysqli_query($CPack->getDbConn(), $sql)) {
    //    		$rowcount = mysqli_num_rows($result);
    //    	}
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetPack()
  {
    $result = null;

    $sql = sprintf('SELECT * FROM adv_pack WHERE Date=%d ORDER BY Unit ASC', parent::GetYear());

    if ($result = parent::doQuery($sql)) {
    }
    return $result;
  }

  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetTotals()
  {
    $sqlPackSum = sprintf('SELECT SUM(lion), SUM(tiger), SUM(bobcat), SUM(wolf), SUM(bear), SUM(webelos), SUM(aol), SUM(YTD),SUM(adventure)
		FROM adv_pack WHERE Date=%d', self::GetYear());

    $resultPackSum = parent::doQuery($sqlPackSum, MYSQLI_STORE_RESULT);
    $RankTotal = $resultPackSum->fetch_assoc();
    self::$PackTotal['lion'] = (int)$RankTotal['SUM(lion)'];
    self::$PackTotal['tiger'] = (int)$RankTotal['SUM(tiger)'];
    self::$PackTotal['bobcat'] = (int)$RankTotal['SUM(bobcat)'];
    self::$PackTotal['wolf'] = (int)$RankTotal['SUM(wolf)'];
    self::$PackTotal['bear'] = (int)$RankTotal['SUM(bear)'];
    self::$PackTotal['webelos'] = (int)$RankTotal['SUM(webelos)'];
    self::$PackTotal['aol'] = (int)$RankTotal['SUM(aol)'];
    self::$PackTotal['YTD'] = (int)$RankTotal['SUM(YTD)'];
    self::$PackTotal['adventure'] = (int)$RankTotal['SUM(adventure)'];
    self::$PackTotal['youth'] = (int)parent::GetProgramTotalYouth("Pack");
    return self::$PackTotal;
  }
  /**************************************************************************
   **
   ** This function will get the current year membership numbers.
   **
   *************************************************************************/
  public static function GetMemberTotals()
  {
    $sqlPackSum = sprintf("SELECT SUM(Male_Youth), SUM(Female_Youth), SUM(Total_Youth), SUM(Male_Adults), SUM(Female_Adults), SUM(Total_Adults), SUM(Youth_Last_Year), SUM(Adults_Last_Year)
		FROM membershiptotals WHERE Expire_Date LIKE '%s%%' AND Unit LIKE 'Pack%%'", self::GetYear());

    $resultPackSum = parent::doQuery($sqlPackSum, MYSQLI_STORE_RESULT);
    if ($resultPackSum) {
      $MemberTotal = $resultPackSum->fetch_assoc();
      self::$MemberTotal['Male_Youth']       = $MemberTotal['SUM(Male_Youth)'];
      self::$MemberTotal['Female_Youth']     = $MemberTotal['SUM(Female_Youth)'];
      self::$MemberTotal['Total_Youth']      = $MemberTotal['SUM(Total_Youth)'];
      self::$MemberTotal['Youth_Last_Year']  = $MemberTotal['SUM(Youth_Last_Year)'];
      self::$MemberTotal['Male_Adults']      = $MemberTotal['SUM(Male_Adults)'];
      self::$MemberTotal['Female_Adults']    = $MemberTotal['SUM(Female_Adults)'];
      self::$MemberTotal['Total_Adults']     = $MemberTotal['SUM(Total_Adults)'];
      self::$MemberTotal['Adults_Last_Year'] = $MemberTotal['SUM(Adults_Last_Year)'];
    }
    return self::$MemberTotal;
  }
  /**************************************************************************
   **
   ** This function will get the current year membership numbers.
   **
   *************************************************************************/
  public static function GetPreviousMemberTotals()
  {
    $sqlPackSum = sprintf("SELECT SUM(Male_Youth), SUM(Female_Youth), SUM(Youth)
		FROM adv_pack WHERE Date LIKE '%s%%'", self::GetYear());

    $resultPackSum = parent::doQuery($sqlPackSum, MYSQLI_STORE_RESULT);
    if ($resultPackSum) {
      $MemberTotal = $resultPackSum->fetch_assoc();
      self::$MemberTotal['Male_Youth']       = $MemberTotal['SUM(Male_Youth)'];
      self::$MemberTotal['Female_Youth']     = $MemberTotal['SUM(Female_Youth)'];
      self::$MemberTotal['Total_Youth']      = $MemberTotal['SUM(Youth)'];
    }
    return self::$MemberTotal;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function DisplayAdvancementTable()
  {
?>
    <table class="table table-striped">
      <thead>
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
        <td style="width:50px">
          <tr>
            <th>Lion</th>
            <th>Tiger</th>
            <th>Bobcat</th>
            <th>Wolf</th>
            <th>Bear</th>
            <th>WEBLOS</th>
            <th>AOL</th>
            <th>Total Rank</th>
            <th>Youth</th>
            <th>Rank /Scout</th>
            <th>Adventures</th>
            <th>Date</th>
          </tr>
      </thead>
    <?php
  }

  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function DisplayUnitAdvancement()
  {
    ?>
      <div class="px-5">
        <table class="table table-striped">
          <thead>
            <td style="width:120px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
            <td style="width:50px">
              <tr>
                <th>Unit</th>
                <th>Lion</th>
                <th>Tiger</th>
                <th>Wolf</th>
                <th>Bear</th>
                <th>WEBLOS</th>
                <th>AOL</th>
                <th>Total Rank</th>
                <th>Total Youth</th>
                <th>Rank /Scout</th>
                <th>Adventures</th>
                <th>Date</th>
              </tr>
          </thead>
          <?php
        }
        /**************************************************************************
         **
         **
         **
         *************************************************************************/
        public static function DisplayAdvancmenetDescription()
        {
          if (parent::GetYear() <= "2020") {
          ?>
            <div>
              <p style='text-align: center;'>The table below show the Advancment history of this unit. For a scout to earn Arrow of Light (AOL)
                They must earn 7 ranks in 6 years which means they need to earn 1.2 rank per year. The Rank/Scout
                column in the table shows the average rank per scout that is earned in this unit.</p>
            </div>
          <?php
          } else {
          ?>
            <div style='text-align: center;'>
              <p>The table below show the Advancment history of this unit. For a scout to earn Arrow of Light (AOL)
                They must earn 6 ranks and 43 adventures in 6 years which means they need to earn 7.2 rank per year.
                The Rank/Scout column in the table shows the average rank per scout that is earned in this unit.</p>
            </div>
          <?php
          }
          ?>
          <p style='text-align: center;'>Cub Scout Program Updates can be found here <a href="https://www.scouting.org/program-updates/cub-scout-program-updates-announced/">Updates</a> </p>
      <?php
        }
        /**************************************************************************
         **
         **
         **
         *************************************************************************/
        public static function DispalyBarAdvancementData()
        {
          $Totals = self::GetTotals();
          //$strTotals = "['Lion','"    . $Totals['lion'] .    "','red', '"  . $Totals['lion'] . "']," .
          //		     "['Tiger','"   . $Totals['tiger'] .   "','blue', '"  .$Totals['tiger'] . "']," .
          //		     "['Bobcat','"  . $Totals['bobcat'] .  "','green', '" . $Totals['bobcat'] . "']," .
          //		     "['Wolf','"    . $Totals['wolf'] .    "','red', '"   . $Totals['wolf'] . "']," .
          //		     "['Bear','"    . $Totals['bear'] .    "','red', '"   . $Totals['bear'] . "']," .
          //		     "['Webelos','" . $Totals['webelos'] . "','red', '"   . $Totals['webelos'] . "']," .
          //		     "['AOL','"     . $Totals['aol'] .      "','red', '"   .$Totals['aol'] . "']";
          $strTotals = "['Lion',"    . $Totals['lion'] .    "]," .
            "['Tiger',"   . $Totals['tiger'] .   "]," .
            "['Bobcat',"  . $Totals['bobcat'] .  "]," .
            "['Wolf',"    . $Totals['wolf'] .    "]," .
            "['Bear',"    . $Totals['bear'] .    "]," .
            "['Webelos'," . $Totals['webelos'] . "]," .
            "['AOL',"     . $Totals['aol'] .     "]";
          return $strTotals;
        }
        /**************************************************************************
         **
         **
         **
         *************************************************************************/
        public static function DisplayPacksBelowData()
        {
          $strData = "";
          $strData = "['Above'," . self::GetPacksAboveGoal(self::GetDistrictGoal(null)) . "]," .
            "['Below'," . self::GetPacksBelowGoal(self::GetDistrictGoal(null)) . "]";
          return $strData;
        }
        /**************************************************************************
         **
         **
         **
         *************************************************************************/
        public static function DisplayPacksAboveData()
        {
          $strData = "";
          $strData = "['Meeting'," . self::GetPacksAboveGoal(self::GetDistrictGoal(null)) . "]," .
            "['Below'," . self::GetPacksBelowGoal(self::GetDistrictGoal(null)) . "]";
          return $strData;
        }
        /******************************************************************************
         * 
         * This function will read in the file DetailedAdvancementReportCubScout.csv
         * downloaded from my.scouting,org. The unit totals will be updated when the 
         * User updated the membershiptotals.csv file.
         * 09Oct2022 - BSA changed the file format along with the name of the file.
         * 
         *****************************************************************************/
        public static function &UpdatePack($fileName)
        {
          $colDistrict = 0;
          $colOrg      = 1;
          $colUnitID   = 2;

          $colLionMTD  = 3;
          $colLionYTD  = 4;
          $colTigerMTD  = 5;
          $colTigerYTD  = 6;
          $colBobcatMTD  = 7;
          $colBobcatYTD  = 8;
          $colWolfMTD  = 9;
          $colWolfYTD  = 10;
          $colBearMTD  = 11;
          $colBearYTD  = 12;
          $colWebelosMTD  = 13;
          $colWebelosYTD  = 14;
          $colAOLMTD  = 15;
          $colAOLYTD  = 16;
          $colRanksMTD = 17;
          $colRankYTD = 18;
          $colYouthTotal = 19;
          //$filePath = "Data/" . $fileName;
          $Datestr = "";
          $sqlPackInsertSt = "INSERT INTO `adv_pack`(`lion`, `tiger`, `bobcat`, `wolf`, `bear`, `webelos`, `aol`, `YTD`, `Unit`, `Date`) 
  VALUES (";
          $Inserted = 0;
          $Updated = 0;
          $RecordsInError = 0;
          $row = 1;
          $PackYear = parent::GetYear();
          if (($handle = fopen($fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
              if ($row < 11) { // Skip the first row(s), headers.
                if ($row == 7)
                  $Datestr = $data[0]; // Get the report date.
                $row++;
                continue;
              }
              // Verify the proper array size, should be $Exprire_Date + 1
              if (count($data) != ($colYouthTotal + 1)) {
                $strMsg = "ERROR: UpdatePack(" . $fileName . ") is incorrect.";
                error_log($strMsg);
                parent::function_alert($strMsg);
                exit;
              }
              $PackUnit = parent::formatUnitNumber($data[$colOrg], null);
              if ($PackUnit == null || $PackUnit[0] == 'C' || $PackUnit[0] == 'T') {
                continue;
              }  // For some reason there can be troop and crew data in this file.
              // Update the membership data.. Since BSA no longer produces the membership total report.
              // Test to see if data is in database and then select either INSERT or UPDATE
              if (parent::InsertUpdateCheck($PackYear, $PackUnit)) {
                // Need to Update membership table.. TODO:
                // Most of the data for the membership table will come from the assigned_unassigned_Units.csv file
                parent::UpdateMembership($data[$colDistrict],  $PackUnit, $data[$colUnitID], $data[$colYouthTotal]);

                //Update Date
                $sqlUpdatePack = sprintf(
                  "UPDATE `adv_pack` SET `lion`='%s',`tiger`='%s',`bobcat`='%s',`wolf`='%s',`bear`='%s',
                `webelos`='%s',`aol`='%s', `YTD`='%s', `Youth`='%s' WHERE `Unit`='%s' AND `Date`='%s'",
                  $data[$colLionYTD],
                  $data[$colTigerYTD],
                  $data[$colBobcatYTD],
                  $data[$colWolfYTD],
                  $data[$colBearYTD],
                  $data[$colWebelosYTD],
                  $data[$colAOLYTD],
                  $data[$colRankYTD],
                  $data[$colYouthTotal],
                  $PackUnit,
                  $PackYear
                );
                if (!parent::doQuery($sqlUpdatePack)) {
                  $strErr =  "Error: UpdatePack(" . $fileName . "): " . $sqlUpdatePack . " " . mysqli_error(parent::getDbConn());
                  error_log($strErr);
                  $RecordsInError++;
                } else {
                  $Updated++;
                }
                //Reset String
                $sqlUpdatePack = "";
              } else {
                // Need to Update membership table.. TODO:
                // Most of the data for the membership table will come from the assigned_unassigned_Units.csv file
                parent::UpdateMembership($data[$colDistrict],  $PackUnit, $data[$colUnitID], $data[$colYouthTotal]);

                // Insert Data
                $Date = addslashes($PackYear);
                $sqlInsertPack = "INSERT INTO `adv_pack`(`lion`, `tiger`, `bobcat`, `wolf`, `bear`, 
                `webelos`, `aol`, `Date`, `YTD`, `Youth`, `Unit`,
                `Male_Youth`, `Female_Youth`, `LDS`, `Gender`, `Sub-District`, `adventure`) 
                VALUES ('$data[$colLionYTD]','$data[$colTigerYTD]','$data[$colBobcatYTD]','$data[$colWolfYTD]','$data[$colBearYTD]',
                '$data[$colWebelosYTD]','$data[$colAOLYTD]', '$Date', '$data[$colRankYTD]','$data[$colYouthTotal]', '$PackUnit',
                '0', '0', '0', '', '', '0')";
                // Update the database
                if (!parent::doQuery($sqlInsertPack)) {
                  $strErr =  "Error: UpdatePack(" . $fileName . ") Insert Error: " . $sqlInsertPack . " " . mysqli_error(parent::getDbConn());
                  error_log($strErr);
                  $RecordsInError++;
                } else {
                  $Inserted++;
                  // TODO: update the rank/scout now.
                }
                //Reset String
                $sqlInsertPack = "";
              }
            }
            fclose($handle);
            $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
            //parent::function_alert($Usermsg);
          } else {
            $Usermsg = "Failed to open file";
            //parent::function_alert($Usermsg);
          }
          parent::UpdateLastUpdated('adv_pack', $Datestr);
          return $RecordsInError;
        }
        /******************************************************************************
         * 
         * This function will update the Packs adventure awards
         * 27Oct22 - BSA changed the format of the file..
         * 
         *****************************************************************************/
        public static function &UpdateAdventure($fileName)
        {
          //Define the layout of the .csv file
          $colDistrict     = 0;
          $colOrganization = 1;
          $colUnit_ID      = 2;
          $colMTD_Jan      = 3;
          $colMTD_Feb      = 4;
          $colMTD_Mar      = 5;
          $colMTD_Apr      = 6;
          $colMTD_May      = 7;
          $colMTD_Jun      = 8;
          $colMTD_Jul      = 9;
          $colMTD_Aug      = 10;
          $colMTD_Sep      = 11;
          $colMTD_Oct      = 12;
          $colMTD_Nov      = 13;
          $colMTD_Dec      = 14;
          $colTotal        = 15;
          $ColYouth        = 16;
          //$filePath = "Data/" . $fileName;
          //$sqlPackInsertSt = "INSERT INTO `adv_pack`(`lion`, `tiger`, `bobcat`, `wolf`, `bear`, `webelos`, `aol`, `YTD`, `Unit`, `Date`) 
          //  VALUES (";
          $Inserted = 0;
          $Updated = 0;
          $RecordsInError = 0;
          $row = 1;
          //$PackYear = date("Y");
          $PackYear = parent::GetYear();
          if (($handle = fopen($fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
              if ($row < 10) {
                $row++;
                continue;
              } //Skip first 10 rows "Headers"
              // Verify the proper array size, should be $Exprire_Date + 1
              if (count($data) != ($ColYouth + 1)) {
                $strMsg = "ERROR: UpdateAdventure(" . $fileName . ") is incorrect.";
                error_log($strMsg);
                parent::function_alert($strMsg);
                exit;
              }
              $Unit = strtok($data[$colOrganization], '(');
              $Gender = strtok(')');
              $PackUnit = parent::formatUnitNumber($Unit, $Gender);
              if ($PackUnit[$colOrganization] == 'C' || $PackUnit[$colOrganization] == 'T') {
                continue;
              }  // For some reason there can be troop and crew data in this file.
              if (parent::InsertUpdateCheck($PackYear, $PackUnit)) {
                //Update Date
                $sqlUpdatePack = sprintf(
                  "UPDATE `adv_pack` SET `adventure`='%s' WHERE `Unit`='%s' AND `Date`='%s'",
                  $data[$colTotal],
                  $PackUnit,
                  $PackYear
                );
                if (!parent::doQuery($sqlUpdatePack)) {
                  $strErr = "Error: UpdateAdventure(" . $fileName . ") Error: " . $sqlUpdatePack . "" . mysqli_error(parent::getDbConn());
                  error_log($strErr);
                  $RecordsInError++;
                } else {
                  $Updated++;
                }
                //Reset String
                $sqlUpdatePack = "";
              } else {
                $Usermsg = "Error: UpdateAdventure() - Pack advancement data needs to be entered first " . $PackUnit;
                error_log($Usermsg);
                $RecordsInError++;
                parent::function_alert($Usermsg);
              }
            }
            fclose($handle);
            $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
            //parent::function_alert($Usermsg);
          } else {
            $Usermsg = "Failed to open file";
            //parent::function_alert($Usermsg);
          }
          return $RecordsInError;
        }
        /******************************************************************************
         * 
         * This function will update the Packs adventure awards
         * 
         *****************************************************************************/
        public static function GetPacksBelowGoal()
        {
          //Now get number below goal	
          $PacksUnderGoal = 0;
          $sql = sprintf("SELECT * FROM adv_pack WHERE Date=%s ORDER BY Unit ASC", parent::GetYear());
          if ($result = parent::doQuery($sql)) {
            while ($row = $result->fetch_assoc()) {
              $UnitYouth = self::GetUnitTotalYouth($row['Unit'], $row['Youth'], parent::GetYear());
              $UnitRankScout = self::GetUnitRankperScout($UnitYouth, $row["YTD"] + $row["adventure"], $row["Unit"]);
              if ($UnitYouth == 0) {
                $PacksUnderGoal++;
              } elseif (floatval($UnitRankScout) <= self::GetDistrictGoal($row["Date"])) {
                $PacksUnderGoal++;
              }
            }
            mysqli_free_result($result);
          }
          //$PacksAboveGoal = $TotalPacks - $PacksUnderGoal;
          return $PacksUnderGoal;
        }
        /******************************************************************************
         * 
         * This function will update the Packs adventure awards
         * 
         *****************************************************************************/
        public static function GetPacksAboveGoal($goal)
        {
          //Now get number below goal	
          $PacksGoal = 0;
          $sql = sprintf("SELECT * FROM adv_pack WHERE Date=%s ORDER BY Unit ASC", parent::GetYear());
          if ($result = parent::doQuery($sql)) {
            while ($row = $result->fetch_assoc()) {
              $UnitYouth = self::GetUnitTotalYouth($row['Unit'], $row['Youth'], parent::GetYear());
              $UnitRankScout = self::GetUnitRankperScout($UnitYouth, $row["YTD"] + $row["adventure"], $row["Unit"]);
              if ($UnitYouth == 0) {
                //$PacksGoal++;
              } elseif (floatval($UnitRankScout) >= $goal) {
                $PacksGoal++;
              }
            }
            mysqli_free_result($result);
          }
          //$PacksAboveGoal = $TotalPacks - $PacksUnderGoal;
          return $PacksGoal;
        }
      }
