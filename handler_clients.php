<?php
// AYONION-CMS/handler_clients.php - Handles CRUD operations for clients

// Error handling for production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

try {
    include 'includes/config.php';
    $conn = connect_db();

    $action = $_GET['action'] ?? '';
    $input = json_decode(file_get_contents("php://input"), true);

    // --- 1. HANDLE ADD CLIENT (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    
    $id = time() . mt_rand(100, 999); 
    
    $partnerId = $conn->real_escape_string($input['partnerId'] ?? '');
    $companyName = $conn->real_escape_string($input['companyName'] ?? '');
    $renewalDate = $conn->real_escape_string($input['renewalDate'] ?? null);
    $subscriptionMonths = (int)($input['subscriptionMonths'] ?? 12);
    $packageCredits = (int)($input['packageCredits'] ?? 0);
    $previousRemainingCredits = (int)($input['previousRemainingCredits'] ?? 0);
    $managingPlatforms = $conn->real_escape_string($input['managingPlatforms'] ?? '');
    $industry = $conn->real_escape_string($input['industry'] ?? '');
    $logoUrl = $conn->real_escape_string($input['logoUrl'] ?? '');

    // If no logo URL provided, leave it empty (will show icon in frontend)
    if (empty($logoUrl)) {
        $logoUrl = '';
    }
    
    // Calculate subscription dates
    $subscriptionStartDate = $renewalDate;
    $subscriptionEndDate = null;
    if ($renewalDate) {
        $subscriptionEndDate = date('Y-m-d', strtotime($renewalDate . " +{$subscriptionMonths} months"));
    }
    
    // FIX: Using NULL for renewalDate if not provided.
    $sql = "INSERT INTO clients (
        id, partner_id, company_name, renewal_date, subscription_months, subscription_start_date, subscription_end_date,
        package_credits, managing_platforms, industry, logo_url,
        extra_credits, carried_forward_credits, used_credits, total_ad_budget, total_spent
    ) VALUES (
        '$id', '$partnerId', '$companyName', " . ($renewalDate ? "'$renewalDate'" : "NULL") . ", 
        $subscriptionMonths, " . ($subscriptionStartDate ? "'$subscriptionStartDate'" : "NULL") . ", 
        " . ($subscriptionEndDate ? "'$subscriptionEndDate'" : "NULL") . ", $packageCredits, 
        '$managingPlatforms', '$industry', '$logoUrl', 
        0, $previousRemainingCredits, 0, 0.00, 0.00
    )";

    
    if (query_db($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Client added successfully."]);
    } else {
        http_response_code(500);
        $error_msg = strpos($conn->error, 'Duplicate entry') !== false ? "Partner ID already exists." : "Failed to save client: Database Error.";
        echo json_encode(["success" => false, "message" => $error_msg . " Check XAMPP/Apache Error Log."]);
    }
} 
// --- 2. HANDLE DELETE CLIENT (GET) ---
else if ($action === 'delete') {
    $clientId = (int)($_GET['id'] ?? 0);
    $sql = "DELETE FROM clients WHERE id = $clientId";
    
    if (query_db($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Client and all related data deleted."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to delete client: " . $conn->error]);
    }
}
// --- 3. HANDLE UPDATE CREDITS (POST) ---
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_credits') {
    $clientId = (int)($input['clientId'] ?? 0);
    $packageCredits = (int)($input['packageCredits'] ?? 0);
    $extraCredits = (int)($input['extraCredits'] ?? 0);
    $resetUsedCredits = (bool)($input['resetUsedCredits'] ?? false);
    
    if ($clientId <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid client ID."]);
        exit;
    }
    
    // Build update query
    $usedCreditsUpdate = $resetUsedCredits ? ", used_credits = 0" : "";
    
    $sql = "UPDATE clients SET 
        package_credits = $packageCredits,
        extra_credits = $extraCredits
        $usedCreditsUpdate
        WHERE id = $clientId";
    
    if (query_db($conn, $sql)) {
        echo json_encode([
            "success" => true, 
            "message" => "Credits updated successfully." . ($resetUsedCredits ? " Used credits reset to 0." : "")
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update credits: " . $conn->error]);
    }
}
// --- 4. HANDLE UPDATE CLIENT (POST) ---
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_client') {
    $clientId = (int)($input['clientId'] ?? 0);
    
    if ($clientId <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid client ID."]);
        exit;
    }
    
    $partnerId = $conn->real_escape_string($input['partnerId'] ?? '');
    $companyName = $conn->real_escape_string($input['companyName'] ?? '');
    $renewalDate = $conn->real_escape_string($input['renewalDate'] ?? '');
    $subscriptionMonths = (int)($input['subscriptionMonths'] ?? 12);
    $managingPlatforms = $conn->real_escape_string($input['managingPlatforms'] ?? '');
    $industry = $conn->real_escape_string($input['industry'] ?? '');
    $totalAdBudget = (float)($input['totalAdBudget'] ?? 0);
    $logoUrl = $conn->real_escape_string($input['logoUrl'] ?? '');
    $previousRemainingCredits = (int)($input['previousRemainingCredits'] ?? 0);
    $isPaused = (int)($input['isPaused'] ?? 0);
    $pauseStartDate = $conn->real_escape_string($input['pauseStartDate'] ?? null);
    $pauseEndDate = $conn->real_escape_string($input['pauseEndDate'] ?? null);

    // Recalculate subscription end date if subscription months changed
    $subscriptionEndDate = date('Y-m-d', strtotime($renewalDate . " +{$subscriptionMonths} months"));

    $sql = "UPDATE clients SET 
        partner_id = '$partnerId',
        company_name = '$companyName',
        renewal_date = '$renewalDate',
        subscription_months = $subscriptionMonths,
        subscription_end_date = '$subscriptionEndDate',
        managing_platforms = '$managingPlatforms',
        industry = '$industry',
        total_ad_budget = $totalAdBudget,
        logo_url = '$logoUrl',
        carried_forward_credits = $previousRemainingCredits,
        is_paused = $isPaused,
        pause_start_date = " . ($pauseStartDate ? "'$pauseStartDate'" : "NULL") . ",
        pause_end_date = " . ($pauseEndDate ? "'$pauseEndDate'" : "NULL") . "
        WHERE id = $clientId";

    if (query_db($conn, $sql)) {
        echo json_encode([
            "success" => true, 
            "message" => "Client information updated successfully."
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to update client: " . $conn->error]);
    }
}
// --- 5. HANDLE AUTO CARRY FORWARD (POST) ---
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'auto_carry_forward') {
    $today = date('Y-m-d');
    $results = [];
    $processed = 0;
    
    // Get clients whose renewal date has passed and subscription is still active
    // Only process if last carry forward was not done for current renewal period
    $sql = "SELECT * FROM clients 
            WHERE renewal_date <= '$today' 
            AND (subscription_end_date IS NULL OR subscription_end_date >= '$today')
            AND (last_carry_forward_date IS NULL OR last_carry_forward_date < renewal_date)";
    
    $result = query_db($conn, $sql);
    
    if ($result && $result->num_rows > 0) {
        while ($client = $result->fetch_assoc()) {
            $clientId = $client['id'];
            $currentRenewal = $client['renewal_date'];
            
            // Calculate unused credits
            $totalCredits = $client['package_credits'] + $client['extra_credits'] + $client['carried_forward_credits'];
            $unusedCredits = $totalCredits - $client['used_credits'];
            
            if ($unusedCredits > 0) {
                // Move to next month
                $newRenewalDate = date('Y-m-d', strtotime($currentRenewal . ' +1 month'));
                
                // Update client: carry forward unused credits, reset used credits, update renewal date
                $updateSql = "UPDATE clients SET 
                             carried_forward_credits = $unusedCredits,
                             used_credits = 0,
                             renewal_date = '$newRenewalDate',
                             last_carry_forward_date = '$today'
                             WHERE id = $clientId";
                
                if (query_db($conn, $updateSql)) {
                    $processed++;
                    $results[] = [
                        'client_id' => $clientId,
                        'client_name' => $client['company_name'],
                        'carried_forward' => $unusedCredits,
                        'new_renewal_date' => $newRenewalDate,
                        'subscription_end_date' => $client['subscription_end_date']
                    ];
                }
            } else {
                // Even if no credits to carry forward, update renewal date
                $newRenewalDate = date('Y-m-d', strtotime($currentRenewal . ' +1 month'));
                $updateSql = "UPDATE clients SET 
                             renewal_date = '$newRenewalDate',
                             last_carry_forward_date = '$today'
                             WHERE id = $clientId";
                query_db($conn, $updateSql);
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'processed_count' => $processed,
        'results' => $results
    ]);
}
// --- 6. ERROR HANDLING ---
else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid API endpoint request."]);
}

$conn->close();

} catch (Exception $e) {
    error_log("Handler error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Server error: " . $e->getMessage()
    ]);
} catch (Error $e) {
    error_log("PHP Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Fatal error occurred"
    ]);
}
?>