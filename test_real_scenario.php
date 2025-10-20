<?php
// Test the exact same scenario as the real application
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>Real Scenario Test - Multiple Items</h2>";
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
    
    // Simulate the exact same process as handler_finance.php
    echo "<strong>Simulating real document creation process:</strong><br><br>";
    
    // Generate ID using the same method as the application
    $id = time() . mt_rand(100000, 999999);
    echo "Generated ID: $id<br>";
    
    // Double-check for uniqueness (same as application)
    $check_sql = "SELECT COUNT(*) as count FROM documents WHERE id = '$id'";
    $check_result = $conn->query($check_sql);
    if ($check_result && $check_result->fetch_assoc()['count'] > 0) {
        $id = $id . mt_rand(1000, 9999);
        echo "ID updated after collision check: $id<br>";
    }
    
    $clientId = 1; // Assuming client ID 1 exists
    $docType = 'quotation';
    $date = date('Y-m-d');
    $clientName = 'Test Client';
    
    // Test items (same structure as frontend sends)
    $items = [
        [
            'itemType' => 'Monthly Payment',
            'description' => 'Monthly subscription fee',
            'quantity' => 1,
            'unitPrice' => 1000.00,
            'total' => 1000.00
        ],
        [
            'itemType' => 'Extra Content Credits',
            'description' => 'Additional content creation',
            'quantity' => 5,
            'unitPrice' => 50.00,
            'total' => 250.00
        ],
        [
            'itemType' => 'Ad Budget',
            'description' => 'Facebook advertising budget',
            'quantity' => 1,
            'unitPrice' => 500.00,
            'total' => 500.00
        ]
    ];
    
    echo "<br>Items to create: " . count($items) . "<br><br>";
    
    // Start transaction (same as application)
    $conn->begin_transaction();
    
    try {
        // Insert each item as line items in the same document
        foreach ($items as $index => $item) {
            $itemType = $conn->real_escape_string($item['itemType']);
            $description = $conn->real_escape_string($item['description']);
            $quantity = (int)$item['quantity'];
            $unitPrice = (float)$item['unitPrice'];
            $total = (float)$item['total'];
            
            // Create line item ID for each item
            $lineItemId = $id . '_line_' . $index;
            echo "Creating line item $index with ID: $lineItemId<br>";
            
            // Check if item_order column exists
            $check_column_sql = "SHOW COLUMNS FROM documents LIKE 'item_order'";
            $column_check = $conn->query($check_column_sql);
            
            if ($column_check->num_rows > 0) {
                $sql = "INSERT INTO documents 
                    (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date, item_order) 
                    VALUES 
                    ('$lineItemId', $clientId, '$clientName', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date', $index)";
            } else {
                $sql = "INSERT INTO documents 
                    (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
                    VALUES 
                    ('$lineItemId', $clientId, '$clientName', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date')";
            }
            
            if ($conn->query($sql) === TRUE) {
                echo "✓ Item $index created successfully<br>";
            } else {
                throw new Exception("Failed to create item $index: " . $conn->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        echo "<br>✓ Transaction committed successfully<br>";
        
        // Test document retrieval
        echo "<br><strong>Testing document retrieval:</strong><br>";
        $docIdPattern = $id . '_line_%';
        $retrieve_sql = "SELECT * FROM documents WHERE id = '$id' OR id LIKE '$docIdPattern' ORDER BY item_order ASC";
        $retrieve_result = $conn->query($retrieve_sql);
        
        if ($retrieve_result) {
            echo "✓ Retrieved " . $retrieve_result->num_rows . " items<br>";
            while ($row = $retrieve_result->fetch_assoc()) {
                echo "- Item: " . $row['item_type'] . " | Total: Rs. " . number_format($row['total'], 2) . "<br>";
            }
        }
        
        // Clean up test data
        $cleanup_sql = "DELETE FROM documents WHERE id = '$id' OR id LIKE '$docIdPattern'";
        if ($conn->query($cleanup_sql) === TRUE) {
            echo "<br>✓ Test data cleaned up<br>";
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<br>✗ Transaction rolled back: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "Error: " . $e->getMessage();
    echo "</div>";
}

$conn->close();
echo "</div>";
?>
