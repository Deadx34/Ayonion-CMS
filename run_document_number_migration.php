<?php
// Run document_number column migration
require_once 'includes/config.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $conn = connect_db();
    
    echo "<h2>Document Number Column Migration</h2>";
    echo "<hr>";
    
    // Check if column already exists
    $check = $conn->query("SHOW COLUMNS FROM documents LIKE 'document_number'");
    
    if ($check && $check->num_rows > 0) {
        echo "<p style='color: orange;'>✓ Column 'document_number' already exists. Skipping column creation.</p>";
    } else {
        echo "<p>Adding 'document_number' column...</p>";
        
        // Add the column
        $sql1 = "ALTER TABLE documents ADD COLUMN document_number VARCHAR(50) NULL AFTER id";
        if ($conn->query($sql1)) {
            echo "<p style='color: green;'>✓ Column 'document_number' added successfully!</p>";
        } else {
            echo "<p style='color: red;'>✗ Error adding column: " . $conn->error . "</p>";
        }
    }
    
    // Check for unique constraint
    $checkUnique = $conn->query("SHOW INDEXES FROM documents WHERE Key_name = 'document_number'");
    
    if ($checkUnique && $checkUnique->num_rows > 0) {
        echo "<p style='color: orange;'>✓ Unique constraint on 'document_number' already exists.</p>";
    } else {
        echo "<p>Adding unique constraint...</p>";
        
        $sql2 = "ALTER TABLE documents ADD UNIQUE KEY `document_number` (`document_number`)";
        if ($conn->query($sql2)) {
            echo "<p style='color: green;'>✓ Unique constraint added successfully!</p>";
        } else {
            echo "<p style='color: red;'>✗ Error adding unique constraint: " . $conn->error . "</p>";
        }
    }
    
    // Check for idx_doc_type_date index
    $checkIndex = $conn->query("SHOW INDEXES FROM documents WHERE Key_name = 'idx_doc_type_date'");
    
    if ($checkIndex && $checkIndex->num_rows > 0) {
        echo "<p style='color: orange;'>✓ Index 'idx_doc_type_date' already exists.</p>";
    } else {
        echo "<p>Creating index for document type and date...</p>";
        
        $sql3 = "CREATE INDEX idx_doc_type_date ON documents(doc_type, date)";
        if ($conn->query($sql3)) {
            echo "<p style='color: green;'>✓ Index 'idx_doc_type_date' created successfully!</p>";
        } else {
            echo "<p style='color: red;'>✗ Error creating index: " . $conn->error . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3 style='color: green;'>Migration Complete! ✓</h3>";
    echo "<p>You can now close this window and refresh your application.</p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Migration failed: " . $e->getMessage() . "</p>";
}
?>
