    <?php
/**
 * Copy this file to includes/config.php (which is gitignored)
 * and replace placeholder values with real database credentials.
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * Map each host to its own DB credentials.
 * Add one block per client demo URL.
 */
function get_db_config_by_host(): array {
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    $host = preg_replace('/:\\d+$/', '', $host); // remove :port if present

    $configs = [
        'logiclab.byethost15.com' => [
            'db_host' => 'sql105.byethost15.com',
            'db_user' => 'b15_41427299',
            'db_pass' => 'Php@1234',
            'db_name' => 'b15_41427299_C1',
            'db_port' => 3306,
        ],

        // Example for next client demo domain/subdomain
        'client1.byethost15.com' => [
            'db_host' => 'sql105.byethost15.com',
            'db_user' => 'b15_41427299',
            'db_pass' => 'Php@1234',
            'db_name' => 'b15_41427299_C1',
            'db_port' => 3306,
        ],
    ];

    // Fallback: first config (useful in CLI or unknown host)
    if (!isset($configs[$host])) {
        return reset($configs);
    }

    return $configs[$host];
}

/**
 * Shared DB connector used by all handlers.
 */
function connect_db(): mysqli {
    $cfg = get_db_config_by_host();

    $conn = new mysqli(
        $cfg['db_host'],
        $cfg['db_user'],
        $cfg['db_pass'],
        $cfg['db_name'],
        $cfg['db_port'] ?? 3306
    );

    $conn->set_charset('utf8mb4');
    return $conn;
}
/**
 * Execute a query and return success status.
 */
function query_db(mysqli $conn, string $sql): bool {
    if (!$conn->query($sql)) {
        return false;
    }
    return true;
}