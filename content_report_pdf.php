<?php
// AYONION-CMS/content_report_pdf.php - Content report PDF generation

function generateContentReportPDF($client, $contents, $companyInfo, $isSelectedReport = false, $selectedCount = 0, $totalSelectedCredits = 0) {
    $totalCredits = $client['packageCredits'] + $client['extraCredits'] + $client['carriedForwardCredits'];
    $available = $totalCredits - $client['usedCredits'];
    
    // Add selected report info header if applicable
    $selectedReportInfo = '';
    if ($isSelectedReport && $selectedCount > 0) {
        $selectedReportInfo = "
            <div style='background: #fef3c7; padding: 12px; border-left: 4px solid #f59e0b; border-radius: 4px; margin-bottom: 20px;'>
                <p style='margin: 0; color: #92400e; font-size: 13px;'>
                    <strong>📋 Selected Items Report:</strong> This report includes <strong>{$selectedCount}</strong> selected content items with a total of <strong>{$totalSelectedCredits} credits</strong>.
                </p>
            </div>
        ";
    }
    
    $tableRows = '';
    if (count($contents) > 0) {
        foreach ($contents as $c) {
            $imageHtml = '';
            if (!empty($c['imageUrl'])) {
                $imageHtml = "<img src='{$c['imageUrl']}' alt='{$c['creative']}' style='width: 80px; height: 80px; object-fit: cover; border-radius: 4px; display: block; margin-bottom: 5px;'>";
            }
            $tableRows .= "
                <tr style='border-bottom: 1px solid #ddd;'>
                    <td style='padding: 10px; font-size: 12px;'>
                        {$imageHtml}
                        <div style='font-weight: bold;'>{$c['creative']}</div>
                    </td>
                    <td style='padding: 10px; font-size: 12px;'>{$c['contentType']}</td>
                    <td style='padding: 10px; text-align: center; font-size: 12px;'>{$c['credits']}</td>
                    <td style='padding: 10px; font-size: 12px;'>" . ($c['publishedDate'] ? date('M j, Y', strtotime($c['publishedDate'])) : '-') . "</td>
                    <td style='padding: 10px; font-size: 12px; color: #10b981;'>{$c['status']}</td>
                </tr>";
        }
    } else {
        $tableRows = '<tr><td colspan="5" style="padding: 20px; text-align: center; font-size: 12px;">No content records found.</td></tr>';
    }
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <link href='https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap' rel='stylesheet'>
        <title>Content Credit Report - {$client['companyName']}</title>
        <style>
            @page { margin: 0.75in; }
            body { 
                font-family: 'Lato', 'Arial', sans-serif; 
                margin: 0; 
                padding: 0; 
                color: #333; 
                line-height: 1.4;
            }
            .header { 
                display: flex; 
                align-items: center; 
                margin-bottom: 30px; 
                border-bottom: 2px solid #e5e7eb; 
                padding-bottom: 20px; 
            }
            .logo { 
                height: 60px; 
                margin-right: 20px; 
                object-fit: contain; 
            }
            .client-logo {
                display: block;
                margin: 0 auto 15px;
                max-height: 80px;
                object-fit: contain;
            }
            .company-name { 
                color: #2c3e50; 
                margin: 0; 
                font-size: 24px; 
                font-weight: bold;
            }
            .report-title { 
                color: #666; 
                margin: 5px 0; 
                font-size: 16px;
            }
            .client-info { 
                margin: 20px 0; 
                padding: 15px; 
                background: #f8f9fa; 
                border-radius: 8px; 
            }
            .client-name { 
                color: #2c3e50; 
                margin-bottom: 15px; 
                font-size: 18px; 
                font-weight: bold;
            }
            .credit-summary { 
                margin: 20px 0; 
                padding: 15px; 
                background: #cff4fc; 
                border-radius: 8px; 
            }
            .summary-title { 
                margin-bottom: 10px; 
                font-size: 16px; 
                font-weight: bold;
            }
            .summary-table { 
                width: 100%; 
                border-collapse: collapse; 
            }
            .summary-table td { 
                padding: 5px; 
                font-size: 14px;
            }
            .summary-total { 
                border-top: 2px solid #2c3e50; 
                font-weight: bold;
            }
            .available-credits { 
                color: #10b981; 
                font-weight: bold;
            }
            .content-table { 
                width: 100%; 
                border-collapse: collapse; 
                margin: 20px 0; 
                font-size: 12px;
            }
            .content-table th, .content-table td { 
                padding: 8px; 
                text-align: left; 
                border-bottom: 1px solid #ddd; 
            }
            .content-table th { 
                background-color: #f8f9fa; 
                font-weight: bold; 
                color: #2c3e50;
            }
            .footer { 
                margin-top: 40px; 
                padding-top: 20px; 
                border-top: 2px solid #eee; 
                text-align: center; 
                color: #666; 
                font-size: 12px; 
            }
            @media print { 
                body { margin: 0; }
            }
        </style>
    </head>
    <body>
        <div class='header'>
            " . ($companyInfo['logoUrl'] ? "<img src='{$companyInfo['logoUrl']}' alt='Logo' class='logo'>" : '') . "
            <div>
                <h1 class='company-name'>{$companyInfo['name']}</h1>
                <p class='report-title'>Content Credit Usage Report</p>
            </div>
        </div>
        
        " . (!empty($client['logoUrl']) 
            ? "<div style='text-align:center; margin:15px 0;'><img src='" . $client['logoUrl'] . "' alt='Client Logo' class='client-logo'></div>" 
            : "<div style='text-align:center; margin:15px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;'><h2 style='color: #6366f1; margin: 0; font-size: 22px;'>{$client['companyName']}</h2></div>") . "

        <div class='client-info'>
            <h3 class='client-name'>{$client['companyName']} ({$client['partnerId']})</h3>
            <p><strong>Reporting Cycle:</strong> Ends on " . date('F j, Y', strtotime($client['renewalDate'])) . "</p>
            <p><strong>Generated:</strong> " . date('F j, Y') . "</p>
        </div>
        
        {$selectedReportInfo}
        
        <div class='credit-summary'>
            <h4 class='summary-title'>Credit Summary</h4>
            <table class='summary-table'>
                <tr><td><strong>Package Credits:</strong></td><td>{$client['packageCredits']}</td></tr>
                <tr><td><strong>Extra Credits:</strong></td><td>{$client['extraCredits']}</td></tr>
                <tr><td><strong>Carried Credits:</strong></td><td>{$client['carriedForwardCredits']}</td></tr>
                <tr><td><strong>TOTAL Credits:</strong></td><td>{$totalCredits}</td></tr>
                <tr class='summary-total'><td><strong>Used Credits:</strong></td><td>{$client['usedCredits']}</td></tr>
                <tr><td><strong>Available Credits:</strong></td><td class='available-credits'>{$available}</td></tr>
            </table>
        </div>
        
        <table class='content-table'>
            <thead>
                <tr>
                    <th style='width: 30%;'>Creative</th>
                    <th style='width: 20%;'>Content Type</th>
                    <th style='width: 10%; text-align: center;'>Credits</th>
                    <th style='width: 20%;'>Published Date</th>
                    <th style='width: 20%;'>Status</th>
                </tr>
            </thead>
            <tbody>
                {$tableRows}
            </tbody>
        </table>
        
        <div class='footer'>
            <p><strong>Thank you for using AYONION CMS!</strong></p>
            <p>Generated on " . date('F j, Y \a\t g:i A') . "</p>
            <p>This report was generated automatically by AYONION CMS</p>
        </div>
    </body>
    </html>";
    
    return $html;
}
?>
