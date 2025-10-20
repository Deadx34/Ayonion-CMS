<?php
// Test script to check if multiple items feature works
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>Multiple Items Feature Test</h2>";
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
    
    // Check if item_order column exists
    $check_sql = "SHOW COLUMNS FROM documents LIKE 'item_order'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        echo "✓ item_order column EXISTS<br>";
    } else {
        echo "✗ item_order column MISSING<br>";
        echo "<br><strong style='color: red;'>MIGRATION NEEDED!</strong><br>";
        echo "<a href='migrate_now.php' style='color: #6366f1; text-decoration: none; font-weight: bold;'>→ Run Migration Now</a><br><br>";
    }
    
    // Test creating a document with multiple items
    echo "<br><strong>Testing document creation with multiple items:</strong><br>";
    
    $testId = time() . '_test';
    $clientId = 1; // Assuming client ID 1 exists
    $docType = 'quotation';
    $date = date('Y-m-d');
    
    // Test items
    $testItems = [
        [
            'itemType' => 'Monthly Payment',
            'description' => 'Test Monthly Payment',
            'quantity' => 1,
            'unitPrice' => 1000.00,
            'total' => 1000.00
        ],
        [
            'itemType' => 'Extra Content Credits',
            'description' => 'Test Extra Credits',
            'quantity' => 5,
            'unitPrice' => 50.00,
            'total' => 250.00
        ]
    ];
    
    echo "Test ID: $testId<br>";
    echo "Items to create: " . count($testItems) . "<br><br>";
    
    // Try to insert test items
    foreach ($testItems as $index => $item) {
        $itemId = $testId . '_' . $index;
        $itemType = $conn->real_escape_string($item['itemType']);
        $description = $conn->real_escape_string($item['description']);
        $quantity = (int)$item['quantity'];
        $unitPrice = (float)$item['unitPrice'];
        $total = (float)$item['total'];
        
        // Check if item_order column exists
        $check_column_sql = "SHOW COLUMNS FROM documents LIKE 'item_order'";
        $column_check = $conn->query($check_column_sql);
        
        if ($column_check->num_rows > 0) {
            $sql = "INSERT INTO documents 
                (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date, item_order) 
                VALUES 
                ('$itemId', $clientId, 'Test Client', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date', $index)";
        } else {
            $sql = "INSERT INTO documents 
                (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
                VALUES 
                ('$itemId', $clientId, 'Test Client', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date')";
        }
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ Item $index created successfully<br>";
        } else {
            echo "✗ Failed to create item $index: " . $conn->error . "<br>";
        }
    }
    
    // Clean up test data
    $cleanup_sql = "DELETE FROM documents WHERE id LIKE '$testId%'";
    if ($conn->query($cleanup_sql) === TRUE) {
        echo "<br>✓ Test data cleaned up<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "Error: " . $e->getMessage();
    echo "</div>";
}

$conn->close();
echo "</div>";
?>
