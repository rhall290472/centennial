<?php
// doubleknot-pdf.php — NO LAYOUT, NO HTML BEFORE PDF
if (file_exists(__DIR__ . '/../../config/config.php')) {
  require_once __DIR__ . '/../../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}
require_once BASE_PATH . '/src/Classes/CMBCollege.php';
$CMBCollege = CMBCollege::getInstance();

require_once BASE_PATH . '/vendor/autoload.php';
use Mpdf\Mpdf;

// Require CollegeYear (from POST or GET — use POST for consistency)
if (empty($_POST['CollegeYear'])) {
    die("No college year selected.");
}
$CollegeYear = trim($_POST['CollegeYear']);

// Run query
$qryDK = "SELECT * FROM college_counselors WHERE `College` = ? ORDER BY `MBPeriod`, `MBName`";
$stmt = $CMBCollege->getDbConn()->prepare($qryDK);
$stmt->bind_param("s", $CollegeYear);
$stmt->execute();
$report_results = $stmt->get_result();

// Get report content
$tableHtml = $CMBCollege->ReportDoubleKnot($report_results);
$report_results->free_result();

// Build PDF HTML (minimal – just report)
$fullHtml = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Black Pug Report ' . htmlspecialchars($CollegeYear) . '</title>
    <style>
        body { font-family: dejavusans, sans-serif; font-size: 11pt; margin: 15mm; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #666; padding: 8px; text-align: left; }
        th { background: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Centennial District Merit Badge College<br>Black Pug Signup Report – ' . htmlspecialchars($CollegeYear) . '</h2>
    <p style="text-align:center;"><strong>Generated:</strong> ' . date('F j, Y') . '</p>
    ' . $tableHtml . '
</body>
</html>';

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'Letter',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 20,
        'margin_bottom' => 20,
    ]);

    $mpdf->WriteHTML($fullHtml);
    $mpdf->Output("DoubleKnot_Report_{$CollegeYear}.pdf", 'D');
} catch (Exception $e) {
    die("PDF generation failed: " . htmlspecialchars($e->getMessage()));
}

  ?>

exit;