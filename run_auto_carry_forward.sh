#!/bin/bash
# AYONION-CMS Auto Carry Forward - Linux/Unix Runner Script
# This script runs the auto carry forward process on Linux/Unix systems

echo "========================================"
echo "Ayonion CMS - Auto Carry Forward"
echo "========================================"
echo ""

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Set PHP path (will use system default)
PHP_PATH=$(which php)

# Check if PHP exists
if [ ! -x "$PHP_PATH" ]; then
    echo "ERROR: PHP not found in system PATH"
    echo "Please install PHP or update the script with correct path"
    exit 1
fi

echo "Running auto carry forward process..."
echo ""

# Run the PHP script
$PHP_PATH handler_auto_carry_forward.php

echo ""
echo "========================================"
echo "Process completed!"
echo "Check logs/auto_carry_forward.log for details"
echo "========================================"
echo ""
