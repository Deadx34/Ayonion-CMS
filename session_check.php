<?php
// AYONION-CMS/session_check.php

header('Content-Type: application/json');
include 'includes/config.php';

// Start/harden session for reading
if (session_status() === PHP_SESSION_NONE) {
    if (function_exists('session_set_cookie_params')) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => true, // set true in production over HTTPS
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    @ini_set('session.use_strict_mode', '1');
    session_start();
}

$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
if (!$loggedIn) {
    echo json_encode([ 'success' => false, 'message' => 'Not logged in' ]);
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$role = $_SESSION['role'] ?? '';

if ($userId <= 0 || $role === '') {
    echo json_encode([ 'success' => false, 'message' => 'Session incomplete' ]);
    exit;
}

$conn = connect_db();
$sql = "SELECT username, is_temp_password FROM users WHERE id = {$userId} LIMIT 1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $userId,
            'username' => $row['username'],
            'role' => $role,
            'isTempPassword' => (bool)$row['is_temp_password']
        ]
    ]);
} else {
    echo json_encode([ 'success' => false, 'message' => 'User not found' ]);
}

$conn->close();
?>


