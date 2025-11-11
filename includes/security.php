<?php
/**
 * Security Helper Functions
 * Provides centralized security utilities for the Ayonion CMS
 */

// Rate limiting storage
$_RATE_LIMIT_STORAGE = [];

/**
 * Validate and sanitize input data
 */
function sanitize_input($data, $type = 'string') {
    if ($data === null) return null;
    
    switch ($type) {
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT) !== false ? (int)$data : 0;
        
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT) !== false ? (float)$data : 0.0;
        
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL) !== false ? filter_var($data, FILTER_SANITIZE_EMAIL) : '';
        
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL) !== false ? filter_var($data, FILTER_SANITIZE_URL) : '';
        
        case 'bool':
            return filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        
        case 'date':
            // Validate date format YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                $timestamp = strtotime($data);
                if ($timestamp !== false) {
                    return $data;
                }
            }
            return null;
        
        case 'string':
        default:
            // Remove null bytes, trim whitespace
            $data = str_replace("\0", '', $data);
            return trim($data);
    }
}

/**
 * Prepared statement helper - prevents SQL injection
 */
function prepare_and_execute($conn, $sql, $types, $params) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    return $stmt;
}

/**
 * Check if user is authenticated
 */
function require_auth() {
    if (session_status() === PHP_SESSION_NONE) {
        start_secure_session();
    }
    
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
        exit;
    }
    
    return [
        'user_id' => (int)($_SESSION['user_id'] ?? 0),
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['role'] ?? ''
    ];
}

/**
 * Check if user has required role
 */
function require_role($allowed_roles) {
    $user = require_auth();
    
    if (!in_array($user['role'], $allowed_roles, true)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden. Insufficient permissions.']);
        exit;
    }
    
    return $user;
}

/**
 * Start secure session with hardened settings
 */
function start_secure_session() {
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }
    
    // Detect HTTPS
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ((string)($_SERVER['SERVER_PORT'] ?? '') === '443')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    
    // Set secure session parameters
    session_set_cookie_params([
        'lifetime' => 3600, // 1 hour
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    
    // Additional security settings
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Strict');
    
    if ($isHttps) {
        ini_set('session.cookie_secure', '1');
    }
    
    session_start();
    
    // Regenerate session ID periodically
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Rate limiting - prevent brute force attacks
 */
function rate_limit($identifier, $max_attempts = 5, $window_seconds = 300) {
    global $_RATE_LIMIT_STORAGE;
    
    $key = 'rate_limit_' . md5($identifier);
    $now = time();
    
    // Initialize or get current data
    if (!isset($_RATE_LIMIT_STORAGE[$key])) {
        $_RATE_LIMIT_STORAGE[$key] = ['attempts' => 0, 'reset_time' => $now + $window_seconds];
    }
    
    $data = $_RATE_LIMIT_STORAGE[$key];
    
    // Reset if window expired
    if ($now > $data['reset_time']) {
        $_RATE_LIMIT_STORAGE[$key] = ['attempts' => 1, 'reset_time' => $now + $window_seconds];
        return true;
    }
    
    // Check if limit exceeded
    if ($data['attempts'] >= $max_attempts) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'Too many attempts. Please try again later.',
            'retry_after' => $data['reset_time'] - $now
        ]);
        exit;
    }
    
    // Increment attempts
    $_RATE_LIMIT_STORAGE[$key]['attempts']++;
    return true;
}

/**
 * Validate CSRF token
 */
function validate_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        start_secure_session();
    }
    
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit;
    }
    
    return true;
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        start_secure_session();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Log security events
 */
function log_security_event($event_type, $details = []) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event_type,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'details' => $details
    ];
    
    error_log("SECURITY: " . json_encode($log_entry));
}

/**
 * Validate file upload
 */
function validate_file_upload($file, $allowed_types = ['image/jpeg', 'image/png', 'image/gif'], $max_size = 2097152) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error.'];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large. Max size: ' . ($max_size / 1024 / 1024) . 'MB'];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types, true)) {
        return ['success' => false, 'message' => 'Invalid file type.'];
    }
    
    // Additional check for images
    if (strpos($mime_type, 'image/') === 0) {
        $image_info = getimagesize($file['tmp_name']);
        if ($image_info === false) {
            return ['success' => false, 'message' => 'Invalid image file.'];
        }
    }
    
    return ['success' => true];
}

/**
 * Generate secure random string
 */
function generate_secure_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Hash password securely
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Prevent clickjacking
 */
function set_security_headers() {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    
    // Content Security Policy
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
           "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; " .
           "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; " .
           "img-src 'self' data: https:; " .
           "connect-src 'self';";
    
    header("Content-Security-Policy: $csp");
}
?>
