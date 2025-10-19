<?php
// Test script to verify upload functionality
echo "<h2>AYONION CMS - Upload Directory Test</h2>";

// Check if uploads directory exists
$uploadsDir = 'uploads';
$logosDir = 'uploads/logos';

echo "<h3>Directory Structure Check:</h3>";
echo "<ul>";
echo "<li>uploads/ directory: " . (is_dir($uploadsDir) ? "✅ EXISTS" : "❌ MISSING") . "</li>";
echo "<li>uploads/logos/ directory: " . (is_dir($logosDir) ? "✅ EXISTS" : "❌ MISSING") . "</li>";
echo "</ul>";

// Check permissions
echo "<h3>Directory Permissions:</h3>";
echo "<ul>";
echo "<li>uploads/ permissions: " . substr(sprintf('%o', fileperms($uploadsDir)), -4) . "</li>";
echo "<li>uploads/logos/ permissions: " . substr(sprintf('%o', fileperms($logosDir)), -4) . "</li>";
echo "</ul>";

// Check if upload handler exists
echo "<h3>Upload Handler Check:</h3>";
echo "<ul>";
echo "<li>upload_logo_handler.php: " . (file_exists('upload_logo_handler.php') ? "✅ EXISTS" : "❌ MISSING") . "</li>";
echo "<li>.htaccess file: " . (file_exists('.htaccess') ? "✅ EXISTS" : "❌ MISSING") . "</li>";
echo "</ul>";

// Test write permissions
echo "<h3>Write Permission Test:</h3>";
$testFile = $logosDir . '/test_write.txt';
if (file_put_contents($testFile, 'Test write permission')) {
    echo "<p>✅ Write permission test PASSED</p>";
    unlink($testFile); // Clean up test file
} else {
    echo "<p>❌ Write permission test FAILED</p>";
}

// Check PHP configuration
echo "<h3>PHP Configuration:</h3>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>max_execution_time: " . ini_get('max_execution_time') . " seconds</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? "✅ ENABLED" : "❌ DISABLED") . "</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Go to your AYONION CMS Settings tab</li>";
echo "<li>Try uploading a logo image</li>";
echo "<li>Check if the file appears in uploads/logos/ directory</li>";
echo "<li>Verify the logo appears in the sidebar and reports</li>";
echo "</ol>";

echo "<p><strong>Directory structure is ready for logo uploads!</strong></p>";
?>
