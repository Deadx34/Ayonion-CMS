<?php
// Migration script to add item_order field to documents table
include 'includes/config.php';

try {
    $conn = connect_db();
    
    echo "Starting migration to add item_order field...\n";
    
    // Add item_order column to documents table
    $sql1 = "ALTER TABLE documents ADD COLUMN item_order INT DEFAULT 0";
    if (query_db($conn, $sql1)) {
        echo "✓ Added item_order column\n";
    } else {
        echo "✗ Failed to add item_order column\n";
    }
    
    // Update existing records to have item_order = 0
    $sql2 = "UPDATE documents SET item_order = 0 WHERE item_order IS NULL";
    if (query_db($conn, $sql2)) {
        echo "✓ Updated existing records with item_order = 0\n";
    } else {
        echo "✗ Failed to update existing records\n";
    }
    
    // Add index for better performance
    $sql3 = "CREATE INDEX idx_documents_id_order ON documents(id, item_order)";
    if (query_db($conn, $sql3)) {
        echo "✓ Added index for better performance\n";
    } else {
        echo "✗ Failed to add index (may already exist)\n";
    }
    
    echo "\nMigration completed successfully!\n";
    echo "The financial management modal now supports multiple document items.\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

$conn->close();
?>
