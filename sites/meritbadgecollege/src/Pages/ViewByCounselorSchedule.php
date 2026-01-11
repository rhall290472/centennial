<?php
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

load_class(BASE_PATH . '/src/Classes/CCounselor.php');
$Counselor = cCounselor::getInstance();  // Fixed variable name consistency

$CMBCollege = CMBCollege::getInstance();

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
    header('Location: index.php?page=login');
    exit;
}

// Handle college year selection
if (isset($_POST['CollegeYear']) && !empty($_POST['CollegeYear'])) {
    setYear($_POST['CollegeYear']);
}

$CollegeYear = $Counselor->getYear();
?>

<div class="row justify-content-center mt-4">
    <div class="col-lg-10 col-xl-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Counselor Schedule by Name
                </h4>
            </div>
            <div class="card-body p-4 p-md-5">

                <!-- Counselor Selector Form -->
                <form method="post" class="mb-5">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="CounselorName" class="form-label fw-semibold">Select Counselor (Optional)</label>
                            <select class="form-select form-select-lg" 
                                    id="CounselorName" 
                                    name="CounselorName" 
                                    placeholder="Type to search by name...">
                                <option value="">-- All Counselors --</option>
                                <?php
                                // Populate dropdown with counselors for current year
                                $sql = "SELECT DISTINCT BSAId, LastName, FirstName 
                                        FROM college_counselors 
                                        WHERE College = ? 
                                        ORDER BY LastName, FirstName";
                                $result = $Counselor->doQuery($sql);
                                if ($result) {
                                    while ($row = $result->fetch_assoc()) {
                                        $selected = (isset($_POST['CounselorName']) && $_POST['CounselorName'] === $row['BSAId']) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($row['BSAId']) . '" ' . $selected . '>'
                                            . htmlspecialchars($row['LastName'] . ', ' . $row['FirstName'])
                                            . '</option>';
                                    }
                                    $result->free();
                                }
                                ?>
                            </select>
                            <div class="form-text">Leave blank to view all counselors for <?php echo htmlspecialchars($CollegeYear); ?></div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="SubmitCounselor" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search me-2"></i>View Schedule
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="my-5">

                <!-- Schedule Report -->
                <h5 class="mb-4 text-primary">
                    <i class="fas fa-list me-2"></i>
                    <?php echo isset($_POST['CounselorName']) && !empty($_POST['CounselorName']) 
                        ? 'Schedule for Selected Counselor' 
                        : 'Full Counselor Schedule'; ?>
                    <span class="text-muted ms-2">(<?php echo htmlspecialchars($CollegeYear); ?>)</span>
                </h5>

                <?php
                // Build query based on selection
                if (isset($_POST['CounselorName']) && !empty($_POST['CounselorName'])) {
                    $SelectCounselor = $_POST['CounselorName'];
                    $query = sprintf(
                        "SELECT * FROM college_counselors 
                         WHERE College='%s' AND BSAId='%s' 
                         ORDER BY LastName, FirstName, MBPeriod",
                        $Counselor->getDbConn()->real_escape_string($CollegeYear),
                        $Counselor->getDbConn()->real_escape_string($SelectCounselor)
                    );
                } else {
                    $query = sprintf(
                        "SELECT * FROM college_counselors 
                         WHERE College='%s' 
                         ORDER BY LastName, FirstName, MBPeriod",
                        $Counselor->getDbConn()->real_escape_string($CollegeYear)
                    );
                }

                $report_results = $Counselor->doQuery($query);

                if ($report_results && $report_results->num_rows > 0) {
                    $Counselor->ReportCounselorSchedule($report_results, $CollegeYear);
                    $report_results->free();
                } else {
                    echo '<div class="alert alert-info text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-3"></i><br>
                            No counselors are currently signed up for ' . htmlspecialchars($CollegeYear) . '.
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
        new TomSelect('#CounselorName', {
            sortField: { field: 'text', direction: 'asc' },
            placeholder: 'Type to search by name...',
            maxOptions: null
        });
    });
</script>