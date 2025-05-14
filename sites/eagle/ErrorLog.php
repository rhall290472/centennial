<?php
if (!session_id()) {
  session_start();
}

require_once 'CEagle.php';
$cEagle = CEagle::getInstance();

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
  <?php include_once('header.php'); ?>
  <div>
    <p>Below is a list of recorded errors found.
    </p>
  </div>
  <?php
  $errorlog = file_get_contents('https://centennialdistrict.co/Eagle/php_errors.log');

  if ($errorlog) {
    echo nl2br($errorlog);
  }
  ?>
  </div>

  <?php include('Footer.php'); ?>
</body>

</html>