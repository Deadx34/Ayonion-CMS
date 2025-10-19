<?php
// AYONION-CMS/tcpdf_generator.php - Simple PDF generator without external dependencies

class SimplePDFGenerator {
    private $content = '';
    private $objects = [];
    private $objectCounter = 1;
    
    public function __construct() {
        $this->content = "%PDF-1.4\n";
    }
    
    private function addObject($content) {
        $this->objects[] = $content;
        return $this->objectCounter++;
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
        // Create catalog object
        $catalogId = $this->addObject("<<\n/Type /Catalog\n/Pages 2 0 R\n>>");
        
        // Create pages object
        $pagesId = $this->addObject("<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>");
        
        // Create page object
        $pageId = $this->addObject("<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n/Resources <<\n/Font <<\n/F1 <<\n/Type /Font\n/Subtype /Type1\n/BaseFont /Helvetica\n>>\n>>\n>>\n>>");
        
        // Create content stream
        $contentStream = $this->content;
        $contentId = $this->addObject("<<\n/Length " . strlen($contentStream) . "\n>>\nstream\n" . $contentStream . "\nendstream");
        
        // Build PDF structure
        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj\n" . $this->objects[0] . "\nendobj\n";
        $pdf .= "2 0 obj\n" . $this->objects[1] . "\nendobj\n";
        $pdf .= "3 0 obj\n" . $this->objects[2] . "\nendobj\n";
        $pdf .= "4 0 obj\n" . $this->objects[3] . "\nendobj\n";
        
        // Add xref table
        $pdf .= "xref\n";
        $pdf .= "0 5\n";
        $pdf .= "0000000000 65535 f \n";
        $pdf .= "0000000009 00000 n \n";
        $pdf .= "0000000058 00000 n \n";
        $pdf .= "0000000115 00000 n \n";
        $pdf .= "0000000274 00000 n \n";
        
        // Add trailer
        $pdf .= "trailer\n";
        $pdf .= "<<\n";
        $pdf .= "/Size 5\n";
        $pdf .= "/Root 1 0 R\n";
        $pdf .= ">>\n";
        $pdf .= "startxref\n";
        $pdf .= "0\n";
        $pdf .= "%%EOF\n";
        
        return $pdf;
    }
}

// Function to create a simple PDF document
function createSimplePDF($title, $content) {
    $pdf = new SimplePDFGenerator();
    
    // Add title
    $pdf->addText($title, 50, 750, 16);
    $pdf->addLine(50, 740, 550, 740);
    
    // Add content (split into lines)
    $lines = explode("\n", $content);
    $y = 720;
    foreach ($lines as $line) {
        if ($y < 50) break; // Prevent going off page
        $pdf->addText($line, 50, $y, 12);
        $y -= 20;
    }
    
    return $pdf->generate();
}
?>
