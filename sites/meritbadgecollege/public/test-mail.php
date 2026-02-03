<?php
use PHPMailer\PHPMailer\PHPMailer;
// Load configuration
if (file_exists(__DIR__ . '/../config/config.php')) {
  require_once __DIR__ . '/../config/config.php';
} else {
  error_log("Unable to find file config.php @ " . __FILE__ . ' ' . __LINE__);
  die('An error occurred. Please try again later.');
}

require '../vendor/autoload.php';

$mail = new PHPMailer(true);
$mail->SMTPDebug = 3;                  // 1=client msgs, 2=client+server, 3=verbose (best for connection issues)
$mail->Debugoutput = 'error_log';      // Logs to your php_errors.log (SHARED_PATH . '/logs/php_errors.log')
// OR use: $mail->Debugoutput = 'html'; // if testing in browser

$email = 'rhall290472@gmail.com';
$full_name = 'Richard Hall';

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(SMTP_USER, 'Test Mailer');
    $mail->addAddress($email, $full_name);

    $mail->Subject = 'iPage SMTP Test';
    $mail->Body = 'If you see this, iPage SMTP works!';

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Failed: " . $mail->ErrorInfo;
}
?>