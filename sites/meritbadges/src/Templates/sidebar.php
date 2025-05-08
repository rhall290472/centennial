<?php
defined('IN_APP') or die('Direct access not allowed.');

// Function to check if a file exists and return appropriate link attributes
function checkFileExists($filePath)
{
  // Resolve relative path to absolute path
  $absolutePath = realpath(__DIR__ . '/' . $filePath);
  if ($absolutePath && file_exists($absolutePath)) {
    return ['href' => $filePath, 'class' => 'nav-link px-0 text-white-50', 'valid' => true];
  } else {
    return ['href' => '#', 'class' => 'nav-link px-0 text-muted disabled', 'valid' => false];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<body>

  <div class="col-auto col-md-3 col-xl-auto px-sm-2 px-0 bg-dark d-print-none">
    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
      <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) { ?>
        <p class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
          <span class="fs-5 d-none d-sm-inline">Menu</span>
        </p>
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
          <li>
            <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
              <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline text-white">Reports</span></a>
            <ul class="collapse nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
              <?php
              $adminMenuLinks = [
                ['path' => './reports.php?ReportBy=ByMB', 'label' => 'Counselors per Badge'],
                ['path' => './reports.php?ReportBy=ByMB_ALL', 'label' => 'All Counselors per Badge'],
                ['path' => './reports.php?ReportBy=ByCounselor', 'label' => 'By Counselors'],
                ['path' => './reports.php?ReportBy=ByTroop', 'label' => 'By Unit'],
                ['path' => './reports.php?ReportBy=CounselorofMB', 'label' => 'By Merit Badge'],
                ['path' => './reports.php?ReportBy=ForSelectedTroop', 'label' => 'Selected Troop'],
                ['path' => './reports.php?ReportBy=BySelectedCounselor', 'label' => 'Selected Counselor'],
                ['path' => './reports.php?ReportBy=ByFullSelectedTroop', 'label' => 'All For selected troop'],
              ];
              foreach ($adminMenuLinks as $link) {
                $fileCheck = checkFileExists(parse_url($link['path'], PHP_URL_PATH)); // Ignore query string for file check
              ?>
                <li class="w-100">
                  <a href="<?php echo $link['path']; ?>" class="<?php echo $fileCheck['class']; ?>">
                    <span class="d-none d-sm-inline"><?php echo $link['label']; ?></span>
                    <?php if (!$fileCheck['valid']) echo '<span class="text-danger ms-1">(Missing)</span>'; ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>
          <li>
            <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
              <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline text-white">Uploads</span></a>
            <ul class="collapse nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
              <?php
              $uploadLinks = [
                ['path' => './UpdateData.php', 'label' => 'Counselor List'],
              ];
              foreach ($uploadLinks as $link) {
                $fileCheck = checkFileExists($link['path']);
              ?>
                <li class="w-100">
                  <a href="<?php echo $fileCheck['href']; ?>" class="<?php echo $fileCheck['class']; ?>">
                    <span class="d-none d-sm-inline"><?php echo $link['label']; ?></span>
                    <?php if (!$fileCheck['valid']) echo '<span class="text-danger ms-1">(Missing)</span>'; ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>
          <li>
            <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
              <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline text-white">Functions</span></a>
            <ul class="collapse nav flex-column ms-1" id="submenu3" data-bs-parent="#menu">
              <?php
              $functionLinks = [
                ['path' => 'AdminFunctions.php?AdminFunction=ByUntrained', 'label' => 'Untrained Counselors'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByExpireypt', 'label' => 'Expired YPT'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByInactive', 'label' => 'Inactive Counselors'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByMBCperMB', 'label' => 'Counselors/Badge'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByMB15', 'label' => 'MB > 15'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByNoID', 'label' => 'Counselors No ID'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByNoEmail', 'label' => 'Counselors No Email'],
                ['path' => 'AdminFunctions.php?AdminFunction=MBCnoMB', 'label' => 'Counselors 0 Badges'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByMB', 'label' => 'Special Training'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByMBNoMBC', 'label' => 'No Counselors/Badge'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByCounselorwithNoUnit', 'label' => 'No Unit'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByCounselorInfo', 'label' => 'Contact Information'],
                ['path' => 'AdminFunctions.php?AdminFunction=ByCollegeHistory', 'label' => 'College History'],
              ];
              foreach ($functionLinks as $link) {
                $fileCheck = checkFileExists(parse_url($link['path'], PHP_URL_PATH));
              ?>
                <li>
                  <a href="<?php echo $fileCheck['href']; ?>" class="<?php echo $fileCheck['class']; ?>">
                    <span class="d-none d-sm-inline"><?php echo $link['label']; ?></span>
                    <?php if (!$fileCheck['valid']) echo '<span class="text-danger ms-1">(Missing)</span>'; ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>
          <li>
            <a href="#submenu4" data-bs-toggle="collapse" class="nav-link px-0 align-middle">
              <i class="fs-4 bi-book"></i> <span class="ms-1 d-none d-sm-inline text-white">Edits</span></a>
            <ul class="collapse nav flex-column ms-1" id="submenu4" data-bs-parent="#menu">
              <?php
              $editLinks = [
                ['path' => 'RequirementRevisions.php', 'label' => 'Requirement Revisions'],
                ['path' => 'Counselorform/Counselorform.php', 'label' => 'Counselors'],
                ['path' => 'MeritBadgeform/MeritBadgeform.php', 'label' => 'Merit Badge'],
              ];
              foreach ($editLinks as $link) {
                $fileCheck = checkFileExists($link['path']);
              ?>
                <li>
                  <a href="<?php echo $fileCheck['href']; ?>" class="<?php echo $fileCheck['class']; ?>">
                    <span class="d-none d-sm-inline"><?php echo $link['label']; ?></span>
                    <?php if (!$fileCheck['valid']) echo '<span class="text-danger ms-1">(Missing)</span>'; ?>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>
        </ul>
      <?php } ?>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>