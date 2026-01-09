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

include_once('CMBCollege.php');
/******************************************************************************
 * 
 * 
 * 
 *****************************************************************************/
/**
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 */
class CCounselor extends CMBCollege
{
  private $LastName;
  private $FirstName;
  private $College;
  private $BSAId;
  private $Email;
  private $Phone;
  private $MBName = array();
  private $MBPeriod = array();
  private $MBPrerequisities = array();
  private $MBNotes = array();
  private $MBCSL = array();
  private $MBFee = array();
  private $MBRoom = array();
  private $MB_Count = 0;
  private $nIndex = -1;

  /******************************************************************************
   *
   * This function will search the college_counselors database to see if the 
   * selected cousnelor has already signed up, if so, then we will "recall"
   * their signup input and display them back to they so that they can be 
   * updated.
   *
   * 
   *****************************************************************************/
  public function IsSignedUp($Year, $LastName, $FirstName)
  {
    $bReturn = false;
    //$this->nIndex = -1;

    $sqlEdit = sprintf(
      "SELECT * FROM `college_counselors` WHERE `College`='%s' AND `LastName`='%s' AND `FirstName`='%s' ORDER BY `MBPeriod`",
      $Year,
      $LastName,
      $FirstName
    );

    if (!$EditMB = self::doQuery($sqlEdit)) {
      // Not sure we need this as the function MeritQuery will display the error message.
      $msg = "Error: MeritQuery() " . $sqlEdit;
      self::function_alert($msg);
    } else {
      //$this->CollegeYear = $Year;
      $this->LastName = $LastName;
      $this->FirstName = $FirstName;
      $nLoop = 0;
      while ($Results = $EditMB->fetch_assoc()) {
        $this->MBName[$nLoop] = $Results['MBName'];
        $this->MBPeriod[$nLoop] = $Results['MBPeriod'];
        $this->MBPrerequisities[$nLoop] = $Results['MBPrerequisities'];
        $this->MBNotes[$nLoop] = $Results['MBNotes'];
        $this->MBCSL[$nLoop] = $Results['MBCSL'];
        $this->MBFee[$nLoop] = $Results['MBFee'];
        $this->MBRoom[$nLoop] = $Results['MBRoom'];
        $nLoop++;
      }
      $this->MB_Count = $nLoop;
      $bReturn = true;
    }
    return $bReturn;
  }
  /******************************************************************************
   *
   *
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  function Delete($Year)
  {
    $bReturn = false;
    $sqlDelete = sprintf(
      "DELETE FROM `college_counselors` WHERE `College`='%s' AND 
            `LastName`='%s' AND `FirstName`='%s'",
      $Year,
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
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  function AddInfo($FirstName, $LastName, $Email, $Phone, $BSAId, $MBCollegeName)
  {
    $this->FirstName = $FirstName;
    $this->LastName = $LastName;
    $this->Email = $Email;
    $this->Phone = $Phone;
    $this->BSAId = $BSAId;
    $this->College = $MBCollegeName;
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
  function MB_Match($MeritNamee, $Index)
  {
    $bReturn = false;
    $Index--; // This will be the Period...

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
  function Period_Match($Period, $nPeriod)
  {
    $bReturn = false;

    if (($nPeriod <= $this->nIndex) && !strcmp($this->MBPeriod[$nPeriod], $Period))
      $bReturn = true;

    return $bReturn;
  }

  function DisplayPeriods($nPeriod)
  {
    $nPeriod = $nPeriod - 1;  // nIndex is zero based

    if (self::GetPeriodATime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('A', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='A'>Period A - %s</option>", $strSelected, self::GetPeriodATime(parent::getyear()));
    }
    if (self::GetPeriodBTime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('B', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='B'>Period B - %s</option>", $strSelected, self::GetPeriodBTime(parent::getyear()));
    }
    if (self::GetPeriodCTime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('C', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='C'>Period C - %s</option>", $strSelected, self::GetPeriodCTime(parent::getyear()));
    }
    if (self::GetPeriodDTime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('D', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='D'>Period D - %s</option>", $strSelected, self::GetPeriodDTime(parent::getyear()));
    }
    if (self::GetPeriodABTime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('AB', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='AB'>Period A-B - %s</option>", $strSelected, self::GetPeriodABTime(parent::getyear()));
    }
    if (self::GetPeriodCDTime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('CD', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='CD'>Period C-D - %s</option>", $strSelected, self::GetPeriodCDTime(parent::getyear()));
    }
    if (self::GetPeriodETime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('E', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='E'>Period E - %s</option>", $strSelected, self::GetPeriodETime(parent::getyear()));
    }
    if (self::GetPeriodFTime(parent::getyear()) != null) {
      $strSelected = $this->Period_Match('F', $nPeriod) ? "selected" : "";
      echo sprintf("<option %s value='F'>Period F - %s</option>", $strSelected, self::GetPeriodFTime(parent::getyear()));
    }
  }

  /******************************************************************************
   * 
   *
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  function Display_ClassSize($Element, $nPeriod)
  {
    $nPeriod = $nPeriod - 1; // Indexs are zero based
    $required = $nPeriod == 0 ? "required" : "";
    $DefaultSize = 15;

    // If we have class size limit, show it..
    if ($nPeriod < count($this->MBCSL)) {
      $str = sprintf(
        "<input class='form-control' id='%s' name='%s' size='5' value='%s' %s />",
        $Element,
        $Element,
        $this->MBCSL[$nPeriod],
        $required
      );
    } else {
      $str = sprintf(
        "<input class='form-control' id='%s' name='%s' size='5' %s value='%s'/>",
        $Element,
        $Element,
        $required,
        $DefaultSize
      );
    }
    echo $str;
  }
  /******************************************************************************
   * 
   *
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  function Display_ClassFee($Element, $nPeriod)
  {
    $nPeriod = $nPeriod - 1; // Indexs are zero based
    //$required = $nPeriod == 0 ? "required" : "";
    $required = "";

    // If we have class fee, show it..
    if ($nPeriod < count($this->MBFee)) {
      $str = sprintf(
        "<input class='form-control' id='%s' name='%s' size='5' value='%s' %s />",
        $Element,
        $Element,
        $this->MBFee[$nPeriod],
        $required
      );
    } else {
      $str = sprintf(
        "<input class='form-control' id='%s' name='%s' size='5' %s />",
        $Element,
        $Element,
        $required
      );
    }
    echo $str;
  }
  /******************************************************************************
   * 
   *
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  function Display_ClassRoom($Element, $nPeriod)
  {
    $nPeriod = $nPeriod - 1; // Indexs are zero based

    // If we have class room, show it..
    if ($nPeriod < count($this->MBRoom) && $this->MBRoom != null) {
      $str = sprintf(
        "<input class='form-control' id='%s' name='%s' size='5' value='%s'/>",
        $Element,
        $Element,
        $this->MBRoom[$nPeriod]
      );
    } else {
      $str = sprintf(
        "<input class='form-control' id='%s' name='%s' size='5'/>",
        $Element,
        $Element
      );
    }
    echo $str;
  }
  /******************************************************************************
   *
   *
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  function Display_Prerequisities($Element, $nPeriod)
  {
    $nPeriod = $nPeriod - 1; // Indexs are zero based

    if ($nPeriod < count($this->MBPrerequisities)) {
      $str = sprintf('<textarea rows="10" cols="30" class="textarea form-control-sm" id="%s" name="%s">%s</textarea>', $Element, $Element, $this->MBPrerequisities[$nPeriod]);
    } else {
      $str = sprintf(
        '<textarea  rows="10" cols="30" class="textarea form-control-sm" id="%s" name="%s"></textarea>',
        $Element,
        $Element
      );
    }
    echo $str;
  }

  /******************************************************************************
   *
   *
   * MB_Match MUST be called before this function !!!!!
   *
   *****************************************************************************/
  function Display_Notes($Element, $nPeriod)
  {
    $nPeriod = $nPeriod - 1; // Indexs are zero based

    if ($nPeriod < count($this->MBNotes)) {
      $str = sprintf(
        '<textarea rows="10"  cols="30" class="textarea form-control-sm" id="%s" name="%s">%s</textarea>',
        $Element,
        $Element,
        $this->MBNotes[$this->nIndex]
      );
    } else {
      $str = sprintf(
        '<textarea rows="10" cols="30" class="textarea form-control-sm" id="%s" name="%s"></textarea>',
        $Element,
        $Element
      );
    }
    echo $str;
  }
  /*=============================================================================
     *
     * This function will add a merit badge to the college list of available merit
     * badges.
     * 
     *===========================================================================*/
  function AddMBClass($MBName, $MBPeriod, $MBClassSize, $MBFee, $MBRoom, $MBPrerequisities, $MBNotes)
  {

    // TODO: #9 Ensure Counselor has not already signed up for this period.
    $sqlUpdate = sprintf(
      "INSERT INTO `college_counselors`(`College`, `LastName`, `FirstName`, `Email`, `Phone`, `BSAId`, `MBName`, `MBPeriod`, `MBCSL`, `MBFee`, `MBRoom`, `MBPrerequisities`, `MBNotes`) 
    				VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
      $this->College,
      $this->LastName,
      $this->FirstName,
      $this->Email,
      $this->Phone,
      $this->BSAId,
      $MBName,
      $MBPeriod,
      $MBClassSize,
      $MBFee,
      $MBRoom,
      $MBPrerequisities,
      $MBNotes
    );

    if (!self::doQuery($sqlUpdate)) {
      self::function_alert("Error: AddMBClass()");
    }
    // Don't email if Admin is editing Counselor signup    
    if (!(isset($_SESSION["loggedin"]) && $_SESSION["Type"] === "Admin")) {
      $str = sprintf("New signup %s, at %s\n", $sqlUpdate, Date('Y-m-d H:i:s'));
      error_log($str);
    }
  }
  /*=============================================================================
     *
     * This function will produce a schedule of Counselors Merit Badge Classes 
     * 
     *===========================================================================*/
  function ReportCounselorSchedule($report_results, $CollegeYear)
  {

    $csv_hdr = "College, First Name, Last Name, Email, Phone, Period, Merit Badge, First Name Scout, Last Name Scout, Scouts Email";
    $csv_output = "";

    $Counselor = "";

?>
    <p>This report will only show merit bagdes that Scouts have signed up for. Counselors may have signed up for more, view college Schedule for all the badges
      Counselor may have signed up for.</p>
    <?php

    $TodaysDate = strtotime("now");

    while ($row = $report_results->fetch_assoc()) {

      // If New Counselor, shutdown old table and create a new one.
      if ($Counselor != $row['BSAId']) {
        $Counselor = $row['BSAId'];
        echo "</table>";
    ?> <p style="page-break-after: always;">&nbsp;</p>
    <?php
        echo "<br>";
        echo "<h2>", $row['FirstName'], " ", $row['LastName'], "</h2>";
        $Expired = false;

        //Go get YPT Status, this is in the mbccounselors table
        $sqlYPT = "SELECT YPT from mbccounselors WHERE `FirstName`='$row[FirstName]' AND `LastName`='$row[LastName]'";
        $resultYPT = self::doQuery($sqlYPT);
        if ($resultYPT) {
          $rowYPT = $resultYPT->fetch_assoc();
          $Counselorsypt = strtotime($rowYPT['YPT']);
          if ($TodaysDate > $Counselorsypt) {
            $Expired = true;
          }
          //if ($Expired)
            //$yptValid = "<span style='color:red; font-weight:bold;'>YPT valid until: " . $rowYPT['YPT'] . "</span><br/>";
          //else
           // $yptValid = "<span style='color:#212529'>YPT valid until: " . $rowYPT['YPT'] . "</span><br/>";
        }
        //echo $yptValid;
        ?>
        <table class='table'  style='width:1280';>
        <td style='width:50px'>
        <td style='width:200px'>
        <td style='width:200px'>
        <td style='width:150px'>
        <td style='width:150px'>
        <td style='width:100px'>
        <td style='width:75px'>
        <tr>
        <th>Period</th>
        <th>Merit badge</th>
        <th>Scout</th>
        <th>Unit</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Room</th>
        <th>Did Not Attend</th>
        </tr>
        <?php
      }
      // Now get the Counselor data for the Merit Badge.
      $qryByPeriod = sprintf("SELECT * FROM college_registration 
                WHERE MeritBadge='%s' AND Period='%s' AND College='%s'", $row['MBName'], $row['MBPeriod'], $CollegeYear);
      $resultByPeriod = self::doQuery($qryByPeriod, $CollegeYear);
      while ($rowPeriod = $resultByPeriod->fetch_assoc()) {
        if (mysqli_num_rows($resultByPeriod) == 0) {
          // PROBLEM !! No Counselor found for this merit badge in selected Period !!!
          $Formatter = "<b style='color:red;'>";
          $FirstName = "";
          $LastName = "";
          $Unit = "";
          $Phone = "";
          $Email = "";
        } else {
          // If scout has not paid flag
          if ($rowPeriod['Registration'] <= 0)
            $Formatter = "<b style='color:green;'>";
          else
            $Formatter = "";
          $FirstName = $rowPeriod['FirstNameScout'];
          $LastName = $rowPeriod['LastNameScout'];
          $Unit = $rowPeriod['UnitType'] . " " . $rowPeriod['UnitNumber'];
          $Phone = self::formatPhoneNumber(NULL, $rowPeriod['Telephone']);
          $Email = self::formatEmail($rowPeriod['email']);
          $Room = $row['MBRoom'];
        }


        echo '<tr><td>' .
          $Formatter . $row['MBPeriod'] . '</td><td>' .
          $Formatter . $row['MBName'] . '</td><td>' .
          $Formatter . $FirstName . ' ' . $LastName . '</td><td>' .
          $Formatter . $Unit . '</td><td>' .
          $Formatter . $Phone . '</td><td>' .
          $Formatter . $Email . '</td><td>' .
          $Formatter . $Room . '</td><td>' .
          '<center><input type="checkbox" name="name1" />&nbsp;</center><td></tr>';


        $csv_output .= $CollegeYear . ",";
        $csv_output .= $row['FirstName'] . ",";
        $csv_output .= $row['LastName'] . ",";
        $csv_output .= $row['Email'] . ",";
        $csv_output .= $row['Phone'] . ",";
        $csv_output .= $row['MBPeriod'] . ",";
        $csv_output .= $row['MBName'] . ",";
        $csv_output .= $rowPeriod['FirstNameScout'] . ",";
        $csv_output .= $rowPeriod['LastNameScout'] . ",";
        $csv_output .= $rowPeriod['email'] . "\n";
      }
    }
    echo "</table>";
    ?>
    <br /><br /><br />
    <center>
      <form name="export" action="export.php" method="post">
        <input class='btn btn-primary btn-sm  d-print-none' style="width:220px" type="submit" value="Export table to CSV">
        <input type="hidden" value="<?php echo $csv_hdr; ?>" name="csv_hdr">
        <input type="hidden" value="<?php echo $csv_output; ?>" name="csv_output">
      </form>
    </center>
    <br />
  <?php
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
  /*=============================================================================
     *
     * This function will email a schedule of Scouts Merit Badge Classes 
     * 
     *===========================================================================*/
  public function EmailCounselors($report_results, $bPreview)
  {
    $BSAID = "";
    $CollegeYear = parent::getYear();
    $htmlMessage = "";
    $to = "";
    $head = "";
    $head = "";
    $subject  = "";
    $FirstName = "";
    $LastName = "";
    $Email = "";
    $bFirstPass = true;

    while ($CounselorRow = $report_results->fetch_assoc()) {

      // If New Scout, shutdown old table and create a new one.
      if ($BSAID != $CounselorRow['BSAId']) {


        $BSAID = $CounselorRow['BSAId'];

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
              echo "</br>Message sent successfully...To: " . $to . "-" . $FirstName . " " . $LastName . "</br>";
            } else {
              echo "</br><b style='color:red;'>Message could not be sent...To: " . $to . "-" . $FirstName . " " . $LastName . "</br>";
            }
          }
        }


        $to = $CounselorRow['Email'];
        $subject = "Centennial Merit Badge College ";

        // Create email headers
        $head = implode("\r\n", [
          "MIME-Version: 1.0",
          "Content-type: text/html; charset=utf-8",
          "Bcc: richard.hall@centennialdistrict.co"
        ]);

        //$htmlMessage = file_get_contents("https://centennialdistrict.co/MBCollege/CounselorEmaill.html");
        $htmlMessage = file_get_contents("./CounselorEmaill.html");
        if (!$htmlMessage) {
          $msg = "Error: EmailCounselors() - file_get_contents";
          self::function_alert($msg);
          self::GotoURL(("index.php"));
          exit;
        }

        $htmlMessage .= "<br>" . "\r\n";
        $htmlMessage .= "<h2>" . $CounselorRow['FirstName'] . " " . $CounselorRow['LastName'] . "</h2>" . "\r\n";
        $htmlMessage .= "<b> BSA Id#: " . $BSAID . "</b>\r\n";

        $htmlMessage .= "<table class='table' ;>" . "\r\n";
        $htmlMessage .= "<td style='width:200px'>" . "\r\n";
        $htmlMessage .= "<td style='width:200px'>" . "\r\n";
        $htmlMessage .= "<td style='width:200px'>" . "\r\n";
        $htmlMessage .= "<td style='width:50px'>" . "\r\n";
        $htmlMessage .= "<td style='width:100px'>" . "\r\n";
        $htmlMessage .= "<td style='width:150px'>" . "\r\n";
        $htmlMessage .= "<td style='width:150px'>" . "\r\n";
        $htmlMessage .= "<tr>" . "\r\n";
        $htmlMessage .= "<th>Period</th>" . "\r\n";
        $htmlMessage .= "<th>Merit badge</th>" . "\r\n";
        $htmlMessage .= "<th>Scout</th>" . "\r\n";
        $htmlMessage .= "<th>BSA Id</th>" . "\r\n";
        $htmlMessage .= "<th>Email</th>" . "\r\n";
        $htmlMessage .= "<th>Phone</th>" . "\r\n";
        $htmlMessage .= "<th>Did Not Attend</th>" . "\r\n";
        $htmlMessage .= "</tr>" . "\r\n";
      }
      // Now get the Scout data for the Merit Badge.
      $qryByPeriod = sprintf("SELECT * FROM college_registration 
                WHERE MeritBadge='%s' AND Period='%s' AND College='%s'", $CounselorRow['MBName'], $CounselorRow['MBPeriod'], $CollegeYear);
      $resultByPeriod = self::doQuery($qryByPeriod, $CollegeYear);
      //$rowPeriod = $resultByPeriod->fetch_assoc();

      while ($rowPeriod = $resultByPeriod->fetch_assoc()) {
        if (mysqli_num_rows($resultByPeriod) == 0) {
          // PROBLEM !! No Counselor found for this merit badge in selected Period !!!
          $Formatter = "<b style='color:red;'>";
          $FirstName = "";
          $LastName = "";
          $Email = "";
        } else {
          $Formatter = "";
          $FirstName = $rowPeriod['FirstNameScout'];
          $LastName = $rowPeriod['LastNameScout'];
          $Email = $rowPeriod['email'];
          //$Room = $rowPeriod['MBRoom'];
        }

        $PeriodTime = self::PeriodTime($CounselorRow['MBPeriod']);

        $htmlMessage .= "<tr><td>" .
          $Formatter . $PeriodTime . "</td><td>" .
          $Formatter . $CounselorRow['MBName'] . "</td><td>" .
          $Formatter . $FirstName . " " . $LastName . "</td><td>" .
          $Formatter . $rowPeriod['BSAIdScout'] . "</td><td>" .
          $Formatter . self::formatEmail($Email) . "</td><td>" .
          $Formatter . self::formatPhoneNumber(NULL, $rowPeriod['Telephone']) . "</td><td>" .
          '<center><input type="checkbox" name="name1" />&nbsp;</center><td></tr>';
      }
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
        echo "</br>Message sent successfully...To: " . $to . "-" . $FirstName . " " . $LastName . "</br>";
      } else {
        echo "</br><b style='color:red;'>Message could not be sent...To: " . $to . "-" . $FirstName . " " . $LastName . "</br>";
      }
    }
  }
  /*=============================================================================
     *
     * This function will email a schedule of Scouts Merit Badge Classes 
     * Doesn't work needs some TLC
     * 
     *===========================================================================*/
  public function EmailCounselorsAttachment($report_results, $bPreview)
  {
    //define the receiver of the email 
    $to = 'rhall290472@gmail.com';
    //define the subject of the email 
    $subject = 'Test email with attachment';
    //create a boundary string. It must be unique 
    //so we use the MD5 algorithm to generate a random hash 
    $random_hash = md5(date('r', time()));
    //define the headers we want passed. Note that they are separated with \r\n 
    $headers = "From: richard.hall@centennialdistrict.co\r\nReply-To: richard.hall@centennialdistrict.co";
    //add boundary string and mime type specification 
    $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
    //read the atachment file contents into a string,
    //encode it with MIME base64,
    //and split it into smaller chunks
    $attachment = chunk_split(base64_encode(file_get_contents('.\Attachments\MeritBadgeCollege2022.pdf')));
    //define the body of the message. 
    ob_start(); //Turn on output buffering 
  ?>
    --PHP-mixed-<?php echo $random_hash; ?>
    Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"

    --PHP-alt-<?php echo $random_hash; ?>
    Content-Type: text/plain; charset="iso-8859-1"
    Content-Transfer-Encoding: 7bit

    Hello World!!!
    This is simple text email message.

    --PHP-alt-<?php echo $random_hash; ?>
    Content-Type: text/html; charset="iso-8859-1"
    Content-Transfer-Encoding: 7bit

    <h2>Hello World!</h2>
    <p>This is something with <b>HTML</b> formatting.</p>

    --PHP-alt-<?php echo $random_hash; ?>--

    --PHP-mixed-<?php echo $random_hash; ?>
    Content-Type: application/pdf; name="MeritBadgeCollege2022.pdf"
    Content-Transfer-Encoding: base64
    Content-Disposition: attachment

    <?php echo $attachment; ?>
    <!-- --PHP-mixed- -->
    <?php echo $random_hash; ?>--

    <?php
    //copy current buffer contents into $message variable and delete current output buffer 
    $message = ob_get_clean();
    //send the email 
    if ($bPreview) {
      echo "</br>To: " . $to . "</br>Subject: " . $subject . "</br>Head: " . $headers . "</br>";
      echo $message . "</br>";
    } else {
      $FirstName = "Joe";
      $LastName = "Scouter";
      $retval = mail($to, $subject, $message, $headers);
      //$retval = false;
      if ($retval == true) {
        echo "</br>Message sent successfully...To: " . $to . "-" . $FirstName . " " . $LastName . "</br>";
      } else {
        echo "</br><b style='color:red;'>Message could not be sent...To: " . $to . "-" . $FirstName . " " . $LastName . "</br>";
      }
    }




    //$mail_sent = @mail( $to, $subject, $message, $headers ); 
    //if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
    //echo $mail_sent ? "Mail sent" : "Mail failed"; 
  }
  /*=============================================================================
     *
     * This function will 
     * 
     *===========================================================================*/
  public function SelectCounselor($CollegeYear, $bPreview)
  {

    $querySelectedCounselor1 = "SELECT DISTINCTROW LastName, FirstName, BSAId FROM college_counselors
        WHERE College = '$CollegeYear' ORDER BY LastName, FirstName";

    $result_ByCounselor = self::doQuery($querySelectedCounselor1);
    if (!$result_ByCounselor) {
      self::function_alert("ERROR: MeritQuery($querySelectedCounselor1)");
    }
    ?>

    <form method=post>
      <div class="row  d-print-none">
        <div class="col-2">


          <label for='CounselorName'></label>
          <select class='form-control' id='CounselorName' name='CounselorName'>
            <option value=\"\" </option>
              <?php
              while ($rowCerts = $result_ByCounselor->fetch_assoc()) {
                echo "<option value=" . $rowCerts['BSAId'] . ">" . $rowCerts['LastName'] . " " . $rowCerts['FirstName'] . "</option>";
              } ?>
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

          <input class='btn btn-primary btn-sm' type='submit' name='SubmitCounselor' value='Select Counselor' />
        </div>
      </div>
    </form>

    <?php
  }
  /******************************************************************************
   * 
   * This function will read in and update the table from the 
   * CouncilMeritBadgeCounselorListing.csv file which is downloaded from the
   * my.scouting.org web-site.
   *  22Aug2021 - BSA changed report format again.
   *  25Jul2022 - BSA changed report format again.
   * 
   * $data[0]  == organizations
   * $data[1]  == firstname
   * $data[2]  == lastname
   * $data[3]  == yptstatus
   * $data[4]  == strexpirydt
   * $data[5]  == troopnos
   * $data[6]  == phone
   * $data[7]  == email
   * $data[8]  == mbcounciling
   * $data[9]  == awards_p01
   * 
   * ***************************************************************************/
  public static function UpdateCouncilListShort($uploadPath)
  {
    /* Define column layout of file */
    $colOrganizations = 0;
    $colFirst_Name = 1;
    $colLast_Name = 2;
    $colmemberid = 3;
    $colStrexpirydt = 4;
    $colYPT_Status = 5;
    $colStryptexpirydt = 6;
    $colTroopnos = 7;
    $colPhone = 8;
    $colEmail = 9;
    $colCity = 11;
    $colZip = 12;
    $colNumber_Badges_Counsel = 13;
    $colAwards = 14;

    // File does not contain BSA Member ID so we create fake one
    //$BSAMemberID = -100;

    $Inserted = 0;
    $Updated = 0;
    $RecordsInError = 0;
    $RecordsInsert = 0;
    $FileToOpen = UPLOAD_DIRECTORY . $uploadPath;
    $AddCounselor = 0;
    $RecordsInErrorDebug = 0;


    set_time_limit(300);  // Give it time to complete/

    // First set the all of the Merit Badge status to DROP, we will then over write as need.
    $sqlUpdate = sprintf("UPDATE `mbccounselormerit` SET `Status`='DROP', `StatusDate`='%s' WHERE `Status` <> 'DROP'", Date("d/M/Y"));
    if (!self::doQuery($sqlUpdate)) {
      $msg = "Error: " . $sqlUpdate;
      error_log($msg);
      exit();
    }
    // Second set the all of the Merit Badge Counselors to Inactive.
    $sqlUpdate = sprintf("UPDATE `mbccounselors` SET `Active`='No', `ValidationDate`='%s' WHERE `Active` <> 'No'", Date("d/M/Y"));
    if (!self::doQuery($sqlUpdate)) {
      $msg = "Error: " . $sqlUpdate;
      error_log($msg);
      exit();
    }


    $Row = 1;
    if (($handle = fopen($FileToOpen, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",", '"', "\\")) !== FALSE) {
        if ($Row <= 9) {
          $Row++;
          continue;
        } //Skip past the header stuff, first line of data is row 11
        // First need to check if this will be an UPDATE or INSERT
        $District = $data[$colOrganizations];
        $FirstName = $data[$colFirst_Name];
        $LastName = $data[$colLast_Name];
        $MemberID = $data[$colmemberid];
        $YPT = $data[$colYPT_Status];
        //$StrYPTExp = $data[$colStrYPTExp];
        //$MemberID = $data[$colMember_ID];
        //$Troop_s = $data[$colTroopnos];
        //$Phone = $data[$colPhone];
        //$Phone = self::right($Phone, 10);
        $Email = $data[$colEmail];
        $City = $data[$colCity];
        $NumOfBadges = $data[$colNumber_Badges_Counsel];

        if ($NumOfBadges == 0) {
          // TODO: Mark the counselors as Not Active.
          //if (!self::MarkCounselorNotActive($MemberID)) {
          //    $msg = "Error: Marking Counselor NotActive " . $FirstName . " " . $LastName . " " . $MemberID;
          //    self::function_alert($msg);
          //}
        }

        $i = 0;
        $Badge[$i] = strtok($data[$colAwards], ",");
        while ($Badge[$i] !== false) {
          $MeritBadge = $Badge[$i];
          $MeritBadge = self::FixMeritBadgeName($MeritBadge);

          /********************
                  Update the Database - Check if counselors is in database, if not insert record.
           *********************/
          if (!self::InsertUpdateMeritBadge($FirstName, addslashes($LastName), $MeritBadge)) {
            // Insert Counselor data
            // Now update Counselor information
            if ($i == 0) {
              $sqlCounselorInsert = sprintf(
                "INSERT INTO `mbccounselors`(`LastName`, `HomeDistrict`, `FirstName`, `HomePhone`, `MemberID`,  
                             `Active`, `YPT`, `Email`,`City`, `ValidationDate`,  `NumOfBadges`) 
                            VALUES ('%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                addslashes($LastName),
                $District,
                $FirstName,
                "",
                $MemberID,
                "Yes",
                $YPT,
                //$StrYPTExp = $data[$colStrYPTExp];
                //$MemberID = $data[$colMember_ID];
                //$Troop_s = $data[$colTroopnos];
                //$Phone = $data[$colPhone];
                //$Phone = self::right($Phone, 10);
                $Email = $data[$colEmail],
                $City,
                date("d/M/Y"),
                $NumOfBadges = $data[$colNumber_Badges_Counsel]
              );
              $AddCounselor++;
              if (!self::doQuery($sqlCounselorInsert)) {
                $msg = sprintf("Error: %s ", $sqlCounselorInsert);
                echo "Error: " . $sqlCounselorInsert . $MeritBadge . "<br/>";
                self::function_alert($msg);
              }
            }

            // Insert New Merit Badges
            $sqlInsert = sprintf("INSERT INTO `mbccounselormerit`(`LastName`, `FirstName`, `MeritName`, `Status`, `StatusDate`) 
                        VALUES ('%s', '%s', '%s', 'ADD', '%s')", addslashes($LastName), $FirstName, $MeritBadge, date("d/M/Y"));
            if (!self::doQuery($sqlInsert)) {
              $RecordsInError++;
              echo "Error: " . $sqlInsert . "<br/>";
            } else {
              $RecordsInsert++;
              $Inserted++;
            }
          } else {
            // COunselor has been found in the database, update the records.
            //Update Old Data
            $sqlUpdate = sprintf(
              "UPDATE `mbccounselormerit` SET `LastName`='%s',`FirstName`='%s',`MeritName`='%s',`Status`='UPDATED',`StatusDate`='%s'
                            WHERE `LastName`='%s' AND `FirstName`='%s' AND `MeritName`='%s'",
              addslashes($LastName),
              $FirstName,
              $MeritBadge,
              date("d/M/Y"),
              addslashes($LastName),
              $FirstName,
              $MeritBadge
            );
            if (!self::doQuery($sqlUpdate)) {
              $msg = sprintf("Error: %s ", $sqlUpdate);
              echo "Error: " . $sqlUpdate . "<br/>";
              self::function_alert($msg);
              $RecordsInError++;
            } else
              $Updated++;
            // TODO: This needs to be done somewhere else, because it could be looped here mutiple times.
            if ($i == 0) {
              // Now update Counselor information
              $sqlUpdate = sprintf(
                "UPDATE `mbccounselors` SET `ValidationDate`='%s', `MemberID`='%s', 
                                    `HomePhone`='%s', `Email`='%s', `Active`='%s', `YPT`='%s', `NumOfBadges`='%s'
                                     WHERE `LastName`='%s' AND `FirstName`='%s'",
                date("d/M/Y"),
                $MemberID,
                "",
                $Email,
                "Yes",
                $data[$colYPT_Status],
                $NumOfBadges,
                addslashes($LastName),
                $FirstName
              );
              if (!self::doQuery($sqlUpdate)) {
                $msg = sprintf("Error: %s ", $sqlUpdate);
                self::function_alert($msg);
              }
            }
          }
          // Get Next merit badge for this counselor
          $i++;
          $Badge[$i] = strtok(",");
        }
      }
      fclose($handle);
      $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Records Updated Inserted: ' . $Inserted . ' Updated: ' . $Updated . ' Errors: ' . $RecordsInErrorDebug];
      // $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInErrorDebug;
      // self::function_alert($Usermsg);
      if ($RecordsInError == 0 && $RecordsInsert != 0 && $AddCounselor == 0) echo "<script>window.location.href = 'index.php';</script>";
      else {
    ?>
        <center>
          </br><button class=' RoundButton' style='width:220px' onclick="window.location.href ='index.php';"' >Return Main </button></br>
            </center>
            <?php
          }
        } else {
          $Usermsg = "Failed to open file";
          self::function_alert($Usermsg);
        }
      }
      /*=============================================================================
     *
     * This function will mark the Merit Badge Counselor Not Active
     * 
     *===========================================================================*/
  public static function FixMeritBadgeName($badge)
  {
    $badge = trim($badge);
    $map = [
      "Cit. in Comm." => "Citizenship in the Community",
      "Cit. in Nation" => "Citizenship in the Nation",
      "Cit. in World" => "Citizenship in the World",
      "Signs Signals Codes" => "Signs, Signals, and Codes",
      "Signs" => "Signs, Signals, and Codes",
      "Signals" => "Bad Merit Badge Name",
      "and Codes" => "Bad Merit Badge Name",
      "Wilderness Surv." => "Wilderness Survival",
      "Model Design" => "Model Design and Building",
      "Fish and Wildlife" => "Fish and Wildlife Management",
      "Mining" => "Mining in Society",
      "Soil and Water Con." => "Soil and Water Conservation",
      "Small-Boat Sailing" => "Small Boat Sailing",
      "Composite Mat." => "Composite Materials",
      "Scout Heritage" => "Scouting Heritage",
      "Reptile and Amph." => "Reptile and Amphibian Study",
      "Disabilities Awar." => "Disabilities Awareness",
      "Amer. Cultures" => "American Cultures",
      "Automotive Maint." => "Automotive Maintenance",
      "Landscape Arch." => "Landscape Architecture",
      "Emergency Prep." => "Emergency Preparedness",
      "Pers. Fitness" => "Personal Fitness",
      "Personal Mgmt." => "Personal Management", // Fixed typo
      "Amer. Business" => "American Business",
      "Communication" => "Communications",
      "Amer. Heritage" => "American Heritage",
      "Digital Tech" => "Digital Technology",
      "Enviro. Science" => "Environmental Science",
      "Vet. Medicine" => "Veterinary Medicine",
      "Truck Trans." => "Truck Transportation",
      "Amer. Labor" => "American Labor",
      "Motorboating" => "Motorboating",
      "Medicine (2018 - Discontinued 12/31/2021)" => "Medicine"
    ];
    return $map[$badge] ?? $badge;
  }
      /******************************************************************************
       * Check if data is already in table, if so update it else insert it.
       * In this Table their should only be one Unit, this is the master that all
       * other table are "inked" to.
       * Returns
       *   true if data is in table
       *   false if data not found in table
       *****************************************************************************/
      public static function &InsertUpdateCheck($ID)
      {
        $query = self::doQuery("SELECT * FROM `mbcounselors` WHERE `MemberID`='$ID'");
        if (!$query) {
        }

        if (mysqli_num_rows($query) > 0) {
          $result = true;
        } else {
          $result = false;
        }

        return $result;
      }
      /******************************************************************************
       * Check if data is already in table, if so update it else insert it.
       * In this Table their should only be one Unit, this is the master that all
       * other table are "inked" to.
       * Parameters:
       *   Connector to Database
       *   First Name
       *   Last Name
       *   Merit Badge
       *
       * Returns
       *   true if data is in table
       *   false if data not found in table
       *****************************************************************************/
      public static function &InsertUpdateMeritBadge($FirstName, $LastName, $MeritBadge)
      {
        $sqlQuery = sprintf(
          "SELECT * FROM `mbccounselormerit` WHERE `FirstName`='%s' AND `LastName`='%s' AND `MeritName`='%s'",
          $FirstName,
          $LastName,
          $MeritBadge
        );
        $query = self::doQuery($sqlQuery);

        if (!$query) {
          die('Error: InsertUpdateMeritBadge()');
        }

        if (mysqli_num_rows($query) > 0) {
          // Found record needs an Update
          $result = true;
        } else {
          // Not record found needs an Insert
          $result = false;
        }

        return $result;
      }
      /*=============================================================================
     *
     * This function will return left length of a string
     * 
     *===========================================================================*/
      public static function left($str, $length)
      {
        return substr($str, 0, $length);
      }
      /*=============================================================================
     *
     * This function will return mid length of a string
     * 
     *===========================================================================*/
      public static function mid($str, $start, $length)
      {
        return substr($str, $start, $length);
      }
      /*=============================================================================
     *
     * This function will return right length of a string
     * 
     *===========================================================================*/
      public static function right($str, $length)
      {
        return substr($str, -$length);
      }
    }
