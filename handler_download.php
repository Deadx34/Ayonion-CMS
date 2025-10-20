<?php
// AYONION-CMS/handler_download.php - Handles document PDF generation and download

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$docId = $_GET['id'] ?? '';
$docType = $_GET['type'] ?? '';

try {
    if ($action !== 'download') {
        throw new Exception("Invalid action. Use 'download' action.", 400);
    }
    
    if (empty($docId) || empty($docType)) {
        throw new Exception("Document ID and type are required.", 400);
    }

    // Check if item_order column exists
    $check_column_sql = "SHOW COLUMNS FROM documents LIKE 'item_order'";
    $column_check = $conn->query($check_column_sql);
    
    if ($column_check->num_rows > 0) {
        // Column exists, use full query with item_order
        $doc_sql = "SELECT d.*, c.company_name, c.partner_id, c.industry, c.managing_platforms 
                    FROM documents d 
                    JOIN clients c ON d.client_id = c.id 
                    WHERE (d.id = ? OR d.id LIKE ?) AND d.doc_type = ?
                    ORDER BY d.item_order ASC";
    } else {
        // Column doesn't exist, use basic query
        $doc_sql = "SELECT d.*, c.company_name, c.partner_id, c.industry, c.managing_platforms 
                    FROM documents d 
                    JOIN clients c ON d.client_id = c.id 
                    WHERE (d.id = ? OR d.id LIKE ?) AND d.doc_type = ?
                    ORDER BY d.id ASC";
    }
    
    $docIdPattern = $docId . '_%';
    
    $stmt = $conn->prepare($doc_sql);
    $stmt->bind_param("sss", $docId, $docIdPattern, $docType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Document not found.", 404);
    }
    
    // Get all items for this document
    $docItems = [];
    while ($row = $result->fetch_assoc()) {
        $docItems[] = $row;
    }
    $stmt->close();
    
    // Use the first item for basic document info (client, date, etc.)
    $doc = $docItems[0];

    // Fetch company settings
    $settings_sql = "SELECT * FROM settings WHERE id = 1";
    $settings_result = $conn->query($settings_sql);
    $settings = $settings_result->fetch_assoc();

    // Generate HTML content optimized for PDF conversion
    include 'simple_pdf.php';
    $htmlContent = createPDFDocument($doc, $settings, $docItems);
    
    // Set headers for PDF download
    $filename = strtoupper($docType) . "_" . $docId . "_" . date('Y-m-d') . ".pdf";
    
    // Use browser's PDF generation with proper headers
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Add JavaScript to trigger PDF download
    $htmlContent = str_replace('</body>', '
    <script>
        // Auto-trigger print dialog for PDF generation
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
    </body>', $htmlContent);
    
    echo $htmlContent;
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();

function generateDocumentPDF($doc, $settings) {
    $docNum = ['quotation' => 'Q', 'invoice' => 'I', 'receipt' => 'R'];
    $colors = ['quotation' => '#6366f1', 'invoice' => '#10b981', 'receipt' => '#f59e0b'];
    $titles = ['quotation' => 'QUOTATION', 'invoice' => 'INVOICE', 'receipt' => 'RECEIPT'];
    
    $docType = $doc['doc_type'];
    $color = $colors[$docType];
    $title = $titles[$docType];
    $docNumber = $docNum[$docType] . substr($doc['id'], -6);
    
    $companyName = $settings['company_name'] ?? 'AYONION CMS';
    $companyEmail = $settings['email'] ?? '';
    $companyPhone = $settings['phone'] ?? '';
    $companyAddress = $settings['address'] ?? '';
    
    $clientName = $doc['company_name'];
    $partnerId = $doc['partner_id'];
    $date = date('F j, Y', strtotime($doc['date']));
    $itemType = $doc['item_type'];
    $description = $doc['description'];
    $quantity = $doc['quantity'];
    $unitPrice = number_format($doc['unit_price'], 2);
    $total = number_format($doc['total'], 2);
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>{$title} - {$docNumber}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid {$color}; padding-bottom: 20px; }
            .company-info { margin-bottom: 20px; }
            .document-title { font-size: 28px; font-weight: bold; color: {$color}; margin: 10px 0; }
            .document-number { font-size: 18px; color: #666; }
            .content { display: flex; justify-content: space-between; margin: 30px 0; }
            .client-info, .company-details { width: 45%; }
            .section-title { font-size: 16px; font-weight: bold; color: {$color}; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
            .info-row { margin: 5px 0; }
            .label { font-weight: bold; }
            .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            .table th { background-color: #f8f9fa; font-weight: bold; }
            .total-row { background-color: #f8f9fa; font-weight: bold; }
            .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #eee; text-align: center; color: #666; font-size: 14px; }
            @media print { body { margin: 0; } }
        </style>
    </head>
    <body>
        <div class='header'>
            <div class='company-info'>
                <h1 style='margin: 0; color: {$color};'>{$companyName}</h1>
                <div style='margin: 5px 0; color: #666;'>
                    " . ($companyEmail ? "Email: {$companyEmail}<br>" : "") . "
                    " . ($companyPhone ? "Phone: {$companyPhone}<br>" : "") . "
                    " . ($companyAddress ? "Address: {$companyAddress}" : "") . "
                </div>
            </div>
            <div class='document-title'>{$title}</div>
            <div class='document-number'>Document #: {$docNumber}</div>
            <div style='margin-top: 10px; color: #666;'>Date: {$date}</div>
        </div>
        
        <div class='content'>
            <div class='client-info'>
                <div class='section-title'>Bill To:</div>
                <div class='info-row'><span class='label'>Company:</span> {$clientName}</div>
                <div class='info-row'><span class='label'>Partner ID:</span> {$partnerId}</div>
            </div>
            
            <div class='company-details'>
                <div class='section-title'>From:</div>
                <div class='info-row'><span class='label'>Company:</span> {$companyName}</div>
                " . ($companyEmail ? "<div class='info-row'><span class='label'>Email:</span> {$companyEmail}</div>" : "") . "
                " . ($companyPhone ? "<div class='info-row'><span class='label'>Phone:</span> {$companyPhone}</div>" : "") . "
            </div>
        </div>
        
        <table class='table'>
            <thead>
                <tr>
                    <th>Item Type</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{$itemType}</td>
                    <td>{$description}</td>
                    <td>{$quantity}</td>
                    <td>Rs. {$unitPrice}</td>
                    <td>Rs. {$total}</td>
                </tr>
                <tr class='total-row'>
                    <td colspan='4' style='text-align: right;'>Total Amount:</td>
                    <td>Rs. {$total}</td>
                </tr>
            </tbody>
        </table>
        
        <div class='footer'>
            <p>Thank you for your business!</p>
            <p>Generated on " . date('F j, Y \a\t g:i A') . "</p>
        </div>
    </body>
    </html>";
    
    return $html;
}
?>
