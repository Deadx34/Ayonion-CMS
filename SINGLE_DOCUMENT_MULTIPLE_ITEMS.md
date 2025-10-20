# Single Document with Multiple Line Items

## Overview
The financial management system now creates **one single document** with **multiple line items** instead of separate documents for each item type. This provides a cleaner, more professional approach to document management.

## How It Works

### 1. **Single Document Creation**
- **One Document ID**: All line items share the same base document ID
- **Line Item IDs**: Each item gets a unique line item ID: `documentId_line_0`, `documentId_line_1`, etc.
- **Single PDF**: All items appear in one professional document

### 2. **Database Structure**
```
Document ID: 1760978795123456
├── Line Item 0: 1760978795123456_line_0 (Monthly Payment)
├── Line Item 1: 1760978795123456_line_1 (Extra Content Credits)  
└── Line Item 2: 1760978795123456_line_2 (Ad Budget)
```

### 3. **PDF Generation**
The generated document shows:
```
INVOICE #I123456
Date: January 15, 2024

Bill To: Client Company Name
Partner ID: PARTNER123

Line Items:
┌─────────────────┬─────────────────────┬──────────┬─────────────┬─────────┐
│ Item Type       │ Description         │ Quantity │ Unit Price  │ Total   │
├─────────────────┼─────────────────────┼──────────┼─────────────┼─────────┤
│ Monthly Payment │ Monthly subscription│    1     │ Rs. 1000.00 │ Rs. 1000.00 │
│ Extra Credits   │ Additional content  │    5     │ Rs. 50.00   │ Rs. 250.00  │
│ Ad Budget       │ Facebook ads        │    1     │ Rs. 500.00  │ Rs. 500.00  │
├─────────────────┴─────────────────────┴──────────┴─────────────┼─────────┤
│                                                      Grand Total │ Rs. 1750.00 │
└─────────────────────────────────────────────────────────────────┴─────────┘
```

## Benefits

### ✅ **Professional Documents**
- Single, clean document with all items
- Proper line-by-line breakdown
- Professional appearance

### ✅ **Better Organization**
- One document per transaction
- Easier to track and manage
- Clear itemization

### ✅ **Improved User Experience**
- Add multiple items in one form
- See total calculation in real-time
- Generate one comprehensive document

## Technical Implementation

### **Frontend (Modal)**
- Dynamic item addition/removal
- Real-time total calculation
- Form validation for all items

### **Backend (Database)**
- Single transaction for all line items
- Proper ordering with `item_order` field
- Grand total calculation

### **PDF Generation**
- Retrieves all line items for document
- Displays in professional table format
- Shows grand total at bottom

## Usage Example

### **Creating a Multi-Item Invoice:**

1. **Open Financial Management**
2. **Click "Create Invoice"**
3. **Fill in Client and Date**
4. **Add Items:**
   - Item 1: Monthly Payment - Rs. 1000.00
   - Item 2: Extra Content Credits (5 units) - Rs. 250.00
   - Item 3: Ad Budget - Rs. 500.00
5. **Review Total: Rs. 1750.00**
6. **Generate Document**

### **Result:**
- ✅ **One Invoice** with ID `I123456`
- ✅ **Three Line Items** properly ordered
- ✅ **Professional PDF** with all details
- ✅ **Grand Total** calculated correctly

## Database Changes

### **New Structure:**
```sql
-- Each line item is stored as a separate row
documents table:
├── id: 1760978795123456_line_0 (Monthly Payment)
├── id: 1760978795123456_line_1 (Extra Credits)
└── id: 1760978795123456_line_2 (Ad Budget)
```

### **Retrieval:**
```sql
-- Get all line items for a document
SELECT * FROM documents 
WHERE id = '1760978795123456' OR id LIKE '1760978795123456_line_%'
ORDER BY item_order ASC;
```

## Files Modified

1. **`handler_finance.php`** - Line item insertion logic
2. **`handler_download.php`** - Document retrieval for PDF
3. **`simple_pdf.php`** - Multi-line item display
4. **`index.html`** - Frontend modal functionality

## Testing

**Test the new functionality:**
- Visit: `https://yourdomain.com/ayonion-cms/test_real_scenario.php`
- Create documents with multiple items
- Verify single document with line items
- Check PDF generation

## Summary

The system now creates **one professional document** with **multiple line items** instead of separate documents. This provides:

- ✅ Better organization
- ✅ Professional appearance  
- ✅ Easier management
- ✅ Clear itemization
- ✅ Proper totals

Perfect for creating comprehensive invoices, quotations, and receipts with multiple services/products!
