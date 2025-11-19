# Dual Logo System Implementation

## Overview
The system now supports **two separate logos** for different background contexts:
- **Logo for Light Backgrounds** - Used in documents, invoices, receipts, and reports (white/light backgrounds)
- **Logo for Dark Backgrounds** - Used in login screen, sidebar, and other dark UI elements

## Database Migration Required

**IMPORTANT**: Run the SQL migration before using the dual logo feature:

```sql
-- File: add_dual_logo_columns.sql
-- Execute this in phpMyAdmin or your MySQL client

ALTER TABLE settings 
ADD COLUMN logo_light VARCHAR(500) DEFAULT '' AFTER logo_url,
ADD COLUMN logo_dark VARCHAR(500) DEFAULT '' AFTER logo_light;

UPDATE settings 
SET logo_light = logo_url, logo_dark = logo_url 
WHERE logo_url != '' AND logo_url IS NOT NULL;
```

## Features Implemented

### 1. Settings Page Updates
- **Two Upload Fields**:
  - "Logo for Light Backgrounds" - Shows on light gray preview background
  - "Logo for Dark Backgrounds" - Shows on dark preview background
- Individual upload, preview, and remove buttons for each logo
- Visual indicators showing which background each logo is used for

### 2. Logo Usage by Context

| Area | Logo Used | Reason |
|------|-----------|--------|
| **Login Screen** | Dark Logo | Dark card background (#030B0D) |
| **Sidebar** | Dark Logo | Dark gradient background |
| **Documents/Invoices** | Light Logo | White/light backgrounds |
| **Reports (PDF)** | Light Logo | White paper background |
| **Receipts** | Light Logo | White background |

### 3. Backward Compatibility
- Existing `logo_url` column is preserved
- If dual logos aren't set, system falls back to `logo_url`
- Migration script copies existing logo to both new fields

### 4. Updated Files

**Backend:**
- `handler_settings.php` - Added `logo_light` and `logo_dark` fields to GET/UPDATE operations
- `add_dual_logo_columns.sql` - Database migration script

**Frontend:**
- `index.php` - Updated settings form with dual logo upload fields
- JavaScript functions: `handleLogoLightUpload()`, `handleLogoDarkUpload()`, `removeLogoLight()`, `removeLogoDark()`
- Updated `loadSettings()` to load both logos
- Updated `loadCompanySettings()` to include both logos in COMPANY_INFO
- Updated `loadLoginLogo()` to use dark logo for login screen
- Updated sidebar to use dark logo

## How to Use

### Step 1: Run Database Migration
Execute `add_dual_logo_columns.sql` in your database.

### Step 2: Upload Logos
1. Login as **Admin**
2. Go to **Settings** section
3. Upload two versions of your logo:
   - **Light Background Logo**: Dark/colored version that shows well on white
   - **Dark Background Logo**: Light/white version that shows well on dark backgrounds

### Step 3: Verify Display
Check that logos appear correctly in:
- ✓ Login screen (dark logo)
- ✓ Sidebar (dark logo)
- ✓ Generated invoices (light logo)
- ✓ Generated receipts (light logo)
- ✓ Campaign reports (light logo)

## Design Recommendations

### For Light Background Logo:
- Use your standard logo with dark or colored elements
- Ensure good contrast against white/light gray (#f8f9fa)
- Recommended formats: PNG with transparency, SVG
- Example: Dark text logo, colored brand logo

### For Dark Background Logo:
- Use white or light-colored version of logo
- Ensure visibility against dark backgrounds (#030B0D, #2E404C)
- Recommended formats: PNG with transparency, white/light SVG
- Example: White logo, light outlined logo

## File Size Limits
- Maximum file size: **2 MB**
- Supported formats: **JPG, PNG, SVG, GIF**
- Recommended: PNG with transparency for best results

## Troubleshooting

### Logo doesn't update immediately
- Refresh the page after saving settings
- Clear browser cache if needed

### Logo appears on wrong background
- Verify you uploaded to the correct field
- Check the preview backgrounds:
  - Light preview = light gray background
  - Dark preview = dark (#2E404C) background

### Logo too large/small
- Logos are automatically sized to fit their containers
- Maximum display height: 100px in previews, 60px in sidebar
- Use vector formats (SVG) for best scaling

## Database Schema

```sql
settings table:
- id (INT)
- company_name (VARCHAR 255)
- logo_url (VARCHAR 500)        -- Legacy, kept for compatibility
- logo_light (VARCHAR 500)      -- NEW: For light backgrounds
- logo_dark (VARCHAR 500)       -- NEW: For dark backgrounds
- email (VARCHAR 255)
- phone (VARCHAR 50)
- website (VARCHAR 255)
- address (TEXT)
```

## Technical Notes

### COMPANY_INFO Object
Now includes:
```javascript
{
  logoUrl: settings.logo_light,    // Default to light for documents
  logoLight: settings.logo_light,
  logoDark: settings.logo_dark
}
```

### Fallback Logic
```
Dark contexts: logo_dark → logo_url → icon fallback
Light contexts: logo_light → logo_url → empty
```

## Support
If you experience any issues with the dual logo system, verify:
1. ✓ Database migration ran successfully
2. ✓ Both logo files uploaded correctly
3. ✓ File permissions allow uploads to `uploads/logos/` directory
4. ✓ Browser cache cleared after making changes
