<?php
// contact-form.php

header('Content-Type: application/json');

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$name    = htmlspecialchars(trim($data['name']));
$email   = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$company = htmlspecialchars(trim($data['company']));
$address = htmlspecialchars(trim($data['address']));
$message = htmlspecialchars(trim($data['message']));
$phone   = htmlspecialchars(trim($data['phone']));


// Validate required fields
if (empty($name) || empty($email) || empty($message) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Name, Email, Phone, and Message are required.']);
    exit;
}


// ✅ Load Composer autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP config for Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tusharsharma.vedanet@gmail.com';   // Your Gmail address
    $mail->Password   = 'zezz clvp jnuc ahma';             // Your Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('tusharsharma.vedanet@gmail.com', 'Vedanet Contact Form');
    $mail->addAddress('tusharsharma.vedanet@gmail.com');  // Send to yourself

    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body    = "
        <h2>New Contact Message</h2>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Phone:</strong> {$phone}</p>
        <p><strong>Company:</strong> {$company}</p>
        <p><strong>Address:</strong> {$address}</p>
        <p><strong>Message:</strong><br>{$message}</p>
    ";

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
