<?php
// Migration: Add `item_details` column to `documents` if it does not exist.
// Run this from the project root: php migrate_add_item_details_column.php

include 'includes/config.php';

try {
    $conn = connect_db();

    // Check if column exists
    $checkSql = "SHOW COLUMNS FROM documents LIKE 'item_details'";
    $res = $conn->query($checkSql);
    if ($res && $res->num_rows > 0) {
        echo "item_details column already exists.\n";
        exit(0);
    }

    // Add column as TEXT to support large JSON payloads
    $alterSql = "ALTER TABLE documents ADD COLUMN item_details TEXT DEFAULT NULL";
    if ($conn->query($alterSql) === TRUE) {
        echo "Successfully added item_details column to documents.\n";
    } else {
        throw new Exception('Failed to add column: ' . $conn->error);
    }

    // Optional: copy existing JSON stored in item_type into item_details for rows that look like JSON
    $selectSql = "SELECT id, item_type FROM documents";
    $result = $conn->query($selectSql);
    if ($result && $result->num_rows > 0) {
        $updated = 0;
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $it = $row['item_type'];
            if (is_string($it) && (strpos(trim($it), '[') !== false || strpos(trim($it), '{') !== false)) {
                // likely JSON - try decode
                $decoded = json_decode($it, true);
                if (is_string($decoded)) $decoded = json_decode($decoded, true);
                if (!is_array($decoded)) $decoded = json_decode(stripslashes($it), true);
                if (is_array($decoded)) {
                    $upd = $conn->prepare("UPDATE documents SET item_details = ? WHERE id = ?");
                    $json = json_encode($decoded);
                    $upd->bind_param('ss', $json, $id);
                    if ($upd->execute()) $updated++;
                    $upd->close();
                }
            }
        }
        echo "Copied JSON into item_details for $updated rows.\n";
    }

    $conn->close();
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>