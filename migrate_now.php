<?php
// Simple migration script to add item_order field
// Run this by visiting: http://localhost/ayonion-cms/migrate_now.php

echo "<h2>Database Migration: Adding Multiple Items Support</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px;'>";

// Database connection parameters
$servername = "sql104.byethost33.com";
$username = "b33_40185301";
$password = "123456";
$dbname = "b33_40185301_epiz_12345678_ayoniondb";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✓ Connected to database successfully<br><br>";
    
    // Check if item_order column already exists
    $check_sql = "SHOW COLUMNS FROM documents LIKE 'item_order'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        echo "✓ item_order column already exists<br>";
    } else {
        // Add item_order column to documents table
        $sql1 = "ALTER TABLE documents ADD COLUMN item_order INT DEFAULT 0";
        if ($conn->query($sql1) === TRUE) {
            echo "✓ Added item_order column<br>";
        } else {
            echo "✗ Failed to add item_order column: " . $conn->error . "<br>";
        }
    }
    
    // Update existing records to have item_order = 0
    $sql2 = "UPDATE documents SET item_order = 0 WHERE item_order IS NULL";
    if ($conn->query($sql2) === TRUE) {
        echo "✓ Updated existing records with item_order = 0<br>";
    } else {
        echo "✗ Failed to update existing records: " . $conn->error . "<br>";
    }
    
    // Add index for better performance
    $sql3 = "CREATE INDEX IF NOT EXISTS idx_documents_id_order ON documents(id, item_order)";
    if ($conn->query($sql3) === TRUE) {
        echo "✓ Added index for better performance<br>";
    } else {
        echo "✗ Failed to add index (may already exist): " . $conn->error . "<br>";
    }
    
    echo "<br><strong style='color: green;'>Migration completed successfully!</strong><br>";
    echo "The financial management modal now supports multiple document items.<br>";
    echo "<br><a href='index.html' style='color: #6366f1; text-decoration: none; font-weight: bold;'>← Back to Main Application</a>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "Migration failed: " . $e->getMessage();
    echo "</div>";
}

$conn->close();
echo "</div>";
?>
