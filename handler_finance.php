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

        // Get all document items before deletion
        $doc_sql = "SELECT * FROM documents WHERE id = '$docId' AND doc_type = '$docType'";
        $doc_result = $conn->query($doc_sql);
        
        if ($doc_result->num_rows === 0) {
            throw new Exception("Document not found.", 404);
        }
        
        $docItems = [];
        while ($row = $doc_result->fetch_assoc()) {
            $docItems[] = $row;
        }
        
        $clientId = (int)$docItems[0]['client_id'];
        $adBudgetTotal = 0;
        $creditsTotal = 0;
        
        // Calculate totals for reverting
        foreach ($docItems as $item) {
            if ($item['item_type'] === 'Ad Budget') {
                $adBudgetTotal += (float)$item['total'];
            } elseif ($item['item_type'] === 'Extra Content Credits') {
                $creditsTotal += (int)$item['quantity'];
            }
        }

        // Delete all document line items (handle both old single-item and new multi-item formats)
        $delete_sql = "DELETE FROM documents WHERE id = '$docId' OR id LIKE '$docId\_line\_%'";
        if (!query_db($conn, $delete_sql)) {
            throw new Exception("Failed to delete document.");
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
    // Generate unique ID that fits within BIGINT limits
    $id = time() . mt_rand(100000, 999999);
    
    // Double-check for uniqueness
    $check_sql = "SELECT COUNT(*) as count FROM documents WHERE id = '$id'";
    $check_result = $conn->query($check_sql);
    if ($check_result && $check_result->fetch_assoc()['count'] > 0) {
        // If duplicate, add more randomness
        $id = $id . mt_rand(1000, 9999);
    }
    
    $clientId = (int)($input['clientId'] ?? 0);
    $docType = $conn->real_escape_string($input['docType'] ?? '');
    $items = $input['items'] ?? [];
    $date = $conn->real_escape_string($input['date'] ?? '');

    if ($clientId === 0 || empty($docType) || empty($date) || empty($items) || !is_array($items)) {
        throw new Exception("Client, Document Type, Date, and Items are required.", 400);
    }

    // Validate items
    $totalAmount = 0;
    foreach ($items as $item) {
        if (empty($item['itemType']) || empty($item['description']) || 
            $item['quantity'] <= 0 || $item['unitPrice'] <= 0) {
            throw new Exception("All item fields are required and must be valid.", 400);
        }
        $totalAmount += (float)$item['total'];
    }

    // Fetch client name for storage in the document
    $client_name_sql = "SELECT company_name FROM clients WHERE id = $clientId";
    $client_result = $conn->query($client_name_sql);
    if ($client_result->num_rows !== 1) {
        throw new Exception("Client not found.", 404);
    }
    $client_row = $client_result->fetch_assoc();
    $clientName = $conn->real_escape_string($client_row['company_name']);


    // --- 1. Insert the document as a single document with multiple line items ---
    // Calculate grand total
    $grandTotal = 0;
    foreach ($items as $item) {
        $grandTotal += (float)$item['total'];
    }
    
    // Insert each item as a line item in the same document
    foreach ($items as $index => $item) {
        $itemType = $conn->real_escape_string($item['itemType']);
        $description = $conn->real_escape_string($item['description']);
        $quantity = (int)$item['quantity'];
        $unitPrice = (float)$item['unitPrice'];
        $total = (float)$item['total'];
        
        // Use the same document ID for all items, but with line item index
        $lineItemId = $id . '_line_' . $index;
        
        // Check if item_order column exists, if not use basic insert
        $check_column_sql = "SHOW COLUMNS FROM documents LIKE 'item_order'";
        $column_check = $conn->query($check_column_sql);
        
        if ($column_check->num_rows > 0) {
            // Column exists, use full insert with item_order
            $sql_insert_doc = "INSERT INTO documents 
                (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date, item_order) 
                VALUES 
                ('$lineItemId', $clientId, '$clientName', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date', $index)";
        } else {
            // Column doesn't exist, use basic insert
            $sql_insert_doc = "INSERT INTO documents 
                (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
                VALUES 
                ('$lineItemId', $clientId, '$clientName', '$docType', '$itemType', '$description', $quantity, $unitPrice, $total, '$date')";
        }

        if (!query_db($conn, $sql_insert_doc)) {
            throw new Exception("Failed to save document line item to the database.");
        }
    }


    // --- 2. If it's a RECEIPT, update the client's profile ---
    if ($docType === 'receipt') {
        $adBudgetTotal = 0;
        $creditsTotal = 0;
        
        foreach ($items as $item) {
            if ($item['itemType'] === 'Ad Budget') {
                $adBudgetTotal += (float)$item['total'];
            } elseif ($item['itemType'] === 'Extra Content Credits') {
                $creditsTotal += (int)$item['quantity'];
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
        "message" => ucfirst($docType) . " created successfully!",
        "documentId" => $id
    ]);

} catch (Exception $e) {
    // If anything failed, roll back all changes
    $conn->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>