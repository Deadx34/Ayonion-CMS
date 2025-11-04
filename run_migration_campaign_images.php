<?php
// Run Campaign Images Migration
// This script adds evidence_image_url and creative_image_url columns to campaigns table

include 'includes/config.php';

try {
    $conn = connect_db();
    
    echo "<h2>Campaign Images Migration</h2>";
    echo "<p>Adding image fields to campaigns table...</p>";
    
    // Add evidence_image_url column
    $sql1 = "ALTER TABLE campaigns ADD COLUMN IF NOT EXISTS evidence_image_url TEXT DEFAULT NULL";
    if ($conn->query($sql1)) {
        echo "<p>✅ Added evidence_image_url column</p>";
    } else {
        echo "<p>⚠️ evidence_image_url column: " . $conn->error . "</p>";
    }
    
    // Add creative_image_url column
    $sql2 = "ALTER TABLE campaigns ADD COLUMN IF NOT EXISTS creative_image_url TEXT DEFAULT NULL";
    if ($conn->query($sql2)) {
        echo "<p>✅ Added creative_image_url column</p>";
    } else {
        echo "<p>⚠️ creative_image_url column: " . $conn->error . "</p>";
    }
    
    echo "<h3>Migration Complete!</h3>";
    echo "<p><a href='index.html'>Go to Application</a></p>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
