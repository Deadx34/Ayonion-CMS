# Auto Carry Forward System - Setup Guide

## Overview
The Auto Carry Forward System automatically manages monthly credit cycles for all clients in the Ayonion CMS. It ensures that unused credits are carried forward to the next month without manual intervention.

## How It Works

### Monthly Credit Cycle
1. **Default Monthly Credits**: Each month starts with **40 credits** as package credits
2. **Automatic Carry Forward**: All unused credits automatically carry forward to the next month
3. **Example Flow**:
   - Month 1: 40 credits allocated, 30 used → 10 remaining
   - Month 2: 40 new credits + 10 carried forward = **50 total credits**
   - Month 3: 40 new credits + (50-35 used) = **55 total credits**

### Process Trigger
The system processes carry forwards for clients whose renewal date has been reached or passed.

## Database Setup

### 1. Run Migration
Execute the SQL migration to add the required column:

```bash
mysql -u your_username -p your_database < migrate_auto_carry_forward.sql
```

Or run via phpMyAdmin:
```sql
ALTER TABLE clients ADD COLUMN IF NOT EXISTS last_carry_forward DATETIME DEFAULT NULL;
CREATE INDEX IF NOT EXISTS idx_renewal_date ON clients(renewal_date);
UPDATE clients SET package_credits = 40 WHERE package_credits = 0 OR package_credits IS NULL;
```

## Automated Setup (Recommended)

### Option 1: Linux/Unix Cron Job

1. Open crontab editor:
```bash
crontab -e
```

2. Add this line to run at 12:00 AM on the 1st of every month:
```bash
0 0 1 * * /usr/bin/php /path/to/ayonion-cms/handler_auto_carry_forward.php >> /path/to/ayonion-cms/logs/cron.log 2>&1
```

### Option 2: Windows Task Scheduler

1. Open Task Scheduler
2. Create Basic Task
3. **Trigger**: Monthly, on day 1 at 12:00 AM
4. **Action**: Start a program
5. **Program**: `C:\xampp\php\php.exe`
6. **Arguments**: `C:\xampp\htdocs\ayonion-cms\handler_auto_carry_forward.php`

### Option 3: cPanel Cron Jobs

1. Login to cPanel
2. Navigate to "Cron Jobs"
3. Add new cron job:
   - **Common Settings**: Once Per Month (0 0 1 * *)
   - **Command**: `/usr/bin/php /home/username/public_html/ayonion-cms/handler_auto_carry_forward.php`

## Manual Execution

### Via Web Interface
1. Login as Admin
2. Go to **Settings** section
3. Scroll to **Auto Carry Forward System**
4. Click **"Run Auto Carry Forward Now"**

### Via Command Line
```bash
php handler_auto_carry_forward.php
```

### Via Web Browser (for testing)
```
https://yourdomain.com/ayonion-cms/handler_auto_carry_forward.php?manual=true
```

## Monitoring & Logs

### View Process Log
- **Via Interface**: Settings → Auto Carry Forward System → "View Process Log"
- **Direct Access**: `logs/auto_carry_forward.log`

### Log Contents
The log file contains:
- Timestamp of each run
- Client-by-client processing details
- Credits carried forward
- New renewal dates
- Error messages (if any)
- Summary statistics

### Example Log Entry
```
2025-10-30 00:00:01 - Auto Carry Forward Process Started
  ✓ Client: ABC Company (ID: 1)
    Previous: Used 30 of 40 credits
    Carried Forward: 10 credits
    New Cycle: 40 + 10 = 50 total credits
    New Renewal Date: 2025-11-30

Summary:
  Total Processed: 5
  Errors: 0
  Process Completed: 2025-10-30 00:00:05
```

## Configuration

### Adjust Default Credits
To change the default monthly credits (currently 40):

1. Edit `handler_auto_carry_forward.php`
2. Modify this line:
```php
define('DEFAULT_MONTHLY_CREDITS', 40); // Change to desired amount
```

## Features

### ✅ Automatic Processing
- Runs without manual intervention
- Processes all eligible clients at once
- Updates renewal dates automatically

### ✅ Safe & Reliable
- Database transactions ensure data integrity
- Rollback on errors
- Detailed logging for audit trail

### ✅ Flexible Scheduling
- Can run daily (will only process eligible clients)
- Monthly recommended for efficiency
- Manual trigger available anytime

### ✅ Credit Preservation
- No credits are lost
- All unused credits carry forward
- History maintained in database

## Troubleshooting

### Issue: Cron job not running
**Solution**: Check cron logs
```bash
grep CRON /var/log/syslog
```

### Issue: Permission denied
**Solution**: Make handler executable
```bash
chmod +x handler_auto_carry_forward.php
```

### Issue: PHP not found
**Solution**: Use full path to PHP
```bash
which php
# Then use that path in cron job
```

### Issue: Log file not created
**Solution**: Ensure logs directory exists and is writable
```bash
mkdir -p logs
chmod 755 logs
```

## Security Notes

1. **Restrict Access**: The handler should only be accessible via cron or admin panel
2. **Database Credentials**: Ensure config.php is secured and not web-accessible
3. **Log Files**: Protect logs directory from public access via .htaccess

## Testing

### Test the System
1. Set a client's renewal date to today
2. Run auto carry forward manually
3. Check the results in the interface
4. Verify log file created
5. Confirm client credits updated correctly

### Validation Checklist
- [ ] Database migration completed
- [ ] Cron job configured and running
- [ ] Log directory created and writable
- [ ] Manual trigger works from admin panel
- [ ] Test client processed successfully
- [ ] Log file generated correctly
- [ ] Email notifications working (if configured)

## Support

For issues or questions:
1. Check the process log first
2. Review database transactions
3. Verify cron job configuration
4. Contact system administrator

---

**Version**: 1.0  
**Last Updated**: October 30, 2025  
**Author**: Ayonion Studios Development Team
