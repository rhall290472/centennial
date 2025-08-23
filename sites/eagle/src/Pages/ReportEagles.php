<?php
/*
 * ReportEagles.php: Page for Eagles in the Centennial District website.
 * Copyright 2017-2025 - Richard Hall (Proprietary Software).
 */

// Load classes
load_class(__DIR__ . '/../Classes/CEagle.php');
$cEagle = CEagle::getInstance();

// Session check
if (!session_id()) {
    session_start([
        'cookie_httponly' => true,
        'use_strict_mode' => true,
        'cookie_secure' => isset($_SERVER['HTTPS'])
    ]);
}

// Authentication check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("HTTP/1.0 403 Forbidden");
    exit;
}

// Ensure CSRF token is set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$UnitType = '';
$UnitNum = '';
$csv_hdr = "Unit Type,Unit Number,Gender,Name,Year,Beneficiary,Project Name,Project Hours\n";
$csv_output = '';
$feedback = isset($_SESSION['feedback']) ? $_SESSION['feedback'] : [];
unset($_SESSION['feedback']);

// Handle unit selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['SubmitUnit'], $_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $Unit = filter_input(INPUT_POST, 'Unit', FILTER_SANITIZE_STRING);
    if ($Unit) {
        [$UnitType, $UnitNum] = array_map('trim', explode(',', $Unit, 2));
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .fixed_header { width: 1250px; margin: auto; }
        .fixed_header th, .fixed_header td { text-align: left; }
        .fixed_header th:nth-child(1), .fixed_header td:nth-child(1) { width: 100px; }
        .fixed_header th:nth-child(2), .fixed_header td:nth-child(2) { width: 50px; }
        .fixed_header th:nth-child(3), .fixed_header td:nth-child(3) { width: 50px; }
        .fixed_header th:nth-child(4), .fixed_header td:nth-child(4) { width: 250px; }
        .fixed_header th:nth-child(5), .fixed_header td:nth-child(5) { width: 100px; }
        .fixed_header th:nth-child(6), .fixed_header td:nth-child(6) { width: 300px; }
        .fixed_header th:nth-child(7), .fixed_header td:nth-child(7) { width: 300px; }
        .fixed_header th:nth-child(8), .fixed_header td:nth-child(8) { width: 100px; }
        .content-center { text-align: center; }
    </style>
</head>
<body>
    <div class="container-fluid mt-5 pt-3 content-center">
        <!-- Display Feedback -->
        <?php if (!empty($feedback)): ?>
            <div class="alert alert-<?php echo htmlspecialchars($feedback['type']); ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($feedback['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h4>Scouts who have Eagled (since 2017)</h4>
        <?php $cEagle->SelectUnit($_SESSION['csrf_token']); ?>
        <table class="fixed_header table table-striped">
            <thead>
                <tr>
                    <th>Unit Type</th>
                    <th>Unit#</th>
                    <th>Gender</th>
                    <th>Name</th>
                    <th>Year</th>
                    <th>Beneficiary</th>
                    <th>Project Name</th>
                    <th>Project Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $queryScout = "SELECT * FROM `scouts` WHERE `Eagled` = 1";
                $params = [];
                $types = '';
                if (!empty($UnitType) && !empty($UnitNum)) {
                    $queryScout .= " AND `UnitType` = ? AND `UnitNumber` = ?";
                    $params[] = $UnitType;
                    $params[] = $UnitNum;
                    $types .= 'si';
                }
                $queryScout .= " ORDER BY `LastName`";

                $stmt = mysqli_prepare($cEagle->getDbConn(), $queryScout);
                if ($params) {
                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                }
                mysqli_stmt_execute($stmt);
                $Scout = mysqli_stmt_get_result($stmt);

                if (!$Scout) {
                    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'Error retrieving scout data.'];
                    header("Location: index.php?page=Ereport");
                    exit;
                }

                while ($rowScout = mysqli_fetch_assoc($Scout)) {
                    $FirstName = htmlspecialchars($cEagle->GetScoutPreferredName($rowScout));
                    $LastName = htmlspecialchars($rowScout['LastName'] ?? '');
                    $Scoutid = htmlspecialchars($rowScout['Scoutid'] ?? ''); // Fixed case
                    $UnitTypeVal = htmlspecialchars($rowScout['UnitType'] ?? '');
                    $UnitNumberVal = htmlspecialchars($rowScout['UnitNumber'] ?? '');
                    $GenderVal = htmlspecialchars($rowScout['Gender'] ?? '');
                    $BORVal = htmlspecialchars($rowScout['BOR'] ?? '');
                    $BeneficiaryVal = htmlspecialchars($rowScoutapas['Beneficiary'] ?? '');
                    $ProjectNameVal = htmlspecialchars($rowScout['ProjectName'] ?? '');
                    $ProjectHoursVal = htmlspecialchars($rowScout['ProjectHours'] ?? '');

                    echo "<tr>
                        <td>$UnitTypeVal</td>
                        <td>$UnitNumberVal</td>
                        <td>$GenderVal</td>
                        <td><a href=index.php?page=edit-select-scout&Scoutid=$Scoutid\">$FirstName $LastName</a></td>
                        <td>$BORVal</td>
                        <td>$BeneficiaryVal</td>
                        <td>$ProjectNameVal</td>
                        <td>$ProjectHoursVal</td>
                    </tr>";

                    // Escape CSV values
                    $csv_output .= sprintf('"%s","%s","%s","%s","%s","%s","%s","%s"%s',
                        str_replace('"', '""', $UnitTypeVal),
                        str_replace('"', '""', $UnitNumberVal),
                        str_replace('"', '""', $GenderVal),
                        str_replace('"', '""', "$FirstName $LastName"),
                        str_replace('"', '""', $BORVal),
                        str_replace('"', '""', $BeneficiaryVal),
                        str_replace('"', '""', $ProjectNameVal),
                        str_replace('"', '""', $ProjectHoursVal),
                        "\n"
                    );
                }
                mysqli_stmt_close($stmt);
                ?>
            </tbody>
        </table>
        <p><b>Total: <?php echo mysqli_num_rows($Scout); ?> scouts</b></p>

        <form name="export" action="export.php" method="post" class="mt-4">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="csv_hdr" value="<?php echo htmlspecialchars($csv_hdr); ?>">
            <input type="hidden" name="csv_output" value="<?php echo htmlspecialchars($csv_output); ?>">
            <input class="btn btn-primary btn-sm" type="submit" value="Export to CSV">
        </form>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>