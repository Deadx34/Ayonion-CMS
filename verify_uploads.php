<?php
// Script to verify uploaded files in uploads/logos/ directory
echo "<h2>AYONION CMS - Upload Verification</h2>";

$logosDir = 'uploads/logos';
$files = glob($logosDir . '/*');

echo "<h3>Files in uploads/logos/ directory:</h3>";

if (empty($files) || (count($files) === 1 && basename($files[0]) === '.gitkeep')) {
    echo "<p>📁 Directory is empty (only .gitkeep file present)</p>";
    echo "<p><strong>Next step:</strong> Upload a logo through the Settings tab or test page</p>";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Filename</th><th>Size</th><th>Type</th><th>Modified</th><th>Preview</th></tr>";
    
    foreach ($files as $file) {
        if (basename($file) === '.gitkeep') continue;
        
        $filename = basename($file);
        $filesize = filesize($file);
        $filetype = mime_content_type($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($filename) . "</td>";
        echo "<td>" . number_format($filesize / 1024, 2) . " KB</td>";
        echo "<td>" . htmlspecialchars($filetype) . "</td>";
        echo "<td>" . $modified . "</td>";
        echo "<td>";
        
        if (strpos($filetype, 'image/') === 0) {
            echo "<img src='" . htmlspecialchars($file) . "' alt='Logo' style='max-width: 50px; max-height: 50px; object-fit: contain; border: 1px solid #ddd;'>";
        } else {
            echo "❌ Not an image";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<h3>Directory Status:</h3>";
echo "<ul>";
echo "<li>Directory exists: " . (is_dir($logosDir) ? "✅ YES" : "❌ NO") . "</li>";
echo "<li>Directory writable: " . (is_writable($logosDir) ? "✅ YES" : "❌ NO") . "</li>";
echo "<li>Total files: " . count($files) . "</li>";
echo "<li>Directory size: " . number_format(array_sum(array_map('filesize', $files)) / 1024, 2) . " KB</li>";
echo "</ul>";

echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='test_logo_upload.html' target='_blank'>🧪 Logo Upload Test Page</a></li>";
echo "<li><a href='index.html' target='_blank'>🏠 Main Application</a></li>";
echo "</ul>";

echo "<h3>Quick Test:</h3>";
echo "<p>To test the upload functionality:</p>";
echo "<ol>";
echo "<li>Go to <a href='index.html' target='_blank'>Main Application</a></li>";
echo "<li>Login as admin</li>";
echo "<li>Go to Settings tab</li>";
echo "<li>Upload a logo image</li>";
echo "<li>Refresh this page to see the uploaded file</li>";
echo "</ol>";
?>
