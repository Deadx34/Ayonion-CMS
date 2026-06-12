<?php
/**
 * AYONION-CMS Database Migration Runner
 * Runs pending migrations from the database folder
 */

header('Content-Type: application/json');
include 'includes/config.php';

try {
    $conn = connect_db();
    
    // List of migrations to run
    $migrations = [
        'database/migrate_add_non_customer_invoice_columns.sql'
    ];
    
    $results = [];
    
    foreach ($migrations as $migrationFile) {
        if (!file_exists($migrationFile)) {
            $results[] = [
                'file' => $migrationFile,
                'status' => 'skipped',
                'message' => 'Migration file not found'
            ];
            continue;
        }
        
        $sql = file_get_contents($migrationFile);
        
        // Split multiple statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        try {
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    if (!$conn->query($statement)) {
                        throw new Exception($conn->error);
                    }
                }
            }
            
            $results[] = [
                'file' => $migrationFile,
                'status' => 'success',
                'message' => 'Migration completed successfully'
            ];
        } catch (Exception $e) {
            $results[] = [
                'file' => $migrationFile,
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Migration runner completed',
        'results' => $results
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Migration error: ' . $e->getMessage()
    ]);
}
?>
