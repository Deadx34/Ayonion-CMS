<?php
// AYONION-CMS/includes/document_number_generator.php
// Generates document numbers in format: Q10P001202511 (Quotation), I10P001202511 (Invoice), R10P001202511 (Receipt)

/**
 * Generate document number based on type and current month's count
 * Format: [PREFIX]10P[COUNT][YEAR][MONTH]
 * Example: Q10P001202511 = 1st Quotation of November 2025
 * 
 * @param mysqli $conn Database connection
 * @param string $docType Document type: 'quotation', 'invoice', or 'receipt'
 * @return string Generated document number
 */
function generateDocumentNumber($conn, $docType) {
    // Determine prefix based on document type
    $prefixMap = [
        'quotation' => 'Q',
        'invoice' => 'I',
        'receipt' => 'R'
    ];
    
    $prefix = $prefixMap[strtolower($docType)] ?? 'D';
    
    // Get current year and month
    $year = date('Y');
    $month = date('m');
    
    // Get count of documents of this type created in current month
    $startOfMonth = date('Y-m-01 00:00:00');
    $endOfMonth = date('Y-m-t 23:59:59');
    $count = 1;
    do {
        $documentNumber = sprintf('%s10P%03d%s%s', $prefix, $count, $year, $month);
        $sqlCheck = "SELECT 1 FROM documents WHERE document_number = ? LIMIT 1";
        $stmtCheck = $conn->prepare($sqlCheck);
        if (!$stmtCheck) {
            error_log("Failed to prepare statement: " . $conn->error);
            return $prefix . '10P' . time();
        }
        $stmtCheck->bind_param('s', $documentNumber);
        $stmtCheck->execute();
        $stmtCheck->store_result();
        $exists = $stmtCheck->num_rows > 0;
        $stmtCheck->close();
        if ($exists) {
            $count++;
        } else {
            break;
        }
    } while (true);
    return $documentNumber;
}

/**
 * Parse document number to extract information
 * 
 * @param string $documentNumber Document number to parse
 * @return array Parsed information (prefix, count, year, month)
 */
function parseDocumentNumber($documentNumber) {
    // Pattern: [PREFIX]10P[COUNT (3 digits)][YEAR (4 digits)][MONTH (2 digits)]
    if (preg_match('/^([A-Z])10P(\d{3})(\d{4})(\d{2})$/', $documentNumber, $matches)) {
        return [
            'prefix' => $matches[1],
            'count' => (int)$matches[2],
            'year' => $matches[3],
            'month' => $matches[4],
            'type' => getDocumentTypeFromPrefix($matches[1])
        ];
    }
    return null;
}

/**
 * Get document type from prefix
 * 
 * @param string $prefix Document prefix (Q, I, R)
 * @return string Document type
 */
function getDocumentTypeFromPrefix($prefix) {
    $typeMap = [
        'Q' => 'Quotation',
        'I' => 'Invoice',
        'R' => 'Receipt'
    ];
    return $typeMap[$prefix] ?? 'Unknown';
}

/**
 * Format document number for display
 * 
 * @param string $documentNumber Document number
 * @return string Formatted display string
 */
function formatDocumentNumberDisplay($documentNumber) {
    $parsed = parseDocumentNumber($documentNumber);
    if ($parsed) {
        $monthName = date('F', mktime(0, 0, 0, $parsed['month'], 1));
        return sprintf('%s #%d - %s %s', 
            $parsed['type'], 
            $parsed['count'], 
            $monthName, 
            $parsed['year']
        );
    }
    return $documentNumber;
}
?>
