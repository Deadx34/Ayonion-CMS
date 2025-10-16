<?php
// AYONION-CMS/logout.php

header('Content-Type: application/json');

// Harden session cookie scope and destroy session
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

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

echo json_encode(["success" => true, "message" => "Logged out"]);
?>


