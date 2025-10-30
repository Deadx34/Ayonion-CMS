<?php
// AYONION-CMS/handler_auto_carry_forward.php - Automatic Monthly Credit Carry Forward System
// This script should be run via cron job at the start of each month
// Cron example: 0 0 1 * * /usr/bin/php /path/to/handler_auto_carry_forward.php

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

// Configuration
define('DEFAULT_MONTHLY_CREDITS', 40);

// Check if this is a manual trigger or cron trigger
$manual_trigger = isset($_GET['manual']) && $_GET['manual'] === 'true';

try {
    // Get current date info
    $current_date = new DateTime();
    $current_month = $current_date->format('Y-m');
    $next_renewal_date = $current_date->modify('+1 month')->format('Y-m-d');
    
    // Log file for tracking
    $log_file = __DIR__ . '/logs/auto_carry_forward.log';
    $log_dir = dirname($log_file);
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_message = "\n" . date('Y-m-d H:i:s') . " - Auto Carry Forward Process Started\n";
    
    // Get all active clients whose renewal date has passed
    $today = date('Y-m-d');
    $clients_sql = "SELECT * FROM clients WHERE renewal_date <= '$today' ORDER BY id";
    $clients_result = $conn->query($clients_sql);
    
    if (!$clients_result) {
        throw new Exception("Failed to fetch clients: " . $conn->error);
    }
    
    $processed_count = 0;
    $error_count = 0;
    $results = [];
    
    // Start transaction
    $conn->begin_transaction();
    
    while ($client = $clients_result->fetch_assoc()) {
        try {
            $client_id = $client['id'];
            $client_name = $client['company_name'];
            
            // Calculate current available credits
            $total_credits = $client['package_credits'] + $client['extra_credits'] + $client['carried_forward_credits'];
            $available_credits = $total_credits - $client['used_credits'];
            
            // Auto carry forward: All unused credits carry forward
            $credits_to_carry = max(0, $available_credits);
            
            // Calculate new renewal date (1 month from current renewal date)
            $current_renewal = new DateTime($client['renewal_date']);
            $new_renewal = $current_renewal->modify('+1 month')->format('Y-m-d');
            
            // Update client with new cycle
            $update_sql = "UPDATE clients SET 
                package_credits = " . DEFAULT_MONTHLY_CREDITS . ",
                carried_forward_credits = $credits_to_carry,
                used_credits = 0,
                renewal_date = '$new_renewal',
                last_carry_forward = NOW()
                WHERE id = $client_id";
            
            if (!$conn->query($update_sql)) {
                throw new Exception("Failed to update client $client_id: " . $conn->error);
            }
            
            // Log the carry forward action
            $new_total = DEFAULT_MONTHLY_CREDITS + $credits_to_carry;
            $log_message .= "  ✓ Client: $client_name (ID: $client_id)\n";
            $log_message .= "    Previous: Used {$client['used_credits']} of $total_credits credits\n";
            $log_message .= "    Carried Forward: $credits_to_carry credits\n";
            $log_message .= "    New Cycle: " . DEFAULT_MONTHLY_CREDITS . " + $credits_to_carry = $new_total total credits\n";
            $log_message .= "    New Renewal Date: $new_renewal\n\n";
            
            $results[] = [
                'client_id' => $client_id,
                'client_name' => $client_name,
                'carried_forward' => $credits_to_carry,
                'new_total' => $new_total,
                'new_renewal_date' => $new_renewal,
                'status' => 'success'
            ];
            
            $processed_count++;
            
        } catch (Exception $e) {
            $error_count++;
            $log_message .= "  ✗ Error processing client $client_name (ID: $client_id): " . $e->getMessage() . "\n";
            $results[] = [
                'client_id' => $client_id,
                'client_name' => $client_name,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    $log_message .= "\nSummary:\n";
    $log_message .= "  Total Processed: $processed_count\n";
    $log_message .= "  Errors: $error_count\n";
    $log_message .= "  Process Completed: " . date('Y-m-d H:i:s') . "\n";
    $log_message .= str_repeat('-', 80) . "\n";
    
    // Write to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    echo json_encode([
        'success' => true,
        'message' => "Auto carry forward completed successfully. Processed $processed_count client(s).",
        'processed_count' => $processed_count,
        'error_count' => $error_count,
        'results' => $results,
        'log_message' => $log_message
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    
    $error_log = "\n" . date('Y-m-d H:i:s') . " - ERROR: " . $e->getMessage() . "\n";
    if (isset($log_file)) {
        file_put_contents($log_file, $error_log, FILE_APPEND);
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Auto carry forward failed: ' . $e->getMessage()
    ]);
}

if (isset($conn)) {
    $conn->close();
}
?>

