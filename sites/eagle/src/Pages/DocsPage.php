<?php
/*
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */
load_class(BASE_PATH . '/src/Classes/CEagle.php');
$cEagle = CEagle::getInstance();

if (!session_id()) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("HTTP/1.0 403 Forbidden");
  exit;
}

if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$uploadDirectory = BASE_PATH . '/public/Policy/';
$arrFiles = [];

if (is_dir($uploadDirectory) && is_readable($uploadDirectory)) {
  $handle = opendir($uploadDirectory);
  while (($entry = readdir($handle)) !== false) {
    if ($entry !== '.' && $entry !== '..' && is_file($uploadDirectory . $entry)) {
      $arrFiles[] = $entry;
    }
  }
  closedir($handle);
} else {
  echo $uploadDirectory;
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Policy directory is inaccessible.'];
}
?>

<main class="main-content">
  <div class="container-fluid mt-5 pt-3">
    <!-- Display Feedback -->
    <?php if (!empty($_SESSION['feedback'])): ?>
      <div class="alert alert-<?php echo htmlspecialchars($_SESSION['feedback']['type']); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['feedback']['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php unset($_SESSION['feedback']); ?>
    <?php endif; ?>

    <h2>Policy Documents</h2>
    <?php if (empty($arrFiles)): ?>
      <p>No files found in the Policy directory.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($arrFiles as $file): ?>
          <!-- <li><a href="<?php //echo htmlspecialchars('Policy/' . $file); ?>" target="_blank"><?php //echo htmlspecialchars($file); ?></a></li> -->
          <li><a href="<?php echo '../Policy/'. $file; ?>" target="_blank"><?php echo htmlspecialchars($file); ?></a></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</main>