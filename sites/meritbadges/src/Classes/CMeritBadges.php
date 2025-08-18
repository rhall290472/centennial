<?php
defined('IN_APP') or die('Direct access not allowed.');

if (!session_id()) {
  session_start();
}

/**
 * Centennial District Advancement Merit Badge Class
 *
 * Supports management of merit badge counselor data.
 * Copyright 2017-2024 - Richard Hall
 *
 * @author Richard Hall
 * @license Proprietary
 */
load_class(SHARED_PATH . 'src/Classes/cAdultLeaders.php');
$cAdultLeaders = AdultLeaders::getInstance();

/**
 * Get current year from session
 * @return string|null
 */
function getYear()
{
  return $_SESSION['year'] ?? null;
}

/**
 * Set year in session
 * @param string $yr
 */
function setYear($yr)
{
  $_SESSION['year'] = filter_var($yr);
}

/**
 * Singleton class for managing merit badge data
 */
class CMeritBadges
{
  private static $instances = [];
  private static $year;
  private $dbConn;

  protected function __construct() {}

  protected function __clone() {}

  public function __wakeup()
  {
    throw new \Exception("Cannot unserialize a singleton.");
  }

  public static function getInstance()
  {
    $cls = static::class;
    if (!isset(self::$instances[$cls])) {
      self::$instances[$cls] = new static();
    }
    return self::$instances[$cls];
  }

  /**
   * Initialize database connection
   * @return self
   */
  private static function initConnection()
  {
    $db = self::getInstance();
    $db->dbConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->dbConn->connect_error) {
      throw new Exception("Database connection failed: " . $db->dbConn->connect_error);
    }
    $db->dbConn->set_charset('utf8');
    return $db;
  }

  /**
   * Get database connection
   * @return mysqli|null
   */
  public static function getDbConn()
  {
    try {
      $db = self::initConnection();
      return $db->dbConn;
    } catch (Exception $ex) {
      error_log("Database connection error: " . $ex->getMessage());
      return null;
    }
  }

  /**
   * Execute a database query with prepared statements
   * @param string $sql
   * @param array $params
   * @return mysqli_result|bool
   */
  public static function doQuery($sql, $params = [])
  {
    $mysqli = self::getDbConn();
    if (!$mysqli) {
      return false;
    }

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
      error_log("Prepare failed: " . $mysqli->error);
      return false;
    }

    if (!empty($params)) {
      $types = str_repeat('s', count($params));
      $stmt->bind_param($types, ...$params);
    }

    $result = $stmt->execute();
    if (!$result) {
      error_log("Query failed: " . $stmt->error);
      $stmt->close();
      return false;
    }

    $result = $stmt->get_result() ?: true;
    $stmt->close();
    return $result;
  }

  /**
   * Check if a file exists
   * @param string $filePath
   * @return array
   */
  public static function checkFileExists($filePath)
  {
    $cacheKey = 'file_exists_' . md5($filePath);
    if (!isset($_SESSION[$cacheKey])) {
      $absolutePath = realpath(__DIR__ . '/' . $filePath);
      $_SESSION[$cacheKey] = $absolutePath && file_exists($absolutePath);
    }
    return $_SESSION[$cacheKey] ? [
      'href' => $filePath,
      'class' => 'nav-link px-0 text-white-50',
      'valid' => true
    ] : [
      'href' => '#',
      'class' => 'nav-link px-0 text-muted disabled',
      'valid' => false
    ];
  }

  public static function GetYear()
  {
    if (!isset(self::$year)) {
      self::$year = getYear();
    }
    return self::$year;
  }

  public static function SetYear($year)
  {
    self::$year = filter_var($year);
  }

  /**
   * Render year selection dropdown dynamically from database
   */
  public static function SelectYear()
  {
    $years = ['2022', '2021', '2020', '2019', '2018', '2017', '2016', '2015']; // Ideally, fetch from DB
    $currentYear = self::GetYear();
?>
    <form method="post" class="d-flex align-items-center mb-3">
      <label for="Year" class="me-2">Select Year:</label>
      <select class="form-select w-auto" id="Year" name="Year">
        <?php foreach ($years as $year): ?>
          <option value="<?php echo htmlspecialchars($year); ?>" <?php echo $year === $currentYear ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($year); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" name="SubmitYear" class="btn btn-primary ms-2 rounded-pill">Submit</button>
    </form>
  <?php
  }

  /**
   * Display a Bootstrap modal for user messages
   * @param string $msg
   */
  public static function showMessage($msg)
  {
  ?>
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="messageModalLabel">Notification</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php echo htmlspecialchars($msg); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <script>
      var messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
      messageModal.show();
    </script>
  <?php
  }

  public static function GotoURL($url)
  {
    header("Location: " . filter_var($url, FILTER_SANITIZE_URL));
    exit;
  }

  public static function GetLastUpdated($Table)
  {
    $result = self::doQuery("SELECT ? FROM dates", [$Table]);
    if ($result && $row = $result->fetch_assoc()) {
      return $row[$Table];
    }
    return null;
  }

  public static function DropAllBadges()
  {
    $sql = "UPDATE `counselormerit` SET `Status` = 'DROP', `StatusDate` = ? WHERE `Status` <> 'DROP'";
    if (!self::doQuery($sql, [date("d/M/Y")])) {
      self::showMessage("Error updating merit badge statuses.");
      exit;
    }
  }

  public static function SetAllInactive()
  {
    $sql = "UPDATE `counselors` SET `Active` = 'No' WHERE `Active` <> 'No'";
    if (!self::doQuery($sql)) {
      self::showMessage("Error setting counselors inactive.");
      exit;
    }
  }

  public static function formatPhoneNumber($row, $Phone)
  {
    $Phone = $row['HomePhone'] ?? $Phone;
    $phoneNumber = preg_replace('/[^0-9]/', '', $Phone);

    if (strlen($phoneNumber) > 10) {
      $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
      $areaCode = substr($phoneNumber, -10, 3);
      $nextThree = substr($phoneNumber, -7, 3);
      $lastFour = substr($phoneNumber, -4, 4);
      return '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
    } elseif (strlen($phoneNumber) == 10) {
      $areaCode = substr($phoneNumber, 0, 3);
      $nextThree = substr($phoneNumber, 3, 3);
      $lastFour = substr($phoneNumber, 6, 4);
      return '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
    } elseif (strlen($phoneNumber) == 7) {
      $nextThree = substr($phoneNumber, 0, 3);
      $lastFour = substr($phoneNumber, 3, 4);
      return $nextThree . '-' . $lastFour;
    }
    return $phoneNumber;
  }

  public static function formatZipCode($Zip)
  {
    $Zip = preg_replace('/[^0-9]/', '', $Zip);
    if (strlen($Zip) > 5) {
      $ZipCode = substr($Zip, 0, 5);
      $ZpCode4 = substr($Zip, -4, 4);
      $Zip = $ZipCode . '-' . $ZpCode4;
    }
    return $Zip;
  }

  public static function formatEmail($Email)
  {
    $Email = strtolower(filter_var($Email, FILTER_SANITIZE_EMAIL));
    return "<a href='mailto:$Email?subject=Merit Badge Counselor'>$Email</a>";
  }

  public static function left($str, $length)
  {
    return substr($str, 0, $length);
  }

  public static function mid($str, $start, $length)
  {
    return substr($str, $start, $length);
  }

  public static function right($str, $length)
  {
    return substr($str, -$length);
  }

  public static function UpdateCounselors($fileName)
  {
    $filePath = "Data/" . filter_var($fileName);
    $inserted = 0;
    $updated = 0;
    $errors = 0;

    if (($handle = fopen($filePath, "r")) !== false) {
      self::doQuery("UPDATE `counselors` SET `Active` = 'No'");

      $row = 1;
      while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        if ($row++ < 10) continue;
        if (strcmp($data[14], "Merit Badge Counselor")) continue;

        $memberID = filter_var($data[11]);
        if (self::InsertUpdateCheck($memberID)) {
          $sql = "UPDATE `counselors` SET `HomeDistrict` = ?, `FirstName` = ?, `LastName` = ?, `Zip` = ?, 
                            `MemberID` = ?, `Email` = ?, `Trained` = ?, `Active` = ?, `ValidationDate` = ? 
                            WHERE `MemberID` = ?";
          $params = [
            $data[2],
            $data[7],
            $data[9],
            $data[10],
            $data[11],
            $data[13],
            $data[16],
            'YES',
            date("d/M/Y"),
            $data[11]
          ];
          if (!self::doQuery($sql, $params)) {
            $errors++;
          } else {
            if (!empty($data[4])) {
              $sql = "UPDATE `counselors` SET `HomeTroop` = ? WHERE `MemberID` = ?";
              if (!self::doQuery($sql, [$data[4], $data[11]])) {
                $errors++;
              }
            }
            $updated++;
          }
        } else {
          $sql = "INSERT INTO `counselors` (`HomeDistrict`, `HomeTroop`, `FirstName`, `LastName`, `Zip`, 
                            `MemberID`, `Email`, `Trained`, `Active`, `ValidationDate`, `DoNotPublish`) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
          $params = [
            $data[2],
            $data[4],
            $data[7],
            $data[9],
            $data[10],
            $data[11],
            $data[13],
            $data[16],
            'YES',
            date("d/M/Y"),
            'FALSE'
          ];
          if (!self::doQuery($sql, $params)) {
            $errors++;
          } else {
            if (!empty($data[4])) {
              $sql = "UPDATE `counselors` SET `HomeTroop` = ? WHERE `MemberID` = ?";
              if (!self::doQuery($sql, [$data[4], $data[11]])) {
                $errors++;
              }
            }
            $inserted++;
          }
        }
      }
      fclose($handle);
      $msg = "Records Inserted: $inserted, Updated: $updated, Errors: $errors";
      self::showMessage($msg);
    } else {
      self::showMessage("Failed to open file: $filePath");
    }

    if ($errors == 0 && $inserted == 0) {
      self::GotoURL('index.php');
    } else {
      echo '<center><br><button class="btn btn-primary rounded-pill" style="width:220px" onclick="window.location.href=\'index.php\'">Return Main</button><br></center>';
    }
  }

  public static function Updateypt($fileName)
  {
    $filePath = "Data/" . filter_var($fileName);
    $inserted = 0;
    $updated = 0;
    $errors = 0;

    if (($handle = fopen($filePath, "r")) !== false) {
      $row = 1;
      while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        if ($row++ < 9) continue;
        if (strcmp($data[9], "Merit Badge Counselor")) continue;

        $memberID = filter_var($data[8]);
        if (self::InsertUpdateCheck($memberID)) {
          $sql = "UPDATE `counselors` SET `YPT` = ?, `Active` = ?, `ValidationDate` = ? WHERE `MemberID` = ?";
          $params = [$data[14], $data[10], date("d/M/Y"), $memberID];
          if (!self::doQuery($sql, $params)) {
            $errors++;
          } else {
            $updated++;
          }
        } else {
          echo "Error: MemberID = " . htmlspecialchars($memberID) . "<br>";
          $errors++;
        }
      }
      fclose($handle);
      $msg = "Records Inserted: $inserted, Updated: $updated, Errors: $errors";
      self::showMessage($msg);
    } else {
      self::showMessage("Failed to open file: $filePath");
    }

    if ($errors == 0) {
      self::GotoURL('index.php');
    } else {
      echo '<center><br><button class="btn btn-primary rounded-pill" style="width:220px" onclick="window.location.href=\'index.php\'">Return Main</button><br></center>';
    }
  }

  public static function create_progress()
  {
  ?>
    <div class="progress mt-3" style="width: 300px; margin: auto;">
      <div class="progress-bar bg-info" role="progressbar" style="width: 0%;" id="progressBar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>
    <script>
      function updateProgress(percent) {
        const bar = document.getElementById('progressBar');
        bar.style.width = percent + '%';
        bar.textContent = percent + '%';
        bar.setAttribute('aria-valuenow', percent);
      }
    </script>
<?php
  }

  public static function update_progress($percent)
  {
    echo "<script>updateProgress($percent);</script>";
    flush();
  }

  public static function GetFileSize($fileName)
  {
    $fileName = filter_var($fileName);
    try {
      $lineCount = 0;
      $handle = fopen($fileName, 'r');
      if ($handle === false) {
        throw new Exception("Error opening file: $fileName");
      }
      while (!feof($handle)) {
        if (fgets($handle) !== false) {
          $lineCount++;
        }
      }
      fclose($handle);
      return $lineCount;
    } catch (Exception $e) {
      error_log($e->getMessage());
      return 0;
    }
  }

  public static function UpdateCouncilList($uploadPath)
  {
    $colOrganizations = 0;
    $colFirstname = 1;
    $colLastname = 2;
    $colMemberid = 3;
    $colStrexpirydt = 4;
    $colYptstatus = 5;
    $colStryptexpirydt = 6;
    $colTroopnos = 7;
    $colPhone = 8;
    $colEmail = 9;
    $colCstreetaddress = 10;
    $colCcity = 11;
    $colCstatezip = 12;
    $colMbcounciling = 13;
    $colAwards = 14;

    $filePath = "Data/" . filter_var($uploadPath);
    $inserted = 0;
    $updated = 0;
    $errors = 0;
    $addCounselor = 0;

    self::DropAllBadges();
    self::SetAllInactive();

    $numRows = self::GetFileSize($filePath);
    self::create_progress();

    if (($handle = fopen($filePath, "r")) !== false) {
      $row = 1;
      while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        if ($row++ < 10) continue;

        $percent = number_format(($row / $numRows) * 100, 2);
        self::update_progress($percent);

        $unit = AdultLeaders::FindMemberUnit($data);
        $district = filter_var($data[$colOrganizations]);
        $firstName = filter_var($data[$colFirstname]);
        $lastName = filter_var($data[$colLastname]);
        $memberID = filter_var($data[$colMemberid]);
        $ypt = filter_var($data[$colStrexpirydt]);
        $phone = self::right(filter_var($data[$colPhone]), 10);
        $email = filter_var($data[$colEmail], FILTER_SANITIZE_EMAIL);
        $street = filter_var($data[$colCstreetaddress]);
        $city = filter_var($data[11]);
        $stateZip = explode(' ', filter_var($data[$colCstatezip]));
        $state = $stateZip[0] ?? '';
        $zipCode = str_replace("-", '', $stateZip[1] ?? '');
        $numBadges = filter_var($data[$colMbcounciling], FILTER_SANITIZE_NUMBER_INT);

        if ($numBadges == 0) {
          if (!self::MarkCounselorNotActive($memberID)) {
            $errors++;
            error_log("Error marking counselor inactive: $firstName $lastName $memberID");
          }
        }

        $i = 0;
        $badges = array_filter(explode(",", $data[$colAwards]));
        foreach ($badges as $badge) {
          $meritBadge = self::FixMeritBadgeName(trim($badge));

          if (!self::InsertUpdateMeritBadge($firstName, $lastName, $meritBadge)) {
            if ($i == 0) {
              $sql = "INSERT INTO `counselors` (`LastName`, `FirstName`, `HomePhone`, `Email`, 
                                    `MemberID`, `ValidationDate`, `HomeDistrict`, `NumOfBadges`, `Address`, 
                                    `City`, `State`, `Zip`, `YPT`, `Unit1`, `Unit2`, `Active`) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
              $params = [
                $lastName,
                $firstName,
                $phone,
                $email,
                $memberID,
                date("d/M/Y"),
                $district,
                $numBadges,
                $street,
                $city,
                $state,
                $zipCode,
                $ypt,
                $unit[0],
                $unit[1],
                "Yes"
              ];
              if (!self::doQuery($sql, $params)) {
                $errors++;
                error_log("Error inserting counselor: $sql");
              } else {
                $addCounselor++;
              }
            }

            $sql = "INSERT INTO `counselormerit` (`LastName`, `FirstName`, `MeritName`, `Status`, `StatusDate`) 
                                VALUES (?, ?, ?, 'ADD', ?)";
            $params = [$lastName, $firstName, $meritBadge, date("d/M/Y")];
            if (!self::doQuery($sql, $params)) {
              $errors++;
              error_log("Error inserting merit badge: $sql");
            } else {
              $inserted++;
            }
          } else {
            $sql = "UPDATE `counselormerit` SET `LastName` = ?, `FirstName` = ?, `MeritName` = ?, 
                                `Status` = 'UPDATED', `StatusDate` = ? WHERE `LastName` = ? AND `FirstName` = ? AND `MeritName` = ?";
            $params = [$lastName, $firstName, $meritBadge, date("d/M/Y"), $lastName, $firstName, $meritBadge];
            if (!self::doQuery($sql, $params)) {
              $errors++;
              error_log("Error updating merit badge: $sql");
            } else {
              $updated++;
            }

            if ($i == 0) {
              $sql = "UPDATE `counselors` SET `ValidationDate` = ?, `YPT` = ?, `HomePhone` = ?, `Email` = ?, 
                                    `Active` = ?, `NumOfBadges` = ?, `Address` = ?, `City` = ?, `State` = ?, `Zip` = ?, 
                                    `Unit1` = ?, `Unit2` = ? WHERE `LastName` = ? AND `FirstName` = ?";
              $params = [
                date("d/M/Y"),
                $ypt,
                $phone,
                $email,
                "Yes",
                $numBadges,
                $street,
                $city,
                $state,
                $zipCode,
                (isset($unit[0]))? $unit[0]: "NA",
                (isset($unit[1]))? $unit[1]: "NA",
                $lastName,
                $firstName
              ];
              if (!self::doQuery($sql, $params)) {
                $errors++;
                error_log("Error updating counselor: $sql");
              }
            }
          }
          $i++;
        }
      }
      fclose($handle);
      $msg = "Records Inserted: $inserted, Updated: $updated, Errors: $errors";
      self::showMessage($msg);

      if ($errors == 0 && $addCounselor == 0) {
        self::GotoURL('index.php');
      } else {
        echo '<center><br><button class="btn btn-primary rounded-pill" style="width:220px" onclick="window.location.href=\'index.php\'">Return Main</button><br></center>';
      }
    } else {
      self::showMessage("Failed to open file: $filePath");
    }
  }

  public static function InsertUpdateCheck($id)
  {
    $sql = "SELECT 1 FROM `counselors` WHERE `MemberID` = ?";
    $result = self::doQuery($sql, [$id]);
    return $result && $result->num_rows > 0;
  }

  public static function InsertUpdateMeritBadge($firstName, $lastName, $meritBadge)
  {
    $sql = "SELECT 1 FROM `counselormerit` WHERE `FirstName` = ? AND `LastName` = ? AND `MeritName` = ?";
    $result = self::doQuery($sql, [$firstName, $lastName, $meritBadge]);
    return $result && $result->num_rows > 0;
  }

  public static function MarkCounselorNotActive($memberID)
  {
    $sql = "UPDATE `counselors` SET `Active` = 'No', `ValidationDate` = ? WHERE `MemberID` = ?";
    return self::doQuery($sql, [date("d/M/Y"), $memberID]);
  }

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
      "Motorboating" => "Motorboating"
    ];
    return $map[$badge] ?? $badge;
  }
}
?>
