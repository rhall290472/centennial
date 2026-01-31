<?php
/*
 * CEmail.php - Centralized email sending with PHPMailer
 * Usage: CEmail::send($to, $subject, $bodyHtml, $bodyPlain = null, $attachments = [])
 */

require_once BASE_PATH . '/vendor/autoload.php'; // Composer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CEmail
{
  private static $instance = null;

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct() {}

  /**
   * Send an email using PHPMailer
   *
   * @param string|array $to          Recipient email(s) or array [email => name]
   * @param string       $subject
   * @param string       $bodyHtml    HTML body
   * @param string|null  $bodyPlain   Plain text fallback (auto-generated if null)
   * @param array        $attachments Array of [path => name] or just paths
   * @return bool|string  true on success, error message on failure
   */
  public function send($to, $subject, $bodyHtml, $bodyPlain = null, $attachments = [])
  {
    $mail = new PHPMailer(true);

    try {
      // === SERVER SETTINGS ===
      $mail->isSMTP();
      $mail->Host       = SMTP_HOST;
      $mail->SMTPAuth   = true;
      $mail->Username   = SMTP_USER;
      $mail->Password   = SMTP_PASS;
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = SMTP_PORT;


      // === DEBUG (remove in production) ===
      // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // 2 = client+server, 1 = client

      // === SENDER ===
      $mail->setFrom('no-reply@centennialdistrict.org', 'Centennial District Eagle');
      $mail->addReplyTo('richard.hall@centennialdistrict.co', 'Richard Hall');

      // === RECIPIENTS ===
      if (is_array($to)) {
        foreach ($to as $email => $name) {
          if (is_numeric($email)) {
            $mail->addAddress($name);
          } else {
            $mail->addAddress($email, $name);
          }
        }
      } else {
        $mail->addAddress($to);
      }

      // === CONTENT ===
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body    = $bodyHtml;
      $mail->AltBody = $bodyPlain ?? strip_tags($bodyHtml);

      // === ATTACHMENTS ===
      foreach ($attachments as $file => $name) {
        if (is_numeric($file)) {
          $mail->addAttachment($name);
        } else {
          $mail->addAttachment($file, $name);
        }
      }

      $mail->send();
      return true;
    } catch (Exception $e) {
      error_log("Email send failed: {$mail->ErrorInfo} | To: " . (is_array($to) ? implode(', ', array_keys($to)) : $to));
      return "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}
