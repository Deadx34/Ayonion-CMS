<?php
// AYONION-CMS/handler_users.php

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

try {
    // Harden session cookies
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
    // Enforce admin session
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
        throw new Exception("Authentication required.", 401);
    }

    // --- HANDLE LIST USERS (GET) ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
        $users = [];
        $sql = "SELECT id, username, role, is_temp_password FROM users ORDER BY username";
        if ($result = $conn->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $users[] = [
                    'id' => (int)$row['id'],
                    'username' => $row['username'],
                    'role' => $row['role'],
                    'isTempPassword' => (bool)$row['is_temp_password']
                ];
            }
            echo json_encode(["success" => true, "users" => $users]);
        } else {
            throw new Exception("Database error: Could not fetch users.");
        }
    }
    // --- HANDLE ADD USER (POST) ---
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
        $username = trim($input['username'] ?? '');
        $password = (string)($input['password'] ?? '');
        $role = trim($input['role'] ?? '');

        if ($username === '' || $password === '' || $role === '') {
            throw new Exception("Username, password, and role are required.", 400);
        }

        // role validation: allow only marketer and finance (not admin)
        $allowed_roles = ['marketer', 'finance'];
        if (!in_array($role, $allowed_roles, true)) {
            throw new Exception("Invalid role. Allowed roles: marketer, finance.", 400);
        }

        $username_esc = $conn->real_escape_string($username);
        $role_esc = $conn->real_escape_string($role);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, role, is_temp_password) VALUES ('{$username_esc}', '{$hashed_password}', '{$role_esc}', 1)";

        if (query_db($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "User created successfully."]);
        } else {
            // Try to provide a friendlier duplicate username error
            if (strpos($conn->error, 'Duplicate') !== false) {
                throw new Exception("Username already exists.", 409);
            }
            throw new Exception("Database error: Could not create user.");
        }
    }
    // --- HANDLE DELETE USER (GET) ---
    else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === 0) {
            throw new Exception("User ID is required.", 400);
        }

        // Prevent deleting the main admin account (assume admin has id=1)
        if ($id === 1) {
            throw new Exception("Cannot delete the main admin account.", 403);
        }

        $sql = "DELETE FROM users WHERE id = {$id}";
        if (query_db($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "User deleted."]);
        } else {
            throw new Exception("Database error: Could not delete user.");
        }
    }
    // --- HANDLE GET PROFILE (GET) ---
    else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_profile') {
        // Any authenticated user can view their own profile
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            throw new Exception("Authentication required.", 401);
        }
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        
        // Try to get profile with new columns, fallback to basic if columns don't exist
        $sql = "SELECT username, role FROM users WHERE id = {$userId} LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $row = $result->fetch_assoc()) {
            $profile = [
                'username' => $row['username'],
                'role' => $row['role'],
                'full_name' => null,
                'email' => null
            ];
            
            // Check if full_name and email columns exist by trying to select them
            $checkSql = "SELECT full_name, email FROM users WHERE id = {$userId} LIMIT 1";
            $checkResult = $conn->query($checkSql);
            
            if ($checkResult && $checkRow = $checkResult->fetch_assoc()) {
                $profile['full_name'] = $checkRow['full_name'];
                $profile['email'] = $checkRow['email'];
            }
            // If query fails (columns don't exist), we keep the null values
            
            echo json_encode([
                "success" => true,
                "profile" => $profile
            ]);
        } else {
            throw new Exception("Could not fetch profile.");
        }
    }
    // --- HANDLE UPDATE PROFILE (POST) ---
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_profile') {
        // Any authenticated user can update their own profile
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            throw new Exception("Authentication required.", 401);
        }
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $fullName = trim($input['fullName'] ?? '');
        $email = trim($input['email'] ?? '');
        
        $fullName_esc = $conn->real_escape_string($fullName);
        $email_esc = $conn->real_escape_string($email);
        
        // Check if columns exist before updating
        $checkSql = "SELECT full_name, email FROM users WHERE id = {$userId} LIMIT 1";
        $checkResult = $conn->query($checkSql);
        
        if ($checkResult) {
            // Columns exist, update them
            $sql = "UPDATE users SET full_name = '{$fullName_esc}', email = '{$email_esc}' WHERE id = {$userId}";
            
            if (query_db($conn, $sql)) {
                echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
            } else {
                throw new Exception("Database error: Could not update profile.");
            }
        } else {
            // Columns don't exist yet - migration needed
            throw new Exception("Profile columns not available. Please run the user profile migration first.", 503);
        }
    }
    // --- HANDLE CHANGE PASSWORD (POST) ---
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'change_password') {
        // Any authenticated user can change their own password
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            throw new Exception("Authentication required.", 401);
        }
        
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $currentPassword = (string)($input['currentPassword'] ?? '');
        $newPassword = (string)($input['newPassword'] ?? '');
        
        if ($currentPassword === '' || $newPassword === '') {
            throw new Exception("Current password and new password are required.", 400);
        }
        
        if (strlen($newPassword) < 6) {
            throw new Exception("New password must be at least 6 characters long.", 400);
        }
        
        // Verify current password
        $sql = "SELECT password FROM users WHERE id = {$userId} LIMIT 1";
        $result = $conn->query($sql);
        
        if (!$result || !$row = $result->fetch_assoc()) {
            throw new Exception("User not found.");
        }
        
        if (!password_verify($currentPassword, $row['password'])) {
            throw new Exception("Current password is incorrect.", 403);
        }
        
        // Update to new password
        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '{$hashed_password}', is_temp_password = 0 WHERE id = {$userId}";
        
        if (query_db($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "Password changed successfully."]);
        } else {
            throw new Exception("Database error: Could not change password.");
        }
    }
    else {
        throw new Exception("Invalid user action.", 400);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>