<?php
// AYONION-CMS/pdf_generator.php - Simple PDF generator without external libraries

class SimplePDF {
    private $content = '';
    private $fontSize = 12;
    private $lineHeight = 1.2;
    
    public function __construct() {
        $this->content = "%PDF-1.4\n";
        $this->content .= "1 0 obj\n";
        $this->content .= "<<\n";
        $this->content .= "/Type /Catalog\n";
        $this->content .= "/Pages 2 0 R\n";
        $this->content .= ">>\n";
        $this->content .= "endobj\n";
    }
    
    public function addText($text, $x = 50, $y = 750, $fontSize = 12) {
        $this->content .= "BT\n";
        $this->content .= "/F1 $fontSize Tf\n";
        $this->content .= "$x $y Td\n";
        $this->content .= "($text) Tj\n";
        $this->content .= "ET\n";
    }
    
    public function addLine($x1, $y1, $x2, $y2) {
        $this->content .= "$x1 $y1 m\n";
        $this->content .= "$x2 $y2 l\n";
        $this->content .= "S\n";
    }
    
    public function generate() {
        // Add page object
        $this->content .= "2 0 obj\n";
        $this->content .= "<<\n";
        $this->content .= "/Type /Pages\n";
        $this->content .= "/Kids [3 0 R]\n";
        $this->content .= "/Count 1\n";
        $this->content .= ">>\n";
        $this->content .= "endobj\n";
        
        // Add page content
        $this->content .= "3 0 obj\n";
        $this->content .= "<<\n";
        $this->content .= "/Type /Page\n";
        $this->content .= "/Parent 2 0 R\n";
        $this->content .= "/MediaBox [0 0 612 792]\n";
        $this->content .= "/Contents 4 0 R\n";
        $this->content .= "/Resources <<\n";
        $this->content .= "/Font <<\n";
        $this->content .= "/F1 <<\n";
        $this->content .= "/Type /Font\n";
        $this->content .= "/Subtype /Type1\n";
        $this->content .= "/BaseFont /Helvetica\n";
        $this->content .= ">>\n";
        $this->content .= ">>\n";
        $this->content .= ">>\n";
        $this->content .= ">>\n";
        $this->content .= "endobj\n";
        
        // Add content stream
        $this->content .= "4 0 obj\n";
        $this->content .= "<<\n";
        $this->content .= "/Length " . strlen($this->content) . "\n";
        $this->content .= ">>\n";
        $this->content .= "stream\n";
        $this->content .= $this->content;
        $this->content .= "endstream\n";
        $this->content .= "endobj\n";
        
        // Add xref table
        $this->content .= "xref\n";
        $this->content .= "0 5\n";
        $this->content .= "0000000000 65535 f \n";
        $this->content .= "0000000009 00000 n \n";
        $this->content .= "0000000058 00000 n \n";
        $this->content .= "0000000115 00000 n \n";
        $this->content .= "0000000274 00000 n \n";
        
        // Add trailer
        $this->content .= "trailer\n";
        $this->content .= "<<\n";
        $this->content .= "/Size 5\n";
        $this->content .= "/Root 1 0 R\n";
        $this->content .= ">>\n";
        $this->content .= "startxref\n";
        $this->content .= "0\n";
        $this->content .= "%%EOF\n";
        
        return $this->content;
    }
}

// Function to generate a simple PDF document
function generateSimplePDF($doc, $settings) {
    $pdf = new SimplePDF();
    
    // Add document title
    $pdf->addText("QUOTATION", 250, 750, 16);
    $pdf->addText("Document #: Q" . substr($doc['id'], -6), 250, 720, 12);
    $pdf->addText("Date: " . date('F j, Y', strtotime($doc['date'])), 250, 700, 12);
    
    // Add company info
    $pdf->addText($settings['company_name'] ?? 'AYONION CMS', 50, 750, 14);
    if ($settings['email']) {
        $pdf->addText("Email: " . $settings['email'], 50, 720, 10);
    }
    if ($settings['phone']) {
        $pdf->addText("Phone: " . $settings['phone'], 50, 700, 10);
    }
    
    // Add client info
    $pdf->addText("Bill To:", 50, 650, 12);
    $pdf->addText($doc['company_name'], 50, 630, 12);
    $pdf->addText("Partner ID: " . $doc['partner_id'], 50, 610, 10);
    
    // Add line separator
    $pdf->addLine(50, 580, 550, 580);
    
    // Add table headers
    $pdf->addText("Item Type", 50, 550, 12);
    $pdf->addText("Description", 150, 550, 12);
    $pdf->addText("Quantity", 350, 550, 12);
    $pdf->addText("Unit Price", 450, 550, 12);
    $pdf->addText("Total", 500, 550, 12);
    
    // Add line under headers
    $pdf->addLine(50, 540, 550, 540);
    
    // Add item data
    $pdf->addText($doc['item_type'], 50, 520, 10);
    $pdf->addText($doc['description'], 150, 520, 10);
    $pdf->addText($doc['quantity'], 350, 520, 10);
    $pdf->addText("Rs. " . number_format($doc['unit_price'], 2), 450, 520, 10);
    $pdf->addText("Rs. " . number_format($doc['total'], 2), 500, 520, 10);
    
    // Add total line
    $pdf->addLine(50, 480, 550, 480);
    $pdf->addText("Total Amount:", 400, 460, 12);
    $pdf->addText("Rs. " . number_format($doc['total'], 2), 500, 460, 12);
    
    // Add footer
    $pdf->addText("Thank you for your business!", 250, 400, 12);
    $pdf->addText("Generated on " . date('F j, Y \a\t g:i A'), 250, 380, 10);
    
    return $pdf->generate();
}
?>
