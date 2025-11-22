<?php
/**
 * PHP Configuration for Production Environment
 * Add this at the top of ALL handler files
 */

// Enable error logging but hide errors from output
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Hide errors from users
ini_set('log_errors', 1);       // Log errors to file
ini_set('error_log', __DIR__ . '/error_log.txt');

// Catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Server error occurred',
            'error' => $error['message'],
            'file' => basename($error['file']),
            'line' => $error['line']
        ]);
    }
});

// Set JSON header by default
if (!headers_sent()) {
    header('Content-Type: application/json');
}
?>
