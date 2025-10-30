# Multi-Select Content Report Feature

## Overview
This feature allows users to select multiple content items and generate a single combined report including all selected content details. This is useful when you need a report for specific content items rather than all content for a client.

## Features Implemented

### 1. **Selection Checkboxes**
- Added a checkbox column to the content credits table
- Each content row has a checkbox to select individual items
- "Select All" checkbox in the table header to select/deselect all items at once

### 2. **Dynamic Button Display**
- **"Generate Report (All)"** - Always visible, generates report for all content items
- **"Generate Report (X Selected)"** - Appears only when items are selected
  - Shows the count of selected items dynamically
  - Hidden when no items are selected

### 3. **Smart Selection Management**
- Real-time counter updates as you select/deselect items
- "Select All" checkbox shows indeterminate state when some (but not all) items are selected
- Selection state maintained across collapsed/expanded months
- Click event properly isolated to prevent row clicks from toggling checkboxes

### 4. **Selected Report Generation**
- Generates a PDF report with only the selected content items
- Includes visual indicator that it's a "Selected Items Report"
- Shows metadata:
  - Number of items selected
  - Total credits for selected items
  - Note indicating X of Y total items included
- Same professional layout as full report

## How to Use

### For Users:
1. Navigate to **Content Credits Management** section
2. Select a client from the dropdown
3. Use checkboxes to select desired content items:
   - Click individual checkboxes for specific items
   - Use "Select All" checkbox to select/deselect all items
4. Click **"Generate Report (X Selected)"** button
5. The report will open in a new window ready to print/save as PDF

### Technical Details:

#### Frontend Changes (index.html & index.php):
- Added checkbox column to table header
- Updated `loadContentCredits()` to include checkboxes in each row
- Implemented `toggleSelectAllContents()` function
- Implemented `updateSelectedCount()` function for real-time updates
- Implemented `generateSelectedContentReport()` function for report generation
- Updated table colspan from 7 to 8 to accommodate checkbox column

#### Backend Changes (PHP):
**generate_content_report.php:**
- Added support for `isSelectedReport`, `selectedCount`, and `totalSelectedCredits` parameters
- Passes these to the PDF generator function

**content_report_pdf.php:**
- Updated `generateContentReportPDF()` function signature to accept selection parameters
- Added `$selectedReportInfo` section for selected report indicator
- Displays yellow info box with selection details when applicable

## Report Differences

### Full Report (All Items):
- Shows all content items for the client
- Standard credit summary
- No special indicators

### Selected Report:
- Shows only checked items
- Highlighted info box: "📋 Selected Items Report: This report includes X selected content items with a total of Y credits"
- Note at bottom: "This report includes only the selected content items (X of Y total items)"
- Same professional layout and formatting

## Benefits

1. **Flexibility** - Generate reports for specific content without including everything
2. **Efficiency** - Quickly create focused reports for presentations or client reviews
3. **Clarity** - Clear indication when viewing a selected items report vs full report
4. **User-Friendly** - Intuitive checkbox selection with visual feedback
5. **Professional** - Maintains the same high-quality PDF output

## Browser Compatibility
- Works in all modern browsers (Chrome, Firefox, Edge, Safari)
- Uses standard HTML5 checkboxes and JavaScript
- Print/PDF functionality works across all platforms

## Notes
- Selection state resets when you change clients
- Checkboxes work independently from row actions (View, Edit, Delete)
- Both report buttons (All and Selected) can coexist - one doesn't interfere with the other
- The "Select All" checkbox intelligently handles collapsed months (selects all visible and hidden items)

---

**Version:** 1.0  
**Date:** October 30, 2025  
**Status:** ✅ Production Ready
