<?php
// Simplified upload handler for testing
session_start();

// Debug session info
error_log("Session check - user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));
error_log("Session check - user_role: " . (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'not set'));

// For testing, skip admin check temporarily
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     error_log("Access denied - user not admin or not logged in");
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Access denied - admin session required']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
    $uploadDir = 'uploads/logos/';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $file = $_FILES['logo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    // Validate file
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP allowed.']);
        exit;
    }
    
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File too large. Maximum 2MB allowed.']);
        exit;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'logo_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        error_log("File moved successfully to: " . $filepath);
        echo json_encode(['success' => true, 'logo_url' => $filepath]);
    } else {
        error_log("File move failed - tmp_name: " . $file['tmp_name'] . ", target: " . $filepath);
        echo json_encode(['success' => false, 'message' => 'File upload failed - check directory permissions']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
}
?>
