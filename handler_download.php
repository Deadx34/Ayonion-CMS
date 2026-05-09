<?php
// AYONION-CMS/handler_download.php - Handles document PDF generation and download

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$docId = $_GET['id'] ?? '';
$docType = $_GET['type'] ?? '';

try {
    if ($action !== 'download' && $action !== 'print') {
        throw new Exception("Invalid action. Use 'download' or 'print' action.", 400);
    }
    
    if (empty($docId) || empty($docType)) {
        throw new Exception("Document ID and type are required.", 400);
    }

    // Fetch document details
    $doc_sql = "SELECT d.*, c.company_name, c.partner_id, c.industry, c.managing_platforms 
                FROM documents d 
                JOIN clients c ON d.client_id = c.id 
                WHERE d.id = ? AND d.doc_type = ?";
    
    $stmt = $conn->prepare($doc_sql);
    $stmt->bind_param("ss", $docId, $docType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Document not found.", 404);
    }
    
    $doc = $result->fetch_assoc();
    $stmt->close();

    // Fetch company settings
    $settings_sql = "SELECT * FROM settings WHERE id = 1";
    $settings_result = $conn->query($settings_sql);
    $settings = $settings_result->fetch_assoc();

    // Generate HTML content optimized for PDF conversion
    include 'simple_pdf.php';
    $htmlContent = createPDFDocument($doc, $settings);
    
    // Set headers for document display
    $filename = strtoupper($docType) . "_" . $docId . "_" . date('Y-m-d') . ".html";
    
    // Use browser's HTML generation with proper headers
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // Add JavaScript to trigger print dialog
    $htmlContent = str_replace('</body>', '
    <script>
        // Auto-trigger print dialog
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
                    <?php
                        // Build detailed rows from item_details if available, otherwise split item_type by commas
                        $rowsHtml = '';
                        $builtFromDetails = false;

                        if (!empty($doc['item_details'])) {
                            $decoded = json_decode($doc['item_details'], true);
                            if ($decoded && !is_array($decoded)) {
                                $decoded = [$decoded];
                            }
                            if (is_array($decoded) && count($decoded) > 0) {
                                $builtFromDetails = true;
                                foreach ($decoded as $it) {
                                    if (is_string($it)) {
                                        $try = json_decode($it, true);
                                        if ($try) $it = $try; else $it = ['itemType' => $it];
                                    }
                                    $service = htmlspecialchars($it['itemType'] ?? $it['item_type'] ?? 'Service');
                                    $desc = htmlspecialchars($it['description'] ?? '');
                                    $sub = htmlspecialchars($it['subText'] ?? $it['sub_text'] ?? '');
                                    $qty = isset($it['quantity']) && $it['quantity'] !== null ? $it['quantity'] : '';
                                    $uprice = isset($it['unitPrice']) && $it['unitPrice'] !== null ? number_format($it['unitPrice'], 2) : '';
                                    $t = isset($it['total']) && $it['total'] !== null ? number_format($it['total'], 2) : '';

                                    $rowsHtml .= "<tr>\n";
                                    $rowsHtml .= "<td><div style='font-weight:600;'>{$service}</div>";
                                    if ($sub !== '') $rowsHtml .= "<div style='font-size:11px;color:#666;margin-top:4px;white-space:pre-wrap;'>" . nl2br($sub) . "</div>";
                                    if ($desc !== '' && $desc !== $service) $rowsHtml .= "<div style='font-size:11px;color:#555;margin-top:4px;white-space:pre-wrap;'>" . nl2br($desc) . "</div>";
                                    $rowsHtml .= "</td>\n";
                                    $rowsHtml .= "<td>" . ($qty !== '' ? $qty : '') . "</td>\n";
                                    $rowsHtml .= "<td>" . ($uprice !== '' ? 'Rs. ' . $uprice : '') . "</td>\n";
                                    $rowsHtml .= "<td>" . ($t !== '' ? 'Rs. ' . $t : '') . "</td>\n";
                                    $rowsHtml .= "</tr>\n";
                                }
                            }
                        }

                        if (!$builtFromDetails) {
                            $raw = $itemType;
                            $parts = array_filter(array_map('trim', explode(',', $raw)));
                            if (count($parts) > 1) {
                                foreach ($parts as $idx => $p) {
                                    $pSafe = htmlspecialchars($p);
                                    if ($idx === 0) {
                                        $rowsHtml .= "<tr>\n";
                                        $rowsHtml .= "<td><div style='font-weight:600;'>{$pSafe}</div>";
                                        if ($description) $rowsHtml .= "<div style='font-size:11px;color:#555;margin-top:4px;white-space:pre-wrap;'>" . htmlspecialchars($description) . "</div>";
                                        $rowsHtml .= "</td>\n";
                                        $rowsHtml .= "<td>" . ($quantity !== null ? $quantity : '') . "</td>\n";
                                        $rowsHtml .= "<td>Rs. {$unitPrice}</td>\n";
                                        $rowsHtml .= "<td>Rs. {$total}</td>\n";
                                        $rowsHtml .= "</tr>\n";
                                    } else {
                                        $rowsHtml .= "<tr>\n";
                                        $rowsHtml .= "<td><div style='font-weight:600;'>{$pSafe}</div></td>\n";
                                        $rowsHtml .= "<td></td>\n";
                                        $rowsHtml .= "<td></td>\n";
                                        $rowsHtml .= "<td></td>\n";
                                        $rowsHtml .= "</tr>\n";
                                    }
                                }
                            } else {
                                $rowsHtml .= "<tr>\n";
                                $rowsHtml .= "<td>" . htmlspecialchars($itemType) . "</td>\n";
                                $rowsHtml .= "<td>" . htmlspecialchars($description) . "</td>\n";
                                $rowsHtml .= "<td>" . ($quantity !== null ? $quantity : '') . "</td>\n";
                                $rowsHtml .= "<td>Rs. {$unitPrice}</td>\n";
                                $rowsHtml .= "<td>Rs. {$total}</td>\n";
                                $rowsHtml .= "</tr>\n";
                            }
                        }

                        echo $rowsHtml;
                    ?>
                    <tr class='total-row'>
                        <td colspan='4' style='text-align: right;'>Total Amount:</td>
                        <td>Rs. <?php echo htmlspecialchars($total); ?></td>
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
