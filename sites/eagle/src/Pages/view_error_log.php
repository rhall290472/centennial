<?php
load_class(BASE_PATH . '/src/Classes/CEagle.php');
$cEagle = CEagle::getInstance();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("HTTP/1.0 403 Forbidden");
    exit('Access Denied');
}

$logFile = BASE_PATH . '/../../shared/logs/php_errors.log';
$cleared = false;

// Handle Clear Log (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_log']) && $_POST['clear_log'] === '1') {
    if (file_exists($logFile)) {
        $result = file_put_contents($logFile, '');
        $cleared = ($result !== false);
    }
}

$lines = [];
$maxLines = 500;

if (file_exists($logFile)) {
    $allLines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse(array_slice($allLines, -$maxLines));
} else {
    $lines[] = "Log file not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error Log Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f8f9fa; }
        pre { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 0.9em; line-height: 1.4; }
        .log-entry { margin-bottom: 10px; border-left: 4px solid #dc3545; padding-left: 10px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <h2 class="mb-4">📋 Error Log Viewer (Last <?= $maxLines ?> entries)</h2>

    <div class="mb-3">
        <a href="index.php?page=view_error_log" class="btn btn-primary">Refresh</a>
        <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
        
        <?php if (file_exists($logFile) && filesize($logFile) > 0): ?>
            <form method="post" style="display: inline;" 
                  onsubmit="return confirm('⚠️ Clear the entire error log? This action cannot be undone.')">
                <input type="hidden" name="clear_log" value="1">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-danger">🗑️ Clear Entire Log</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($cleared): ?>
        <div class="alert alert-success">✅ Error log cleared successfully.</div>
    <?php endif; ?>

    <?php if (empty($lines)): ?>
        <div class="alert alert-info">The log is empty.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <pre><?php
                    foreach ($lines as $line) {
                        $escaped = htmlspecialchars($line);
                        $escaped = preg_replace('/(PHP Fatal error|PHP Warning|PHP Deprecated|PHP Notice)/i', 
                                              '<strong style="color:#ff6b6b;">$1</strong>', $escaped);
                        echo '<div class="log-entry">' . $escaped . '</div>';
                    }
                ?></pre>
            </div>
        </div>
    <?php endif; ?>

    <hr>
    <small class="text-muted">
        Log location: <?= htmlspecialchars($logFile) ?><br>
        Size: <?= file_exists($logFile) ? number_format(filesize($logFile)/1024, 1) . ' KB' : '0 KB' ?>
    </small>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>