<?php

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
class CEagle
{
  /**
   * The Singleton's instance is stored in a static field. This field is an
   * array, because we'll allow our Singleton to have subclasses. Each item in
   * this array will be an instance of a specific Singleton's subclass. You'll
   * see how this works in a moment.
   */
  private static $instances = [];

  /**
   * Database connection instance.
   *
   * @var mysqli|null
   */
  private $dbConn = null;

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
    if (!self::checkDatabaseConnection($db->dbConn)) {
      $db->dbConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      $db->dbConn->set_charset('utf8');
    }
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
  private static function checkDatabaseConnection($mysqli)
  {
    // Check if connection object exists and no connection error
    if ($mysqli instanceof mysqli && !$mysqli->connect_error) {
      try {
        // Test the connection with a lightweight query
        $mysqli->query("SELECT 1");
        return true; // Connection is active
      } catch (Exception $e) {
        return false; // Query failed, connection is not active
      }
    }
    return false; // No connection object or initial connection error
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
        error_log("Error: doQuery(" . $sql . ") - " . $strError, 0);
      }
    } catch (Exception $ex) {
      $strError = "I was unable to execute query. " . $ex->getMessage();
      error_log($strError, 0);
      $Result = null;
    }
    if (!$Result)
      error_log("SQL Statement failed: " . $$sql);
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
  public static function SelectYear($csrf_token)
  {
    // This will call the function once the user and selected a troop and click submit

    // Fill up the drop down with merit badge names
?>
    <form method=post>
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
      <label for='Year'>&nbsp;</label>
      <div class="form-row px-5">
        <div class="col-2">
          <select class='form-control' id='Year' name='Year'>

            <?php
            $yr = $_SESSION['year'];
            // First recod is blank "all"
            //echo "<option value=\"\" </option>";
            //$Selected = "";
            if (!strcmp($yr, "2028")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2028' " . $Selected . ">2028</option>";

            if (!strcmp($yr, "2027")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2027' " . $Selected . ">2027</option>";

            if (!strcmp($yr, "2026")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2026' " . $Selected . ">2026</option>";

            if (!strcmp($yr, "2025")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2025' " . $Selected . ">2025</option>";

            if (!strcmp($yr, "2024")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2024' " . $Selected . ">2024</option>";

            if (!strcmp($yr, "2023")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2023' " . $Selected . ">2023</option>";

            if (!strcmp($yr, "2022")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2022' " . $Selected . ">2022</option>";

            if (!strcmp($yr, "2021")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2021' " . $Selected . ">2021</option>";

            if (!strcmp($yr, "2020")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2020' " . $Selected . " >2020</option>";

            if (!strcmp($yr, "2019")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2019' " . $Selected . " >2019</option>";

            if (!strcmp($yr, "2018")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2018' " . $Selected . " >2018</option>";

            if (!strcmp($yr, "2017")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2017' " . $Selected . " >2017</option>";

            if (!strcmp($yr, "2016")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2016' " . $Selected . " >2016</option>";

            if (!strcmp($yr, "2015")) $Selected = "selected";
            else $Selected = "";
            echo "<option value='2015' " . $Selected . " >2015</option>";
            ?>

          </select>
        </div>
        <div class="col-2">
          <input class='btn btn-primary btn-sm' type='submit' name='SubmitYear' placeholder='Year' value='SubmitYear' />
        </div>
      </div>
      </div>
    </form>
  <?php
  }
  /*=============================================================================
     *
     * This function will allow the user to select the units to view by.
     * 
     *===========================================================================*/
  public static function SelectUnit($qry, $csrf_token)
  {
    $sqlUnits = $qry;

    $ResultUnits = self::doQuery($sqlUnits);
    if (!$ResultUnits) {
      self::function_alert("ERROR: SelectUnit()");
      exit();
    }
    // Fill up the drop down with unit names
  ?>
    <form method=post>
      <div class="form-row">
        <div class="col-2">

          <label for='Unit'>&nbsp;</label>
          <select class='form-control' id='Unit' name='Unit'>
            <option value=""></option>
            <?php
            while ($rowUnit = $ResultUnits->fetch_assoc()) {
              echo "<option value=" . $rowUnit['UnitType'] . "-" . $rowUnit['UnitNumber'] . ">" . $rowUnit['UnitType'] . " " . $rowUnit['UnitNumber'] . "</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-1 py-45">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <input class='btn btn-primary btn-sm' type='submit' name='SubmitUnit' placeholder='Unit' value='SubmitUnit' />
        </div>
      </div>
      </div>
    </form>
  <?php
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

    $str = "<a href='mailto:" . strtolower($Email) . "?subject=Eagle Rank'>" . strtolower($Email) . "</a>";

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
    if (isset($_POST[$data])) {
      //$value = $_POST[$data];
      $return = addslashes($_POST[$data]);
    } else {
      // This is needed for check boxes, if box is not checked will not
      // Post a value.
      $return = "0";
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
      <p>This information is to be used only for authorized purposes on behalf of the Boy Scouts of America, Denver Area
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
   * This function will search database for a scout.
   * 
   *****************************************************************************/
  public static function IsScoutinDB($Data)
  {
    $IdxFirst = 0;
    $IdxLast = 1;

    $Name = explode(" ", $Data['0']);
    /* Check if middle name is provided */
    if (count($Name) > 2 && !IsNullOrEmptyString($Name[2])) {
      $IdxLast = 2; /* Middle Name provided */
    }
    $sqlFind = "SELECT * FROM `scouts` WHERE `FirstName`='$Name[$IdxFirst]' AND `LastName`='$Name[$IdxLast]'";
    $Results = self::doQuery($sqlFind);

    $NumFound = mysqli_num_rows($Results);
    if ($NumFound > 1) {
      // Handle the fact the we have duplicates.
      self::DisplayWarning("Found Duplciate Scouts");
      exit;
    }

    if (mysqli_num_rows($Results))
      $Found = true;
    else
      $Found = false;

    return $Found;
  }
  /******************************************************************************
   *
   * This function will search database for a scout.
   * 
   *****************************************************************************/

  public static function IsScoutIDinDB($MemberID)
  {
    $sqlFind = "SELECT * FROM `scouts` WHERE `MemberId`='$MemberID'";
    $Results = self::doQuery($sqlFind);

    return $NumFound = mysqli_num_rows($Results);
  }
  /******************************************************************************
   *
   * This function will insert a life scout into the database.
   * 
   *****************************************************************************/
  public static function InsertScout($Data, $Unit)
  {
    if ($Unit[2] == "B" || $Unit[2] == "(B)")
      $Gender = "Male";
    else
      $Gender = "Female";
    $MemberId = strlen($Data[1]) > 0 ? $Data[1] : $Data[2];

    $IdxFirst = 0;
    $IdxMiddle = 1;
    $IdxLast = 1;
    $Name = explode(" ", $Data['0']);
    /* Check for middle name field */
    $IdxLast = count($Name) - 2; // Make zero based

    $sqlInsert = "INSERT INTO `scouts`(`FirstName`, `LastName`, `MemberId`, `Eagled`, `AgedOut`, 
            `UnitType`, `UnitNumber`, `Gender`, `District`, `created_by`) 
            VALUES ('$Name[$IdxFirst]', '$Name[$IdxLast]', '$MemberId', '0', '0', 
            '$Unit[0]', '$Unit[1]', '$Gender', 'Centennial', '$_SESSION[username]')";

    $Results = self::doQuery($sqlInsert);
    return $Results;
  }
  /******************************************************************************
   *
   * This function will insert a Eagle scout into the database.
   * Input Parameters:
   *  $Data[0] = First and Lst Name
   *  $Data[1] = BSA ID
   *  $Data[2] = Position
   *  $Data[3] = Rank
   *  $Data[4] = Age
   *  $Data[5] =""
   * 
   *  $Unit[0] = Unit Type i.e Troop, Crew
   *  $Unit[1] = Unit Number
   *  $Unit[2] = Unit Gender B || (B) or G (G)
   *****************************************************************************/
  public static function InsertEagle($Data, $Unit)
  {
    if ($Unit[2] == "B" || $Unit[2] == "(B)")
      $Gender = "Male";
    else
      $Gender = "Female";
    $MemberId = strlen($Data[1]) > 0 ? $Data[1] : $Data[2];

    $IdxFirst = 0;
    $IdxMiddle = 1;
    $IdxLast = 1;
    /* Should check for middle names */
    $Name = explode(" ", $Data['0']);
    $IdxLast = count($Name) - 2; // Make zero based

    $sqlInsert = "INSERT INTO `scouts`(`FirstName`, `LastName`, `MemberId`, `Eagled`, `AgedOut`, 
            `UnitType`, `UnitNumber`, `Gender`, `District`, `BOR`, `created_by`) 
            VALUES ('$Name[$IdxFirst]', '$Name[$IdxLast]', '$MemberId', '1', '0', 
            '$Unit[0]', '$Unit[1]', '$Gender', 'Centennial', '$Data[4]', '$_SESSION[username]')";

    $Results = self::doQuery($sqlInsert);
    return $Results;
  }
  /******************************************************************************
   *
   * This function will Update a Eagle scout into the database.
   * 
   *****************************************************************************/
  public static function UpdateEagle($Data, $Unit)
  {
    if ($Unit[3] == "B")
      $Gender = "Male";
    else
      $Gender = "Female";
    $MemberId = strlen($Data[1]) > 0 ? $Data[1] : $Data[2];
    if (count($Data) == 5 || count($Data) == 6) {
      $MemberId = $Data[1];
      $BOR = $Data[3];
    } else {
      $MemberId = $Data[2];
      $BOR = $Data[4];
    }

    $IdxFirst = 0;
    $IdxMiddle = 1;
    $IdxLast = 1;
    /* Should check for middle names */
    $Name = explode(" ", $Data['0']);
    if (count($Name) == 4) {
      $IdxLast = 2;
    }

    $sqlInsert = "UPDATE `scouts` SET `Eagled`='1', `AgedOut`='0', `MemberId`='$MemberId', `BOR`='$BOR',
            `UnitType`='$Unit[1]', `UnitNumber`='$Unit[2]', `Gender`='$Gender', `District`='Centennial', 
            `updated_by`='$_SESSION[username]' WHERE
            `FirstName`='$Name[$IdxFirst]' AND `LastName`='$Name[$IdxLast]'";

    $Results = self::doQuery($sqlInsert);
    return $Results;
  }
  /******************************************************************************
   *
   * This function will Update a Eagle scout into the database.
   * 01Apr23 - Not sure why Gender is in the Where clause but cause the females
   * in the crew not to be updated.  AND `Gender`='$Gender'
   * 
   *****************************************************************************/
  public static function UpdateCC($Data)
  {
    $colProgram = 1;
    $ColNumber = 2;
    $colGender = 3;
    $colFirstName = 5;
    $colLastName = 7;
    $colEmail = 19;
    $colPhone = 20;

    $Phone = self::CleanPhoneNumber($Data[$colPhone]);
    $UnitNumber = (int)$Data[$ColNumber];
    $Gender = 'Male';
    if ($Data[$colGender] == 'G')
      $Gender = 'Female';
    $Email = strtolower($Data[$colEmail]);

    $sqlCC = "UPDATE `scouts` SET `CCFirst`='$Data[$colFirstName]',`CCLast`='$Data[$colLastName]',`CCPhone`='$Phone',`CCEmail`='$Email' 
            WHERE `UnitType`='$Data[$colProgram]' AND `UnitNumber`='$UnitNumber' AND (`Eagled` IS NULL OR `Eagled`='0') AND
            (`AgedOut` IS NULL OR `AgedOut`='0')";
    $Results = self::doQuery($sqlCC);
    return $Results;
  }
  /******************************************************************************
   *
   * This function will Update a Eagle scout into the database.
   * 01Apr23 - Not sure why Gender is in the Where clause but cause the females
   * in the crew not to be updated.  AND `Gender`='$Gender'
   * 
   *****************************************************************************/
  public static function UpdateUL($Data)
  {
    $colProgram = 1;
    $ColNumber = 2;
    $colGender = 3;
    $colFirstName = 5;
    $colLastName = 7;
    $colEmail = 19;
    $colPhone = 20;

    $Phone = self::CleanPhoneNumber($Data[$colPhone]);
    $UnitNumber = (int)$Data[$ColNumber];
    $Gender = 'Male';
    if ($Data[$colGender] == 'G')
      $Gender = 'Female';
    $Email = strtolower($Data[$colEmail]);

    $sqlCC = "UPDATE `scouts` SET `ULFirst`='$Data[$colFirstName]',`ULLast`='$Data[$colLastName]',`ULPhone`='$Phone',`ULEmail`='$Email' 
            WHERE `UnitType`='$Data[$colProgram]' AND `UnitNumber`='$UnitNumber' AND (`Eagled` IS NULL OR `Eagled`='0') AND
            (`AgedOut` IS NULL OR `AgedOut`='0')";
    $Results = self::doQuery($sqlCC);
    return $Results;
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
    if (strlen($Coach['PreferredName'] ?? '') > 0) {
      $Name = $Coach['PreferredName'];
    } else {
      $Name = $Coach['First_Name'];
    }
    return $Name;
  }
  /*=============================================================================
     *
     * If available get Preferred Name
     * 
     *===========================================================================*/
  public static function GetScoutPreferredName($rowScout)
  {
    $Name = null;
    if (strlen($rowScout['PreferredName'] ?? '') > 0) {
      $Name = $rowScout['PreferredName'];
    } else {
      $Name = $rowScout['FirstName'];
    }
    return $Name;
  }  /*=============================================================================
     *
     * Read in Eagle Scouts from .csv file
     * 
     *===========================================================================*/
  public static function ImportEagles($fileName)
  {
    /* Defined the file columns, which change */
    $Format2017 = false;
    $Format2018 = false;
    $Format2019 = false;
    $Format2020 = false;
    $Format2021 = false;
    $Format2022 = true;

    if ($Format2017) {
      $colUnitType = 0;
      $colUnitNumber = 1;
      $colDistrict = 2;
      $colFirstName = 3;
      $colMiddleName = 4;
      $colLastName = 5;
      $colEmail = 6;
      $colULFirst = 7;
      $colULLast = 8;
      $colULEmail = 9;
      $colEagledDate = 10;
      $colBeneficiary = 11;
    } else if ($Format2018) {
      $colUnitType = 0;
      $colUnitNumber = 1;
      $colDistrict = 2;
      $colFirstName = 3;
      $colMiddleName = 4;
      $colLastName = 5;
      $colStreet_Address = 7;
      $colCity = 8;
      $colState = 9;
      $colZip = 10;
      $colEmail = 11;
      $colULFirst = 12;
      $colULLast = 13;
      $colULEmail = 14;
      $colHours = 19;
      $colBeneficiary = 20;
    } else if ($Format2019) {
      $colUnitType = 1;
      $colUnitNumber = 2;
      $colDistrict = 3;
      $colFirstName = 4;
      $colMiddleName = 5;
      $colLastName = 6;
      $colBOR = 7;
      $colHours = 8;
      $colBeneficiary = 9;
    } else if ($Format2020) {
      $colUnitType = 1;
      $colUnitNumber = 2;
      $colDistrict = 3;
      $colFirstName = 4;
      $colMiddleName = 5;
      $colLastName = 6;
      $colStreet_Address = 8;
      $colCity = 9;
      $colState = 10;
      $colZip = 11;
      $colPhone = 12;
      $colEmail = 13;
      $colULFirst = 14;
      $colULLast = 15;
      $colULEmail = 16;
      $colHours = 30;
      $colEagledDate = 31;
      $colBeneficiary = 32;
    }
    if ($Format2021) {
      $colUnitType = 0;
      $colUnitNumber = 1;
      $colDistrict = 2;
      $colFirstName = 3;
      $colMiddleName = 4;
      $colLastName = 5;
      $colHours = 7;
      $colBeneficiary = 8;
    }
    if ($Format2022) {
      $colUnitType = 1;
      $colUnitNumber = 2;
      $colDistrict = 3;
      $colFirstName = 4;
      $colMiddleName = 5;
      $colLastName = 6;
      $colStreet_Address = 8;
      $colCity = 9;
      $colState = 10;
      $colZip = 11;
      $colPhone = 12;
      $colEmail = 13;
      $colULFirst = 14;
      $colULLast = 15;
      $colULEmail = 16;
      $colDOB = 21;
      $colEagledDate = 25;
      $colHours = 30;
      $colBeneficiary = 31;
    }



    $RecordsInError = 0;
    $RecordsInserted = 0;
    $RecordsUpdated = 0;
    $SkippedRecords = 0;
    $UnitType = "";
    $EagleEarned = "1";
    $username = $_SESSION['username'];
    $filePath = "Data/" . $fileName;

    $row = 0;
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row < 1) {
          $row++;
          continue;
        }
        $District = $data[$colDistrict];
        $pos = strcmp($District, "Centennial");
        if ($pos != 0) {
          $SkippedRecords++;
        } else {

          if ($Format2017) {
            $UnitType     = $data[$colUnitType];
            $UnitNumber   = $data[$colUnitNumber];
            // District
            $FirstNameScout  = rtrim(addslashes(ucfirst(strtolower($data[$colFirstName]))));
            $MiddleNameScout = addslashes(ucfirst(strtolower($data[$colMiddleName])));
            $LastNameScout   = rtrim(addslashes(ucfirst(strtolower($data[$colLastName]))));
            $Email          = strtolower($data[$colEmail]);
            $ULFirst      = addslashes($data[$colULFirst]);
            $ULLast       = addslashes($data[$colULLast]);
            $ULEmail      = $data[$colULEmail];
            $EagledDate   = $data[$colEagledDate];
            $Beneficiary  = addslashes($data[$colBeneficiary]);
            $Gender       = "Male";

            $sqlScout = "INSERT INTO `scouts`(`UnitType`, `UnitNumber`, `District`, `FirstName`, `MiddleName`, `LastName`,
                        `Email`, `ULFirst`,
                        `ULLast`, `ULEmail`, `Beneficiary` , `EagledDate`, `Eagled`, `Gender`,
                        `created_by`) VALUES 
                        ('$UnitType',' $UnitNumber', '$District', '$FirstNameScout','$MiddleNameScout','$LastNameScout',
                        '$Email ', '$ULFirst',
                        '$ULLast',  '$ULEmail', '$Beneficiary', '$EagledDate', '$EagleEarned', '$Gender',
                        '$username')";
          } else if ($Format2018) {
            $UnitType     = $data[$colUnitType];
            $UnitNumber   = $data[$colUnitNumber];
            // District
            $FirstNameScout  = addslashes(ucfirst(strtolower($data[$colFirstName])));
            $MiddleNameScout = addslashes(ucfirst(strtolower($data[$colMiddleName])));
            $LastNameScout   = addslashes(ucfirst(strtolower($data[$colLastName])));
            $Street         = $data[$colStreet_Address];
            $City           = $data[$colCity];
            $State          = $data[$colState];
            $Zip            = $data[$colZip];
            $Email          = strtolower($data[$colEmail]);
            $ULFirst      = addslashes($data[$colULFirst]);
            $ULLast       = addslashes($data[$colULLast]);
            $ULEmail      = $data[$colULEmail];
            $Hours         = $data[$colHours];
            $Beneficiary  = addslashes($data[$colBeneficiary]);
            $Gender       = "Male";

            $sqlScout = "INSERT INTO `scouts`(`UnitType`, `UnitNumber`, `District`, `FirstName`, `MiddleName`, `LastName`,
                            `Street_Address`, `City`, `State`, `Zip`, `Email`, `ULFirst`,
                            `ULLast`, `ULEmail`, `ProjectHours`, `Beneficiary` , `Eagled`, `Gender`,
                            `created_by`) VALUES 
                            ('$UnitType',' $UnitNumber', '$District', '$FirstNameScout','$MiddleNameScout','$LastNameScout',
                            '$Street',  '$City', '$State', $Zip, '$Email ', '$ULFirst',
                            '$ULLast',  '$ULEmail', '$Hours', '$Beneficiary', '$EagleEarned', '$Gender',
                            '$username')";
          } else if ($Format2019) {
            $UnitType     = $data[$colUnitType];
            $UnitNumber   = $data[$colUnitNumber];
            // District
            $FirstNameScout  = addslashes(ucfirst(strtolower($data[$colFirstName])));
            $MiddleNameScout = addslashes(ucfirst(strtolower($data[$colMiddleName])));
            $LastNameScout   = addslashes(ucfirst(strtolower($data[$colLastName])));
            $BOR = $data[$colBOR];
            $Hours         = $data[$colHours];
            $Beneficiary  = addslashes($data[$colBeneficiary]);
            $Gender       = "Male";

            $sqlScout = "INSERT INTO `scouts`(`UnitType`, `UnitNumber`, `District`, `FirstName`, `MiddleName`, `LastName`,
                            `BOR`, `ProjectHours`, `Beneficiary` , `Eagled`, `Gender`,
                            `created_by`) VALUES 
                            ('$UnitType',' $UnitNumber', '$District', '$FirstNameScout','$MiddleNameScout','$LastNameScout',
                            '$BOR',  '$Hours', '$Beneficiary', '$EagleEarned', '$Gender',
                            '$username')";
          } else if ($Format2020) {
            $UnitType     = $data[$colUnitType];
            $UnitNumber   = $data[$colUnitNumber];
            // District
            $FirstNameScout  = addslashes(ucfirst(strtolower($data[$colFirstName])));
            $MiddleNameScout = addslashes(ucfirst(strtolower($data[$colMiddleName])));
            $LastNameScout   = addslashes(ucfirst(strtolower($data[$colLastName])));
            $Street         = $data[$colStreet_Address];
            $City           = $data[$colCity];
            $State          = $data[$colState];
            $Zip            = $data[$colZip];
            $Phone          = self::CleanPhoneNumber($data[$colPhone]);
            $Email          = strtolower($data[$colEmail]);
            $ULFirst      = addslashes($data[$colULFirst]);
            $ULLast       = addslashes($data[$colULLast]);
            $ULEmail      = $data[$colULEmail];
            $Hours         = $data[$colHours];
            $EagledDate   = $data[$colEagledDate];
            $Beneficiary  = addslashes($data[$colBeneficiary]);
            $Gender       = "Male";

            $sqlScout = "INSERT INTO `scouts`(`UnitType`, `UnitNumber`, `District`, `FirstName`, `MiddleName`, `LastName`,
                            `Street_Address`, `City`, `State`, `Zip`, `Phone_Home`, `Email`, `ULFirst`,
                            `ULLast`, `ULEmail`, `ProjectHours`, `Beneficiary` , `EagledDate`, `Eagled`, `Gender`,
                            `created_by`) VALUES 
                            ('$UnitType',' $UnitNumber', '$District', '$FirstNameScout','$MiddleNameScout','$LastNameScout',
                            '$Street',  '$City', '$State', '$Zip', '$Phone', '$Email ', '$ULFirst',
                            '$ULLast',  '$ULEmail', '$Hours', '$Beneficiary', '$EagledDate', '$EagleEarned', '$Gender',
                            '$username')";
          } else if ($Format2021) {
            $UnitType     = $data[$colUnitType];
            $UnitNumber   = $data[$colUnitNumber];
            // District
            $FirstNameScout  = addslashes(ucfirst(strtolower($data[$colFirstName])));
            $MiddleNameScout = addslashes(ucfirst(strtolower($data[$colMiddleName])));
            $LastNameScout   = addslashes(ucfirst(strtolower($data[$colLastName])));
            $Hours         = $data[$colHours];
            $Beneficiary  = addslashes($data[$colBeneficiary]);
            $Gender       = "Male";

            $sqlScout = "INSERT INTO `scouts`(`UnitType`, `UnitNumber`, `District`, `FirstName`, `MiddleName`, `LastName`,
                        `ProjectHours`, `Beneficiary` , `Eagled`, `Gender`,
                        `created_by`) VALUES 
                        ('$UnitType',' $UnitNumber', '$District', '$FirstNameScout','$MiddleNameScout','$LastNameScout',
                        '$Hours', '$Beneficiary', '$EagleEarned', '$Gender',
                        '$username')";
          } else if ($Format2022) {
            $UnitType     = $data[$colUnitType];
            $UnitNumber   = $data[$colUnitNumber];
            // District
            $FirstNameScout  = rtrim(addslashes(ucfirst(strtolower($data[$colFirstName]))));
            $MiddleNameScout = addslashes(ucfirst(strtolower($data[$colMiddleName])));
            $LastNameScout   = rtrim(addslashes(ucfirst(strtolower($data[$colLastName]))));
            $Street         = $data[$colStreet_Address];
            $City           = $data[$colCity];
            $State          = $data[$colState];
            $Zip            = $data[$colZip];
            $Phone          = self::CleanPhoneNumber($data[$colPhone]);
            $Email          = strtolower($data[$colEmail]);
            $ULFirst      = addslashes($data[$colULFirst]);
            $ULLast       = addslashes($data[$colULLast]);
            $ULEmail      = $data[$colULEmail];
            $Hours         = $data[$colHours];
            $EagledDate   = $data[$colEagledDate];
            $Beneficiary  = addslashes($data[$colBeneficiary]);
            $Gender       = "Male";

            $Scout['0'] = $FirstNameScout . " " . $LastNameScout;
            if (self::IsScoutinDB($Scout)) {
              $sqlScout = "UPDATE `scouts` SET `FirstName`='$FirstNameScout',`MiddleName`='$MiddleNameScout',`LastName`='$LastNameScout',
                                `Gender`='$Gender',`Phone_Home`='$Phone', `Email`='$Email',`District`='$District',`UnitType`='$UnitType',
                                `UnitNumber`='$UnitNumber',`ULFirst`='$ULFirst', `ULLast`='$ULLast',`ULEmail`='$ULEmail',`Street_Address`='$Street',
                                `City`='$City',`State`='$State',`Zip`='$Zip',`BOR`='$EagledDate', `Eagled`='1',
                                `ProjectHours`='$Hours',`Beneficiary`='$Beneficiary',`updated_by`='$username' WHERE 
                                `FirstName`='$FirstNameScout' AND `LastName`='$LastNameScout'";
              $RecordsUpdated++;
            } else {
              $sqlScout = "INSERT INTO `scouts`(`UnitType`, `UnitNumber`, `District`, `FirstName`, `MiddleName`, `LastName`,
                                `Street_Address`, `City`, `State`, `Zip`, `Phone_Home`, `Email`, `ULFirst`,
                                `ULLast`, `ULEmail`, `ProjectHours`, `Beneficiary` , `BOR`, `Eagled`, `Gender`,
                                `created_by`) VALUES 
                                ('$UnitType',' $UnitNumber', '$District', '$FirstNameScout','$MiddleNameScout','$LastNameScout',
                                '$Street',  '$City', '$State', '$Zip', '$Phone', '$Email ', '$ULFirst',
                                '$ULLast',  '$ULEmail', '$Hours', '$Beneficiary', '$EagledDate', '$EagleEarned', '$Gender',
                                '$username')";
              $RecordsInserted++;
            }
          }


          if (!self::doQuery($sqlScout)) {
            $RecordsInError++;
            echo "Error: " . $sqlScout . "</br>";
            $str = sprintf("Error: Import Scout %s\n", $sqlScout, Date('Y-m-d H:i:s'));
            error_log($str, 1, "richard.hall@centennialdistrict.co");
          }
        }
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $RecordsInserted . " Updated: " . $RecordsUpdated . " Errors: " . $RecordsInError;
      self::function_alert($Usermsg);
    } else {
      $Usermsg = "Failed to open file";
      self::function_alert($Usermsg);
    }
    return $RecordsInError;
  }
  /*=============================================================================
     *
     * Update YPT status along with adress, email, phone for the 
     * YPT_Centennial_02.csv file.
     * 
     *===========================================================================*/
  //  public static function ImportYPT($fileName)
  //  {
  //    /* Defined the file columns, which change */
  //    $colProgram = 1;
  //    $colFirstName = 5;
  //    $colMiddleName = 6;
  //    $colLastName = 7;
  //    $colMemberID = 8;
  //    $colYPTCurrent = 10;
  //    $colYPTExpires = 11;
  //    $colStreet = 15;
  //    $colCity = 16;
  //    $colState = 17;
  //    $colZip = 18;
  //    $colEmail = 19;
  //    $colPhone = 20;
  //
  //    $RecordsInError = 0;
  //    $RecordsInserted = 0;
  //    $RecordsUpdated = 0;
  //    $SkippedRecords = 0;
  //
  //    $filePath = "Data/" . $fileName;
  //    $UserName = $_SESSION['username'];
  //    $row = 0;
  //    if (($handle = fopen($filePath, "r")) !== FALSE) {
  //      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
  //        if ($row < 8) {
  //          $row++;
  //          continue;
  //        }
  //        $Program = $data[$colProgram];
  //        //if($Program != ""){$SkippedRecords++;continue;}
  //
  //        $FirstName  = addslashes(ucfirst(strtolower($data[$colFirstName])));
  //        $MiddleName = addslashes(ucfirst(strtolower($data[$colMiddleName])));
  //        $LastName   = addslashes(ucfirst(strtolower($data[$colLastName])));
  //        $MemberID   = $data[$colMemberID];
  //        $YPTStatus  = $data[$colYPTCurrent];
  //        $YPTExpires = $data[$colYPTExpires];
  //        $Street     = $data[$colStreet];
  //        $City       = $data[$colCity];
  //        $State      = $data[$colState];
  //        $Zip        = $data[$colZip];
  //        $Email      = $data[$colEmail];
  //        $Phone      = self::CleanPhoneNumber($data[$colPhone]);
  //
  //        $sqlCoach = "UPDATE `coaches` SET `First_Name`=' $FirstName',`Middle_Name`='$MiddleName',`Last_Name`='$LastName',`Member_ID`='$MemberID',
  //                    `Email_Address`='$Email',`Phone_Home`='$Phone',`Street_Address`='$Street ',
  //                    `City`='$City',`State`='$State',`Zip`='$Zip',`YPT_Expires`='$YPTExpires',`updated_by`='$UserName'
  //                    WHERE `Member_ID`='$MemberID'";
  //
  //        if (!self::doQuery($sqlCoach)) {
  //          $RecordsInError++;
  //          echo $sqlCoach;
  //        } else {
  //          $RecordsUpdated++;
  //        }
  //      }
  //      fclose($handle);
  //      $Usermsg = "Records Updated Inserted: " . $RecordsInserted . " Updated: " . $RecordsUpdated . " Errors: " . $RecordsInError;
  //      self::function_alert($Usermsg);
  //      if ($RecordsInError == 0)
  //        self::GotoURL('index.php');
  //    } else {
  //      $Usermsg = "Failed to open file";
  //      self::function_alert($Usermsg);
  //    }
  //    return $RecordsInError;
  //  }
  /*=============================================================================
     *
     * Import Life scouts 
     *  This function will import Life and Eagle scouts from a unit Youth Member
     * Age report.
     * 
     *===========================================================================*/
  public static function ImportLife($fileName)
  {

    $RecordsInError = 0;
    $RecordsInserted = 0;
    $RecordsUpdated = 0;
    $SkippedRecords = 0;
    $Unit = array();

    $IdxName = 0;
    $IdxID = 1;
    $IdxPosition = 2;
    $IdxRank = 3;
    $IdxAge = 4;
    $IdxGrade = 5;

    $filePath = "Data/" . $fileName;
    $UserName = $_SESSION['username'];
    $row = 0;
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        /* Skip down to row 3 [4] to get unit */
        if ($row < 3) {
          $row++;
          continue;
        }
        /*Pull out Unit Data */
        if ($row == 3) {
          $Position = strpos($data[0], "Name:");
          if ($Position !== false) {
            $Unitstr = substr($data[0], $Position + 6);
            $data[0] = self::reduceMultipleSpacesToSingleSpace($Unitstr);
            $Unit = explode(" ", $data[0]);
          }
        }
        /* Now skip down to the data */
        if ($row < 9) {
          $row++;
          continue;
        }

        /* Ensure we have the correct number of columns */
        if (count($data) < 6) {
          continue;
        }
        if (strpos($data[$IdxRank], "Life Scout") !== false) {
          if (!self::IsScoutinDB($data)) {
            self::InsertScout($data, $Unit);
            $RecordsInserted++;
          }
        }
        if (strpos($data[$IdxRank], "Eagle Scout") !== false) {
          if (!self::IsScoutinDB($data)) {
            self::InsertEagle($data, $Unit);
            $RecordsInserted++;
          }
          //                    else{
          //                        self::UpdateEagle($data, $Unit);
          //                        $RecordsUpdated++;
          //                    }
        }
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $RecordsInserted . " Updated: " . $RecordsUpdated . " Errors: " . $RecordsInError;
      self::function_alert($Usermsg);
      if ($RecordsInError == 0)
        self::GotoURL('index.php');
    } else {
      $Usermsg = "Failed to open file";
      self::function_alert($Usermsg);
    }
    return $RecordsInError;
  }
  /*=============================================================================
     *
     * Import Unit Leader details for active Scout "in-Work". Pulled from the 
     * YPT report
     * 
     *===========================================================================*/
  //  public static function ImportUnitLeaders($fileName)
  //  {
  //
  //    $colDistrict = 0;
  //    $colProgram = 1;
  //    $colFirstName = 5;
  //    $colMiddleName = 6;
  //    $colLastName = 7;
  //    $colMemberID = 8;
  //    $colPosition = 9;
  //    $colYPTCurrent = 10;
  //    $colYPTExpires = 11;
  //    $colStreet = 15;
  //    $colCity = 16;
  //    $colState = 17;
  //    $colZip = 18;
  //    $colEmail = 19;
  //    $colPhone = 20;
  //
  //
  //    $RecordsInError = 0;
  //    $RecordsInserted = 0;
  //    $RecordsUpdated = 0;
  //    $SkippedRecords = 0;
  //    $Unit = array();
  //
  //
  //    $filePath = "Data/" . $fileName;
  //    //$UserName = $_SESSION['username'];
  //    $row = 0;
  //    if (($handle = fopen($filePath, "r")) !== FALSE) {
  //      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
  //        if (count($data) < 9) {
  //          continue;
  //        } // Skip until we get to data!
  //        if ($data[$colProgram] == "Troop") {
  //          if ($data[$colPosition] == "Committee Chair" && $data[$colProgram] == "Troop")
  //            self::UpdateCC($data);
  //          else if ($data[$colPosition] == "Scoutmaster" && $data[$colProgram] == "Troop") {
  //            self::UpdateUL($data);
  //          }
  //        } else if ($data[$colProgram] == "Crew") {
  //          if ($data[$colPosition] == "Committee Chair" && $data[$colProgram] == "Crew")
  //            self::UpdateCC($data);
  //          else if ($data[$colPosition] == "Venturing Crew Advisor" && $data[$colProgram] == "Crew") {
  //            self::UpdateUL($data);
  //          }
  //        }
  //      }
  //      fclose($handle);
  //      $Usermsg = "Records Updated Inserted: " . $RecordsInserted . " Updated: " . $RecordsUpdated . " Errors: " . $RecordsInError;
  //      self::function_alert($Usermsg);
  //      if ($RecordsInError == 0)
  //        self::GotoURL('index.php');
  //    } else {
  //      $Usermsg = "Failed to open file";
  //      self::function_alert($Usermsg);
  //    }
  //    return $RecordsInError;
  //  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayPosition($Position)
  {

    $strSelected = !strcmp("District Life-to-Eagle Chair", $Position) ? "selected" : "";
    echo sprintf("<option %s value='District Life-to-Eagle Chair'>District Life-to-Eagle Chair</option>", $strSelected);
    $strSelected = !strcmp("District Advancement Chair", $Position) ? "selected" : "";
    echo sprintf("<option %s value='District Advancement Chair'>District Advancement Chair</option>", $strSelected);
    $strSelected = !strcmp("District Eagle Project Approval", $Position) ? "selected" : "";
    echo sprintf("<option %s value='District Eagle Project Approval'>District Eagle Project Approval</option>", $strSelected);
    $strSelected = !strcmp("District Eagle Coach", $Position) ? "selected" : "";
    echo sprintf("<option %s value='District Eagle Coach'>District Eagle Coach</option>", $strSelected);
    $strSelected = !strcmp("Unit Eagle Mentor", $Position) ? "selected" : "";
    echo sprintf("<option %s value='Unit Eagle Mentor'>Unit Eagle Mentor</option>", $strSelected);

    return $strSelected;
  }
  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayDistrict($District)
  {
    $strSelected = !strcmp('Centennial', $District) ? "selected" : "";
    echo sprintf("<option %s value='Centennial'>Centennial</option>", $strSelected);
    $strSelected = !strcmp("Alpine", $District) ? "selected" : "";
    echo sprintf("<option %s value='Alpine'>Alpine</option>", $strSelected);
    $strSelected = !strcmp("BlackFeather", $District) ? "selected" : "";
    echo sprintf("<option %s value='BlackFeather'>Black Feather</option>", $strSelected);
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

    return;
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

    return;
  }
  /******************************************************************************
   *
   * Make a form dropdown tp select unit types.
   *
   *****************************************************************************/
  public static function DisplayUnitType($element_name, $UnitType)
  {
    echo "<select class='form-control' name='$element_name' >";

    $strSelected = !strcmp("Crew", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Crew'>Crew</option>", $strSelected);
    $strSelected = !strcmp("Ship", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Ship'>Ship</option>", $strSelected);
    $strSelected = !strcmp("Troop", $UnitType) ? "selected" : "";
    echo sprintf("<option %s value='Troop'>Troop</option>", $strSelected);
    echo '</select>';
  }


  /******************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function DisplayGuardianRelationship($Relationship)
  {
    echo "<option value=''></option>";
    $strSelected = !strcmp("Father", $Relationship) ? "selected" : "";
    echo sprintf("<option %s value='Father'>Father</option>", $strSelected);
    $strSelected = !strcmp("Mother", $Relationship) ? "selected" : "";
    echo sprintf("<option %s value='Mother'>Mother</option>", $strSelected);
    $strSelected = !strcmp("GrandFather", $Relationship) ? "selected" : "";
    echo sprintf("<option %s value='GrandFather'>GrandFather</option>", $strSelected);
    $strSelected = !strcmp("GrandMother", $Relationship) ? "selected" : "";
    echo sprintf("<option %s value='GrandMother'>GrandMother</option>", $strSelected);
    $strSelected = !strcmp("Guardian", $Relationship) ? "selected" : "";
    echo sprintf("<option %s value='Guardian'>Guardian</option>", $strSelected);
    $strSelected = !strcmp("Other", $Relationship) ? "selected" : "";
    echo sprintf("<option %s value='Other'>Other</option>", $strSelected);
  }
  /******************************************************************************
   *
   * Display list of coaches from the coaches table.
   * Parameters:
   *  $Coach - Index value of coach from database, will be selected as the 
   * defualt in the option box.
   *
   *****************************************************************************/
  public static function DisplayCoach($element_name, $Coach)
  {
    $queryCoaches = "SELECT DISTINCTROW Last_Name, First_Name, Coachesid FROM coaches ORDER BY Last_Name, First_Name";

    $result_ByCoaches = self::doQuery($queryCoaches);
    if (!$result_ByCoaches) {
      self::function_alert("ERROR: self::doQuery($result_ByCoaches)");
    }

    echo "<select class='form-control' name='$element_name' >";
    echo "<option value=\"\" </option>";
    while ($rowCoach = $result_ByCoaches->fetch_assoc()) {
      $strSelected = ($rowCoach['Coachesid'] == $Coach) ? "selected" : "";

      echo sprintf("<option %s value=" . $rowCoach['Coachesid'] . ">" . $rowCoach['Last_Name'] . " " . $rowCoach['First_Name'] . "</option>", $strSelected);
    }
    echo "</select>";
  }
  /*****************************************************************************
   *
   *
   *****************************************************************************/
  public static function UpdateCoachRecord($Coach)
  {
    //Check to see if this is a new coach, 
    if ($Coach['Coachesid'] == -1) {
      // It's a new coach so INSERT
      $sqlStmt = "INSERT INTO `coaches`(`First_Name`, `PreferredName`, `Middle_Name`, `Last_Name`, `Member_ID`, `Email_Address`, `Phone_Home`, 
            `Phone_Mobile`, `Gender`, `District`, `Street_Address`, `City`, `State`, `Zip`, `Position`, `Trained`, `YPT_Expires`, 
            `Active`, `Notes`, `created_by`) 
            VALUES 
            ('$Coach[First_Name]','$Coach[PreferredName]', '$Coach[Middle_Name]','$Coach[Last_Name]','$Coach[Member_ID]', '$Coach[Email_Address]','$Coach[Phone_Home]',
            '$Coach[Phone_Mobile]','$Coach[Gender]','$Coach[District]','$Coach[Street_Address]','$Coach[City]','$Coach[State]','$Coach[Zip]',
            '$Coach[Position]','$Coach[Trained]','$Coach[YPT_Expires]','$Coach[Active]','$Coach[Notes]','$_SESSION[username]')";
    } else {
      $sqlStmt = "UPDATE `coaches` SET `First_Name`='$Coach[First_Name]',`PreferredName`='$Coach[PreferredName]', `Middle_Name`='$Coach[Middle_Name]',`Last_Name`='$Coach[Last_Name]',
            `Member_ID`='$Coach[Member_ID]',`Email_Address`='$Coach[Email_Address]',`Phone_Home`='$Coach[Phone_Home]',`Phone_Mobile`='$Coach[Phone_Mobile]',`Gender`='$Coach[Gender]',
            `District`='$Coach[District]',
            `Street_Address`='$Coach[Street_Address]',
            `City`='$Coach[City]',
            `State`='$Coach[State]',
            `Zip`='$Coach[Zip]',
            `Position`='$Coach[Position]',`Trained`='$Coach[Trained]',`YPT_Expires`='$Coach[YPT_Expires]',`Active`='$Coach[Active]',
            `Notes`='$Coach[Notes]',`updated_by`='$_SESSION[username])'
            WHERE Coachesid='$Coach[Coachesid]'";
    }

    // Excute the sql Statement
    $Result = self::doQuery($sqlStmt);
    return $Result;
  }
  /*****************************************************************************
   *
   * We have to create an audit trail this way beacuse iPage does not support
   * the use of triggers in the database.
   *
   *****************************************************************************/
  public static function CreateAudit($Old, $New, $IdKey)
  {
    if ($New[$IdKey] == -1) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed CreateAudit.'];
      return;
    }

    $Index = 0;
    $Indexid = 'Coachesid';
    if ($IdKey == 'Coachesid') {
      $PrimaryKey = 'Coachesid';
      $db = 'coaches_audit_trail';
    } else {
      $PrimaryKey = 'Scoutid';
      $db = 'scouts_audit_trail';
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
        if (!$Result) {
          $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Failed CreateAudit 2.'];
          return;
        }
      }
      $Index++;
    }
    return $Result;
  }
  /*****************************************************************************
   *
   *
   *****************************************************************************/
  public static function UpdateScoutRecord($Scout)
  {
    //Check to see if this is a new scout, 
    $ProjectName = addslashes($Scout['ProjectName']);

    // TODO: Need to ensure that we do not have a duplicate scout
    //self::IsScoutIDinDB($Scout['MemberId']);


    $sqlStmt = "UPDATE `scouts` SET `FirstName`='$Scout[FirstName]',`PreferredName`='$Scout[PreferredName]',`MiddleName`='$Scout[MiddleName]',`LastName`='$Scout[LastName]', `is_deleted`='$Scout[is_deleted]',
            `Email`='$Scout[Email]', `Phone_Home`='$Scout[Phone_Home]',`Phone_Mobile`='$Scout[Phone_Mobile]',
            `Street_Address`='$Scout[Street_Address]',`City`='$Scout[City]',`State`='$Scout[State]',`Zip`='$Scout[Zip]',
            `UnitType`='$Scout[UnitType]',`UnitNumber`='$Scout[UnitNumber]', `District`='$Scout[District]',`Gender`='$Scout[Gender]', `AgeOutDate`='$Scout[AgeOutDate]',`MemberId`='$Scout[MemberId]',
            `ULFirst`='$Scout[ULFirst]',`ULLast`='$Scout[ULLast]',`ULPhone`='$Scout[ULPhone]',`ULEmail`='$Scout[ULEmail]',
            `CCFirst`='$Scout[CCFirst]',`CCLast`='$Scout[CCLast]',`CCPhone`='$Scout[CCPhone]',`CCEmail`='$Scout[CCEmail]',
            `GuardianFirst`='$Scout[GuardianFirst]',`GuardianLast`='$Scout[GuardianLast]',`GuardianPhone`='$Scout[GuardianPhone]',
            `GuardianEmail`='$Scout[GuardianEmail]', `GuardianRelationship`='$Scout[GuardianRelationship]',
            `AgedOut`='$Scout[AgedOut]',`AttendedPreview`='$Scout[AttendedPreview]',`ProjectApproved`='$Scout[ProjectApproved]',`ProjectDate`='$Scout[ProjectDate]',`Coach`='$Scout[Coach]',`ProjectHours`='$Scout[ProjectHours]',
            `Beneficiary`='$Scout[Beneficiary]',`ProjectName`='$ProjectName',
            `BOR`='$Scout[BOR]',`BOR_Member`='$Scout[BOR_Member]',`Eagled`='$Scout[Eagled]',
            `Notes`='$Scout[Notes]',`updated_by`='$_SESSION[username]' WHERE `Scoutid` = '$Scout[Scoutid]'";
    //    }

    // Excute the sql Statement
    $Result = self::doQuery($sqlStmt);
    if (!$Result) {
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Duplicate Member IDs found in database for Member ID:  Please contact the system administrator.'];
    }
    return $Result;
  }
  /*****************************************************************************
   *
   *
   *****************************************************************************/
  public static function AgedOutByYear($Year)
  {
    $sql = "SELECT * FROM scouts WHERE `AgedOut` = '1' AND `AgeOutDate` LIKE '%$Year%'";
    // Excute the sql Statement
    $Result = self::doQuery($sql);
    return mysqli_num_rows($Result);
  }
  /*****************************************************************************
   *
   *
   *****************************************************************************/
  public static function AttendPreviewAgedOut($Year)
  {
    $sql = "SELECT * FROM scouts WHERE `AgedOut` = '1' AND `AttendedPreview` = '1' AND `AgeOutDate` LIKE '%$Year%'";
    // Excute the sql Statement
    $Result = self::doQuery($sql);
    return mysqli_num_rows($Result);
  }
  /*****************************************************************************
   *
   *
   *****************************************************************************/
  public static function AttendPreviewEagled($Year)
  {
    $sql = "SELECT * FROM scouts WHERE `Eagled` = '1' AND `AttendedPreview` = '1' AND `BOR` LIKE '%$Year%'";
    // Excute the sql Statement
    $Result = self::doQuery($sql);
    return mysqli_num_rows($Result);
  }
  /*****************************************************************************
   *
   *
   *****************************************************************************/
  public static function ApprovedProject($Year)
  {
    $sql = "SELECT * FROM scouts WHERE `ProjectApproved` = '1' AND `BOR` LIKE '%$Year%'";
    // Excute the sql Statement
    $Result = self::doQuery($sql);
    return mysqli_num_rows($Result);
  }
  /*****************************************************************************
   *
   *
   *
   *****************************************************************************/
  public static function SelectScout()
  {
    // Scout selection query
    $queryScouts = "SELECT DISTINCT LastName, MiddleName, FirstName, Scoutid FROM scouts 
                WHERE (`Scoutid` IS NOT NULL)
                AND (`Eagled` IS NULL OR `Eagled` = 0) 
                AND (`AgedOut` IS NULL OR `AgedOut` = 0)
                AND (`is_deleted` IS NULL OR `is_deleted` = 0)
                ORDER BY LastName, FirstName";
    $result = self::doQuery($queryScouts);
    if (!$result) {
      self::function_alert("ERROR: Query failed: " . mysqli_error(self::getDbConn()));
    }

  ?>
    <h4>Select Scout</h4>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
      <div class="form-row px-5 d-print-none">
        <div class="col-3">
          <label for="ScoutID">Choose a Scout: </label>
          <select class="form-control" id="ScoutID" name="ScoutID">
            <option value="">-- Select Scout --</option>
            <?php while ($row = $result->fetch_assoc()): ?>
              <?php if ($row['Scoutid'] != -1) { ?>
                <option value="<?php echo htmlspecialchars($row['Scoutid']); ?>">
                  <?php echo htmlspecialchars(trim($row['LastName'] . ', ' . $row['FirstName'])); ?>
                </option>
              <?php } ?>
            <?php endwhile; ?>
            <option value="-1">Add New Scout</option>
          </select>
        </div>
        <div class="col-1 py-4">
          <input class="btn btn-primary btn-sm" type="submit" name="SubmitScout" value="Select Scout" />
        </div>
      </div>
    </form>
<?php

  }
}
