<?php
// Simple test script to verify PHPMailer setup
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com'; // Replace with your Gmail
    $mail->Password = 'your-app-password'; // Replace with your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('your-email@gmail.com', 'Test Sender');
    $mail->addAddress('your-email@gmail.com', 'Test Recipient');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test - Contact Form Backend';
    $mail->Body = '<h1>Test Email</h1><p>If you received this email, your PHPMailer setup is working correctly!</p>';
    $mail->AltBody = 'Test Email - If you received this email, your PHPMailer setup is working correctly!';

    $mail->send();
    echo 'Test email sent successfully! Check your inbox.';
} catch (Exception $e) {
    echo "Test failed: {$mail->ErrorInfo}";
}
?>

