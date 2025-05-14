<?php
if (!session_id()) {
  session_start();
}


require_once 'CEagle.php';
$cEagle = CEagle::getInstance();

// This code stops anyone for seeing this page unless they have logged in and
// they account is enabled.
if (!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php include('head.php'); ?>
</head>

<body>
  <?php include('header.php');

  $arrFiles = array();
  $handle = opendir('./Policy');
  $currentDirectory = getcwd();
  $uploadDirectory = "./Policy/";
  if ($handle) {
    while (($entry = readdir($handle)) !== FALSE) {
      $arrFiles[] = $entry;
    }
    closedir($handle);

    // Now display them..
    echo "</br></br>";
    echo '<ul>';
    for ($i = 2; $i < count($arrFiles); $i++) {
      $uploadPath = $uploadDirectory . basename($arrFiles[$i]);

      echo "<li><a href='$uploadPath'>$arrFiles[$i]</a></li>";
    }
    echo '</ul>';
  }

  ?>
  </div>
  <?php include('Footer.php'); ?>
</body>

</html>