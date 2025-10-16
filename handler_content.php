<?php
// AYONION-CMS/handler_clients.php - Handles CRUD operations for clients

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

// --- 1. HANDLE ADD CLIENT (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    
    // Generate a more unique ID
    $id = time() . mt_rand(100, 999); 
    
    $partnerId = $conn->real_escape_string($input['partnerId'] ?? '');
    $companyName = $conn->real_escape_string($input['companyName'] ?? '');
    $renewalDate = $conn->real_escape_string($input['renewalDate'] ?? null);
    $packageCredits = (int)($input['packageCredits'] ?? 0);
    $managingPlatforms = $conn->real_escape_string($input['managingPlatforms'] ?? '');
    $industry = $conn->real_escape_string($input['industry'] ?? '');
    $logoUrl = $conn->real_escape_string($input['logoUrl'] ?? '');

    if (empty($logoUrl) && !empty($companyName)) {
        $firstChar = strtoupper(substr($companyName, 0, 1));
        $logoUrl = "https://via.placeholder.com/150/6c757d/ffffff?text=" . urlencode($firstChar);
    }
    
    $sql = "INSERT INTO clients (
        id, partner_id, company_name, renewal_date, package_credits, managing_platforms, industry, logo_url,
        extra_credits, carried_forward_credits, used_credits, total_ad_budget, total_spent
    ) VALUES (
        '$id', '$partnerId', '$companyName', " . ($renewalDate ? "'$renewalDate'" : "NULL") . ", $packageCredits, 
        '$managingPlatforms', '$industry', '$logoUrl', 
        0, 0, 0, 0.00, 0.00
    )";

    // ✅ FIXED: Passed the $conn object to the query_db function
    if (query_db($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Client added successfully."]);
    } else {
        http_response_code(500);
        $error_msg = strpos($conn->error, 'Duplicate entry') !== false ? "Partner ID already exists." : "Failed to save client: Database Error.";
        echo json_encode(["success" => false, "message" => $error_msg]);
    }
} 
// --- 2. HANDLE DELETE CLIENT (GET) ---
else if ($action === 'delete') {
    $clientId = (int)($_GET['id'] ?? 0);
    $sql = "DELETE FROM clients WHERE id = $clientId";
    
    // ✅ FIXED: Passed the $conn object to the query_db function
    if (query_db($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Client and all related data deleted."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to delete client: " . $conn->error]);
    }
}
// --- 3. ERROR HANDLING ---
else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid API endpoint request."]);
}

$conn->close();
?>