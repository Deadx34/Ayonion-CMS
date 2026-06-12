<?php
// AYONION-CMS/handler_data.php - Fetches ALL application data for frontend

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$response_data = [
    'success' => true,
    'clients' => [],
    'contentCredits' => [],
    'campaigns' => [],
    'documents' => [
        'quotations' => [],
        'invoices' => [],
        'receipts' => []
    ]
];

// --- 1. Fetch Clients ---
$client_sql = "SELECT * FROM clients";
$client_result = $conn->query($client_sql);
if ($client_result) {
    while ($row = $client_result->fetch_assoc()) {
        $response_data['clients'][] = [
            'id' => (int)$row['id'],
            'partnerId' => $row['partner_id'],
            'companyName' => $row['company_name'],
            'renewalDate' => $row['renewal_date'],
            'packageCredits' => (int)$row['package_credits'],
            'managingPlatforms' => $row['managing_platforms'],
            'industry' => $row['industry'],
            'logoUrl' => $row['logo_url'],
            'extraCredits' => (int)$row['extra_credits'],
            'carriedForwardCredits' => (int)$row['carried_forward_credits'],
            'usedCredits' => (int)$row['used_credits'],
            'totalAdBudget' => (float)$row['total_ad_budget'],
            'totalSpent' => (float)$row['total_spent'],
            'isPaused' => (int)$row['is_paused'],
            'pauseStartDate' => $row['pause_start_date'],
            'pauseEndDate' => $row['pause_end_date']
        ];
    }
}

// --- 2. Fetch Credits ---
$content_sql = "SELECT * FROM content_credits";
$content_result = $conn->query($content_sql);
if ($content_result) {
    while ($row = $content_result->fetch_assoc()) {
        $response_data['contentCredits'][] = [
            'id' => (int)$row['id'],
            'clientId' => (int)$row['client_id'],
            'creative' => $row['credit_type'], // Map credit_type to creative
            'contentType' => $row['credit_type'],
            'credits' => (int)$row['credits'],
            'startDate' => $row['date'],
            'status' => $row['status'] ?? 'In Progress',
            'publishedDate' => $row['published_date'] ?? null,
            'contentUrl' => $row['content_url'] ?? null,
            'imageUrl' => $row['image_url'] ?? null
        ];
    }
}

// --- 3. Fetch Campaigns ---
$campaign_sql = "SELECT * FROM campaigns";
$campaign_result = $conn->query($campaign_sql);
if ($campaign_result) {
    while ($row = $campaign_result->fetch_assoc()) {
        $response_data['campaigns'][] = [
            'id' => (int)$row['id'],
            'clientId' => (int)$row['client_id'],
            'platform' => $row['platform'],
            'adName' => $row['ad_name'],
            'adId' => $row['ad_id'],
            'resultType' => $row['result_type'],
            'results' => (int)$row['results'],
            'cpr' => (float)$row['cpr'],
            'reach' => (int)$row['reach'],
            'impressions' => (int)$row['impressions'],
            'spend' => (float)$row['spend'],
            'qualityRanking' => $row['quality_ranking'],
            'conversionRanking' => $row['conversion_ranking'],
            'evidenceImageUrl' => $row['evidence_image_url'] ?? null,
            'creativeImageUrl' => $row['creative_image_url'] ?? null,
            'dateAdded' => date('Y-m-d H:i:s') // Add current timestamp for display
        ];
    }
}

// --- 4. Fetch All Documents ---
$document_sql = "SELECT * FROM documents ORDER BY date DESC";
$document_result = $conn->query($document_sql);
if ($document_result) {
     while ($row = $document_result->fetch_assoc()) {
        // Prefer item_details JSON if present (new column). Fallback to legacy item_type otherwise.
        $item_type = $row['item_type'];
        $item_details = null;

        $raw_details = $row['item_details'] ?? null;
        if (!empty($raw_details)) {
            $decoded = json_decode($raw_details, true);
            if (is_string($decoded)) $decoded = json_decode($decoded, true);
            if (!is_array($decoded)) $decoded = json_decode(stripslashes($raw_details), true);
            if (is_array($decoded)) {
                if (isset($decoded['itemType']) || isset($decoded['item_type'])) $decoded = [$decoded];
                if (isset($decoded[0]) && is_array($decoded[0])) {
                    $normalized = [];
                    foreach ($decoded as $entry) {
                        $normalized[] = [
                            'itemType' => $entry['itemType'] ?? $entry['item_type'] ?? 'Service',
                            'description' => $entry['description'] ?? '',
                            'subText' => $entry['subText'] ?? $entry['sub_text'] ?? '',
                            'quantity' => isset($entry['quantity']) ? (float)$entry['quantity'] : 0,
                            'unitPrice' => isset($entry['unitPrice']) ? (float)$entry['unitPrice'] : (isset($entry['unit_price']) ? (float)$entry['unit_price'] : 0),
                            'total' => isset($entry['total']) ? (float)$entry['total'] : 0
                        ];
                    }
                    $item_details = $normalized;
                    $item_names = array_filter(array_column($normalized, 'itemType'));
                    $item_type = !empty($item_names) ? implode(', ', $item_names) : 'General';
                }
            }
        } else {
            // Legacy behavior: try to decode item_type if it contains JSON
            if (is_string($item_type)) {
                $raw_item_type = trim($item_type);
                $decodeCandidates = [
                    $raw_item_type,
                    stripslashes($raw_item_type),
                    str_replace('\\"', '"', $raw_item_type)
                ];
                $decoded_items = null;
                foreach ($decodeCandidates as $candidate) {
                    $tmp = json_decode($candidate, true);
                    if (is_string($tmp)) {
                        $tmp2 = json_decode($tmp, true);
                        if (is_array($tmp2)) $tmp = $tmp2;
                    }
                    if (is_array($tmp)) {
                        $decoded_items = $tmp;
                        break;
                    }
                }
                if (is_array($decoded_items)) {
                    if (isset($decoded_items['itemType']) || isset($decoded_items['item_type'])) $decoded_items = [$decoded_items];
                    if (isset($decoded_items[0]) && is_array($decoded_items[0])) {
                        $normalized = [];
                        foreach ($decoded_items as $entry) {
                            $normalized[] = [
                                'itemType' => $entry['itemType'] ?? $entry['item_type'] ?? 'Service',
                                'description' => $entry['description'] ?? '',
                                'subText' => $entry['subText'] ?? $entry['sub_text'] ?? '',
                                'quantity' => isset($entry['quantity']) ? (float)$entry['quantity'] : 0,
                                'unitPrice' => isset($entry['unitPrice']) ? (float)$entry['unitPrice'] : (isset($entry['unit_price']) ? (float)$entry['unit_price'] : 0),
                                'total' => isset($entry['total']) ? (float)$entry['total'] : 0
                            ];
                        }
                        $item_details = $normalized;
                        $item_names = array_filter(array_column($normalized, 'itemType'));
                        $item_type = !empty($item_names) ? implode(', ', $item_names) : 'General';
                    } elseif (isset($decoded_items[0]) && is_string($decoded_items[0])) {
                        $item_type = implode(', ', $decoded_items);
                    }
                }
            }
        }
        
        $formatted_doc = [
            'id' => (int)$row['id'],
            'documentNumber' => $row['document_number'] ?? '',
            'clientId' => (int)$row['client_id'],
            'clientName' => $row['client_name'] ?: 'Unknown Client',
            'docType' => $row['doc_type'],
            'itemType' => $item_type ?: 'General',
            'rawItemType' => $row['item_type'] ?? '',
            'itemDetails' => $item_details, // Include detailed item information
            'description' => $row['description'] ?: '',
            'quantity' => (int)$row['quantity'],
            'unitPrice' => (float)$row['unit_price'],
            'total' => (float)$row['total'],
            'date' => $row['date']
        ];
        
        // Sort documents into their respective types for the frontend
        if ($row['doc_type'] === 'quotation') {
            $response_data['documents']['quotations'][] = $formatted_doc;
        } elseif ($row['doc_type'] === 'invoice') {
            $response_data['documents']['invoices'][] = $formatted_doc;
        } elseif ($row['doc_type'] === 'receipt') {
            $response_data['documents']['receipts'][] = $formatted_doc;
        }
    }
}

echo json_encode($response_data);

$conn->close();
?>