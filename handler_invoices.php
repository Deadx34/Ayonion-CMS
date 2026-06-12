<?php
// AYONION-CMS/handler_invoices.php - Handles invoice operations

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

try {
    include 'includes/config.php';
    include 'includes/document_number_generator.php';
    $conn = connect_db();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

// Use transactions for data integrity
$conn->begin_transaction();

try {
    // --- 1. HANDLE CREATE INVOICE (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
        $invoiceId = time() . mt_rand(100, 999);
        $clientId = (int)($input['clientId'] ?? 0);
        $selectedCampaigns = $input['selectedCampaigns'] ?? [];
        $notes = $conn->real_escape_string($input['notes'] ?? '');
        
        if (empty($selectedCampaigns)) {
            throw new Exception("No campaigns selected for invoice.");
        }

        // Calculate total amount from selected campaigns
        $totalAmount = 0.00;
        $campaignDetails = [];
        
        foreach ($selectedCampaigns as $campaignId) {
            $sql = "SELECT spend, ad_name, platform FROM campaigns WHERE id = " . (int)$campaignId . " AND client_id = $clientId";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $campaign = $result->fetch_assoc();
                $totalAmount += (float)$campaign['spend'];
                $campaignDetails[] = [
                    'id' => $campaignId,
                    'spend' => $campaign['spend'],
                    'ad_name' => $campaign['ad_name'],
                    'platform' => $campaign['platform']
                ];
            }
        }

        if ($totalAmount <= 0) {
            throw new Exception("No valid campaigns found or total amount is zero.");
        }

        // Generate invoice number in new format (I10P001202511)
        $invoiceNumber = generateDocumentNumber($conn, 'invoice');
        
        // Calculate due date (30 days from now)
        $dueDate = date('Y-m-d', strtotime('+30 days'));

        // 1. Create Invoice Record
        $invoiceNumberEscaped = $conn->real_escape_string($invoiceNumber);
        $dueDateEscaped = $conn->real_escape_string($dueDate);
        
        $sql_invoice = "INSERT INTO invoices (
            id, client_id, total_amount, invoice_number, due_date, notes, status
        ) VALUES (
            '$invoiceId', $clientId, $totalAmount, '$invoiceNumberEscaped', '$dueDateEscaped', '$notes', 'draft'
        )";

        if (!query_db($conn, $sql_invoice)) {
            throw new Exception("Failed to create invoice.");
        }

        // 2. Create Invoice Items
        foreach ($campaignDetails as $campaign) {
            $campaignId = (int)$campaign['id'];
            $campaignSpend = (float)$campaign['spend'];
            $description = $conn->real_escape_string($campaign['platform'] . ' - ' . $campaign['ad_name']);
            
            $sql_item = "INSERT INTO invoice_items (
                invoice_id, campaign_id, amount, description
            ) VALUES (
                '$invoiceId', $campaignId, $campaignSpend, '$description'
            )";
            
            if (!query_db($conn, $sql_item)) {
                throw new Exception("Failed to create invoice item for campaign {$campaignId}.");
            }
        }

        $conn->commit();
        echo json_encode([
            "success" => true, 
            "message" => "Invoice created successfully.",
            "invoiceId" => $invoiceId,
            "invoiceNumber" => $invoiceNumber,
            "totalAmount" => $totalAmount
        ]);
    }
    
    // --- 2. HANDLE GET INVOICE DETAILS (GET) ---
    else if ($action === 'get' && isset($_GET['id'])) {
        $invoiceId = (int)($_GET['id'] ?? 0);
        
        // Get invoice details
        $sql_invoice = "SELECT i.*, c.company_name, c.partner_id 
                       FROM invoices i 
                       JOIN clients c ON i.client_id = c.id 
                       WHERE i.id = $invoiceId";
        
        $result = $conn->query($sql_invoice);
        if (!$result || $result->num_rows === 0) {
            throw new Exception("Invoice not found.");
        }
        
        $invoice = $result->fetch_assoc();
        
        // Get invoice items with campaign details
        $sql_items = "SELECT ii.*, c.platform, c.ad_name, c.spend 
                     FROM invoice_items ii 
                     JOIN campaigns c ON ii.campaign_id = c.id 
                     WHERE ii.invoice_id = $invoiceId";
        
        $items_result = $conn->query($sql_items);
        $items = [];
        if ($items_result) {
            while ($item = $items_result->fetch_assoc()) {
                $items[] = $item;
            }
        }
        
        $invoice['items'] = $items;
        
        echo json_encode([
            "success" => true,
            "invoice" => $invoice
        ]);
    }
    
    // --- 3. HANDLE GET CLIENT INVOICES (GET) ---
    else if ($action === 'list' && isset($_GET['clientId'])) {
        $clientId = (int)($_GET['clientId'] ?? 0);
        
        $sql = "SELECT i.*, c.company_name 
                FROM invoices i 
                JOIN clients c ON i.client_id = c.id 
                WHERE i.client_id = $clientId 
                ORDER BY i.created_at DESC";
        
        $result = $conn->query($sql);
        $invoices = [];
        
        if ($result) {
            while ($invoice = $result->fetch_assoc()) {
                $invoices[] = $invoice;
            }
        }
        
        echo json_encode([
            "success" => true,
            "invoices" => $invoices
        ]);
    }
    
    // --- 4. HANDLE UPDATE INVOICE STATUS (PUT) ---
    else if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $action === 'update_status') {
        $invoiceId = (int)($input['invoiceId'] ?? 0);
        $status = $conn->real_escape_string($input['status'] ?? 'draft');
        
        $sql = "UPDATE invoices SET status = '$status' WHERE id = $invoiceId";
        
        if (query_db($conn, $sql)) {
            $conn->commit();
            echo json_encode(["success" => true, "message" => "Invoice status updated."]);
        } else {
            throw new Exception("Failed to update invoice status.");
        }
    }
    
    // --- 5. HANDLE CREATE NON-CUSTOMER INVOICE (POST) ---
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create_non_customer') {
        // First, check if the required columns exist
        $colCheckCustomer = $conn->query("SHOW COLUMNS FROM invoices LIKE 'customer_name'");
        $colCheckNonCustomer = $conn->query("SHOW COLUMNS FROM invoices LIKE 'is_non_customer'");
        
        if (!$colCheckCustomer || $colCheckCustomer->num_rows === 0 || !$colCheckNonCustomer || $colCheckNonCustomer->num_rows === 0) {
            throw new Exception(
                "Database migration required. Please run the migration script:\n" .
                "database/migrate_add_non_customer_invoice_columns.sql\n\n" .
                "Missing columns: customer_name or is_non_customer in invoices table.",
                503
            );
        }
        
        $invoiceId = time() . mt_rand(100, 999);
        $customerName = $conn->real_escape_string($input['customerName'] ?? '');
        $itemDetails = $input['itemDetails'] ?? [];  // New format from documentModal
        $itemTypes = $input['itemTypes'] ?? [];
        $notes = $conn->real_escape_string($input['description'] ?? $input['notes'] ?? '');
        
        if (empty($customerName)) {
            throw new Exception("Customer name is required for non-customer invoices.");
        }
        
        if (empty($itemDetails)) {
            throw new Exception("At least one invoice item is required.");
        }
        
        // Calculate total amount from itemDetails
        $totalAmount = 0.00;
        $items = [];  // Convert itemDetails to items format for documents table
        foreach ($itemDetails as $detail) {
            $quantity = (float)($detail['quantity'] ?? 1);
            $unitPrice = (float)($detail['unitPrice'] ?? 0);
            $totalAmount += ($quantity * $unitPrice);
            
            // Build items array for documents table
            $items[] = [
                'description' => $detail['itemType'] ?? $detail['description'] ?? 'Service',
                'quantity' => $quantity,
                'unitPrice' => $unitPrice
            ];
        }
        
        if ($totalAmount <= 0) {
            throw new Exception("Invoice total must be greater than zero.");
        }
        
        // Generate invoice number
        $invoiceNumber = generateDocumentNumber($conn, 'invoice');
        
        // Calculate due date (30 days from now)
        $dueDate = date('Y-m-d', strtotime('+30 days'));
        
        // 1. Create Invoice Record with customer_name and is_non_customer flags
        $invoiceNumberEscaped = $conn->real_escape_string($invoiceNumber);
        $dueDateEscaped = $conn->real_escape_string($dueDate);
        $customerNameEscaped = $conn->real_escape_string($customerName);
        
        $sql_invoice = "INSERT INTO invoices (
            id, client_id, customer_name, is_non_customer, total_amount, invoice_number, due_date, notes, status
        ) VALUES (
            '$invoiceId', NULL, '$customerNameEscaped', TRUE, $totalAmount, '$invoiceNumberEscaped', '$dueDateEscaped', '$notes', 'draft'
        )";
        
        if (!query_db($conn, $sql_invoice)) {
            throw new Exception("Failed to create non-customer invoice.");
        }
        
        // 2. Create Invoice Items
        foreach ($itemDetails as $detail) {
            $itemType = $conn->real_escape_string($detail['itemType'] ?? $detail['description'] ?? 'Service');
            $quantity = (float)($detail['quantity'] ?? 1);
            $unitPrice = (float)($detail['unitPrice'] ?? 0);
            $amount = $quantity * $unitPrice;
            
            $sql_item = "INSERT INTO invoice_items (
                invoice_id, campaign_id, amount, description
            ) VALUES (
                '$invoiceId', NULL, $amount, '$itemType'
            )";
            
            if (!query_db($conn, $sql_item)) {
                throw new Exception("Failed to create invoice item.");
            }
        }

        // --- 3. Save a corresponding documents row so the existing document PDF/template is reused ---
        $docId = time() . mt_rand(100, 999);
        $documentNumber = $invoiceNumber;
        $docDate = date('Y-m-d');

        // Build item details for documents table
        $docItemDetails = [];
        $docItemTypes = [];
        foreach ($itemDetails as $detail) {
            $itemType = $detail['itemType'] ?? $detail['description'] ?? 'Service';
            $docItemTypes[] = $itemType;
            $docItemDetails[] = [
                'itemType' => $itemType,
                'description' => $detail['description'] ?? '',
                'quantity' => (float)($detail['quantity'] ?? 1),
                'unitPrice' => (float)($detail['unitPrice'] ?? 0),
                'total' => ((float)($detail['quantity'] ?? 1)) * ((float)($detail['unitPrice'] ?? 0))
            ];
        }

        $itemDetailsJson = $conn->real_escape_string(json_encode($docItemDetails));
        $itemTypesJson = $conn->real_escape_string(json_encode($docItemTypes));

        // Detect whether the documents table has an item_details column
        $hasItemDetails = false;
        $colCheck = $conn->query("SHOW COLUMNS FROM documents LIKE 'item_details'");
        if ($colCheck && $colCheck->num_rows > 0) {
            $hasItemDetails = true;
        }

        if ($hasItemDetails) {
            $sql_insert_doc = "INSERT INTO documents 
            (id, document_number, client_id, client_name, doc_type, item_type, item_details, description, quantity, unit_price, total, date) 
            VALUES 
            ('$docId', '$documentNumber', NULL, '$customerNameEscaped', 'invoice', '$itemTypesJson', '$itemDetailsJson', '$notes', 1, $totalAmount, $totalAmount, '$docDate')";
        } else {
            // Legacy fallback: store JSON in item_type
            $sql_insert_doc = "INSERT INTO documents 
            (id, document_number, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date) 
            VALUES 
            ('$docId', '$documentNumber', NULL, '$customerNameEscaped', 'invoice', '$itemDetailsJson', '$notes', 1, $totalAmount, $totalAmount, '$docDate')";
        }

        if (!query_db($conn, $sql_insert_doc)) {
            throw new Exception("Failed to create linked document for non-customer invoice.");
        }
        
        $conn->commit();
        echo json_encode([
            "success" => true, 
            "message" => "Non-customer invoice created successfully.",
            "invoiceId" => $invoiceId,
            "invoiceNumber" => $invoiceNumber,
            "totalAmount" => $totalAmount,
            "documentId" => $docId
        ]);
    }
    
    // --- 6. HANDLE GET ALL NON-CUSTOMER INVOICES (GET) ---
    else if ($action === 'list_non_customer') {
        $sql = "SELECT i.*, NULL as company_name, NULL as partner_id 
                FROM invoices i 
                WHERE i.is_non_customer = TRUE 
                ORDER BY i.created_at DESC";
        
        $result = $conn->query($sql);
        $invoices = [];
        
        if ($result) {
            while ($invoice = $result->fetch_assoc()) {
                $invoices[] = $invoice;
            }
        }
        
        echo json_encode([
            "success" => true,
            "invoices" => $invoices
        ]);
    }
    
    else {
        throw new Exception("Invalid API endpoint request.", 400);
    }
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>
