<?php
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

/******************************************************************************
 * 
 * 
 * 
 *****************************************************************************/
function setYear($yr)
{
  $_SESSION['year'] = $yr;
}
/******************************************************************************
 * 
 * 
 * 
 *****************************************************************************/


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
class CMBCollege
{
  /**
   * The Singleton's instance is stored in a static field. This field is an
   * array, because we'll allow our Singleton to have subclasses. Each item in
   * this array will be an instance of a specific Singleton's subclass. You'll
   * see how this works in a moment.
   */
  private static $instances = [];
  private static $year;

  protected $dbConn;  // Declared here to fix dynamic property deprecation

  /**
   * The Singleton's constructor should always be private to prevent direct
   * construction calls with the `new` operator.
   */
  protected function __construct() {}

  /**
   * Singletons should not be cloneable.
   */
  protected function __clone() {}

  /**
   * Singletons should not be restorable from strings.
   */
  public function __wakeup()
  {
    throw new \Exception("Cannot unserialize a singleton.");
  }

  /**
   * This is the static method that controls the access to the singleton
   * instance. On the first run, it creates a singleton object and places it
   * into the static field. On subsequent runs, it returns the client existing
   * object stored in the static field.
   *
   * This implementation lets you subclass the Singleton class while keeping
   * just one instance of each subclass around.
   */
  public static function getInstance()
  {
    $cls = static::class;
    if (!isset(self::$instances[$cls])) {
      self::$instances[$cls] = new static();
    }

    return self::$instances[$cls];
  }

  /**
   *
   * @return DbConn
   */
  private static function initConnection()
  {
    $db = self::getInstance();
    
    $db->dbConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check for connection errors (prevents silent failures)
    if ($db->dbConn->connect_error) {
      throw new Exception("Database connection failed: " . $db->dbConn->connect_error);
    }

    // Use 'utf8mb4' for full Unicode support ('utf8' is deprecated/incomplete)
    $db->dbConn->set_charset('utf8mb4');

    return $db;
  }

  /**
   * @return mysqli
   */
  public static function getDbConn()
  {
    try {
      $db = self::initConnection();
      return $db->dbConn;
    } catch (Exception $ex) {
      $strError = "I was unable to open a connection to the database. " . $ex->getMessage();
      error_log($strError, 0);
      return null;
    }
  }

  /**************************************************************************
   **
   ** doQuery()
   ** Executes a mysqli_query
   **
   *************************************************************************/
  public static function doQuery($sql)  // Removed & reference (not needed for mysqli_result)
  {
    $Result = false;
    try {
      $mysqli = self::getDbConn();
      $Result = $mysqli->query($sql);
      if (!$Result) {
        $strError = "Unable to execute query. sql = " . $sql . " " . __FILE__ . ", " . __LINE__;
        error_log($strError, 0);
      }
    } catch (Exception $ex) {
      $strError = "Unable to execute query. " . $ex->getMessage() . "sql = " . $sql . " " . __FILE__ . ", " . __LINE__;
      error_log($strError, 0);
      $Result = false;
    }
    return $Result;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetYear()
  {
    //        if(!isset(self::$year)){
    if (!isset($_SESSION['year'])) {
      $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details ORDER BY College DESC";
      $result_CollegeYear = self::doQuery($queryCollegeYear);
      if ($result_CollegeYear) {
        $rowCollege = $result_CollegeYear->fetch_assoc();
        $_SESSION['year'] = $rowCollege['College'];
      } else
        $_SESSION['year'] = "2023-2";
    }
    self::$year = $_SESSION['year'];
    //self::$year = GetYear();
    return self::$year;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function SetYear($year)
  {
    $_SESSION['year'] = $year;
    self::$year = $year;
  }

  /*=============================================================================
     * 
     * This function will allow the user to select the year of data to view.
     * 
     *===========================================================================*/
  public static function SelectYear()
  {
?>
    <form method=post>
      <label for='Year'>&nbsp;</label>
      <select class='form-control form-select' id='Year' name='Year'>

        <?php $yr = $_SESSION['year']; ?>
        <!--  First recod is blank "all" -->
        <option value=""> </option>
          <?php
          if (!strcmp($yr, "2022"))
            $Selected = "selected";
          else
            $Selected = "";
          ?>
        <option value='2022' " . $Selected . ">2022</option>

      <?php
      if (!strcmp($yr, "2021")) $Selected = "selected";
      else $Selected = "";
      echo "<option value='2021' " . $Selected . ">2021</option>";

      if (!strcmp($yr, "2020")) $Selected = "selected";
      else $Selected = "";
      echo "<option value='2020' " . $Selected . ">2020</option>";

      if (!strcmp($yr, "2019")) $Selected = "selected";
      else $Selected = "";
      echo "<option value='2019' " . $Selected . ">2019</option>";

      if (!strcmp($yr, "2018")) $Selected = "selected";
      else $Selected = "";
      echo "<option value='2018' " . $Selected . ">2018</option>";

      if (!strcmp($yr, "2017")) $Selected = "selected";
      else $Selected = "";
      echo "<option value='2017' " . $Selected . ">2017</option>";

      if (!strcmp($yr, "2016")) $Selected = "selected";
      else $Selected = "";
      echo "<option value='2016' " . $Selected . ">2016</option>";

      if (!strcmp($yr, "2015")) $Selected = "selected";
      else $Selected = "";
      echo "<option value='2015' " . $Selected . ">2015</option>";

      echo '
      </select>';
      echo "<input class='btn btn-primary btn-sm' type='submit' name='SubmitYear' placeholder='Year' value='SubmitYear' />";
      echo "
    </form>";
    }
    /******************************************************************************
     *
     *****************************************************************************/
    public static function function_alert($msg)
    {
      echo "<script type='text/javascript'>
      alert('$msg');
    </script>";
    }
    /******************************************************************************
     *
     *****************************************************************************/
    // public static function GotoURL($url)
    // {
    //   echo "<script>
    //   location.replace('$url')
    // </script>";
    // }
    /**************************************************************************
     **
     **
     **
     *************************************************************************/
    public static function DisplayWarning($Table)
    {
      ?>
        <div class=WarningMessageContainer style="width:1200px">
          <p>This information is to be used only for authorized purposes on behalf of the Boy Scouts of America, Denver Area Council, Centennial District.
            Disclosing, copying, or making any inappropriate use of this information is strictly prohibited.</p>
        </div>

        <?php
        echo "<br>";

        $lastUpdated = self::GetLastUpdated($Table);
        //$lastUpdated = GetLastUpdated(self::getDbConn(), $Table);
        echo "Content last changed: " . $lastUpdated;
      }
      /**************************************************************************
       **
       **
       **
       *************************************************************************/

      public static function GetLastUpdated($Table)
      {
        $sqlDate = sprintf('SELECT * FROM dates');
        $resultData = mysqli_query(self::getDbConn(), $sqlDate, MYSQLI_STORE_RESULT);
        $row = $resultData->fetch_assoc();
        $lastUpdated = $row[$Table];
        return $lastUpdated;
      }
      /******************************************************************************
       * 
       * This function will update the date the table was last updated
       * 
       *****************************************************************************/
      //    function UpdateLastUpdated($Table){
      //        $lastUpdated = date("m/d/Y");
      //        $sqlStartDate = sprintf('UPDATE `dates` SET');
      //        $sqlDate = sprintf("%s `%s`='%s' WHERE 1", $sqlStartDate, $Table, $lastUpdated);
      //
      //        if(!mysqli_query(self::getDbConn(), $sqlDate)){
      //            echo "Error: " .$sqlDate. "" .mysqli_error(self::getDbConn())."<br />";
      //            //$RecordsInError++;
      //        }
      //    }
      /******************************************************************************
       * 
       * The BSA data is not consistent in the way the number the unit in different
       * Reports so, we will make our own standard.
       * 
       *****************************************************************************/
      public static function &formatUnitNumber($UnitNumber, $UnitGender)
      {

        $Unit = strtok($UnitNumber, ' ');
        $Number = strtok(' ');
        $Gender = strtok(' ');

        $Number = sprintf("%04d", $Number);

        $NewNumber = null;
        switch ($Unit) {
          case "Post":
          case "Crew":
            $NewNumber =  $Unit . " " . $Number . "-NA";
            break;
          case "Troop":
            if (!strcmp($Gender, "(G)") || !strcmp($UnitGender, "G"))
              $NewNumber = $Unit . " " . $Number . "-GT";
            else
              $NewNumber = $Unit . " " . $Number . "-BT";
            break;
          case "Pack":
            if (!strcmp($Gender, "(F)") || !strcmp($UnitGender, "F"))
              $NewNumber = $Unit . " " . $Number . "-FP";
            else
              $NewNumber = $Unit . " " . $Number . "-BP";
            break;
          default;
            break;
        }

        return $NewNumber;
      }
      /*=============================================================================
     *
     * This function will Return Meir Bagde college data
     * 
     *===========================================================================*/
      public static function &GetMBCollegeClasses($college, $period)
      {
        $sql = sprintf(
          "SELECT * FROM college_counselors WHERE `College`='%s' AND `MBPeriod`='%s' ORDER BY `MBName` ASC",
          $college,
          $period
        );
        $result = self::doQuery($sql);

        return $result;
      }
      /*=============================================================================
     *
     * This function will 
     * 
     *===========================================================================*/
      public static function test_input($data)
      {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
      /*=============================================================================
     *
     * This function will 
     * 
     *===========================================================================*/
      public static function GetFormData(string $key): string
      {
        return trim($_POST[$key] ?? '');
      }
      /*=============================================================================
     *
     * This function will format the phone number field to look pretty ;-)
     * 
     *===========================================================================*/
      public static function &formatPhoneNumber($row, $Phone)
      {
        if (!is_null($row)) {
          switch ($row['PrimaryContact']) {
            case "Home":
              $Phone = $row['HomePhone'];
              break;
            case "Mobile":
              $Phone = $row['MobilePhone'];
              break;
            case "Work":
              $Phone = $row['WorkPhone'];
              break;
            default:
              $Phone = "None";
              break;
          }
        }

        $phoneNumber = preg_replace('/[^0-9]/', '', $Phone);

        if (strlen($phoneNumber) > 10) {
          $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
          $areaCode = substr($phoneNumber, -10, 3);
          $nextThree = substr($phoneNumber, -7, 3);
          $lastFour = substr($phoneNumber, -4, 4);

          $phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } else if (strlen($phoneNumber) == 10) {
          $areaCode = substr($phoneNumber, 0, 3);
          $nextThree = substr($phoneNumber, 3, 3);
          $lastFour = substr($phoneNumber, 6, 4);

          $phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } else if (strlen($phoneNumber) == 7) {
          $nextThree = substr($phoneNumber, 0, 3);
          $lastFour = substr($phoneNumber, 3, 4);

          $phoneNumber = $nextThree . '-' . $lastFour;
        }
        return $phoneNumber;
      }
      /*=============================================================================
     *
     * This function will format the zip codefield to look pretty ;-)
     * 
     *===========================================================================*/
      public static function formatEmail($Email)
      {

        $str = "<a href='mailto:" . strtolower($Email) . "?subject=Merit Badge College'>" . strtolower($Email) . "</a>";

        return $str;
      }
      /*=============================================================================
     *
     * This function will 
     * 
    =========================================================================*/
      public static function RemoveNewLine($str)
      {
        $count = null;

        $str = str_replace("\n", "", $str, $Count);
        $str = str_replace("\r", "", $str, $Count);

        return $str;
      }
      /*=============================================================================
     *
     * This function will check is Regisation is open for the college
     * 
    =========================================================================*/
      public static function &RegistrationOpen()
      {
        $return = false;
        $yr = self::GetYear();

        $qryRegOpen = "SELECT * FROM college_details WHERE College = '$yr' ORDER BY College DESC";

        $RegOpen = self::doQuery($qryRegOpen);
        while ($Results = $RegOpen->fetch_assoc()) {
          $Open = $Results['Open'];
          if ($Open) break;
        }

        return $Open;
      }
      /*=============================================================================
     *
     * This function will the number of scouts registered based on the
     * Merit Badge name and Period.
     * 
    =========================================================================*/
      public static function &GetRegisteredScouts($Badge, $Period)
      {

        $sqlReg = sprintf("SELECT * FROM college_registration WHERE MeritBadge='%s' AND Period='%s' AND College='%s'", $Badge, $Period, self::GetYear());
        $result = self::doQuery($sqlReg, MYSQLI_STORE_RESULT);
        //$RegTotal = $result->fetch_assoc();
        $RegTotal = mysqli_num_rows($result);
        return $RegTotal;
      }
      /*=============================================================================
     *
     * This function will produce a list of merit to counselors 
      * 
     *===========================================================================*/
      function ReportMeritBadges($report_results)
      {
        $MBName = "";
        while ($row = $report_results->fetch_assoc()) {


          if ($MBName != $row['MBName']) {
            $MBName = $row['MBName'];
            $sqlMBName = "SELECT * FROM meritbadges WHERE MeritName ='$MBName'";
            echo "</table class='table'>";
            $Result_MB = self::doQuery($sqlMBName);


            while ($rowMB = $Result_MB->fetch_assoc()) {

              echo "<h3>", $rowMB['MeritName'], "</h3>", "Requirments: ", $rowMB['RequirementsRevised'];
            }
            echo "<br>";
            echo "<table class='table table-light tl1 tl2 tl3 tc4 tc5' style='width:600px';>";
            echo "<td style='width:50px'>";
            echo "<td style='width:250px'>";
            echo "<td style='width:150px'>";
            echo "<td style='width:50px'>";
            echo "<td style='width:50px'>";
            echo "<td style='width:50px'>";
            echo "<tr>";
            echo "<th>Period</th>";
            echo "<th>Merit badge</th>";
            echo "<th>Counselor</th>";
            // Don't display on this page.   echo "<th>Email</th>";
            echo "<th>Size</th>";
            echo "<th>Reg</th>";
            echo "<th>Room</th>";
            echo "</tr>";
          }
          // Get Number registered for each of the Periods.
          $Registered = self::GetRegisteredScouts($MBName, $row['MBPeriod']);
          echo "<tr><td>" .

            $row['MBPeriod'] . "</td><td>" .
            $row['MBName'] . "</td><td>" .
            $row['FirstName'] . " " . $row['LastName'] . "</td><td>" .
            $row['MBCSL'] . "</td><td>"  .
            $Registered . "</td><td>"  .
            $row['MBRoom'] . "</td><td>";
        }
        echo "</table>";
      }
      /*=============================================================================
    *
    * This function will produce a list of merit to counselors 
    * if the flag All is set it will include counselors that are list Troop Only
    * Along with counselors with the DoNotPublish flag.
    * 
    *===========================================================================*/
      public static function ReportMeritBadges1($report_results)
      {
        $Fname = "";
        $Lname = "";
        $Expired = false;
        $TodaysDate = strtotime("now");
        $Counselor = "";


        while ($row = $report_results->fetch_assoc()) {

          // If New Counselor, shutdown old table and create a new one.
          if ($Counselor != $row['BSAId']) {
            $Counselor = $row['BSAId'];
            echo "</table>";
            echo "<br>";
            echo "<h3>", $row['FirstName'], " ", $row['LastName'], "</h3>";

            $Phone = $row['Phone'];
            self::formatPhoneNumber(NULL, $Phone);
            $HomePhone = "<b> Phone: </b>" . $Phone;
            $Email = self::formatEmail($row['Email']);
            // Don;t display it on this page which is open to the world. echo $HomePhone, "<b> Email: </b>", $Email;
            echo "<table class='table table-light tl1 tl2 tl3 tc4 tc5' style='width:512px';>";
            echo "<td style='width:50px'>";
            echo "<td style='width:150px'>";
            echo "<td style='width:10px'>";
            echo "<td style='width:10px'>";
            echo "<td style='width:10px'>";
            echo "<tr>";
            echo "<th>Period</th>";
            echo "<th>Merit badge</th>";
            echo "<th>Size</th>";
            echo "<th>Reg</th>";
            echo "<th>Room</th>";
            echo "</tr>";
          }
          // Get Number registered for each of the Periods.
          $Registered = self::GetRegisteredScouts($row['MBName'], $row['MBPeriod']);


          echo "<tr><td>" .
            $row['MBPeriod'] . "</td><td>" .
            $row['MBName'] . "</td><td>" .
            $row['MBCSL'] . "</td><td>"  .
            $Registered . "</td><td>"  .
            $row['MBRoom'] . "</td></tr>";
        }
        echo "</table>";
      }
      /*=============================================================================
     *
     * This function will produce a list of merit to room
      * 
     *===========================================================================*/
      function ReportMeritBadgesRoom($report_results)
      {

        $csv_hdr = "College, First Name, Last Name, Email, Phone, Period, Merit Badge, First Name Scout, Last Name Scout, Scouts Email";
        $csv_output = "";

        $MBRoom = "";

        while ($row = $report_results->fetch_assoc()) {

          // If New Room, shutdown old table and create a new one.
          if ($MBRoom != $row['MBRoom']) {
            $MBRoom = $row['MBRoom'];
        ?>
            </table>
            <p style="page-break-after: always;">&nbsp;</p>
            <br>
            <h2><?php echo "$MBRoom"; ?></h2>
            <table class='table'>
              <td style='width:200px'>
              <td style='width:200px'>
              <td style='width:150px'>
              <td style='width:100px'>
              <td style='width:100px'>
              <td style='width:100px'>
              <td style='width:150px'>
                <tr>
                  <th>Period</th>
                  <th>Merit badge</th>
                  <th>Scout</th>
                  <th>Unit</th>
                </tr>
            <?php
          }
          // Now get the Counselor data for the Merit Badge.
          $qryByPeriod = sprintf("SELECT * FROM college_registration 
                WHERE MeritBadge='%s' AND Period='%s' AND College='%s'", $row['MBName'], $row['MBPeriod'], self::GetYear());
          $resultByPeriod = self::doQuery($qryByPeriod, self::GetYear());
          while ($rowPeriod = $resultByPeriod->fetch_assoc()) {
            if (mysqli_num_rows($resultByPeriod) == 0) {
              // PROBLEM !! No Counselor found for this merit badge in selected Period !!!
              $Formatter = "<b style='color:red;'>";
              $FirstName = "";
              $LastName = "";
              $Unit = "";
            } else {
              // If scout has not paid flag
              if ($rowPeriod['Registration'] <= 0)
                $Formatter = "<b style='color:green;'>";
              else
                $Formatter = "";
              $FirstName = $rowPeriod['FirstNameScout'];
              $LastName = $rowPeriod['LastNameScout'];
              $Unit = $rowPeriod['UnitType'] . " " . $rowPeriod['UnitNumber'];
              $MBRoom = $row['MBRoom'];
            }


            echo "<tr><td>" .
              $Formatter . self::GetPeriodTime($row['MBPeriod']) . "</td><td>" .
              $Formatter . $row['MBName'] . "</td><td>" .
              $Formatter . $FirstName . " " . $LastName . "</td><td>" .
              $Formatter . $Unit . "</td></tr>";


            $csv_output .= self::GetYear() . ",";
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
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetDate($College)
        {
          $sql = sprintf("SELECT Date FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          if ($row != null)
            return $row['Date'];
          else
            return null;
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetLocation($College)
        {
          $sql = sprintf("SELECT Location FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          if ($row != null)
            return $row['Location'];
          else
            return null;
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetAddress($College)
        {
          $sql = sprintf("SELECT Address FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          if ($row != null)
            return $row['Address'];
          else
            return null;
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetStartTime($College)
        {
          $sql = sprintf("SELECT StartTime FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          if ($row != null)
            return $row['StartTime'];
          else
            return null;
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetEndTime($College)
        {
          $sql = sprintf("SELECT EndTime FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          if ($row != null)
            return $row['EndTime'];
          else
            return null;
        }
        /*=============================================================================
    *
    * Return the times for the MB period.
    * 
    *===========================================================================*/
        public static function GetPeriodTime($Period)
        {
          switch ($Period) {
            case 'A':
              $Time = self::GetPeriodATime(self::GetYear());
              break;
            case 'B':
              $Time = self::GetPeriodBTime(self::GetYear());
              break;
            case 'C':
              $Time = self::GetPeriodCTime(self::GetYear());
              break;
            case 'D':
              $Time = self::GetPeriodDTime(self::GetYear());
              break;
            case 'E':
              $Time = self::GetPeriodETime(self::GetYear());
              break;
            case 'F':
              $Time = self::GetPeriodFTime(self::GetYear());
              break;
            case 'AB':
              $Time = self::GetPeriodABTime(self::GetYear());
              break;
            case 'CD':
              $Time = self::GetPeriodCDTime(self::GetYear());
              break;
            default:
              $Time = 'Unknow';
              break;
          }
          return $Time;
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodATime($College)
        {
          $sql = sprintf("SELECT PeriodA FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodA'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodBTime($College)
        {
          $sql = sprintf("SELECT PeriodB FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodB'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetLunchTime($College)
        {
          $sql = sprintf("SELECT Lunch FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['Lunch'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodCTime($College)
        {
          $sql = sprintf("SELECT PeriodC FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodC'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodDTime($College)
        {
          $sql = sprintf("SELECT PeriodD FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodD'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodETime($College)
        {
          $sql = sprintf("SELECT PeriodE FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodE'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodFTime($College)
        {
          $sql = sprintf("SELECT PeriodF FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodF'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodABTime($College)
        {
          $sql = sprintf("SELECT PeriodAB FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodAB'];
        }
        /*=============================================================================
    *
    * Return the data of the college
    * 
    *===========================================================================*/
        public static function GetPeriodCDTime($College)
        {
          $sql = sprintf("SELECT PeriodCD FROM college_details WHERE College = '%s'", $College);
          $Result = self::doQuery($sql);

          $row = $Result->fetch_assoc();

          return $row['PeriodCD'];
        }
        /*=============================================================================
    *
    * This function will produce a schedule of Scouts Merit Badge Classes 
    * 
    *===========================================================================*/
        public static function ReportStats($Period)
        {
          $CollegeYear = self::getYear();

          if (!strcmp($Period, 'Totals')) {
            // Get Number of Merit Badges for College
            $qryNumofBadgesPeriod = "SELECT * FROM college_counselors WHERE `College`='$CollegeYear'";
            $ResultNumofBadgesPeriod = self::doQuery($qryNumofBadgesPeriod);
            $NumofBadges = mysqli_num_rows($ResultNumofBadgesPeriod);

            //Get Number of Distinct Scouts in College
            $qryNumofScouts = "SELECT DISTINCTROW FirstNameScout, LastNameScout FROM college_registration WHERE `College`='$CollegeYear'";
            $ResultNumofScouts = self::doQuery($qryNumofScouts);
            $NumofScouts = mysqli_num_rows($ResultNumofScouts);
            //$NumofScouts = $NumofScouts." Scouts not MB";
            //Get Number of Distinct Counselors in College
            $qryNumofCounselors = "SELECT DISTINCTROW FirstName, LastName FROM college_counselors WHERE `College`='$CollegeYear'";
            $ResultNumofCounselors = self::doQuery($qryNumofCounselors);
            $NumofCounselors = mysqli_num_rows($ResultNumofCounselors);

            //Get Number of Seats in college
            $qryNumofSeats = "SELECT SUM(MBCSL) FROM college_counselors WHERE `College`='$CollegeYear'";
            $ResultNumofSeats = self::doQuery($qryNumofSeats);
            $SumSeats = $ResultNumofSeats->fetch_assoc();
            $NumofSeats = $SumSeats['SUM(MBCSL)'];

            $Percent = "";
          } else {

            // Get Number of Merit Badges for Period
            $qryNumofBadgesPeriod = "SELECT * FROM college_counselors WHERE `MBPeriod`='$Period' AND `College`='$CollegeYear'";
            $ResultNumofBadgesPeriod = self::doQuery($qryNumofBadgesPeriod);
            $NumofBadges = mysqli_num_rows($ResultNumofBadgesPeriod);
            $NumofCounselors = $NumofBadges;    // each badge must have a counselor

            //Get Number of Scout in Period
            $qryNumofScouts = "SELECT * FROM college_registration WHERE `Period`='$Period' AND `College`='$CollegeYear'";
            $ResultNumofScouts = self::doQuery($qryNumofScouts);
            $NumofScouts = mysqli_num_rows($ResultNumofScouts);

            //Get Number of Seats in Period
            $qryNumofSeats = "SELECT SUM(MBCSL) FROM college_counselors WHERE `MBPeriod`='$Period' AND `College`='$CollegeYear'";
            $ResultNumofSeats = self::doQuery($qryNumofSeats);
            $SumSeats = $ResultNumofSeats->fetch_assoc();
            $NumofSeats = $SumSeats['SUM(MBCSL)'];

            $Percent = 0;
            if ($NumofSeats != 0)
              $Percent = number_format(($NumofScouts / $NumofSeats) * 100., 2);
          }

          if ($NumofBadges == 0) {
            echo "</td></tr>";
          } else {
            echo "<tr><td>" .
              $Period . "</td><td>" .
              $NumofBadges . "</td><td>" .
              $NumofScouts . "</td><td>" .
              $NumofSeats  . "</td><td>" .
              $Percent . "</td><td>" .
              $NumofCounselors . "</td></tr>";
          }
        }

        /*=============================================================================
    *
     This function will produce a schedule of Scouts Merit Badge Classes 
     
    ===========================================================================*/
        public static function ReportFinancials()
        {
          $CollegeYear = self::getYear();

          //Get Number of Distinct Scouts in College
          $qryNumofScouts = "SELECT DISTINCTROW FirstNameScout, LastNameScout FROM college_registration WHERE `College`='$CollegeYear'";
          $ResultNumofScouts = self::doQuery($qryNumofScouts);
          $NumofScouts = mysqli_num_rows($ResultNumofScouts);

          $qryCollegeDetails = "SELECT * FROM college_details WHERE `College`='$CollegeYear'";
          $ResultCollegeDetails = self::doQuery($qryCollegeDetails);
          $RowCollegeDetails = $ResultCollegeDetails->fetch_assoc();

          //Get Number of Distinct Counselors in College
          $qryNumofCounselors = "SELECT DISTINCTROW FirstName, LastName FROM college_counselors WHERE `College`='$CollegeYear'";
          $ResultNumofCounselors = self::doQuery($qryNumofCounselors);
          $NumofCounselors = mysqli_num_rows($ResultNumofCounselors);

          //Get sum of "other" fees per merit badge

          $qryMBDetails = "SELECT SUM(MeritBadgeCost) FROM college_registration WHERE `College`='$CollegeYear'";
          $ResultMBDetails = self::doQuery($qryMBDetails);
          $RowMBDetails = $ResultMBDetails->fetch_assoc();


          $TotalScoutfee = $RowCollegeDetails['Fee/Scout'] * $NumofScouts + $RowMBDetails['SUM(MeritBadgeCost)'];
          $TotalLunchCost = $RowCollegeDetails['LunchCost'] * ($NumofScouts + $NumofCounselors);
          $TotalCouncilfee = $RowCollegeDetails['%ToCouncil'] * ($RowCollegeDetails['Fee/Scout'] * $NumofScouts);
          $ProfitLoss =  $TotalScoutfee - $TotalLunchCost - $TotalCouncilfee - $RowCollegeDetails['FacilityCost'];

          if ($ProfitLoss <= 0)
            $Formatter = "<b style='color:red;'>";
          else
            $Formatter = "<b style='color:green;'>";

          echo "<tr><td>" .
            "Fee/Scout" . "</td><td>" .
            $RowCollegeDetails['Fee/Scout'] . "</td><td>" .
            $TotalScoutfee . "</td><td>" .

            "</tr><tr><td>" .
            "Facility/Cost" . "</td><td>" .
            " " . "</td><td>" .
            $RowCollegeDetails['FacilityCost'] . "</td><td>" .


            "</tr><tr><td>" .
            "Lunch Cost" . "</td><td>" .
            $RowCollegeDetails['LunchCost'] . "</td><td>" .
            $TotalLunchCost . "</td><td>" .

            "</tr><tr><td>" .
            "Council/Fee" . "</td><td>" .
            $RowCollegeDetails['%ToCouncil'] . "%" . "</td><td>" .
            $TotalCouncilfee . "</td><td>" .

            "</tr><tr><td>" .
            "Profit/Loss" . "</td><td>" .
            " " . "</td><td>" .
            $Formatter . $ProfitLoss . "</td></tr>";
        }
        /*=============================================================================
    *
    * This function will produce a list of which districts have signed up  for
    * the college.
    * 
    *===========================================================================*/
        public static function ReportByDistrict()
        {
          $CollegeYear = self::getYear();

          //Get Number of Distinct's'
          $qryNumofDistricts = "SELECT DISTINCTROW District FROM college_registration WHERE `College`='$CollegeYear'
           ORDER BY District, UnitNumber";
          $ResultNumofDistricts = self::doQuery($qryNumofDistricts);
          //$NumofDistricts = mysqli_num_rows($ResultNumofDistricts);

          while ($row = $ResultNumofDistricts->fetch_assoc()) {
            // Number get the numbers
            $qryNum = sprintf("SELECT DISTINCTROW LastNameScout, FirstNameScout FROM college_registration 
               WHERE `College`='%s' AND `District`='%s'", $CollegeYear, $row['District']);
            $ResultNum = self::doQuery($qryNum);
            $DistrictName = $row['District'];
            $DistrictTotal = mysqli_num_rows($ResultNum);
            echo "<tr><td>" .
              $DistrictName . "</td><td>" .
              $DistrictTotal . "</td><td>";



            // Number Units in that district that have scouts attending
            $qryUnit = sprintf("SELECT DISTINCTROW UnitType, UnitNumber FROM college_registration 
               WHERE `College`='%s' AND `District`='%s' ORDER BY UnitNumber", $CollegeYear, $row['District']);
            $ResultNumofUnitss = self::doQuery($qryUnit);
            while ($rowUnit = $ResultNumofUnitss->fetch_assoc()) {
              $UnitNumber = $rowUnit['UnitNumber'];
              $UnitType =  $rowUnit['UnitType'];
              $qryNumofSscouts = sprintf("SELECT DISTINCTROW LastNameScout, FirstNameScout FROM college_registration 
                    WHERE `College`='%s' AND `District`='%s' AND `UnitNumber`='%s' AND `UnitType`='%s'", $CollegeYear, $row['District'],  $UnitNumber, $UnitType);
              $ResultNumofScouts = self::doQuery($qryNumofSscouts);
              //$Scouts = $ResultNumofScouts->fetch_assoc();
              $SumScouts = mysqli_num_rows($ResultNumofScouts);

              echo $rowUnit['UnitType'] . "</td><td>" .
                $rowUnit['UnitNumber'] . "</td><td>" .
                $SumScouts . "</td></tr>" .
                // Start a new row and space over two columns
                "<tr><td>" .
                '' . "</td><td>" .
                '' . "</td><td>";
            }
          }
        }
        /*=============================================================================
    *
    * This function will produce a report that maybe sent to Council for the 
    * Double Knot signup
    * 
    *===========================================================================*/
        public static function ReportDoubleKnot($results)
        {
          $csv_hdr = "Time^ Merit Badge^ Counselor^ Email^ Prerequisities^ Notes^ Class Size^ Fee";
          $csv_output = null;


          echo "<table class='table'  style='width:1340';>";
          echo "<td style='width:140px'>";
          echo "<td style='width:200px'>";
          echo "<td style='width:100px'>";
          echo "<td style='width:10px'>";
          echo "<td style='width:200px'>";
          echo "<td style='width:200px'>";
          echo "<td style='width:10px'>";
          echo "<td style='width:60px'>";
          echo "<tr>";
          echo "<th>Period</th>";
          echo "<th>Merit badge</th>";
          echo "<th>Counselor</th>";
          echo "<th>Email</th>";
          echo "<th>Prerequisities</th>";
          echo "<th>Notes</th>";
          echo "<th>Class Size</th>";
          echo "<th>MB Fee</th>";

          echo "</tr>";

          while ($row = $results->fetch_assoc()) {
            switch ($row['MBPeriod']) {
              case 'A':
                $Time = self::GetPeriodATime(self::GetYear());
                break;
              case 'B':
                $Time = self::GetPeriodBTime(self::GetYear());
                break;
              case 'C':
                $Time = self::GetPeriodCTime(self::GetYear());
                break;
              case 'D':
                $Time = self::GetPeriodDTime(self::GetYear());
                break;
              case 'E':
                $Time = self::GetPeriodETime(self::GetYear());
                break;
              case 'F':
                $Time = self::GetPeriodFTime(self::GetYear());
                break;
              case 'AB':
                $Time = self::GetPeriodABTime(self::GetYear());
                break;
              case 'CD':
                $Time = self::GetPeriodCDTime(self::GetYear());
                break;
              default:
                $Time = 'Unknow';
                break;
            }

            $Fee = sprintf("$%2.2f</td></tr>", number_format($row['MBFee'], 2, '.', ''));

            echo "<tr><td>" .
              $Time . "</td><td>" .
              $row['MBName'] . "</td><td>" .
              $row['FirstName'] . " " . $row['LastName'] . "</td><td>" .
              $row['Email'] . "</td><td>" .
              $row['MBPrerequisities'] . "</td><td>" .
              $row['MBNotes'] . "</td><td>" .
              $row['MBCSL'] . "</td><td>" .
              $Fee;

            //Now create for CSV file
            $csv_output .= $Time . "^";
            $csv_output .= $row['MBName'] . "^";
            $csv_output .= $row['FirstName'] . " " . $row['LastName'] . "^";
            $csv_output .= $row['Email'] . "^";
            $csv_output .= $row['MBPrerequisities'] . "^";
            $csv_output .= $row['MBNotes'] . "^";
            $csv_output .= $row['MBCSL'] . "^";
            $csv_output .= sprintf("$%2.2f\n", number_format($row['MBFee'], 2, '.', ''));
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
    * This function will produce a list of which districts have signed up  for
    * the college.
    * 
    *===========================================================================*/
        public static function SelectCollegeYear($CollegeYear, $Title, $bPreview)
        {
          $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details WHERE College > 0 ORDER BY College DESC";
          $result_CollegeYear = self::doQuery($queryCollegeYear);

          ?>
            <form method=post>
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token()); ?>">
              <div class="row  d-print-none">
                <div class="col-2">
                  <select class='form-control' id='CollegeYear' name='CollegeYear'>

                    <option value=""> </option>
                    <?php
                    while ($rowCollege = $result_CollegeYear->fetch_assoc()) {
                      if (!strcmp($rowCollege['College'], $CollegeYear)) {
                        echo "<option selected value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
                      } else
                        echo "<option value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
                    }
                    echo "<option value=-1>New</option>";
                    ?>
                  </select>
                </div>

                <?php
                if ($bPreview) { ?>
                  <div class="col-1">
                    <input type='checkbox' name='Preview' id='chkPreview' value='1' />
                    <label for='chkPreview'>Preview Email(s) </label>
                  </div>
                <?php } ?>
                <div class="col-2">
                  <input class='btn btn-primary btn-sm' type='submit' name='SubmitCollege' value='Select College' />
                </div>
              </div>
            </form>
          <?php
        }
        /*=============================================================================
    *
    * This function will have two selections one for the College year and the other
    * for ta Scout
    * 
    *===========================================================================*/
        public static function SelectCollegeYearandScout($CollegeYear, $Title, $bPreview)
        {
          $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details WHERE College > 0 ORDER BY College DESC";
          $result_CollegeYear = self::doQuery($queryCollegeYear);

          $queryScouts = "SELECT DISTINCTROW LastNameScout, FirstNameScout, BSAIdScout FROM college_registration
          WHERE college='$CollegeYear' ORDER BY LastNameScout, FirstNameScout";
          ?>
            <form method=post>
              <div class="row  d-print-none">
                <div class="col-2">
                  <select class='form-control' id='CollegeYear' name='CollegeYear'>

                    <option value=""> </option>
                    <?php
                    while ($rowCollege = $result_CollegeYear->fetch_assoc()) {
                      if (!strcmp($rowCollege['College'], $CollegeYear)) {
                        echo "<option selected value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
                      } else
                        echo "<option value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
                    }
                    echo "<option value=-1>New</option>";
                    ?>
                  </select>

                  <?php
                  if ($bPreview) { ?>
                    <input type='checkbox' name='Preview' id='chkPreview' value='1' />
                    <label for='chkPreview'>Preview Email(s) </label>
                  <?php } ?>
                </div>
                <div class="col-2">
                  <input class='btn btn-primary btn-sm' type='submit' name='SubmitCollege' value='Select College' />
                </div>
                <!-- </div> -->
            </form>
            <?php
            $result_ByScout = self::doQuery($queryScouts);
            if (!$result_ByScout) {
              self::function_alert("ERROR: self->doQuery($queryScouts)");
            }
            ?>
            <div class="col-2">
              <form id="ScoutName" method=post>
                <select class='form-select' id='ScoutName' name='ScoutName'>
                  <option value=""> </option>
                  <option value=-1>Add New</option>
                  <?php while ($rowCollegeMBs = $result_ByScout->fetch_assoc()) {
                    echo "<option value=" . $rowCollegeMBs['BSAIdScout'] . ">" . $rowCollegeMBs['LastNameScout'] . " " . $rowCollegeMBs['FirstNameScout'] . "</option>";
                  } ?>
                </select>
            </div>
            <div class="col-1">
              <input class='btn btn-primary btn-sm' type='submit' name='SubmitScout' value='Select Scout' />
            </div>
    </form>
    </div>

  <?php
        }
        /*=============================================================================
    *
    * This function will produce display the location of the college and the POC
    * 
    *===========================================================================*/
        public static function DisplayCollegeDetails($CollegeYear)
        {
          $queryCollegeYear = "SELECT * FROM college_details WHERE College='$CollegeYear'";
          $result_CollegeYear = self::doQuery($queryCollegeYear);
          if ($result_CollegeYear) {
            $rowCollege = $result_CollegeYear->fetch_assoc();

            echo "</br>Location: " . $rowCollege['Location'];
            echo "</br>Address : " . $rowCollege['Address'];
            echo "</br>Contact : " . $rowCollege['Contact'];
            echo "</br>";
          }
        }
        /*=============================================================================
    *
    * This function will produce display the location of the college and the POC
    * 
    *===========================================================================*/
        public static function DisplaySelectCollegeYear()
        {
          $queryCollegeYear = "SELECT DISTINCTROW College FROM college_details ORDER BY College DESC";
          $result_CollegeYear = self::doQuery($queryCollegeYear);
  ?>
    <form method=post>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
      <div class="row  py-3 d-print-none">
        <div class="col-2">
          <select class='form-control' id='CollegeYear' name='CollegeYear'>

            <option value=""> </option>
            <?php
            while ($rowCollege = $result_CollegeYear->fetch_assoc()) {
              if (!strcmp($rowCollege['College'], self::GetYear())) {
                echo "<option selected value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
              } else
                echo "<option value=" . $rowCollege['College'] . ">" . $rowCollege['College'] . "</option>";
            } ?>
          </select>

        </div>
        <div class="col-2">
          <input class='btn btn-primary btn-sm' type='submit' name='SubmitCollege' value='Select College' />
        </div>
      </div>
    </form>
<?php
        }
        /*=============================================================================
    *
    * Add or update the college details 
    * 
    *===========================================================================*/
        public static function AddUpdateCollege($CollegeDetails)
        {
          // Check if we are adding a new college or updated an old one.
          $queryCollegeYear = "SELECT * FROM college_details WHERE `College`='$CollegeDetails[College]'";
          $result_CollegeYear = self::doQuery($queryCollegeYear);

          if (mysqli_num_rows($result_CollegeYear) !== 0) {
            $sqlUpdate = sprintf(
              "UPDATE `college_details` SET `Open`='%d' ,`College`='%s',`Location`='%s',`Address`='%s',
            `Contact`='%s', `Phone`='%s', `Email`='%s',
            `Fee/Scout`='%.2f', `FacilityCost`='%.2f', `LunchCost`='%.2f', `%%ToCouncil`='%.2f', `Profit/Loss`='%.2f', 
            `PeriodA`='%s', `PeriodB`='%s', `PeriodC`='%s', `PeriodD`='%s',
            `PeriodAB`='%s', `PeriodCD`='%s', `PeriodE`='%s', `PeriodF`='%s',
            `Lunch`='%s', `Date`='%s', `StartTime`='%s', `EndTime`='%s', 
            `Notes`='%s'
            WHERE `College`='%s'",
              $CollegeDetails['Open'],
              $CollegeDetails['College'],
              $CollegeDetails['Location'],
              $CollegeDetails['Address'],
              $CollegeDetails['Contact'],
              $CollegeDetails['Phone'],
              $CollegeDetails['Email'],
              $CollegeDetails['Fee/Scout'],
              $CollegeDetails['FacilityCost'],
              $CollegeDetails['LunchCost'],
              $CollegeDetails['ToCouncil'],
              $CollegeDetails['Profit/Loss'],
              $CollegeDetails['PeriodA'],
              $CollegeDetails['PeriodB'],
              $CollegeDetails['PeriodC'],
              $CollegeDetails['PeriodD'],
              $CollegeDetails['PeriodAB'],
              $CollegeDetails['PeriodCD'],
              $CollegeDetails['PeriodE'],
              $CollegeDetails['PeriodF'],
              $CollegeDetails['Lunch'],
              $CollegeDetails['Date'],
              $CollegeDetails['StartTime'],
              $CollegeDetails['EndTime'],
              $CollegeDetails['Notes'],
              $CollegeDetails['College']
            );
          } else {
            // Add
            $sqlUpdate = sprintf(
              "INSERT INTO `college_details`(`Open`, `College`, `Location`, `Address`, 
            `Contact`, `Phone`, `Email`, 
            `Fee/Scout`, `FacilityCost`, `LunchCost`, `%%ToCouncil`, `Profit/Loss`, 
            `PeriodA`, `PeriodB`, `PeriodC`, `PeriodD`, 
            `PeriodAB`, `PeriodCD`, `PeriodE`, `PeriodF`, 
            `Lunch`, `Date`, `StartTime`, `EndTime`, 
            `Notes`) VALUES (
            '%d' ,'%s','%s','%s',
            '%s', '%s', '%s',
            '%.2f', '%.2f', '%.2f', '%.2f', '%.2f', 
            '%s', '%s', '%s', '%s', 
            '%s', '%s', '%s', '%s', 
            '%s', '%s', '%s', '%s', 
            '%s') 
            ",
              $CollegeDetails['Open'],
              $CollegeDetails['College'],
              $CollegeDetails['Location'],
              $CollegeDetails['Address'],
              $CollegeDetails['Contact'],
              $CollegeDetails['Phone'],
              $CollegeDetails['Email'],
              $CollegeDetails['Fee/Scout'],
              $CollegeDetails['FacilityCost'],
              $CollegeDetails['LunchCost'],
              $CollegeDetails['ToCouncil'],
              $CollegeDetails['Profit/Loss'],
              $CollegeDetails['PeriodA'],
              $CollegeDetails['PeriodB'],
              $CollegeDetails['PeriodC'],
              $CollegeDetails['PeriodD'],
              $CollegeDetails['PeriodAB'],
              $CollegeDetails['PeriodCD'],
              $CollegeDetails['PeriodE'],
              $CollegeDetails['PeriodF'],
              $CollegeDetails['Lunch'],
              $CollegeDetails['Date'],
              $CollegeDetails['StartTime'],
              $CollegeDetails['EndTime'],
              $CollegeDetails['Notes']

            );
          }

          if (!self::doQuery($sqlUpdate)) {
            $strError = "I was unable to execute query. " . $sqlUpdate;
            error_log($strError, 0);
            self::function_alert("Query failed see error for details");
          }
        }
      }


// Helper function (put somewhere in utils or CMBCollege class)
function get_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}      