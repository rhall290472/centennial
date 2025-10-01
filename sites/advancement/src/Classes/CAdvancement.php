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
class CAdvancement
{
  /**
   * The Singleton's instance is stored in a static field. This field is an
   * array, because we'll allow our Singleton to have subclasses. Each item in
   * this array will be an instance of a specific Singleton's subclass. You'll
   * see how this works in a moment.
   */
  private static $instances = [];
  private static $year;

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

  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function getConfigData()
  {

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      //ip from share internet
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      //ip pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    $userdata  = array();

    if (!strcmp($ip, "::1")) {
      $userdata['dbhost'] = "localhost";
      $userdata['dbuser'] = "webuser";
      $userdata['dbpass'] = "webuser";
      $userdata['db']     = "centennial";
    } else if ((isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
      $userdata['dbhost'] = "rhall29047217205.ipagemysql.com";
      $userdata['dbuser'] = "centennial";
      $userdata['dbpass'] = "w3frRWX^&q";
      $userdata['db']     = "centennial";
    } else {
      $userdata['dbhost'] = "rhall29047217205.ipagemysql.com";
      $userdata['dbuser'] = "webuser1";
      $userdata['dbpass'] = "webuser1";
      $userdata['db']     = "centennial";
    }

    return $userdata;
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
    $connConf = self::getConfigData();
    $db->dbConn = new mysqli($connConf['dbhost'], $connConf['dbuser'], $connConf['dbpass'], $connConf['db']);
    $db->dbConn->set_charset('utf8');
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
   ** Excutes a mysqli_query
   **
   *************************************************************************/
  public static function &doQuery($sql)
  {
    $Result = null;
    try {
      $mysqli = self::getDbConn();
      $Result = $mysqli->query($sql);
      if (!$Result) {
        $strError = $mysqli->error;
        error_log($strError, 0);
      }
    } catch (Exception $ex) {
      $strError = "I was unable to execute query. " . $ex->getMessage();
      error_log($strError, 0);
      $Result = null;
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
    if (isset($_SESSION['year']))
      self::$year = $_SESSION['year'];
    else
      self::$year = Date("Y");
    return self::$year;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function SetYear($year)
  {
    self::$year = $year;
  }

  /*=============================================================================
     *
     * This function will allow the user to select the year of data to view.
     * 
     *===========================================================================*/
  public static function SelectYear()
  {
    $yr = date("Y");
    if (isset($_SESSION['year']))
      $yr = $_SESSION['year'];

    // This will call the function once the user and selected a troop and click submit
    $sqlGetDates = "SELECT DISTINCT Expire_Date FROM membershiptotals";
    if (!$result = self::doQuery($sqlGetDates)) {
      //Report Error
    } else { ?>
      <!--  From Calling function needs this line #### <div class="col-1"> -->
      <!-- <div class="col-1"> -->
      <form class="d-print-none" method=post>
        <label for='Year'>&nbsp;</label>
        <select class='form-control d-print-none' id='Year' name='Year'>

          <?php
          while ($row = $result->fetch_assoc()) {
            $arrYears[] = date("Y", strtotime($row['Expire_Date']));
          }
          $Year = array_unique($arrYears);
          rsort($Year);
          $index = 0;
          while ($index < count($Year)) {
            if (!strcmp($yr, $Year[$index])) $Selected = "selected";
            else $Selected = "";
            echo "<option value=" . $Year[$index] . " " . $Selected . ">$Year[$index]</option>";
            $index++;
          }
          ?>
        </select>
        </div>
        <div class="col-1 py-6">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <input class='btn btn-primary btn-sm d-print-none' type='submit' name='SubmitYear' placeholder='Year' value='Set Year' />
        </div>
      </form>
    <?php
    }
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  function function_alert($msg)
  {
    echo "<script type='text/javascript'>alert('$msg');</script>";
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function DisplayWarning($Table)
  {
    ?>
    <div class=WarningMessageContainer>
      <p>This information is to be used only for authorized purposes on behalf of Scouting America, Greater Colorado Council, Centennial District.
        Disclosing, copying, or making any inappropriate use of this information is strictly prohibited.</p>
    </div>

<?php
    echo "<br>";

    $lastUpdated = self::GetLastUpdated($Table);
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
  public static function UpdateLastUpdated($Table, $Datestr)
  {
    $lastUpdated = null;
    if (strlen($Datestr) == 0) {
      $lastUpdated = date("m/d/Y");
    } else {
      // Extract date from report string.
      $token = strtok($Datestr, ':');
      $lastUpdated = strtok(':');
      ltrim($lastUpdated);
    }
    $sqlStartDate = sprintf('UPDATE `dates` SET');
    $sqlDate = sprintf("%s `%s`='%s' WHERE 1", $sqlStartDate, $Table, $lastUpdated);

    if (!mysqli_query(self::getDbConn(), $sqlDate)) {
      $strMsg =  "Error: " . $sqlDate . "" . mysqli_error(self::getDbConn());
      error_log($strMsg);
      //$RecordsInError++;
    }
  }
  /******************************************************************************
   * 
   * This function will return the total number of youth is a program
   * (i.e. crew, pack or troop).
   * 
   *****************************************************************************/
  public static function &GetProgramTotalYouth($UnitType)
  {

    // Catch if year is not set.
    if (!isset($_SESSION["year"])) {
      $year = Date("Y");
      $_SESSION["year"] = $year;
    } else
      $year = $_SESSION["year"];

    $sqlUnitSum = sprintf(
      "SELECT SUM(`Total_Youth`) FROM `membershiptotals` WHERE `Unit` LIKE '%s%%' AND `Expire_Date` LIKE '%s%%'",
      $UnitType,
      $year
    );
    $resultUnitSum = mysqli_query(self::getDbConn(), $sqlUnitSum, MYSQLI_STORE_RESULT);
    if ($resultUnitSum) {
      $YouthTotal = $resultUnitSum->fetch_assoc();
      $Total = $YouthTotal["SUM(`Total_Youth`)"];
      if ($_SESSION['year'] != date('Y')) {
        $sqlUnitSum = sprintf(
          "SELECT SUM(`Youth`) FROM `adv_%s` WHERE `Unit` LIKE '%s%%' AND `Date` LIKE '%%%s'",
          strtolower($UnitType),
          $UnitType,
          $year
        );
        $resultUnitSum = mysqli_query(self::getDbConn(), $sqlUnitSum, MYSQLI_STORE_RESULT);
        $YouthTotal = $resultUnitSum->fetch_assoc();
        $Total = $YouthTotal["SUM(`Youth`)"];
      }
    } else {
      $strErr = "ERROR: GetProgramTotalYouth(" . $UnitType . ") - " . $sqlUnitSum;
      error_log($strErr);
    }
    return $Total;
  }
  /******************************************************************************
   * 
   * This function will return the total number of youth in a Unit
   * (i.e. Crew 0113-NA, Pack 0015-FP or Troop 0317-BT).
   * 
   * parameters:
   *  $con - link to database
   *  $Unit - poperly formatted unit number (i.e. Troop 0317-BT)
   *  $AdvYouth - Youth total from adv_x table, this is used in case we can not
   *  find the unit in the membership table
   *  $Year - Year of data we are looking for.
   * 
   *****************************************************************************/
  public static function &GetUnitTotalYouth($Unit, $AdvYouth, $Year)
  {


    $sqlUnitYouth = sprintf(
      "SELECT * FROM `membershiptotals` WHERE `Unit`='%s' AND `Expire_Date` LIKE '%%%s'",
      $Unit,
      $Year
    );
    $resultUnitYouth = self::doQuery($sqlUnitYouth);
    if ($resultUnitYouth) {
      $YouthTotal = $resultUnitYouth->fetch_assoc();
      if ($YouthTotal)
        $UnitYouth = $YouthTotal["Total_Youth"];
      else
        $UnitYouth = $AdvYouth;
    } else {
      // If we can not find the Unit in the membership table, returh the 
      // value found in the adv_ table. Needed to support other years.
      $UnitYouth = $AdvYouth;
    }
    return $UnitYouth;
  }
  /******************************************************************************
   * 
   * This function will return calcuation of rank advancements per Scout
   * 
   *****************************************************************************/
  public static function &GetUnitRankperScout($Youth, $Rank, $Unit)
  {

    if ($Youth > 0)
      $UnitRankScout = sprintf("%0.2f", $Rank / $Youth);
    else
      $UnitRankScout = 0;

    return $UnitRankScout;
  }
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
        if ($UnitGender == null)
          $UnitGender = $UnitNumber[12];
        if (!strcmp($Gender, "(G)") || !strcmp($UnitGender, "G"))
          $NewNumber = $Unit . " " . $Number . "-GT";
        else
          $NewNumber = $Unit . " " . $Number . "-BT";
        break;
      case "Pack":
        if ($UnitGender == null)
          $UnitGender = $UnitNumber[11];
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
  /******************************************************************************
   * Check if data is already in table, if so update it else insert it.
   * In this Table their should only be one Unit, this is the master that all
   * other table are "inked" to.
   * Returns
   *   true if data is in table
   *   false if data not found in table
   *****************************************************************************/
  public static function &InsertUpdateCheck($UnitYear, $Unit)
  {

    switch ($Unit[0]) {
      case 'P':
        $sql = "SELECT * FROM `adv_pack` WHERE `Date` ='$UnitYear' AND `Unit`='$Unit'";
        break;
      case 'T':
        $sql = "SELECT * FROM `adv_troop` WHERE `Date` ='$UnitYear' AND `Unit`='$Unit'";
        break;
      case 'C':
        $sql = "SELECT * FROM `adv_crew` WHERE `Date` ='$UnitYear' AND `Unit`='$Unit'";
        break;
      default:
        "InsertUpdateCheck Unknow unit type" . $Unit . "<br/>";
        $sql = "";
        break;
    }

    $query = mysqli_query(self::getDbConn(), $sql);
    if (!$query) {
      die('InsertUpdateCheck() Error: ' . mysqli_error(self::getDbConn()));
    }

    if (mysqli_num_rows($query) > 0) {
      $result = true;
    } else {
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
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GotoURL($url)
  {
    echo "<script>location.replace('$url')</script>";
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function UpdateMembership($District, $Unit, $UnitID, $YouthTotal)
  {

    // First check to see if we can find the unit to update or do we need to insert
    $Year = self::$year;
    $sql = "SELECT * FROM membershiptotals WHERE `UnitID` = '$UnitID' AND `Expire_Date` LIKE '$Year%' ";
    if (!$result = self::doQuery($sql)) {
      //Report Error
    } else {
      if (($row = $result->fetch_assoc()) == null) {
        // Not found insert
        if ($Unit != null) {
          $sql = "INSERT INTO `membershiptotals`(`DistrictName`, `Unit`, `UnitID`, `Total_Youth`, `Expire_Date`) VALUES
          ('$District', '$Unit', '$UnitID', '$YouthTotal', DATE('$Year-12-31'))";
        }
      } else {
        // Update
        if ($Unit != null) {
          $sql = "UPDATE `membershiptotals` SET `DistrictName`='$District', `Unit`='$Unit',
          `UnitID`='$UnitID', `Total_Youth`='$YouthTotal', `Expire_Date`=DATE('$Year-12-31') 
          WHERE `UnitID`='$UnitID'";
        }
      }
      if (!$result = self::doQuery($sql)) {
        //Report Error
      }
    }
  }
}










/**
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 */
class CShip extends CAdvancement
{
  /**
   * Finally, any singleton should define some business logic, which can be
   * executed on its instance.
   */
  public function someBusinessLogic()
  {
    // ...
  }
}

















/**
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 */
class CPost extends CAdvancement
{
  private static $MemberTotal = array();
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetMemberTotals()
  {
    $sqlPackSum = sprintf("SELECT SUM(Male_Youth), SUM(Female_Youth), SUM(Total_Youth), SUM(Male_Adults), SUM(Female_Adults), SUM(Total_Adults), SUM(Youth_Last_Year), SUM(Adults_Last_Year)
		FROM membershiptotals WHERE Expire_Date LIKE '%s%%' AND Unit LIKE 'Post%%'", self::GetYear());

    $resultPackSum = mysqli_query(parent::getDbConn(), $sqlPackSum, MYSQLI_STORE_RESULT);
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
		FROM adv_post WHERE Date LIKE '%s%%'", self::GetYear());

    $resultPackSum = mysqli_query(parent::getDbConn(), $sqlPackSum, MYSQLI_STORE_RESULT);
    if ($resultPackSum) {
      $MemberTotal = $resultPackSum->fetch_assoc();
      self::$MemberTotal['Male_Youth']       = $MemberTotal['SUM(Male_Youth)'];
      self::$MemberTotal['Female_Youth']     = $MemberTotal['SUM(Female_Youth)'];
      self::$MemberTotal['Total_Youth']      = $MemberTotal['SUM(Youth)'];
    }
    return self::$MemberTotal;
  }
}
