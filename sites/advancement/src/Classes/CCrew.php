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
class CCrew extends CAdvancement
{
  private static $CrewTotals = array();
  private static $MemberTotal = array();
  private static $CrewDistrictGoal = 0; //Crews have no advancement goals
  private static $CrewIdealGoal = 0;    //Crews have no advancement goals
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetTotals()
  {
    $sqlTroopSum = sprintf('SELECT SUM(Scout),SUM(Tenderfoot),SUM(SecondClass),SUM(FirstClass),
            SUM(Star),SUM(Life),SUM(Eagle),SUM(Palms),SUM(YTD),SUM(MeritBadge),SUM(Discovery),
            SUM(Pathfinder), SUM(Summit), SUM(Venturing)
            FROM adv_crew WHERE Date=%d', parent::GetYear());

    $resultPackSum = parent::doQuery($sqlTroopSum, MYSQLI_STORE_RESULT);
    $RankTotal = $resultPackSum->fetch_assoc();

    self::$CrewTotals['Scout'] = $RankTotal['SUM(Scout)'];
    self::$CrewTotals['Tenderfoot'] = $RankTotal['SUM(Tenderfoot)'];
    self::$CrewTotals['SecondClass'] = $RankTotal['SUM(SecondClass)'];
    self::$CrewTotals['FirstClass'] = $RankTotal['SUM(FirstClass)'];
    self::$CrewTotals['Star'] = $RankTotal['SUM(Star)'];
    self::$CrewTotals['Life'] = $RankTotal['SUM(Life)'];
    self::$CrewTotals['Eagle'] = $RankTotal['SUM(Eagle)'];
    self::$CrewTotals['Palms'] = $RankTotal['SUM(Palms)'];
    self::$CrewTotals['YTD'] = $RankTotal['SUM(YTD)'];
    self::$CrewTotals['Youth'] = parent::GetProgramTotalYouth("Troop");
    self::$CrewTotals['MeritBadges'] = $RankTotal['SUM(MeritBadge)'];
    self::$CrewTotals['Discovery'] = $RankTotal['SUM(Discovery)'];
    self::$CrewTotals['Pathfinder'] = $RankTotal['SUM(Pathfinder)'];
    self::$CrewTotals['Summit'] = $RankTotal['SUM(Summit)'];
    self::$CrewTotals['Venturing'] = $RankTotal['SUM(Venturing)'];

    return self::$CrewTotals;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetMemberTotals()
  {
    $sqlPackSum = sprintf("SELECT SUM(Male_Youth), SUM(Female_Youth), SUM(Total_Youth), SUM(Male_Adults), SUM(Female_Adults), SUM(Total_Adults), SUM(Youth_Last_Year), SUM(Adults_Last_Year)
		FROM membershiptotals WHERE Expire_Date LIKE '%s%%' AND Unit LIKE 'Crew%%'", self::GetYear());

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
		FROM adv_crew WHERE Date LIKE '%s%%'", self::GetYear());

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
    return ((self::$CrewTotals['YTD'] + self::$CrewTotals['MeritBadges']) / self::$CrewTotals['Youth']);
  }
  /**************************************************************************
   **
   ** Retuern the district goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetDistrictGoal()
  {

    return self::$CrewDistrictGoal;
  }
  /**************************************************************************
   **
   ** Retuern the Ideal goals for packs. This number changed in 2021 with
   ** the Cub's having to earn rank + adventures.
   **
   *************************************************************************/
  public static function GetIdealGoal()
  {
    return self::$CrewIdealGoal;
  }

  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetNumofCrews()
  {
    $NumOfCrews = 0;

    $sql = sprintf('SELECT * FROM adv_crew WHERE Date=%d ORDER BY Unit ASC', parent::GetYear());

    if ($result = parent::doQuery($sql)) {
      $NumOfCrews = mysqli_num_rows($result);
    }
    return $NumOfCrews;
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
   * update the Crew advancement data from this file.
   * 
   *****************************************************************************/
  public static function &UpdateCrew($fileName)
  {
    
    $sqlCrewInsertSt = "INSERT INTO `adv_crew`(`Scout`, `Tenderfoot`, `SecondClass`, `FirstClass`, `Star`, `Life`, `Eagle`, `YTD`,
    	`Palms`, `MeritBadge`, `Date`, `Unit`) 
        VALUES (";

    $Inserted = 0;
    $Updated = 0;
    $RecordsInError = 0;
    $row = 1;
    $filePath = $fileName;
        if (!file_exists($filePath) || !is_readable($filePath)) {
        error_log("UpdateCrew: File not found or unreadable at $filePath");
        return ++$RecordsInError;
      }

    $Datestr = "";
    $CrewYear = self::GetYear();
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 0, ',', '"', '')) !== false) {
        if ($row < 11) { // Skip the first row(s), headers.
          if ($row == 7)
            $Datestr = $data[0]; // Get the report date.
          $row++;
          continue;
        }

        $CrewUnit = parent::formatUnitNumber($data[1], $data[2]);
        if ($CrewUnit == null || $CrewUnit[0] == 'T' || $CrewUnit[0] == 'P') {
          continue;
        }  // For some reason there can be pack and crew data in this file.

        // Test to see if data is in database and then select either INSERT or UPDATE
        if (parent::InsertUpdateCheck($CrewYear, $CrewUnit)) {
          //Update Date
          $sqlUpdateCrew = sprintf(
            "UPDATE `adv_crew` SET `Scout`='%s',`Tenderfoot`='%s', `SecondClass`='%s',`FirstClass`='%s',
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
            $CrewYear,
            $CrewUnit,
            $CrewYear
          );

          if (!parent::doQuery($sqlUpdateCrew)) {
            echo "Update Error: " . $sqlUpdateCrew . "" . mysqli_error(parent::getDbConn()) . "<br />";
            $RecordsInError++;
          } else
            $Updated++;
          //Reset String
          $sqlUpdateCrew = "";
        } else {
          // Insert Data
          $sqlInsertCrew = "";
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[3]);  //Scout_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[5]);  //Tenderfoot_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[7]);  //Second_Class_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[9]);  //First_Class_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[11]);  //Star_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[13]);  //Life_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[15]);  //Eagle_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[17]);  //Total_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[19]);  //Palms_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $data[21]);  //Merit_Badges_YTD
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $CrewYear);  //Year
          $sqlInsertCrew = $sqlInsertCrew . sprintf("'%s', ", $CrewUnit);  //Unit

          $sqlInsertCrew = substr($sqlInsertCrew, 0, (strlen($sqlInsertCrew) - 2));
          $sqlInsertCrew =  $sqlCrewInsertSt . $sqlInsertCrew . ");";
          // Update the database
          if (!parent::doQuery($sqlInsertCrew)) {
            echo "Insert Error: " . $sqlInsertCrew . "" . mysqli_error(parent::getDbConn()) . "<br />";
            $RecordsInError++;
          } else
            $Inserted++;
          //Reset String
          $sqlInsertCrew = "";
        }
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
      //parent::function_alert($Usermsg);
    } else {
      $Usermsg = "Failed to open file";
      //parent::function_alert($Usermsg);
    }
    parent::UpdateLastUpdated('adv_crew', $Datestr);

    return $RecordsInError;
  }
  /******************************************************************************
   * 
   * This will update the Venturing awards
   * 
   *****************************************************************************/
  public static function &UpdateVenturing($fileName)
  {
    $col_distorgname = 0;
    $col_organizationname = 1;
    $col_unitid = 2;
    $col_venturingdiscoveryawardmtd = 3;
    $col_venturingdiscoveryawardytd = 4;
    $col_venturingpathfinderawardmtd = 5;
    $col_venturingpathfinderawardytd = 6;
    $col_venturingsummitawardmtd = 7;
    $col_venturingsummitawardytd =8 ;
    $col_venturingawardmtd = 9;
    $col_venturingawardytd = 10;
    $col_totalmtd = 11;
    $col_totalytd = 12;
    $col_youthqtt = 13;

    $Inserted = 0;
    $Updated = 0;
    $RecordsInError = 0;
    $row = 1;
    //$filePath = "Data/" . $fileName;
    $Datestr = "";
    //$CrewYear = date("Y");
    $CrewYear = parent::GetYear();
    if (($handle = fopen($fileName, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 0, ',', '"', '')) !== false) {
        if ($row < 11) { // Skip the first row(s), headers.
          if ($row == 7)
            $Datestr = $data[0]; // Get the report date.
          $row++;
          continue;
        }

        $CrewUnit = parent::formatUnitNumber($data[$col_organizationname], $data[$col_organizationname]);
        if ($data[$col_organizationname][0] == 'C' && $data[$col_organizationname][1] == 'r') {  // For some reason there can be Troop data in this file.

          // Test to see if data is in database and then select either INSERT or UPDATE
          if (parent::InsertUpdateCheck($CrewYear, $CrewUnit)) {
            //Update Date
           //parent::UpdateMembership($data[$col_distorgname],  $CrewUnit, $data[$col_unitid], $data[$col_youthqtt]);
            $sqlUpdateCrew = sprintf(
              "UPDATE `adv_crew` SET `discovery`='%s',`pathfinder`='%s', `summit`='%s',`venturing`='%s', `UnitID`='', `Youth`=''
        					WHERE `Unit`='%s' AND `Date`='%s'",
              $data[$col_venturingdiscoveryawardytd],
              $data[$col_venturingpathfinderawardytd],
              $data[$col_venturingsummitawardytd],
              $data[$col_venturingawardytd],
              $data[$col_unitid],
              $data[$col_youthqtt],
              $CrewUnit,
              $CrewYear
            );

            if (!parent::doQuery($sqlUpdateCrew)) {
              echo "Update Error: " . $sqlUpdateCrew . "" . mysqli_error(parent::getDbConn()) . "<br />";
              $RecordsInError++;
            } else
              $Updated++;
            //Reset String
            $sqlUpdateCrew = "";
          } 
          else {
            // Insert Data
            ///parent::UpdateMembership($data[$col_distorgname],  $CrewUnit, $data[$colUnitID], $data[$colYouthTotal]);
            $sqlInsertCrew = sprintf("INSERT INTO `adv_crew`(`discovery`, `pathfinder`, `summit`, `venturing`, `Date`, `YTD`, `Youth`, `Unit`, `UnitID`) 
              VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s')",
              
              $data[$col_venturingdiscoveryawardytd],
              $data[$col_venturingpathfinderawardytd],
              $data[$col_venturingsummitawardytd],
              $data[$col_venturingawardytd],
              $CrewYear,
              $data[$col_totalytd],
              $data[$col_youthqtt],
              $CrewUnit,
              $data[$col_unitid],
              );

              if (!parent::doQuery($sqlInsertCrew)) {
                echo "Update Error: " . $sqlInsertCrew . "" . mysqli_error(parent::getDbConn()) . "<br />";
                $RecordsInError++;
              } else
                $Updated++;
          }
        }
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
      //parent::function_alert($Usermsg);
    } else {
      $Usermsg = "Failed to open file";
      //parent::function_alert($Usermsg);
    }
    parent::UpdateLastUpdated('adv_crew', $Datestr);

    return $RecordsInError;
  }
}
