<?php
// AYONION-CMS/pdf_generator.php - Simple PDF generator without external libraries

class SimplePDF {
    private $content = '';
    class SimplePDF {
        private $ops = [];
        private $fontSize = 12;
        private $lineHeight = 1.2;
        private $pageWidth = 612; // default US Letter width in points

        public function __construct() {
            // nothing appended yet; we record drawing ops and build PDF on generate()
        }

        public function addText($text, $x = 50, $y = 750, $fontSize = 12) {
            $this->ops[] = [
                'type' => 'text',
                'x' => $x,
                'y' => $y,
                'font' => 'F1',
                'size' => $fontSize,
                'text' => $text,
            ];
        }

        public function addLine($x1, $y1, $x2, $y2) {
            $this->ops[] = [
                'type' => 'line',
                'x1' => $x1,
                'y1' => $y1,
                'x2' => $x2,
                'y2' => $y2,
            ];
        }

        private function buildContentStream() {
            if (empty($this->ops)) return '';

            // compute bounding box of ops
            $minY = PHP_INT_MAX;
            $maxY = PHP_INT_MIN;
            foreach ($this->ops as $op) {
                if ($op['type'] === 'text') {
                    $y = $op['y'];
                    $minY = min($minY, $y);
                    $maxY = max($maxY, $y);
                } else {
                    $minY = min($minY, $op['y1'], $op['y2']);
                    $maxY = max($maxY, $op['y1'], $op['y2']);
                }
            }

            // margins (points)
            $bottomMargin = 20;
            $topMargin = 20;

            // normalized height
            $contentHeight = ($maxY - $minY) + $topMargin + $bottomMargin;

            // shift ops so minY maps to bottomMargin
            $yShift = -$minY + $bottomMargin;

            $stream = "BT\n"; // begin text once, but we'll close/reopen as needed
            foreach ($this->ops as $op) {
                if ($op['type'] === 'text') {
                    $x = $op['x'];
                    $y = $op['y'] + $yShift;
                    $size = $op['size'];
                    $txt = $this->escapeText($op['text']);
                    $stream .= "/{$op['font']} $size Tf\n";
                    $stream .= "$x $y Td\n";
                    $stream .= "($txt) Tj\n";
                    $stream .= "ET\nBT\n"; // close and reopen text object to reset text matrix
                } else {
                    // draw line
                    $x1 = $op['x1'];
                    $y1 = $op['y1'] + $yShift;
                    $x2 = $op['x2'];
                    $y2 = $op['y2'] + $yShift;
                    $stream .= "ET\n"; // ensure text is closed before path ops
                    $stream .= "$x1 $y1 m\n";
                    $stream .= "$x2 $y2 l\n";
                    $stream .= "S\nBT\n"; // stroke the path
                }
            }
            $stream .= "ET\n";

            return [
                'stream' => $stream,
                'height' => ceil($contentHeight),
                'yShift' => $yShift,
            ];
        }

        private function escapeText($text) {
            $replacements = ["\\" => "\\\\", "(" => "\\(", ")" => "\\)"];
            return strtr($text, $replacements);
        }

        public function generate() {
            $header = "%PDF-1.4\n";

            // basic objects header
            $header .= "1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n";

            // compute content stream and desired page height
            $contentInfo = $this->buildContentStream();
            $contentStream = $contentInfo ? $contentInfo['stream'] : '';
            $pageHeight = $contentInfo ? $contentInfo['height'] : 792;

            // Pages object
            $header .= "2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n";

            // Page object with calculated MediaBox height
            $header .= "3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 {$this->pageWidth} {$pageHeight}]\n/Contents 4 0 R\n/Resources <<\n/Font <<\n/F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\n>>\n>>\n>>\nendobj\n";

            // Content stream object
            $streamLen = strlen($contentStream);
            $header .= "4 0 obj\n<<\n/Length $streamLen\n>>\nstream\n";
            $header .= $contentStream;
            $header .= "endstream\nendobj\n";

            // xref and trailer (very minimal; offsets not accurate but acceptable for many readers)
            $header .= "xref\n0 5\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000120 00000 n \n0000000300 00000 n \n";
            $header .= "trailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n0\n%%EOF\n";

            return $header;
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
