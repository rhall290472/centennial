<!-- src/Pages/changepassword.php -->
<?php 
// Secure session start
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'use_strict_mode' => true,
    'cookie_secure' => isset($_SERVER['HTTPS'])
  ]);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['feedback'] = ['type' => 'danger', 'message' => 'You must be logged in to change your password.'];
    header('Location: index.php?page=login');
    exit;
}

$user_id = $_SESSION['id'];
$feedback = $_SESSION['feedback'] ?? null;
unset($_SESSION['feedback']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $feedback = ['type' => 'danger', 'message' => 'Invalid CSRF token.'];
    } else {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $feedback = ['type' => 'danger', 'message' => 'All fields are required.'];
        } elseif ($new !== $confirm) {
            $feedback = ['type' => 'danger', 'message' => 'New passwords do not match.'];
        } elseif (strlen($new) < 8) {
            $feedback = ['type' => 'danger', 'message' => 'New password must be at least 8 characters.'];
        } else {
            load_class(BASE_PATH . '/src/Classes/CDistrictAwards.php');
            $CAdvancement = CDistrictAwards::getInstance();
            $db = $CAdvancement->getDbConn();

            // Verify current password
            $sql = "SELECT password FROM users WHERE Userid = ? LIMIT 1";
            if ($stmt = mysqli_prepare($db, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $hashed_password);
                if (mysqli_stmt_fetch($stmt) && password_verify($current, $hashed_password)) {
                    // Current password correct → update
                    $new_hashed = password_hash($new, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE users SET password = ? WHERE Userid = ?";
                    if ($update_stmt = mysqli_prepare($db, $update_sql)) {
                        mysqli_stmt_bind_param($update_stmt, "si", $new_hashed, $user_id);
                        if (mysqli_stmt_execute($update_stmt)) {
                            $feedback = ['type' => 'success', 'message' => 'Password changed successfully.'];
                            // Optional: force re-login on next page load for extra security
                        } else {
                            $feedback = ['type' => 'danger', 'message' => 'Failed to update password.'];
                        }
                        mysqli_stmt_close($update_stmt);
                    }
                } else {
                    $feedback = ['type' => 'danger', 'message' => 'Current password is incorrect.'];
                }
                mysqli_stmt_close($stmt);
            } else {
                $feedback = ['type' => 'danger', 'message' => 'Database error. Please try again later.'];
            }
        }
    }
    $_SESSION['feedback'] = $feedback;
    header('Location: index.php?page=changepassword');
    exit;
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Change Password</h4>
                </div>
                <div class="card-body">
                    <?php if ($feedback): ?>
                        <div class="alert alert-<?= htmlspecialchars($feedback['type']) ?> ?> alert-dismissible fade show">
                            <?= htmlspecialchars($feedback['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" required minlength="8">
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Change Password</button>
                        <a href="index.php?page=home" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>