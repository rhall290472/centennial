<?php

// Load configuration
if (file_exists(BASE_PATH . '/config/config.php')) {
  require_once BASE_PATH . '/config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}

load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');
load_class(SHARED_PATH .'src/Classes/CUnit.php');
//include('../cAdultLeaders.php');
//require_once '../CUnit.php';

$cAdultLeaders = AdultLeaders::getInstance();


/******************************************************************************
 * 
 * 
 * 
 *****************************************************************************/
function getConfigData()
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
    $userdata['dbuser'] = "root";
    $userdata['dbpass'] = "";
    $userdata['db']     = "districtawards";
  } else if ((isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
    $userdata['dbhost'] = "rhall29047217205.ipagemysql.com";
    $userdata['dbuser'] = "awardsadmin";
    $userdata['dbpass'] = "w3frRWX^&q";
    $userdata['db']     = "districtawards";
  } else {
    $userdata['dbhost'] = "rhall29047217205.ipagemysql.com";
    $userdata['dbuser'] = "centennial";
    $userdata['dbpass'] = "w3frRWX^&q";
    $userdata['db']     = "districtawards";
  }

  return $userdata;
}

/*****************************************************************************
 *
 * Check for null or empty string
 *
 *****************************************************************************/
function IsNullOrEmptyString($str)
{
  return ($str === null || trim($str) === '');
}


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
class CDistrictAwards
{
  /**
   * The Singleton's instance is stored in a static field. This field is an
   * array, because we'll allow our Singleton to have subclasses. Each item in
   * this array will be an instance of a specific Singleton's subclass. You'll
   * see how this works in a moment.
   */
  private static $instances = [];
  private $dbConn = null;
  private static $year;
  private static $nominee;

  public static $DistrictAwardofMerit = 1;
  public static $OutStandingLeaders = 14;

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
    //$connConf = getConfigData();
// **FIX: Only create if not already connected**
    if ($db->dbConn === null) {
        $db->dbConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($db->dbConn->connect_error) {
            throw new Exception("Connection failed: " . $db->dbConn->connect_error);
        }
        $db->dbConn->set_charset('utf8');
        error_log("New DB connection created (Thread ID: " . $db->dbConn->thread_id . ")");  // Optional: Log for debugging
    } else {
        error_log("Reusing existing DB connection (Thread ID: " . $db->dbConn->thread_id . ")");  // Optional: Confirm reuse
    }    return $db;
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
      $strError = "Error: doQuery(" . $sql . "), unable to execute query. " . $ex->getMessage();
      error_log($strError, 0);
      $Result = null;
    }
    if (!$Result)
      error_log("Error: doQuery(" . $sql . ") " . __FILE__ . ", " . __LINE__);
    return $Result;
  }
  /**************************************************************************
   **
   **
   **
   *************************************************************************/
  public static function GetYear()
  {
    if (!isset($_SESSION['year']))
      $_SESSION['year'] = Date("Y");
    return $_SESSION['year'];
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function setYear($yr)
  {
    $_SESSION['year'] = $yr;
  }
  /*=============================================================================
     *
     * This function will allow the user to select the year of data to view.
     * 
     *===========================================================================*/
  public static function SelectYear()
  {
    // Give a list of Years available in the database.
    $qryYears = "SELECT DISTINCTROW Year FROM district_awards ORDER BY Year DESC";
    if (!$ResultYears = self::doQuery($qryYears)) {
      $msg = "Error: doQuery(".$qryYears.") ".__FILE__.", ".__LINE__;
      error_log($msg);
      exit();
    }

    // Fill up the drop down with merit badge names
    echo "<form method=post>";
    echo "<label for='Year'>&nbsp;</label>";
    echo "<select class='selectWrapper d-print-none' id= 'Year' name='Year' >";

    $yr = self::GetYear();

    while ($row = $ResultYears->fetch_assoc()) {
      if (!strcmp($yr, $row['Year'])) $Selected = "selected";
      else $Selected = "";
      echo "<option value='$row[Year]' " . $Selected . ">" . $row['Year'] . "</option>";
    }
    ?>
    </select>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <input  class='btn btn-primary btn-sm d-print-none' type='submit' name='SubmitYear' placeholder='Year' value='SubmitYear'/>
    </form>
    <?php
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function SetNominee($Nominee)
  {
    $_SESSION['nominee'] = $Nominee;
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function GetNominee()
  {
    if (!isset($_SESSION['nominee']))
      $_SESSION['nominee'] = "";
    return $_SESSION['nominee'];
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function SetAward($award)
  {
    $_SESSION['award'] = $award;
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function GetAward()
  {
    if (!isset($_SESSION['award']))
      $_SESSION['award'] = "";
    return $_SESSION['award'];
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function SetUnit($unit)
  {
    $_SESSION['unit'] = $unit;
  }
  /******************************************************************************
   * 
   * 
   * 
   *****************************************************************************/
  public static function GetUnit()
  {
    if (!isset($_SESSION['unit']))
      $_SESSION['unit'] = "";
    return $_SESSION['unit'];
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GotoURL($url)
  {
    echo "<script>location.replace('$url')</script>";
  }
  /******************************************************************************
   *
   * Remove all characater expect numbers for the phone number. 
   *
   *****************************************************************************/
  public static function CleanPhoneNumber($PhoneNumber)
  {
    $CleanNumber = str_replace(array('(', ')', '-', ' '), '', $PhoneNumber);

    return $CleanNumber;
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
    } else {
      $phoneNumber = "";
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
     * This function will get the data from the user form and compare against
     * stored data. If changed, update the coachs database and then provide
     * a audit log of the changes.
     * 
     *===========================================================================*/
  public static function &GetFormData($data)
  {
    if (!isset($_POST[$data])) {
      $strMsg = "ERROR: GetFormData(" . $data . ") is NOT set " . __FILE__ . ", " . __LINE__;
      error_log($strMsg);
      $return = "";
    } else {
      $value = $_POST[$data];
      if (!isset($value)) {
        $return = "";
      } else {
        $return = addslashes($_POST[$data]);
      }
    }

    return $return;
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
    <div class=WarningMessageContainer style="width:1200px">
      <p>This information is to be used only for authorized purposes on behalf of the Scouting America, Greater Colorado
        Council, Centennial District.
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
   * This function will search database for a Nominee and return MemberID
   * TODO: Search the adult_leaders database YPT table to find the Member ID.
   * 
   *****************************************************************************/
  public static function FindNomineeID($Data)
  {
    $MemberID = null;

    // OK, no MemberID entered, so lets go see if we can find the Nominee in the YPT database.
    $MemberID = AdultLeaders::FindMemberID($Data['FirstName'], $Data['LastName']);
    // Could not find in the YPT database, so search awards database and if no make one up.
    if ($MemberID == null) {
      $sqlFind = "SELECT * FROM `district_awards` WHERE `FirstName`='$Data[FirstName]' AND `LastName`='$Data[LastName]'";
      $Results = self::doQuery($sqlFind);

      $NumFound = mysqli_num_rows($Results);

      if ($NumFound == 0) {
        //OK, first time in database go find largest memberID and add 1 to set.
        $sqlFind = "SELECT MAX(MemberID) FROM `district_awards`";
        $Results = self::doQuery($sqlFind);
        $row = $Results->fetch_assoc();
        $MemberID = $row['MAX(MemberID)'];
        $MemberID++;
      } else {
        $row = $Results->fetch_assoc();
        $MemberID = $row['MemberID'];
      }
    }

    return $MemberID;
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
    $Number = strtok('');
    $Gender = strtok(' ');
    //
    //$Number = sprintf("%04d",$Number );
    //
    //$NewNumber = null;
    switch ($Unit) {
      case "Post":
      case "Crew":
        $NewNumber =  $Unit . " " . $Number . "-NA";
        break;
      case "Troop":
        if (!strcmp($Gender, "(G)") or !strcmp($UnitGender, "G"))
          $NewNumber = $Unit . " " . $Number . "-GT";
        else
          $NewNumber = $Unit . " " . $Number . "-BT";
        break;
      case "Pack":
        if (!strcmp($Gender, "(F)") or !strcmp($UnitGender, "F"))
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
  /*=============================================================================
     *
     * A PHP function that reduces multiple consecutive spaces in a string to one 
     * single space, so something like "   " is reduced to " ".
     * 
     *===========================================================================*/
  public static function reduceMultipleSpacesToSingleSpace($text)
  {
    return preg_replace('/\s+/', " ", $text);
  }
  /*=============================================================================
     *
     * If available get Preferred Name
     * 
     *===========================================================================*/
  public static function GetPreferredName($Coach)
  {
    $Name = null;
    if (strlen($Coach['PreferredName']) > 0) {
      $Name = $Coach['PreferredName'];
    } else {
      $Name = $Coach['First_Name'];
    }
    return $Name;
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayGender($Gender)
  {

    $strSelected = !strcmp("Male", $Gender) ? "selected" : "";
    echo sprintf("<option %s value='Male'>Male</option>", $strSelected);
    $strSelected = !strcmp("Female", $Gender) ? "selected" : "";
    echo sprintf("<option %s value='Female'>Female</option>", $strSelected);
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayUnitType($UnitType)
  {

    $strSelected = !strcmp("Crew", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Crew'>Crew</option>", $strSelected);
    $strSelected = !strcmp("Ship", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Ship'>Ship</option>", $strSelected);
    $strSelected = !strcmp("Troop", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Troop'>Troop</option>", $strSelected);
  }
  /******************************************************************************
   *
   * Fill a option dialog box with awards from database.
   *
   *****************************************************************************/
  public static function DisplayAwardsList($Award)
  {
    // Go get the list of available awards.
    $queryAwards = "SELECT * FROM `awards` ORDER BY `Award`";
    if (!$AwardList = self::doQuery($queryAwards)) {
      $msg = "Error: doQuery(".$queryAwards.") ".__FILE__.", ".__LINE__;
      error_log($msg);
      exit();
    }
    while ($rowAward = $AwardList->fetch_assoc()) {
      $strSelected = $Award == $rowAward['AwardIDX'] ? "selected" : "";
      echo sprintf("<option %s value='%s'>%s</option>", $strSelected, $rowAward['AwardIDX'], $rowAward['Award']);
    }
    return;
  }
  /******************************************************************************
   *
   * Fill a option dialog box with award status from database.
   *
   *****************************************************************************/
  public static function DisplayAwardsStatus($NomineeStatus)
  {
    // Go get the list of available awards.
    $queryStatus = "SELECT * FROM `status`";
    if (!$Status = self::doQuery($queryStatus)) {
      $msg = "Error: doQuery(".$queryStatus.") ".__FILE__.", ".__LINE__;
      error_log($msg);
      exit();
}
    while ($rowStatus = $Status->fetch_assoc()) {
      $strSelected = $NomineeStatus == $rowStatus['StatusIDX'] ? "selected" : "";
      echo sprintf("<option %s value='%s'>%s</option>", $strSelected, $rowStatus['StatusIDX'], $rowStatus['Status']);
    }
    return;
  }
  /*****************************************************************************
   *
   * This function will add or update a Nominess record.
   *
   *****************************************************************************/
public static function UpdateNomineeRecord($NomineeData)
{
    $bAdd = false;
    $Result = true;
    // First fix up MemberID field.
    if (empty($NomineeData['MemberID'])) {
        $MemberID = self::FindNomineeID($NomineeData);
    } else
        $MemberID = $NomineeData['MemberID'];

    // Check to see if this is a new Nominee, 
    if ($NomineeData['NomineeIDX'] == -1) {
        $bAdd = true;
        // It's a new Nominee so INSERT
        $sqlStmt = "INSERT INTO `district_awards`(`FirstName`, `PName`, `MName`, `LastName`, 
              `Year`, `Award`, `Status`, `MemberID`,  
              `Position`, `Unit`, `Notes`, `IsDeleted`, `created_by`,
              `NominatedBy`, `NominatedByUnit`, `NominatedByPosition`
              ) 
              VALUES 
              ('$NomineeData[FirstName]','$NomineeData[PName]', '$NomineeData[MName]','$NomineeData[LastName]',
              '$NomineeData[Year]', '$NomineeData[Award]', '$NomineeData[Status]', '$MemberID', 
              '$NomineeData[Position]','$NomineeData[Unit]','$NomineeData[Notes]','$NomineeData[IsDeleted]', '$NomineeData[created_by]',
              '$NomineeData[NominatedBy]','$NomineeData[NominatedByUnit]','$NomineeData[NominatedByPosition]')";

        // Execute the sql Statement (for main insert)
        $Result &= self::doQuery($sqlStmt);

        // **FIX: Capture the new ID IMMEDIATELY after main INSERT (only for adds)**
        $New_id = self::doQuery("SELECT LAST_INSERT_ID() AS new_id;");
        $IDX = $New_id->fetch_assoc();
        $newNomineeIdx = $IDX['new_id'];  // e.g., 123
        $NomineeData['NomineeIDX'] = $newNomineeIdx;  // Update for downstream use (e.g., awardofmerit)

        // If this is an Outstanding Leaders Award, we have to add the other three names.
        if ($NomineeData['Award'] == self::$OutStandingLeaders) {
            // Gather other three names 
            if (isset($_POST['element_2_1'])) {
                $MemberID = AdultLeaders::FindMemberID($NomineeData['FirstName2'], $NomineeData['LastName2']);
                $sqlStmt2 = "INSERT INTO `district_awards`(`FirstName`, `PName`, `MName`, `LastName`, 
                          `Year`, `Award`, `Status`, `MemberID`,  
                          `Unit`, `Notes`, `IsDeleted`, `created_by`,
                          `NominatedBy`, `NominatedByUnit`, `NominatedByPosition`
                          ) 
                          VALUES 
                          ('$NomineeData[FirstName2]','$NomineeData[PName2]', '$NomineeData[MName2]','$NomineeData[LastName2]',
                          '$NomineeData[Year]', '$NomineeData[Award]', '$NomineeData[Status]', '$MemberID', 
                          '$NomineeData[Unit]','$NomineeData[Notes]','$NomineeData[IsDeleted]', '$NomineeData[created_by]',
                          '$NomineeData[NominatedBy]','$NomineeData[NominatedByUnit]','$NomineeData[NominatedByPosition]')";

                $Result &= self::doQuery($sqlStmt2);
                // Optional: Capture additional IDs if needed, e.g., $additionalId = self::doQuery("SELECT LAST_INSERT_ID() AS new_id;")->fetch_assoc()['new_id'];
            }
            if (isset($_POST['element_3_1'])) {
                $MemberID = AdultLeaders::FindMemberID($NomineeData['FirstName3'], $NomineeData['LastName3']);
                $sqlStmt3 = "INSERT INTO `district_awards`(`FirstName`, `PName`, `MName`, `LastName`, 
                          `Year`, `Award`, `Status`, `MemberID`,  
                          `Unit`, `Notes`, `IsDeleted`, `created_by`,
                          `NominatedBy`, `NominatedByUnit`, `NominatedByPosition`
                          ) 
                          VALUES 
                          ('$NomineeData[FirstName3]','$NomineeData[PName3]', '$NomineeData[MName3]','$NomineeData[LastName3]',
                          '$NomineeData[Year]', '$NomineeData[Award]', '$NomineeData[Status]', '$MemberID', 
                          '$NomineeData[Unit]','$NomineeData[Notes]','$NomineeData[IsDeleted]', '$NomineeData[created_by]',
                          '$NomineeData[NominatedBy]','$NomineeData[NominatedByUnit]','$NomineeData[NominatedByPosition]')";

                $Result &= self::doQuery($sqlStmt3);
            }
            if (isset($_POST['element_4_1']) && !empty($_POST['element_4_1'])) {
                $MemberID = AdultLeaders::FindMemberID($NomineeData['FirstName4'], $NomineeData['LastName4']);
                $sqlStmt4 = "INSERT INTO `district_awards`(`FirstName`, `PName`, `MName`, `LastName`, 
                          `Year`, `Award`, `Status`, `MemberID`,  
                          `Unit`, `Notes`, `IsDeleted`, `created_by`,
                          `NominatedBy`, `NominatedByUnit`, `NominatedByPosition`
                          ) 
                          VALUES 
                          ('$NomineeData[FirstName4]','$NomineeData[PName4]', '$NomineeData[MName4]','$NomineeData[LastName4]',
                          '$NomineeData[Year]', '$NomineeData[Award]', '$NomineeData[Status]', '$MemberID', 
                          '$NomineeData[Unit]','$NomineeData[Notes]','$NomineeData[IsDeleted]', '$NomineeData[created_by]',
                          '$NomineeData[NominatedBy]','$NomineeData[NominatedByUnit]','$NomineeData[NominatedByPosition]')";

                $Result &= self::doQuery($sqlStmt4);
            }
        }
    } else {
        $sqlStmt = "UPDATE `district_awards` SET `FirstName`='$NomineeData[FirstName]',`PName`='$NomineeData[PName]', `MName`='$NomineeData[MName]',`LastName`='$NomineeData[LastName]',
              `Year`='$NomineeData[Year]',`Award`='$NomineeData[Award]',`Status`='$NomineeData[Status]',`MemberID`='$MemberID',
              `Position`='$NomineeData[Position]',`Unit`='$NomineeData[Unit]',`Notes`='$NomineeData[Notes]',`IsDeleted`='$NomineeData[IsDeleted]',`updated_by`='$_SESSION[username]',
              `NominatedBy`='$NomineeData[NominatedBy]',`NominatedByUnit`='$NomineeData[NominatedByUnit]',`NominatedByPosition`='$NomineeData[NominatedByPosition]'
              WHERE NomineeIDX='$NomineeData[NomineeIDX]'";

      $sqlStmtNote = "UPDATE `district_awards` SET `Notes`='$NomineeData[Notes]' WHERE NomineeIDX='$NomineeData[NomineeIDX]'";

      //Execute the sql Statement (for update, if applicable)
      $Result &= self::doQuery($sqlStmt);
      $Result &= self::doQuery($sqlStmtNote);

    }


    // **REMOVED: The unconditional LAST_INSERT_ID() query—now handled only in insert branch above**

    if ($Result && $NomineeData['Award'] == self::$DistrictAwardofMerit) {

        if ($bAdd) {
            // **FIX: Use $newNomineeIdx (the actual new NomineeIDX) instead of $MemberID**
            $sqlStmt = "INSERT INTO `awardofmerit` (`NomineeIDX`";
            $values = "'$newNomineeIdx'";  // Or better: use prepared stmt with ?

            $fields = [
                'DLAward',
                'SRAward',
                'STAward',
                'CoachAward',
                'SilverBeaver',
                'ScouterKey',
                'CSAward',
                'WoodBadge',
                'DCSA',
                'WDLAward',
                'Other1',
                'Other2'
            ];

            foreach ($fields as $field) {
                if (isset($NomineeData[$field])) {
                    $sqlStmt .= ", `$field`";
                    $values .= ", '" . addslashes($NomineeData[$field]) . "'";
                }
            }

            $sqlStmt .= ") VALUES ($values)";
        } else {
            $sqlStmt = "UPDATE `awardofmerit` SET ";
            $updates = [];

            $fields = [
                'DLAward',
                'SRAward',
                'STAward',
                'CoachAward',
                'SilverBeaver',
                'ScouterKey',
                'CSAward',
                'WoodBadge',
                'DCSA',
                'WDLAward',
                'Other1',
                'Other2'
            ];

            foreach ($fields as $field) {
                if (isset($NomineeData[$field])) {
                    $updates[] = "`$field`='" . addslashes($NomineeData[$field]) . "'";
                }
            }

            $sqlStmt .= implode(", ", $updates);
            // **FIX: Use existing NomineeIDX, not fallback to $MemberID**
            $sqlStmt .= " WHERE NomineeIDX='" . addslashes($NomineeData['NomineeIDX']) . "'";
        }
        // Execute the sql Statement
        $Result = self::doQuery($sqlStmt);
    }

    if (!$Result) {
        $strMsg = "ERROR: UpdateNomineeRecord(), doQuery(" . $sqlStmt . ") failed at " . __FILE__ . ", " . __LINE__;
        error_log($strMsg);
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed to add Nominee record.'];
    }

    // **OPTIONAL: Return the new ID for caller use, e.g., if needed elsewhere**
    return $Result ? ['success' => true, 'new_nominee_idx' => $bAdd ? $newNomineeIdx : null] : ['success' => false];

    // Or if you want to keep returning just $Result, echo/log $newNomineeIdx for debugging: error_log("New NomineeIDX: " . $newNomineeIdx);
}  /*****************************************************************************
   *
   * We have to create an audit trail this way beacuse iPage does not support
   * the use of triggers in the database.
   *
   *****************************************************************************/
  public static function CreateAudit($Old, $New, $IdKey)
  {
    // Check for new record.
    if ($New[$IdKey] == -1)
      return;

    $Index = 0;
    $Indexid = 'NomineeIDX';
    if ($IdKey == 'NomineeIDX') {
      $PrimaryKey = 'NomineeIDX';
      $db = 'nominee_audit_trail';
    }
    foreach ($New as $key => $value) {
      // Don't audit the coachesid value
      if ($Index == 0) {
        $Indexid = $key;
        $Index++;
        continue;
      } else if ($Old[$key] != $New[$key]) {
        $sqlStmt = "INSERT INTO `$db`(`$PrimaryKey`, `column_name`, `old_value`, `new_value`, `done_by`)
                     VALUES ('$New[$Indexid]','$key','$Old[$key]','$New[$key]','$_SESSION[username]')";
        //Excute the sql Statement
        $Result = self::doQuery($sqlStmt);
      }
      $Index++;
    }
    return;
  }
  /*=============================================================================
     *
     * Retrun the text name of a Award given the index to the award
     * 
     *===========================================================================*/
  public static function GetAwardName($AwardIDX)
  {
    // Should never happen...
    if ($AwardIDX == "")
      return "";

    $qryAward = "SELECT * FROM awards WHERE `AwardIDX`='$AwardIDX' ORDER BY `Award`";

    if (!$ResultAward = self::doQuery($qryAward)) {
      $msg = "Error: doQuery(".$qryAward.") ".__FILE__.", ".__LINE__;
      error_log($msg);
      exit();
    }
    $row = $ResultAward->fetch_assoc();
    if ($row)
      $AwardName = $row['Award'];
    else {
      $strError = "GetAwardName(" . $AwardIDX . "), failed to find award name in " . __FILE__ . ", " . __LINE__;
      error_log($strError);
      $AwardName = "Error";
    }

    return $AwardName;
  }
  /*=============================================================================
     *
     * Retrun the Status text of a Award given the index to the status
     * 
     *===========================================================================*/
  public static function GetAwardStatus($StatusIDX)
  {
    $qryStatus = "SELECT * FROM status WHERE `StatusIDX`='$StatusIDX'";

    if (!$ResultStatus = self::doQuery($qryStatus)) {
      $msg = "Error in GetAwardStatus(" . $StatusIDX . "), ";
      $msg .= "doQuery()";
      error_log($msg);
      self::function_alert($msg);
    }
    $row = $ResultStatus->fetch_assoc();
    $Status = $row['Status'];

    return $Status;
  }
  /*=============================================================================
     *
     * This function will allow the user to select the Nominee of data to view.
     * 
     *===========================================================================*/
  public static function SelectNominee()
  {
    // Give a list of Years available in the database.
    $qryNominee = "SELECT DISTINCTROW FirstName, LastName, MemberID FROM district_awards ORDER BY LastName";
    if (!$ResultNominee = self::doQuery($qryNominee)) {
      $msg = "Error: doQuery(".$qryNominee.") ".__FILE__.", ".__LINE__;
      error_log($msg);
      exit();
    }

    $NomineeSelected = self::GetNominee();
  ?>
    <!-- // Fill up the drop down with merit badge names -->
    <div class="form-row">
      <div class="col-2">

        <form method=post>
          <label for='Nominee'>&nbsp;</label>
          <select class='form-control' id='Nominee' name='Nominee'>
            <?php
            while ($row = $ResultNominee->fetch_assoc()) {
              if ($NomineeSelected == $row['MemberID'])
                $Selected = "selected";
              else
                $Selected = "";
              echo "<option value='$row[MemberID]'" . $Selected . ">" . $row['LastName'] . " " . $row['FirstName'] . "</option>";
            }
            ?>
          </select>
      </div>
      <div class="col-2 py-4">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input class='btn btn-primary btn-sm' type='submit' name='SubmitNominee' placeholder='Year' value='Nominee' />
      </div>
      </form>
    </div>
  <?php
  }
  /*=============================================================================
     *
     * This function will allow the user to select the Award of data to view.
     * 
     *===========================================================================*/
  public static function SelectAward()
  {
    // Give a list of Years available in the database.
    $qryAward = "SELECT * FROM awards ORDER BY Award";
    if (!$ResultAward = self::doQuery($qryAward)) {
      $msg = "Error: doQuery(".$qryAward.") ".__FILE__.", ".__LINE__;
      error_log($msg);
      exit();
    }

    $AwardSelected = self::GetAward();
    // Fill up the drop down with merit badge names
  ?>
    <div class="form-row">
      <div class="col-2">

        <form method=post>
          <label for='Award'>&nbsp;</label>
          <select class='form-control' id='Award' name='Award'>
            <?php
            while ($row = $ResultAward->fetch_assoc()) {
              if ($row['AwardIDX'] == $AwardSelected)
                $Selected = "selected";
              else
                $Selected = "";
              echo "<option value='$row[AwardIDX]'" . $Selected . ">" . $row['Award'] . "</option>";
            }
            ?>
          </select>
      </div>
      <div class="col-2 py-4">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <input class='btn btn-primary btn-sm' type='submit' name='SubmitAward' placeholder='Year' value='Award' />
      </div>
      </form>
    </div>
  <?php
  }
  /*=============================================================================
        *
        * This function will allow the user to select the Unit of data to view.
        *
        *===========================================================================*/
  public static function SelectUnit()
  {
    // Give a list of Years available in the database.
    $qryUnit = "SELECT DISTINCTROW Unit FROM district_awards WHERE NomineeIDX > 0 ORDER BY Unit";
    if (!$ResultUnit = self::doQuery($qryUnit)) {
      $msg = "Error: doQuery(".$qryUnit.") ".__FILE__.", ".__LINE__;
      error_log($msg);
      exit();
    }

    $UnitedSelected = self::GetUnit();

    // Fill up the drop down with merit badge names
  ?>
    <div class="form-row">
      <div class="col-2">

        <form method=post>
          <label for='Unit'>&nbsp;</label>
          <select class='form-control' id='Unit' name='Unit'>
            <?php
            while ($row = $ResultUnit->fetch_assoc()) {
              if ($row['Unit'] == $UnitedSelected)
                $Selected = "selected";
              else
                $Selected = "";
              echo "<option value='$row[Unit]'" . $Selected . ">" . $row['Unit'] . "</option>";
            }
            ?>
          </select>
          </div>
          <div class="col-2 py-4">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input class='btn btn-primary btn-sm' type='submit' name='SubmitUnit' value='Unit' />
          </div>

        </form>
      </div>
    <?php
  }
  /*=============================================================================
        *
        * This function will return list of Nominees
        *
        *===========================================================================*/
public static function GetDistrictNominees($Index, $forPDF = false)
{
    ?>
    <table>
        <tr>
            <th style="width: 40px;">For</th>
            <th style="width: 40px;">Against</th>
            <th style="width: 100px;">First Name</th>
            <th style="width: 100px;">Last Name</th>
            <th style="width: 100px;">Unit</th>
        </tr>
    <?php
    $year = self::GetYear();

    $queryNominees = "SELECT * FROM `district_awards` WHERE Award='$Index' AND Status='2' AND Year='$year' AND (`IsDeleted` IS NULL || `IsDeleted` <>'1') ORDER BY `LastName`";

    if (!$ResultNominees = self::doQuery($queryNominees)) {
        $msg = "Error: doQuery(" . $queryNominees . ") " . __FILE__ . ", " . __LINE__;
        error_log($msg);
        exit();
    }

    while ($rowNominee = $ResultNominees->fetch_assoc()) {
        if ($rowNominee['NomineeIDX'] != -1) {
            $AwardName = self::GetAwardName($rowNominee['Award']);
            $Status = self::GetAwardStatus($rowNominee['Status']);
            ?>
            <tr>
                <td style='width:50px; text-align: center;'>
                    <?php echo $forPDF ? '[ ]' : '<input type=\'checkbox\' name=\'name1\' />'; ?>
                </td>
                <td style='width:50px; text-align: center;'>
                    <?php echo $forPDF ? '[ ]' : '<input type=\'checkbox\' name=\'name1\' />'; ?>
                </td>
                <td style='width:100px'><?php echo $rowNominee["FirstName"]; ?></td>
                <td style='width:100px'>
                    <?php 
                    if ($forPDF) {
                        echo $rowNominee['LastName'];
                    } else {
                        echo "<a href=index.php?page=edit-nominee&NomineeIDX=" . $rowNominee['NomineeIDX'] . ">" . $rowNominee['LastName'] . "</a>";
                    }
                    ?>
                </td>
                <td style='width:150px'><?php echo $rowNominee['Unit']; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </table>
    <?php
}
  /*=============================================================================
     *
     * This function will return list of Units in the District
     * 
     *===========================================================================*/
  public static function GetDistrictUnits($element_name, $default_unit)
  {
    // Make Unit selection a dropdown of active units in the District.
    $resultunit = UNIT::GetUnits();

    echo "<select class='form-control' name='$element_name' >";
    echo "<option value='-1' >" . 'Unit Type & Number' . "</option>";
    while ($row = $resultunit->fetch_assoc()) {
      if (!strcmp($default_unit, $row['Unit'])) $Selected = "selected";
      else $Selected = "";
      echo "<option value='$row[Unit]'" . $Selected . ">" . $row['Unit'] . "</option>";
    }
    echo "<option value='Other'>Other</option>";
    echo '</select>';
  }

  /*=============================================================================
     *
     * This function will return list of Positions in Scouting
     * 
     *===========================================================================*/
  public static function GetScoutingPosition($element_name, $default_position)
  {
    $resultposition = AdultLeaders::GetPositions();

    echo "<select class='form-control' name='$element_name' placeholder='Scouting Position' >";
    echo "<option value='-1' >" . 'Scouting Position' . "</option>";
    while ($row = $resultposition->fetch_assoc()) {
      if (!strcmp($default_position, $row['Position'])) $Selected = "selected";
      else $Selected = "";
      echo "<option value='$row[Position]'" . $Selected . ">" . $row['Position'] . "</option>";
    }
    echo "<option value='Other'>Other</option>";
    echo '</select>';
  }
}
