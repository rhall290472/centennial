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
 * ****************************************************************************
 * ****************************************************************************
 * The Singleton class defines the `GetInstance` method that serves as an
 * alternative to constructor and lets clients access the same instance of this
 * class over and over.
 * ****************************************************************************
 * ****************************************************************************
 */
class AdultLeaders
{
  // Class-level constants for CSV column indices
  const COL_DISTRICTNAME = 0;
  const COL_DISPLAYNAME = 1;
  const COL_FIRSTNAME = 2;
  const COL_LASTNAME = 3;
  const COL_POSITIONNAME = 4;
  const COL_PHONE = 5;
  const COL_EMAIL = 6;

  private static $instances = [];
  private ?mysqli $dbConn = null; // Allow null initially

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
   * Provide user information for access to the Adult_leaders database
   * 
   *****************************************************************************/
  static function getConfigData()
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
    if ($db->dbConn->connect_error) {
      error_log("Database connection failed: " . $db->dbConn->connect_error);
      throw new \Exception("Failed to connect to database: " . $db->dbConn->connect_error);
    }
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
      if ($mysqli === null) {
        $strError = "doQuery: No database connection available for query: " . $sql;
        error_log($strError, 0);
        return $Result;
      }
      $Result = $mysqli->query($sql);
      if (!$Result) {
        $strError = "Error: doQuery(" . $sql . ") " . $mysqli->error . " " . __FILE__ . ", " . __LINE__;
        error_log($strError, 0);
      }
    } catch (Exception $ex) {
      $strError = "I was unable to execute query. " . $ex->getMessage();
      error_log($strError, 0);
      $Result = null;
    }
    return $Result;
  }
  /******************************************************************************
   * 
   * Return the current unit leader information
   * 
   *****************************************************************************/
  public static function GetUnitLeader($Unit)
  {
    $UL = array();

    $qryUL = "SELECT * FROM ypt WHERE `Unit_Number`='$Unit' AND (`Position`='Scoutmaster' OR
        `Position`='Venturing Crew Advisor')";
    $result_ul = self::doQuery($qryUL);
    if ($result_ul && $row = $result_ul->fetch_assoc()) {
      $UL['FirstName'] = $row['First_Name'];
      $UL['LastName'] = $row['Last_Name'];
      $UL['Email'] = $row['Email_Address'];
      $UL['Phone'] = $row['Phone'];
    } else {
      $UL['FirstName'] = '';
      $UL['LastName'] = '';
      $UL['Email'] = '';
      $UL['Phone'] = '';
    }
    return $UL;
  }
  /******************************************************************************
   * 
   * Return the current Committee Chair information
   * 
   *****************************************************************************/
  public static function GetCommitteeChair($Unit)
  {
    $CC = array();

    $qryCC = "SELECT * FROM ypt WHERE `Unit_Number`='$Unit' AND `Position`='Committee Chair'";
    $result_cc = self::doQuery($qryCC);
    if ($result_cc && $row = $result_cc->fetch_assoc()) {
      $CC['FirstName'] = $row['First_Name'];
      $CC['LastName'] = $row['Last_Name'];
      $CC['Email'] = $row['Email_Address'];
      $CC['Phone'] = $row['Phone'];
    } else {
      $CC['FirstName'] = '';
      $CC['LastName'] = '';
      $CC['Email'] = '';
      $CC['Phone'] = '';
    }
    return $CC;
  }
  /******************************************************************************
   * 
   * Return YES if member is YPT Current
   * 
   *****************************************************************************/
  public static function IsTrained($FName, $LName, $Position)
  {
    // Input validation
    if (
      !is_string($FName) || !is_string($LName) || !is_string($Position) ||
      empty(trim($FName)) || empty(trim($LName)) || empty(trim($Position))
    ) {
      error_log("IsTrained: Invalid input - FName: '$FName', LName: '$LName', Position: '$Position'");
      return false; // Return false for invalid inputs
    }

    // Sanitize inputs (trim whitespace)
    $FName = trim($FName);
    $LName = trim($LName);
    $Position = trim($Position);

    try {
      // Get database connection
      $dbConn = self::getDbConn();
      if (!$dbConn) {
        error_log("IsTrained: Database connection failed");
        return false; // Return false if connection fails
      }

      // Prepare SQL query with placeholders
      $qryTrained = "SELECT Trained FROM trainedleader WHERE First_Name = ? AND Last_Name = ? AND Position = ?";
      $stmt = mysqli_prepare($dbConn, $qryTrained);
      if (!$stmt) {
        error_log("IsTrained: Failed to prepare statement: " . mysqli_error($dbConn));
        return false;
      }

      // Bind parameters
      mysqli_stmt_bind_param($stmt, "sss", $FName, $LName, $Position);

      // Execute query
      if (!mysqli_stmt_execute($stmt)) {
        error_log("IsTrained: Query execution failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
      }

      // Get result
      $result = mysqli_stmt_get_result($stmt);
      if (!$result) {
        error_log("IsTrained: Failed to get result: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
      }

      // Fetch row
      $row = mysqli_fetch_assoc($result);
      mysqli_stmt_close($stmt);

      // Check if row exists
      if (!$row) {
        error_log("IsTrained: No matching record found for FName: '$FName', LName: '$LName', Position: '$Position'");
        return false; // No record found
      }

      // Return Trained status (assuming 'Trained' is a string like 'Yes'/'No')
      $trained = $row['Trained'];
      return ($trained === 'Yes' || $trained === '1' || $trained === true); // Normalize to boolean
    } catch (Exception $e) {
      error_log("IsTrained: Exception occurred: " . $e->getMessage());
      return false; // Return false on any exception
    }
  }
  /******************************************************************************
   * 
   * Return a mysqli results for for all untrained leader in selected position
   * 
   *****************************************************************************/
  public static function GetPositionUnTrained($Position)
  {
    $qry = "SELECT * FROM trainedleader WHERE Position='$Position' AND Trained = 'NO' ORDER BY Last_Name";
    $result = self::doQuery($qry);

    return $result;
  }

  /******************************************************************************
   * 
   * Return the unit which the member s registered with, maybe more that one but
   * will be limited to two.
   * 
   *****************************************************************************/
  public static function FindMemberUnit($FName, $LName, $Unit1Type, $Unit2Type)
  {
    $Units = array();

    if (is_null($Unit2Type))
      $qryUnits = "SELECT DISTINCT Unit FROM trainedleader WHERE First_Name='$FName' AND Last_Name='$LName' AND Unit LIKE '%$Unit1Type%'";
    else
      $qryUnits = "SELECT DISTINCT Unit FROM trainedleader WHERE First_Name='$FName' AND Last_Name='$LName' AND (Unit LIKE '%$Unit1Type%' OR Unit LIKE '%$Unit2Type%')";

    $result_units = self::doQuery($qryUnits);
    while ($row_unit = $result_units->fetch_assoc()) {
      $Unit = $row_unit['Unit'];
      // Remove the unit type from returned value.
      $pieces = explode(" ", $Unit);
      $Units[] = $pieces[1];
    }

    //Check to ensure we found something
    if (is_array($Units)) {
      if (sizeof($Units) < 1) {
        $Units[0] = "";
        $Units[1] = "";
      } else if (sizeof($Units) < 2) {
        $Units[1] = "";
      }
    } else {
      $Units[0] = "";
      $Units[1] = "";
    }

    return $Units;
  }
  /******************************************************************************
   * 
   * Return the unit which the member s registered with, maybe more that one but
   * will be limited to two.
   * 
   *****************************************************************************/
  public static function FindMemberID($FName, $LName)
  {
    $MemberID = array();

    $qryID = "SELECT DISTINCT Member_ID FROM ypt WHERE First_Name='$FName' AND Last_Name='$LName'";

    $result_units = self::doQuery($qryID);
    while ($row_ID = $result_units->fetch_assoc()) {
      $MemberID = $row_ID['Member_ID'];
    }

    return $MemberID;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTLastName()
  {
    $qry_ypt_lastname = "SELECT DISTINCT Last_Name, First_Name, Member_ID FROM ypt Where Status = 'NO' ORDER BY Last_Name";
    $result_ypt_lastname = self::doQuery($qry_ypt_lastname);
    return $result_ypt_lastname;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTByID($mid)
  {
    // Validate input
    if (empty($mid) || !is_string($mid)) {
      return null;
    }

    // Use prepared statement to prevent SQL injection
    $conn = self::getDbConn(); // Assume a method to get DB connection
    $stmt = $conn->prepare("SELECT `Y01_Expires` FROM `ypt` WHERE `Member_ID` = ?");
    if (!$stmt) {
      return null; // Handle preparation failure
    }

    $stmt->bind_param("s", $mid);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch result
    $ypt_expires = $result->fetch_assoc();

    // Clean up
    $stmt->close();

    // Return null if no row found
    return $ypt_expires ?: null;
  }

  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTPositon()
  {
    $qry_ypt_position = "SELECT DISTINCT Position FROM ypt Where Status = 'NO' ORDER BY Position";
    $result_ypt_position = self::doQuery($qry_ypt_position);
    return $result_ypt_position;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTUnit()
  {
    $qry_ypt_unit = "SELECT DISTINCT Unit_Number, Program FROM ypt Where Status = 'NO' ORDER BY Program, Unit_Number";
    $result_ypt_unit = self::doQuery($qry_ypt_unit);
    return $result_ypt_unit;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTIDCount($mID)
  {
    if (!empty($mID)) {
      $SqlExpiredypt = sprintf("SELECT * FROM `ypt` WHERE `Status` = 'NO' AND `Member_ID` = '%s' ORDER BY Last_Name", $mID);
    } else {
      $SqlExpiredypt = "SELECT * FROM `ypt` WHERE `Status` = 'NO' ORDER BY 'Last_Name'";
    }
    $Result = self::doQuery($SqlExpiredypt);
    $UnTrained = mysqli_num_rows($Result);
    return $UnTrained;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTTotalIDCount($mID)
  {
    if (!empty($mID)) {
      $SqlValidypt = sprintf("SELECT * FROM `ypt` WHERE `Member_ID` = '%s'", $mID);
    } else {
      $SqlValidypt = "SELECT * FROM `ypt`";
    }
    $Result = self::doQuery($SqlValidypt);
    $UnTrained = mysqli_num_rows($Result);
    return $UnTrained;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetResultIDYPT($mID)
  {
    if (!empty($mID)) {
      $SqlExpiredypt = sprintf("SELECT * FROM `ypt` WHERE `Status` = 'NO' AND `Member_ID` = '%s' ORDER BY Last_Name", $mID);
    } else {
      $SqlExpiredypt = "SELECT * FROM `ypt` WHERE `Status` = 'NO' ORDER BY 'Last_Name'";
    }
    $Result = self::doQuery($SqlExpiredypt);
    return $Result;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTPositionCount($position)
  {
    if (!empty($position)) {
      $SqlExpiredypt = sprintf("SELECT * FROM `ypt` WHERE `Status` = 'NO' AND `Position` = '%s' ORDER BY Position", $position);
    } else {
      $SqlExpiredypt = "SELECT * FROM `ypt` WHERE `Status` = 'NO' ORDER BY Position";
    }
    $Result = self::doQuery($SqlExpiredypt);
    $Expiredypt = mysqli_num_rows($Result);
    return $Expiredypt;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTTotalPositionCount($position)
  {
    if (!empty($position)) {
      $SqlValidypt = sprintf("SELECT * FROM `ypt` WHERE `Position` = '%s'", $position);
    } else {
      $SqlValidypt = "SELECT * FROM `ypt`";
    }
    $Result = self::doQuery($SqlValidypt);
    $Validypt = mysqli_num_rows($Result);
    return $Validypt;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetResultPositionYPT($position)
  {
    if (!empty($position)) {
      $SqlExpiredypt = sprintf("SELECT * FROM `ypt` WHERE `Status` = 'NO' AND `Position` = '%s' ORDER BY Position", $position);
    } else {
      $SqlExpiredypt = "SELECT * FROM `ypt` WHERE `Status` = 'NO' ORDER BY Position";
    }
    $Result = self::doQuery($SqlExpiredypt);
    return $Result;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTUnitCount($unit)
  {
    if (!empty($unit)) {
      $SqlExpiredypt = sprintf("SELECT * FROM `ypt` WHERE `Status` = 'NO' AND `Unit_Number` = '%s' ORDER BY Unit_Number", $unit);
    } else {
      $SqlExpiredypt = "SELECT * FROM `ypt` WHERE `Status` = 'NO' ORDER BY Unit_Number";
    }
    $Result = self::doQuery($SqlExpiredypt);
    $Expiredypt = mysqli_num_rows($Result);
    return $Expiredypt;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetYPTTotalUnitCount($unit)
  {
    if (!empty($unit)) {
      $SqlValidypt = sprintf("SELECT * FROM `ypt`  WHERE `Unit_Number` = '%s'", $unit);
    } else {
      $SqlValidypt = "SELECT * FROM `ypt`";
    }
    $Result = self::doQuery($SqlValidypt);
    $Validypt = mysqli_num_rows($Result);
    return $Validypt;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetResultUnitYPT($unit)
  {
    if (!empty($unit)) {
      $SqlExpiredypt = sprintf("SELECT * FROM `ypt` WHERE `Status` = 'NO' AND `Unit_Number` = '%s' ORDER BY Unit_Number", $unit);
    } else {
      $SqlExpiredypt = "SELECT * FROM `ypt` WHERE `Status` = 'NO' ORDER BY Unit_Number";
    }
    $Result = self::doQuery($SqlExpiredypt);
    return $Result;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetMemberYPT($Member)
  {
    if (empty($Member)) {
      $strErrorMsg = "Error: GetMemberYPT(" . $Member . ") called with empty MemberID";
      error_log($strErrorMsg, 0);
      $row_ypt = false;
      $YPT = "";
    } else {
      $sql = sprintf('SELECT * FROM ypt Where Member_ID = "%s"', $Member);
      $result_ypt = self::doQuery($sql);
      $row_ypt = $result_ypt->fetch_assoc();
      if ($row_ypt)
        $YPT = $row_ypt['Y01_Expires'];
      else
        $YPT = "";
    }
    return $YPT;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function GetMemberYPTStatus($Member)
  {
    if (empty($Member)) {
      $strErrorMsg = "Error: GetMemberYPT(" . $Member . ") called with empty MemberID";
      error_log($strErrorMsg, 0);
      $row_ypt = false;
      $YPT = "";
    } else {
      $sql = sprintf('SELECT * FROM ypt Where Member_ID = "%s"', $Member);
      $result_ypt = self::doQuery($sql);
      $row_ypt = $result_ypt->fetch_assoc();
      if ($row_ypt)
        $YPT = $row_ypt['Status'];
      else
        $YPT = "";
    }
    return $YPT;
  }
  /**
   * GetUntrainedLastName()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetUntrainedName()
  {
    $qry_name = "SELECT DISTINCT Last_Name, First_Name, MemberID FROM trainedleader WHERE Trained = 'NO' ORDER BY Last_Name";
    $result_untrained_name = self::doQuery($qry_name);
    return  $result_untrained_name;
  }
  /**
   * GetUntrainedPosition()
   * Get a 
   */
  public static function GetUntrainedPosition()
  {
    $qryposition = "SELECT DISTINCT Position FROM trainedleader WHERE Trained = 'NO' ORDER BY Position";
    $result_untrained_position = self::doQuery($qryposition);
    return $result_untrained_position;
  }
  /**
   * GetUntrainedUnit()
   * Get a 
   */
  public static function GetUntrainedUnit()
  {
    $qryunit = "SELECT DISTINCT Unit FROM trainedleader WHERE Trained = 'NO' ORDER BY Unit";
    $result_untrained_unit = self::doQuery($qryunit);
    return $result_untrained_unit;
  }

  /**
   * GetUnTrainedIDCount()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetUnTrainedIDCount($ID)
  {
    if (!empty($ID)) {
      $SqlUnTrained = sprintf("SELECT * FROM trainedleader WHERE `Trained` = 'No' AND `MemberID` = '%s' ORDER BY Direct_Contact_Leader DESC,Position", $ID);
    } else {
      $SqlUnTrained = "SELECT * FROM trainedleader WHERE `Trained` = 'No' ORDER BY Direct_Contact_Leader DESC, Last_Name";
    }
    $UnTrained = -1;
    $Result = self::doQuery($SqlUnTrained);
    if ($Result)
      $UnTrained = mysqli_num_rows($Result);
    return $UnTrained;
  }
  /**
   * GetTotalIDCount()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetTotalIDCount($ID)
  {
    if (!empty($ID)) {
      $SqlTotal = sprintf("SELECT * FROM  WHERE `MemberID` = '%s' ORDER BY Direct_Contact_Leader DESC,Position", $ID);
    } else {
      $SqlTotal =  "SELECT * FROM trainedleader";
    }
    $Total = -1;
    $Result = self::doQuery($SqlTotal);
    if ($Result)
      $Total = mysqli_num_rows($Result);
    return $Total;
  }
  /**
   * GetResultIDUnTrained()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetResultIDUnTrained($ID)
  {
    if (!empty($ID)) {
      $SqlUnTrained = sprintf("SELECT * FROM trainedleader WHERE `Trained` = 'No' AND `MemberID` = '%s' ORDER BY Direct_Contact_Leader DESC,Position", $ID);
    } else {
      $SqlUnTrained = "SELECT * FROM trainedleader WHERE `Trained` = 'No' ORDER BY Direct_Contact_Leader DESC, Last_Name";
    }
    $Result = self::doQuery($SqlUnTrained);
    return $Result;
  }
  /**
   * GetUntrainedLastName()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetByPosition($position)
  {
    //echo "<h1>Untrained Leaders by Positon ", $position, "</h1>";
    $sql = array();
    if (!empty($position)) {
      $SqlUnTrained = sprintf("SELECT * FROM trainedleader WHERE `Trained` = 'No' AND Position = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $position);
      $SqlTotal = sprintf("SELECT * FROM trainedleader WHERE `Position` = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $position);
    } else {
      $SqlUnTrained = "SELECT * FROM trainedleader WHERE `Trained` = 'No' ORDER BY Direct_Contact_Leader DESC,Position,Last_Name";
      $SqlTotal = "SELECT * FROM trainedleader";
    }
    $sql[0] = $SqlUnTrained;
    $sql[1] = $SqlTotal;
    return $sql;
  }
  /**
   * GetUnTrainedPositionCount()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetUnTrainedPositionCount($position)
  {
    if (!empty($position)) {
      $SqlUnTrained = sprintf("SELECT * FROM  WHERE `Trained` = 'No' AND Position = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $position);
    } else {
      $SqlUnTrained = "SELECT * FROM trainedleader WHERE `Trained` = 'No' ORDER BY Direct_Contact_Leader DESC,Position,Last_Name";
    }
    $Result = self::doQuery($SqlUnTrained);
    $UnTrained = mysqli_num_rows($Result);
    return $UnTrained;
  }
  /**
   * GetTotalPositionCount()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetTotalPositionCount($position)
  {
    if (!empty($position)) {
      $SqlTotal = sprintf("SELECT * FROM trainedleader WHERE `Position` = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $position);
    } else {
      $SqlTotal = "SELECT * FROM trainedleader";
    }
    $Result = self::doQuery($SqlTotal);
    $Total = mysqli_num_rows($Result);
    return $Total;
  }
  /**
   * GetResultPositionUnTrained()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetResultPositionUnTrained($position)
  {
    if (!empty($position)) {
      $SqlUnTrained = sprintf("SELECT * FROM trainedleader WHERE `Trained` = 'No' AND Position = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $position);
    } else {
      $SqlUnTrained = "SELECT * FROM trainedleader WHERE `Trained` = 'No' ORDER BY Direct_Contact_Leader DESC,Position,Last_Name";
    }
    $Result = mysqli_query(self::getDbConn(), $SqlUnTrained);
    return $Result;
  }
  /**
   * GetResultPositionUnTrained()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetUnTrainedUnitCount($unit)
  {
    if (!empty($unit)) {
      $SqlUnTrained = sprintf("SELECT * FROM trainedleader WHERE `Trained` = 'No' AND Unit = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $unit);
    } else {
      $SqlUnTrained = "SELECT * FROM trainedleader WHERE `Trained` = 'No' ORDER BY Direct_Contact_Leader DESC,Unit,Last_Name";
    }
    $Result = self::doQuery($SqlUnTrained);
    $UnTrained = mysqli_num_rows($Result);
    return $UnTrained;
  }
  /**
   * GetResultPositionUnTrained()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetTotalUnitCount($unit)
  {
    if (!empty($unit)) {
      $SqlTotal = sprintf("SELECT * FROM trainedleader WHERE `Unit` = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $unit);
    } else {
      $SqlTotal = "SELECT * FROM trainedleader";
    }
    $Result = self::doQuery($SqlTotal);
    $Total = mysqli_num_rows($Result);
    return $Total;
  }
  /**
   * GetResultPositionUnTrained()
   * Execute a quuery of the connected database and return a list of untrained leaders
   * by last name.
   */
  public static function GetResultUnitUnTrained($unit)
  {
    if (!empty($unit)) {
      $SqlUnTrained = sprintf("SELECT * FROM trainedleader WHERE `Trained` = 'No' AND Unit = '%s' ORDER BY Direct_Contact_Leader DESC,Last_Name", $unit);
    } else {
      $SqlUnTrained = "SELECT * FROM trainedleader WHERE `Trained` = 'No' ORDER BY Direct_Contact_Leader DESC,Unit,Last_Name";
    }
    $Result = self::doQuery($SqlUnTrained);
    return $Result;
  }
  /******************************************************************************
   * 
   *****************************************************************************/
  public static function function_alert($msg)
  {
    // echo "<script type='text/javascript'>alert('$msg');</script>";
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
        if (!strcmp($Gender, "(G)") || !strcmp($UnitGender, "G") || !strcmp($UnitGender, "Female"))
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
   * 
   * Read in the trained leader report from my.scouting.org
   * 
   *****************************************************************************/
  public static function  &TrainedLeader($fileName)
  {
    $colCouncil = 0;
    $colServiceArea = 1;
    $colDistrict    = 2;
    $colSubDistrict = 3;
    $colUnit        = 4;
    $colGenderAccepted  = 5;
    $colCharteredOrg    = 6;
    $colFirstName       = 7;
    $colMiddleName      = 8;
    $colLastName        = 9;
    $colZipCode         = 10;
    $colMemberID        = 11;
    $colProgram         = 12;
    $colEmail           = 13;
    $colPosition        = 14;
    $colDirectContact   = 15;
    $colTrained         = 16;
    $colExpirDate       = 17;
    $colIncomMandatory  = 18;
    $colIncomClass      = 19;
    $colIncomOnline     = 20;

    $sqlLeadersTrainedInsertSt = "INSERT INTO `trainedleader`(`Council`, `Service_Area`, `District`, `Sub_District`, `Unit`, `Gender_Accepted`, `Chartered_Org_Name`, `First_Name`, 
    		`Middle_Name`, `Last_Name`, `Zip_Code`, `MemberID`, `Program`, `Email`, `Position`, `Direct_Contact_Leader`, `Trained`, `Registration_Expiration_Date`, 
    		`Incomplete_Mandatory`, `Incomplete_Classroom`, `Incomplete_Online`) VALUES (";

    $Inserted = 0;
    $Updated = 0;
    $RecordsInError = 0;
    $row = 1;
    $filePath = $fileName;
    if (!file_exists($filePath)) {
      $strError = "ERROR: File not found: " . $filePath;
      error_log($strError, 0);
      self::function_alert($strError);
      exit();
    }
    // Delete all of the Old data
    if (!self::doQuery("TRUNCATE TABLE `trainedleader`")) {
      $strError = "see error log - TRUNCATE TABLE `trainedleader`";
      error_log($strError, 0);
      self::function_alert($strError);
      $RecordsInError = -1;
      exit();
    }

    // Insert new data
    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($row < 10) { // Skip the first row(s), headers.
          $row++;
          continue;
        }

        // Verify the proper array size, should be $Exprire_Date + 1
        if (count($data) != ($colIncomOnline + 1)) {
          $strMsg = "ERROR: UpdateTotals(" . $fileName . ") is incorrect.";
          error_log($strMsg);
          self::function_alert($strMsg);
          exit;
        }

        $Unit = self::formatUnitNumber($data[$colUnit], $data[$colGenderAccepted]);

        // Insert Data
        $sqlLeadersTrainedInsert = "";
        for ($i = 0; $i < count($data); $i++) {
          if ($i == 4)
            $sqlLeadersTrainedInsert = $sqlLeadersTrainedInsert . sprintf("'%s', ", $Unit);
          else
            $sqlLeadersTrainedInsert = $sqlLeadersTrainedInsert . sprintf("'%s', ", addslashes($data[$i]));
        }
        $sqlLeadersTrainedInsert = substr($sqlLeadersTrainedInsert, 0, (strlen($sqlLeadersTrainedInsert) - 2));
        $sqlLeadersTrainedInsert =  $sqlLeadersTrainedInsertSt . $sqlLeadersTrainedInsert . ");";
        //echo $sqlLeadersTrainedInsert."<br />";
        //echo "<br />";
        // Update the database
        if (!mysqli_query(self::getDbConn(), $sqlLeadersTrainedInsert)) {
          $strErr =  "Insert Error: " . $sqlLeadersTrainedInsert . "" . mysqli_error(self::getDbConn());
          error_log($strErr);
          $RecordsInError++;
        } else
          $Inserted++;
        //Reset String
        $sqlLeadersTrainedInsert = "";
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
      self::function_alert($Usermsg);
    } else {
      $Usermsg = "ERROR: Failed to open file, TrainedLeader(" . $fileName . ")";
      error_log($Usermsg);
      self::function_alert($Usermsg);
    }

    return $RecordsInError;
  }
  /******************************************************************************
   * 
   * This funtion will import data from the my.scouting.org website.
   * 11-Sep-2021, BSA added phone number field
   * 
   *****************************************************************************/
  function &Updateypt($fileName)
  {

    $colDistrict     = array(0, "District");
    $colProgram     = array(1, "Program");
    $colUnit      = array(2, "Unit_Number");
    $colGender       = array(3,  "Gender_Accepted");
    $colCharteredOrg   = array(4,  "Chartered_Org_Name");
    $colFirstName     = array(5,  "First_Name");
    $colMiddleName     = array(6,  "Middle_Name");
    $colLastName    = array(7,  "Last_Name");
    $colMemberID     = array(8,  "Member_ID");
    $colPosition     = array(9,  "Position");
    $colStatus       = array(10, "Status");
    $colEffectiveThrough = array(11, "Effective_Through");
    $colYPTCoce     = array(12, "Youth_Protection_Code");
    $colYPTCompleted   = array(13, "Y01_Completed");
    $colYPTExpired     = array(14, "Y01_Expires");
    $colStreetAddress  = array(15, "Street_Address");
    $colCity      = array(16, "City");
    $colState      = array(17, "State");
    $colZip        = array(18, "Zip");
    $colEmail       = array(19, "Email_Address");
    $colPhone       = array(20, "Phone");
    $colRegistrationDate = array(21, "Registration_Date");
    $colExpiryDate       = array(22, "Expiry_Date");
    $colOnlineCourses   = array(23, "Online_Courses");




    $sqlyptInsertSt = "INSERT INTO `ypt`(`$colDistrict[1]`, `$colProgram[1]`, `$colUnit[1]`, `$colGender[1]`, `$colCharteredOrg[1]`, 
    	`$colFirstName[1]`, `$colMiddleName[1]`, `$colLastName[1]`, `$colMemberID[1]`, `$colPosition[1]`, `$colStatus[1]`, 
    	`$colEffectiveThrough[1]`, `$colYPTCoce[1]`, `$colYPTCompleted[1]`, `$colYPTExpired[1]`, 
    	`$colEmail[1]`, `$colPhone[1]`, `$colRegistrationDate[1]`, `$colOnlineCourses[1]`) VALUES (";

    $Inserted = 0;
    $Updated = 0;
    $RecordsInError = 0;
    $Row = 1;
    $filePath = $fileName;
    $Datestr = "";

    if (!file_exists($filePath) || !is_readable($filePath)) {
      error_log("Updateypt: File not found or unreadable at $filePath");
      return ++$RecordsInError;
    }

    // Delete all of the Old data
    if (!self::doQuery("TRUNCATE TABLE `ypt`")) {
      $strError = "TRUNCATE TABLE `ypt`";
      self::function_alert($strError);
    }

    if (($handle = fopen($filePath, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($Row < 10) { // Skip the first row(s), headers.
          if ($Row == 5)
            $Datestr = $data[0]; // Get the report date.
          $Row++;
          continue;
        }
        // Verify the proper array size, should be $Exprire_Date + 1
        if (count($data) != ($colOnlineCourses[0] + 1)) {
          $strMsg = "ERROR: Updateypt(" . $fileName . ") is incorrect.";
          error_log($strMsg);
          self::function_alert($strMsg);
          exit;
        }



        $Unit = self::formatUnitNumber($data[$colProgram[0]] . " " . $data[$colUnit[0]], $data[$colGender[0]]);

        // Insert Data
        $sqlInsertypt = "";
        //for($i=0; $i < count($data); $i++){
        //if($i == 2)
        //$sqlInsertypt += $sqlInsertypt.sprintf("'%s', ", $Unit);
        //else
        $sqlInsertypt = "'" . $data[$colDistrict[0]] . "', "
          . "'" . addslashes($data[$colProgram[0]]) . "', "
          . "'" . addslashes($Unit) . "', "
          . "'" . addslashes($data[$colGender[0]]) . "', "
          . "'" . addslashes($data[$colCharteredOrg[0]]) . "', "
          . "'" . addslashes($data[$colFirstName[0]]) . "', "
          . "'" . addslashes($data[$colMiddleName[0]]) . "', "
          . "'" . addslashes($data[$colLastName[0]]) . "', "
          . "'" . addslashes($data[$colMemberID[0]]) . "', "
          . "'" . addslashes($data[$colPosition[0]]) . "', "
          . "'" . addslashes($data[$colStatus[0]]) . "', "
          . "'" . addslashes($data[$colEffectiveThrough[0]]) . "', "
          . "'" . addslashes($data[$colYPTCoce[0]]) . "', "
          . "'" . addslashes($data[$colYPTCompleted[0]]) . "', "
          . "'" . addslashes($data[$colYPTExpired[0]]) . "', "
          . "'" . addslashes($data[$colEmail[0]]) . "', "
          . "'" . addslashes($data[$colPhone[0]]) . "', "
          . "'" . addslashes($data[$colRegistrationDate[0]]) . "', "
          . "'" . addslashes($data[$colOnlineCourses[0]]) . "'";




        //$sqlInsertypt = $sqlInsertypt.sprintf("'%s', ", addslashes($data[$i]));
        //}
        //$sqlInsertypt = substr($sqlInsertypt, 0, (strlen($sqlInsertypt)-2));
        $sqlInsertypt =  $sqlyptInsertSt . $sqlInsertypt . ");";

        // Update the database
        if (!self::doQuery($sqlInsertypt)) {
          echo "Update Error: " . $sqlInsertypt . "" . mysqli_error(self::getDbConn()) . "<br />";
          $RecordsInError++;
        } else
          $Inserted++;
        //Reset String
        $sqlInsertypt = "";
      }
      fclose($handle);
      $Usermsg = "Records Updated Inserted: " . $Inserted . " Updated: " . $Updated . " Errors: " . $RecordsInError;
      //self::function_alert($Usermsg);
    } else {
      $Usermsg = "Failed to open file";
      //self::function_alert($Usermsg);
    }

?>
        <?php

        return $RecordsInError;
      }
      /******************************************************************************
       * This funtion will 
       *****************************************************************************/
      public static function UpdateFunctionalRole($fileName)
      {
        $RecordsInError = 0;
        $Inserted = 0;
        $Updated = 0;
        $Error = 0;
        $row = 0;
        $reportDate = null;

        // Secure file path using UPLOAD_DIRECTORY
        $filePath = UPLOAD_DIRECTORY . basename($fileName);
        if (!file_exists($filePath) || !is_readable($filePath)) {
          error_log("UpdateFunctionalRole: File not found or unreadable at $filePath");
          return ++$RecordsInError;
        }

        $dbConn = self::getDbConn(); // Assuming a method to get DB connection
        mysqli_begin_transaction($dbConn);

        try {
          if (($handle = fopen($filePath, "r")) !== false) {
            // Read header row to validate structure
            $header = fgetcsv($handle, 1000, ",");
            //            if ($header === false || count($header) < 7) {
            if ($header === false) {
              error_log("UpdateFunctionalRole: Invalid CSV header in $fileName");
              throw new Exception("Invalid CSV format: Missing required columns.");
            }

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
              $row++;
              // Skip empty rows

              // Extract report date from row 5 (configurable if needed)
              if ($row === 5) {
                $reportDate = $data[0] ?? null;
                continue;
              }

              // Skip rows before data (e.g., headers, metadata)
              if ($row < 8) {
                continue;
              }
              if (empty($data) || count($data) < 7) {
                $Error++;
                $RecordsInError++;
                error_log("UpdateFunctionalRole: Skipping empty or invalid row $row in $fileName");
                continue;
              }

              // Sanitize and validate data
              $firstName = trim($data[self::COL_FIRSTNAME] ?? '');
              $lastName = trim($data[self::COL_LASTNAME] ?? '');
              $positionName = trim($data[self::COL_POSITIONNAME] ?? '');
              $unit = self::formatUnitNumber($data[self::COL_DISPLAYNAME] ?? '', null);

              //                if (empty($firstName) || empty($lastName) || empty($unit) || empty($positionName)) {
              if (empty($firstName) || empty($lastName) || empty($positionName)) {
                $Error++;
                $RecordsInError++;
                error_log("UpdateFunctionalRole: Missing required fields in row $row: " . json_encode($data));
                continue;
              }

              // Find matching record
              $sqlFind = "SELECT idx FROM trainedleader WHERE First_Name = ? AND Last_Name = ? AND Unit = ?";
              $stmt = mysqli_prepare($dbConn, $sqlFind);
              if (!$stmt) {
                throw new Exception("Failed to prepare SELECT statement: " . mysqli_error($dbConn));
              }
              mysqli_stmt_bind_param($stmt, "sss", $firstName, $lastName, $unit);
              mysqli_stmt_execute($stmt);
              $result = mysqli_stmt_get_result($stmt);
              $numRows = mysqli_num_rows($result);
              mysqli_stmt_close($stmt);

              if ($numRows === 1) {
                // Update functional role
                $sqlUpdate = "UPDATE  trainedleader SET FunctionalRole = ? WHERE First_Name = ? AND Last_Name = ? AND Unit = ?";
                $stmt = mysqli_prepare($dbConn, $sqlUpdate);
                if (!$stmt) {
                  throw new Exception("Failed to prepare UPDATE statement: " . mysqli_error($dbConn));
                }
                mysqli_stmt_bind_param($stmt, "ssss", $positionName, $firstName, $lastName, $unit);
                if (mysqli_stmt_execute($stmt)) {
                  $Updated++;
                } else {
                  $Error++;
                  $RecordsInError++;
                  error_log("UpdateFunctionalRole: Failed to update row $row: " . mysqli_error($dbConn));
                }
                mysqli_stmt_close($stmt);
              } else if ($unit === null) {
                // Will end up here is the FunctionalRoleAssignementReport has a displayedname is ==
                // to Centennial 02
              } else {
                // Found more than one role for this leader 
                // Should just take the first onme but for now just skip
                //$Error++;
                //$RecordsInError++;
                //error_log("UpdateFunctionalRole: Row $row - Found $numRows matches for FirstName: $firstName, LastName: $lastName, Unit: $unit");
              }
            }
            fclose($handle);
          } else {
            throw new Exception("Failed to open file: $filePath");
          }

          mysqli_commit($dbConn);
        } catch (Exception $e) {
          mysqli_rollback($dbConn);
          error_log("UpdateFunctionalRole: Error processing $fileName: " . $e->getMessage());
          $RecordsInError++;
        }

        // Log summary
        error_log("UpdateFunctionalRole: Processed $fileName - Updated: $Updated, Errors: $Error, Total Rows: $row");

        return $RecordsInError;
      }
      /******************************************************************************
       * This funtion will return trained status for direct contact leaders
       * 
       * $DirectContact must be either a YES or NO
       * 
       *****************************************************************************/
      public static function DirectTrained($Unit, $DirectContact)
      {
        $Direct = array(
          "Trained" => 0,
          "Untrained" => 0
        );

        $sqlDirect = "SELECT * FROM trainedleader WHERE Unit = '$Unit' AND Direct_Contact_Leader = '$DirectContact' ORDER BY Direct_Contact_Leader DESC ";
        $sqlDirectUnTrained = "SELECT * FROM trainedleader WHERE Unit = '$Unit' AND Direct_Contact_Leader = '$DirectContact' AND Trained = 'NO' ";

        //Get Direct contact training status:
        $result = self::doQuery($sqlDirect);
        //TODO: Needs error checking
        $rowcount = mysqli_num_rows($result);
        $result_untrained = self::doQuery($sqlDirectUnTrained);
        //TODO: Needs error checking
        $Direct['Untrained'] = mysqli_num_rows($result_untrained);
        $Direct['Trained'] = $rowcount - $Direct['Untrained'];

        return $Direct;
      }

      /******************************************************************************
       * 
       * This funtion will scouting positions in the district
       * 
       *****************************************************************************/
      public static function GetPositions()
      {
        $sqlPosition = 'SELECT DISTINCT Position FROM ypt ORDER BY Position ASC';

        //Get Position
        $resultposition = self::doQuery($sqlPosition);

        return $resultposition;
      }
    }
