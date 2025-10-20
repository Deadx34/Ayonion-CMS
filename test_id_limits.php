<?php
// Test ID generation limits
header('Content-Type: text/html; charset=UTF-8');

echo "<h2>ID Generation Limits Test</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px;'>";

echo "<strong>Database Limits:</strong><br>";
echo "BIGINT maximum value: 9223372036854775807<br>";
echo "BIGINT minimum value: -9223372036854775808<br><br>";

echo "<strong>Current ID Generation:</strong><br>";
$id = time() . mt_rand(100000, 999999);
echo "Generated ID: $id<br>";
echo "ID length: " . strlen($id) . " characters<br>";
echo "ID as integer: " . (int)$id . "<br><br>";

echo "<strong>Testing multiple IDs:</strong><br>";
for ($i = 0; $i < 5; $i++) {
    $testId = time() . mt_rand(100000, 999999);
    echo "ID $i: $testId (length: " . strlen($testId) . ")<br>";
}

echo "<br><strong>Current timestamp:</strong> " . time() . "<br>";
echo "<strong>Timestamp in 10 years:</strong> " . (time() + (10 * 365 * 24 * 60 * 60)) . "<br>";

echo "<br><strong>Analysis:</strong><br>";
echo "Current timestamp: " . time() . " (10 digits)<br>";
echo "Random part: 100000-999999 (6 digits)<br>";
echo "Total: ~16 digits maximum<br>";
echo "BIGINT can handle up to 19 digits<br>";
echo "✓ Our IDs are well within limits!<br>";

echo "</div>";
?>
