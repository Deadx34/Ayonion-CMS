<?php
// AYONION-CMS/handler_month_end.php - Handles month-end credit processing

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $action !== 'process') {
        throw new Exception("Invalid request method or action.", 400);
    }

    $clientId = (int)($input['clientId'] ?? 0);
    $creditsToCarry = (int)($input['creditsToCarry'] ?? 0);
    $newRenewalDate = $conn->real_escape_string($input['newRenewalDate'] ?? '');

    if ($clientId <= 0) {
        throw new Exception("Valid client ID is required.", 400);
    }

    if ($creditsToCarry < 0) {
        throw new Exception("Credits to carry cannot be negative.", 400);
    }

    if (empty($newRenewalDate)) {
        throw new Exception("New renewal date is required.", 400);
    }

    // Start transaction for data integrity
    $conn->begin_transaction();

    // Get current client data
    $client_sql = "SELECT * FROM clients WHERE id = $clientId";
    $client_result = $conn->query($client_sql);
    if (!$client_result || $client_result->num_rows === 0) {
        throw new Exception("Client not found.", 404);
    }
    $client = $client_result->fetch_assoc();

    // Check if client is paused and current date is within pause period
    $isPaused = (int)($client['is_paused'] ?? 0);
    $pauseStart = $client['pause_start_date'] ?? null;
    $pauseEnd = $client['pause_end_date'] ?? null;
    $todayDate = date('Y-m-d');
    if ($isPaused && $pauseStart && $pauseEnd && $todayDate >= $pauseStart && $todayDate <= $pauseEnd) {
        throw new Exception("Client subscription is paused. No credits assigned for this period.", 200);
    }

    $totalCredits = $client['package_credits'] + $client['extra_credits'] + $client['carried_forward_credits'];
    $availableCredits = $totalCredits - $client['used_credits'];

    // Validate credits to carry
    if ($creditsToCarry > $availableCredits) {
        throw new Exception("Credits to carry ($creditsToCarry) cannot exceed available credits ($availableCredits).", 400);
    }

    // Calculate credits that will expire
    $creditsExpiring = $availableCredits - $creditsToCarry;

    // Update client record with month-end processing
    $update_sql = "UPDATE clients SET 
        carried_forward_credits = $creditsToCarry,
        used_credits = 0,
        renewal_date = '$newRenewalDate'
        WHERE id = $clientId";

    if (!$conn->query($update_sql)) {
        throw new Exception("Failed to update client credits: " . $conn->error);
    }

    // Log the month-end process (optional - for audit trail)
    $log_sql = "INSERT INTO month_end_logs (client_id, previous_available, credits_carried, credits_expired, new_renewal_date, processed_date) 
                VALUES ($clientId, $availableCredits, $creditsToCarry, $creditsExpiring, '$newRenewalDate', NOW())";
    
    // Create month_end_logs table if it doesn't exist
    $create_log_table = "CREATE TABLE IF NOT EXISTS month_end_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_id BIGINT,
        previous_available INT,
        credits_carried INT,
        credits_expired INT,
        new_renewal_date DATE,
        processed_date DATETIME,
        FOREIGN KEY (client_id) REFERENCES clients(id)
    )";
    
    $conn->query($create_log_table);
    $conn->query($log_sql);

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Month-end process completed successfully!",
        "details" => [
            "clientId" => $clientId,
            "creditsCarried" => $creditsToCarry,
            "creditsExpired" => $creditsExpiring,
            "newRenewalDate" => $newRenewalDate,
            "previousAvailable" => $availableCredits
        ]
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        "success" => false, 
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>
