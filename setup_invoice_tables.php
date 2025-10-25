<?php
// Setup invoice tables via web interface
header('Content-Type: text/plain');

include 'includes/config.php';

try {
    $conn = connect_db();
    
    // Read the SQL file
    $sql = file_get_contents('create_invoice_tables.sql');
    
    if ($conn->multi_query($sql)) {
        echo "Invoice tables created successfully.\n\n";
        
        // Check if tables exist
        $result = $conn->query("SHOW TABLES LIKE 'invoices'");
        if ($result && $result->num_rows > 0) {
            echo "✓ invoices table exists\n";
        } else {
            echo "✗ invoices table not found\n";
        }
        
        $result = $conn->query("SHOW TABLES LIKE 'invoice_items'");
        if ($result && $result->num_rows > 0) {
            echo "✓ invoice_items table exists\n";
        } else {
            echo "✗ invoice_items table not found\n";
        }
        
        // Show table structure
        echo "\n--- invoices table structure ---\n";
        $result = $conn->query("DESCRIBE invoices");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo $row['Field'] . " - " . $row['Type'] . "\n";
            }
        }
        
        echo "\n--- invoice_items table structure ---\n";
        $result = $conn->query("DESCRIBE invoice_items");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo $row['Field'] . " - " . $row['Type'] . "\n";
            }
        }
        
    } else {
        echo "Error creating tables: " . $conn->error . "\n";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
