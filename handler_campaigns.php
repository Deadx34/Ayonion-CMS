<?php
// AYONION-CMS/handler_campaigns.php - Handles campaigns add/delete

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

// Use transactions for data integrity
$conn->begin_transaction();

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

try {
    // --- 1. HANDLE ADD CAMPAIGN (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
        $id = time() . mt_rand(100, 999);
        $clientId = (int)($input['clientId'] ?? 0);
        $spend = (float)($input['spend'] ?? 0.00); 

        // Sanitize and escape all fields
        $platform = $conn->real_escape_string($input['platform'] ?? '');
        $adName = $conn->real_escape_string($input['adName'] ?? '');
        $adId = $conn->real_escape_string($input['adId'] ?? '');
        $resultType = $conn->real_escape_string($input['resultType'] ?? '');
        $results = (int)($input['results'] ?? 0);
        $cpr = (float)($input['cpr'] ?? 0.00);
        $reach = (int)($input['reach'] ?? 0);
        $impressions = (int)($input['impressions'] ?? 0);
        $qualityRanking = $conn->real_escape_string($input['qualityRanking'] ?? '');
        $conversionRanking = $conn->real_escape_string($input['conversionRanking'] ?? '');
        
        $evidenceImageUrl = $conn->real_escape_string($input['evidenceImageUrl'] ?? '');
        $creativeImageUrl = $conn->real_escape_string($input['creativeImageUrl'] ?? '');

        // 1. Add Campaign Record
        $sql_campaign = "INSERT INTO campaigns (
            id, client_id, platform, ad_name, ad_id, result_type, results, cpr, reach, impressions, spend, quality_ranking, conversion_ranking, evidence_image_url, creative_image_url
        ) VALUES (
            '$id', '$clientId', '$platform', '$adName', '$adId', '$resultType', $results, $cpr, $reach, $impressions, $spend, '$qualityRanking', '$conversionRanking', '$evidenceImageUrl', '$creativeImageUrl'
        )";

        // 2. Update Client's Total Spent
        $sql_client = "UPDATE clients SET total_spent = total_spent + $spend WHERE id = $clientId";

        // ✅ FIXED: Passed the $conn object to the query_db functions
        if (query_db($conn, $sql_campaign) && query_db($conn, $sql_client)) {
            $conn->commit();
            echo json_encode(["success" => true, "message" => "Campaign added and budget updated."]);
        } else {
            throw new Exception("Failed to save campaign.");
        }
    } 
    // --- 2. HANDLE DELETE CAMPAIGN (GET) ---
    else if ($action === 'delete') {
        $campaignId = (int)($_GET['id'] ?? 0);

        // A. Get campaign details before deleting
        $fetch_sql = "SELECT client_id, spend FROM campaigns WHERE id = $campaignId";
        $result = $conn->query($fetch_sql);
        if (!$result || $result->num_rows !== 1) {
            throw new Exception("Campaign not found.");
        }
        $campaign = $result->fetch_assoc();
        $clientId = (int)$campaign['client_id'];
        $spendToRevert = (float)$campaign['spend'];

        // B. Delete the campaign
        $sql_delete = "DELETE FROM campaigns WHERE id = $campaignId";
        
        // C. Revert the client's total_spent
        $sql_revert = "UPDATE clients SET total_spent = total_spent - $spendToRevert WHERE id = $clientId";

        if (query_db($conn, $sql_delete) && query_db($conn, $sql_revert)) {
            $conn->commit();
            echo json_encode(["success" => true, "message" => "Campaign deleted and budget reverted."]);
        } else {
            throw new Exception("Failed to delete campaign and revert budget.");
        }
    } else {
        throw new Exception("Invalid API endpoint request.", 400);
    }
} catch (Exception $e) {
    $conn->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>