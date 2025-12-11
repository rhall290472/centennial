<?php
load_class(BASE_PATH . '/src/Classes/CDistrictAwards.php');
$cDistrictAwards = cDistrictAwards::getInstance();

// ---------------------------------------------------------------------------
// 1. FULL PDF EXPORT – runs first, no HTML output
// ---------------------------------------------------------------------------
if (isset($_POST['export_full_pdf'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    require_once BASE_PATH . '/src/vendor/autoload.php';

    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('District Awards');
    $pdf->SetTitle('Full Nominees Report');
    $pdf->SetMargins(10, 15, 10);
    $pdf->SetAutoPageBreak(true, 10);

    $year = $cDistrictAwards->GetYear();
    $title = $year === '' ? 'All Years' : $year;

    // Build correct query (your doQuery only accepts one parameter)
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

    // Page 1: Summary Table
    $pdf->AddPage('L');
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, "Centennial District Awards – Nominees for $title", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', '', 9);

    $html = '<table border="1" cellpadding="4">
        <thead><tr style="background-color:#dddddd;">
            <th>Year</th><th>First</th><th>Pref.</th><th>Last</th>
            <th>Award</th><th>Status</th><th>Member ID</th><th>Unit</th>
        </tr></thead><tbody>';

    foreach ($nominees as $row) {
        $award  = $cDistrictAwards->GetAwardName($row['Award']);
        $status = $cDistrictAwards->GetAwardStatus($row['Status']);
        $html  .= "<tr>
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
        $pdf->Cell(0, 10, $row['FirstName'].' '.$row['LastName'].' – Nomination Details', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);

        $award  = $cDistrictAwards->GetAwardName($row['Award']);
        $status = $cDistrictAwards->GetAwardStatus($row['Status']);

        $detail = "<p><b>Year:</b> {$row['Year']}<br>
                   <b>Award:</b> $award<br>
                   <b>Status:</b> $status<br>
                   <b>Position:</b> ".htmlspecialchars($row['Position'] ?? '')."<br>
                   <b>Unit:</b> ".htmlspecialchars($row['Unit'] ?? '')."<br>
                   <b>BSA ID:</b> ".htmlspecialchars($row['MemberID'] ?? '')."<br>
                   <b>Notes:</b><br>".nl2br(htmlspecialchars($row['Notes'] ?? ''))."</p>";

        $pdf->writeHTML($detail, true, false, true, false, '');
    }

    $filename = $year === '' ? 'Nominees_All_Years_Full.pdf' : "Nominees_{$year}_Full.pdf";
    $pdf->Output($filename, 'D');
    exit;
}

// ---------------------------------------------------------------------------
// 2. NORMAL PAGE DISPLAY
// ---------------------------------------------------------------------------
if (isset($_POST['SubmitYear'])) {
    $cDistrictAwards->SetYear($_POST['Year']);
}
$cDistrictAwards->SelectYear();
$year = $cDistrictAwards->GetYear();
$title = $year === '' ? 'All Years' : $year;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="description" content="ReportAwardYear.php">
    <style>
        .fixed_header th, .fixed_header td { padding: 8px; text-align: left; }
        .dataTables_wrapper { margin: 20px; }
        .dt-buttons { margin-bottom: 10px; text-align: center; }
        .dt-buttons .btn { margin-right: 5px; }
    </style>
</head>
<body>

<center>
    <h4>Nominees for <?= htmlspecialchars($title) ?></h4>

<form method="post" action="report_pdf.php" target="_blank">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <button type="submit" class="btn btn-success btn-lg">
        Export Full PDF with All Nominations
    </button>
</form>

    <table id="nomineesTable" class="fixed_header table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th style='width:150px'>Year</th>
                <th style='width:150px'>First Name</th>
                <th style='width:150px'>Preferred Name</th>
                <th style='width:150px'>Last Name</th>
                <th style='width:500px'>Award</th>
                <th style='width:50px'>Status</th>
                <th style='width:150px'>Member ID</th>
                <th style='width:500px'>Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = $year === ''
                ? "SELECT * FROM district_awards WHERE (IsDeleted IS NULL OR IsDeleted <> '1') ORDER BY Year DESC, LastName"
                : "SELECT * FROM district_awards WHERE Year = '$year' AND (IsDeleted IS NULL OR IsDeleted <> '1') ORDER BY LastName";

            $result = $cDistrictAwards->doQuery($sql);  // Only one parameter!

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['NomineeIDX'] == -1) continue;
                    $award  = $cDistrictAwards->GetAwardName($row['Award']);
                    $status = $cDistrictAwards->GetAwardStatus($row['Status']);
                    echo "<tr>
                        <td>{$row['Year']}</td>
                        <td>{$row['FirstName']}</td>
                        <td>{$row['PName']}</td>
                        <td><a href='index.php?page=edit-nominee&NomineeIDX={$row['NomineeIDX']}'>{$row['LastName']}</a></td>
                        <td>$award</td>
                        <td>$status</td>
                        <td>{$row['MemberID']}</td>
                        <td>{$row['Unit']}</td>
                    </tr>";
                }
            }
            ?>
        </tbody>
    </table>
</center>

<script>
$(document).ready(function() {
    $('#nomineesTable').DataTable({
        dom: 'Bfrtip',
        paging: false,
        buttons: ['copy','csv','excel','pdf'],
        order: [[0, 'desc']]
    });
});
</script>
</body>
</html>