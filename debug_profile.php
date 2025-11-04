<?php
// Debug version of profile handler
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/profile_debug.log');

header('Content-Type: application/json');

try {
    // Start session first
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Log session info
    error_log("=== Profile Debug ===");
    error_log("Session ID: " . session_id());
    error_log("Logged in: " . (isset($_SESSION['loggedin']) ? 'yes' : 'no'));
    error_log("User ID: " . ($_SESSION['user_id'] ?? 'not set'));
    error_log("Username: " . ($_SESSION['username'] ?? 'not set'));
    error_log("Role: " . ($_SESSION['role'] ?? 'not set'));
    
    // Check authentication
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        throw new Exception("Authentication required. Session loggedin: " . var_export($_SESSION['loggedin'] ?? null, true));
    }
    
    // Include config
    if (!file_exists('includes/config.php')) {
        throw new Exception("Config file not found");
    }
    
    include 'includes/config.php';
    $conn = connect_db();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    error_log("Database connected successfully");
    
    $userId = (int)($_SESSION['user_id'] ?? 0);
    
    if ($userId === 0) {
        throw new Exception("Invalid user ID");
    }
    
    error_log("Fetching profile for user ID: " . $userId);
    
    // Try to get profile with new columns, fallback to basic if columns don't exist
    $sql = "SELECT username, role FROM users WHERE id = {$userId} LIMIT 1";
    error_log("SQL Query: " . $sql);
    
    $result = $conn->query($sql);
    
    if (!$result) {
        error_log("Query failed: " . $conn->error);
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    if ($row = $result->fetch_assoc()) {
        error_log("Basic profile found: " . json_encode($row));
        
        $profile = [
            'username' => $row['username'],
            'role' => $row['role'],
            'full_name' => null,
            'email' => null
        ];
        
        // Check if full_name and email columns exist
        $checkSql = "SELECT full_name, email FROM users WHERE id = {$userId} LIMIT 1";
        error_log("Checking extended fields: " . $checkSql);
        
        $checkResult = $conn->query($checkSql);
        
        if ($checkResult && $checkRow = $checkResult->fetch_assoc()) {
            error_log("Extended fields found: " . json_encode($checkRow));
            $profile['full_name'] = $checkRow['full_name'];
            $profile['email'] = $checkRow['email'];
        } else {
            error_log("Extended fields not available: " . ($conn->error ?: 'Columns may not exist'));
        }
        
        $response = [
            "success" => true,
            "profile" => $profile
        ];
        
        error_log("Returning response: " . json_encode($response));
        
        echo json_encode($response);
    } else {
        error_log("No user found with ID: " . $userId);
        throw new Exception("Could not fetch profile - user not found");
    }
    
} catch (Exception $e) {
    error_log("ERROR: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
}
?>
