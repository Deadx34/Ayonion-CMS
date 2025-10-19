<?php
// AYONION-CMS/migrate_database.php - Web-accessible database migration

header('Content-Type: application/json');
include 'includes/config.php';

try {
    $conn = connect_db();
    
    // Check if columns already exist
    $checkColumns = "SHOW COLUMNS FROM content_credits LIKE 'content_url'";
    $result = $conn->query($checkColumns);
    
    if ($result && $result->num_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Migration already completed. All columns exist.',
            'status' => 'already_done'
        ]);
        exit;
    }
    
    // Run the migration
    $migrationSQL = "
        ALTER TABLE content_credits 
        ADD COLUMN content_url VARCHAR(500) DEFAULT NULL,
        ADD COLUMN image_url VARCHAR(500) DEFAULT NULL,
        ADD COLUMN status VARCHAR(50) DEFAULT 'In Progress',
        ADD COLUMN published_date DATE DEFAULT NULL
    ";
    
    if ($conn->query($migrationSQL)) {
        // Update existing records
        $updateSQL = "UPDATE content_credits SET status = 'In Progress' WHERE status IS NULL";
        $conn->query($updateSQL);
        
        echo json_encode([
            'success' => true,
            'message' => 'Migration completed successfully! Added content_url, image_url, status, and published_date columns.',
            'status' => 'completed'
        ]);
    } else {
        throw new Exception("Migration failed: " . $conn->error);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Migration error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
