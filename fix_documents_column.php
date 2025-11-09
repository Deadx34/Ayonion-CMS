<?php
/**
 * Migration Script: Fix documents table item_type column
 * 
 * This script changes the item_type column from VARCHAR(255) to TEXT
 * to properly store large JSON arrays with multiple items and descriptions.
 * 
 * Run this script once to apply the fix to your existing database.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Documents Table Migration</h2>";
echo "<p>Fixing item_type column to support larger JSON data...</p>";

try {
    require_once 'includes/config.php';
    $conn = connect_db();
    
    echo "<p>✓ Connected to database successfully</p>";
    
    // Check current column type
    $checkSql = "SHOW COLUMNS FROM documents WHERE Field = 'item_type'";
    $result = $conn->query($checkSql);
    
    if ($result && $result->num_rows > 0) {
        $column = $result->fetch_assoc();
        echo "<p><strong>Current column type:</strong> {$column['Type']}</p>";
        
        // Only alter if not already TEXT
        if (stripos($column['Type'], 'text') === false) {
            echo "<p>Altering column type to TEXT...</p>";
            
            // Alter the column
            $alterSql = "ALTER TABLE documents MODIFY COLUMN item_type TEXT";
            
            if ($conn->query($alterSql)) {
                echo "<p style='color: green;'>✓ Successfully changed item_type column to TEXT</p>";
                
                // Verify the change
                $verifyResult = $conn->query($checkSql);
                if ($verifyResult && $verifyResult->num_rows > 0) {
                    $newColumn = $verifyResult->fetch_assoc();
                    echo "<p><strong>New column type:</strong> {$newColumn['Type']}</p>";
                }
            } else {
                throw new Exception("Failed to alter column: " . $conn->error);
            }
        } else {
            echo "<p style='color: blue;'>ℹ Column is already TEXT type. No changes needed.</p>";
        }
    } else {
        throw new Exception("Could not find item_type column in documents table");
    }
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✓ Migration completed successfully!</h3>";
    echo "<p>You can now create documents with multiple items and descriptions without truncation.</p>";
    echo "<p><a href='index.php'>← Return to Application</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>
