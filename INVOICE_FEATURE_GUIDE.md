# Invoice Generation Feature - Implementation Guide

## 🎯 Overview
This feature allows users to generate invoices for clients based on selected ad campaigns. The implementation includes a complete frontend interface with checkbox selection, invoice preview modal, and backend API for data persistence.

## 🗄️ Database Setup

### 1. Create Invoice Tables
Run the SQL script to create the required tables:

```sql
-- Execute the contents of create_invoice_tables.sql
```

Or run the test script:
```bash
php test_invoice_setup.php
```

### 2. Database Schema

**invoices table:**
- `id` (BIGINT PRIMARY KEY) - Unique invoice identifier
- `client_id` (BIGINT) - Foreign key to clients table
- `total_amount` (DECIMAL) - Total invoice amount
- `created_at` (TIMESTAMP) - Invoice creation date
- `status` (VARCHAR) - Invoice status (draft, sent, paid, etc.)
- `invoice_number` (VARCHAR) - Human-readable invoice number
- `due_date` (DATE) - Payment due date
- `notes` (TEXT) - Additional notes

**invoice_items table:**
- `id` (INT AUTO_INCREMENT PRIMARY KEY) - Unique item identifier
- `invoice_id` (BIGINT) - Foreign key to invoices table
- `campaign_id` (BIGINT) - Foreign key to campaigns table
- `amount` (DECIMAL) - Item amount
- `description` (TEXT) - Item description

## 🎨 Frontend Features

### 1. Campaign Selection Interface
- **Checkbox Column**: Added to campaigns table for multi-selection
- **Select All**: Master checkbox to select/deselect all campaigns
- **Generate Invoice Button**: Appears when campaigns are selected

### 2. Invoice Preview Modal
- **Client Details**: Shows client information and billing details
- **Campaign List**: Displays selected campaigns with amounts
- **Total Calculation**: Auto-calculates total invoice amount
- **Professional Layout**: Clean, printable invoice format

### 3. Actions Available
- **Save Invoice**: Creates database record and invoice items
- **Download PDF**: Opens print dialog for PDF generation
- **Cancel**: Closes modal without saving

## 🔧 Backend API

### Endpoints Available

#### 1. Create Invoice
```
POST /handler_invoices.php?action=create
Content-Type: application/json

{
    "clientId": 123,
    "selectedCampaigns": [456, 789, 101],
    "notes": "Optional notes"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Invoice created successfully.",
    "invoiceId": 1234567890,
    "invoiceNumber": "INV-2024-1234567890",
    "totalAmount": 15000.00
}
```

#### 2. Get Invoice Details
```
GET /handler_invoices.php?action=get&id=1234567890
```

#### 3. List Client Invoices
```
GET /handler_invoices.php?action=list&clientId=123
```

#### 4. Update Invoice Status
```
PUT /handler_invoices.php?action=update_status
Content-Type: application/json

{
    "invoiceId": 1234567890,
    "status": "sent"
}
```

## 🚀 Usage Workflow

### Step 1: Select Client
1. Navigate to "Ad Campaign Management" section
2. Choose a client from the dropdown
3. Campaigns for that client will be displayed

### Step 2: Select Campaigns
1. Use individual checkboxes to select specific campaigns
2. Or use "Select All" checkbox to select all campaigns
3. "Generate Invoice" button will appear when campaigns are selected

### Step 3: Generate Invoice
1. Click "Generate Invoice" button
2. Review the invoice preview in the modal
3. Verify client details and campaign amounts

### Step 4: Save or Download
1. **Save Invoice**: Creates database record and returns invoice number
2. **Download PDF**: Opens print dialog for PDF generation
3. **Cancel**: Closes modal without saving

## 🎨 UI Components

### Modified Elements

#### Campaigns Table
```html
<th width="50">
    <input type="checkbox" id="selectAllCampaigns" onchange="toggleAllCampaigns()">
</th>
```

#### Generate Invoice Button
```html
<button class="btn btn-success me-2 mt-2" id="generateInvoiceBtn" onclick="showInvoiceModal()" style="display:none;">
    <i class="fas fa-file-invoice me-2"></i>Generate Invoice
</button>
```

#### Invoice Preview Modal
- Professional invoice layout
- Client billing information
- Campaign details table
- Total amount calculation
- Action buttons (Save, Download, Cancel)

## 🔧 JavaScript Functions

### Core Functions

#### `toggleAllCampaigns()`
- Handles master checkbox functionality
- Selects/deselects all campaign checkboxes

#### `updateSelectedCampaigns()`
- Updates selected campaigns array
- Shows/hides Generate Invoice button
- Manages UI state

#### `showInvoiceModal()`
- Validates selected campaigns
- Generates invoice preview HTML
- Opens invoice preview modal

#### `saveInvoice()`
- Sends POST request to backend
- Creates invoice and invoice_items records
- Shows success/error messages
- Resets UI state

#### `downloadInvoicePDF()`
- Opens new window with print-friendly layout
- Triggers browser print dialog
- Generates PDF-ready HTML

## 🎯 Key Features

### ✅ Multi-Campaign Selection
- Individual campaign checkboxes
- Select all functionality
- Visual feedback for selections

### ✅ Professional Invoice Layout
- Client billing information
- Campaign details with amounts
- Total calculation
- Print-ready formatting

### ✅ Database Integration
- Invoice records with metadata
- Campaign-to-invoice mapping
- Proper foreign key relationships

### ✅ PDF Generation
- Browser-based PDF generation
- Print-optimized styling
- Professional invoice format

### ✅ Error Handling
- Validation for selected campaigns
- Backend error responses
- User-friendly error messages

## 🔍 Testing

### Manual Testing Steps
1. **Database Setup**: Run `test_invoice_setup.php`
2. **Select Client**: Choose a client with campaigns
3. **Select Campaigns**: Use checkboxes to select campaigns
4. **Generate Invoice**: Click Generate Invoice button
5. **Preview Invoice**: Review invoice details in modal
6. **Save Invoice**: Test database record creation
7. **Download PDF**: Test PDF generation functionality

### Expected Results
- ✅ Checkboxes work correctly
- ✅ Generate Invoice button appears/disappears
- ✅ Invoice preview shows correct data
- ✅ Database records are created
- ✅ PDF generation works
- ✅ Error handling works properly

## 🎨 Styling Notes

The implementation maintains the existing design system:
- **Purple sidebar** and card layouts
- **Bootstrap 5** components and styling
- **Font Awesome** icons
- **Consistent color scheme**
- **Responsive design**

## 🔧 Customization Options

### Invoice Template
Modify the invoice HTML template in `showInvoiceModal()` function to customize:
- Company branding
- Invoice layout
- Additional fields
- Styling

### PDF Generation
Enhance PDF generation by:
- Adding company logo
- Custom styling
- Additional invoice fields
- Professional formatting

### Database Schema
Extend the database schema for:
- Tax calculations
- Payment tracking
- Invoice status workflow
- Additional metadata

## 🚀 Future Enhancements

### Potential Improvements
1. **Email Integration**: Send invoices via email
2. **Payment Tracking**: Track invoice payments
3. **Tax Calculations**: Add tax support
4. **Invoice Templates**: Multiple invoice templates
5. **Bulk Operations**: Generate multiple invoices
6. **Reporting**: Invoice analytics and reports

### Advanced Features
1. **Recurring Invoices**: Automated recurring billing
2. **Payment Gateway**: Online payment integration
3. **Invoice Approval**: Workflow for invoice approval
4. **Client Portal**: Client access to invoices
5. **Export Options**: Excel, CSV export functionality

## 📝 Notes

- The feature is fully integrated with the existing CMS
- Maintains existing design patterns and user experience
- Uses the same authentication and permission system
- Compatible with existing client and campaign data
- No breaking changes to existing functionality

## 🎯 Success Criteria

✅ **Functional Requirements Met:**
- Multi-campaign selection with checkboxes
- Professional invoice preview modal
- Database persistence with proper relationships
- PDF generation capability
- Clean, modern UI consistent with existing design

✅ **Technical Requirements Met:**
- Modular, maintainable code structure
- Proper error handling and validation
- Database transactions for data integrity
- Responsive design and accessibility
- Cross-browser compatibility
