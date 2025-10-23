<?php
// AYONION-CMS PHP Wrapper
// This ensures proper MIME type and content processing

// Set proper headers
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Read and output the HTML file
$htmlFile = 'index.html';

if (file_exists($htmlFile)) {
    // Read the HTML content
    $content = file_get_contents($htmlFile);
    
    // Output the content with proper headers
    echo $content;
                } else {
    // Fallback if index.html doesn't exist
    echo '<!DOCTYPE html>
<html lang="en">
                <head>
                    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AYONION-CMS - File Not Found</title>
                    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; }
                    </style>
                </head>
                <body>
    <div class="container">
        <h1>AYONION-CMS</h1>
        <div class="error">
            <h3>File Not Found</h3>
            <p>The main application file (index.html) was not found.</p>
            <p>Please ensure all files are uploaded correctly.</p>
                            </div>
                            </div>
                </body>
</html>';
}
?>