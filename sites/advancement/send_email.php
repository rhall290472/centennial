<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'assets/vendor/autoload.php'; // If using Composer

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
  // Server settings
  //$mail->SMTPDebug = 2; 
  $mail->isSMTP();
  $mail->Host = 'smtp.gmail.com'; // Set your SMTP host
  $mail->SMTPAuth = true;
  $mail->Username = 'rhall290472@gmail.com'; // Your SMTP username
  $mail->Password = 'vicx cxho rywh ylok'; // Your SMTP password or App Password
  // $mail->Username = 'richard.hall@centennialdistrict.co'; // Your SMTP username
  // $mail->Password = 'Rlh$33033'; // Your SMTP password or App Password
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587;

  // Recipients
  $mail->setFrom($_POST['email'], $_POST['name']);
  $mail->addAddress('richard.hall@centennialdistrict.co'); // Add your recipient email

  // Content
  $mail->isHTML(true);
  $mail->Subject = $_POST['subject'] . " - Centennialdistrict.co";
  $mail->Body    = nl2br($_POST['email']) .'</br>'. nl2br($_POST['name']) . '</br>' . nl2br($_POST['message']);
  $mail->AltBody = $_POST['message'];

  $mail->send();

  // Redirect back with success message
  echo "Your message has been sent. Thank you!";
  echo "<script>window.history.back()</script>";
  //header('Location: contact.php?status=success');
} catch (Exception $e) {
  // Redirect back with error message
  echo $e->getMessage();
  //header('Location: contact.php?status=error');
}

?>