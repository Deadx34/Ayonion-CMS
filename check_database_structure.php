<?php
// Check database structure to see if migration is needed
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>Database Structure Check</h2>";
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
        echo "✓ item_order column EXISTS - Migration already completed<br>";
    } else {
        echo "✗ item_order column MISSING - Migration needed<br>";
        echo "<br><strong style='color: red;'>You need to run the migration first!</strong><br>";
        echo "<a href='migrate_now.php' style='color: #6366f1; text-decoration: none; font-weight: bold;'>→ Run Migration Now</a><br>";
    }
    
    // Show current documents table structure
    echo "<br><strong>Current documents table structure:</strong><br>";
    $structure_sql = "DESCRIBE documents";
    $structure_result = $conn->query($structure_sql);
    
    if ($structure_result) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $structure_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffe6e6; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "Error: " . $e->getMessage();
    echo "</div>";
}

$conn->close();
echo "</div>";
?>
