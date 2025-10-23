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
    $itemTypes = $input['itemTypes'] ?? [];
    $itemDetails = $input['itemDetails'] ?? [];
    $description = $conn->real_escape_string($input['description'] ?? '');
    $date = $conn->real_escape_string($input['date'] ?? '');

    if ($clientId === 0 || empty($docType) || empty($date) || empty($itemTypes) || !is_array($itemTypes)) {
        throw new Exception("Client, Document Type, Date, and Item Types are required.", 400);
    }
    
    if (empty($itemDetails) || !is_array($itemDetails)) {
        throw new Exception("Item details are required.", 400);
    }
    
    // Calculate total from all item details
    $total = 0;
    foreach ($itemDetails as $item) {
        $total += (float)($item['total'] ?? 0);
    }

    // Fetch client name for storage in the document
    $client_name_sql = "SELECT company_name FROM clients WHERE id = $clientId";
    $client_result = $conn->query($client_name_sql);
    if ($client_result->num_rows !== 1) {
        throw new Exception("Client not found.", 404);
    }
    $client_row = $client_result->fetch_assoc();
    $clientName = $conn->real_escape_string($client_row['company_name']);


    // --- 1. Insert a single document with all item details as JSON ---
    // Encode all item details as JSON for storage
    $itemDetailsJson = json_encode($itemDetails);
    $itemTypesJson = json_encode($itemTypes);
    
    // Calculate average quantity and unit price for display purposes
    $avgQuantity = array_sum(array_column($itemDetails, 'quantity')) / count($itemDetails);
    $avgUnitPrice = array_sum(array_column($itemDetails, 'unitPrice')) / count($itemDetails);
    
    $sql_insert_doc = "INSERT INTO documents 
        (id, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
        VALUES 
        ('$id', $clientId, '$clientName', '$docType', '$itemDetailsJson', '$description', $avgQuantity, $avgUnitPrice, $total, '$date')";

    if (!query_db($conn, $sql_insert_doc)) {
        throw new Exception("Failed to save document to the database.");
    }
    
    $documentsCreated = 1;


    // --- 2. If it's a RECEIPT, update the client's profile for each item type ---
    if ($docType === 'receipt') {
        $adBudgetTotal = 0;
        $creditsTotal = 0;
        
        foreach ($itemDetails as $item) {
            $itemType = $item['itemType'];
            $itemTotal = (float)$item['total'];
            $itemQuantity = (int)$item['quantity'];
            
            if ($itemType === 'Ad Budget') {
                $adBudgetTotal += $itemTotal;
            } elseif ($itemType === 'Extra Content Credits') {
                $creditsTotal += $itemQuantity;
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
        "message" => ucfirst($docType) . " created successfully with " . count($itemDetails) . " item type(s) and total amount Rs. " . number_format($total, 2) . "!",
        "documentId" => $id,
        "itemTypes" => $itemTypes,
        "itemDetails" => $itemDetails,
        "totalAmount" => $total
    ]);

} catch (Exception $e) {
    // If anything failed, roll back all changes
    $conn->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>