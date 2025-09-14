<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Include PHPMailer
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get form data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['name', 'email', 'message'];
$errors = [];

foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        $errors[] = ucfirst($field) . ' is required';
    }
}

// Validate email format
if (!isset($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// If validation fails
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit();
}

// Sanitize input
$name = htmlspecialchars(strip_tags(trim($input['name'])));
$email = htmlspecialchars(strip_tags(trim($input['email'])));
$company = isset($input['company']) ? htmlspecialchars(strip_tags(trim($input['company']))) : 'Not provided';
$address = isset($input['address']) ? htmlspecialchars(strip_tags(trim($input['address']))) : 'Not provided';
$message = htmlspecialchars(strip_tags(trim($input['message'])));

// Create PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com'; // Replace with your email
    $mail->Password = 'your-app-password'; // Replace with your app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom($email, $name);
    $mail->addAddress('your-email@gmail.com', 'Your Name'); // Replace with your email and name

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission - ItWeb';

    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 5px 5px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #2563eb; }
            .value { background: white; padding: 10px; border-radius: 3px; border-left: 4px solid #2563eb; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
                <p>You have received a new message from your website</p>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>{$name}</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>{$email}</div>
                </div>
                <div class='field'>
                    <div class='label'>Company:</div>
                    <div class='value'>{$company}</div>
                </div>
                <div class='field'>
                    <div class='label'>Address:</div>
                    <div class='value'>{$address}</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value'>{$message}</div>
                </div>
                <div class='field'>
                    <div class='label'>Submitted:</div>
                    <div class='value'>" . date('Y-m-d H:i:s') . "</div>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Body = $emailBody;
    $mail->AltBody = "New Contact Form Submission\n\nName: {$name}\nEmail: {$email}\nCompany: {$company}\nAddress: {$address}\nMessage: {$message}\nSubmitted: " . date('Y-m-d H:i:s');

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully.'
    ]);

} catch (Exception $e) {
    error_log("Mail Error: " . $mail->ErrorInfo);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send email. Please try again later.'
    ]);
}
?>

