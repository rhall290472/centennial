<?php
/*
!==============================================================================!
!\                                                                            /!
!\\                                                                          //!
! \##########################################################################/ !
!  #         This is Proprietary Software of Richard Hall                   #  !
!  ##########################################################################  !
!  #                                                                        #  !
!  #  FILE NAME   :  CScout.php                                             #  !
!  #                                                                        #  !
!  #  DESCRIPTION :  Website to Support Merit Badge College.                #  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #  REFERENCES  :                                                         #  !
!  #                                                                        #  !
!  #                                                                        #  !
!  #  CHANGE HISTORY ;                                                      #  !
!  #                                                                        #  !
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

include_once "CMBCollege.php";
/**
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 */
class CScout extends CMBCollege
{
  private $LastName;
  private $FirstName;
  private $Phone;
  public  $College;
  private $BSAId;
  private $MBName = array();
  private $MBPeriod = array();
  private $Registration;
  private $District;
  private $UnitType;
  private $UnitNumber;
  private $Gender;
  private $Email;
  private $MB_Count = 0;
  private $nIndex = -1;
  private $FakeBSAID = -1;
  private $CounselorFirst = array();
  private $CounselorLast = array();
  private $CounselorEmail = array();
  private $MBAttend = array();
  private $N1BSAId = null;

  /******************************************************************************
   *
   * This function will search 
   *
   * 
   *****************************************************************************/
  public function IsSignedUp($Year, $LastName, $FirstName, $SelectedScout)
  {
    $bReturn = false;
    //$this->nIndex = -1;

    $sqlEdit = "SELECT * FROM `college_registration` 
		    INNER JOIN college_counselors ON college_registration.MeritBadge=college_counselors.MBName AND college_registration.Period=college_counselors.MBPeriod
            AND college_registration.College=college_counselors.College
		    WHERE college_registration.College='$Year' AND college_registration.FirstNameScout='$FirstName' AND college_registration.LastNameScout='$LastName'";

    //$sqlEdit = sprintf("SELECT * FROM `college_registration` WHERE `College`='%s' AND `LastNameScout`='%s' AND `FirstNameScout`='%s' ORDER BY `Period`",
    //    $Year, $LastName, $FirstName);

    if (!$EditMB = self::doQuery($sqlEdit)) {
      $msg = "Error: MeritQuery() " . $sqlEdit;
      self::function_alert($msg);
    } else if (mysqli_num_rows($EditMB) > 0) {
      $this->College = $Year;
      $this->LastName = $LastName;
      $this->FirstName = $FirstName;
      $nLoop = 0;
      while ($Results = $EditMB->fetch_assoc()) {
        $this->MBName[$nLoop] = $Results['MeritBadge'];
        $this->MBPeriod[$nLoop] = $Results['Period'];
        $this->CounselorFirst[$nLoop] = $Results['FirstName'];
        $this->CounselorLast[$nLoop] = $Results['LastName'];
        $this->CounselorEmail[$nLoop] = $Results['Email'];
        $this->MBAttend[$nLoop] = $Results['didnotattend'];
        $nLoop++;
      }
      $this->MB_Count = $nLoop;

      $bReturn = true;
    }
    return $bReturn;
  }
  /******************************************************************************
   *
   * This function will search to see if scout has signed up for badge
   *
   * 
   *****************************************************************************/
  public function IsSignedUpMB($Year, $LastName, $FirstName, $MBName)
  {
    $bReturn = false;

    $sqlEdit = "SELECT * FROM `college_registration` 
		    INNER JOIN college_counselors ON college_registration.MeritBadge=college_counselors.MBName AND college_registration.Period=college_counselors.MBPeriod
            AND college_registration.College=college_counselors.College
		    WHERE college_registration.College='$Year' AND college_registration.FirstNameScout='$FirstName' AND college_registration.LastNameScout='$LastName'
            AND college_registration.MeritBadge='$MBName'";


    if (!$EditMB = self::doQuery($sqlEdit)) {
      $msg = "Error: MeritQuery() " . $sqlEdit;
      self::function_alert($msg);
    } else if (mysqli_num_rows($EditMB) > 0) {
      $this->College = $Year;
      $this->LastName = $LastName;
      $this->FirstName = $FirstName;
      $nLoop = 0;
      while ($Results = $EditMB->fetch_assoc()) {
        $this->BSAId = $Results['BSAIdScout'];
        $this->MBName[$nLoop] = $Results['MeritBadge'];
        $this->MBPeriod[$nLoop] = $Results['Period'];
        $this->CounselorFirst[$nLoop] = $Results['FirstName'];
        $this->CounselorLast[$nLoop] = $Results['LastName'];
        $this->CounselorEmail[$nLoop] = $Results['Email'];
        $this->MBAttend[$nLoop] = $Results['didnotattend'];
        $nLoop++;
      }
      $this->MB_Count = $nLoop;

      $bReturn = true;
    }
    return $bReturn;
  }
  /******************************************************************************
   *
   * This function will search to see if scout has signed up for badge
   *
   * 
   *****************************************************************************/
  public function IsSignedUpPeriod($Year, $LastName, $FirstName, $MBPeriod)
  {
    $bReturn = false;

    $sqlEdit = "SELECT * FROM `college_registration` 
		    INNER JOIN college_counselors ON college_registration.MeritBadge=college_counselors.MBName AND college_registration.Period=college_counselors.MBPeriod
            AND college_registration.College=college_counselors.College
		    WHERE college_registration.College='$Year' AND college_registration.FirstNameScout='$FirstName' AND college_registration.LastNameScout='$LastName'
            AND college_registration.Period='$MBPeriod'";


    if (!$EditMB = self::doQuery($sqlEdit)) {
      $msg = "Error: MeritQuery() " . $sqlEdit;
      self::function_alert($msg);
    } else if (mysqli_num_rows($EditMB) > 0) {
      $this->College = $Year;
      $this->LastName = $LastName;
      $this->FirstName = $FirstName;
      $nLoop = 0;
      while ($Results = $EditMB->fetch_assoc()) {
        $this->BSAId = $Results['BSAIdScout'];
        $this->MBName[$nLoop] = $Results['MeritBadge'];
        $this->MBPeriod[$nLoop] = $Results['Period'];
        $this->CounselorFirst[$nLoop] = $Results['FirstName'];
        $this->CounselorLast[$nLoop] = $Results['LastName'];
        $this->CounselorEmail[$nLoop] = $Results['Email'];
        $this->MBAttend[$nLoop] = $Results['didnotattend'];
        $nLoop++;
      }
      $this->MB_Count = $nLoop;

      $bReturn = true;
    }
    return $bReturn;
  }
  /******************************************************************************
   *
   * Needs to call after IsSignedUp()
   *
   *****************************************************************************/
  public function Delete()
  {

    // TODO:
    $bReturn = false;
    $sqlDelete = sprintf(
      "DELETE FROM `college_registration` WHERE `College`='%s' AND 
            `LastNameScout`='%s' AND `FirstNameScout`='%s'",
      $this->College,
      $this->LastName,
      $this->FirstName
    );

    if (!self::doQuery($sqlDelete)) {
      $bReturn = false;
      $msg = "Error: MeritQuery() " . $sqlDelete;
      self::function_alert($msg);
    } else
      $bReturn = true;

    return $bReturn;
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public function AddInfo(
    $FirstName,
    $LastName,
    $Email,
    $Phone,
    $BSAId,
    $MBCollegeName,
    $Registration,
    $District,
    $UnitType,
    $UnitNumber,
    $Gender
  ) {

    $this->FirstName = $FirstName;
    $this->LastName = $LastName;
    $this->Email = $Email;
    $this->Phone = $Phone;
    if ($BSAId == 0)
      $BSAId = $this->FakeBSAID--;
    $this->BSAId = $BSAId;
    $this->College = $MBCollegeName;
    $this->Registration = $Registration;
    $this->District = $District;
    $this->UnitType = $UnitType;
    $this->UnitNumber = $UnitNumber;
    $this->Gender = $Gender;
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public function UpdateInfo(
    $FirstName,
    $LastName,
    $Email,
    $Phone,
    $BSAId,
    $MBCollegeName,
    $Registration,
    $District,
    $UnitType,
    $UnitNumber,
    $Gender
  ) {

    $this->FirstName = $FirstName;
    $this->LastName = $LastName;
    $this->Email = $Email;
    $this->Phone = $Phone;
    //$this->BSAId = $BSAId;
    $this->College = $MBCollegeName;
    $this->Registration = $Registration;
    $this->District = $District;
    $this->UnitType = $UnitType;
    $this->UnitNumber = $UnitNumber;
    $this->Gender = $Gender;
  }

  /*=============================================================================
     *
     * This function will add a merit badge to the college list of available merit
     * badges.
     * 
     *===========================================================================*/
  public function AddMBClass($MBName, $MBPeriod, $DoNotAttend)
  {

    $bError = FALSE;
    $this->LastName = addslashes($this->LastName);

    $sqlUpdate = sprintf(
      "INSERT INTO `college_registration`(`Registration`, `College`, `FirstNameScout`, `LastNameScout`, `District`, `UnitType`, `UnitNumber`, `BSAIdScout`, `Gender`, 
                    `Telephone`, `email`, `MeritBadge`, `Period`, `didnotattend`) VALUES ('$this->Registration','$this->College','$this->FirstName', '$this->LastName','$this->District',
                    '$this->UnitType','$this->UnitNumber','$this->BSAId', '$this->Gender', '$this->Phone','$this->Email','$MBName','$MBPeriod', '$DoNotAttend')"
    );

    if (!self::doQuery($sqlUpdate)) {
      $bError = TRUE;
      self::function_alert("Error: AddMBClass()");
      error_log("Error: AddMBClass() - " . $sqlUpdate);
    }
    $str = sprintf("New signup %s, at %s\n", $sqlUpdate, Date('Y-m-d H:i:s'));
    return $bError;
  }
  /*=============================================================================
     *
     * This function will add a merit badge to the college list of available merit
     * badges.
     * 
     *===========================================================================*/
  public function UpdateMBClass($MBName, $MBPeriod)
  {

    $bError = FALSE;
    // TODO: change from insert to update ...
    $sqlUpdate = sprintf("UPDATE `college_registration` SET `Registration`='$this->Registration',`College`='$this->College',`FirstNameScout`='$this->FirstName',
            `LastNameScout`='$this->LastName',`UnitType`='$this->UnitType',`UnitNumber`='$this->UnitNumber',`BSAIdScout`='$this->BSAId',`Gender`='$this->Gender',
            `Telephone`='$this->Phone',`email`='$this->Email',`MeritBadge`='$MBName',`Period`='$MBPeriod' WHERE `FirstNameScout`='$this->FirstName' AND
            `LastNameScout`='$this->LastName' AND `Period`='$MBPeriod' AND `College`='$this->College'");

    /*
                    "INSERT INTO `college_registration`(`Registration`, `College`, `FirstNameScout`, `LastNameScout`, `District`, `UnitType`, `UnitNumber`, `BSAIdScout`, `Gender`, 
                    `Telephone`, `email`, `MeritBadge`, `Period`) VALUES ('$this->Registration','$this->College','$this->FirstName', '$this->LastName','$this->District',
                    '$this->UnitType','$this->UnitNumber','$this->BSAId', '$this->Gender', '$this->Phone','$this->Email','$MBName','$MBPeriod')");
        */
    if (!self::doQuery($sqlUpdate)) {
      $bError = TRUE;
      self::function_alert("Error: UpdateMBClass()");
    }
    $str = sprintf("Updated signup %s, at %s\n", $sqlUpdate, Date('Y-m-d H:i:s'));
    //error_log($str, 1, "richard.hall@centennialdistrict.co");
    return $bError;
  }

  /******************************************************************************
   *
   * This function will check the supplied merit badge against the one the counselor
   * has signed up for and return true if found.
   *
   * THIS functions set the $nIndex label which all others calls are based on.
   * THIS function must be called before the others.
   *
   *****************************************************************************/
  public function MB_Match($MeritNamee, $Index)
  {
    $bReturn = false;
    $Index--; // Zero based

    //for($k = $Index-1; $k < $this->MB_Count; $k++){
    if ($Index < $this->MB_Count && !strcmp($this->MBName[$Index], $MeritNamee)) {
      // We have a match.
      $this->nIndex = $Index;
      $bReturn = true;
      //break;
    }
    //}
    return $bReturn;
  }

  /******************************************************************************
   *
   * This function will check the period match counselor signup
   *
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  public function Period_Match($Period, $nPeriod)
  {
    $bReturn = false;

    if (($nPeriod <= $this->nIndex) && !strcmp($this->MBPeriod[$nPeriod], $Period))
      if (!strcmp($this->MBPeriod[$nPeriod], $Period))
        $bReturn = true;

    return $bReturn;
  }
  public function DisplayPeriods($nPeriod, $CollegeYear)
  {
    $nPeriod = $nPeriod - 1;  // nIndex is zero based

    //        $CollegeYear = parent::getYear();
    $Times = '';
    $qryTimes = "SELECT * FROM `college_details` WHERE `College`='$CollegeYear'";
    $resultTimes = self::doQuery($qryTimes, $CollegeYear);
    $rowTime = $resultTimes->fetch_assoc();

    if (self::GetPeriodATime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('A', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='A'>Period A - %s</option>", $strSelected, $rowTime['PeriodA']);
    }
    if (self::GetPeriodBTime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('B', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='B'>Period B - %s</option>", $strSelected, $rowTime['PeriodB']);
    }
    if (self::GetPeriodCTime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('C', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='C'>Period C - %s</option>", $strSelected, $rowTime['PeriodC']);
    }
    if (self::GetPeriodDTime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('D', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='D'>Period D - %s</option>", $strSelected, $rowTime['PeriodD']);
    }
    if (self::GetPeriodABTime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('AB', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='AB'>Period A-B - %s</option>", $strSelected, $rowTime['PeriodAB']);
    }
    if (self::GetPeriodCDTime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('CD', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='CD'>Period C-D - %s</option>", $strSelected, $rowTime['PeriodCD']);
    }
    if (self::GetPeriodETime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('E', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='E'>Period E - %s</option>", $strSelected, $rowTime['PeriodE']);
    }
    if (self::GetPeriodFTime($CollegeYear) != null) {
      $strSelected = $this->Period_Match('F', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='F'>Period F - %s</option>", $strSelected, $rowTime['PeriodF']);
    }
  }

  public static function PeriodTime($nPeriod)
  {
    $CollegeYear = parent::getYear();
    $Times = '';
    $qryTimes = "SELECT * FROM `college_details` WHERE `College`='$CollegeYear'";
    $resultTimes = self::doQuery($qryTimes, $CollegeYear);
    $rowTime = $resultTimes->fetch_assoc();

    if (!strcmp('A', $nPeriod))
      $Times = 'A ' . $rowTime['PeriodA'];
    else if (!strcmp('B', $nPeriod))
      $Times = 'B ' . $rowTime['PeriodB'];
    else if (!strcmp('C', $nPeriod))
      $Times = 'C ' . $rowTime['PeriodC'];
    else if (!strcmp('D', $nPeriod))
      $Times = 'D ' . $rowTime['PeriodD'];
    else if (!strcmp('AB', $nPeriod))
      $Times = 'AB ' . $rowTime['PeriodAB'];
    else if (!strcmp('CD', $nPeriod))
      $Times = 'CD ' . $rowTime['PeriodCD'];
    else if (!strcmp('E', $nPeriod))
      $Times = 'E ' . $rowTime['PeriodE'];
    else if (!strcmp('F', $nPeriod))
      $Times = 'F ' . $rowTime['PeriodF'];


    return $Times;
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayDistrict($District)
  {

    $strSelected = !strcmp("Alpine", $District) ? "selected" : "";
    echo sprintf("<option %s value='Alpine'>Alpine</option>", $strSelected);
    $strSelected = !strcmp("BlackFeather", $District) ? "selected" : "";
    echo sprintf("<option %s value='BlackFeather'>Black Feather</option>", $strSelected);
    $strSelected = !strcmp("Centennial", $District) ? "selected" : "";
    echo sprintf("<option %s value='Centennial'>Centennial</option>", $strSelected);
    $strSelected = !strcmp("Frontier", $District) ? "selected" : "";
    echo sprintf("<option %s value='Frontier'>Frontier</option>", $strSelected);
    $strSelected = !strcmp("MajesticMesas", $District) ? "selected" : "";
    echo sprintf("<option %s value='MajesticMesas'>Majestic Mesas</option>", $strSelected);
    $strSelected = !strcmp("ThreeRivers", $District) ? "selected" : "";
    echo sprintf("<option %s value='ThreeRivers'>Three Rivers</option>", $strSelected);
    $strSelected = !strcmp("Valley", $District) ? "selected" : "";
    echo sprintf("<option %s value='Valley'>Valley</option>", $strSelected);
    $strSelected = !strcmp("Other", $District) ? "selected" : "";
    echo sprintf("<option %s value='Other'>Other</option>", $strSelected);
    $strSelected = !strcmp("Unknow", $District) ? "selected" : "";
    echo sprintf("<option %s value='Unknow'>Unknow</option>", $strSelected);
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayUnitType($UnitType)
  {

    $strSelected = !strcmp("Crew-NA", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Crew-NA'>Crew-NA</option>", $strSelected);
    $strSelected = !strcmp("Pack-BP", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Pack-BP'>Pack-BP</option>", $strSelected);
    $strSelected = !strcmp("Pack-FP", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Pack-FP'>Pack-FP</option>", $strSelected);
    $strSelected = !strcmp("Post-NA", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Post-NA'>Post-NA</option>", $strSelected);
    $strSelected = !strcmp("Ship-NA", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Ship-NA'>Ship-NA</option>", $strSelected);
    $strSelected = !strcmp("Troop-B", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Troop-B'>Troop-B</option>", $strSelected);
    $strSelected = !strcmp("Troop-G", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Troop-G'>Troop-G</option>", $strSelected);
    $strSelected = !strcmp("LoneScout", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='LoneScout'>LoneScout</option>", $strSelected);
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayGender($Gender)
  {
    $strSelected = !strcmp("Female", $Gender) ? "selected" : "";
    echo sprintf("<option %s value='Female'>Female</option>", $strSelected);
    $strSelected = !strcmp("Male", $Gender) ? "selected" : "";
    echo sprintf("<option %s value='Male'>Male</option>", $strSelected);
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public function GetCounselorData($Results, $Badge)
  {
    //$Badge--;   // Index is zero based
    $FirstName = '';
    $FirstName = '';
    $LastName = '';
    $Email = '';


    if ($this->nIndex != -1 && mysqli_num_rows($Results) > $Badge) {
      $row = $Results->data_seek($Badge - 1);
      $row = $Results->fetch_assoc();
      $FirstName = $row['FirstName'];
      $LastName = $row['LastName'];
      $Email = $row['Email'];
    }

    return array($FirstName, $LastName, $Email);
  }
  public function GetAttend($MBNumber)
  {
    $Attend = "unchecked";
    if ($MBNumber <= count($this->MBAttend))
      if ($this->MBAttend[$MBNumber - 1] == 1)
        $Attend = "checked";
    return $Attend;
  }
  /******************************************************************************
   *
   * This function will search 
   *
   * 
   *****************************************************************************/
  public static function EmailSchedule()
  {
    //https://www.tutorialrepublic.com/php-tutorial/php-send-email.php

    $to = "rhall290472@gmail.com";
    $subject = "Centennial-Black Feather Merit Badge College";
    $from = "richard.hall@centennialdistrict.co";

    //$message .= "<b>Thank You for registering your Scout into the Centennial-Black Feather Merit Badge College</b>";
    //$message .= "<h1>Please find attached your scouts schedule and PLEASE note the time changes.</h1>";
    //$message .= "</body></html>";
    //
    //// In case any of our lines are larger than 70 characters, we should use wordwrap()
    //$message = wordwrap($message, 70, "\r\n");
    //$message = "<html><body>";

    //https://www.codexworld.com/send-beautiful-html-email-using-php/
    $htmlContent = ' 
        <html> 
        <head> 
            <title>Welcome to CodexWorld</title> 
        </head> 
        <body> 
            <h1>Thanks you for joining with us!</h1> 
            <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
                <tr> 
                    <th>Name:</th><td>CodexWorld</td> 
                </tr> 
                <tr style="background-color: #e0e0e0;"> 
                    <th>Email:</th><td>contact@codexworld.com</td> 
                </tr> 
                <tr> 
                    <th>Website:</th><td><a href="http://www.codexworld.com">www.codexworld.com</a></td> 
                </tr> 
            </table> 
        </body> 
        </html>';


    // Get HTML contents from file 
    //$htmlContent = file_get_contents("email_template.html");

    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0';
    $headers .= 'Content-type: text/html; charset=iso-8859-1';
    // Create email headers
    $headers .= 'From: ' . $from . "\r\n" .
      'Reply-To: ' . $from . "\r\n" .
      'X-Mailer: PHP/' . phpversion();

    // Additional headers
    //$headers[] = 'To: richard.hall@centennialdistrict.co';
    //$headers[] = 'From: richard.hall@centennialdistrict.co';
    //$header = 'From: richard.hall@centennialdistrict.co' . "\r\n".
    //    'Reply-To: richard.hall@centennialdistrict.co' . "\r\n" .
    //    'X-Mailer: PHP/' . phpversion();

    $retval = mail($to, $subject, $htmlContent, $headers);

    if ($retval == true) {
      echo "Message sent successfully...";
    } else {
      echo "Message could not be sent...";
    }
  }
  /*=============================================================================
    *
    * This function will produce a schedule of Scouts Merit Badge Classes 
    * 
    *===========================================================================*/
  public function ReportScoutMeritBadges($report_results, $CollegeYear)
  {
    $Fname = "";
    $Lname = "";
    $Expired = false;
    $TodaysDate = strtotime("now");
    $ScoutID = "";
    $Scout = new cScout;
    ?>

    <?php
    $i = 0;
    while ($row = $report_results->fetch_assoc()) {
      // If New Scout, shutdown old table and create a new one.
      if ($ScoutID != $row['BSAIdScout']) {
        $ScoutID = $row['BSAIdScout'];
        echo "</table>";

        // Three scouts per page.
        if ($i++ >= 3) {
          $i = 0;
          ?>
          <p style="page-break-before: always;">&nbsp;</p>
           <?php
        }

        if ($row['Registration'] <= 0)
          $Formatter = "<b style='color:green;'>";
        else
          $Formatter = "<b style='color:black;'>";
        echo "<br>";
        echo "<h2>" . $Formatter . $row['FirstNameScout'] . " " . $Formatter . $row['LastNameScout'] . "</h2>";

        $District = $row['District'];
        $UnitType = $row['UnitType'];
        $UnitNumber = $row['UnitNumber'];
        $BSAIdScout = $row['BSAIdScout'];
        
        echo "<b> District: " . $District . " Unit: " . $UnitType . " " . $UnitNumber . " BSA Id#: " . $BSAIdScout . "</b>";
        ?>
        <table class='table'  style='width:1024';>
        <td style='width:150px'>
        <td style='width:200px'>
        <td style='width:100px'>
        <td style='width:10px'>
        <td style='width:10px'>
        <tr>
        <th>Period</th>
        <th>Merit badge</th>
        <th>Counselor</th>
        <th>Email</th>
        <th>Room</th>
        </tr>
        <?php
      }
      // Now get the Counselor data for the Merit Badge.
      $qryByPeriod = sprintf("SELECT * FROM college_counselors 
               WHERE MBName='%s' AND MBPeriod='%s' AND College='%s'", $row['MeritBadge'], $row['Period'], $CollegeYear);
      $resultByPeriod = self::doQuery($qryByPeriod, $CollegeYear);
      $rowPeriod = $resultByPeriod->fetch_assoc();

      if (mysqli_num_rows($resultByPeriod) == 0) {
        // PROBLEM !! No Counselor found for this merit badge in selected Period !!!
        $Formatter = "<b style='color:red;'>";
        $FirstName = "";
        $LastName = "";
        $Email = "";
        $Room = "";
      } else {
        $Formatter = "";
        $FirstName = $rowPeriod['FirstName'];
        $LastName = $rowPeriod['LastName'];
        $Email = $rowPeriod['Email'];
        $Room = $rowPeriod['MBRoom'];
      }

      $PeriodTime = $Scout->PeriodTime($row['Period']);

      echo "<tr><td>" .
        $Formatter . $PeriodTime . "</td><td>" .
        $Formatter . $row['MeritBadge'] . "</td><td>" .
        $Formatter . $FirstName . " " . $LastName . "</td><td>" .
        $Formatter . $Email . "</td><td>" .
        $Formatter . $Room . "</td></tr>";
    }
    echo "</table>";
  }
  /*=============================================================================
    *
    * This function will produce a schedule of Scouts Merit Badge Classes 
    * 
    *===========================================================================*/
  public function ShowScoutMeritBadges($report_results, $CollegeYear)
  {
    $Fname = "";
    $Lname = "";
    $Expired = false;
    $TodaysDate = strtotime("now");
    $ScoutID = "";
    $Scout = new cScout;
    //$CollegeYear = $GLOBALS["MBCollegeYear"];
    ?>
    <!-- <p style="page-break-after: always;">&nbsp;</p> -->
    <!-- <div style="page-break-inside:avoid;page-break-after:always"></div> -->
    <div class="pagebreak"> </div>
    <?php
    $i = 0;
    while ($row = $report_results->fetch_assoc()) {
      // If New Scout, shutdown old table and create a new one.
      if ($ScoutID != $row['BSAIdScout']) {
        $ScoutID = $row['BSAIdScout'];
        echo "</table>";

        // Three scouts per page.
        if ($i++ >= 3) {
          $i = 0;
    ?>
          <p style="page-break-after: always;">&nbsp;</p>
    <?php
        }

        if ($row['Registration'] <= 0)
          $Formatter = "<b style='color:green;'>";
        else
          $Formatter = "<b style='color:black;'>";
        echo "<br>";
        echo "<h2>" . $Formatter . $row['FirstNameScout'] . " " . $Formatter . $row['LastNameScout'] . "</h2>";

        echo "<table class='table'  style='width:90vw';>";
        echo "<td >";
        echo "<td >";
        echo "<td >";
        echo "<td >";
        echo "<tr>";
        echo "<th>Period</th>";
        echo "<th>Merit badge</th>";
        echo "<th>Counselor</th>";
        echo "<th>Room</th>";
        echo "</tr>";
      }
      // Now get the Counselor data for the Merit Badge.
      $qryByPeriod = sprintf("SELECT * FROM college_counselors 
                WHERE MBName='%s' AND MBPeriod='%s' AND College='%s'", $row['MeritBadge'], $row['Period'], $CollegeYear);
      $resultByPeriod = self::doQuery($qryByPeriod, $CollegeYear);
      $rowPeriod = $resultByPeriod->fetch_assoc();

      if (mysqli_num_rows($resultByPeriod) == 0) {
        // PROBLEM !! No Counselor found for this merit badge in selected Period !!!
        $Formatter = "<b style='color:red;'>";
        $FirstName = "";
        $LastName = "";
        $Email = "";
        $Room = "";
      } else {
        $Formatter = "";
        $FirstName = $rowPeriod['FirstName'];
        $LastName = $rowPeriod['LastName'];
        $Room = $rowPeriod['MBRoom'];
      }

      $PeriodTime = $Scout->PeriodTime($row['Period']);

      echo "<tr><td>" .
        $Formatter . $PeriodTime . "</td><td>" .
        $Formatter . $row['MeritBadge'] . "</td><td>" .
        $Formatter . $FirstName . " " . $LastName . "</td><td>" .
        $Formatter . $Room . "</td></tr>";
    }
    echo "</table>";
  }
  /*=============================================================================
    *
    * This function will email a schedule of Scouts Merit Badge Classes 
    * 
    *===========================================================================*/
  //public function EmailReportMeritBadges($report_results, $bPreview){
  //   $ScoutID = "";
  //   $Scout = new cScout;
  //   $CollegeYear = getYear();
  //   $htmlMessage = "";
  //   $to = "";
  //   $head = "";
  //   $subject  = "";
  //   $ScoutFirst = "";
  //   $ScoutLast = "";
  //   $bFirstPass = true;
  //
  //   while ($ScoutRow = $report_results->fetch_assoc()) {
  //
  //       // If New Scout, shutdown old table and create a new one.
  //       if ($ScoutID != $ScoutRow['BSAIdScout']) {
  //
  //
  //           $ScoutID = $ScoutRow['BSAIdScout'];
  //
  //           if($bFirstPass)
  //               $bFirstPass = false;
  //           else{
  //               $htmlMessage .= "</table>";
  //           
  //               $retval = mail ($to,$subject,$htmlMessage,$head);
  //               //$retval = false;
  //               if( $retval == true ) {
  //                   echo "</br>Message sent successfully...To: ".$to."-".$ScoutFirst. " ". $ScoutLast."</br>";
  //               }else {
  //                   echo "</br><b style='color:red;'>Message could not be sent...To: ".$to."-".$ScoutFirst. " ". $ScoutLast."</br>";
  //               }
  //           }
  //   
  //
  //           $to = $ScoutRow['email'];
  //           //$to = "rhall290472@gmail.com";
  //           //$subject = "Centennial-Black Feather Merit Badge College ".$ScoutRow['email'];
  //           $subject = "Centennial-Black Feather Merit Badge College ";
  //
  //UnitNumber
  //           
  //           // Create email headers
  //           $head = implode("\r\n", [
  //               "MIME-Version: 1.0",
  //               "Content-type: text/html; charset=utf-8",
  //               "Bcc: richard.hall@centennialdistrict.co"
  //             ]);
  //
  //
  //
  //           //Start new html message here...
  //
  //
  //           //$htmlMessage  = "<html><body>";
  //           $District = $ScoutRow['District'];
  //           $UnitType = $ScoutRow['UnitType'];
  //           $UnitNumber = $ScoutRow['UnitNumber'];
  //           $BSAIdScout = $ScoutRow['BSAIdScout'];
  //           $ScoutFirst = $ScoutRow['FirstNameScout'];
  //           $ScoutLast = $ScoutRow['LastNameScout'];
  //
  //           // Message to scout/parents
  //             $htmlMessage  = "<p>Thank you for registering your scout for the Centenninal-Black Feather Merit Badge College ";
  //             $htmlMessage .= "below you will find your scouts schedule and please <b>NOTE</b> the times have changed. This was do to ";
  //             $htmlMessage .= "a request from our host (Regis) to be finished with the college by 3:30pm. Checkin for the college ";
  //             $htmlMessage .= "will start 30-minutes before the schedule class time.</p>";
  //             $htmlMessage .= "<h2>This email now includes the room numbers for your scout(s) class(es)</h2> ";
  //             $htmlMessage .= "<p>Please verify your Scouts BSA ID#, it will be used to connect your scout to the counselor</p>";
  //             $htmlMessage .= "<p>If you have any questions please contact <a href='mailto:richard.hall@centennialdistrict.co?subject=Merit Badge College Help'>Richard Hall</a>";
  //            $htmlMessage = file_get_contents("https://mbcollege.centennialdistrict.co/ScoutEmaill.html");
  //
  //
  //
  //           $htmlMessage .="<br>"."\r\n";
  //           $htmlMessage .= "<h2>". $ScoutRow['FirstNameScout']. " ". $ScoutRow['LastNameScout']."</h2>"."\r\n";
  //           $htmlMessage .= "<b> District:".$District." Unit:".$UnitType." ".$UnitNumber." BSA Id#: ".$BSAIdScout."</b>\r\n";
  //
  //           $htmlMessage .= "<table class='table'  style='width:1024';>"."\r\n";
  //           $htmlMessage .= "<td style='width:150px'>"."\r\n";
  //           $htmlMessage .= "<td style='width:200px'>"."\r\n";
  //           $htmlMessage .= "<td style='width:100px'>"."\r\n";
  //           $htmlMessage .= "<td style='width:10px'>"."\r\n";
  //           $htmlMessage .= "<td style='width:10px'>"."\r\n";
  //           $htmlMessage .= "<tr>"."\r\n";
  //           $htmlMessage .= "<th>Period</th>"."\r\n";
  //           $htmlMessage .= "<th>Merit badge</th>"."\r\n";
  //           $htmlMessage .= "<th>Counselor</th>"."\r\n";
  //           $htmlMessage .= "<th>Email</th>"."\r\n";
  //           $htmlMessage .= "<th>Room</th>"."\r\n";
  //           $htmlMessage .= "</tr>"."\r\n";
  //       }
  //       // Now get the Counselor data for the Merit Badge.
  //       $qryByPeriod = sprintf("SELECT * FROM college_counselors 
  //           WHERE MBName='%s' AND MBPeriod='%s' AND College='%s'", $ScoutRow['MeritBadge'], $ScoutRow['Period'], $CollegeYear);
  //       $resultByPeriod = self::doQuery($qryByPeriod, $CollegeYear);
  //       $rowPeriod = $resultByPeriod->fetch_assoc();
  //
  //       if(mysqli_num_rows($resultByPeriod) == 0){
  //           // PROBLEM !! No Counselor found for this merit badge in selected Period !!!
  //           $Formatter = "<b style='color:red;'>";
  //           $FirstName = "";
  //           $LastName = "";
  //           $Email = "";
  //       }
  //       else{
  //           $Formatter = "";
  //           $FirstName = $rowPeriod['FirstName'];
  //           $LastName = $rowPeriod['LastName'];
  //           $Email = $rowPeriod['Email'];
  //           $Room = $rowPeriod['MBRoom'];
  //       }
  //
  //       $PeriodTime = self::PeriodTime($ScoutRow['Period']);
  //
  //       $htmlMessage .= "<tr><td>" .
  //           $Formatter.$PeriodTime . "</td><td>" .
  //           $Formatter.$ScoutRow['MeritBadge'] . "</td><td>" .
  //           $Formatter.$FirstName ." ".$LastName . "</td><td>" .
  //           $Formatter.$Email . "</td><td>" .
  //           $Formatter.$Room ."</td></tr>";
  //   }
  //   $htmlMessage .= "</table>";
  //
  //   // TODO: send last one here..
  //   if($bPreview){
  //    // Do email it...
  //   }
  //   else{
  //       $retval = mail ($to,$subject,$htmlMessage,$head);
  //       //$retval = false;
  //       if( $retval == true ) {
  //           echo "</br>Message sent successfully...To: ".$to."-".$ScoutFirst. " ". $ScoutLast."</br>";
  //       }else {
  //           echo "</br><b style='color:red;'>Message could not be sent...To: ".$to."-".$ScoutFirst. " ". $ScoutLast."</br>";
  //       }
  //    }
  //
  //}
  /*=============================================================================
     *
     * This function will produce a schedule of Scouts Merit Badge Classes 
     * 
     *===========================================================================*/
  function ReportBadBSAId($report_results)
  {
    $csv_hdr = "ScoutLastName, ScoutFirstName, District, UnitType, UnitNumber, BSAIdScout, Telephone, email";
    $csv_output = "";


    $Fname = "";
    $Lname = "";
    $Expired = false;
    $TodaysDate = strtotime("now");
    $Scout = "";
    $CollegeYear = parent::getYear();

    echo "<table class='table'  style='width:1024';>";
    echo "<td style='width:150px'>";
    echo "<td style='width:150px'>";
    echo "<td style='width:150px'>";
    echo "<td style='width:150px'>";
    echo "<td style='width:150px'>";
    echo "<td style='width:150px'>";
    echo "<td style='width:150px'>";
    echo "<td style='width:150px'>";
    echo "<tr>";
    echo "<th>ScoutLastName</th>";
    echo "<th>ScoutFirstName</th>";
    echo "<th>District</th>";
    echo "<th>Unit Type</th>";
    echo "<th>UNit Number</th>";
    echo "<th>ScoutBSAMemberID</th>";
    echo "<th>Telephone</th>";
    echo "<th>Email</th>";
    echo "</tr>";


    while ($row = $report_results->fetch_assoc()) {

      echo "<tr><td>" .
        $row['LastNameScout'] . "</td><td>" .
        $row['FirstNameScout'] . "</td><td>" .
        $row['District'] . "</td><td>" .
        $row['UnitType'] . "</td><td>" .
        $row['UnitNumber'] . "</td><td>" .
        $row['BSAIdScout'] . "</td><td>" .
        self::formatPhoneNumber(NULL, $row['Telephone']) . "</td><td>" .
        self::formatEmail($row['email']) . "</td></tr>";

      //Now create for CSV file
      $csv_output .= $row['LastNameScout'] . ",";
      $csv_output .= $row['FirstNameScout'] . ",";
      $csv_output .= $row['District'] . ",";
      $csv_output .= $row['UnitType']  . ",";
      $csv_output .= $row['UnitNumber']  . ",";
      $csv_output .= $row['BSAIdScout'] . ",";
      $csv_output .= $row['Telephone'] . ",";
      $csv_output .= $row['email'] . "\n";
    }
    echo "</table>";

    ?>
    <center>
      <form name="export" action="export.php" method="post">
        <input class='RoundButton' style="width:220px" type="submit" value="Export table to CSV">
        <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
        <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
      </form>
    </center>
    <br />
  <?php
  }
  /*=============================================================================
    *
    * This function will produce a schedule of Scouts Merit Badge Classes 
    * 12Sep2023 - Scout book changed the format of the CSV file, now each counselor
    * will need there own upload file.
    * 
    *===========================================================================*/
  public function ReportCSV($report_results)
  {
    $csv_hdr = "ScoutLastName, ScoutBSAMemberID, MeritBadgeName";
    $csv_output = "";

    $Counselor_Name = null;

    while ($row = $report_results->fetch_assoc()) {
      // Get Counselor Name
      if (is_null($Counselor_Name)) {
        $Counselor_Name = $row['LastName'] . "_" . $row['FirstName'];
      } else if (strcasecmp($Counselor_Name, $row['LastName'] . "_" . $row['FirstName'])) {
        $Counselor_Name = "All_Counselors";
      }
      if (!isset($this->N1BSAId) || $row['BSAId'] !== $this->N1BSAId) {
        echo "<table class='table'  style='width:700';>";
        echo "<td style='width:100px'>";
        echo "<td style='width:50px'>";
        echo "<td style='width:150px'>";
        echo "<td style='width:100px'>";
        echo "<tr>";
        echo "<th>ScoutLastName</th>";
        echo "<th>ScoutBSAMemberID</th>";
        echo "<th>MeritBadgeName</th>";
        echo "</tr>";
      }
      $this->N1BSAId = $row['BSAId'];
      echo "<tr><td>" .
        $row['LastNameScout'] . "</td><td>" .
        $row['BSAIdScout'] . "</td><td>" .
        $row['MeritBadge'] . "</td></tr>";

      //Now create for CSV file
      $csv_output .= $row['LastNameScout'] . ",";
      $csv_output .= $row['BSAIdScout'] . ",";
      if ($row['MeritBadge'] == "Signs, Signals, and Codes") {
        // TODO: This is a problem but I don't know how to fix it
        $csv_output .= $row['MeritBadge'] . ",";
      } else
        $csv_output .= $row['MeritBadge'] . "\n";
    }
    echo "</table>";

  ?>
    <center>
      <form name="export" action="export.php" method="post">
        <input class='RoundButton' style="width:220px" type="submit" value="Export table to CSV">
        <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
        <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
        <input type="hidden" value="<?php echo $Counselor_Name; ?>" name="csv_filename">
      </form>
    </center>
    <br />
  <?php
  }
  /*=============================================================================
     *
     * This function will email a schedule of Scouts Merit Badge Classes 
     * 
     *===========================================================================*/
  public function EmailScouts($report_results, $bPreview)
  {
    $ScoutID = "";
    $Scout = new cScout;
    $CollegeYear = parent::getYear();
    $htmlMessage = "";
    $to = "";
    $head = "";
    $subject  = "";
    $ScoutFirst = "";
    $ScoutLast = "";
    $bFirstPass = true;

    while ($ScoutRow = $report_results->fetch_assoc()) {

      // If New Scout, shutdown old table and create a new one.
      if ($ScoutID != $ScoutRow['BSAIdScout']) {


        $ScoutID = $ScoutRow['BSAIdScout'];

        if ($bFirstPass)
          $bFirstPass = false;
        else {
          $htmlMessage .= "</table>";

          if ($bPreview) {
            echo "</br>To: " . $to . "</br>Subject: " . $subject . "</br>Head: " . $head . "</br>";
            echo $htmlMessage . "</br>";
          } else {
            $retval = mail($to, $subject, $htmlMessage, $head);
            //$retval = false;
            if ($retval == true) {
              echo "</br>Message sent successfully...To: " . $to . "-" . $ScoutFirst . " " . $ScoutLast . "</br>";
            } else {
              echo "</br><b style='color:red;'>Message could not be sent...To: " . $to . "-" . $ScoutFirst . " " . $ScoutLast . "</br>";
            }
          }
        }


        $to = $ScoutRow['email'];
        //$to = "rhall290472@gmail.com";
        //$subject = "Centennial-Black Feather Merit Badge College ".$ScoutRow['email'];
        $subject = "Centennial Merit Badge College ";



        // Create email headers
        $head = implode("\r\n", [
          "MIME-Version: 1.0",
          "Content-type: text/html; charset=utf-8",
          "Bcc: richard.hall@centennialdistrict.co"
        ]);



        //Start new html message here...


        //$htmlMessage  = "<html><body>";
        $District = $ScoutRow['District'];
        $UnitType = $ScoutRow['UnitType'];
        $UnitNumber = $ScoutRow['UnitNumber'];
        $BSAIdScout = $ScoutRow['BSAIdScout'];
        $ScoutFirst = $ScoutRow['FirstNameScout'];
        $ScoutLast = $ScoutRow['LastNameScout'];
        $MeritBadge = $ScoutRow['MeritBadge'];









        // Message to scout/parents
        //$htmlMessage = file_get_contents("ScoutEmail.html");
        $htmlMessage = file_get_contents("./ScoutEmail.html");
        //                $htmlMessage  = "<p>Thank you for registering your scout for the Black Feather-Centenninal Merit Badge College ";
        //                $htmlMessage .= "below you will find your scouts scheduled. Check-in for the college  ";
        //                $htmlMessage .= "will start 30-minutes before the schedule class time.</p>";
        //                //$htmlMessage .= "<h2>This email includes the room numbers for your scout(s) class(es)</h2> ";
        //                $htmlMessage .= "<p>Please verify your Scouts BSA ID#, it will be used to connect your scout to the counselor</p>";
        //                if($MeritBadge == "Rifle Shooting"){
        //                    $htmlMessage .= "<h2>Rifle Shooting Merit Badge</h2> ";
        //                    $htmlMessage .= "<p>The Merit Badge College is just days away. I have a few important comments that you'll need prior to the event. 
        //                        Please review the required material ahead of time</p>.";
        //                    $htmlMessage .= "<h2>Bring a copy of health form, this is required for shooting sports Forms A and B only</h2>";
        //                    $htmlMessage .= "<a href='https://filestore.scouting.org/filestore/HealthSafety/pdf/680-001_AB.pdf'>Medical Forms</a>";
        //                    $htmlMessage .= "<h2>Bring a copy of the Rifle Shooting Merit Badge Workbook</h2>";
        //                    $htmlMessage .= "<a href='http://usscouts.org/usscouts/mb/worksheets/Rifle-Shooting.pdf'>Rifle Shooting</a>";
        //                    $htmlMessage .= "<p>Bring a camp chair</p>";
        //                }
        //                else if($MeritBadge == "Archery"){
        //                    $htmlMessage .= "<h2>Archery Merit Badge</h2> ";
        //                    $htmlMessage .= "<p>The Merit Badge College is just days away. I have a few important comments that you'll need prior to the event. 
        //                        Please review the required material ahead of time</p>.";
        //                    $htmlMessage .= "<h2>Bring a copy of health form, this is required for shooting sports Forms A and B only</h2>";
        //                    $htmlMessage .= "<a href='https://filestore.scouting.org/filestore/HealthSafety/pdf/680-001_AB.pdf'>Medical Forms</a>";
        //                    $htmlMessage .= "<h2>Bring a copy of the Archery Merit Badge Workbook</h2>";
        //                    $htmlMessage .= "<a href='http://usscouts.org/mb/worksheets/Archery.pdf'>Archery</a>";
        //                    $htmlMessage .= "<p>Bring a camp chair</p>";
        //                
        //                }
        //                else{
        //                    $htmlMessage .= "<h2>Bring a copy of the Merit Badge Workbook(s)</h2><a href='http://usscouts.org/mb/worksheets/list.asp'>Work Books</a>";
        //                    $htmlMessage .= "<p>Bring a camp chair</p>";
        //                }
        //
        //
        //                $htmlMessage .= "<p>If you have any questions please contact <a href='mailto:jcoroot@gmail.com?subject=Merit Badge College Help'>Johnny Cordova</a>";

        $htmlMessage .= "<br>" . "\r\n";
        $htmlMessage .= "<h2>" . $ScoutRow['FirstNameScout'] . " " . $ScoutRow['LastNameScout'] . "</h2>" . "\r\n";
        $htmlMessage .= "<b> District:" . $District . " Unit:" . $UnitType . " " . $UnitNumber . " BSA Id#: " . $BSAIdScout . "</b>\r\n";

        $htmlMessage .= "<table class='table'  style='width:1024';>" . "\r\n";
        $htmlMessage .= "<td style='width:150px'>" . "\r\n";
        $htmlMessage .= "<td style='width:200px'>" . "\r\n";
        $htmlMessage .= "<td style='width:100px'>" . "\r\n";
        $htmlMessage .= "<td style='width:10px'>" . "\r\n";
        $htmlMessage .= "<td style='width:10px'>" . "\r\n";
        $htmlMessage .= "<tr>" . "\r\n";
        $htmlMessage .= "<th>Period</th>" . "\r\n";
        $htmlMessage .= "<th>Merit badge</th>" . "\r\n";
        $htmlMessage .= "<th>Counselor</th>" . "\r\n";
        $htmlMessage .= "<th>Email</th>" . "\r\n";
        $htmlMessage .= "<th>Room</th>" . "\r\n";
        $htmlMessage .= "<th>Prerequisities</th>" . "\r\n";
        $htmlMessage .= "</tr>" . "\r\n";
      }
      // Now get the Counselor data for the Merit Badge.
      $qryByPeriod = sprintf("SELECT * FROM college_counselors 
                WHERE MBName='%s' AND MBPeriod='%s' AND College='%s'", $ScoutRow['MeritBadge'], $ScoutRow['Period'], $CollegeYear);
      $resultByPeriod = self::doQuery($qryByPeriod, $CollegeYear);
      $rowPeriod = $resultByPeriod->fetch_assoc();

      if (mysqli_num_rows($resultByPeriod) == 0) {
        // PROBLEM !! No Counselor found for this merit badge in selected Period !!!
        $Formatter = "<b style='color:red;'>";
        $FirstName = "";
        $LastName = "";
        $Email = "";
      } else {
        $Formatter = "";
        $FirstName = $rowPeriod['FirstName'];
        $LastName = $rowPeriod['LastName'];
        $Email = $rowPeriod['Email'];
        $Room = $rowPeriod['MBRoom'];
        $Prerequisities = $rowPeriod['MBPrerequisities'];
      }

      $PeriodTime = $Scout->PeriodTime($ScoutRow['Period']);

      $htmlMessage .= "<tr><td>" .
        $Formatter . $PeriodTime . "</td><td>" .
        $Formatter . $ScoutRow['MeritBadge'] . "</td><td>" .
        $Formatter . $FirstName . " " . $LastName . "</td><td>" .
        $Formatter . self::formatEmail($Email) . "</td><td>" .
        $Formatter . $Room . "</td><td>" .
        $Formatter . $Prerequisities. "</td></tr>";
    }
    $htmlMessage .= "</table>";

    // TODO: send last one here..
    if ($bPreview) {
      echo "</br>To: " . $to . "</br>Subject: " . $subject . "</br>Head: " . $head . "</br>";
      echo $htmlMessage . "</br>";
    } else {
      $retval = mail($to, $subject, $htmlMessage, $head);
      //$retval = false;
      if ($retval == true) {
        echo "</br>Message sent successfully...To: " . $to . "-" . $ScoutFirst . " " . $ScoutLast . "</br>";
      } else {
        echo "</br><b style='color:red;'>Message could not be sent...To: " . $to . "-" . $ScoutFirst . " " . $ScoutLast . "</br>";
      }
    }
  }
  /*=============================================================================
     *
     * This function will import scouts from double know file
     * 
     *===========================================================================*/
  public function ImportScouts($fileName)
  {
    /* Defined the file columns, which change */
    $colMeritBadge = 0;         /* Event */
    $colRegistration = 1;       /* Registration # */
    $colRegistrationCost = 12;  /* Registration Cost */
    $colFirstName = 18;         /* First Name */
    $colLastName = 19;          /*Last Name */
    $colPhone = 21;             /* Primary Telephone */
    $colEmail = 22;             /* Primary Email */
    $colGender = 35;            /* Gender */
    $colPeriod = 43;            /* Program */
    //        $colDistrict = 42;          /* What district are you with? */
    $colUnit = 45;              /* What type of unit are you in? */
    //        $colUnitNum = 44;           /* What is your Unit Number? */
    $colBSA_ID = 46;            /* What is your Unit Number? */

    $MBCollegeName = parent::getYear();

    $RecordsInError = 0;
    $SkippedRecords = 0;
    $UnitType = "";
    $UnitNumber = null;
    //$FakeBSAID = -2;
    $DoNotAttend = 0; //TODO: Need to fix

    $filePath = "Data/" . $fileName;

    $Inserted = 0;
    $Updated = 0;
    $row = 0;
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",", '"', "\\")) !== FALSE) {
        if ($row < 2) {
          $row++;
          continue;
        }
        $MeritBadge = $data[$colMeritBadge];
        $pos = strpos($MeritBadge, "2023 Centennial");
        if ($pos !== false) { // Not a merit badge line, skip
          $SkippedRecords++;
        } else {
          $Period = $data[$colPeriod];
          if ($Period[9] == '&')
            $Period = $Period[7] . $Period[11];
          else
            $Period = $Period[7];

          //                    // Double-knot sessions and my Period don't match so....
          //                    switch($Period){
          //                        case 'A':
          //                            $Period = 'A';
          //                            break;
          //                        case 'B':
          //                            $Period = 'B';
          //                            break;
          //                        case 'AB':
          //                            $Period = 'AB';
          //                            break;
          //                        default:
          //                            $Period = '?';
          //                    }

          // Common data for each Merit Badge
          $FirstNameScout =   ucfirst(strtolower($data[$colFirstName]));
          $LastNameScout =    ucfirst(strtolower($data[$colLastName]));
          $Email =            strtolower($data[$colEmail]);
          $Phone =            $data[$colPhone];
          $BSAId =            $data[$colBSA_ID];
          $Registration =     $data[$colRegistration];
          //                    $District     =     $data[ $colDistrict];
          $District     =     null;
          $UnitType     =     $data[$colUnit];
          if ($data[$colUnit] == "Crew")
            $UnitType     =     $data[$colUnit] . "-NA";
          if ($data[$colUnit] == "Troop") {
            if ($data[$colGender] == "Female")
              $UnitType = "Troop-G";
            else
              $UnitType = "Troop-B";
          }
          //                    $UnitNumber   =     $data[$colUnitNum];
          $Gender       =     $data[$colGender];
          $MBName =           $data[$colMeritBadge];
          $RegistrationCost = $data[$colRegistrationCost];

          //Fix up MB Name by removing (....) from string.
          $pos = strpos($MBName, '(');
          if ($pos !== false) {
            $pos--;
            $MBName = substr($MBName, 0,  $pos);
          }

          // If we are updating the records for a updated double-knot file we will to check
          // if we already have the scout recorded.
          if (self::IsSignedUpPeriod($MBCollegeName, $LastNameScout, $FirstNameScout, $Period)) {
            // Then update record else
            self::UpdateInfo(
              $FirstNameScout,
              $LastNameScout,
              $Email,
              $Phone,
              $BSAId,
              $MBCollegeName,
              $Registration,
              $District,
              $UnitType,
              $UnitNumber,
              $Gender
            );
            if (self::UpdateMBClass($MBName, $Period))
              $RecordsInError++;
            else
              $Updated++;
          } else {
            self::AddInfo(
              $FirstNameScout,
              $LastNameScout,
              $Email,
              $Phone,
              $BSAId,
              $MBCollegeName,
              $Registration,
              $District,
              $UnitType,
              $UnitNumber,
              $Gender
            );
            if (self::AddMBClass($MBName, $Period, $DoNotAttend))
              $RecordsInError++;
            else
              $Inserted++;
          }
        }
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
      parent::function_alert($Usermsg);
    } else {
      $Usermsg = "Failed to open file";
      parent::function_alert($Usermsg);
    }
    return $RecordsInError;
  }
  //*************************************************
  //
  // Allow user to select a single Scout to display
  //
  //*************************************************
  public function SelectSingleScout($CollegeYear, $bPreview)
  {
    $qrySelectedScout = "SELECT DISTINCTROW LastNameScout, FirstNameScout, BSAIdScout FROM college_registration
            WHERE College = '$CollegeYear' ORDER BY LastNameScout, FirstNameScout";

    $result_ByScout = self::doQuery($qrySelectedScout);
    if (!$result_ByScout) {
      self::function_alert("ERROR: MeritQuery($qrySelectedScout)");
    }
  ?>
    <form method=post>
      <div class="row  d-print-none">
        <div class="col-2">
          <label for='ScoutName'></label>
          <select class='form-control' id='ScoutName' name='ScoutName'>
            <option value="" </option>
              <?php
              while ($rowCerts = $result_ByScout->fetch_assoc()) {
                echo "<option value=" . $rowCerts['BSAIdScout'] . ">" . $rowCerts['LastNameScout'] . " " . $rowCerts['FirstNameScout'] . "</option>";
              }
              ?>
          </select>
        </div>
        <?php
        if ($bPreview) { ?>
          <div class="col-1">
            <input type='checkbox' name='Preview' id='chkPreview' value='1' />
            <label for='chkPreview'>Preview Email </label>
          </div>
        <?php
        }
        ?>
        <div class="col-2">
          <input class='btn btn-primary btn-sm' type='submit' name='SubmitScout' />
        </div>
    </form>
    </div>
<?php
  }
}
