<?php
if (!session_id()) {
  session_start();
}
require_once 'CDistrictAwards.php';
$cDistrictAwards = cDistrictAwards::getInstance();
?>


<head>
  <?php include("header.php"); ?>
  <meta name="description" content="FileUpload.php">

</head>
<?php
$currentDirectory = getcwd();
$uploadDirectory = "/Data/";

$errors = []; // Store errors here

$fileExtensionsAllowed = ['csv']; // These will be the only file extensions allowed 

$fileName = $_FILES['the_file']['name'];
$fileSize = $_FILES['the_file']['size'];
$fileTmpName  = $_FILES['the_file']['tmp_name'];
$fileType = $_FILES['the_file']['type'];
$filedot = '.';
$fileExtension = strtolower(end(explode($filedot, $fileName)));

$uploadPath = $currentDirectory . $uploadDirectory . basename($fileName);

if (isset($_POST['submit'])) {

  if (!in_array($fileExtension, $fileExtensionsAllowed)) {
    $errors[] = "This file extension is not allowed. Please upload a CSV file";
  }

  if ($fileSize > 4000000) {
    $errors[] = "File exceeds maximum size (4MB)";
  }

  if (empty($errors)) {
    $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

    if ($didUpload) {
      /* echo "The file " . basename($fileName) . " has been uploaded<br/>"; */
      $Update = $_POST['submit'];
      switch ($Update) {
        case "ImportIDs":
          $RecordsInError = $cDistrictAwards->ImportIDs($fileName);
          break;
        default:
          echo "Default case reached";
          break;
      }
    } else {
      echo "An error occurred. Please contact the administrator.";
    }
  } else {
    foreach ($errors as $error) {
      echo $error . "These are the errors" . "\n";
    }
  }

  if ($RecordsInError == 0) {
    $cDistrictAwards->GotoURL('Import.php');
  }
}
