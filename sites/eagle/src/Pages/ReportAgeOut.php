<?php
/*
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */

require_once BASE_PATH . '/src/Classes/CEagle.php';
$cEagle = CEagle::getInstance();

// ── Session & Security ───────────────────────────────────────────────
if (!session_id()) {
    session_start([
        'cookie_httponly'  => true,
        'use_strict_mode'  => true,
        'cookie_secure'    => isset($_SERVER['HTTPS']),
    ]);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Handle unit selection ────────────────────────────────────────────
$selectedUnitType = null;
$selectedUnitNumber = null;

if (isset($_POST['SubmitUnit'], $_POST['Unit']) && $_POST['Unit'] !== '-') {
    [$selectedUnitType, $selectedUnitNumber] = explode('-', $_POST['Unit'], 2);
    $selectedUnitType   = trim($selectedUnitType);
    $selectedUnitNumber = trim($selectedUnitNumber);
}

// ── Prepare unit dropdown query ──────────────────────────────────────
$qryUnits = "
    SELECT DISTINCT UnitType, UnitNumber
    FROM scouts
    WHERE (ProjectApproved IS NULL OR ProjectApproved = '0')
      AND (Eagled       IS NULL OR Eagled       = '0')
      AND (AgedOut      IS NULL OR AgedOut      = '0')
      AND (is_deleted   IS NULL OR is_deleted   = '0')
      AND MemberId > '0'
    ORDER BY UnitType ASC, UnitNumber ASC
";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Age Out Report – Centennial District</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <style>
        .dt-button.btn-primary {
            margin-right: 6px !important;
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-right: 12px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        #loadingOverlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <?php $cEagle->SelectUnit($qryUnits, $_SESSION['csrf_token']); ?>

    <h4 class="text-center mt-4">Scouts and their Age Out Dates</h4>

    <div class="table-responsive position-relative">
        <div id="loadingOverlay">
            <div class="spinner"></div>
            <span>Loading...</span>
        </div>

        <table id="ageOutTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Unit Type</th>
                    <th>Unit#</th>
                    <th>Gender</th>
                    <th style="width:250px">Name</th>
                    <th>BSA ID</th>
                    <th>Age Out Date</th>
                    <th>Project Approval</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // ── Build scout query ────────────────────────────────────────
            if ($selectedUnitType && $selectedUnitNumber) {
                $query = "
                    SELECT *
                    FROM scouts
                    WHERE (Eagled     IS NULL OR Eagled     = '0')
                      AND (AgedOut    IS NULL OR AgedOut    = '0')
                      AND (is_deleted IS NULL OR is_deleted = '0')
                      AND UnitType   = ?
                      AND UnitNumber = ?
                      AND MemberId > '0'
                    ORDER BY Gender, LastName ASC
                ";
                $stmt = mysqli_prepare($cEagle->getDbConn(), $query);
                mysqli_stmt_bind_param($stmt, 'ss', $selectedUnitType, $selectedUnitNumber);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            } else {
                $query = "
                    SELECT *
                    FROM scouts
                    WHERE (Eagled     IS NULL OR Eagled     = '0')
                      AND (AgedOut    IS NULL OR AgedOut    = '0')
                      AND (is_deleted IS NULL OR is_deleted = '0')
                      AND MemberId > '0'
                    ORDER BY STR_TO_DATE(AgeOutDate, '%m/%d/%Y') ASC, Gender
                ";
                $result = $cEagle->doQuery($query);
            }

            if (!$result) {
                echo '<tr><td colspan="7" class="text-danger text-center">Database error</td></tr>';
            } else {
                while ($row = $result->fetch_assoc()) {
                    $preferredName = $cEagle->GetScoutPreferredName($row);
                    $fullName      = $preferredName . ' ' . $row['LastName'];
                    $ageOutDate    = trim($row['AgeOutDate'] ?? '');

                    // Prepare sortable value for DataTables
                    $sortable = '';
                    if ($ageOutDate !== '') {
                        $dt = DateTime::createFromFormat('m/d/Y', $ageOutDate);
                        if ($dt !== false) {
                            $sortable = $dt->format('Ymd');
                        }
                    }

                    // Overdue styling
                    $isOverdue = false;
                    if ($ageOutDate !== '' && strtotime($ageOutDate) <= time()) {
                        $isOverdue = true;
                    }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['UnitType']) ?></td>
                        <td><?= htmlspecialchars($row['UnitNumber']) ?></td>
                        <td><?= htmlspecialchars($row['Gender']) ?></td>
                        <td style="width:250px">
                            <a href="index.php?page=edit-select-scout&Scoutid=<?= htmlspecialchars($row['Scoutid']) ?>">
                                <?= htmlspecialchars($fullName) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['MemberId']) ?></td>
                        <td data-order="<?= htmlspecialchars($sortable) ?>">
                            <?php if ($isOverdue): ?>
                                <b style="color:red;"><?= htmlspecialchars($ageOutDate) ?></b>
                            <?php else: ?>
                                <?= htmlspecialchars($ageOutDate) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['ProjectDate'] ?? '') ?></td>
                    </tr>
                    <?php
                }

                if ($selectedUnitType && $selectedUnitNumber) {
                    mysqli_stmt_close($stmt);
                } else {
                    mysqli_free_result($result);
                }
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts ──────────────────────────────────────────────────────── -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#loadingOverlay').show();

        // Reset default button classes so your className fully controls the style
        $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';
        
        if ($.fn.DataTable.isDataTable('#ageOutTable')) {
            $('#ageOutTable').DataTable().destroy();
        }

        $('#ageOutTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy',  className: 'btn btn-primary btn-sm', title: 'Centennial District Age Out Report' },
                { extend: 'csv',   className: 'btn btn-primary btn-sm', filename: 'Centennial District Age Out Report' },
                { extend: 'excel', className: 'btn btn-primary btn-sm', filename: 'Centennial District Age Out Report' },
                { extend: 'pdf',   className: 'btn btn-primary btn-sm', filename: 'Centennial District Age Out Report' }
            ],
            paging: false,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            columnDefs: [
                { orderable: true, targets: '_all' }
            ],
            initComplete: function() {
                setTimeout(() => {
                    $('#loadingOverlay').hide();
                }, 1200);
            }
        });
    });
    </script>

</body>
</html>