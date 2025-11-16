<?php
// AYONION-CMS/handler_settings.php - Admin-only global settings

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

try {
    // Secure session
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((string)($_SERVER['SERVER_PORT'] ?? '') === '443')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        if (function_exists('session_set_cookie_params')) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        @ini_set('session.use_strict_mode', '1');
        session_start();
    }

    // Allow logo access for login page (public endpoint for get action only)
    $isGetAction = ($action === 'get');
    $requiresAuth = !$isGetAction;
    
    if ($requiresAuth && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['role'] ?? '') !== 'admin')) {
        throw new Exception("Authentication required.", 401);
    }

    // Ensure a row exists (id=1 acts as singleton)
    $conn->query("INSERT INTO settings (id, company_name, logo_url, email, phone, address, website) 
                  SELECT 1, 'Ayonion Studios', '', '', '', '', '' WHERE NOT EXISTS (SELECT 1 FROM settings WHERE id=1)");

    if ($action === 'get') {
        $sql = "SELECT company_name, logo_url, email, phone, address, website FROM settings WHERE id = 1";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            echo json_encode([ 'success' => true, 'settings' => $row ]);
        } else {
            echo json_encode([ 'success' => false, 'message' => 'Settings not found' ]);
        }
    }
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
        $companyName = $conn->real_escape_string(trim($input['companyName'] ?? ''));
        $logoUrl = $conn->real_escape_string(trim($input['logoUrl'] ?? ''));
        $email = $conn->real_escape_string(trim($input['email'] ?? ''));
        $phone = $conn->real_escape_string(trim($input['phone'] ?? ''));
        $website = $conn->real_escape_string(trim($input['website'] ?? ''));
        $address = $conn->real_escape_string(trim($input['address'] ?? ''));

        if ($companyName === '') {
            http_response_code(400);
            echo json_encode([ 'success' => false, 'message' => 'Company name is required' ]);
            exit;
        }

        $sql = "UPDATE settings SET company_name='$companyName', logo_url='$logoUrl', email='$email', phone='$phone', website='$website', address='$address' WHERE id = 1";
        if ($conn->query($sql)) {
            echo json_encode([ 'success' => true, 'message' => 'Settings updated' ]);
        } else {
            http_response_code(500);
            echo json_encode([ 'success' => false, 'message' => 'Failed to update settings: ' . $conn->error ]);
        }
    }
    else {
        http_response_code(400);
        echo json_encode([ 'success' => false, 'message' => 'Invalid action' ]);
    }
} catch (Throwable $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([ 'success' => false, 'message' => $e->getMessage() ]);
}

$conn->close();
?>


