<?php
// AYONION-CMS/handler_content.php - Handles content credit add/delete and updates client used credits

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

try {
    // --- ADD CONTENT CREDIT (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
        $clientId = (int)($input['clientId'] ?? 0);
        $credits = (int)($input['credits'] ?? 0);
        $contentType = $conn->real_escape_string($input['contentType'] ?? '');
        $startDate = $conn->real_escape_string($input['startDate'] ?? '');

        if ($clientId <= 0 || $credits <= 0 || $contentType === '' || $startDate === '') {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "clientId, credits, contentType, startDate are required."]);
            exit;
        }

        $creative = $conn->real_escape_string($input['creative'] ?? $contentType);
        $status = $conn->real_escape_string($input['status'] ?? 'In Progress');
        $publishedDate = $conn->real_escape_string($input['publishedDate'] ?? '');
        $contentUrl = $conn->real_escape_string($input['contentUrl'] ?? '');
        $imageUrl = $conn->real_escape_string($input['imageUrl'] ?? '');
        
        // Check if new columns exist, if not use basic insert
        $checkColumns = "SHOW COLUMNS FROM content_credits LIKE 'content_url'";
        $columnCheck = $conn->query($checkColumns);
        
        if ($columnCheck && $columnCheck->num_rows > 0) {
            // New columns exist, use full insert
            $insertSql = "INSERT INTO content_credits (client_id, credit_type, credits, date, status, published_date, content_url, image_url) VALUES ($clientId, '$creative', $credits, '$startDate', '$status', '$publishedDate', '$contentUrl', '$imageUrl')";
        } else {
            // Fallback to basic insert without new columns
            $insertSql = "INSERT INTO content_credits (client_id, credit_type, credits, date) VALUES ($clientId, '$creative', $credits, '$startDate')";
        }
        if (!$conn->query($insertSql)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to insert content credit: " . $conn->error]);
            exit;
        }

        // Update client's used_credits
        $updateSql = "UPDATE clients SET used_credits = COALESCE(used_credits,0) + $credits WHERE id = $clientId";
        if (!$conn->query($updateSql)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to update client's used credits: " . $conn->error]);
            exit;
        }

        echo json_encode(["success" => true, "message" => "Content credit added and client credits updated."]);
    }
    // --- DELETE CONTENT CREDIT (GET) ---
    else if ($action === 'delete') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Content id is required."]);
            exit;
        }

        // Find the record to get clientId and credits for rollback
        $selSql = "SELECT client_id, credits FROM content_credits WHERE id = $id LIMIT 1";
        $res = $conn->query($selSql);
        if (!$res || $res->num_rows === 0) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Content record not found."]);
            exit;
        }
        $row = $res->fetch_assoc();
        $clientId = (int)$row['client_id'];
        $credits = (int)$row['credits'];

        // Delete the content credit
        $delSql = "DELETE FROM content_credits WHERE id = $id";
        if (!$conn->query($delSql)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to delete content credit: " . $conn->error]);
            exit;
        }

        // Rollback used_credits on clients (ensure non-negative)
        $rollbackSql = "UPDATE clients SET used_credits = GREATEST(COALESCE(used_credits,0) - $credits, 0) WHERE id = $clientId";
        if (!$conn->query($rollbackSql)) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to update client's used credits: " . $conn->error]);
            exit;
        }

        echo json_encode(["success" => true, "message" => "Content credit deleted and client credits updated."]);
    }
    else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid content action."]);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
}

$conn->close();
?>