<?php
// Check if database has required columns for Auto Carry Forward System

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

try {
    // Check if new columns exist
    $sql = "SHOW COLUMNS FROM clients LIKE 'subscription_months'";
    $result = query_db($conn, $sql);
    
    if ($result && $result->num_rows > 0) {
        // Check all required columns
        $requiredColumns = ['subscription_months', 'subscription_start_date', 'subscription_end_date', 'last_carry_forward_date'];
        $existingColumns = [];
        
        foreach ($requiredColumns as $column) {
            $sql = "SHOW COLUMNS FROM clients LIKE '$column'";
            $result = query_db($conn, $sql);
            if ($result && $result->num_rows > 0) {
                $existingColumns[] = $column;
            }
        }
        
        if (count($existingColumns) === count($requiredColumns)) {
            echo json_encode([
                'success' => true,
                'message' => 'All required columns exist',
                'columns' => $existingColumns
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Missing columns: ' . implode(', ', array_diff($requiredColumns, $existingColumns)),
                'existing' => $existingColumns
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Migration not run yet. Please run migrate_subscription_tracking.sql'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking database: ' . $e->getMessage()
    ]);
}
?>
