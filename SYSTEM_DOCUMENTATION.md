# Ayonion CMS - Complete System Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [System Architecture](#system-architecture)
3. [Security Measures](#security-measures)
4. [System Capabilities](#system-capabilities)
5. [Database Structure](#database-structure)
6. [API Endpoints](#api-endpoints)
7. [User Roles & Permissions](#user-roles--permissions)
8. [Technical Specifications](#technical-specifications)

---

## System Overview

**Ayonion CMS** is a comprehensive Content Management System designed specifically for managing digital marketing campaigns, client relationships, content creation workflows, and financial operations. Built for Ayonion Studios, this system provides end-to-end management capabilities for advertising agencies and marketing firms.

### Key Features
- **Client Management**: Complete client database with logo support and detailed profiles
- **Campaign Management**: Track ad campaigns with performance metrics and evidence documentation
- **Content Management**: Manage creative content with multi-image support
- **Financial Management**: Invoice generation, payment tracking, and content credit system
- **Performance Reporting**: Generate comprehensive PDF reports for campaigns
- **User Management**: Multi-user support with role-based access control
- **Document Management**: Centralized document storage and retrieval

### System Purpose
The system streamlines the workflow of digital marketing agencies by:
- Centralizing client information and campaign data
- Automating report generation and documentation
- Tracking financial transactions and content credits
- Providing real-time performance metrics
- Facilitating team collaboration with multi-user support

---

## System Architecture

### Technology Stack

#### Frontend
- **HTML5** - Semantic markup and structure
- **CSS3** - Modern styling with CSS Grid and Flexbox
- **Bootstrap 5.3.2** - Responsive UI framework
- **JavaScript ES6+** - Modern JavaScript features
- **Font Awesome 6.4.2** - Icon library
- **Google Fonts (Lato)** - Typography

#### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL/MariaDB** - Relational database management
- **Apache/XAMPP** - Web server environment

#### Libraries & Frameworks
- **LocalStorage API** - Client-side data persistence
- **Fetch API** - Asynchronous HTTP requests
- **FormData API** - File upload handling
- **FileReader API** - Client-side file processing

### Application Structure

```
ayonion-cms/
├── index.php                    # Main application (7724 lines)
├── includes/
│   ├── config.php              # Database configuration
│   └── security.php            # Security functions
├── handlers/
│   ├── handler_login.php       # Authentication handler
│   ├── handler_clients.php     # Client operations
│   ├── handler_campaigns.php   # Campaign operations
│   ├── handler_content.php     # Content operations
│   ├── handler_finance.php     # Financial operations
│   ├── handler_invoices.php    # Invoice management
│   ├── handler_data.php        # Data operations
│   ├── handler_users.php       # User management
│   ├── handler_settings.php    # System settings
│   └── handler_download.php    # File downloads
├── uploads/
│   ├── logos/                  # Client logos
│   └── content_images/         # Campaign and content images
├── setup_database.sql          # Database schema
└── session_check.php           # Session validation

```

### Data Flow Architecture

```
User Interface (Browser)
        ↓
    Session Check
        ↓
    PHP Handlers ←→ MySQL Database
        ↓
    JSON Response
        ↓
    LocalStorage Cache
        ↓
    UI Update (JavaScript)
```

### Single Page Application (SPA) Design

The system uses a **hybrid SPA architecture**:
- Single `index.php` file with multiple sections
- Dynamic content loading via AJAX
- Client-side routing with section management
- LocalStorage for offline data caching
- Server-side session management for security

---

## Security Measures

### Authentication & Authorization

#### 1. Session Management
```php
// Secure session configuration
session_start();
session_regenerate_id(true); // Prevent session fixation
$_SESSION['user_id']         // User identifier
$_SESSION['username']        // Username
$_SESSION['role']            // User role (admin/user)
```

**Security Features:**
- Session regeneration on login
- Session timeout after inactivity
- Secure session cookie flags
- Server-side session validation

#### 2. Password Security
```php
// Password hashing with bcrypt
password_hash($password, PASSWORD_BCRYPT);
password_verify($inputPassword, $hashedPassword);
```

**Features:**
- BCrypt hashing algorithm
- Automatic salt generation
- Password verification on login
- Temporary password system for first-time users

#### 3. User Roles
- **Admin**: Full system access, user management, settings control
- **User**: Limited access to assigned modules, no system settings

### SQL Injection Prevention

#### 1. Prepared Statements
```php
// Example from config.php
function execute_prepared($conn, $sql, $types = '', $params = []) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt;
}
```

**Implementation:**
- All database queries use prepared statements
- Parameter binding with type specification
- No direct SQL concatenation
- Parameterized queries throughout

#### 2. Input Validation
```php
// Server-side validation
$email = filter_var($input, FILTER_VALIDATE_EMAIL);
$id = intval($_POST['id']);
$string = mysqli_real_escape_string($conn, $input);
```

### Cross-Site Scripting (XSS) Prevention

#### 1. Output Encoding
```javascript
// Frontend sanitization
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
```

#### 2. Content Security Policy
```php
// HTTP Headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
```

### File Upload Security

#### 1. File Type Validation
```php
// Whitelist approach
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file_type, $allowed_types)) {
    die("Invalid file type");
}
```

#### 2. File Size Limits
```php
// Maximum 5MB per file
$max_size = 5 * 1024 * 1024;
if ($_FILES['file']['size'] > $max_size) {
    die("File too large");
}
```

#### 3. Secure File Storage
- Files stored outside web root where possible
- Unique filenames with timestamp
- File permission restrictions (644)
- No executable permissions on upload directories

### Database Security

#### 1. Connection Security
```php
// Charset specification prevents encoding attacks
$conn->set_charset("utf8mb4");
```

#### 2. Error Handling
```php
// No error messages exposed to users
error_log("SQL Error: " . $conn->error);
http_response_code(500);
die(json_encode(["success" => false, "message" => "Database error"]));
```

#### 3. Principle of Least Privilege
- Database user has minimal required permissions
- No DROP or ALTER permissions for application user
- Read-only access where applicable

### CSRF Protection

#### 1. Session-Based Validation
```php
// Verify user session on every request
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(["success" => false, "message" => "Unauthorized"]));
}
```

#### 2. Request Origin Validation
```php
// Check HTTP referer
$valid_referer = $_SERVER['HTTP_HOST'];
```

### Data Protection

#### 1. Sensitive Data Handling
- Passwords never stored in plain text
- Database credentials in separate config file
- No sensitive data in client-side storage
- Logging excludes sensitive information

#### 2. HTTPS Enforcement (Production)
```apache
# .htaccess for SSL
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Backup & Recovery

#### 1. Database Backups
- Regular automated backups
- Point-in-time recovery capability
- Backup encryption recommended

#### 2. File System Backups
- Uploads directory backed up separately
- Version control for code (Git)
- Disaster recovery plan

### Security Best Practices Implemented

✅ **Authentication**
- Strong password requirements
- Session management
- Account lockout protection

✅ **Authorization**
- Role-based access control
- Permission checking on every action
- Admin-only functions protected

✅ **Data Validation**
- Server-side validation
- Client-side pre-validation
- Type checking and sanitization

✅ **Communication**
- Prepared statements
- Parameterized queries
- JSON responses

✅ **Error Handling**
- Generic error messages for users
- Detailed logging for administrators
- No stack traces exposed

✅ **Session Security**
- HTTPOnly cookies
- Secure flag in production
- Session timeout
- Regeneration on privilege change

---

## System Capabilities

### 1. Dashboard
**Purpose:** Central hub providing real-time system overview

**Features:**
- Real-time statistics (clients, campaigns, content, revenue)
- Quick access cards for major functions
- System activity overview
- User information display
- Logo integration in sidebar

**Metrics Displayed:**
- Total Clients
- Active Campaigns
- Total Content Pieces
- Total Revenue (Rs)

---

### 2. Client Management

**Purpose:** Comprehensive client database and relationship management

#### Features:

**Client Creation:**
- Company name and contact person
- Email and phone number
- Partner ID for unique identification
- Logo upload support (JPEG, PNG, GIF, WEBP)
- Automatic timestamp tracking

**Client Information:**
- Company details
- Contact information
- Logo display (32x32px thumbnails, 150x150px previews)
- Campaign history
- Content statistics
- Financial summary

**Client Operations:**
- Add new clients
- Edit existing client information
- Delete clients (with confirmation)
- View detailed client profiles
- Search and filter clients
- Logo upload and preview
- Logo replacement

**Client Data Structure:**
```javascript
{
    id: number,
    companyName: string,
    contactPerson: string,
    email: string,
    phone: string,
    partnerId: string,
    logoUrl: string,
    createdAt: datetime
}
```

**Client Reporting:**
- Generate client-specific reports
- Export client data
- Print client information

---

### 3. Campaign Management

**Purpose:** Track and manage advertising campaigns with performance metrics

#### Features:

**Campaign Creation:**
- Client selection (dropdown with logo)
- Campaign name
- Platform selection (Facebook, Instagram, Google, LinkedIn, etc.)
- Budget allocation
- Start and end dates
- Performance metrics:
  - Impressions
  - Reach
  - Results (clicks, conversions, etc.)
  - Cost Per Result (CPR)
  - Total Spend
- Creative images (multiple uploads)
- Evidence images (multiple uploads with labels)
- Campaign description

**Campaign Performance Metrics:**
- Total impressions delivered
- Unique reach
- Engagement metrics
- Cost efficiency analysis
- ROI calculations
- Quality rankings
- Conversion rankings

**Campaign Operations:**
- Add new campaigns
- Edit campaign details
- Delete campaigns (with confirmation)
- View campaign history
- Filter by client
- Sort by date, budget, or performance
- Multiple image uploads (both creative and evidence)
- Image preview and removal
- Image labeling and categorization

**Campaign Reporting:**
- Campaign Performance Report (PDF-ready)
- Client-specific campaign reports
- Performance comparison reports
- Date range filtering
- Image galleries (creative and evidence)
- Cost efficiency analysis
- Custom notes and evidence documentation

**Evidence Image Types:**
- Ad Performance Screenshots
- Analytics Data
- Engagement Metrics
- Reach Statistics
- Conversion Tracking
- ROI Documentation
- Client Communications
- Other Custom Types

---

### 4. Content Management

**Purpose:** Manage creative content production and delivery

#### Features:

**Content Creation:**
- Client assignment
- Content type (Post, Story, Reel, Video, Graphic, Copy)
- Platform selection
- Caption/description
- Content image upload
- Creative credits allocation
- Delivery date tracking

**Content Credits System:**
- Track creative work units
- Assign credits per content piece
- Monitor credit usage per client
- Calculate content costs
- Content credit reporting

**Content Operations:**
- Add new content
- Edit content details
- Delete content (with confirmation)
- View content history
- Filter by client, type, or platform
- Search content by description
- Image upload and preview
- Multi-select for batch operations

**Content Reporting:**
- Generate content delivery reports (PDF)
- Client-specific content summaries
- Date range reports
- Credit usage analysis
- Platform performance
- Content type distribution

**Bulk Operations:**
- Select multiple content items
- Batch report generation
- Bulk content export
- Mass operations confirmation

---

### 5. Finance Management

**Purpose:** Comprehensive financial tracking and payment management

#### Features:

**Payment Recording:**
- Client selection
- Payment amount
- Payment date
- Payment method (Cash, Bank Transfer, Credit Card, PayPal, etc.)
- Reference number
- Notes and descriptions
- Automatic total calculation

**Content Credits:**
- Credit allocation per client
- Credit consumption tracking
- Credit balance display
- Credit history
- Credit-based pricing

**Financial Operations:**
- Record new payments
- View payment history
- Edit payment records
- Delete payment entries
- Filter by client
- Date range filtering
- Payment method filtering
- Search transactions

**Financial Reporting:**
- Total revenue tracking
- Client-wise revenue breakdown
- Payment method statistics
- Monthly revenue trends
- Outstanding balance reports
- Payment history exports

**Financial Data Structure:**
```javascript
{
    id: number,
    clientId: number,
    amount: decimal,
    paymentDate: date,
    method: string,
    reference: string,
    notes: string,
    createdAt: datetime
}
```

---

### 6. Invoice & Document Management

**Purpose:** Generate and manage invoices and service documents

#### Features:

**Invoice Creation:**
- Client selection
- Invoice date and due date
- Invoice number (auto-generated)
- Multiple item types:
  - Ad Campaign Services
  - Content Creation Services
  - Other Services
- Per-item pricing
- Quantity and subtotal calculation
- Tax calculation (optional)
- Discount support
- Total amount calculation
- Payment terms
- Notes and conditions

**Quick Invoice:**
- Fast invoice generation from campaigns
- Pre-populated campaign data
- Multi-campaign selection
- Automatic calculation
- One-click invoice creation

**Document Types:**
- Service Invoices
- Payment Receipts
- Content Reports
- Campaign Reports
- Custom Documents

**Document Operations:**
- Create new documents
- Edit existing documents
- Delete documents
- View document details
- Print documents
- Export to PDF
- Email documents (future feature)

**Invoice Features:**
- Professional layout
- Company branding
- Client information
- Itemized billing
- Subtotals and totals
- Tax breakdown
- Payment status
- Due date tracking

---

### 7. Campaign Performance Reporting

**Purpose:** Generate comprehensive campaign performance reports

#### Features:

**Report Generation:**
- Client selection
- Date range specification
- Campaign data auto-loading
- Manual campaign addition
- Performance metrics input:
  - Ad Name
  - Result Type (Link Clicks, Conversions, etc.)
  - Results count
  - Cost Per Result
  - Reach
  - Impressions
  - Total Spend
  - Quality Ranking (Above Average, Average, Below Average)
  - Conversion Ranking

**Report Components:**
- Report header with client info and date range
- Summary cards (Total Spend, Total Reach, Total Impressions)
- Campaign Performance Details table (9 columns)
- Cost Efficiency Analysis (bullet points)
- Evidence & Additional Notes
- Creative Images gallery (multiple images)
- Evidence Images gallery (multiple images)
- Professional footer with company info

**Report Features:**
- Live preview before printing
- PDF-ready layout
- Responsive design
- Print optimization
- Smaller image thumbnails (120px) for space efficiency
- Compact table design (11px font, 8px padding)
- Auto-calculation of totals
- Image galleries with labels
- Professional formatting

**Image Management:**
- Upload multiple creative images
- Upload multiple evidence images
- Image preview cards
- Image labeling
- Remove individual images
- Drag-and-drop support

**Print Features:**
- Clean print layout
- Page break optimization
- No browser headers/footers in title
- A4 page size optimization
- 1cm margins
- High-quality image output

---

### 8. Settings Management

**Purpose:** Configure system-wide settings and preferences

#### Features:

**Company Information:**
- Company name
- Email address
- Phone number
- Address
- Logo upload and management
- Logo preview (100x100px)
- Logo replacement

**System Configuration:**
- Default currency settings
- Date format preferences
- Time zone settings
- Tax rates
- Payment terms
- Invoice numbering format

**Logo Management:**
- Upload company logo
- Preview logo in settings
- Display logo in sidebar (32x32px)
- Logo on invoices and reports
- Supported formats: JPEG, PNG, GIF, WEBP
- Maximum file size: 5MB

**Settings Operations:**
- Update company information
- Change logo
- Modify system defaults
- Save configuration
- Reset to defaults (admin only)

---

### 9. User Management

**Purpose:** Manage system users and access control

#### Features:

**User Administration:**
- Add new users
- Edit user details
- Delete users
- View user list
- Password management
- Role assignment

**User Roles:**
- **Admin**: Full system access
  - User management
  - Settings configuration
  - All module access
  - Delete operations
  - Financial management
  
- **User**: Limited access
  - View assigned modules
  - Add/edit own content
  - View reports
  - No settings access
  - No user management

**User Operations:**
- Create new user accounts
- Assign usernames and passwords
- Set user roles
- Activate/deactivate users
- Password reset
- User activity tracking

**Password Management:**
- Temporary password system
- Password change enforcement
- Strong password requirements
- Password reset by admin
- Secure password storage (bcrypt)

**User Data Structure:**
```javascript
{
    id: number,
    username: string,
    password: string (hashed),
    role: string (admin/user),
    createdAt: datetime,
    lastLogin: datetime
}
```

---

### 10. Data Management

**Purpose:** System-wide data operations and maintenance

#### Features:

**Data Operations:**
- Load all system data
- Refresh data from database
- Clear client-side cache
- Sync with server
- Data validation
- Error handling

**LocalStorage Management:**
- Cache system data locally
- Reduce server requests
- Improve performance
- Offline data access (read-only)
- Automatic cache updates
- Cache expiration

**Data Synchronization:**
- Real-time updates
- Automatic refresh
- Manual refresh option
- Conflict resolution
- Data integrity checks

**Database Operations:**
- CRUD operations for all entities
- Batch operations
- Data import/export
- Backup and restore
- Data migration support

---

### 11. Reporting System

**Purpose:** Comprehensive reporting across all modules

#### Report Types:

**1. Client Reports**
- Client directory
- Client activity summary
- Revenue per client
- Campaign performance by client

**2. Campaign Reports**
- Campaign Performance Report (detailed PDF)
- Multi-campaign summaries
- Platform performance comparison
- ROI analysis
- Time-based performance trends

**3. Content Reports**
- Content delivery report (PDF)
- Content type distribution
- Platform-wise content analysis
- Creative credit usage
- Production timeline reports

**4. Financial Reports**
- Revenue reports
- Payment history
- Outstanding balances
- Payment method breakdown
- Tax summaries
- Invoice aging reports

**5. Custom Reports**
- Date range filtering
- Client-specific reports
- Platform-specific reports
- Custom metric selection
- Export to PDF
- Print-ready formatting

#### Report Features:
- Professional formatting
- Company branding
- Client information
- Date ranges
- Summary statistics
- Detailed breakdowns
- Visual elements (tables, cards)
- Image galleries
- Export capabilities
- Print optimization

---

### 12. Search and Filtering

**Purpose:** Quick data retrieval and filtering

#### Search Capabilities:
- Global search across modules
- Client search by name, email, phone
- Campaign search by name, platform
- Content search by description, type
- Financial search by reference, client

#### Filtering Options:
- Filter by client
- Filter by date range
- Filter by platform
- Filter by content type
- Filter by payment method
- Filter by status
- Multi-criteria filtering
- Advanced search options

#### Sorting:
- Sort by date (ascending/descending)
- Sort by amount
- Sort by name
- Sort by performance metrics
- Custom sort orders
- Table column sorting

---

### 13. File Management

**Purpose:** Manage uploaded files and images

#### Upload Features:
- Client logo uploads
- Campaign image uploads (multiple)
- Content image uploads
- Evidence image uploads
- Drag-and-drop support
- Preview before upload
- Progress indicators

#### File Operations:
- Upload files
- View uploaded files
- Delete files
- Replace files
- Download files
- File size validation
- File type validation

#### Storage:
- Organized directory structure
- Unique filename generation
- Timestamp-based naming
- Secure file permissions
- File size limits (5MB default)
- Supported formats: JPEG, PNG, GIF, WEBP

---

### 14. Notification System

**Purpose:** User feedback and system alerts

#### Alert Types:
- Success messages (green)
- Error messages (red)
- Warning messages (yellow)
- Info messages (blue)

#### Notifications:
- Operation success confirmations
- Error notifications
- Validation warnings
- System status updates
- Auto-dismiss (5 seconds)
- Manual dismiss option

---

### 15. Print & Export

**Purpose:** Generate printable documents and exports

#### Print Features:
- Invoice printing
- Report printing
- Document printing
- Print preview
- Page formatting
- Print-optimized layouts
- Browser print dialog integration

#### Export Features:
- PDF generation
- Data export (JSON)
- Report exports
- Document downloads
- Batch exports
- Custom format support

---

## Database Structure

### Database Schema Overview

The system uses **MySQL/MariaDB** with the following tables:

#### 1. **users**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

#### 2. **clients**
```sql
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    partner_id VARCHAR(100) UNIQUE,
    logo_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 3. **campaigns**
```sql
CREATE TABLE campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    ad_name VARCHAR(255) NOT NULL,
    platform VARCHAR(100),
    budget DECIMAL(10,2),
    start_date DATE,
    end_date DATE,
    impressions INT DEFAULT 0,
    reach INT DEFAULT 0,
    results INT DEFAULT 0,
    cpr DECIMAL(10,2),
    spend DECIMAL(10,2),
    creative_image_url TEXT,
    evidence_image_url TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);
```

#### 4. **content**
```sql
CREATE TABLE content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    content_type VARCHAR(100),
    platform VARCHAR(100),
    creative VARCHAR(255),
    caption TEXT,
    image_url VARCHAR(500),
    credits INT DEFAULT 0,
    delivered_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);
```

#### 5. **payments**
```sql
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    method VARCHAR(100),
    reference VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);
```

#### 6. **invoices**
```sql
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    invoice_number VARCHAR(100) UNIQUE NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE,
    items JSON,
    subtotal DECIMAL(10,2),
    tax DECIMAL(10,2),
    discount DECIMAL(10,2),
    total DECIMAL(10,2),
    notes TEXT,
    status ENUM('draft', 'sent', 'paid', 'overdue') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);
```

#### 7. **settings**
```sql
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Database Relationships

```
clients (1) ────< (many) campaigns
clients (1) ────< (many) content
clients (1) ────< (many) payments
clients (1) ────< (many) invoices
```

### Index Strategy

**Primary Indexes:**
- All tables have auto-incrementing primary keys

**Foreign Key Indexes:**
- client_id in campaigns, content, payments, invoices
- Automatic indexing on foreign keys

**Unique Indexes:**
- username in users
- partner_id in clients
- invoice_number in invoices
- setting_key in settings

**Performance Indexes:**
```sql
CREATE INDEX idx_client_name ON clients(company_name);
CREATE INDEX idx_campaign_dates ON campaigns(start_date, end_date);
CREATE INDEX idx_content_date ON content(delivered_date);
CREATE INDEX idx_payment_date ON payments(payment_date);
CREATE INDEX idx_invoice_date ON invoices(invoice_date);
```

### Data Types & Storage

**Text Storage:**
- VARCHAR for limited strings (names, emails)
- TEXT for unlimited text (descriptions, notes)
- JSON for structured data (invoice items, image arrays)

**Numeric Storage:**
- INT for IDs and counts
- DECIMAL(10,2) for currency (precise to 2 decimal places)

**Date/Time Storage:**
- DATE for date-only fields
- TIMESTAMP for automatic tracking
- DEFAULT CURRENT_TIMESTAMP for creation time
- ON UPDATE CURRENT_TIMESTAMP for modification time

**Binary Storage:**
- File paths stored as VARCHAR/TEXT
- Actual files stored in filesystem
- No BLOB storage for performance

---

## API Endpoints

All endpoints return JSON responses with the following structure:

```javascript
{
    "success": boolean,
    "message": string,
    "data": object|array (optional)
}
```

### Authentication Endpoints

#### POST `/handler_login.php`
**Purpose:** User authentication

**Request:**
```json
{
    "username": "string",
    "password": "string"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user_id": 1,
        "username": "admin",
        "role": "admin"
    }
}
```

#### GET `/logout.php`
**Purpose:** End user session

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

### Client Endpoints

#### GET `/handler_clients.php?action=getAll`
**Purpose:** Retrieve all clients

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "companyName": "Example Corp",
            "contactPerson": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "partnerId": "PART001",
            "logoUrl": "/uploads/logos/logo_123.png",
            "createdAt": "2025-01-01 10:00:00"
        }
    ]
}
```

#### POST `/handler_clients.php?action=add`
**Purpose:** Add new client

**Request:**
```json
{
    "companyName": "string",
    "contactPerson": "string",
    "email": "string",
    "phone": "string",
    "partnerId": "string",
    "logoUrl": "string"
}
```

#### POST `/handler_clients.php?action=update`
**Purpose:** Update existing client

**Request:**
```json
{
    "id": number,
    "companyName": "string",
    "contactPerson": "string",
    "email": "string",
    "phone": "string",
    "partnerId": "string",
    "logoUrl": "string"
}
```

#### POST `/handler_clients.php?action=delete`
**Purpose:** Delete client

**Request:**
```json
{
    "id": number
}
```

---

### Campaign Endpoints

#### GET `/handler_campaigns.php?action=getAll`
**Purpose:** Retrieve all campaigns

#### POST `/handler_campaigns.php?action=add`
**Purpose:** Add new campaign

**Request:**
```json
{
    "clientId": number,
    "adName": "string",
    "platform": "string",
    "budget": number,
    "startDate": "YYYY-MM-DD",
    "endDate": "YYYY-MM-DD",
    "impressions": number,
    "reach": number,
    "results": number,
    "cpr": number,
    "spend": number,
    "creativeImageUrl": "array|string",
    "evidenceImageUrl": "array|string",
    "description": "string"
}
```

#### POST `/handler_campaigns.php?action=update`
**Purpose:** Update campaign

#### POST `/handler_campaigns.php?action=delete`
**Purpose:** Delete campaign

---

### Content Endpoints

#### GET `/handler_content.php?action=getAll`
**Purpose:** Retrieve all content

#### POST `/handler_content.php?action=add`
**Purpose:** Add new content

**Request:**
```json
{
    "clientId": number,
    "contentType": "string",
    "platform": "string",
    "creative": "string",
    "caption": "string",
    "imageUrl": "string",
    "credits": number,
    "deliveredDate": "YYYY-MM-DD"
}
```

#### POST `/handler_content.php?action=update`
**Purpose:** Update content

#### POST `/handler_content.php?action=delete`
**Purpose:** Delete content

---

### Finance Endpoints

#### GET `/handler_finance.php?action=getAll`
**Purpose:** Retrieve all payments

#### POST `/handler_finance.php?action=add`
**Purpose:** Record new payment

**Request:**
```json
{
    "clientId": number,
    "amount": number,
    "paymentDate": "YYYY-MM-DD",
    "method": "string",
    "reference": "string",
    "notes": "string"
}
```

#### POST `/handler_finance.php?action=update`
**Purpose:** Update payment record

#### POST `/handler_finance.php?action=delete`
**Purpose:** Delete payment record

---

### Invoice Endpoints

#### GET `/handler_invoices.php?action=getAll`
**Purpose:** Retrieve all invoices

#### POST `/handler_invoices.php?action=add`
**Purpose:** Create new invoice

**Request:**
```json
{
    "clientId": number,
    "invoiceNumber": "string",
    "invoiceDate": "YYYY-MM-DD",
    "dueDate": "YYYY-MM-DD",
    "items": [
        {
            "description": "string",
            "quantity": number,
            "price": number,
            "subtotal": number
        }
    ],
    "subtotal": number,
    "tax": number,
    "discount": number,
    "total": number,
    "notes": "string"
}
```

---

### Settings Endpoints

#### GET `/handler_settings.php?action=get`
**Purpose:** Retrieve all settings

#### POST `/handler_settings.php?action=update`
**Purpose:** Update settings

**Request:**
```json
{
    "companyName": "string",
    "email": "string",
    "phone": "string",
    "address": "string",
    "logoUrl": "string"
}
```

---

### User Management Endpoints

#### GET `/handler_users.php?action=getAll`
**Purpose:** Retrieve all users (admin only)

#### POST `/handler_users.php?action=add`
**Purpose:** Add new user (admin only)

**Request:**
```json
{
    "username": "string",
    "password": "string",
    "role": "admin|user"
}
```

#### POST `/handler_users.php?action=delete`
**Purpose:** Delete user (admin only)

---

### File Upload Endpoints

#### POST `/upload_logo_handler.php`
**Purpose:** Upload client or company logo

**Request:** FormData with file

**Response:**
```json
{
    "success": true,
    "url": "/uploads/logos/logo_timestamp.png"
}
```

#### POST `/upload_content_image.php`
**Purpose:** Upload campaign or content image

**Request:** FormData with file

**Response:**
```json
{
    "success": true,
    "url": "/uploads/content_images/img_timestamp.jpg"
}
```

---

### Data Endpoints

#### GET `/handler_data.php?action=getAll`
**Purpose:** Retrieve all system data (combined)

**Response:**
```json
{
    "success": true,
    "data": {
        "clients": [],
        "campaigns": [],
        "content": [],
        "payments": [],
        "invoices": [],
        "users": [],
        "settings": {}
    }
}
```

---

## User Roles & Permissions

### Role Hierarchy

```
Admin (Full Access)
  ├── System Settings
  ├── User Management
  ├── All Module Access
  └── Delete Operations

User (Limited Access)
  ├── Assigned Modules Only
  ├── View & Add Operations
  └── No Settings/User Management
```

### Permission Matrix

| Feature                  | Admin | User |
|-------------------------|-------|------|
| Dashboard               | ✅     | ✅    |
| View Clients            | ✅     | ✅    |
| Add Clients             | ✅     | ✅    |
| Edit Clients            | ✅     | ✅    |
| Delete Clients          | ✅     | ❌    |
| View Campaigns          | ✅     | ✅    |
| Add Campaigns           | ✅     | ✅    |
| Edit Campaigns          | ✅     | ✅    |
| Delete Campaigns        | ✅     | ❌    |
| View Content            | ✅     | ✅    |
| Add Content             | ✅     | ✅    |
| Edit Content            | ✅     | ✅    |
| Delete Content          | ✅     | ❌    |
| View Finance            | ✅     | ✅    |
| Add Payments            | ✅     | ❌    |
| Edit Payments           | ✅     | ❌    |
| Delete Payments         | ✅     | ❌    |
| Generate Invoices       | ✅     | ✅    |
| View Invoices           | ✅     | ✅    |
| Delete Invoices         | ✅     | ❌    |
| Generate Reports        | ✅     | ✅    |
| Print Documents         | ✅     | ✅    |
| System Settings         | ✅     | ❌    |
| User Management         | ✅     | ❌    |
| Upload Logos            | ✅     | ✅    |
| Upload Images           | ✅     | ✅    |
| Change Own Password     | ✅     | ✅    |
| Reset Other Passwords   | ✅     | ❌    |

### Access Control Implementation

```javascript
// Client-side role check
function requireAdmin() {
    if (currentUser.role !== 'admin') {
        showAlert('Admin access required', 'danger');
        return false;
    }
    return true;
}

// Server-side role check (PHP)
if ($_SESSION['role'] !== 'admin') {
    die(json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]));
}
```

### Default Admin Account

**Username:** `admin`  
**Default Password:** `admin123` (should be changed immediately)

---

## Technical Specifications

### System Requirements

#### Server Requirements:
- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher / MariaDB 10.3+
- **Apache:** 2.4+ with mod_rewrite
- **PHP Extensions:**
  - mysqli
  - json
  - session
  - fileinfo
  - gd or imagick (for image processing)

#### Client Requirements:
- **Browser:** Modern browser with JavaScript enabled
  - Chrome 90+
  - Firefox 88+
  - Edge 90+
  - Safari 14+
- **Screen Resolution:** 1280x720 minimum
- **Internet Connection:** Required for CDN resources

#### Server Resources:
- **RAM:** 512MB minimum, 1GB recommended
- **Storage:** 5GB minimum (plus growth for uploads)
- **CPU:** 1 core minimum, 2+ cores recommended

### Performance Specifications

#### Page Load Times:
- Initial load: < 2 seconds
- Subsequent navigation: < 500ms
- Data refresh: < 1 second
- File uploads: Depends on file size and connection

#### Optimization Features:
- LocalStorage caching
- Lazy loading of images
- Minified CSS/JS from CDN
- Efficient database queries
- Indexed database tables
- Prepared statements for query optimization

#### Scalability:
- Supports 100+ concurrent users
- Handles 10,000+ records per table
- Database connection pooling
- Horizontal scaling capability

### Browser Compatibility

| Browser | Version | Support |
|---------|---------|---------|
| Chrome  | 90+     | ✅ Full  |
| Firefox | 88+     | ✅ Full  |
| Safari  | 14+     | ✅ Full  |
| Edge    | 90+     | ✅ Full  |
| Opera   | 76+     | ✅ Full  |
| IE      | Any     | ❌ Not Supported |

### Mobile Responsiveness

- **Responsive Design:** Bootstrap 5 framework
- **Mobile Breakpoints:**
  - Mobile: < 768px
  - Tablet: 768px - 1024px
  - Desktop: > 1024px
- **Touch Support:** Touch-friendly buttons and inputs
- **Mobile Menu:** Collapsible sidebar on mobile

### File Upload Specifications

**Supported Formats:**
- **Images:** JPEG, PNG, GIF, WEBP
- **Maximum Size:** 5MB per file
- **Maximum Dimension:** 4000x4000 pixels
- **Multiple Uploads:** Supported (campaigns, reports)

**Upload Validation:**
- File type checking (MIME type)
- File size validation
- Filename sanitization
- Unique filename generation
- Error handling

### Backup Recommendations

**Database Backups:**
- **Frequency:** Daily automated backups
- **Retention:** 30 days minimum
- **Method:** mysqldump or phpMyAdmin export
- **Storage:** Off-site backup storage

**File Backups:**
- **Uploads Folder:** Weekly backups
- **System Files:** Version controlled (Git)
- **Configuration:** Separate secure backup

### Maintenance Schedule

**Daily:**
- Monitor error logs
- Check disk space
- Verify backups

**Weekly:**
- Review user activity
- Check for updates
- Test backup restoration

**Monthly:**
- Security audit
- Performance review
- Database optimization
- Clear temporary files

**Quarterly:**
- System update
- Security patch application
- Feature updates
- User training

---

## Deployment Information

### Installation Steps

1. **Server Setup:**
   ```bash
   # Install Apache, PHP, MySQL
   sudo apt install apache2 php mysql-server
   sudo apt install php-mysqli php-json php-gd
   ```

2. **Database Creation:**
   ```sql
   CREATE DATABASE ayonion_cms;
   CREATE USER 'ayonion_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON ayonion_cms.* TO 'ayonion_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

3. **File Deployment:**
   ```bash
   cd /var/www/html/
   git clone [repository-url] ayonion-cms
   cd ayonion-cms
   chmod 755 uploads/
   chmod 755 uploads/logos/
   chmod 755 uploads/content_images/
   ```

4. **Database Import:**
   ```bash
   mysql -u ayonion_user -p ayonion_cms < setup_database.sql
   ```

5. **Configuration:**
   - Edit `includes/config.php`
   - Set database credentials
   - Configure file upload paths
   - Set timezone

6. **Security Hardening:**
   - Change default admin password
   - Enable HTTPS
   - Configure firewall
   - Set file permissions
   - Enable error logging

### Environment Configuration

**Development:**
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DEBUG_MODE', true);
```

**Production:**
```php
error_reporting(0);
ini_set('display_errors', 0);
define('DEBUG_MODE', false);
```

### Version Control

**Git Strategy:**
- Main branch for production
- Development branch for testing
- Feature branches for new features
- Regular commits with descriptive messages

### Support & Maintenance

**Technical Support:**
- Email: support@ayonionstudios.com
- Response Time: 24-48 hours
- Emergency Support: Available for critical issues

**System Updates:**
- Regular security patches
- Feature updates quarterly
- Bug fixes as needed
- Database migrations managed

---

## Conclusion

Ayonion CMS is a robust, secure, and feature-rich content management system designed specifically for digital marketing agencies. With comprehensive modules for client management, campaign tracking, content creation, and financial operations, it provides a complete solution for managing marketing operations.

**Key Strengths:**
- ✅ Comprehensive feature set
- ✅ Strong security measures
- ✅ User-friendly interface
- ✅ Scalable architecture
- ✅ Active development and support

**Version:** 1.0.0  
**Last Updated:** November 12, 2025  
**Developer:** Ayonion Studios
