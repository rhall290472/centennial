<?php
// ────────────────────────────────────────────────
// MUST BE FIRST: No whitespace or output before this block
// ────────────────────────────────────────────────

/*
   Your copyright/proprietary notice block...
*/

$CMBCollege = CMBCollege::getInstance();

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in.'];
  header('Location: index.php?page=login');
  exit;
}

require_once BASE_PATH . '/vendor/autoload.php';

use Mpdf\Mpdf;

// PDF download — runs and STOPS if requested
if (isset($_POST['DownloadPDF']) && !empty($_POST['CollegeYear'])) {
  $CollegeYear = trim($_POST['CollegeYear']);

  // Query for report data
  $qryDK = "SELECT * FROM college_counselors WHERE `College` = ? ORDER BY `MBPeriod`, `MBName`";
  $stmt = $CMBCollege->getDbConn()->prepare($qryDK);
  $stmt->bind_param("s", $CollegeYear);
  $stmt->execute();
  $report_results = $stmt->get_result();

  // Get clean HTML from your updated ReportDoubleKnot()
  $tableHtml = $CMBCollege->ReportDoubleKnot($report_results);
  $report_results->free_result();

  // PDF-only HTML (NO navbar, NO sidebar, NO full site layout)
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
        <h2>Centennial District Merit Badge College<br>Black PugKnot Signup Report – ' . htmlspecialchars($CollegeYear) . '</h2>
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
    die("PDF Error: " . htmlspecialchars($e->getMessage()));
  }

  exit;  // ← THIS LINE IS CRITICAL — stops all further output
}

// ────────────────────────────────────────────────
// If we reach here → normal page load (no PDF request)
// ────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Black Pug Report</title>
  <!-- Your CSS links here -->
</head>

<body>
  <!-- Your navbar include -->
  <!-- Your sidebar include -->

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col py-3">

        <h3>Double Knot Signup Report</h3>

        <form method="post" class="mb-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">

          <label for="CollegeYear">Select College Year:</label>
          <select name="CollegeYear" id="CollegeYear" class="form-control d-inline-block w-auto">
            <option value="">-- Select --</option>
            <?php
            $query = "SELECT DISTINCT College FROM college_details ORDER BY College DESC";
            $res = $CMBCollege->doQuery($query);
            while ($row = $res->fetch_assoc()) {
              $sel = (isset($_POST['CollegeYear']) && $_POST['CollegeYear'] === $row['College']) ? 'selected' : '';
              echo "<option value=\"{$row['College']}\" $sel>{$row['College']}</option>";
            }
            ?>
          </select>

          <button type="submit" name="SubmitCollege" class="btn btn-primary ml-2">Load Report</button>

          <?php if (!empty($_POST['CollegeYear'])): ?>
            <!-- Inside your form -->
            <button type="submit" name="DownloadPDF" formaction="../src/Pages/doubleknot-pdf.php" class="btn btn-success ml-2">
              Download as PDF
            </button>
          <?php endif; ?>
        </form>

        <?php
        if (!empty($_POST['CollegeYear'])) {
          $CollegeYear = $_POST['CollegeYear'];
          $qryDK = "SELECT * FROM college_counselors WHERE `College` = ? ORDER BY `MBPeriod`, `MBName`";
          $stmt = $CMBCollege->getDbConn()->prepare($qryDK);
          $stmt->bind_param("s", $CollegeYear);
          $stmt->execute();
          $results = $stmt->get_result();
          echo $CMBCollege->ReportDoubleKnot($results);
        }
        ?>
      </div>
    </div>
  </div>
</body>

</html>