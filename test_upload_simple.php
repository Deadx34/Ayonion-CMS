<?php
// Simple upload test without session requirements
session_start();
require_once 'includes/config.php';

echo "<h2>Simple Upload Test</h2>";

// Test database connection
$conn = connect_db();
if ($conn) {
    echo "<p>✅ Database connection successful</p>";
} else {
    echo "<p>❌ Database connection failed</p>";
    exit;
}

// Test directory
$uploadDir = 'uploads/logos/';
if (is_dir($uploadDir) && is_writable($uploadDir)) {
    echo "<p>✅ Upload directory is writable</p>";
} else {
    echo "<p>❌ Upload directory issue</p>";
    exit;
}

// Test file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $file = $_FILES['logo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    echo "<h3>Upload Test Results:</h3>";
    echo "<ul>";
    echo "<li>File name: " . $file['name'] . "</li>";
    echo "<li>File size: " . number_format($file['size'] / 1024, 2) . " KB</li>";
    echo "<li>File type: " . $file['type'] . "</li>";
    echo "<li>Temp name: " . $file['tmp_name'] . "</li>";
    echo "</ul>";
    
    // Validate file
    if (!in_array($file['type'], $allowedTypes)) {
        echo "<p>❌ Invalid file type. Only JPEG, PNG, GIF, and WebP allowed.</p>";
    } elseif ($file['size'] > $maxSize) {
        echo "<p>❌ File too large. Maximum 2MB allowed.</p>";
    } else {
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'test_logo_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            echo "<p>✅ File uploaded successfully to: " . $filepath . "</p>";
            echo "<p>✅ File size on disk: " . number_format(filesize($filepath) / 1024, 2) . " KB</p>";
            
            // Test image display
            echo "<h3>Image Preview:</h3>";
            echo "<img src='" . $filepath . "' alt='Uploaded Logo' style='max-width: 200px; max-height: 200px; border: 1px solid #ddd;'>";
            
            // Clean up test file
            unlink($filepath);
            echo "<p>✅ Test file cleaned up</p>";
        } else {
            echo "<p>❌ File upload failed</p>";
        }
    }
} else {
    echo "<h3>Upload Test Form:</h3>";
    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<p><input type='file' name='logo' accept='image/*' required></p>";
    echo "<p><button type='submit'>Test Upload</button></p>";
    echo "</form>";
}

echo "<h3>System Status:</h3>";
echo "<ul>";
echo "<li>PHP version: " . phpversion() . "</li>";
echo "<li>Upload max filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>Post max size: " . ini_get('post_max_size') . "</li>";
echo "<li>File uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</li>";
echo "<li>Upload directory: " . $uploadDir . "</li>";
echo "<li>Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "</li>";
echo "</ul>";
?>
