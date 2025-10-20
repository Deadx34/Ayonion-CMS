<?php
// Test script for multiple item types functionality
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>Multiple Item Types Test</h2>";
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
    
    // Test creating documents with multiple item types
    echo "<strong>Testing multiple item types functionality:</strong><br><br>";
    
    $baseId = time() . mt_rand(10000, 99999);
    $clientId = 1; // Assuming client ID 1 exists
    $docType = 'quotation';
    $date = date('Y-m-d');
    $clientName = 'Test Client';
    $description = 'Test description for multiple item types';
    $quantity = 1;
    $unitPrice = 100.00;
    $total = $quantity * $unitPrice;
    
    // Test item types
    $itemTypes = ['Monthly Payment', 'Ad Budget', 'Extra Content Credits'];
    
    echo "Base ID: $baseId<br>";
    echo "Item types to create: " . implode(', ', $itemTypes) . "<br><br>";
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create documents for each item type
        foreach ($itemTypes as $index => $itemType) {
            $documentId = $baseId . '_' . $index;
            $itemTypeEscaped = $conn->real_escape_string($itemType);
            
            $sql = "INSERT INTO documents 
                (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
                VALUES 
                ('$documentId', $clientId, '$clientName', '$docType', '$itemTypeEscaped', '$description', $quantity, $unitPrice, $total, '$date')";
            
            if ($conn->query($sql) === TRUE) {
                echo "✓ Created document for: $itemType (ID: $documentId)<br>";
            } else {
                throw new Exception("Failed to create document for $itemType: " . $conn->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        echo "<br>✓ All documents created successfully<br>";
        
        // Test retrieval
        echo "<br><strong>Testing document retrieval:</strong><br>";
        $retrieve_sql = "SELECT * FROM documents WHERE id = '$baseId' OR id LIKE '$baseId\_%' ORDER BY id";
        $retrieve_result = $conn->query($retrieve_sql);
        
        if ($retrieve_result) {
            echo "✓ Retrieved " . $retrieve_result->num_rows . " documents<br>";
            while ($row = $retrieve_result->fetch_assoc()) {
                echo "- Document: " . $row['id'] . " | Item Type: " . $row['item_type'] . " | Total: Rs. " . number_format($row['total'], 2) . "<br>";
            }
        }
        
        // Test deletion
        echo "<br><strong>Testing document deletion:</strong><br>";
        $delete_sql = "DELETE FROM documents WHERE id = '$baseId' OR id LIKE '$baseId\_%'";
        if ($conn->query($delete_sql) === TRUE) {
            echo "✓ All test documents deleted successfully<br>";
        } else {
            echo "✗ Failed to delete test documents: " . $conn->error . "<br>";
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
