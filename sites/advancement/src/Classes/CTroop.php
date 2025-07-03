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
class CTroop extends CAdvancement
{
  private static $TroopTotals = array();
  private static $MemberTotal = array();
  private static $TroopDistrictGoal = 2.0;
  private static $TroopIdealGoal = 4.0;


  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetTotals()
  {
    $sqlTroopSum = sprintf('SELECT SUM(Scout),SUM(Tenderfoot),SUM(SecondClass),SUM(FirstClass),
            SUM(Star),SUM(Life),SUM(Eagle),SUM(Palms),SUM(YTD),SUM(MeritBadge)
            FROM adv_troop WHERE Date=%d', parent::GetYear());

    $resultPackSum = parent::doQuery($sqlTroopSum, MYSQLI_STORE_RESULT);
    $RankTotal = $resultPackSum->fetch_assoc();

    self::$TroopTotals['Scout'] = $RankTotal['SUM(Scout)'];
    self::$TroopTotals['Tenderfoot'] = $RankTotal['SUM(Tenderfoot)'];
    self::$TroopTotals['SecondClass'] = $RankTotal['SUM(SecondClass)'];
    self::$TroopTotals['FirstClass'] = $RankTotal['SUM(FirstClass)'];
    self::$TroopTotals['Star'] = $RankTotal['SUM(Star)'];
    self::$TroopTotals['Life'] = $RankTotal['SUM(Life)'];
    self::$TroopTotals['Eagle'] = $RankTotal['SUM(Eagle)'];
    self::$TroopTotals['Palms'] = $RankTotal['SUM(Palms)'];
    self::$TroopTotals['YTD'] = $RankTotal['SUM(YTD)'];
    self::$TroopTotals['Youth'] = parent::GetProgramTotalYouth("Troop");
    self::$TroopTotals['MeritBadge'] = $RankTotal['SUM(MeritBadge)'];

    return self::$TroopTotals;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetMemberTotals()
  {
    $sqlPackSum = sprintf("SELECT SUM(Male_Youth), SUM(Female_Youth), SUM(Total_Youth), SUM(Male_Adults), SUM(Female_Adults), SUM(Total_Adults), SUM(Youth_Last_Year), SUM(Adults_Last_Year)
		FROM membershiptotals WHERE Expire_Date LIKE '%s%%' AND Unit LIKE 'Troop%%'", self::GetYear());

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
   **
   **
   *************************************************************************/
  public static function GetPreviousMemberTotals()
  {
    $sqlPackSum = sprintf("SELECT SUM(Male_Youth), SUM(Female_Youth), SUM(Youth)
		FROM adv_troop WHERE Date LIKE '%s%%'", self::GetYear());

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
  public static function GetDistrictRatio()
  {
    return ((self::$TroopTotals['YTD'] + self::$TroopTotals['MeritBadge']) / self::$TroopTotals['Youth']);
  }
  /**************************************************************************
   **
   ** Retuern the district goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetDistrictGoal()
  {

    return self::$TroopDistrictGoal;
  }
  /**************************************************************************
   **
   ** Retuern the Ideal goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetIdealGoal()
  {
    return self::$TroopIdealGoal;
  }

  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetNumofTroops()
  {
    $NumOfTroops = 0;

//    $sql = sprintf('SELECT * FROM adv_troop WHERE Date=%d ORDER BY Unit ASC', parent::GetYear());
    $sql = "SELECT COUNT(`Unit`) FROM adv_troop WHERE Date=". parent::GetYear();

    if ($result = parent::doQuery($sql)) {
      $row = $result->fetch_array();
      $NumOfTroops = $row[0];
    }
    return $NumOfTroops;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function DisplayAdvancmenetDescription()
  {
?>
    <div class="py-5">
      <p>The table below show the Advancment history of these units. For a scout to earn Eagle
        They must earn 7 ranks in 7 years plus 21 Merit Badges which means they need to earn
        4.0 rank/merit badges per year. The Rank/Scout column in the table shows the average
        rank per scout that is earned in this unit.</p>
    </div>
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
    <table class="table table-striped">
      <tr>
        <th>Unit</th>
        <th>Scout</th>
        <th>Tenderfoot</th>
        <th>Second Class</th>
        <th>First Class</th>
        <th>Star</th>
        <th>Life</th>
        <th>Eagle</th>
        <th>Palms</th>
        <th>Merit Badges</th>
        <th>Total Rank</th>
        <th>Total Youth</th>
        <th>Rank /Scout</th>
        <th>Date</th>

      </tr>
    <?php
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function DisplayUnitAdvancementTable()
  {
    ?>
      <table class="table table-striped">
        <tr>
          <th>Scout</th>
          <th>Tenderfoot</th>
          <th>Second Class</th>
          <th>First Class</th>
          <th>Star</th>
          <th>Life</th>
          <th>Eagle</th>
          <th>Palms</th>
          <th>Merit Badges</th>
          <th>Total Rank</th>
          <th>Total Youth</th>
          <th>Rank /Scout</th>
          <th>Date</th>

        </tr>
    <?php
  }
  /******************************************************************************
   * 
   * 09Oct2022 - BSA changed form of the CSV download file
   * 
   *****************************************************************************/
  public static function &UpdateTroop($fileName)
  {
    $colDistrict = 0;
    $colOrg      = 1;
    $colUnitID  = 2;
    $colScoutMTD = 3;
    $colScoutYTD = 4;
    $colTenderfootMTD = 5;
    $colTenderfootYTD = 6;
    $colSecondMTD = 7;
    $colSecondYTD = 8;
    $colFirstMTD = 9;
    $colFirstYTD = 10;
    $colStarMTD = 11;
    $colStarYTD = 12;
    $colLifeMTD = 13;
    $colLifeYTD = 14;
    $colEagleMTD = 15;
    $colEagleYTD = 16;
    $colTotalMTD = 17;
    $colTotalYTD = 18;
    $colPalmsMTD = 19;
    $colPalmsYTD = 20;
    $colMeritBadgesMTD = 21;
    $colMeritBadgesYTD = 22;
    $colYouthTotal = 23;

    $Datestr = "";
    $sqlTroopInsertSt = "INSERT INTO `adv_troop`(`Scout`, `Tenderfoot`, `SecondClass`, `FirstClass`, `Star`, `Life`, `Eagle`, `YTD`,
    	`Palms`, `MeritBadge`, `Date`, `Unit`, `Youth`, `UnitID`) 
        VALUES (";

    $Inserted = 0;
    $Updated = 0;
    $RecordsInError = 0;
    $row = 1;
    //$filePath = "Data/" . $fileName;
    //$TroopYear = date("Y");
    $TroopYear = self::GetYear();
    if (($handle = fopen($fileName, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //assertCount(19, "Not enought columns in .csv file");
        if ($row < 11) { // Skip the first row(s), headers.
          if ($row == 7)
            $Datestr = $data[0]; // Get the report date.
          $row++;
          continue;
        }

        // Verify the proper array size, should be $Exprire_Date + 1
        if (count($data) != ($colYouthTotal + 1)) {
          $strMsg = "ERROR: UpdateTroop(" . $fileName . ") is incorrect.";
          error_log($strMsg);
          parent::function_alert($strMsg);
          exit;
        }

        $TroopUnit = parent::formatUnitNumber($data[1], null);
        if ($TroopUnit == null || $TroopUnit[0] == 'C' || $TroopUnit[0] == 'P') {
          continue;
        }  // For some reason there can be pack and crew data in this file.
        // Test to see if data is in database and then select either INSERT or UPDATE
        if (parent::InsertUpdateCheck($TroopYear, $TroopUnit)) {
          //parent::UpdateMembership($data[$colDistrict],  $TroopUnit, $data[$colUnitID], $data[$colYouthTotal]);

          //Update Date
          $sqlUpdateTroop = "UPDATE `adv_troop` SET `Scout`=' $data[$colScoutYTD]',`Tenderfoot`='$data[$colTenderfootYTD]', `SecondClass`='$data[$colSecondYTD]',`FirstClass`='$data[$colFirstYTD]',
   					`Star`='$data[$colStarYTD]',`Life`='$data[$colLifeYTD]',`Eagle`='$data[$colEagleYTD]', `YTD`='$data[$colTotalYTD]',
   					`Palms`='$data[$colPalmsYTD]',`MeritBadge`='$data[$colMeritBadgesYTD]',`Date`='$TroopYear', `Youth`='$data[$colYouthTotal]', `UnitID`='$data[$colUnitID]'
   					WHERE `Unit`='$TroopUnit' AND `Date`='$TroopYear'";
          if (!parent::doQuery($sqlUpdateTroop)) {
            $strErr = "Error: UpdateTroop(" . $fileName . ") Update Error: " . $sqlUpdateTroop . "" . mysqli_error(parent::getDbConn());
            error_log($strErr);
            $RecordsInError++;
          } else
            $Updated++;
          //Reset String
          $sqlUpdateTroop = "";
        } else {
          // Insert Data
          //parent::UpdateMembership($data[$colDistrict],  $TroopUnit, $data[$colUnitID], $data[$colYouthTotal]);
          $sqlInsertTroop = "";
          //for($i=0; $i < count($data); $i++){
          //    $sqlInsertTroop = $sqlInsertTroop.sprintf("'%s', ", addslashes($data[$i]) );
          //}
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colScoutYTD]);  //Scout_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colTenderfootYTD]);  //Tenderfoot_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colSecondYTD]);  //Second_Class_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colFirstYTD]);  //First_Class_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colStarYTD]);  //Star_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colLifeYTD]);  //Life_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colEagleYTD]);  //Eagle_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colTotalYTD]);  //Total_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colPalmsYTD]);  //Palms_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colMeritBadgesYTD]);  //Merit_Badges_YTD
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $TroopYear);  //Year
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $TroopUnit);  //Unit
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colYouthTotal]);  //Youth
          $sqlInsertTroop = $sqlInsertTroop . sprintf("'%s', ", $data[$colUnitID]);  //UnitIS
          $sqlInsertTroop = substr($sqlInsertTroop, 0, (strlen($sqlInsertTroop) - 2));
          $sqlInsertTroop =  $sqlTroopInsertSt . $sqlInsertTroop . ");";
          // Update the database
          if (!parent::doQuery($sqlInsertTroop)) {
            $strErr = "Error: UpdateTroop(" . $fileName . ") Insert Error: " . $sqlInsertTroop . "" . mysqli_error(parent::getDbConn());
            error_log($strErr);
            $RecordsInError++;
          } else
            $Inserted++;
        }
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
      //parent::function_alert($Usermsg);
    } else {
      $Usermsg = "Failed to open file";
      //parent::function_alert($Usermsg);
    }
    parent::UpdateLastUpdated('adv_troop', $Datestr);

    return $RecordsInError;
  }
  /******************************************************************************
   * 
   * This function will return the total number of youth is a program
   * (i.e. crew, pack or troop) by the selected year.
   * 
   *****************************************************************************/
  public static function &GetTotalYouthbyYear($UnitType, $year)
  {

    $Table = null;
    switch ($UnitType) {
      case "Crew":
        $Table = "adv_crew";
        break;
      case "Pack":
        $Table = "adv_pack";
        break;
      case "Troop":
        $Table = "adv_troop";
        break;
      default:
        parent::function_alert("Unknow unit type GetTotalYouthbyYear()");
        exit;
        break;
    }
    $sqlUnitSum = sprintf(
      "SELECT SUM(`Youth`) FROM `%s` WHERE `Date` = '%s'",
      $Table,
      $year
    );
    $resultUnitSum = parent::doQuery($sqlUnitSum, MYSQLI_STORE_RESULT);
    $YouthTotal = $resultUnitSum->fetch_assoc();
    $Total = $YouthTotal["SUM(`Youth`)"];

    return $Total;
  }
  /******************************************************************************
   * 
   * This function will update the Packs adventure awards
   * 
   *****************************************************************************/
  public static function GetTroopsBelowGoal()
  {
    //Now get number below goal	
    $UnderGoal = 0;
    $sql = sprintf("SELECT * FROM adv_troop WHERE Date=%s ORDER BY Unit ASC", parent::GetYear());
    if ($result = parent::doQuery($sql)) {
      while ($row = $result->fetch_assoc()) {
        $UnitYouth = self::GetUnitTotalYouth($row['Unit'], $row['Youth'], parent::GetYear());
        $UnitRankScout = self::GetUnitRankperScout($UnitYouth, $row["YTD"] + $row["MeritBadge"], $row["Unit"]);

        if ($UnitYouth == 0) {
          //$UnderGoal++;
        } elseif (floatval($UnitRankScout) <= self::GetDistrictGoal($row["Date"])) {
          $UnderGoal++;
        }
      }
      mysqli_free_result($result);
    }
    return $UnderGoal;
  }
  /******************************************************************************
   * 
   * This function will update the Packs adventure awards
   * 
   *****************************************************************************/
  public static function GetTroopsAboveGoal()
  {
    //Now get number below goal	
    $AboveGoal = 0;
    $sql = sprintf("SELECT * FROM adv_troop WHERE Date=%s ORDER BY Unit ASC", parent::GetYear());
    if ($result = parent::doQuery($sql)) {
      while ($row = $result->fetch_assoc()) {
        $UnitYouth = parent::GetUnitTotalYouth($row['Unit'], $row['Youth'], parent::GetYear());
        $UnitRankScout = self::GetUnitRankperScout($UnitYouth, $row["YTD"] + $row["MeritBadge"], $row["Unit"]);

        if ($UnitYouth == 0) {
          //$AboveGoal++;
        } elseif (floatval($UnitRankScout) >= self::GetDistrictGoal($row["Date"])) {
          $AboveGoal++;
        }
      }
      mysqli_free_result($result);
    }
    //$PacksAboveGoal = $TotalPacks - $PacksUnderGoal;
    return $AboveGoal;
  }
}
