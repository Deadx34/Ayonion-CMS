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
            'totalSpent' => (float)$row['total_spent']
        ];
    }
}

// --- 2. Fetch Content Credits ---
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
            'status' => 'In Progress', // Default status
            'publishedDate' => null
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
            'evidenceUrls' => json_decode($row['evidence_urls'] ?? '[]'),
            'evidenceFiles' => json_decode($row['evidence_files'] ?? '[]'),
            'dateAdded' => date('Y-m-d H:i:s') // Add current timestamp for display
        ];
    }
}

// --- 4. Fetch All Documents ---
$document_sql = "SELECT * FROM documents ORDER BY date DESC";
$document_result = $conn->query($document_sql);
if ($document_result) {
     while ($row = $document_result->fetch_assoc()) {
        // Sort documents into their respective types for the frontend
        if ($row['doc_type'] === 'quotation') {
            $response_data['documents']['quotations'][] = $row;
        } elseif ($row['doc_type'] === 'invoice') {
            $response_data['documents']['invoices'][] = $row;
        } elseif ($row['doc_type'] === 'receipt') {
            $response_data['documents']['receipts'][] = $row;
        }
    }
}

echo json_encode($response_data);

$conn->close();
?>