<?php
// Run User Profile Migration
// This script adds full_name and email columns to the users table

include 'includes/config.php';

try {
    $conn = connect_db();
    
    echo "<h2>User Profile Migration</h2>";
    echo "<p>Adding profile fields to users table...</p>";
    
    // Add full_name column
    $sql1 = "ALTER TABLE users ADD COLUMN IF NOT EXISTS full_name VARCHAR(255) DEFAULT NULL";
    if ($conn->query($sql1)) {
        echo "<p>✅ Added full_name column</p>";
    } else {
        echo "<p>⚠️ full_name column: " . $conn->error . "</p>";
    }
    
    // Add email column
    $sql2 = "ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL";
    if ($conn->query($sql2)) {
        echo "<p>✅ Added email column</p>";
    } else {
        echo "<p>⚠️ email column: " . $conn->error . "</p>";
    }
    
    // Add index
    $sql3 = "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)";
    if ($conn->query($sql3)) {
        echo "<p>✅ Created email index</p>";
    } else {
        echo "<p>⚠️ email index: " . $conn->error . "</p>";
    }
    
    echo "<h3>Migration Complete!</h3>";
    echo "<p><a href='index.html'>Go to Application</a></p>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
