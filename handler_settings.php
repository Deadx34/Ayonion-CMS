<?php
// AYONION-CMS/handler_settings.php - Admin-only global settings

header('Content-Type: application/json');
include 'includes/config.php';
$conn = connect_db();

function ensureSettingsColumn(mysqli $conn, string $columnName, string $columnDefinition): void {
    $columnNameEscaped = $conn->real_escape_string($columnName);
    $checkSql = "SHOW COLUMNS FROM settings LIKE '$columnNameEscaped'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult && $checkResult->num_rows === 0) {
        $safeColumnName = str_replace('`', '``', $columnName);
        $conn->query("ALTER TABLE settings ADD COLUMN `{$safeColumnName}` {$columnDefinition}");
    }
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents("php://input"), true);

try {
    // Secure session
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((string)($_SERVER['SERVER_PORT'] ?? '') === '443')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        if (function_exists('session_set_cookie_params')) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        @ini_set('session.use_strict_mode', '1');
        session_start();
    }

    // Allow logo access for login page (public endpoint for get action only)
    $isGetAction = ($action === 'get');
    $requiresAuth = !$isGetAction;
    
    if ($requiresAuth && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['role'] ?? '') !== 'admin')) {
        throw new Exception("Authentication required.", 401);
    }

    // Ensure settings table exists for fresh/legacy databases
    $conn->query("CREATE TABLE IF NOT EXISTS settings (
        id TINYINT UNSIGNED NOT NULL,
        company_name VARCHAR(255) NOT NULL,
        logo_url TEXT NULL,
        logo_light TEXT NULL,
        logo_dark TEXT NULL,
        email VARCHAR(255) NULL,
        phone VARCHAR(50) NULL,
        address TEXT NULL,
        website VARCHAR(255) NULL,
        bank_name VARCHAR(255) NULL,
        bank_branch VARCHAR(255) NULL,
        bank_account_name VARCHAR(255) NULL,
        bank_account_number VARCHAR(100) NULL,
        doc_thank_you_text TEXT NULL,
        doc_payment_instructions TEXT NULL,
        doc_bank_intro TEXT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Auto-migrate settings table safely for legacy databases
    ensureSettingsColumn($conn, 'logo_light', 'TEXT NULL');
    ensureSettingsColumn($conn, 'logo_dark', 'TEXT NULL');
    ensureSettingsColumn($conn, 'bank_name', 'VARCHAR(255) NULL');
    ensureSettingsColumn($conn, 'bank_branch', 'VARCHAR(255) NULL');
    ensureSettingsColumn($conn, 'bank_account_name', 'VARCHAR(255) NULL');
    ensureSettingsColumn($conn, 'bank_account_number', 'VARCHAR(100) NULL');
    ensureSettingsColumn($conn, 'doc_thank_you_text', 'TEXT NULL');
    ensureSettingsColumn($conn, 'doc_payment_instructions', 'TEXT NULL');
    ensureSettingsColumn($conn, 'doc_bank_intro', 'TEXT NULL');

    // Ensure a row exists (id=1 acts as singleton)
    $conn->query("INSERT INTO settings (id, company_name, logo_url, logo_light, logo_dark, email, phone, address, website, bank_name, bank_branch, bank_account_name, bank_account_number, doc_thank_you_text, doc_payment_instructions, doc_bank_intro) 
                  SELECT 1, 'Ayonion Studios', '', '', '', '', '', '', '', 'NDB Bank', 'Kadawatha Branch', 'Ayonion Studios (pvt) Ltd', '101001037178', 'Thank you for reaching out Ayonion Studios. We will deliver you the best service possible.', '• All cheques should be crossed and made payable to Ayonion Studios (pvt) Ltd.\n• A 50% of advance payment is required. (Excluding package payments)\n• The quotation is valid for two weeks from the day issued.\n• This is a computer generated quotation, No signature required.', 'Please deposit the advance payment to the below account' WHERE NOT EXISTS (SELECT 1 FROM settings WHERE id=1)");

    if ($action === 'get') {
        $sql = "SELECT company_name, logo_url, logo_light, logo_dark, email, phone, address, website, bank_name, bank_branch, bank_account_name, bank_account_number, doc_thank_you_text, doc_payment_instructions, doc_bank_intro FROM settings WHERE id = 1";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            echo json_encode([ 'success' => true, 'settings' => $row ]);
        } else {
            echo json_encode([ 'success' => false, 'message' => 'Settings not found' ]);
        }
    }
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
        $companyName = $conn->real_escape_string(trim($input['companyName'] ?? ''));
        $logoUrl = $conn->real_escape_string(trim($input['logoUrl'] ?? ''));
        $logoLight = $conn->real_escape_string(trim($input['logoLight'] ?? ''));
        $logoDark = $conn->real_escape_string(trim($input['logoDark'] ?? ''));
        $email = $conn->real_escape_string(trim($input['email'] ?? ''));
        $phone = $conn->real_escape_string(trim($input['phone'] ?? ''));
        $website = $conn->real_escape_string(trim($input['website'] ?? ''));
        $address = $conn->real_escape_string(trim($input['address'] ?? ''));
        $bankName = $conn->real_escape_string(trim($input['bankName'] ?? ''));
        $bankBranch = $conn->real_escape_string(trim($input['bankBranch'] ?? ''));
        $bankAccountName = $conn->real_escape_string(trim($input['bankAccountName'] ?? ''));
        $bankAccountNumber = $conn->real_escape_string(trim($input['bankAccountNumber'] ?? ''));
        $docThankYouText = $conn->real_escape_string(trim($input['docThankYouText'] ?? ''));
        $docPaymentInstructions = $conn->real_escape_string(trim($input['docPaymentInstructions'] ?? ''));
        $docBankIntro = $conn->real_escape_string(trim($input['docBankIntro'] ?? 'Please deposit the advance payment to the below account'));

        if ($companyName === '') {
            http_response_code(400);
            echo json_encode([ 'success' => false, 'message' => 'Company name is required' ]);
            exit;
        }

        $sql = "UPDATE settings SET company_name='$companyName', logo_url='$logoUrl', logo_light='$logoLight', logo_dark='$logoDark', email='$email', phone='$phone', website='$website', address='$address', bank_name='$bankName', bank_branch='$bankBranch', bank_account_name='$bankAccountName', bank_account_number='$bankAccountNumber', doc_thank_you_text='$docThankYouText', doc_payment_instructions='$docPaymentInstructions', doc_bank_intro='$docBankIntro' WHERE id = 1";
        if ($conn->query($sql)) {
            echo json_encode([ 'success' => true, 'message' => 'Settings updated' ]);
        } else {
            http_response_code(500);
            echo json_encode([ 'success' => false, 'message' => 'Failed to update settings: ' . $conn->error ]);
        }
    }
    else {
        http_response_code(400);
        echo json_encode([ 'success' => false, 'message' => 'Invalid action' ]);
    }
} catch (Throwable $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([ 'success' => false, 'message' => $e->getMessage() ]);
}

$conn->close();
?>


