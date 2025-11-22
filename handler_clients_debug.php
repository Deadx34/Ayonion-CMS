<?php
// TEST VERSION - handler_clients_debug.php
// Use this to debug the 500 error

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

header('Content-Type: application/json');

try {
    // Step 1: Test include
    if (!file_exists('includes/config.php')) {
        throw new Exception('config.php not found');
    }
    
    include 'includes/config.php';
    
    // Step 2: Test database connection
    $conn = connect_db();
    
    // Step 3: Get action
    $action = $_GET['action'] ?? 'test';
    
    // Step 4: Test database query
    if ($action === 'test') {
        $result = $conn->query("SELECT COUNT(*) as count FROM clients");
        $row = $result->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'message' => 'Everything works!',
            'clients_count' => $row['count'],
            'php_version' => phpversion(),
            'mysqli_loaded' => extension_loaded('mysqli')
        ]);
        exit;
    }
    
    // Step 5: Test POST handling
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
        $rawInput = file_get_contents("php://input");
        $input = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }
        
        $id = time() . mt_rand(100, 999);
        $companyName = $conn->real_escape_string($input['companyName'] ?? 'Test Company');
        $partnerId = $conn->real_escape_string($input['partnerId'] ?? 'TEST001');
        
        // Simple insert test
        $sql = "INSERT INTO clients (
            id, partner_id, company_name, package_credits, 
            extra_credits, carried_forward_credits, used_credits, 
            total_ad_budget, total_spent
        ) VALUES (
            '$id', '$partnerId', '$companyName', 0, 0, 0, 0, 0.00, 0.00
        )";
        
        if ($conn->query($sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Client added successfully',
                'client_id' => $id
            ]);
        } else {
            throw new Exception('Database error: ' . $conn->error);
        }
        exit;
    }
    
    // Default response
    echo json_encode([
        'success' => false,
        'message' => 'Unknown action: ' . $action,
        'available_actions' => ['test', 'add']
    ]);
    
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error_type' => 'Database Error',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error_type' => 'Application Error',
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error_type' => 'PHP Error',
        'message' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
?>
