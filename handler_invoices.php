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
