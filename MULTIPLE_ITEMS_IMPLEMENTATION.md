# Multiple Document Items Implementation

## Overview
The financial management modal has been enhanced to support multiple document item types in a single document. This allows users to create invoices, quotations, and receipts with multiple line items.

## Changes Made

### 1. Frontend Modal Updates (index.html)
- **Modified Document Modal Form**: Replaced single item form with dynamic multiple items form
- **Added JavaScript Functions**:
  - `addDocumentItem()`: Adds new item rows dynamically
  - `removeDocumentItem()`: Removes item rows (minimum 1 item required)
  - `calculateItemTotal()`: Auto-calculates totals for each item
  - `updateSummary()`: Updates document summary with total items and grand total
  - `initializeDocumentItems()`: Initializes event listeners for all items

### 2. Backend Handler Updates (handler_finance.php)
- **Modified Form Processing**: Now accepts `items` array instead of single item fields
- **Database Insertion**: Loops through items and inserts each as separate document record
- **Receipt Processing**: Aggregates totals for Ad Budget and Extra Content Credits across all items
- **Delete Functionality**: Updated to handle multiple items when deleting documents

### 3. Database Structure Updates
- **Added Migration Script**: `migrate_documents_multiple_items.sql`
- **New Field**: `item_order` INT column to maintain item sequence
- **Index**: Added composite index on `(id, item_order)` for better performance
- **Web Migration**: `migrate_multiple_items_web.php` for easy deployment

### 4. PDF Generation Updates
- **Handler Download**: Modified to fetch all items for a document
- **Simple PDF**: Updated `createPDFDocument()` to accept and display multiple items
- **Table Generation**: Dynamic table rows for all items with proper grand total calculation
- **Backward Compatibility**: Maintains support for single-item documents

## New Features

### Multiple Item Management
- **Add Items**: Click "Add Another Item" button to add more items
- **Remove Items**: Remove button appears when more than 1 item exists
- **Auto-calculation**: Quantity × Unit Price = Total (auto-updated)
- **Document Summary**: Shows total items count and grand total

### Enhanced User Experience
- **Real-time Totals**: All calculations update automatically
- **Form Validation**: Ensures all items have valid data before submission
- **Visual Feedback**: Clear summary card showing document totals
- **Responsive Design**: Works on all screen sizes

## Database Migration Required

**Important**: Run the migration script before using the new multiple items feature:

1. **Option 1 - Web Interface**: Visit `migrate_multiple_items_web.php` in your browser
2. **Option 2 - Command Line**: Run the SQL migration script directly

## Usage Instructions

### Creating Documents with Multiple Items
1. Open Financial Management section
2. Click "Create Quotation/Invoice/Receipt"
3. Fill in client and date information
4. Add multiple items using the "Add Another Item" button
5. Each item requires:
   - Item Type (Monthly Payment, Extra Content Credits, Ad Budget, Other Service)
   - Description
   - Quantity
   - Unit Price
6. Review the document summary
7. Click "Generate Document"

### Managing Items
- **Add Item**: Click the blue "Add Another Item" button
- **Remove Item**: Click the red "Remove Item" button (only available when 2+ items)
- **Edit Item**: Modify any field and totals auto-calculate
- **Reorder**: Items maintain their order in the generated document

## Technical Details

### Form Data Structure
```javascript
{
  clientId: 123,
  docType: "invoice",
  items: [
    {
      itemType: "Monthly Payment",
      description: "Monthly subscription",
      quantity: 1,
      unitPrice: 1000.00,
      total: 1000.00
    },
    {
      itemType: "Extra Content Credits",
      description: "Additional content creation",
      quantity: 5,
      unitPrice: 50.00,
      total: 250.00
    }
  ],
  date: "2024-01-15"
}
```

### Database Schema
```sql
-- New field added to documents table
ALTER TABLE documents ADD COLUMN item_order INT DEFAULT 0;

-- Index for performance
CREATE INDEX idx_documents_id_order ON documents(id, item_order);
```

## Benefits

1. **Flexibility**: Create complex documents with multiple services/products
2. **Efficiency**: Single document for multiple transactions
3. **Accuracy**: Automatic calculations reduce human error
4. **Professional**: Better document presentation with itemized details
5. **Backward Compatible**: Existing single-item documents continue to work

## Files Modified
- `index.html` - Frontend modal and JavaScript
- `handler_finance.php` - Backend processing
- `handler_download.php` - Document retrieval
- `simple_pdf.php` - PDF generation
- `migrate_documents_multiple_items.sql` - Database migration
- `migrate_multiple_items_web.php` - Web migration script

## Testing Recommendations
1. Test with single item (backward compatibility)
2. Test with multiple items of same type
3. Test with mixed item types
4. Test receipt processing with Ad Budget and Credits
5. Test document deletion with multiple items
6. Test PDF generation with multiple items
