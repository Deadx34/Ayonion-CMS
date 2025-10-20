<?php
// AYONION-CMS/handler_finance.php - Handles financial documents

// Ensure we always return JSON, even on errors
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

try {
    include 'includes/config.php';
    $conn = connect_db();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

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

        // Get all documents with the same base ID (handle multiple item types)
        $baseId = str_replace('_' . substr($docId, strrpos($docId, '_') + 1), '', $docId);
        $doc_sql = "SELECT * FROM documents WHERE (id = '$docId' OR id LIKE '$baseId\_%') AND doc_type = '$docType'";
        $doc_result = $conn->query($doc_sql);
        
        if ($doc_result->num_rows === 0) {
            throw new Exception("Document not found.", 404);
        }
        
        $docs = [];
        $clientId = null;
        $adBudgetTotal = 0;
        $creditsTotal = 0;
        
        while ($row = $doc_result->fetch_assoc()) {
            $docs[] = $row;
            if ($clientId === null) $clientId = (int)$row['client_id'];
            
            if ($row['item_type'] === 'Ad Budget') {
                $adBudgetTotal += (float)$row['total'];
            } elseif ($row['item_type'] === 'Extra Content Credits') {
                $creditsTotal += (int)$row['quantity'];
            }
        }

        // Delete all related documents
        $delete_sql = "DELETE FROM documents WHERE (id = '$docId' OR id LIKE '$baseId\_%') AND doc_type = '$docType'";
        if (!query_db($conn, $delete_sql)) {
            throw new Exception("Failed to delete documents.");
        }

        // If it was a receipt, revert the client profile updates
        if ($docType === 'receipt') {
            if ($adBudgetTotal > 0) {
                $revert_ad_sql = "UPDATE clients SET total_ad_budget = total_ad_budget - $adBudgetTotal WHERE id = $clientId";
                if (!query_db($conn, $revert_ad_sql)) {
                    throw new Exception("Failed to revert client ad budget.");
                }
            }
            if ($creditsTotal > 0) {
                $revert_credits_sql = "UPDATE clients SET extra_credits = extra_credits - $creditsTotal WHERE id = $clientId";
                if (!query_db($conn, $revert_credits_sql)) {
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
    $itemTypes = $input['itemTypes'] ?? [];
    $description = $conn->real_escape_string($input['description'] ?? '');
    $quantity = (int)($input['quantity'] ?? 0);
    $unitPrice = (float)($input['unitPrice'] ?? 0.0);
    $date = $conn->real_escape_string($input['date'] ?? '');
    $total = $quantity * $unitPrice;

    if ($clientId === 0 || empty($docType) || empty($date) || empty($itemTypes) || !is_array($itemTypes)) {
        throw new Exception("Client, Document Type, Date, and Item Types are required.", 400);
    }

    // Fetch client name for storage in the document
    $client_name_sql = "SELECT company_name FROM clients WHERE id = $clientId";
    $client_result = $conn->query($client_name_sql);
    if ($client_result->num_rows !== 1) {
        throw new Exception("Client not found.", 404);
    }
    $client_row = $client_result->fetch_assoc();
    $clientName = $conn->real_escape_string($client_row['company_name']);


    // --- 1. Insert documents for each selected item type ---
    $documentIds = [];
    foreach ($itemTypes as $index => $itemType) {
        $itemType = $conn->real_escape_string($itemType);
        $documentId = $id . '_' . $index;
        $documentIds[] = $documentId;
        
        $sql_insert_doc = "INSERT INTO documents 
            (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
            VALUES 
            ('$documentId', $clientId, '$clientName', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date')";

        if (!query_db($conn, $sql_insert_doc)) {
            throw new Exception("Failed to save document for item type: $itemType");
        }
    }


    // --- 2. If it's a RECEIPT, update the client's profile for each item type ---
    if ($docType === 'receipt') {
        $adBudgetTotal = 0;
        $creditsTotal = 0;
        
        foreach ($itemTypes as $itemType) {
            if ($itemType === 'Ad Budget') {
                $adBudgetTotal += $total;
            } elseif ($itemType === 'Extra Content Credits') {
                $creditsTotal += $quantity;
            }
        }
        
        // Update ad budget if applicable
        if ($adBudgetTotal > 0) {
            $update_ad_sql = "UPDATE clients SET total_ad_budget = total_ad_budget + $adBudgetTotal WHERE id = $clientId";
            if (!query_db($conn, $update_ad_sql)) {
                throw new Exception("Document was saved, but failed to update client ad budget.");
            }
        }
        
        // Update credits if applicable
        if ($creditsTotal > 0) {
            $update_credits_sql = "UPDATE clients SET extra_credits = extra_credits + $creditsTotal WHERE id = $clientId";
            if (!query_db($conn, $update_credits_sql)) {
                throw new Exception("Document was saved, but failed to update client credits.");
            }
        }
    }

    // If everything was successful, commit the changes
    $conn->commit();
    echo json_encode([
        "success" => true, 
        "message" => ucfirst($docType) . " created successfully for " . count($itemTypes) . " item type(s)!",
        "documentIds" => $documentIds,
        "itemTypes" => $itemTypes
    ]);

} catch (Exception $e) {
    // If anything failed, roll back all changes
    $conn->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>