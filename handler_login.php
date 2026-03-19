<?php
// AYONION-CMS/handler_login.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method Not Allowed. Use POST."]);
    exit;
}

try {
    include 'includes/config.php';
    $conn = connect_db();
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON input."]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection error."]);
    exit;
}

$username = $conn->real_escape_string($input['username'] ?? '');
$password = (string)($input['password'] ?? '');
$role = $conn->real_escape_string($input['role'] ?? '');

try {
    // Fetch by username + role first, then verify password (supports bcrypt and legacy plaintext)
    $sql = "SELECT id, username, password, role, is_temp_password FROM users WHERE username = '$username' AND role = '$role' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $stored = $user['password'];
        $isValid = false;

        // Verify hashed passwords first (supports bcrypt variants like $2y$, $2a$, $2b$),
        // then fall back to plaintext compare for legacy records.
        if (password_verify($password, $stored)) {
            $isValid = true;
        } else {
            $isValid = hash_equals($stored, $password);
        }

        if ($isValid) {
            // Harden session cookie and regenerate ID upon login
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
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            echo json_encode([
                "success" => true,
                "message" => "Login successful.",
                "user" => [
                    "id" => $user['id'],
                    "username" => $user['username'],
                    "role" => $user['role'],
                    "isTempPassword" => (bool)$user['is_temp_password']
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect password or role for that user."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No user found with that username and role."]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database query error."]);
}

$conn->close();
?>