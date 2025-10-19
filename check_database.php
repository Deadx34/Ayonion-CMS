<?php
// AYONION-CMS/check_database.php - Check database structure and run migration

header('Content-Type: application/json');
include 'includes/config.php';

try {
    $conn = connect_db();
    
    // Check current table structure
    $checkTable = "DESCRIBE content_credits";
    $result = $conn->query($checkTable);
    
    $columns = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'current_columns' => $columns,
        'has_content_url' => in_array('content_url', $columns),
        'has_image_url' => in_array('image_url', $columns),
        'has_status' => in_array('status', $columns),
        'has_published_date' => in_array('published_date', $columns)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
