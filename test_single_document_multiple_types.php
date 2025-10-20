<?php
// Test single document with multiple item types
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>Single Document with Multiple Item Types Test</h2>";
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
    
    // Test creating a single document with multiple item types
    echo "<strong>Testing single document with multiple item types:</strong><br><br>";
    
    $id = time() . mt_rand(100, 999);
    $clientId = 1; // Assuming client ID 1 exists
    $docType = 'quotation';
    $date = date('Y-m-d');
    $clientName = 'Test Client';
    $description = 'Test description for multiple item types';
    $quantity = 1;
    $unitPrice = 100.00;
    $total = $quantity * $unitPrice;
    
    // Test multiple item types
    $itemTypes = ['Monthly Payment', 'Ad Budget', 'Extra Content Credits'];
    $itemTypesJson = json_encode($itemTypes);
    
    echo "Document ID: $id<br>";
    echo "Item types: " . implode(', ', $itemTypes) . "<br>";
    echo "JSON: $itemTypesJson<br><br>";
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create single document with multiple item types
        $sql = "INSERT INTO documents 
            (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
            VALUES 
            ('$id', $clientId, '$clientName', '$docType', '$itemTypesJson', '$description', $quantity, $unitPrice, $total, '$date')";
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ Document created successfully<br>";
        } else {
            throw new Exception("Failed to create document: " . $conn->error);
        }
        
        // Commit transaction
        $conn->commit();
        echo "<br>✓ Transaction committed successfully<br>";
        
        // Test retrieval
        echo "<br><strong>Testing document retrieval:</strong><br>";
        $retrieve_sql = "SELECT * FROM documents WHERE id = '$id'";
        $retrieve_result = $conn->query($retrieve_sql);
        
        if ($retrieve_result && $retrieve_result->num_rows > 0) {
            $row = $retrieve_result->fetch_assoc();
            echo "✓ Retrieved document successfully<br>";
            echo "Document ID: " . $row['id'] . "<br>";
            echo "Item Types (JSON): " . $row['item_type'] . "<br>";
            
            // Test JSON decoding
            $decodedTypes = json_decode($row['item_type'], true);
            if (is_array($decodedTypes)) {
                echo "✓ JSON decoded successfully<br>";
                echo "Decoded types: " . implode(', ', $decodedTypes) . "<br>";
            } else {
                echo "✗ JSON decode failed<br>";
            }
        }
        
        // Test PDF generation
        echo "<br><strong>Testing PDF generation:</strong><br>";
        include 'simple_pdf.php';
        $settings = ['company_name' => 'Test Company', 'email' => 'test@example.com'];
        $testDoc = [
            'id' => $id,
            'doc_type' => $docType,
            'item_type' => $itemTypesJson,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $total,
            'date' => $date,
            'company_name' => $clientName,
            'partner_id' => 'TEST123'
        ];
        
        $htmlContent = createPDFDocument($testDoc, $settings);
        if (strpos($htmlContent, 'Monthly Payment') !== false && strpos($htmlContent, 'Ad Budget') !== false) {
            echo "✓ PDF generation includes multiple item types<br>";
        } else {
            echo "✗ PDF generation may not include all item types<br>";
        }
        
        // Clean up test data
        $cleanup_sql = "DELETE FROM documents WHERE id = '$id'";
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
