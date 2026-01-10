<?php
// UpdateMeritBadge.php - Standalone processor (no HTML output)

if (!session_id()) session_start();

// Redirect unauthenticated users early (before any output)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: index.php?page=login");
    exit;
}

// Optional: Regenerate CSRF token on POST if needed
// But better: validate it here too

// require_once __DIR__ . '/../../../config/config.php';  // Adjust path to load config
load_class(SHARED_PATH . '/src/Classes/CMeritBadges.php');
$CMeritBadge = CMeritBadges::getInstance();

// Optional: Add CSRF check here if you want (recommended)
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
    header("Location: index.php?page=editmeritbadge");
    exit;
}

$oldMeritName = $_POST['original_merit_name'] ?? '';  // You may need to pass original name if updating key

$sqlUpdate = "UPDATE meritbadges SET 
    MeritName = ?, MB_ID = ?, PhampletSKU = ?, PhampletRevised = ?, RequirementsRevised = ?,
    Required = ?, Current = ?, Eagle = ?, SpecialTraining1 = ?, SpecialTraining2 = ?,
    URL = ?, Logo = ?, SpecialTraining = ?, Notes_MB = ?
    WHERE MeritName = ?";  // Use original name if key is changing

$stmt = mysqli_prepare($CMeritBadge->getDbConn(), $sqlUpdate);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssssssissssssss",
        $_POST["element_1_1"],
        $_POST["ID"],
        $_POST["PhampletSKU"],
        $_POST["PhampletRevised"],
        $_POST["RequirementsRevised"],
        $_POST["element_2_1"],
        $_POST["element_2_2"],
        $_POST["element_2_3"],
        $_POST["element_2_4"],
        $_POST["element_2_5"],
        $_POST["element_3_1"],
        $_POST["element_4_1"],
        $_POST["SpecialTraining"],
        $_POST["Notes"],
        $oldMeritName  // or current name if not changing
    );

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Merit badge updated successfully.'];
    } else {
        $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Database update failed.'];
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Database error.'];
}

header("Location: index.php?page=home");
exit;
?>