<?php
// AYONION-CMS/upload_content_image.php - Handles content image uploads

header('Content-Type: application/json');
include 'includes/config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.", 405);
    }

    if (!isset($_FILES['contentImage']) || $_FILES['contentImage']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("No image file uploaded or upload error occurred.", 400);
    }

    $file = $_FILES['contentImage'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.", 400);
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        throw new Exception("File too large. Maximum size is 5MB.", 400);
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/content_images/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Failed to create upload directory.", 500);
        }
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'content_' . time() . '_' . mt_rand(1000, 9999) . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception("Failed to save uploaded file.", 500);
    }
    
    // Generate URL for the uploaded image
    $imageUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
                '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $filePath;
    
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully!',
        'image_url' => $imageUrl,
        'file_name' => $fileName
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
