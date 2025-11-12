# How to Convert Documentation to PDF

This guide explains multiple methods to convert the Ayonion CMS markdown documentation to PDF format.

---

## 📄 Quick Methods

### Method 1: Using the Provided Scripts (Recommended)

#### Option A: PowerShell Script (Professional PDFs with Pandoc)

**Requirements:**
- Install Pandoc first: https://pandoc.org/installing.html
  - **Via Chocolatey:** `choco install pandoc`
  - **Via Winget:** `winget install --id JohnMacFarlane.Pandoc`
  - **Manual:** Download from pandoc.org

**Steps:**
1. Open PowerShell in the project folder
2. Run: `.\convert_docs_to_pdf.ps1`
3. PDFs will be created in `documentation_pdf` folder

**Features:**
- ✅ Automatic conversion of all 4 documentation files
- ✅ Professional formatting with table of contents
- ✅ Proper page margins and styling
- ✅ Hyperlinks preserved
- ✅ Syntax highlighting for code blocks

#### Option B: Browser Method (No Installation Required)

**Steps:**
1. Double-click `open_docs_for_pdf.bat`
2. Documentation files will open in your browser
3. For each tab:
   - Press `Ctrl+P` (Windows) or `Cmd+P` (Mac)
   - Select "Save as PDF" as printer/destination
   - Click "Save"
   - Choose filename and location

**Recommended filenames:**
- `Ayonion_CMS_System_Documentation.pdf`
- `Ayonion_CMS_User_Guide.pdf`
- `Ayonion_CMS_Security_Guide.pdf`
- `Ayonion_CMS_Documentation_Index.pdf`

---

### Method 2: Using VS Code Extension

**Steps:**
1. Install "Markdown PDF" extension in VS Code:
   - Open Extensions (`Ctrl+Shift+X`)
   - Search for "Markdown PDF" by yzane
   - Click Install

2. Convert each file:
   - Open any `.md` file
   - Right-click in editor
   - Select "Markdown PDF: Export (pdf)"
   - PDF saved in same folder

**Or use Command Palette:**
- Press `Ctrl+Shift+P`
- Type "Markdown PDF"
- Select "Markdown PDF: Export (pdf)"

**Batch convert all files:**
- Press `Ctrl+Shift+P`
- Type "Markdown PDF: Export (all types)"

---

### Method 3: Using GitHub (Online)

If your repository is on GitHub:

1. Navigate to any `.md` file on GitHub
2. GitHub automatically renders markdown
3. In browser, press `Ctrl+P`
4. Select "Save as PDF"

**Advantage:** No installation needed, always available

---

### Method 4: Using Online Converters

**Recommended Sites:**
- **Markdown to PDF:** https://www.markdowntopdf.com/
- **MD2PDF:** https://md2pdf.netlify.app/
- **CloudConvert:** https://cloudconvert.com/md-to-pdf
- **Dillinger:** https://dillinger.io/ (has export to PDF)

**Steps:**
1. Visit one of the sites above
2. Upload or paste markdown content
3. Click "Convert" or "Export to PDF"
4. Download the PDF

**Pros:** No installation, works on any device  
**Cons:** May have file size limits, need internet connection

---

### Method 5: Manual Installation of Pandoc + wkhtmltopdf

For the best PDF output quality:

#### Install Pandoc
```powershell
# Using Chocolatey
choco install pandoc

# Using Winget
winget install --id JohnMacFarlane.Pandoc

# Or download from: https://pandoc.org/installing.html
```

#### Install wkhtmltopdf (for better PDF rendering)
```powershell
# Using Chocolatey
choco install wkhtmltopdf

# Or download from: https://wkhtmltopdf.org/downloads.html
```

#### Convert Manually
```powershell
# Single file conversion
pandoc SYSTEM_DOCUMENTATION.md -o SYSTEM_DOCUMENTATION.pdf --pdf-engine=wkhtmltopdf --toc

# With advanced options
pandoc SYSTEM_DOCUMENTATION.md `
    -o SYSTEM_DOCUMENTATION.pdf `
    --pdf-engine=wkhtmltopdf `
    --metadata title="Ayonion CMS - System Documentation" `
    --metadata author="Ayonion Studios" `
    --toc `
    --toc-depth=3 `
    -V geometry:margin=1in `
    -V fontsize=11pt `
    -V linkcolor=blue
```

---

## 📋 Files to Convert

| Markdown File | Recommended PDF Name | Description |
|--------------|---------------------|-------------|
| `SYSTEM_DOCUMENTATION.md` | `Ayonion_CMS_System_Documentation.pdf` | Technical documentation |
| `USER_GUIDE.md` | `Ayonion_CMS_User_Guide.pdf` | User manual |
| `SECURITY_GUIDE.md` | `Ayonion_CMS_Security_Guide.pdf` | Security guide |
| `DOCUMENTATION_INDEX.md` | `Ayonion_CMS_Documentation_Index.pdf` | Documentation index |

---

## 🎨 PDF Styling Tips

### For Browser Method

**Before printing:**
1. Remove headers/footers in print settings
2. Enable background graphics
3. Adjust margins as needed
4. Preview before saving

### For Pandoc Method

**Custom CSS styling:**
Create a file `pdf-style.css`:
```css
body {
    font-family: 'Arial', sans-serif;
    font-size: 11pt;
    line-height: 1.6;
}

h1 {
    color: #667eea;
    page-break-before: always;
}

code {
    background-color: #f5f5f5;
    padding: 2px 4px;
    border-radius: 3px;
}
```

Then use:
```powershell
pandoc input.md -o output.pdf --css=pdf-style.css
```

---

## 🔧 Troubleshooting

### "Pandoc not found"
- Install Pandoc from https://pandoc.org/installing.html
- Restart PowerShell/Terminal after installation
- Verify with: `pandoc --version`

### "wkhtmltopdf not found"
- Install from https://wkhtmltopdf.org/downloads.html
- Add to system PATH
- Or use alternative: `--pdf-engine=pdflatex`

### "Permission denied" on PowerShell script
Run this first:
```powershell
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned
```

### PDFs look ugly/broken
- Try different methods
- Check if all images are accessible
- Use `--pdf-engine=wkhtmltopdf` for better rendering
- Or use VS Code extension for simplicity

### Links not working in PDF
- Use Pandoc method (preserves links)
- Or manually test each link before converting

---

## 📦 Batch Conversion Script

If you want to convert all files at once using Pandoc:

```powershell
# Create output directory
New-Item -ItemType Directory -Force -Path ".\documentation_pdf"

# Convert all documentation files
$files = @(
    "SYSTEM_DOCUMENTATION.md",
    "USER_GUIDE.md",
    "SECURITY_GUIDE.md",
    "DOCUMENTATION_INDEX.md"
)

foreach ($file in $files) {
    $output = $file.Replace(".md", ".pdf")
    pandoc $file -o ".\documentation_pdf\$output" --pdf-engine=wkhtmltopdf --toc
    Write-Host "Converted: $file -> $output"
}

Write-Host "All files converted! Check documentation_pdf folder."
```

---

## ✅ Recommended Approach

**For quick results:**
→ Use `open_docs_for_pdf.bat` + browser print

**For professional PDFs:**
→ Install Pandoc + run `convert_docs_to_pdf.ps1`

**For simplicity:**
→ Use VS Code "Markdown PDF" extension

**For no installation:**
→ Use online converters

---

## 📧 Need Help?

If you encounter issues:
1. Check the troubleshooting section above
2. Try an alternative method
3. Contact: support@ayonionstudios.com

---

**Happy converting!** 📄✨
