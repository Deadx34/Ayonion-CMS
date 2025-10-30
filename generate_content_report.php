<?php
// AYONION-CMS/generate_content_report.php - Content report PDF generation

header('Content-Type: application/pdf');
include 'includes/config.php';
include 'content_report_pdf.php';

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception("Invalid JSON input");
    }
    
    $client = $input['client'];
    $contents = $input['contents'];
    $companyInfo = $input['companyInfo'];
    $isSelectedReport = isset($input['isSelectedReport']) ? $input['isSelectedReport'] : false;
    $selectedCount = isset($input['selectedCount']) ? $input['selectedCount'] : 0;
    $totalSelectedCredits = isset($input['totalSelectedCredits']) ? $input['totalSelectedCredits'] : 0;
    
    // Generate PDF content
    $htmlContent = generateContentReportPDF($client, $contents, $companyInfo, $isSelectedReport, $selectedCount, $totalSelectedCredits);
    
    // Use browser's PDF generation with proper headers
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: inline; filename="Content_Report.pdf"');
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
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
