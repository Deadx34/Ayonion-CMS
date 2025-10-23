<?php
// AYONION-CMS Server Diagnostic Script
// This script will help diagnose PHP and server configuration issues

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AYONION-CMS Server Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; white-space: pre-wrap; }
        h1 { color: #333; text-align: center; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 5px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 AYONION-CMS Server Diagnostic</h1>
        <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <?php
        // Test 1: Basic PHP Execution
        echo '<div class="section">';
        echo '<h2>✅ Test 1: Basic PHP Execution</h2>';
        if (isset($_GET['test']) && $_GET['test'] === 'basic') {
            echo '<div class="status success">PHP is working correctly! This page is being processed by PHP.</div>';
        } else {
            echo '<div class="status info">Click <a href="?test=basic">here</a> to test basic PHP execution.</div>';
        }
        echo '</div>';

        // Test 2: PHP Version and Configuration
        echo '<div class="section">';
        echo '<h2>📋 Test 2: PHP Configuration</h2>';
        echo '<div class="status success">PHP Version: ' . phpversion() . '</div>';
        
        $required_version = '7.4.0';
        if (version_compare(PHP_VERSION, $required_version, '>=')) {
            echo '<div class="status success">✅ PHP version is compatible (requires 7.4+)</div>';
        } else {
            echo '<div class="status error">❌ PHP version is too old. Requires 7.4+</div>';
        }
        echo '</div>';

        // Test 3: Required Extensions
        echo '<div class="section">';
        echo '<h2>🔌 Test 3: Required PHP Extensions</h2>';
        $required_extensions = ['mysqli', 'json', 'session', 'curl', 'openssl'];
        $missing_extensions = [];
        
        foreach ($required_extensions as $ext) {
            if (extension_loaded($ext)) {
                echo '<div class="status success">✅ ' . $ext . ' extension is loaded</div>';
            } else {
                echo '<div class="status error">❌ ' . $ext . ' extension is missing</div>';
                $missing_extensions[] = $ext;
            }
        }
        
        if (empty($missing_extensions)) {
            echo '<div class="status success">All required extensions are available!</div>';
        } else {
            echo '<div class="status error">Missing extensions: ' . implode(', ', $missing_extensions) . '</div>';
        }
        echo '</div>';

        // Test 4: File Permissions
        echo '<div class="section">';
        echo '<h2>📁 Test 4: File Permissions</h2>';
        $files_to_check = ['index.html', 'handler_data.php', 'handler_finance.php', 'includes/config.php'];
        
        foreach ($files_to_check as $file) {
            if (file_exists($file)) {
                $perms = fileperms($file);
                $readable = is_readable($file);
                $writable = is_writable($file);
                echo '<div class="status ' . ($readable ? 'success' : 'error') . '">';
                echo ($readable ? '✅' : '❌') . ' ' . $file . ' - ' . ($readable ? 'Readable' : 'Not readable');
                if ($writable) echo ' (Writable)';
                echo ' - Permissions: ' . substr(sprintf('%o', $perms), -4);
                echo '</div>';
            } else {
                echo '<div class="status warning">⚠️ ' . $file . ' - File not found</div>';
            }
        }
        echo '</div>';

        // Test 5: Database Connection
        echo '<div class="section">';
        echo '<h2>🗄️ Test 5: Database Connection</h2>';
        if (file_exists('includes/config.php')) {
            try {
                include 'includes/config.php';
                $conn = connect_db();
                if ($conn) {
                    echo '<div class="status success">✅ Database connection successful</div>';
                    
                    // Test database tables
                    $tables = ['clients', 'documents', 'users', 'campaigns', 'content_credits'];
                    foreach ($tables as $table) {
                        $result = $conn->query("SHOW TABLES LIKE '$table'");
                        if ($result && $result->num_rows > 0) {
                            echo '<div class="status success">✅ Table ' . $table . ' exists</div>';
                        } else {
                            echo '<div class="status warning">⚠️ Table ' . $table . ' not found</div>';
                        }
                    }
                } else {
                    echo '<div class="status error">❌ Database connection failed</div>';
                }
            } catch (Exception $e) {
                echo '<div class="status error">❌ Database error: ' . $e->getMessage() . '</div>';
            }
        } else {
            echo '<div class="status error">❌ Config file not found</div>';
        }
        echo '</div>';

        // Test 6: Server Information
        echo '<div class="section">';
        echo '<h2>🖥️ Test 6: Server Information</h2>';
        echo '<div class="grid">';
        echo '<div>';
        echo '<h3>Server Software</h3>';
        echo '<div class="code">' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</div>';
        echo '</div>';
        echo '<div>';
        echo '<h3>Document Root</h3>';
        echo '<div class="code">' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . '</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<h3>PHP Configuration</h3>';
        echo '<div class="code">';
        echo 'Memory Limit: ' . ini_get('memory_limit') . "\n";
        echo 'Max Execution Time: ' . ini_get('max_execution_time') . " seconds\n";
        echo 'Upload Max Filesize: ' . ini_get('upload_max_filesize') . "\n";
        echo 'Post Max Size: ' . ini_get('post_max_size') . "\n";
        echo 'Error Reporting: ' . (ini_get('display_errors') ? 'Enabled' : 'Disabled') . "\n";
        echo '</div>';
        echo '</div>';

        // Test 7: URL and Path Information
        echo '<div class="section">';
        echo '<h2>🌐 Test 7: URL and Path Information</h2>';
        echo '<div class="code">';
        echo 'Current URL: ' . (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : 'Unknown') . "\n";
        echo 'Script Name: ' . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "\n";
        echo 'Request Method: ' . ($_SERVER['REQUEST_METHOD'] ?? 'Unknown') . "\n";
        echo 'User Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
        echo '</div>';
        echo '</div>';

        // Test 8: Quick Fixes
        echo '<div class="section">';
        echo '<h2>🔧 Test 8: Quick Fixes</h2>';
        echo '<div class="status info">';
        echo '<h3>If you see code instead of this page:</h3>';
        echo '<ol>';
        echo '<li>Check that your main file is named <code>index.php</code> (not <code>index.html</code>)</li>';
        echo '<li>Ensure PHP is enabled on your hosting account</li>';
        echo '<li>Check file permissions (should be 644 for files, 755 for directories)</li>';
        echo '<li>Contact your hosting provider to enable PHP processing</li>';
        echo '</ol>';
        echo '</div>';
        echo '</div>';

        // Test 9: Application Files Check
        echo '<div class="section">';
        echo '<h2>📄 Test 9: Application Files Check</h2>';
        $app_files = [
            'index.html' => 'Main application file',
            'handler_data.php' => 'Data handler',
            'handler_finance.php' => 'Finance handler',
            'handler_clients.php' => 'Clients handler',
            'handler_campaigns.php' => 'Campaigns handler',
            'handler_content.php' => 'Content handler',
            'handler_users.php' => 'Users handler',
            'handler_login.php' => 'Login handler',
            'includes/config.php' => 'Database configuration'
        ];
        
        foreach ($app_files as $file => $description) {
            if (file_exists($file)) {
                $size = filesize($file);
                echo '<div class="status success">✅ ' . $file . ' - ' . $description . ' (' . number_format($size) . ' bytes)</div>';
            } else {
                echo '<div class="status error">❌ ' . $file . ' - ' . $description . ' (Missing)</div>';
            }
        }
        echo '</div>';
        ?>

        <div class="section">
            <h2>📞 Next Steps</h2>
            <div class="status info">
                <h3>If PHP is not working:</h3>
                <ol>
                    <li>Contact ByetHost support to enable PHP</li>
                    <li>Check your hosting control panel for PHP settings</li>
                    <li>Ensure you're using the correct file extensions (.php)</li>
                    <li>Verify your account has PHP enabled</li>
                </ol>
                
                <h3>If everything looks good:</h3>
                <ol>
                    <li>Try accessing your main application: <a href="index.html">index.html</a></li>
                    <li>Check if the database is properly set up</li>
                    <li>Verify all required files are uploaded</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
