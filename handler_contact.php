<?php
// AYONION-CMS/handler_contact.php - Handles website contact form submissions securely

// 1. Set Headers to allow CORS from your Vercel frontend
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // You can restrict this to your actual Vercel domain later
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method Not Allowed. Use POST."]);
    exit;
}

// 2. Get the input data (supports both JSON and regular form data)
$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    $input = $_POST;
}

// 3. HONEYPOT CHECK
// If the hidden 'website_company_url' field is filled, it's a bot.
if (!empty($input['website_company_url'])) {
    // We return success so the bot thinks it worked, but we don't send anything.
    echo json_encode(["success" => true, "message" => "Message sent successfully."]);
    exit;
}



// 5. EXTRACT AND VALIDATE FORM DATA
$name = htmlspecialchars(trim($input['name'] ?? ''));
$email = filter_var(trim($input['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(trim($input['message'] ?? ''));

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Please fill in all required fields."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid email address."]);
    exit;
}

// 6. SEND EMAIL
$to = "info@ayonionstudios.com"; 
$subject = "New Lead from Website: " . $name;
$headers = "From: no-reply@ayonionstudios.com\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$email_body = "You have received a new lead from your website.\n\n";
$email_body .= "Name: {$name}\n";
$email_body .= "Email: {$email}\n";
$email_body .= "Message:\n{$message}\n\n";
$email_body .= "--\nSent securely via Ayonion CMS (Spam Protected)";

if (mail($to, $subject, $email_body, $headers)) {
    echo json_encode(["success" => true, "message" => "Your message has been sent successfully."]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to send email. Server configuration error."]);
}
?>
