<?php
session_start();
require_once 'includes/config.php';

echo "<h2>Session and Upload Test</h2>";

echo "<h3>Session Information:</h3>";
echo "<ul>";
echo "<li>Session ID: " . session_id() . "</li>";
echo "<li>User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "</li>";
echo "<li>User Role: " . (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'NOT SET') . "</li>";
echo "<li>Username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'NOT SET') . "</li>";
echo "</ul>";

echo "<h3>Database Connection:</h3>";
if ($conn) {
    echo "<p>✅ Database connection successful</p>";
    
    // Check if settings table exists
    $result = $conn->query("SHOW TABLES LIKE 'settings'");
    if ($result && $result->num_rows > 0) {
        echo "<p>✅ Settings table exists</p>";
        
        // Check current settings
        $settings = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch_assoc();
        if ($settings) {
            echo "<p>✅ Settings record found</p>";
            echo "<p>Current logo_url: " . ($settings['logo_url'] ? $settings['logo_url'] : 'EMPTY') . "</p>";
        } else {
            echo "<p>❌ No settings record found</p>";
        }
    } else {
        echo "<p>❌ Settings table does not exist</p>";
    }
} else {
    echo "<p>❌ Database connection failed</p>";
}

echo "<h3>Directory Permissions:</h3>";
$uploadDir = 'uploads/logos/';
echo "<ul>";
echo "<li>Directory exists: " . (is_dir($uploadDir) ? "✅ YES" : "❌ NO") . "</li>";
echo "<li>Directory writable: " . (is_writable($uploadDir) ? "✅ YES" : "❌ NO") . "</li>";
echo "<li>Directory permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</li>";
echo "</ul>";

echo "<h3>PHP Configuration:</h3>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? "✅ ENABLED" : "❌ DISABLED") . "</li>";
echo "</ul>";

echo "<h3>Test Upload Handler:</h3>";
echo "<p><a href='upload_logo_handler.php' target='_blank'>Test Upload Handler</a></p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Make sure you're logged in as admin in the main application</li>";
echo "<li>Try uploading a logo through the Settings tab</li>";
echo "<li>Check the error logs for detailed information</li>";
echo "</ol>";
?>
