<?php
// AYONION-CMS/content_report_pdf.php - Content report PDF generation

function generateContentReportPDF($client, $contents, $companyInfo, $isSelectedReport = false, $selectedCount = 0, $totalSelectedCredits = 0) {
    $totalCredits = $client['packageCredits'] + $client['extraCredits'] + $client['carriedForwardCredits'];
    $available = $totalCredits - $client['usedCredits'];
    
    // Selected report info removed as per user request
    $selectedReportInfo = '';
    
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
                </tr>";
        }
    } else {
        $tableRows = '<tr><td colspan="4" style="padding: 20px; text-align: center; font-size: 12px;">No content records found.</td></tr>';
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
                margin: 0 0 15px 0;
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
                background-color: #030b0d; 
                font-weight: bold; 
                color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                .content-table th {
                    background-color: #030b0d !important;
                    color: white !important;
                }
                .report-header {
                    position: relative;
                    page-break-after: avoid;
                }
                .report-content {
                    page-break-before: avoid;
                }
            }
        </style>
    </head>
    <body>
        <div style='font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; background: white;'>
            <!-- Company Header -->
            <div class='report-header' style='background: #0d0e10; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center;'>
                <div style='display: flex; align-items: center;'>
                    <img src='" . ($companyInfo['logoDark'] ?? $companyInfo['logo'] ?? 'uploads/logos/favicon.svg') . "' alt='Company Logo' style='height: 60px; width: auto; object-fit: contain;'>
                </div>
                <div style='text-align: right; color: white;'>
                    <p style='margin: 0; font-size: 12px; line-height: 1.6;'>" . ($companyInfo['website'] ?? 'www.ayonionstudios.com') . "</p>
                    <p style='margin: 0; font-size: 12px; line-height: 1.6;'>" . ($companyInfo['email'] ?? 'info@ayonionstudios.com') . "</p>
                    <p style='margin: 0; font-size: 11px; line-height: 1.6; opacity: 0.8;'>© " . ($companyInfo['name'] ?? 'Ayonion Studios') . "</p>
                </div>
            </div>

            <div class='report-content' style='padding: 40px;'>
                <!-- Report Title -->
                <div style='margin-bottom: 30px; border-bottom: 3px solid #052C47; padding-bottom: 20px;'>
                    <img class='client-logo' src='" . ($client['logoUrl'] ?? 'uploads/logos/favicon.svg') . "' alt='Client Logo'>
                    <h1 style='color: #052C47; margin: 0; font-size: 32px; text-align: center;'>Content Credit Usage Report</h1>
                    <p style='color: #666; margin: 10px 0 5px 0; font-size: 18px; text-align: center;'>{$client['companyName']} ({$client['partnerId']})</p>
                    <p style='color: #999; margin: 0; font-size: 14px; text-align: center;'>Reporting Cycle Ends: " . date('F j, Y', strtotime($client['renewalDate'])) . "</p>
                    <p style='color: #999; margin: 5px 0 0 0; font-size: 12px; text-align: center;'>Generated on " . date('F j, Y') . "</p>
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
        <table class='content-table'>
            <thead>
                <tr>
                    <th style='width: 35%;'>Creative</th>
                    <th style='width: 25%;'>Content Type</th>
                    <th style='width: 15%; text-align: center;'>Credits</th>
                    <th style='width: 25%;'>Published Date</th>
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
            </div>
        </div>
    </body>
    </html>";
    
    return $html;
}
?>
