<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
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

/**
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 */
class CShip extends CAdvancement
{
  private static $ShipTotals = array();
  private static $MemberTotal = array();
  private static $ShipDistrictGoal = 0; //Ships have no advancement goals
  private static $ShipIdealGoal = 0;    //Ships have no advancement goals
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetTotals()
  {
    $sqlShipSum = sprintf('SELECT SUM(Scout),SUM(Tenderfoot),SUM(SecondClass),SUM(FirstClass),
            SUM(Star),SUM(Life),SUM(Eagle),SUM(Palms),SUM(YTD),SUM(MeritBadge),SUM(Discovery),
            SUM(Pathfinder), SUM(Summit), SUM(Venturing)
            FROM adv_ship WHERE Date=%d', parent::GetYear());

    $resultPackSum = parent::doQuery($sqlShipSum);
    if(!$resultPackSum)
      return false;
    $RankTotal = $resultPackSum->fetch_assoc();

    self::$ShipTotals['Scout'] = $RankTotal['SUM(Scout)'];
    self::$ShipTotals['Tenderfoot'] = $RankTotal['SUM(Tenderfoot)'];
    self::$ShipTotals['SecondClass'] = $RankTotal['SUM(SecondClass)'];
    self::$ShipTotals['FirstClass'] = $RankTotal['SUM(FirstClass)'];
    self::$ShipTotals['Star'] = $RankTotal['SUM(Star)'];
    self::$ShipTotals['Life'] = $RankTotal['SUM(Life)'];
    self::$ShipTotals['Eagle'] = $RankTotal['SUM(Eagle)'];
    self::$ShipTotals['Palms'] = $RankTotal['SUM(Palms)'];
    self::$ShipTotals['YTD'] = $RankTotal['SUM(YTD)'];
    self::$ShipTotals['Youth'] = parent::GetProgramTotalYouth("Troop");
    self::$ShipTotals['MeritBadges'] = $RankTotal['SUM(MeritBadge)'];
    self::$ShipTotals['Discovery'] = $RankTotal['SUM(Discovery)'];
    self::$ShipTotals['Pathfinder'] = $RankTotal['SUM(Pathfinder)'];
    self::$ShipTotals['Summit'] = $RankTotal['SUM(Summit)'];
    self::$ShipTotals['Venturing'] = $RankTotal['SUM(Venturing)'];

    return self::$ShipTotals;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetMemberTotals()
  {
    $sqlPackSum = sprintf("SELECT SUM(Male_Youth), SUM(Female_Youth), SUM(Total_Youth), SUM(Male_Adults), SUM(Female_Adults), SUM(Total_Adults), SUM(Youth_Last_Year), SUM(Adults_Last_Year)
		FROM membershiptotals WHERE Expire_Date LIKE '%s%%' AND Unit LIKE 'Ship%%'", self::GetYear());

    $resultPackSum = parent::doQuery($sqlPackSum);
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
		FROM adv_ship WHERE Date LIKE '%s%%'", self::GetYear());

    $resultPackSum = parent::doQuery($sqlPackSum);
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
    return ((self::$ShipTotals['YTD'] + self::$ShipTotals['MeritBadges']) / self::$ShipTotals['Youth']);
  }
  /**************************************************************************
   **
   ** Retuern the district goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetDistrictGoal()
  {

    return self::$ShipDistrictGoal;
  }
  /**************************************************************************
   **
   ** Retuern the Ideal goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetIdealGoal()
  {
    return self::$ShipIdealGoal;
  }

  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetNumofShips()
  {
    $NumOfShips = 0;

    $sql = sprintf('SELECT * FROM adv_ship WHERE Date=%d ORDER BY Unit ASC', parent::GetYear());

    if ($result = parent::doQuery($sql)) {
      $NumOfShips = mysqli_num_rows($result);
    }
    return $NumOfShips;
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
        <th>Star</th>
        <th>Life</th>
        <th>Eagle</th>
        <th>Palms</th>
        <th>Merit Badges</th>
        <th>Total Rank</th>
        <th>Total Youth</th>
        <th>Rank/Scout</th>
        <th>Discovery</th>
        <th>Pathfinder</th>
        <th>Summit</th>
        <th>Venturing</th>
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
          <th>Star</th>
          <th>Life</th>
          <th>Eagle</th>
          <th>Palms</th>
          <th>Merit Badges</th>
          <th>Total Rank</th>
          <th>Total Youth</th>
          <th>Rank/Scout</th>
          <th>Discovery</th>
          <th>Pathfinder</th>
          <th>Summit</th>
          <th>Venturing</th>
          <th>Date</th>

        </tr>
    <?php
  }
  /******************************************************************************
   * 
   * This file will read in the DetailedAdvancementReportScoutsBSA.csv file and
   * update the Ship advancement data from this file.
   * 
   *****************************************************************************/
  public static function &UpdateShip(string $fileName)
  {
    
    $sqlShipInsertSt = "INSERT INTO `adv_ship`(`Scout`, `Tenderfoot`, `SecondClass`, `FirstClass`, `Star`, `Life`, `Eagle`, `YTD`,
    	`Palms`, `MeritBadge`, `Date`, `Unit`) 
        VALUES (";

    $Inserted = 0;
    $Updated = 0;
    $RecordsInError = 0;
    $row = 1;
    $filePath = $fileName;
        if (!file_exists($filePath) || !is_readable($filePath)) {
        error_log("UpdateShip: File not found or unreadable at $filePath");
        return ++$RecordsInError;
      }

    $Datestr = "";
    $ShipYear = self::GetYear();
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 0, ',', '"', '')) !== false) {
        if ($row < 11) { // Skip the first row(s), headers.
          if ($row == 7)
            $Datestr = $data[0]; // Get the report date.
          $row++;
          continue;
        }

        $ShipUnit = parent::formatUnitNumber($data[1], $data[2]);
        if ($ShipUnit == null || $ShipUnit[0] == 'T' || $ShipUnit[0] == 'P') {
          continue;
        }  // For some reason there can be pack and Ship data in this file.

        // Test to see if data is in database and then select either INSERT or UPDATE
        if (parent::InsertUpdateCheck($ShipYear, $ShipUnit)) {
          //Update Date
          $sqlUpdateShip = sprintf(
            "UPDATE `adv_ship` SET `Scout`='%s',`Tenderfoot`='%s', `SecondClass`='%s',`FirstClass`='%s',
    					`Star`='%s',`Life`='%s',`Eagle`='%s', `YTD`='%s',
    					`Palms`='%s',`MeritBadge`='%s',`Date`='%s'
    					WHERE `Unit`='%s' AND `Date`='%s'",
            $data[3],
            $data[5],
            $data[7],
            $data[9],
            $data[11],
            $data[13],
            $data[15],
            $data[17],
            $data[19],
            $data[21],
            $ShipYear,
            $ShipUnit,
            $ShipYear
          );

          if (!parent::doQuery($sqlUpdateShip)) {
            echo "Update Error: " . $sqlUpdateShip . "" . mysqli_error(parent::getDbConn()) . "<br />";
            $RecordsInError++;
          } else
            $Updated++;
          //Reset String
          $sqlUpdateShip = "";
        } else {
          // Insert Data
          $sqlInsertShip = "";
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[3]);  //Scout_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[5]);  //Tenderfoot_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[7]);  //Second_Class_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[9]);  //First_Class_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[11]);  //Star_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[13]);  //Life_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[15]);  //Eagle_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[17]);  //Total_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[19]);  //Palms_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $data[21]);  //Merit_Badges_YTD
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $ShipYear);  //Year
          $sqlInsertShip = $sqlInsertShip . sprintf("'%s', ", $ShipUnit);  //Unit

          $sqlInsertShip = substr($sqlInsertShip, 0, (strlen($sqlInsertShip) - 2));
          $sqlInsertShip =  $sqlShipInsertSt . $sqlInsertShip . ");";
          // Update the database
          if (!parent::doQuery($sqlInsertShip)) {
            echo "Insert Error: " . $sqlInsertShip . "" . mysqli_error(parent::getDbConn()) . "<br />";
            $RecordsInError++;
          } else
            $Inserted++;
          //Reset String
          $sqlInsertShip = "";
        }
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
      //parent::function_alert($Usermsg);
    } else {
      $Usermsg = "Failed to open file";
      //parent::function_alert($Usermsg);
    }
    parent::UpdateLastUpdated('adv_ship', $Datestr);

    return $RecordsInError;
  }
}
