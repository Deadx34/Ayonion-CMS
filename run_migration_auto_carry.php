<?php
// AYONION-CMS/run_migration_auto_carry.php - Run Auto Carry Forward Migration

header('Content-Type: text/html; charset=utf-8');
include 'includes/config.php';
$conn = connect_db();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run Migration - Ayonion CMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 0; }
        .migration-card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 40px; max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="migration-card">
            <div class="text-center mb-4">
                <i class="fas fa-database fa-3x text-primary mb-3"></i>
                <h1 class="text-primary">Auto Carry Forward Migration</h1>
                <p class="text-muted">Setting up database for auto carry forward system</p>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>This will:</strong>
                <ul class="mb-0 mt-2">
                    <li>Add <code>last_carry_forward</code> column to clients table</li>
                    <li>Create index on <code>renewal_date</code> for faster queries</li>
                    <li>Set default package credits to 40 for existing clients</li>
                </ul>
            </div>

            <?php
            $errors = [];
            $success = [];
            
            try {
                // Check if column already exists
                $check = $conn->query("SHOW COLUMNS FROM clients LIKE 'last_carry_forward'");
                if ($check->num_rows > 0) {
                    $success[] = "✓ Column 'last_carry_forward' already exists";
                } else {
                    // Add last_carry_forward column
                    $sql1 = "ALTER TABLE clients ADD COLUMN last_carry_forward DATETIME DEFAULT NULL";
                    if ($conn->query($sql1)) {
                        $success[] = "✓ Added 'last_carry_forward' column";
                    } else {
                        $errors[] = "Failed to add column: " . $conn->error;
                    }
                }

                // Create index on renewal_date
                $check_index = $conn->query("SHOW INDEX FROM clients WHERE Key_name = 'idx_renewal_date'");
                if ($check_index->num_rows > 0) {
                    $success[] = "✓ Index 'idx_renewal_date' already exists";
                } else {
                    $sql2 = "CREATE INDEX idx_renewal_date ON clients(renewal_date)";
                    if ($conn->query($sql2)) {
                        $success[] = "✓ Created index on 'renewal_date'";
                    } else {
                        $errors[] = "Failed to create index: " . $conn->error;
                    }
                }

                // Update existing clients with default package credits
                $sql3 = "UPDATE clients SET package_credits = 40 WHERE package_credits = 0 OR package_credits IS NULL";
                $result = $conn->query($sql3);
                if ($result) {
                    $affected = $conn->affected_rows;
                    if ($affected > 0) {
                        $success[] = "✓ Updated $affected client(s) with default 40 package credits";
                    } else {
                        $success[] = "✓ All clients already have package credits set";
                    }
                } else {
                    $errors[] = "Failed to update package credits: " . $conn->error;
                }

            } catch (Exception $e) {
                $errors[] = "Exception: " . $e->getMessage();
            }

            // Display results
            if (!empty($success)) {
                echo '<div class="alert alert-success">';
                echo '<h5><i class="fas fa-check-circle me-2"></i>Migration Successful!</h5>';
                foreach ($success as $msg) {
                    echo '<div class="mb-1">' . htmlspecialchars($msg) . '</div>';
                }
                echo '</div>';
            }

            if (!empty($errors)) {
                echo '<div class="alert alert-danger">';
                echo '<h5><i class="fas fa-exclamation-circle me-2"></i>Errors Occurred:</h5>';
                foreach ($errors as $msg) {
                    echo '<div class="mb-1">' . htmlspecialchars($msg) . '</div>';
                }
                echo '</div>';
            }

            $conn->close();
            ?>

            <?php if (empty($errors)): ?>
            <div class="alert alert-success mt-4">
                <h5><i class="fas fa-rocket me-2"></i>Ready to Go!</h5>
                <p class="mb-0">The Auto Carry Forward System is now set up and ready to use.</p>
            </div>

            <div class="text-center mt-4">
                <a href="index.html" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Go to Dashboard
                </a>
            </div>
            <?php else: ?>
            <div class="text-center mt-4">
                <button class="btn btn-warning" onclick="location.reload()">
                    <i class="fas fa-redo me-2"></i>Try Again
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
