# 📊 Database Migration Guide - phpMyAdmin

## Step-by-Step Instructions with Screenshots

### Method 1: Using SQL Tab (Recommended)

#### Step 1: Open phpMyAdmin
1. Open your web browser
2. Navigate to: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin interface

#### Step 2: Select Your Database
1. Look at the left sidebar
2. Click on your database name (usually something like `ayonion_cms` or `cms_database`)
3. The database will be highlighted

#### Step 3: Go to SQL Tab
1. At the top of the page, you'll see several tabs: Structure, SQL, Search, Query, etc.
2. Click on the **"SQL"** tab
3. You'll see a large text area for entering SQL commands

#### Step 4: Open the Migration File
1. Open File Explorer
2. Navigate to: `C:\xampp\htdocs\ayonion-cms\`
3. Find the file: `migrate_subscription_tracking.sql`
4. Right-click → Open with → Notepad (or your preferred text editor)
5. Press `Ctrl+A` to select all
6. Press `Ctrl+C` to copy

#### Step 5: Paste and Execute
1. Go back to phpMyAdmin (SQL tab)
2. Click inside the SQL text area
3. Press `Ctrl+V` to paste the SQL code
4. Click the **"Go"** button at the bottom right
5. Wait for execution (should take 1-2 seconds)

#### Step 6: Verify Success
You should see a green message box saying:
```
4 rows affected.
```

This means:
- ✅ 4 new columns were added successfully
- ✅ Existing clients were updated with default values
- ✅ Indexes were created

---

### Method 2: Using Import Tab (Alternative)

#### Step 1: Open phpMyAdmin
Navigate to: `http://localhost/phpmyadmin`

#### Step 2: Select Your Database
Click your database name in the left sidebar

#### Step 3: Go to Import Tab
Click the **"Import"** tab at the top

#### Step 4: Choose File
1. Click **"Choose File"** button
2. Navigate to: `C:\xampp\htdocs\ayonion-cms\`
3. Select: `migrate_subscription_tracking.sql`
4. Click **"Open"**

#### Step 5: Execute Import
1. Scroll down to the bottom
2. Click the **"Go"** button
3. Wait for execution

#### Step 6: Verify Success
Look for green success message

---

## 🔍 Verification Steps

### Option A: Quick Check
1. In phpMyAdmin, click your database name
2. Click the **"Structure"** tab
3. Find the `clients` table
4. Click on `clients` to view its structure
5. Look for these NEW columns:
   - ✅ `subscription_months`
   - ✅ `subscription_start_date`
   - ✅ `subscription_end_date`
   - ✅ `last_carry_forward_date`

### Option B: SQL Verification
1. Go to SQL tab
2. Run this query:
```sql
SHOW COLUMNS FROM clients LIKE 'subscription%';
```
3. You should see 3 rows returned

### Option C: Browser Verification
Open in browser: `http://localhost/ayonion-cms/check_database.php`

Should return:
```json
{
  "success": true,
  "message": "All required columns exist",
  "columns": [
    "subscription_months",
    "subscription_start_date",
    "subscription_end_date",
    "last_carry_forward_date"
  ]
}
```

---

## 📋 The SQL Being Executed

Here's what the migration does:

```sql
-- 1. Add 4 new columns to clients table
ALTER TABLE clients 
ADD COLUMN IF NOT EXISTS subscription_months INT DEFAULT 12,
ADD COLUMN IF NOT EXISTS subscription_start_date DATE,
ADD COLUMN IF NOT EXISTS subscription_end_date DATE,
ADD COLUMN IF NOT EXISTS last_carry_forward_date DATE;

-- 2. Update existing clients with default values
UPDATE clients 
SET 
    subscription_months = 12,
    subscription_start_date = renewalDate,
    subscription_end_date = DATE_ADD(renewalDate, INTERVAL 12 MONTH)
WHERE subscription_months IS NULL;

-- 3. Create indexes for better performance
CREATE INDEX idx_renewal_date ON clients(renewalDate);
CREATE INDEX idx_subscription_end_date ON clients(subscription_end_date);
CREATE INDEX idx_last_carry_forward ON clients(last_carry_forward_date);
```

---

## ⚠️ Troubleshooting

### Error: "Table 'clients' doesn't exist"
**Solution:** Make sure you selected the correct database in Step 2

### Error: "Duplicate column name"
**Solution:** Migration already ran successfully! Skip to verification.

### Error: "Access denied"
**Solution:** Make sure you're logged into phpMyAdmin as root or admin user

### Error: "Can't create/write to file"
**Solution:** 
1. Check XAMPP is running
2. Restart MySQL service in XAMPP Control Panel

### Nothing happens when clicking "Go"
**Solution:**
1. Make sure you pasted the SQL code
2. Check browser console (F12) for JavaScript errors
3. Try refreshing phpMyAdmin

---

## ✅ After Migration Success

### Next Steps:
1. ✅ Database migration complete
2. ✅ Open the CMS: `http://localhost/ayonion-cms/index.php`
3. ✅ Go to Clients section
4. ✅ Click "Add New Client"
5. ✅ You should see the new field: **"Subscription Duration (Months)"**
6. ✅ Test the system with a client whose renewal date is yesterday

### Testing:
1. Create a test client
2. Set renewal date to yesterday
3. Set subscription duration to 2 months
4. Save the client
5. Reload the page (F5)
6. Open browser console (F12)
7. Look for: `✅ Auto Carry Forward: 1 client(s) processed`

---

## 🎊 Success!

Once you see the green success message in phpMyAdmin, the database migration is complete! The Auto Carry Forward System will now work automatically.

---

## 📞 Need Visual Help?

If you need more detailed visual guidance:

1. **XAMPP Control Panel Location:** Usually at `C:\xampp\xampp-control.exe`
2. **Make sure these are running:**
   - ✅ Apache (green "Running" status)
   - ✅ MySQL (green "Running" status)

3. **phpMyAdmin Access:**
   - URL: `http://localhost/phpmyadmin`
   - Default Username: `root`
   - Default Password: (empty - just press login)

4. **Database Name:**
   - Check your `includes/config.php` file
   - Look for: `$db_name = "your_database_name";`
   - Use that database name in phpMyAdmin

---

**Last Updated:** February 2, 2025  
**Status:** Ready for execution
