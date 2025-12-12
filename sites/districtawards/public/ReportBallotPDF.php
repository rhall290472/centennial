<?php
// ReportBallotPDF.php - FINAL 100% WORKING TWO-COLUMN BALLOT (LANDSCAPE!)
ob_start();
require_once __DIR__ . '/../config/config.php';
load_class(BASE_PATH . '/src/Classes/CDistrictAwards.php');
$cDistrictAwards = cDistrictAwards::getInstance();
require_once BASE_PATH . '/src/vendor/tecnickcom/tcpdf/tcpdf.php';
ob_clean();

if (!isset($_POST['export_pdf']) || !isset($_POST['year'])) die('Invalid request');

$year = (int)$_POST['year'];
$cDistrictAwards->SetYear($year);

$awardTitles = [
    '1'=>'District Award of Merit',
    '2'=>'Scoutmaster of the Year',
    '4'=>'Cubmaster of the Year',
    '6'=>'Crew Advisor of the Year',
    '31'=>'Pack Committee Chair of the Year',
    '20'=>'Pack Committee Member of the Year',
    '12'=>'Pack Den Leader of the Year',
    '35'=>'Troop Committee Chair of the Year',
    '21'=>'Troop Committee Member of the Year',
    '18'=>'Commissioner of the Year',
    '27'=>'Unit Commissioner of the Year',
    '16'=>'Bald Eagle Award',
    '14'=>'Outstanding Leaders',


    '17'=>'Friends of Scouting',
    '3'=>'Rookie Scoutmaster of the Year',
    '5'=>'Rookie Cubmaster of the Year',
    '7'=>'Rookie Crew Advisor of the Year',
    '34'=>'Rookie Pack Committee Chair of the Year',
    '22'=>'Rookie Pack Committee Member of the Year',
    '39'=>'Rookie Troop Committee Chair of the Year',
    '23'=>'Rookie Troop Committee Member of the Year',
    '19'=>'Rookie Commissioner of the Year',
    '38'=>'Rookie Unit Commissioner of the Year',
    '29'=>'Junior Leader of the Year',
    '15'=>'Key Scouter',
];

$half  = ceil(count($awardTitles)/2);
$left  = array_slice($awardTitles, 0, $half, true);
$right = array_slice($awardTitles, $half, null, true);

class MYPDF extends TCPDF {
    public $year;
    public function __construct($y) { 
        parent::__construct('L','mm','A4',true,'UTF-8',false);  // ← MUST BE 'L' FOR LANDSCAPE!
        $this->year = $y; 
    }
    public function Header() {
        $this->SetFont('helvetica','B',18);
        $this->Cell(0,15,"District Award Nominees {$this->year}",0,1,'C');
        $this->Ln(5);
    }
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica','I',10);
        $this->Cell(0,10,'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(),0,0,'C');
    }
}

$pdf = new MYPDF($year);
$pdf->SetMargins(12,28,12);
$pdf->SetAutoPageBreak(true,20);
$pdf->AddPage();

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<style>
    body { font-family: helvetica, sans-serif; font-size: 10pt; }
    h2 { text-align: center; margin: 10px 0 20px 0; font-size: 20pt; }
    table.main { width: 100%; border-collapse: separate; border-spacing: 10mm 0; }
    td.col { width: 50%; vertical-align: top; padding: 0; }
    h4 { background-color: #f0e68c; padding: 8px; margin: 18px 0 6px 0; font-size: 11.5pt; font-weight: bold; }
    table.inner { width: 50%; border-collapse: collapse; table-layout: fixed; margin-bottom: 12px; border: 1.5px solid #333; }
    table.inner th, table.inner td { border: 1.3px solid #333; padding: 7px; text-align: center; font-size: 10pt; }
    table.inner th { background-color: #f0e68c; font-weight: bold; }
    table.inner tr:nth-child(even) td { background-color: #fff9e0; }
    col.c1 { width: 1px; }   /* For 15*/
    col.c2 { width: 1px; }   /* Against 15*/
    col.c3 { width: 1px; }   /* First Name 50*/
    col.c4 { width: 1px; }   /* Last Name 50*/
    col.c5 { width: 1px; }   /* Unit 75*/
    .empty { color: #666; font-style: italic; text-align: center; padding: 12px 0; margin: 10px 0; }
</style>
</head>
<body>

<h2>District Award Nominees — <?php echo $year; ?></h2>

<table class="main">
<tr>
    <td class="col">
        <?php foreach ($left as $id => $title): ?>
            <h4><?php echo $title; ?></h4>
            <?php
            ob_start();
            $cDistrictAwards->GetDistrictNominees($id, true);
            $table = ob_get_clean();
            $clean = preg_replace('/\s+/', '', strip_tags($table));
            $isEmpty = (strpos($clean, 'ForAgainstFirstNameLastNameUnit') !== false && strlen($clean) < 130);
            if ($isEmpty) {
                echo '<div class="empty">No nominees for this award.</div>';
            } else {
                echo '<table class="inner">';
                echo '<colgroup><col class="c1"><col class="c2"><col class="c3"><col class="c4"><col class="c5"></colgroup>';
                echo $table;
                echo '</table>';
            }
            ?>
        <?php endforeach; ?>
    </td>
    <td class="col">
        <?php foreach ($right as $id => $title): ?>
            <h4><?php echo $title; ?></h4>
            <?php
            ob_start();
            $cDistrictAwards->GetDistrictNominees($id, true);
            $table = ob_get_clean();
            $clean = preg_replace('/\s+/', '', strip_tags($table));
            $isEmpty = (strpos($clean, 'ForAgainstFirstNameLastNameUnit') !== false && strlen($clean) < 130);
            if ($isEmpty) {
                echo '<div class="empty">No nominees for this award.</div>';
            } else {
                echo '<table class="inner">';
                echo '<colgroup><col class="c1"><col class="c2"><col class="c3"><col class="c4"><col class="c5"></colgroup>';
                echo $table;
                echo '</table>';
            }
            ?>
        <?php endforeach; ?>
    </td>
</tr>
</table>

</body>
</html>
<?php
$html = ob_get_clean();
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$pdf->Output("District_Awards_Nominees_{$year}.pdf", 'D');
exit;