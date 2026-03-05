<?php
/*
!==============================================================================!
!   Proprietary Software of Richard Hall                                       !
!   Copyright 2017-2024 - Richard Hall                                         !
!==============================================================================!
*/

load_class(BASE_PATH . '/src/Classes/CScout.php');
$CScout = CScout::getInstance();

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to view this page.'];
    header('Location: index.php?page=login');
    exit;
}

// Handle college year selection
if (isset($_POST['CollegeYear']) && !empty($_POST['CollegeYear'])) {
    setYear($_POST['CollegeYear']);
}

$CollegeYear = $CScout->getYear();
?>

<div class="row justify-content-center mt-4">
    <div class="col-lg-10 col-xl-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-user me-2"></i>Scout Schedule by Name
                </h4>
            </div>
            <div class="card-body p-4 p-md-5">

                <!-- Scout Selector Form -->
                <form method="post" class="mb-5">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">

                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="ScoutName" class="form-label fw-semibold">Select Scout (Optional)</label>
                            <select class="form-select form-select-lg" 
                                    id="ScoutName" 
                                    name="ScoutName" 
                                    placeholder="Type to search by name...">
                                <option value="">-- All Scouts --</option>
                                <?php
                                // Populate dropdown with scouts for current year
                                $sql = "SELECT DISTINCT BSAIdScout, LastNameScout, FirstNameScout 
                                        FROM college_registration 
                                        WHERE College = ? 
                                        ORDER BY LastNameScout, FirstNameScout";
                                // Assuming doQuery supports prepared statements or you adjust accordingly
                                $result = $CScout->doQuery($sql);  // ← may need adjustment if your doQuery doesn't support params
                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = (isset($_POST['ScoutName']) && $_POST['ScoutName'] === $row['BSAIdScout']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row['BSAIdScout']) . '" ' . $selected . '>'
                                            . htmlspecialchars($row['LastNameScout'] . ', ' . $row['FirstNameScout'])
                                            . '</option>';
                                    }
                                    $result->free();
                                }
                                ?>
                            </select>
                            <div class="form-text">Leave blank to view all scouts for <?php echo htmlspecialchars($CollegeYear); ?></div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="SubmitScout" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search me-2"></i>View Schedule
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-5">

                <!-- Schedule Report -->
                <h5 class="mb-4 text-primary">
                    <i class="fas fa-list me-2"></i>
                    <?php echo isset($_POST['ScoutName']) && !empty($_POST['ScoutName']) 
                        ? 'Schedule for Selected Scout' 
                        : 'Full Scout Schedule'; ?>
                    <span class="text-muted ms-2">(<?php echo htmlspecialchars($CollegeYear); ?>)</span>
                </h5>

                <?php
                // Build query based on selection
                if (isset($_POST['ScoutName']) && !empty($_POST['ScoutName'])) {
                    $SelectScout = $_POST['ScoutName'];
                    $query = sprintf(
                        "SELECT * FROM college_registration 
                         WHERE College='%s' AND BSAIdScout='%s' 
                         ORDER BY LastNameScout, FirstNameScout, Period",
                        $CScout->getDbConn()->real_escape_string($CollegeYear),
                        $CScout->getDbConn()->real_escape_string($SelectScout)
                    );
                } else {
                    $query = sprintf(
                        "SELECT * FROM college_registration 
                         WHERE College='%s' 
                         ORDER BY LastNameScout, FirstNameScout, Period",
                        $CScout->getDbConn()->real_escape_string($CollegeYear)
                    );
                }

                $report_results = $CScout->doQuery($query);

                if ($report_results && $report_results->num_rows > 0) {
                    $CScout->ReportScoutMeritBadges($report_results, $CollegeYear);
                    $report_results->free();
                } else {
                    echo '<div class="alert alert-info text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                            No scouts are currently registered for ' . htmlspecialchars($CollegeYear) . '.
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Tom Select for Searchable Dropdown -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect('#ScoutName', {
            sortField: { field: 'text', direction: 'asc' },
            placeholder: 'Type to search by name...',
            maxOptions: null
        });
    });
</script>