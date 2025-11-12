# Convert Markdown Documentation to PDF
# Requires: pandoc installed (https://pandoc.org/installing.html)

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Ayonion CMS - Documentation to PDF Converter" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Check if pandoc is installed
try {
    $pandocVersion = pandoc --version
    Write-Host "✓ Pandoc found: $($pandocVersion[0])" -ForegroundColor Green
} catch {
    Write-Host "✗ Pandoc not found!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please install Pandoc first:" -ForegroundColor Yellow
    Write-Host "1. Download from: https://pandoc.org/installing.html" -ForegroundColor Yellow
    Write-Host "2. Or use chocolatey: choco install pandoc" -ForegroundColor Yellow
    Write-Host "3. Or use winget: winget install --id JohnMacFarlane.Pandoc" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "After installing, restart PowerShell and run this script again." -ForegroundColor Yellow
    exit
}

Write-Host ""

# Create output directory
$outputDir = ".\documentation_pdf"
if (-not (Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir | Out-Null
    Write-Host "✓ Created output directory: $outputDir" -ForegroundColor Green
}

Write-Host ""
Write-Host "Converting markdown files to PDF..." -ForegroundColor Cyan
Write-Host ""

# List of documentation files to convert
$docs = @(
    @{
        Input = "SYSTEM_DOCUMENTATION.md"
        Output = "Ayonion_CMS_System_Documentation.pdf"
        Title = "Ayonion CMS - System Documentation"
    },
    @{
        Input = "USER_GUIDE.md"
        Output = "Ayonion_CMS_User_Guide.pdf"
        Title = "Ayonion CMS - User Guide"
    },
    @{
        Input = "SECURITY_GUIDE.md"
        Output = "Ayonion_CMS_Security_Guide.pdf"
        Title = "Ayonion CMS - Security Guide"
    },
    @{
        Input = "DOCUMENTATION_INDEX.md"
        Output = "Ayonion_CMS_Documentation_Index.pdf"
        Title = "Ayonion CMS - Documentation Index"
    }
)

# Convert each document
foreach ($doc in $docs) {
    $inputFile = $doc.Input
    $outputFile = Join-Path $outputDir $doc.Output
    $title = $doc.Title
    
    if (Test-Path $inputFile) {
        Write-Host "Converting: $inputFile" -ForegroundColor Yellow
        
        try {
            # Pandoc command with professional styling
            pandoc $inputFile `
                -o $outputFile `
                --pdf-engine=wkhtmltopdf `
                --metadata title="$title" `
                --metadata author="Ayonion Studios" `
                --metadata date="November 12, 2025" `
                --toc `
                --toc-depth=3 `
                -V geometry:margin=1in `
                -V fontsize=11pt `
                -V linkcolor=blue `
                --highlight-style=tango `
                2>$null
            
            if ($LASTEXITCODE -eq 0) {
                $fileSize = (Get-Item $outputFile).Length / 1KB
                Write-Host "  ✓ Created: $outputFile ($([math]::Round($fileSize, 2)) KB)" -ForegroundColor Green
            } else {
                Write-Host "  ✗ Failed to convert $inputFile" -ForegroundColor Red
            }
        } catch {
            Write-Host "  ✗ Error: $_" -ForegroundColor Red
        }
    } else {
        Write-Host "  ✗ File not found: $inputFile" -ForegroundColor Red
    }
    Write-Host ""
}

Write-Host "==================================" -ForegroundColor Cyan
Write-Host "Conversion Complete!" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "PDF files saved in: $outputDir" -ForegroundColor Cyan
Write-Host ""

# Open the output directory
$openDir = Read-Host "Open output folder? (Y/N)"
if ($openDir -eq "Y" -or $openDir -eq "y") {
    Invoke-Item $outputDir
}
