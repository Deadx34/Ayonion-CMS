<?php
// AYONION-CMS/setup_auto_carry_forward.php
// Quick setup script for Auto Carry Forward System

header('Content-Type: text/html; charset=utf-8');

// Check if already setup
$setup_marker = __DIR__ . '/logs/auto_carry_forward_setup_complete.txt';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Auto Carry Forward System - Ayonion CMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 0; }
        .setup-card { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 40px; max-width: 800px; margin: 0 auto; }
        .step { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #6366f1; }
        .code-block { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 8px; overflow-x: auto; }
        .btn-setup { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 12px 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-card">
            <div class="text-center mb-4">
                <i class="fas fa-sync-alt fa-3x text-primary mb-3"></i>
                <h1 class="text-primary">Auto Carry Forward System</h1>
                <p class="text-muted">Quick Setup Guide</p>
            </div>

            <?php if (file_exists($setup_marker)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Setup already completed!</strong> The system is ready to use.
                </div>
            <?php endif; ?>

            <div class="step">
                <h4><i class="fas fa-database me-2"></i>Step 1: Database Migration</h4>
                <p>Run the SQL migration to add required database columns:</p>
                <div class="code-block mb-3">
                    <code>ALTER TABLE clients ADD COLUMN IF NOT EXISTS last_carry_forward DATETIME DEFAULT NULL;</code>
                </div>
                <button class="btn btn-primary" onclick="runMigration()">
                    <i class="fas fa-play me-2"></i>Run Migration
                </button>
                <div id="migrationResult"></div>
            </div>

            <div class="step">
                <h4><i class="fas fa-folder me-2"></i>Step 2: Create Logs Directory</h4>
                <p>Ensure the logs directory exists and is writable:</p>
                <button class="btn btn-primary" onclick="createLogsDir()">
                    <i class="fas fa-folder-plus me-2"></i>Create Logs Directory
                </button>
                <div id="logsDirResult"></div>
            </div>

            <div class="step">
                <h4><i class="fas fa-vial me-2"></i>Step 3: Test the System</h4>
                <p>Run a test to ensure everything is working:</p>
                <button class="btn btn-success" onclick="testSystem()">
                    <i class="fas fa-flask me-2"></i>Run Test
                </button>
                <div id="testResult"></div>
            </div>

            <div class="alert alert-info mt-4">
                <h5><i class="fas fa-info-circle me-2"></i>Next Steps:</h5>
                <ul>
                    <li>Set up a cron job for automated processing (see AUTO_CARRY_FORWARD_GUIDE.md)</li>
                    <li>Access the system from Settings → Auto Carry Forward System</li>
                    <li>Monitor the logs in logs/auto_carry_forward.log</li>
                </ul>
            </div>

            <div class="text-center mt-4">
                <a href="index.html" class="btn btn-primary btn-setup">
                    <i class="fas fa-home me-2"></i>Go to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script>
        async function runMigration() {
            const resultDiv = document.getElementById('migrationResult');
            resultDiv.innerHTML = '<div class="alert alert-info mt-3">Running migration...</div>';
            
            try {
                const response = await fetch('migrate_database.php?action=auto_carry_forward');
                const result = await response.text();
                resultDiv.innerHTML = `<div class="alert alert-success mt-3"><i class="fas fa-check me-2"></i>Migration completed!</div>`;
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger mt-3"><i class="fas fa-times me-2"></i>Error: ${error.message}</div>`;
            }
        }

        async function createLogsDir() {
            const resultDiv = document.getElementById('logsDirResult');
            resultDiv.innerHTML = '<div class="alert alert-info mt-3">Creating directory...</div>';
            
            try {
                const response = await fetch('handler_auto_carry_forward.php?setup=logs');
                resultDiv.innerHTML = `<div class="alert alert-success mt-3"><i class="fas fa-check me-2"></i>Logs directory ready!</div>`;
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-warning mt-3">Please create logs/ directory manually</div>`;
            }
        }

        async function testSystem() {
            const resultDiv = document.getElementById('testResult');
            resultDiv.innerHTML = '<div class="alert alert-info mt-3">Testing system...</div>';
            
            try {
                const response = await fetch('handler_auto_carry_forward.php?manual=true');
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check me-2"></i>
                            <strong>Test successful!</strong><br>
                            ${result.message}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-warning mt-3">${result.message}</div>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger mt-3"><i class="fas fa-times me-2"></i>Error: ${error.message}</div>`;
            }
        }
    </script>
</body>
</html>
