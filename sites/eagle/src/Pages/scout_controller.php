<?php
require_once __DIR__ . '/../Classes/CEagle.php';
require_once __DIR__ . '/../../../../shared/src/classes/cAdultLeaders.php';

if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}

$cEagle = CEagle::getInstance();
$cLeaders = AdultLeaders::getInstance();

// Handle form submission
if (isset($_POST['SubmitForm']) && $_POST['SubmitForm'] !== 'Cancel') {
  $SelectedScout = filter_var($_POST['Scoutid'], FILTER_VALIDATE_INT);
  if ($SelectedScout === false) {
    header("Location: error.php?msg=Invalid+scout+ID");
    exit;
  }

  // Fetch scout data
  $queryScout = "SELECT * FROM `scouts` WHERE Scoutid = ?";
  $stmt = $cEagle->prepare($queryScout);
  $stmt->bind_param("i", $SelectedScout);
  $stmt->execute();
  $Scout = $stmt->get_result();
  $rowScout = $Scout->fetch_assoc();

  if (!$rowScout) {
    header("Location: error.php?msg=Scout+not+found");
    exit;
  }

  // Process form data (sanitize and validate)
  $FormData = [
    'Scoutid' => $rowScout['Scoutid'],
    'FirstName' => filter_var($cEagle->GetFormData('element_1_1')),
    // ... other fields
  ];

  if ($cEagle->UpdateScoutRecord($FormData)) {
    $cEagle->CreateAudit($rowScout, $FormData, 'Scoutid');
  }
}

// Fetch active scouts for dropdown
$queryScouts = "SELECT DISTINCTROW LastName, MiddleName, FirstName, Scoutid 
                FROM scouts 
                WHERE (`Eagled` IS NULL OR `Eagled` = '0') 
                AND (`AgedOut` IS NULL OR `AgedOut` = '0') 
                AND (`is_deleted` IS NULL OR `is_deleted` = '0') 
                ORDER BY LastName, FirstName";
$result_ByScout = $cEagle->doQuery($queryScouts);

include __DIR__ . '/views/scout_form.php';
