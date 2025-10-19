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
    
    // Generate PDF content
    $htmlContent = generateContentReportPDF($client, $contents, $companyInfo);
    
    // Try to generate actual PDF, fallback to HTML
    $pdfContent = generatePDFFromHTML($htmlContent);
    
    if (strpos($pdfContent, '<!DOCTYPE html>') !== false) {
        // HTML content - set headers for browser PDF conversion
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: inline; filename="Content_Report.pdf"');
        echo $htmlContent;
    } else {
        // Actual PDF content
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Content_Report_' . date('Y-m-d') . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $pdfContent;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
