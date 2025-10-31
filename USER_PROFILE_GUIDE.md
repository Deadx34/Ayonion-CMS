# User Profile Module - Implementation Guide

## Overview
Added a comprehensive User Profile module with password management and role-based access control.

## Features Implemented

### 1. My Profile Section
- **Available to:** All users (Admin, Marketer, Finance)
- **Features:**
  - View username and role (read-only)
  - Edit full name
  - Edit email address
  - Save profile information

### 2. Change Password
- **Available to:** All users
- **Features:**
  - Verify current password
  - Set new password (minimum 6 characters)
  - Confirm new password
  - Secure password hashing using PHP's `password_hash()`

### 3. Role-Based Access Control (RBAC)
- **Admin users:**
  - Access to all sections including "Admin Settings" and "Manage Users"
  - Can manage company settings, users, and all other features

- **Marketer users:**
  - Access to Dashboard, Client Profiles, Content Credits, Ad Campaigns, and My Profile
  - Cannot see "Admin Settings" or "Manage Users"

- **Finance users:**
  - Access to Dashboard, Client Profiles, Financial Management, and My Profile
  - Cannot see "Admin Settings" or "Manage Users"

## Database Changes

### New Columns Added to `users` Table:
- `full_name` VARCHAR(255) - User's full name
- `email` VARCHAR(255) - User's email address
- Index on `email` column for faster lookups

## Files Modified

### Frontend Files:
1. **index.html** - Added profile section, navigation, and JavaScript functions
2. **index.php** - Mirrored changes from index.html for PHP deployment

### Backend Files:
1. **handler_users.php** - Added three new API endpoints:
   - `GET ?action=get_profile` - Fetch current user's profile
   - `POST ?action=update_profile` - Update user's full name and email
   - `POST ?action=change_password` - Change user's password

### Migration Files:
1. **migrate_user_profile.sql** - SQL migration script
2. **run_migration_user_profile.php** - Web-based migration runner

## Installation Steps

### Step 1: Run Database Migration
**Option A - Web Interface (Recommended):**
1. Navigate to: `http://localhost/ayonion-cms/run_migration_user_profile.php`
2. Verify all checks show ✅
3. Click "Go to Application"

**Option B - Command Line:**
```powershell
mysql -u your_username -p your_database < migrate_user_profile.sql
```

### Step 2: Test the Feature
1. Login as any user (admin, marketer, or finance)
2. Click "My Profile" in the sidebar
3. Update your full name and email, click "Save Profile"
4. Test password change:
   - Enter current password
   - Enter new password (min 6 characters)
   - Confirm new password
   - Click "Change Password"

## Navigation Updates

### Menu Items:
- **My Profile** - New item, visible to all users
- **Manage Users** - Only visible to admin
- **Admin Settings** - Renamed from "Settings", only visible to admin

## Security Features

1. **Password Requirements:**
   - Minimum 6 characters
   - Passwords must match confirmation
   - Current password verification required

2. **Access Control:**
   - Profile endpoints accessible to all authenticated users
   - Users can only view/edit their own profile
   - Admin-only sections hidden from non-admin users

3. **Password Storage:**
   - Uses PHP's `password_hash()` with bcrypt
   - Passwords never stored in plain text
   - Old password verified before allowing changes

## API Endpoints

### Get User Profile
```
GET handler_users.php?action=get_profile
Response: {
  "success": true,
  "profile": {
    "username": "john_doe",
    "role": "marketer",
    "full_name": "John Doe",
    "email": "john@example.com"
  }
}
```

### Update Profile
```
POST handler_users.php?action=update_profile
Body: {
  "fullName": "John Doe",
  "email": "john@example.com"
}
Response: {
  "success": true,
  "message": "Profile updated successfully."
}
```

### Change Password
```
POST handler_users.php?action=change_password
Body: {
  "currentPassword": "oldpass123",
  "newPassword": "newpass456"
}
Response: {
  "success": true,
  "message": "Password changed successfully."
}
```

## Testing Checklist

- [ ] Admin can see all menu items (including Admin Settings and Manage Users)
- [ ] Marketer cannot see Admin Settings or Manage Users
- [ ] Finance cannot see Admin Settings or Manage Users
- [ ] All users can access My Profile
- [ ] Profile form loads current user data
- [ ] Profile updates save successfully
- [ ] Password change with wrong current password fails
- [ ] Password change with mismatched passwords fails
- [ ] Password change with valid data succeeds
- [ ] New password works on next login

## Troubleshooting

### Migration Issues:
- If web migration fails, try command-line approach
- Check MySQL user has ALTER TABLE permissions
- Verify database connection in `includes/config.php`

### Permission Issues:
- Clear browser cache if menu items don't update
- Verify user role in database: `SELECT username, role FROM users;`
- Check browser console for JavaScript errors

### Password Change Issues:
- Ensure current password is correct
- New password must be at least 6 characters
- Both new password fields must match

## Future Enhancements

Potential additions:
- Profile picture upload
- Email verification
- Password strength indicator
- Two-factor authentication
- Activity log
- Password reset via email
