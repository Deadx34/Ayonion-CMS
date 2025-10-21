<?php
// Test script to verify invoice tables are created correctly
include 'includes/config.php';

try {
    $conn = connect_db();
    
    // Read and execute the invoice table creation SQL
    $sql = file_get_contents('create_invoice_tables.sql');
    
    if ($conn->multi_query($sql)) {
        echo "✅ Invoice tables created successfully!\n";
        
        // Verify tables exist
        $tables = ['invoices', 'invoice_items'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "✅ Table '$table' exists\n";
            } else {
                echo "❌ Table '$table' not found\n";
            }
        }
        
        // Show table structure
        echo "\n📋 Invoice table structure:\n";
        $result = $conn->query("DESCRIBE invoices");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo "  - {$row['Field']}: {$row['Type']}\n";
            }
        }
        
        echo "\n📋 Invoice_items table structure:\n";
        $result = $conn->query("DESCRIBE invoice_items");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo "  - {$row['Field']}: {$row['Type']}\n";
            }
        }
        
    } else {
        echo "❌ Error creating tables: " . $conn->error . "\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
