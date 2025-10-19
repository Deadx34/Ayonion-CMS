# Logo Storage Options for AYONION CMS

## Current Implementation (Data URLs in Database)

**Location:** `settings` table, `logo_url` column (TEXT)
**Pros:** Simple, no file system management
**Cons:** Large database, slower queries, memory usage

## Recommended Options

### Option 1: File System Storage (IMPLEMENTED)

**Files Created:**
- `upload_logo_handler.php` - Handles file uploads
- `uploads/logos/` - Directory for logo files
- `.htaccess` - Security configuration

**How it works:**
1. User selects logo file
2. File uploaded to `uploads/logos/` directory
3. Database stores file path (e.g., `uploads/logos/logo_1234567890_abc123.png`)
4. Frontend displays image using file path

**Directory Structure:**
```
ayonion-cms/
├── uploads/
│   └── logos/
│       ├── logo_1234567890_abc123.png
│       ├── logo_1234567891_def456.jpg
│       └── ...
├── upload_logo_handler.php
├── .htaccess
└── index.html
```

**Security Features:**
- File type validation (JPEG, PNG, GIF, WebP only)
- File size limit (2MB max)
- Unique filenames prevent conflicts
- PHP execution blocked in uploads directory
- Old files automatically deleted when new logo uploaded

### Option 2: Cloud Storage (AWS S3, Google Cloud, etc.)

**Benefits:**
- Scalable storage
- CDN integration
- Backup and redundancy
- No server storage limits

**Implementation:**
```php
// Example for AWS S3
use Aws\S3\S3Client;

$s3Client = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1',
    'credentials' => [
        'key'    => 'YOUR_ACCESS_KEY',
        'secret' => 'YOUR_SECRET_KEY',
    ],
]);

$result = $s3Client->putObject([
    'Bucket' => 'your-bucket-name',
    'Key'    => 'logos/logo_' . time() . '.jpg',
    'Body'   => file_get_contents($_FILES['logo']['tmp_name']),
    'ACL'    => 'public-read'
]);

$logoUrl = $result['ObjectURL'];
```

### Option 3: CDN Integration

**Popular CDNs:**
- Cloudflare
- AWS CloudFront
- Google Cloud CDN

**Benefits:**
- Faster image loading
- Global distribution
- Automatic optimization
- Bandwidth savings

## Setup Instructions

### For File System Storage (Current Implementation):

1. **Create uploads directory:**
```bash
mkdir -p uploads/logos
chmod 755 uploads/logos
```

2. **Set proper permissions:**
```bash
chown www-data:www-data uploads/logos
chmod 755 uploads
```

3. **Test upload:**
- Go to Settings tab
- Upload a logo image
- Check if file appears in `uploads/logos/` directory

### For Production Deployment:

1. **Update .htaccess for security:**
```apache
# Prevent direct access to PHP files in uploads
<Directory "uploads">
    <Files "*.php">
        Require all denied
    </Files>
</Directory>

# Set proper MIME types
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Header set Cache-Control "max-age=31536000"
</FilesMatch>
```

2. **Configure web server:**
```nginx
# Nginx configuration
location /uploads/ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

## Database Schema

**Current (Data URLs):**
```sql
ALTER TABLE settings MODIFY COLUMN logo_url TEXT DEFAULT '';
```

**Recommended (File Paths):**
```sql
ALTER TABLE settings MODIFY COLUMN logo_url VARCHAR(255) DEFAULT '';
```

## Migration from Data URLs to File Storage

If you want to migrate existing data URLs to file storage:

```php
// migration_script.php
<?php
require_once 'includes/config.php';

$result = $conn->query("SELECT logo_url FROM settings WHERE id = 1");
$row = $result->fetch_assoc();

if ($row && $row['logo_url'] && strpos($row['logo_url'], 'data:image/') === 0) {
    // Convert data URL to file
    $dataUrl = $row['logo_url'];
    $imageData = base64_decode(explode(',', $dataUrl)[1]);
    $extension = explode('/', explode(';', $dataUrl)[0])[1];
    
    $filename = 'logo_' . time() . '.' . $extension;
    $filepath = 'uploads/logos/' . $filename;
    
    if (file_put_contents($filepath, $imageData)) {
        $stmt = $conn->prepare("UPDATE settings SET logo_url = ? WHERE id = 1");
        $stmt->bind_param("s", $filepath);
        $stmt->execute();
        echo "Migration completed successfully!";
    }
}
?>
```

## Best Practices

### Security:
- Validate file types server-side
- Limit file sizes
- Use unique filenames
- Block PHP execution in upload directories
- Sanitize file names

### Performance:
- Compress images before storage
- Use appropriate image formats (WebP for modern browsers)
- Implement caching headers
- Consider CDN for global distribution

### Maintenance:
- Clean up old/unused files
- Regular backups of uploads directory
- Monitor disk space usage
- Implement file rotation policies

## Troubleshooting

### Common Issues:

1. **Permission denied errors:**
```bash
chmod 755 uploads/logos
chown www-data:www-data uploads/logos
```

2. **File not found errors:**
- Check if `uploads/logos/` directory exists
- Verify file permissions
- Check web server configuration

3. **Upload size limits:**
```php
// In php.ini
upload_max_filesize = 2M
post_max_size = 2M
max_execution_time = 30
```

4. **Database connection issues:**
- Verify database credentials in `includes/config.php`
- Check if `settings` table exists
- Ensure proper user permissions

## File Structure Summary

```
ayonion-cms/
├── uploads/                    # Upload directory
│   └── logos/                 # Logo storage
│       ├── logo_123456.png    # Example logo files
│       └── logo_789012.jpg
├── upload_logo_handler.php    # Upload handler
├── .htaccess                  # Security rules
├── includes/
│   └── config.php             # Database config
└── index.html                 # Main application
```

This setup provides a secure, scalable solution for logo storage with proper file management and security measures.
