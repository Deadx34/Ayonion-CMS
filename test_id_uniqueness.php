<?php
// Test ID generation uniqueness
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>ID Generation Uniqueness Test</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px;'>";

echo "<strong>Testing ID generation for multiple item types:</strong><br><br>";

// Generate 10 test base IDs
$baseIds = [];
for ($i = 0; $i < 10; $i++) {
    $baseId = time() . mt_rand(10000, 99999);
    $baseIds[] = $baseId;
    echo "Base ID $i: $baseId<br>";
}

// Check for duplicates
$uniqueIds = array_unique($baseIds);
if (count($baseIds) === count($uniqueIds)) {
    echo "<br>✓ All base IDs are unique!<br>";
} else {
    echo "<br>✗ Found duplicate base IDs!<br>";
}

// Test document ID generation for multiple item types
echo "<br><strong>Testing document ID generation:</strong><br>";
$testBaseId = time() . mt_rand(10000, 99999);
$itemTypes = ['Monthly Payment', 'Ad Budget', 'Extra Content Credits'];

foreach ($itemTypes as $index => $itemType) {
    $documentId = $testBaseId . '_' . $index;
    echo "Item $index ($itemType): $documentId<br>";
}

echo "<br><strong>Current timestamp:</strong> " . time() . "<br>";
echo "<strong>Random range:</strong> 10000-99999 (5 digits)<br>";
echo "<strong>Total ID length:</strong> " . strlen($testBaseId) . " characters<br>";

echo "</div>";
?>
