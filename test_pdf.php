<?php
// AYONION-CMS/test_pdf.php - Test PDF generation

header('Content-Type: text/html; charset=UTF-8');

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test PDF Generation</title>
    <style>
        @page { margin: 0.75in; }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
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
        .content { 
            margin: 30px 0; 
            padding: 20px; 
            background: #f8f9fa; 
            border-radius: 8px; 
        }
        .footer { 
            margin-top: 50px; 
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
    <div class="header">
        <div class="company-name">AYONION CMS</div>
        <div style="margin: 10px 0; color: #666; font-size: 14px;">
            Email: info@ayonion.com<br>
            Phone: +94 77 123 4567<br>
            Address: Colombo, Sri Lanka
        </div>
        <div class="document-title">TEST DOCUMENT</div>
        <div style="margin-top: 10px; color: #666; font-size: 14px;">Date: ' . date('F j, Y') . '</div>
    </div>
    
    <div class="content">
        <h3>PDF Generation Test</h3>
        <p>This is a test document to verify that PDF generation is working correctly.</p>
        <p><strong>Features tested:</strong></p>
        <ul>
            <li>Proper HTML structure</li>
            <li>CSS styling for print</li>
            <li>Professional formatting</li>
            <li>Browser PDF generation</li>
        </ul>
    </div>
    
    <div class="footer">
        <p><strong>Thank you for using AYONION CMS!</strong></p>
        <p>Generated on ' . date('F j, Y \a\t g:i A') . '</p>
        <p>This document was generated automatically by AYONION CMS</p>
    </div>
    
    <script>
        // Auto-trigger print dialog for PDF generation
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>';

echo $html;
?>
