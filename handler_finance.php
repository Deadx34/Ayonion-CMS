<?php
// AYONION-CMS/handler_finance.php - Handles financial documents

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';

// --- HANDLE DELETE DOCUMENT (GET) ---
if ($action === 'delete') {
    $conn->begin_transaction();
    try {
        $docId = $conn->real_escape_string($_GET['id'] ?? '');
        $docType = $conn->real_escape_string($_GET['type'] ?? '');
        
        if (empty($docId) || empty($docType)) {
            throw new Exception("Document ID and type are required.", 400);
        }

        // Get document details before deletion
        $doc_sql = "SELECT * FROM documents WHERE id = '$docId' AND doc_type = '$docType'";
        $doc_result = $conn->query($doc_sql);
        
        if ($doc_result->num_rows !== 1) {
            throw new Exception("Document not found.", 404);
        }
        
        $doc = $doc_result->fetch_assoc();
        $clientId = (int)$doc['client_id'];
        $itemType = $doc['item_type'];
        $quantity = (int)$doc['quantity'];
        $total = (float)$doc['total'];

        // Delete the document
        $delete_sql = "DELETE FROM documents WHERE id = '$docId'";
        if (!query_db($conn, $delete_sql)) {
            throw new Exception("Failed to delete document.");
        }

        // If it was a receipt, revert the client profile updates
        if ($docType === 'receipt') {
            if ($itemType === 'Ad Budget') {
                $revert_sql = "UPDATE clients SET total_ad_budget = total_ad_budget - $total WHERE id = $clientId";
                if (!query_db($conn, $revert_sql)) {
                    throw new Exception("Failed to revert client ad budget.");
                }
            } elseif ($itemType === 'Extra Content Credits') {
                $revert_sql = "UPDATE clients SET extra_credits = extra_credits - $quantity WHERE id = $clientId";
                if (!query_db($conn, $revert_sql)) {
                    throw new Exception("Failed to revert client credits.");
                }
            }
        }

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Document deleted successfully."]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code($e->getCode() ?: 500);
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    $conn->close();
    exit;
}

// --- HANDLE ADD DOCUMENT (POST) ---
// Start transaction for data integrity
$conn->begin_transaction();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.", 405);
    }
    
    $input = json_decode(file_get_contents("php://input"), true);

    // --- Data Validation and Sanitization ---
    $id = time() . mt_rand(100, 999);
    $clientId = (int)($input['clientId'] ?? 0);
    $docType = $conn->real_escape_string($input['docType'] ?? '');
    $itemType = $conn->real_escape_string($input['itemType'] ?? '');
    $description = $conn->real_escape_string($input['description'] ?? '');
    $quantity = (int)($input['quantity'] ?? 0);
    $unitPrice = (float)($input['unitPrice'] ?? 0.0);
    $date = $conn->real_escape_string($input['date'] ?? '');
    $total = $quantity * $unitPrice;

    if ($clientId === 0 || empty($docType) || empty($date)) {
        throw new Exception("Client, Document Type, and Date are required.", 400);
    }

    // Fetch client name for storage in the document
    $client_name_sql = "SELECT company_name FROM clients WHERE id = $clientId";
    $client_result = $conn->query($client_name_sql);
    if ($client_result->num_rows !== 1) {
        throw new Exception("Client not found.", 404);
    }
    $client_row = $client_result->fetch_assoc();
    $clientName = $conn->real_escape_string($client_row['company_name']);


    // --- 1. Insert the document into the 'documents' table ---
    $sql_insert_doc = "INSERT INTO documents 
        (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
        VALUES 
        ('$id', $clientId, '$clientName', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date')";

    if (!query_db($conn, $sql_insert_doc)) {
        throw new Exception("Failed to save document to the database.");
    }


    // --- 2. If it's a RECEIPT, update the client's profile ---
    if ($docType === 'receipt') {
        $update_sql = null;

        if ($itemType === 'Ad Budget') {
            // Add the amount to the client's total ad budget
            $update_sql = "UPDATE clients SET total_ad_budget = total_ad_budget + $total WHERE id = $clientId";
        } elseif ($itemType === 'Extra Content Credits') {
            // Add the quantity to the client's extra credits
            $update_sql = "UPDATE clients SET extra_credits = extra_credits + $quantity WHERE id = $clientId";
        }
        
        if ($update_sql && !query_db($conn, $update_sql)) {
            throw new Exception("Document was saved, but failed to update client profile.");
        }
    }

    // If everything was successful, commit the changes
    $conn->commit();
    echo json_encode(["success" => true, "message" => ucfirst($docType) . " created successfully!"]);

} catch (Exception $e) {
    // If anything failed, roll back all changes
    $conn->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>