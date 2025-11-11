<?php
// AYONION-CMS/session_check_secure.php - Enhanced session validation

header('Content-Type: application/json');
require_once 'includes/config.php';
require_once 'includes/security.php';

// Set security headers
set_security_headers();

// Start secure session
start_secure_session();

// Check if logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Validate session data
$userId = (int)($_SESSION['user_id'] ?? 0);
$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? '';

if ($userId <= 0 || $role === '' || $username === '') {
    session_destroy();
    echo json_encode(['success' => false, 'message' => 'Invalid session data']);
    exit;
}

// Session timeout check (1 hour of inactivity)
$session_timeout = 3600; // 1 hour
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    log_security_event('session_timeout', ['user_id' => $userId, 'username' => $username]);
    session_destroy();
    echo json_encode(['success' => false, 'message' => 'Session expired due to inactivity']);
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Session hijacking prevention - check IP and User Agent
$stored_ip = $_SESSION['ip_address'] ?? '';
$stored_ua = $_SESSION['user_agent'] ?? '';
$current_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$current_ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

if ($stored_ip !== $current_ip || $stored_ua !== $current_ua) {
    log_security_event('session_hijacking_attempt', [
        'user_id' => $userId,
        'username' => $username,
        'stored_ip' => $stored_ip,
        'current_ip' => $current_ip,
        'stored_ua' => substr($stored_ua, 0, 100),
        'current_ua' => substr($current_ua, 0, 100)
    ]);
    
    session_destroy();
    echo json_encode(['success' => false, 'message' => 'Session validation failed']);
    exit;
}

// Verify user still exists in database
$conn = connect_db();
$sql = "SELECT id, username, role, is_temp_password FROM users WHERE id = ? LIMIT 1";
$stmt = execute_prepared($conn, $sql, 'i', [$userId]);

if (!$stmt) {
    session_destroy();
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    // Ensure session data matches database
    if ($user['username'] !== $username || $user['role'] !== $role) {
        log_security_event('session_data_mismatch', [
            'user_id' => $userId,
            'session_username' => $username,
            'db_username' => $user['username']
        ]);
        
        session_destroy();
        echo json_encode(['success' => false, 'message' => 'Session validation failed']);
        exit;
    }
    
    // Return user data with CSRF token
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'isTempPassword' => (bool)$user['is_temp_password']
        ],
        'csrf_token' => generate_csrf_token(),
        'session_expires_in' => $session_timeout - (time() - $_SESSION['last_activity'])
    ]);
} else {
    // User no longer exists
    $stmt->close();
    $conn->close();
    
    log_security_event('session_user_not_found', ['user_id' => $userId, 'username' => $username]);
    session_destroy();
    echo json_encode(['success' => false, 'message' => 'User not found']);
}
?>
