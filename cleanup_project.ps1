# Ayonion CMS Cleanup Script
# Removes unnecessary documentation, test files, and migration scripts

Write-Host "Starting cleanup of Ayonion CMS project..." -ForegroundColor Cyan

$filesRemoved = 0
$filesNotFound = 0

# Documentation files
$docFiles = @(
    "AUTO_CARRY_FORWARD_GUIDE.md",
    "AUTO_CARRY_FORWARD_IMPLEMENTATION.md",
    "CAMPAIGN_EDIT_FEATURE.md",
    "INVOICE_FEATURE_GUIDE.md",
    "JAVASCRIPT_ERRORS_FIXED.md",
    "LOGO_STORAGE_GUIDE.md",
    "MIGRATION_INSTRUCTIONS.md",
    "MULTI_SELECT_REPORT_FEATURE.md",
    "SETUP_COMPLETE.md",
    "UPLOAD_FIXES_SUMMARY.md",
    "USER_PROFILE_FIX.md",
    "USER_PROFILE_GUIDE.md"
)

# Test files
$testFiles = @(
    "check_database.php",
    "debug_profile.php",
    "test_invoice_setup.php",
    "test_logo_upload.html",
    "test_pdf.php",
    "test_profile_endpoint.php",
    "test_session.php",
    "test_single_document_multiple_types.php",
    "test_upload.php",
    "test_upload_no_db.php",
    "test_upload_simple.php",
    "verify_uploads.php"
)

# Migration scripts
$migrationFiles = @(
    "migrate_database.php",
    "run_migration.php",
    "run_migration_auto_carry.php",
    "run_migration_campaign_images.php",
    "run_campaign_images_migration.php",
    "run_migration_multiple_items.php",
    "run_migration_now.php",
    "run_migration_user_profile.php",
    "run_user_profile_migration.php",
    "setup_auto_carry_forward.php"
)

# SQL migration files
$sqlFiles = @(
    "migrate_auto_carry_forward.sql",
    "migrate_campaign_images.sql",
    "migrate_content_images.sql",
    "migrate_settings_logo.sql",
    "migrate_user_profile.sql"
)

# Unused handlers and generators
$unusedFiles = @(
    "upload_logo_handler_simple.php",
    "simple_pdf.php",
    "tcpdf_generator.php",
    "run_auto_carry_forward.sh",
    "run_auto_carry_forward.bat",
    "handler_users.htaccess"
)

# Combine all files
$allFiles = $docFiles + $testFiles + $migrationFiles + $sqlFiles + $unusedFiles

Write-Host "`nRemoving files..." -ForegroundColor Yellow

foreach ($file in $allFiles) {
    $filePath = Join-Path "c:\xampp\htdocs\ayonion-cms" $file
    if (Test-Path $filePath) {
        Remove-Item $filePath -Force
        Write-Host "  [REMOVED] $file" -ForegroundColor Green
        $filesRemoved++
    } else {
        Write-Host "  [NOT FOUND] $file" -ForegroundColor Gray
        $filesNotFound++
    }
}

# Remove empty workflows directory
$workflowsPath = "c:\xampp\htdocs\ayonion-cms\workflows"
if (Test-Path $workflowsPath) {
    Remove-Item $workflowsPath -Force -Recurse
    Write-Host "  [REMOVED] workflows directory" -ForegroundColor Green
    $filesRemoved++
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Cleanup Complete!" -ForegroundColor Green
Write-Host "Files removed: $filesRemoved" -ForegroundColor Green
Write-Host "Files not found: $filesNotFound" -ForegroundColor Gray
Write-Host "========================================" -ForegroundColor Cyan

Write-Host "`nEssential files kept:" -ForegroundColor Cyan
Write-Host "  - index.html & index.php (main app)" -ForegroundColor White
Write-Host "  - handler_*.php (backend logic)" -ForegroundColor White
Write-Host "  - session_check.php, logout.php (auth)" -ForegroundColor White
Write-Host "  - upload handlers (content & logo)" -ForegroundColor White
Write-Host "  - PDF generators (reports)" -ForegroundColor White
Write-Host "  - setup_database.sql, create_invoice_tables.sql (backup)" -ForegroundColor White
Write-Host "  - .htaccess (security)" -ForegroundColor White
Write-Host "  - includes/ & uploads/ folders" -ForegroundColor White

Write-Host "`nPress any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
