<?php
/**
 * Migration Script: Add sub_text column to documents table
 *
 * This script adds an optional sub_text column used by
 * Quotation, Invoice, and Receipt records.
 *
 * Run this script once to apply the migration.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Documents Table Migration</h2>";
echo "<p>Adding optional <code>sub_text</code> column...</p>";

try {
    require_once 'includes/config.php';
    $conn = connect_db();

    echo "<p>✓ Connected to database successfully</p>";

    // Check whether sub_text column already exists
    $checkSql = "SHOW COLUMNS FROM documents LIKE 'sub_text'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows > 0) {
        echo "<p style='color: blue;'>ℹ Column <code>sub_text</code> already exists. No changes needed.</p>";
    } else {
        $alterSql = "ALTER TABLE documents ADD COLUMN sub_text VARCHAR(255) NULL AFTER description";

        if ($conn->query($alterSql)) {
            echo "<p style='color: green;'>✓ Successfully added <code>sub_text</code> column</p>";
        } else {
            throw new Exception("Failed to alter documents table: " . $conn->error);
        }
    }

    echo "<hr>";
    echo "<h3 style='color: green;'>✓ Migration completed successfully!</h3>";
    echo "<p>The system now supports optional sub text for Receipt, Quotation, and Invoice records.</p>";
    echo "<p><a href='index.php'>← Return to Application</a></p>";

    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection and try again.</p>";
}
?>
