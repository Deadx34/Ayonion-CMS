<?php
// AYONION-CMS/handler_finance.php - Add endpoint to get next document number for a given type
header('Content-Type: application/json');
include 'includes/config.php';
include 'includes/document_number_generator.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$docType = $_GET['docType'] ?? '';

if ($action === 'next_document_number' && $docType) {
    $nextNumber = generateDocumentNumber($conn, $docType);
    echo json_encode(["success" => true, "nextDocumentNumber" => $nextNumber]);
    exit;
}
// ...existing code...
?>
