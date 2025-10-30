@echo off
REM AYONION-CMS Auto Carry Forward - Windows Runner Script
REM This script runs the auto carry forward process on Windows systems

echo ========================================
echo Ayonion CMS - Auto Carry Forward
echo ========================================
echo.

REM Change to the script directory
cd /d "%~dp0"

REM Set PHP path (adjust if needed)
set PHP_PATH=C:\xampp\php\php.exe

REM Check if PHP exists
if not exist "%PHP_PATH%" (
    echo ERROR: PHP not found at %PHP_PATH%
    echo Please update PHP_PATH in this script
    pause
    exit /b 1
)

echo Running auto carry forward process...
echo.

REM Run the PHP script
"%PHP_PATH%" handler_auto_carry_forward.php

echo.
echo ========================================
echo Process completed!
echo Check logs/auto_carry_forward.log for details
echo ========================================
echo.

pause
