<?php
// DEBUG VERSION - handler_clients.php
// This will show detailed errors

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json');

try {
    include 'includes/config.php';
    $conn = connect_db();
    
    $action = $_GET['action'] ?? '';
    
    // Test database connection
    if ($action === 'test') {
        echo json_encode([
            'success' => true,
            'message' => 'Connection works',
            'php_version' => phpversion(),
            'mysqli_available' => extension_loaded('mysqli')
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Include successful, action: ' . $action
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
