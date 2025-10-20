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
    
    $companyName = $settings['company_name'] ?? 'AYONION CMS';
    $companyEmail = $settings['email'] ?? '';
    $companyPhone = $settings['phone'] ?? '';
    $companyAddress = $settings['address'] ?? '';
    
    $html = "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>{$title} - {$docNumber}</title>
        <style>
            @page { margin: 0.75in; }
            body { 
                font-family: 'Arial', sans-serif; 
                margin: 0; 
                padding: 0; 
                color: #333; 
                line-height: 1.4;
            }
            .header { 
                text-align: center; 
                margin-bottom: 30px; 
                border-bottom: 3px solid #6366f1; 
                padding-bottom: 20px; 
            }
            .company-name { 
                font-size: 24px; 
                font-weight: bold; 
                color: #6366f1; 
                margin: 10px 0; 
            }
            .document-title { 
                font-size: 28px; 
                font-weight: bold; 
                color: #6366f1; 
                margin: 20px 0 10px 0; 
            }
            .document-number { 
                font-size: 16px; 
                color: #666; 
                margin-bottom: 10px;
            }
            .content { 
                display: flex; 
                justify-content: space-between; 
                margin: 30px 0; 
            }
            .client-info, .company-details { 
                width: 45%; 
            }
            .section-title { 
                font-size: 16px; 
                font-weight: bold; 
                color: #6366f1; 
                margin-bottom: 10px; 
                border-bottom: 1px solid #eee; 
                padding-bottom: 5px; 
            }
            .info-row { 
                margin: 8px 0; 
                font-size: 14px;
            }
            .label { 
                font-weight: bold; 
                display: inline-block;
                width: 100px;
            }
            .table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 30px 0; 
                font-size: 14px;
            }
            .table th, .table td { 
                padding: 12px 8px; 
                text-align: left; 
                border-bottom: 1px solid #ddd; 
            }
            .table th { 
                background-color: #f8f9fa; 
                font-weight: bold; 
                color: #6366f1;
            }
            .total-row { 
                background-color: #f8f9fa; 
                font-weight: bold; 
                border-top: 2px solid #6366f1;
            }
            .footer { 
                margin-top: 50px; 
                padding-top: 20px; 
                border-top: 2px solid #eee; 
                text-align: center; 
                color: #666; 
                font-size: 12px; 
            }
            .amount { 
                text-align: right; 
                font-weight: bold;
            }
            @media print { 
                body { margin: 0; }
                .no-print { display: none; }
            }
        </style>
    </head>
    <body>
        <div class='header'>
            <div class='company-name'>{$companyName}</div>
            <div style='margin: 10px 0; color: #666; font-size: 14px;'>
                " . ($companyEmail ? "Email: {$companyEmail}<br>" : "") . "
                " . ($companyPhone ? "Phone: {$companyPhone}<br>" : "") . "
                " . ($companyAddress ? "Address: {$companyAddress}" : "") . "
            </div>
            <div class='document-title'>{$title}</div>
            <div class='document-number'>Document #: {$docNumber}</div>
            <div style='margin-top: 10px; color: #666; font-size: 14px;'>Date: " . date('F j, Y', strtotime($doc['date'])) . "</div>
        </div>
        
        <div class='content'>
            <div class='client-info'>
                <div class='section-title'>Bill To:</div>
                <div class='info-row'><span class='label'>Company:</span> {$doc['company_name']}</div>
                <div class='info-row'><span class='label'>Partner ID:</span> {$doc['partner_id']}</div>
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
                    <th style='width: 20%;'>Item Type</th>
                    <th style='width: 35%;'>Description</th>
                    <th style='width: 15%; text-align: center;'>Quantity</th>
                    <th style='width: 15%; text-align: right;'>Unit Price</th>
                    <th style='width: 15%; text-align: right;'>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{$doc['item_type']}</td>
                    <td>{$doc['description']}</td>
                    <td style='text-align: center;'>{$doc['quantity']}</td>
                    <td class='amount'>Rs. " . number_format($doc['unit_price'], 2) . "</td>
                    <td class='amount'>Rs. " . number_format($doc['total'], 2) . "</td>
                </tr>
                <tr class='total-row'>
                    <td colspan='4' style='text-align: right; font-size: 16px;'>Total Amount:</td>
                    <td class='amount' style='font-size: 16px;'>Rs. " . number_format($doc['total'], 2) . "</td>
                </tr>
            </tbody>
        </table>
        
        <div class='footer'>
            <p style='margin: 10px 0; font-size: 14px;'><strong>Thank you for your business!</strong></p>
            <p style='margin: 5px 0;'>Generated on " . date('F j, Y \a\t g:i A') . "</p>
            <p style='margin: 5px 0; color: #999;'>This document was generated automatically by AYONION CMS</p>
        </div>
    </body>
    </html>";
    
    return $html;
}
?>
