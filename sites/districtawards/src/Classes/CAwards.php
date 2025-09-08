<?php


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
class cAwards extends CDistrictAwards
{
  /**
   * The Singleton's instance is stored in a static field. This field is an
   * array, because we'll allow our Singleton to have subclasses. Each item in
   * this array will be an instance of a specific Singleton's subclass. You'll
   * see how this works in a moment.
   */
  private static $instances = [];
  private static $year;
  private static $nominee;

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
    $connConf = getConfigData();
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
      $strError = "Error: doQuery(" . $sql . "), unable to execute query. " . $ex->getMessage();
      error_log($strError, 0);
      $Result = null;
    }
    if (!$Result)
      error_log("Error: doQuery(" . $sql . ") " . __FILE__ . ", " . __LINE__);
    return $Result;
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
   *****************************************************************************/
  function function_alert($msg)
  {
    echo "<script type='text/javascript'>alert('$msg');</script>";
  }
  /*=============================================================================
     *
     * This function will return list of Nominees
     * 
     *===========================================================================*/
  public static function DistrictAwardofMerit()
  {
    $htmlMessage = file_get_contents("./Awards/DistrictAwardofMerit.html");
    if (!$htmlMessage) {
      $msg = "Error: DistrictAwardpofMerit() - file_get_contents";
      self::function_alert($msg);
      self::GotoURL(("index.php"));
      exit;
    }
    return $htmlMessage;
    //self::NomineeForm(self::$DistrictAwardofMerit);
    // Now go get Nominee Information
  }
  /*=============================================================================
     *
     * This function will return list of Nominees
     * 
     *===========================================================================*/
  public static function OustandingLeader()
  {
    $htmlMessage = file_get_contents("./Awards/OutstandingLeader.html");
    if (!$htmlMessage) {
      $msg = "Error: OutstandingLeader() - file_get_contents";
      self::function_alert($msg);
      self::GotoURL(("index.php"));
      exit;
    }
    return $htmlMessage;
    //self::NomineeForm(0);
    // Now go get Nominee Information
  }
  /*=============================================================================
     *
     * This function will return list of Nominees
     * 
     *===========================================================================*/
  public static function KeyScouter()
  {
    $htmlMessage = file_get_contents("./Awards/KeyScouter.html");
    if (!$htmlMessage) {
      $msg = "Error: KeyScouter() - file_get_contents";
      self::function_alert($msg);
      self::GotoURL(("index.php"));
      exit;
    }
    return $htmlMessage;
  }
  /*=============================================================================
     *
     * This function will return list of Nominees
     * 
     *===========================================================================*/
  public static function AwardNomination($AwardIDX)
  {
    switch ($AwardIDX) {
      case 1:         // District Award of Merit
        $FileName = BASE_PATH . '/src/Awards/DistrictAwardofMerit.html';
        break;
      case 14:        // Outstanding Leader
        $FileName = BASE_PATH . '/src/Awards/OutstandingLeader.html';
        break;
      case 15:        // Key Scouter
        $FileName = BASE_PATH . '/src/Awards/KeyScouter.html';
        break;
      case 2:        // Scoutmaster of the Year
        $FileName = BASE_PATH . '/src/Awards/Scoutmaster.html';
        break;
      case 3:        // Rookie Scoutmaster of the Year
        $FileName = BASE_PATH . '/src/Awards/RookieScoutmaster.html';
        break;
      case 4:        // Cubtmaster of the Year
        $FileName = BASE_PATH . '/src/Awards/Cubmaster.html';
        break;
      case 5:        // Rookie Cubmaster of the Year
        $FileName = BASE_PATH . '/src/Awards/RookieCubmaster.html';
        break;
      case 12:
        $FileName = BASE_PATH . '/src/Awards/DenLeader.html';
        break;
      case 13:
        $FileName = BASE_PATH . '/src/Awards/RookieDenLeader.html';
        break;
      case 6:        // Crew Advisor of the Year
        $FileName = BASE_PATH . '/src/Awards/CrewAdvisor.html';
        break;
      case 7:        // Rookie Crew Advisor of the Year
        $FileName = BASE_PATH . '/src/Awards/RookieCewAdvisor.html';
        break;
      case 48:
        $FileName = BASE_PATH . '/src/Awards/Skipper.html';
        break;
      case 49:
        $FileName = BASE_PATH . '/src/Awards/RookieSkipper.html';
        break;
      case 20:
        $FileName = BASE_PATH . '/src/Awards/PackCM.html';
        break;
      case 8:
        $FileName = BASE_PATH . '/src/Awards/TroopCM.html';
        break;
      case 9:
        $FileName = BASE_PATH . '/src/Awards/RookieTroopCM.html';
        break;
      case 22: // Pack Rookie Committee Member
        $FileName = BASE_PATH . '/src/Awards/RookiePackCM.html';
        break;
      case 21:
        $FileName = BASE_PATH . '/src/Awards/TroopCM.html';
        break;
      case 23:
        $FileName = BASE_PATH . ' / src/Awards/RookieTroopCM.html';
        break;
      case 36:
        $FileName = BASE_PATH . '/src/Awards/CrewSkipperCM.html.html';
        break;
      case 49:
        $FileName = BASE_PATH . '/src/Awards/CrewSkipperCM.html';
        break;
      case 50:
        $FileName = BASE_PATH . ' / src/Awards/RookieCrewSkipperCM.html';
        break;
      case 18:
        $FileName = BASE_PATH . '/src/Awards/Commissioner.html';
        break;
      case 19:
        $FileName = BASE_PATH . '/src/Awards/RookieCommissioner.html';
        break;
      case 25:
        $FileName = BASE_PATH . '/src/Awards/DistrictCM.html';
        break;
      case 16:
        $FileName = BASE_PATH . '/src/Awards/BaldEagle.html';
        break;
      case 29:
        $FileName = BASE_PATH . '/src/Awards/JuniorLeader.html';
        break;
      case 31:
        $FileName = BASE_PATH . '/src/Awards/PackCC.html';
        break;
      case 34:
        $FileName = BASE_PATH . ' / src/Awards/RookiePackCC.html';
        break;
      case 17:
        $FileName = BASE_PATH . '/src/Awards/FriendsofScouting.html';
        break;
      case 30:
        $FileName = BASE_PATH . '/src/Awards/RookieCrewSkipperCM.html';
        break;
      default:
        $error_msg = " AwardNomination(" . $AwardIDX . ")Default case reached with an award IDX of " . $AwardIDX . " in " . __FILE__ . "," . __LINE__;
        error_log($error_msg, 0);
        $htmlMessage = $error_msg;
        return $htmlMessage;
    }

    if (!file_exists($FileName)) {
      $strError = "ERROR: File not found: " . $FileName . ' - ' . $AwardIDX;
      error_log($strError, 0);
      $_SESSION['feedback'] = ['type' => 'danger', 'message' => $strError];
      //header("Location: index.php?page=$page");
      //exit;
      //self::function_alert($strError);
      //exit();
    }

    $htmlMessage = file_get_contents($FileName);
    if (!$htmlMessage) {
      $msg = "Error: AwardNomination() - file_get_contents  - " . $FileName . " " . __FILE__ . ", " . __LINE__;
      error_log($msg);
      self::function_alert($msg);
      self::GotoURL("index.php");
      exit;
    }
    return $htmlMessage;
  }

  /*=============================================================================
     *
     * This function will handle the on-line nomination. And save the nomination 
     * in the database.
     * 
     *===========================================================================*/
  public static function NomineeForm($AwardForm)
  {
    $rowNominee = NULL;

    //#####################################################
    //
    // Check to see if user as Submitted the form. If so, save the data..
    //
    //#####################################################
    if (isset($_POST['SubmitForm'])) {
      if ($_POST['SubmitForm'] == "Cancel") {
        self::GotoURL("./OnLineNomination.php");
        exit();
      }

      // Save New data..From the user form
      $FormData = array();
      $FormData['NomineeIDX'] = -1; // New recorded
      $FormData['FirstName'] =  parent::GetFormData('element_1_1');
      $FormData['PName'] =  parent::GetFormData('element_1_2');
      $FormData['MName'] =  parent::GetFormData('element_1_3');
      $FormData['LastName'] = parent::GetFormData('element_1_4');
      if ($AwardForm == parent::$OutStandingLeaders) {
        //TODO: Will need to create up to four Nominees..
      }
      $FormData['Year'] =  parent::GetYear();
      $FormData['Award'] =  $AwardForm;
      $FormData['Status'] =  2;   // Nominated
      $FormData['IsDeleted'] = 0;

      $FormData['Position'] =  parent::GetFormData('element_6_1');
      $FormData['Unit'] =  parent::GetFormData('element_6_2');
      $FormData['MemberID'] =  parent::GetFormData('element_6_3');


      $FormData['Notes'] =  parent::GetFormData('element_14_1');

      $FormData['NominatedBy'] =  parent::GetFormData('element_15_1');
      $FormData['NomiantedByUnit'] =  parent::GetFormData('element_15_2');
      $FormData['NominatedByPosition'] =  parent::GetFormData('element_15_3');

      // TODO: create a AddNomineeRecord function and change below
      if (parent::UpdateNomineeRecord($FormData)) {
        // Record has been updated in database now create a audit trail
        parent::CreateAudit($rowNominee, $FormData, 'NomineeIDX');
      }
      parent::GotoURL('OnLineNomination.php');
    }
?>
    <div class="form-nominee">
      <p style="text-align:Left"><b>Nominee Information</b></p>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" s id="add_nominee" method="post">

        <div class="form-row">
          <div class="col-3">
            <input type="text" name="element_1_1" class="form-control" placeholder="First Name">
          </div>
          <div class="col-3">
            <input type="text" name="element_1_2" class="form-control" placeholder="Preferred Name">
          </div>
          <div class="col">
            <input type="text" name="element_1_3" class="form-control" placeholder="Middle">
          </div>
          <div class="col">
            <input type="text" name="element_1_4" class="form-control" placeholder="Last">
          </div>
        </div>

        <?php if ($AwardForm == parent::$OutStandingLeaders) { ?>
          <div class="form-row">
            <div class="col-3">
              <input type="text" name="element_2_1" class="form-control" placeholder="First Name">
            </div>
            <div class="col-3">
              <input type="text" name="element_2_2" class="form-control" placeholder="Preferred Name">
            </div>
            <div class="col">
              <input type="text" name="element_2_3" class="form-control" placeholder="Middle">
            </div>
            <div class="col">
              <input type="text" name="element_2_4" class="form-control" placeholder="Last">
            </div>
          </div>

          <div class="form-row">
            <div class="col-3">
              <input type="text" name="element_3_1" class="form-control" placeholder="First Name">
            </div>
            <div class="col-3">
              <input type="text" name="element_3_2" class="form-control" placeholder="Preferred Name">
            </div>
            <div class="col">
              <input type="text" name="element_3_3" class="form-control" placeholder="Middle">
            </div>
            <div class="col">
              <input type="text" name="element_3_4" class="form-control" placeholder="Last">
            </div>
          </div>

          <div class="form-row">
            <div class="col-3">
              <input type="text" name="element_4_1" class="form-control" placeholder="First Name">
            </div>
            <div class="col-3">
              <input type="text" name="element_4_2" class="form-control" placeholder="Preferred Name">
            </div>
            <div class="col">
              <input type="text" name="element_4_3" class="form-control" placeholder="Middle">
            </div>
            <div class="col">
              <input type="text" name="element_4_4" class="form-control" placeholder="Last">
            </div>
          </div>

        <?php } ?>


        <div class="form-row">
          <?php if ($AwardForm != parent::$OutStandingLeaders) { ?>
            <div class="col-3">
              <input type="text" name="element_6_1" class="form-control" placeholder="Currently registered in Scouting as:">
            </div>
          <?php } ?>
          <div class="col-4">
            <input type="text" name="element_6_2" class="form-control" placeholder="Unit Type & Number i.e Troop 0317-BT">
          </div>
          <?php if ($AwardForm != self::$OutStandingLeaders) { ?>
            <div class="col-3">
              <input type="text" name="element_6_3" class="form-control" placeholder="BSA ID if know">
            </div>
          <?php } ?>
        </div>



        <?php if ($AwardForm == parent::$DistrictAwardofMerit) { ?>
          <p style="text-align:Left"><b>The nominee has earned the following (provide dates):</b></p>
          <div class="form-row">
            <div class="col-4">
              <input type="text" name="element_7_1" class="form-control" placeholder="Den Leader’s Training Award or Den Leader Award">
            </div>
            <div class="col-4">
              <input type="text" name="element_7_2" class="form-control" placeholder="Scouter’s Religious Award:">
            </div>
            <div class="col-4">
              <input type="text" name="element_7_3" class="form-control" placeholder="Other (specify)">
            </div>
          </div>

          <div class="form-row">
            <div class="col-4">
              <input type="text" name="element_8_1" class="form-control" placeholder="Den Leader Coach’s Training Award/Coach Award">
            </div>
            <div class="col-4">
              <input type="text" name="element_8_2" class="form-control" placeholder="Silver Beaver">
            </div>
            <div class="col-4">
              <input type="text" name="element_8_3" class="form-control" placeholder="Other (specify)">
            </div>
          </div>

          <div class="form-row">
            <div class="col-4">
              <input type="text" name="element_9_1" class="form-control" placeholder="Cubmaster Award">
            </div>
            <div class="col-4">
              <input type="text" name="element_9_2" class="form-control" placeholder="Order of the Arrow">
            </div>
            <div class="col-4">
              <input type="text" name="element_9_3" class="form-control" placeholder="Other (specify)">
            </div>
          </div>

          <div class="form-row">
            <div class="col-4">
              <input type="text" name="element_10_1" class="form-control" placeholder="Cub Scouter Award">
            </div>
            <div class="col-4">
              <input type="text" name="element_10_2" class="form-control" placeholder="Wood Badge">
            </div>
            <div class="col-4">
              <input type="text" name="element_10_3" class="form-control" placeholder="Other (specify)">
            </div>
          </div>

          <div class="form-row">
            <div class="col-4">
              <input type="text" name="element_11_1" class="form-control" placeholder="Webelos Den Leader Award">
            </div>
            <div class="col-4">
              <input type="text" name="element_11_2" class="form-control" placeholder="Venturing Awards">
            </div>
            <div class="col-4">
              <input type="text" name="element_11_3" class="form-control" placeholder="Other (specify)">
            </div>
          </div>

          <div class="form-row">
            <div class="col-4">
              <input type="text" name="element_12_1" class="form-control" placeholder="Scouter’s Training Award">
            </div>
            <div class="col-4">
              <input type="text" name="element_12_2" class="form-control" placeholder="Distinguished Commissioner Service Award">
            </div>
            <div class="col-4">
              <input type="text" name="element_12_3" class="form-control" placeholder="Other (specify)">
            </div>
          </div>

          <div class="form-row">
            <div class="col-4">
              <input type="text" name="element_13_1" class="form-control" placeholder="Scouter’s Key">
            </div>
            <div class="col-4">
              <input type="text" name="element_13_2" class="form-control" placeholder="Other (specify)">
            </div>
            <div class="col-4">
              <input type="text" name="element_13_3" class="form-control" placeholder="Other (specify)">
            </div>
          </div>
        <?php } ?>

        </br>
        <p style="text-align:Left"><b>The noteworthy service upon which this nomination is based follows:</b></p>
        <p>(Furnish as much information as possible. For example: president, Rotary Club; vestryman, St. Paul’s Church; chairman, Red
          Cross campaign; vice-president, PTA; medical director, hospital; Cubmaster, 3 years; Scoutmaster, 4 years; Venturing Advisor,
          3 years; commissioner, etc.)</p>

        <div class="form-row">
          <div class="col">
            <textarea name="element_14_1" class="form-control" id="Notes" rows="10" placeholder="Notes"></textarea>
          </div>
        </div>

        <div class="form-row">
          <div class="col-5">
            <input type="text" name="element_15_1" class="form-control" placeholder="Your Name">
          </div>
          <div class="col-3">
            <input type="text" name="element_15_2" class="form-control" placeholder="Unit Type & Number">
          </div>
          <div class="col-3">
            <input type="text" name="element_15_3" class="form-control" placeholder="Your Scouting Position">
          </div>
        </div>


        <?php
        $ID = -1;   // New record
        echo '<input type="hidden" name="NomineeIDX" value="' . $ID . '"/>';
        ?>
        <input id="saveForm2" class="btn btn-primary btn-lg" type="submit" name="SubmitForm" value="Save" />
        <input id="saveForm2" class="btn btn-primary btn-lg" type="submit" name="SubmitForm" value="Cancel" />

      </form>
    </div>
  <?php
  }

  /*=============================================================================
     *
     * This function will handle the on-line nomination for the District
     * Award of Merit.
     * 
     *===========================================================================*/
  public static function old_NomineeForm($AwardForm)
  {
  ?>
    <div class="form-nominee">
      <p style="text-align:Left"><b>Nominee Information</b></p>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" s id="add_nominee" method="post">
        <ul>

          <li id=Line1>
            <span>
              <label>First</label>
              <input id="element_1_1" name="element_1_1" />
            </span>
            <span>
              <label>Preferred Name</label>
              <input id="element_1_2" name="element_1_2" />
            </span>
            <span>
              <label>Middle</label>
              <input id="element_1_3" name="element_1_3" />
            </span>
            <span>
              <label>Last</label>
              <input id="element_1_4" name="element_1_4" />
            </span>
          </li>

          <li id=Line2>
            <span>
              <label>Address</label>
              <input id="element_2_1" name="element_2_1" style="width:45vw;" />
            </span>
          </li>

          <li id=Line3>
            <span>
              <label>City/Town</label>
              <input id="element_2_1" name="element_2_1" style="width:25vw;" />
            </span>

            <span>
              <label>Zip</label>
              <input id="element_2_1" name="element_2_1" />
            </span>

          <li id=Line4>
            <span>
              <label>Currently registered in Scouting as:</label>
              <input id="element_3_1" name="element_3_1" />
            </span>
            <span>
              <label>Currently Unit:</label>
              <input id="element_3_2" name="element_3_2" />
            </span>
          </li>

          <?php if ($AwardForm == parent::$DistrictAwardofMerit) { ?>
            <p style="text-align:Left"><b>The nominee has earned the following (provide dates):</b></p>
            <li id=Line5>
              <span>
                <label>Den Leader’s Training Award or Den Leader Award:</label>
                <input id="element_5_1" name="element_5_1" />
              </span>
              <span>
                <label>Scouter’s Religious Award:</label>
                <input id="element_5_2" name="element_5_2" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_5_3" name="element_5_3" />
              </span>
            </li>

            <li id=Line6>
              <span>
                <label>Den Leader Coach’s Training Award/Coach Award:</label>
                <input id="element_6_1" name="element_6_1" />
              </span>
              <span>
                <label>Silver Beaver:</label>
                <input id="element_6_2" name="element_6_2" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_6_3" name="element_6_3" />
              </span>
            </li>

            <li id=Line7>
              <span>
                <label>Cubmaster Award:</label>
                <input id="element_7_1" name="element_7_1" />
              </span>
              <span>
                <label>Order of the Arrow:</label>
                <input id="element_7_2" name="element_7_2" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_7_3" name="element_7_3" />
              </span>
            </li>

            <li id=Line8>
              <span>
                <label>Cub Scouter Award:</label>
                <input id="element_8_1" name="element_8_1" />
              </span>
              <span>
                <label> Wood Badge:</label>
                <input id="element_8_2" name="element_8_2" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_8_3" name="element_8_3" />
              </span>
            </li>

            <li id=Line9>
              <span>
                <label>Webelos Den Leader Award:</label>
                <input id="element_9_1" name="element_9_1" />
              </span>
              <span>
                <label>Venturing Awards:</label>
                <input id="element_9_2" name="element_9_2" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_9_3" name="element_9_3" />
              </span>
            </li>

            <li id=Line10>
              <span>
                <label>Scouter’s Training Award:</label>
                <input id="element_10_1" name="element_10_1" />
              </span>
              <span>
                <label>Distinguished Commissioner Service Award:</label>
                <input id="element_10_2" name="element_10_2" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_10_3" name="element_10_3" />
              </span>
            </li>

            <li id=Line10>
              <span>
                <label>Scouter’s Key:</label>
                <input id="element_11_1" name="element_11_1" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_11_2" name="element_11_2" />
              </span>
              <span>
                <label>Other (specify):</label>
                <input id="element_11_3" name="element_11_3" />
              </span>
            </li>

          <?php } ?>

          </br>
          <p style="text-align:Left"><b>The noteworthy service upon which this nomination is based follows:</b></p>
          <p>(Furnish as much information as possible. For example: president, Rotary Club; vestryman, St. Paul’s Church; chairman, Red
            Cross campaign; vice-president, PTA; medical director, hospital; Cubmaster, 3 years; Scoutmaster, 4 years; Venturing Advisor,
            3 years; commissioner, etc.)</p>
          <li id=Line5>
            <span>
              <textarea rows="10" id="Notes" name="Notes"></textarea>
              <span>
          </li>

          <li id=Line11>
            <span>
              <label>Name of Person Making Nomination:</label>
              <input id="element_11_1" name="element_11_1" />
            </span>
            <span>
              <label>Unit Type & Number:</label>
              <input id="element_11_2" name="element_11_2" />
            </span>
            <span>
              <label>Your Scouting Position:</label>
              <input id="element_11_3" name="element_11_3" />
            </span>
          </li>


          <li id="li_6" class="buttons">
            <?php //$ID = $rowNominee['NomineeIDX']; 
            ?>
            <?php //echo '<input type="hidden" name="NomineeIDX" value="' . $rowNominee['NomineeIDX'] . '"/>'; 
            ?>
            <input id="saveForm2" class="rounded" type="submit" name="SubmitForm" value="Save" />
            <a class="btn btn-primary btn-lg" href="./index.php">Cancel</a>
          </li>
        </ul>
      </form>
    </div>
  <?php
  }
  /*=============================================================================
     *
     * This function will gather the Nomination data for the Leader awards
     * 
     *===========================================================================*/
  public static function NomineeLeaderForm()
  {
  ?>
    <div class="form-nominee">
      <p style="text-align:Left"><b>Nominee Information</b></p>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" s id="add_nominee" method="post">
        <ul>

          <li id=Line1>
            <span>
              <label>First</label>
              <input id="element_1_1" name="element_1_1" />
            </span>
            <span>
              <label>Preferred Name</label>
              <input id="element_1_2" name="element_1_2" />
            </span>
            <span>
              <label>Middle</label>
              <input id="element_1_3" name="element_1_3" />
            </span>
            <span>
              <label>Last</label>
              <input id="element_1_4" name="element_1_4" />
            </span>
          </li>

          <li id=Line2>
            <span>
              <label>Address</label>
              <input id="element_2_1" name="element_2_1" style="width:45vw;" />
            </span>
          </li>

          <li id=Line3>
            <span>
              <label>City/Town</label>
              <input id="element_2_1" name="element_2_1" style="width:25vw;" />
            </span>

            <span>
              <label>Zip</label>
              <input id="element_2_1" name="element_2_1" />
            </span>

          <li id=Line4>
            <span>
              <label>Currently registered in Scouting as:</label>
              <input id="element_4_1" name="element_4_1" />
            </span>
            <span>
              <label>Unit Type & Number:</label>
              <input id="element_4_2" name="element_4_2" />
            </span>
          </li>

          </br>
          <p style="text-align:Left"><b>The District Awards Committee is interested in the individual’s efforts that merit
              worthiness of the award for which he/she is nominated. Previous awards received are
              NOT criteria for the nominee’s selection. Be sure to state the candidate’s noteworthy
              service both inside and outside of Scouting.:</b></p>
          <li id=Line5>
            <span>
              <textarea rows="10" id="Notes" name="Notes"></textarea>
              <span>
          </li>

          <li id=Line6>
            <span>
              <label>Name of Person Making Nomination:</label>
              <input id="element_4_1" name="element_4_1" />
            </span>
            <span>
              <label>Unit Type & Number:</label>
              <input id="element_4_2" name="element_4_2" />
            </span>
            <span>
              <label>Your Scouting Position:</label>
              <input id="element_4_2" name="element_4_2" />
            </span>
          </li>

          <li id="li_8" class="buttons">
            <center>
              <?php //$ID = $rowNominee['NomineeIDX']; 
              ?>
              <?php //echo '<input type="hidden" name="NomineeIDX" value="' . $rowNominee['NomineeIDX'] . '"/>'; 
              ?>
              <input id="saveForm2" class="rounded" type="submit" name="SubmitForm" value="Save" />
              <input id="saveForm2" class="rounded" type="submit" name="SubmitForm" value="Cancel" />
            </center>
          </li>
        </ul>
      </form>
    </div>
<?php
  }
}
