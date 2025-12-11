<?php
ob_start();
session_start();

// CSRF protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  die('Invalid CSRF token');
}

// Define BASE_PATH 
define('BASE_PATH', realpath(__DIR__ . '/../'));

// Load your class and Composer autoloader
require_once BASE_PATH . '/src/Classes/CDistrictAwards.php';
require_once BASE_PATH . '/src/vendor/autoload.php';

$cDistrictAwards = cDistrictAwards::getInstance();

$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('District Awards');
$pdf->SetTitle('Nominees Report');
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(TRUE, 10);

$year  = $cDistrictAwards->GetYear();
$title = $year === '' ? 'All Years' : $year;

// Query exactly like your original code
$sql = $year === ''
  ? "SELECT * FROM district_awards WHERE (IsDeleted IS NULL OR IsDeleted <> '1') ORDER BY Year DESC, LastName"
  : "SELECT * FROM district_awards WHERE Year = '$year' AND (IsDeleted IS NULL OR IsDeleted <> '1') ORDER BY LastName";

$result = $cDistrictAwards->doQuery($sql);
$nominees = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    if ($row['NomineeIDX'] != -1) {
      $nominees[] = $row;
    }
  }
}

// Page 1 – Summary Table
$pdf->AddPage('L');
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Centennial District Awards – Nominees for $title", 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 9);

$html = '<table border="1" cellpadding="4"><thead><tr style="background-color:#ddd;">
    <th>Year</th><th>First</th><th>Pref.</th><th>Last</th>
    <th>Award</th><th>Status</th><th>Member ID</th><th>Unit</th>
    </tr></thead><tbody>';

foreach ($nominees as $row) {
  $award  = $cDistrictAwards->GetAwardName($row['Award']);
  $status = $cDistrictAwards->GetAwardStatus($row['Status']);
  $html .= "<tr>
        <td>{$row['Year']}</td>
        <td>{$row['FirstName']}</td>
        <td>{$row['PName']}</td>
        <td>{$row['LastName']}</td>
        <td>$award</td>
        <td>$status</td>
        <td>{$row['MemberID']}</td>
        <td>{$row['Unit']}</td>
    </tr>";
}
$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');

// One page per nominee
foreach ($nominees as $row) {
  $pdf->AddPage('P');
  $pdf->SetFont('helvetica', 'B', 14);
  $pdf->Cell(0, 10, $row['FirstName'] . ' ' . $row['LastName'] . ' – Nomination', 0, 1, 'C');
  $pdf->Ln(5);
  $pdf->SetFont('helvetica', '', 10);
  $award = $cDistrictAwards->GetAwardName($row['Award']);
  $detail = "<p><b>Year:</b> {$row['Year']}<br>
           <b>Award:</b> $award<br>
           <b>Notes:</b><br>" . nl2br(htmlspecialchars_decode($row['Notes'] ?? '')) . "</p>";
  $pdf->writeHTML($detail, true, false, true, false, '');
}

// CRITICAL: Clean any output before sending PDF
ob_end_clean();

$filename = $year === '' ? 'Nominees_All_Years_Full.pdf' : "Nominees_{$year}_Full.pdf";
$pdf->Output($filename, 'D');
exit;
