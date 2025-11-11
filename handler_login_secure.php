<?php
// AYONION-CMS/handler_login.php - Secure authentication handler

header('Content-Type: application/json');
require_once 'includes/config.php';
require_once 'includes/security.php';

// Set security headers
set_security_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method Not Allowed. Use POST."]);
    exit;
}

$conn = connect_db();
$input = json_decode(file_get_contents("php://input"), true);

// Sanitize inputs
$username = sanitize_input($input['username'] ?? '', 'string');
$password = sanitize_input($input['password'] ?? '', 'string');
$role = sanitize_input($input['role'] ?? '', 'string');

// Validate inputs
if (empty($username) || empty($password) || empty($role)) {
    log_security_event('login_failed', ['reason' => 'empty_credentials', 'username' => $username]);
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Rate limiting - prevent brute force (5 attempts per 15 minutes)
$client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
rate_limit($client_ip . '_login', 5, 900);

// Use prepared statement to prevent SQL injection
$sql = "SELECT id, username, password, role, is_temp_password FROM users WHERE username = ? AND role = ? LIMIT 1";
$stmt = execute_prepared($conn, $sql, 'ss', [$username, $role]);

if (!$stmt) {
    http_response_code(500);
    log_security_event('login_error', ['reason' => 'database_error']);
    echo json_encode(["success" => false, "message" => "Database error."]);
    exit;
}

$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $stmt->close();

    $stored = $user['password'];
    $isValid = false;

    // Verify password (supports bcrypt and legacy plaintext)
    if (preg_match('/^\$2y\$\d{2}\$/', $stored)) {
        $isValid = verify_password($password, $stored);
    } else {
        // Legacy plaintext - log security warning
        $isValid = hash_equals($stored, $password);
        if ($isValid) {
            log_security_event('login_plaintext_password', [
                'user_id' => $user['id'], 
                'username' => $username,
                'warning' => 'Using plaintext password - should be upgraded to bcrypt'
            ]);
        }
    }

    if ($isValid) {
        // Start secure session
        start_secure_session();
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        // Generate CSRF token
        $csrf_token = generate_csrf_token();
        
        // Log successful login
        log_security_event('login_success', [
            'user_id' => $user['id'], 
            'username' => $username, 
            'role' => $role
        ]);
        
        echo json_encode([
            "success" => true,
            "message" => "Login successful.",
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "role" => $user['role'],
                "isTempPassword" => (bool)$user['is_temp_password']
            ],
            "csrf_token" => $csrf_token
        ]);
    } else {
        // Invalid password
        log_security_event('login_failed', [
            'reason' => 'invalid_password',
            'username' => $username,
            'role' => $role
        ]);
        
        // Generic error message to prevent user enumeration
        echo json_encode(["success" => false, "message" => "Invalid credentials."]);
    }
} else {
    // User not found
    $stmt->close();
    log_security_event('login_failed', [
        'reason' => 'user_not_found',
        'username' => $username,
        'role' => $role
    ]);
    
    // Generic error message to prevent user enumeration
    echo json_encode(["success" => false, "message" => "Invalid credentials."]);
}

$conn->close();
?>
