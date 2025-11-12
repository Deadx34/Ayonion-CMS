@echo off
echo ==========================================
echo Ayonion CMS - Open Documentation in Browser
echo ==========================================
echo.
echo This will open all documentation files in your default browser.
echo You can then use Ctrl+P and "Save as PDF" to create PDF versions.
echo.
pause

echo.
echo Opening documentation files...
echo.

REM Open each documentation file in browser
start "" "SYSTEM_DOCUMENTATION.md"
timeout /t 2 /nobreak >nul

start "" "USER_GUIDE.md"
timeout /t 2 /nobreak >nul

start "" "SECURITY_GUIDE.md"
timeout /t 2 /nobreak >nul

start "" "DOCUMENTATION_INDEX.md"

echo.
echo ==========================================
echo Files opened in browser!
echo ==========================================
echo.
echo Instructions:
echo 1. Wait for browser tabs to load
echo 2. In each tab, press Ctrl+P
echo 3. Select "Save as PDF" as destination
echo 4. Choose output folder (e.g., documentation_pdf)
echo 5. Click Save
echo.
echo Recommended PDF filenames:
echo - Ayonion_CMS_System_Documentation.pdf
echo - Ayonion_CMS_User_Guide.pdf
echo - Ayonion_CMS_Security_Guide.pdf
echo - Ayonion_CMS_Documentation_Index.pdf
echo.
pause
