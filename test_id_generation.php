<?php
// Test ID generation to ensure uniqueness
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>ID Generation Test</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px;'>";

echo "<strong>Testing ID generation:</strong><br><br>";

// Generate 10 test IDs
$ids = [];
for ($i = 0; $i < 10; $i++) {
    $id = (int)(microtime(true) * 1000000) . mt_rand(100000, 999999);
    $ids[] = $id;
    echo "ID $i: $id<br>";
}

// Check for duplicates
$unique_ids = array_unique($ids);
if (count($ids) === count($unique_ids)) {
    echo "<br>✓ All IDs are unique!<br>";
} else {
    echo "<br>✗ Found duplicate IDs!<br>";
}

echo "<br><strong>Current timestamp:</strong> " . microtime(true) . "<br>";
echo "<strong>Current time:</strong> " . time() . "<br>";

echo "</div>";
?>
