<?php
// Web-based migration script to add item_order field to documents table
header('Content-Type: text/html; charset=UTF-8');
include 'includes/config.php';

try {
    $conn = connect_db();
    
    echo "<h2>Migration: Adding Multiple Items Support to Financial Documents</h2>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px;'>";
    
    // Check if item_order column already exists
    $check_sql = "SHOW COLUMNS FROM documents LIKE 'item_order'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        echo "✓ item_order column already exists<br>";
    } else {
        // Add item_order column to documents table
        $sql1 = "ALTER TABLE documents ADD COLUMN item_order INT DEFAULT 0";
        if (query_db($conn, $sql1)) {
            echo "✓ Added item_order column<br>";
        } else {
            echo "✗ Failed to add item_order column: " . $conn->error . "<br>";
        }
    }
    
    // Update existing records to have item_order = 0
    $sql2 = "UPDATE documents SET item_order = 0 WHERE item_order IS NULL";
    if (query_db($conn, $sql2)) {
        echo "✓ Updated existing records with item_order = 0<br>";
    } else {
        echo "✗ Failed to update existing records: " . $conn->error . "<br>";
    }
    
    // Add index for better performance
    $sql3 = "CREATE INDEX IF NOT EXISTS idx_documents_id_order ON documents(id, item_order)";
    if (query_db($conn, $sql3)) {
        echo "✓ Added index for better performance<br>";
    } else {
        echo "✗ Failed to add index (may already exist): " . $conn->error . "<br>";
    }
    
    echo "<br><strong>Migration completed successfully!</strong><br>";
    echo "The financial management modal now supports multiple document items.<br>";
    echo "<br><a href='index.html'>← Back to Main Application</a>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; font-family: monospace; background: #ffe6e6; padding: 20px; border-radius: 5px;'>";
    echo "Migration failed: " . $e->getMessage();
    echo "</div>";
}

$conn->close();
?>
