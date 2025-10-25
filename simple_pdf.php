<?php
// AYONION-CMS/simple_pdf.php - Reliable PDF generation using HTML to PDF conversion

function generatePDFFromHTML($html) {
    // Create a temporary HTML file
    $tempFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.html';
    file_put_contents($tempFile, $html);
    
    // Use wkhtmltopdf if available, otherwise fall back to browser print
    $pdfFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';
    
    // Try to use wkhtmltopdf (if available on server)
    $command = "wkhtmltopdf --page-size A4 --margin-top 0.75in --margin-right 0.75in --margin-bottom 0.75in --margin-left 0.75in --encoding UTF-8 --disable-smart-shrinking '$tempFile' '$pdfFile' 2>/dev/null";
    
    if (function_exists('exec') && exec($command) !== false && file_exists($pdfFile)) {
        $pdfContent = file_get_contents($pdfFile);
        unlink($tempFile);
        unlink($pdfFile);
        return $pdfContent;
    }
    
    // Fallback: Return HTML with proper headers for browser PDF generation
    unlink($tempFile);
    return $html;
}

function createPDFDocument($doc, $settings) {
    $docType = $doc['doc_type'];
    $docNumber = strtoupper(substr($docType, 0, 1)) . substr($doc['id'], -6);
    $title = strtoupper($docType);
    
    $companyLogo = $settings['logo_url'] ?? '';
    $companyName = $settings['company_name'] ?? 'AYONION STUDIOS';
    $companyEmail = $settings['email'] ?? 'info@ayonionstudios.com';
    $companyPhone = $settings['phone'] ?? '+94 (70) 610 1035';
    $companyAddress = $settings['address'] ?? 'No.59/1/C, Kaluwala road, Kossinna, Ganemulla. PV00231937';
    $companyWebsite = $settings['website'] ?? 'www.ayonionstudios.com';
    $companyTagline = $settings['tagline'] ?? 'Service beyond expectation';
    
    // Calculate document details
    $clientName = $doc['company_name'];
    $partnerId = $doc['partner_id'];
    $date = date('d/m/Y', strtotime($doc['date']));
    $validUntil = date('d/m/Y', strtotime($doc['date'] . ' +14 days'));
    
    // Handle multiple item types
    $itemTypes = json_decode($doc['item_type'], true);
    $items = [];
    $grandTotal = 0;
    
    if (is_array($itemTypes)) {
        foreach ($itemTypes as $itemType) {
            $items[] = [
                'description' => $itemType,
                'unit_price' => $doc['unit_price'],
                'quantity' => $doc['quantity'],
                'amount' => $doc['total']
            ];
            $grandTotal += $doc['total'];
        }
    } else {
        $items[] = [
            'description' => $doc['item_type'],
            'unit_price' => $doc['unit_price'],
            'quantity' => $doc['quantity'],
            'amount' => $doc['total']
        ];
        $grandTotal = $doc['total'];
    }
    
    $html = "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>{$title} - {$docNumber}</title>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
        <style>
            @page { margin: 0; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { 
                font-family: 'Arial', sans-serif; 
                color: #333; 
                line-height: 1.4;
                display: flex;
                min-height: 100vh;
            }
            .sidebar {
                width: 30%;
                background: #2c3e50;
                color: white;
                padding: 40px 30px;
                display: flex;
                flex-direction: column;
            }
            .main-content {
                width: 70%;
                background: white;
                padding: 40px;
                display: flex;
                flex-direction: column;
            }
            .company-logo {
                margin-bottom: 30px;
                text-align: center;
            }
            .company-name {
                font-size: 28px;
                font-weight: bold;
                color: white;
                margin-bottom: 8px;
                letter-spacing: 1px;
            }
            .company-tagline {
                font-size: 14px;
                color: #bdc3c7;
                margin-bottom: 40px;
                text-align: center;
                white-space: nowrap;
            }
            .contact-section {
                margin-bottom: 30px;
            }
            .contact-item {
                display: flex;
                align-items: center;
                margin-bottom: 12px;
                font-size: 14px;
            }
            .contact-icon {
                width: 16px;
                margin-right: 10px;
                text-align: center;
            }
            .social-section {
                margin-top: auto;
            }
            .social-title {
                font-size: 14px;
                color: #bdc3c7;
                margin-bottom: 10px;
            }
            .social-handle {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 15px;
            }
            .social-icons {
                display: flex;
                gap: 10px;
            }
            .social-icon {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                color: white;
            }
            .document-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 40px;
                padding-bottom: 20px;
                border-bottom: 2px solid #ecf0f1;
            }
            .customer-info {
                flex: 1;
            }
            .customer-label {
                font-size: 14px;
                color: #7f8c8d;
                margin-bottom: 5px;
            }
            .customer-name {
                font-size: 18px;
                font-weight: bold;
                color: #2c3e50;
            }
            .document-info {
                text-align: right;
            }
            .document-title {
                font-size: 36px;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 20px;
            }
            .document-details {
                font-size: 14px;
                color: #7f8c8d;
            }
            .document-details div {
                margin-bottom: 5px;
            }
            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin: 30px 0;
                font-size: 14px;
            }
            .items-table th {
                background: #f8f9fa;
                padding: 15px 10px;
                text-align: left;
                font-weight: bold;
                color: #2c3e50;
                border-bottom: 2px solid #ecf0f1;
            }
            .items-table td {
                padding: 15px 10px;
                border-bottom: 1px solid #ecf0f1;
            }
            .items-table .text-right {
                text-align: right;
            }
            .items-table .text-center {
                text-align: center;
            }
            .total-section {
                margin-top: 30px;
                text-align: right;
            }
            .total-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #ecf0f1;
            }
            .total-row.final {
                border-top: 2px solid #2c3e50;
                border-bottom: 2px solid #2c3e50;
                font-weight: bold;
                font-size: 18px;
                margin-top: 10px;
            }
            .notes-section {
                margin-top: 40px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            .notes-title {
                font-size: 16px;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 15px;
            }
            .notes-content {
                font-size: 14px;
                line-height: 1.6;
                color: #555;
            }
            .bank-details {
                margin-top: 20px;
                padding: 15px;
                background: #e8f4f8;
                border-radius: 5px;
                border-left: 4px solid #3498db;
            }
            .bank-title {
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 10px;
            }
            .footer {
                margin-top: auto;
                padding-top: 20px;
                border-top: 1px solid #ecf0f1;
                text-align: center;
                color: #7f8c8d;
                font-size: 14px;
            }
            @media print {
                body { margin: 0; }
                .no-print { display: none; }
            }
        </style>
    </head>
    <body>
        <div class='sidebar'>
            <div class='company-logo'>
                " . ($companyLogo ? "<img src='{$companyLogo}' alt='Logo' style='height: 60px; margin-bottom: 10px; object-fit: contain;'>" : "") . "
                <div class='company-tagline'>{$companyTagline}</div>
            </div>
            
            <div class='contact-section'>
                <div class='contact-item'>
                    <span class='contact-icon'>✉</span>
                    <span>{$companyEmail}</span>
                </div>
                <div class='contact-item'>
                    <span class='contact-icon'>📞</span>
                    <span>{$companyPhone}</span>
                </div>
                <div class='contact-item'>
                    <span class='contact-icon'>📍</span>
                    <span>{$companyAddress}</span>
                </div>
                <div class='contact-item'>
                    <span class='contact-icon'>🌐</span>
                    <span><strong>{$companyWebsite}</strong></span>
                </div>
            </div>
            
            <div class='social-section'>
                <div class='social-title'>Find us on social media:</div>
                <div class='social-handle'>ayonionstudios</div>
                <div class='social-icons'>
                    <div class='social-icon' style='background: #ff0000;'><i class='fab fa-youtube'></i></div>
                    <div class='social-icon' style='background: #e4405f;'><i class='fab fa-instagram'></i></div>
                    <div class='social-icon' style='background: #3b5998;'><i class='fab fa-facebook-f'></i></div>
                    <div class='social-icon' style='background: #1da1f2;'><i class='fab fa-twitter'></i></div>
                    <div class='social-icon' style='background: #0077b5;'><i class='fab fa-linkedin-in'></i></div>
                </div>
            </div>
        </div>
        
        <div class='main-content'>
            <div class='document-header'>
                <div class='customer-info'>
                    <div class='customer-label'>Customer</div>
                    <div class='customer-name'>{$clientName}</div>
                </div>
                <div class='document-info'>
                    <div class='document-title'>{$title}</div>
                    <div class='document-details'>
                        <div>Date: {$date}</div>
                        <div>Quote #: {$docNumber}</div>
                        <div>Valid Until: {$validUntil}</div>
                    </div>
                </div>
            </div>
            
            <table class='items-table'>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class='text-right'>Unit Price</th>
                        <th class='text-center'>Quantity</th>
                        <th class='text-right'>Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($items as $item) {
        $html .= "
                    <tr>
                        <td>{$item['description']}</td>
                        <td class='text-right'>" . number_format($item['unit_price'], 2) . "</td>
                        <td class='text-center'>{$item['quantity']}</td>
                        <td class='text-right'>" . number_format($item['amount'], 2) . "</td>
                    </tr>";
    }
    
    $html .= "
                </tbody>
            </table>
            
            <div class='total-section'>
                <div class='total-row'>
                    <span>Subtotal:</span>
                    <span>" . number_format($grandTotal, 2) . "</span>
                </div>
                <div class='total-row'>
                    <span>Discount:</span>
                    <span>-</span>
                </div>
                <div class='total-row final'>
                    <span>Total:</span>
                    <span>" . number_format($grandTotal, 2) . "</span>
                </div>
            </div>
            
            <div class='notes-section'>
                <div class='notes-title'>Thank you</div>
                <div class='notes-content'>
                    Thank you for reaching out Ayonion Studios. We will deliver you the best service possible.<br><br>
                    <strong>Payment Instructions:</strong><br>
                    • All cheques should be crossed and made payable to Ayonion Studios (pvt) Ltd.<br>
                    • A 50% of advance payment is required. (Excluding package payments)<br>
                    • The quotation is valid for two weeks from the day issued.<br>
                    • This is a computer generated quotation, No signature required.<br><br>
                    <div class='bank-details'>
                        <div class='bank-title'>Please deposit the advance payment to the below account</div>
                        <div><strong>Ayonion Studios (pvt) Ltd</strong></div>
                        <div><strong>101001037178</strong></div>
                        <div><strong>NDB Bank, Kadawatha Branch</strong></div>
                    </div>
                </div>
            </div>
            
            <div class='footer'>
                Thank you and have a good day! Team Ayonion Studios.
            </div>
        </div>
    </body>
    </html>";
    
    return $html;
}
?>
