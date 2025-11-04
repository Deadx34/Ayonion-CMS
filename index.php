<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayonion Studios - Management System (Final)</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Lato', 'Segoe UI', sans-serif; background: var(--light); }
        .sidebar { background: var(--dark); min-height: 100vh; position: fixed; left: 0; top: 0; width: 260px; z-index: 1000; overflow-y: auto; transition: transform 0.3s ease; }
        .sidebar-header { padding: 30px 20px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
        .sidebar-header h3 { font-size: 1.4rem; font-weight: 700; margin: 0; }
        .sidebar-header p { margin: 5px 0 0 0; opacity: 0.9; font-size: 0.85rem; }
        .nav-item { margin: 5px 15px; }
        .nav-link { color: #94a3b8; padding: 12px 20px; border-radius: 10px; transition: all 0.3s; display: flex; align-items: center; gap: 12px; cursor: pointer; text-decoration: none; }
        .nav-link:hover { background: rgba(255,255,255,0.1); color: white; transform: translateX(5px); }
        .nav-link.active { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; }
        .nav-link i { width: 20px; text-align: center; }
        .content-area { margin-left: 260px; padding: 30px; min-height: 100vh; transition: margin-left 0.3s ease; }
        /* Adjust content area when warning banner is shown */
        body:has(#tempPasswordWarning[style*="display: block"]) .content-area { padding-top: 60px; }
        .top-bar { background: rgba(255,255,255,0.9); padding: 16px 24px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; position: sticky; top: 0; z-index: 900; backdrop-filter: saturate(180%) blur(8px); border: 1px solid rgba(99,102,241,0.08); }
        .page-title { font-size: 1.6rem; font-weight: 700; color: var(--dark); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-info { display: flex; align-items: center; gap: 8px; flex-wrap: nowrap; }
        .user-chip { display: inline-flex; align-items: center; gap: 8px; background: #f0f1ff; color: #4f46e5; padding: 6px 10px; border-radius: 999px; font-weight: 600; }
        .user-chip i { color: #4f46e5; }
        .card { border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .card-header { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; border-radius: 15px 15px 0 0 !important; padding: 20px 25px; font-weight: 600; }
        .stat-card { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 25px; border-radius: 15px; margin-bottom: 20px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .stat-card h3 { font-size: 2rem; font-weight: 700; margin: 10px 0; }
        .stat-card p { margin: 0; opacity: 0.9; }
        .table { margin: 0; }
        .table thead { background: var(--light); }
        .table-hover > tbody > tr { transition: all 0.2s; }
        .table-hover > tbody > tr:hover { --bs-table-hover-bg: #f0f1ff; cursor: pointer; transform: scale(1.01); }
        .table th { border: none; padding: 15px; font-weight: 600; color: var(--dark); }
        .table td { padding: 15px; vertical-align: middle; }
        .sortable { cursor: pointer; user-select: none; position: relative; padding-right: 25px !important; }
        .sortable:hover { background: #e7e9fd; }
        .sortable::after { content: '⇅'; position: absolute; right: 8px; opacity: 0.3; font-size: 12px; }
        .sortable.asc::after { content: '↑'; opacity: 1; color: var(--primary); }
        .sortable.desc::after { content: '↓'; opacity: 1; color: var(--primary); }
        .badge { padding: 6px 12px; border-radius: 6px; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; transition: all 0.3s; cursor: pointer; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3); }
        .modal-content { border: none; border-radius: 15px; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; border-radius: 15px 15px 0 0; }
        .form-label { font-weight: 600; color: var(--dark); margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 8px; border: 2px solid #e2e8f0; padding: 10px 15px; transition: all 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15); }
        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
        .form-check-input:focus { border-color: var(--primary); box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15); }
        .form-check-label { font-weight: 500; color: var(--dark); }
        .section { display: none; }
        .section.active { display: block; }
        .action-btn { margin: 0 3px; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; transition: all 0.3s; }
        .action-btn:hover { transform: translateY(-2px); }
        .document-preview { 
            background: white; 
            padding: 40px; 
            border-radius: 10px; 
            max-width: 800px; 
            margin: 20px auto; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
        }
        .document-preview * {
            pointer-events: none;
            user-select: text;
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
        }
        .document-preview input,
        .document-preview textarea,
        .document-preview select {
            pointer-events: none;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
            resize: none !important;
        }
        .document-preview {
            position: relative;
        }
        .document-preview::before {
            content: "📄 Document View - Read Only";
            position: absolute;
            top: -10px;
            right: 10px;
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            z-index: 10;
        }
        .logo-preview { width: 60px; height: 60px; object-fit: contain; border: 2px solid #e2e8f0; border-radius: 8px; padding: 5px; cursor: pointer; transition: all 0.3s; }
        .logo-preview:hover { transform: scale(1.1); border-color: var(--primary); }
        .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; opacity: 0.5; }
        .empty-state h4 { margin-bottom: 10px; }
        .evidence-preview { display: inline-block; width: 80px; height: 80px; margin: 5px; border-radius: 8px; overflow: hidden; border: 2px solid #e2e8f0; cursor: pointer; transition: all 0.3s; }
        .evidence-preview:hover { transform: scale(1.1); border-color: var(--primary); }
        .evidence-preview img { width: 100%; height: 100%; object-fit: cover; }
        .clickable { cursor: pointer; transition: all 0.2s; }
        .clickable:hover { color: var(--primary); font-weight: 600; }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); width: 260px; position: fixed; }
            body.sidebar-open .sidebar { transform: translateX(0); }
            .content-area { margin-left: 0; padding: 16px; }
            .top-bar { padding: 12px 16px; border-radius: 12px; gap: 10px; }
            .page-title { font-size: 1.3rem; max-width: 70vw; }
            .stat-card { padding: 18px; }
            .document-preview { padding: 20px; }
            .logo-preview { width: 48px; height: 48px; }
            .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        }
        @media (max-width: 575.98px) {
            .btn { width: 100%; margin-bottom: 8px; }
            .card-header .btn { width: auto; }
            .top-bar { flex-direction: column; align-items: stretch; gap: 10px; }
            .page-title { width: 100%; text-align: left; }
            .user-info { justify-content: space-between; }
            .user-chip { width: 100%; justify-content: space-between; }
        }
        /* Make all modals fullscreen on small screens */
        @media (max-width: 575.98px) {
            .modal-dialog { width: 100% !important; max-width: 100% !important; height: 100%; margin: 0; }
            .modal-content { height: 100%; border-radius: 0; }
        }
        .logout-btn { position: absolute; bottom: 20px; left: 20px; right: 20px; }
        
        /* 5-column layout for client detail buttons */
        .col-md-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
        @media (max-width: 768px) {
            .col-md-2-4 {
                width: 100%;
            }
        }
        
        /* Print Styles for Reports */
        @media print {
            body { background: white !important; }
            #mainApp, .top-bar, .sidebar, .modal-footer, .btn-close { display: none !important; }
            .modal-dialog { width: 100% !important; max-width: 100% !important; margin: 0 !important; }
            .modal-content, .modal-body { box-shadow: none !important; border: none !important; padding: 0 !important; }
            .document-preview { box-shadow: none; max-width: 100%; margin: 0; padding: 0; }
            .document-preview table thead tr { background-color: #f0f1ff !important; -webkit-print-color-adjust: exact; color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div id="loginPage" class="login-container" style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="login-card" style="background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 50px; max-width: 450px; width: 100%;">
            <div style="text-align: center; margin-bottom: 40px;">
                <i class="fas fa-palette fa-3x mb-3" style="color: #6366f1;"></i>
                <h1 style="color: #6366f1; font-weight: 700; font-size: 2rem; margin-bottom: 10px;">Ayonion Studios</h1>
                <p style="color: #64748b; font-size: 0.95rem;">Management System</p>
            </div>
            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label">User Role</label>
                    <select class="form-select" id="userRole" required>
                        <option value="" disabled selected hidden>Select Your Role</option>
                        <option value="admin">Admin</option>
                        <option value="marketer">Digital Marketer</option>
                        <option value="finance">Finance Manager</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" placeholder="Enter username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" style="padding: 12px;">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
        </div>
    </div>

    <div id="mainApp" style="display: none;">
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center mb-2">
                    <img id="sidebarLogo" src="" alt="Logo" style="width: 32px; height: 32px; object-fit: contain; margin-right: 10px; display: none;">
                    <h3 id="sidebarCompanyName">Ayonion</h3>
                </div>
                <p>Management System</p>
            </div>
            <ul class="nav flex-column mt-4" id="navMenu"></ul>
            <div class="logout-btn">
                <button class="btn btn-danger w-100" onclick="logout()">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </div>
        </nav>

        <main class="content-area">
            <div class="top-bar">
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" title="Menu"><i class="fas fa-bars"></i></button>
                    <h1 class="page-title" id="pageTitle">Dashboard</h1>
                </div>
                <div class="user-info">
                    <span class="user-chip"><i class="fas fa-user"></i><span id="currentUser"></span></span>
                    <span class="badge bg-primary"><span id="currentRole"></span></span>
                </div>
            </div>

            <div id="dashboard" class="section active">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%)">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3 id="totalClients">0</h3>
                            <p>Total Clients</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%)">
                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                            <h3 id="totalContentUsed">0</h3>
                            <p>Content Credits Used</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="settings" class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-gear me-2"></i>Settings</span>
                    </div>
                    <div class="card-body">
                        <form id="settingsForm" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="settingsCompanyName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company Logo</label>
                                <input type="file" class="form-control" id="settingsLogoFile" accept="image/*" onchange="handleLogoUpload(this)">
                                <input type="hidden" id="settingsLogoUrl">
                                <div id="logoPreview" class="mt-2" style="display: none;">
                                    <img id="logoPreviewImg" src="" alt="Logo Preview" style="max-width: 100px; max-height: 100px; object-fit: contain; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeLogo()">Remove</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="settingsEmail" placeholder="info@company.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" id="settingsPhone" placeholder="+1 555 0123">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" id="settingsAddress" rows="3"></textarea>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Auto Carry Forward System Card -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-sync-alt me-2"></i>Auto Carry Forward System</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>How it works:</h6>
                            <ul class="mb-0">
                                <li>Each month starts with <strong>40 credits</strong> as package credits</li>
                                <li>Any unused credits automatically carry forward to the next month</li>
                                <li>Example: Used 30 credits → 10 carry forward → Next month starts with 50 total credits (40 + 10)</li>
                                <li>System runs automatically when client's renewal date is reached</li>
                            </ul>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-primary">40</h5>
                                        <p class="mb-0">Default Monthly Credits</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h5 class="text-success">Auto</h5>
                                        <p class="mb-0">Unused Credits Carry Forward</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button class="btn btn-primary" onclick="runAutoCarryForward()">
                                <i class="fas fa-play-circle me-2"></i>Run Auto Carry Forward Now
                            </button>
                            <button class="btn btn-outline-secondary ms-2" onclick="viewCarryForwardLog()">
                                <i class="fas fa-history me-2"></i>View Process Log
                            </button>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                <strong>Note:</strong> For production, set up a cron job to run this automatically:
                                <code class="ms-2">0 0 1 * * php handler_auto_carry_forward.php</code>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div id="clients" class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-users me-2"></i>Client Profiles</span>
                        <button class="btn btn-light btn-sm" id="addClientBtn" onclick="showAddClientModal()" style="display: none;">
                            <i class="fas fa-plus me-2"></i>Add Client
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="clientsTable">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th class="sortable" data-sort="partnerId" data-type="string">Partner ID</th>
                                        <th class="sortable" data-sort="companyName" data-type="string">Company Name</th>
                                        <th class="sortable" data-sort="renewalDate" data-type="date">Renewal Date</th>
                                        <th class="sortable" data-sort="availableCredits" data-type="number">Available Credits</th>
                                        <th class="sortable" data-sort="adBudget" data-type="number">Ad Budget</th>
                                        <th class="sortable" data-sort="platforms" data-type="string">Platforms</th>
                                        <th class="sortable" data-sort="industry" data-type="string">Industry</th>
                                        <th id="clientActionsHeader" style="display: none;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="clientsTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="content" class="section">
                <div class="card">
                    <div class="card-header"><span><i class="fas fa-file-alt me-2"></i>Content Credit Management</span></div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Select Client</label>
                                <select class="form-select" id="contentClientSelect" onchange="loadContentCredits()"></select>
                            </div>
                            <div class="col-md-8 text-end">
                                <button class="btn btn-warning mt-2" onclick="showManageCreditsModal()">
                                    <i class="fas fa-recycle me-2"></i>Month End Process
                                </button>
                                <button class="btn btn-primary mt-2" onclick="showAddContentModal()">
                                    <i class="fas fa-plus me-2"></i>Add Content
                                </button>
                            </div>
                        </div>
                        <div id="contentCreditsInfo" style="display:none;" class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Package Credits:</strong> <span id="packageCredits">0</span><br>
                                    <strong>Extra Credits:</strong> <span id="extraCredits">0</span><br>
                                    <strong>Carried Forward:</strong> <span id="carriedCredits">0</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Total Credits:</strong> <span id="totalCredits">0</span><br>
                                    <strong>Used:</strong> <span id="usedCredits">0</span><br>
                                    <strong>Available:</strong> <span id="availableCredits" class="fw-bold text-success fs-5">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" class="form-check-input" id="selectAllContents" onchange="toggleSelectAllContents()" title="Select All">
                                        </th>
                                        <th>Creative</th><th>Content Type</th><th>Credits</th>
                                        <th>Published Date</th><th>Preview</th><th>Link</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="contentTableBody"></tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-success me-2" onclick="generateContentReport()">
                                <i class="fas fa-file-pdf me-2"></i>Generate Report (All)
                            </button>
                            <button class="btn btn-primary" id="generateSelectedReportBtn" onclick="generateSelectedContentReport()" style="display: none;">
                                <i class="fas fa-file-pdf me-2"></i>Generate Report (<span id="selectedCount">0</span> Selected)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="campaigns" class="section">
                <div class="card">
                    <div class="card-header"><span><i class="fas fa-bullhorn me-2"></i>Ad Campaign Management</span></div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Select Client</label>
                                <select class="form-select" id="campaignClientSelect" onchange="loadCampaigns()"></select>
                            </div>
                            <div class="col-md-8 text-end">
                                <button class="btn btn-success me-2 mt-2" id="generateInvoiceBtn" onclick="showInvoiceModal()" style="display:none;">
                                    <i class="fas fa-file-invoice me-2"></i>Generate Report
                                </button>
                                <button class="btn btn-primary mt-2" onclick="showAddCampaignModal()">
                                    <i class="fas fa-plus me-2"></i>Add Campaign
                                </button>
                            </div>
                        </div>
                        <div id="campaignBudgetInfo" style="display:none;" class="alert alert-warning">
                            <strong>Total Ad Budget:</strong> Rs. <span id="totalAdBudget">0.00</span> | 
                            <strong>Spent:</strong> Rs. <span id="totalSpent">0.00</span> | 
                            <strong>Remaining:</strong> Rs. <span id="remainingBudget" class="fw-bold">0.00</span>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-hover" id="campaignsTable">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCampaigns" onchange="toggleAllCampaigns()">
                                        </th>
                                        <th class="sortable" data-sort="platform" data-type="string">Platform</th>
                                        <th class="sortable" data-sort="adName" data-type="string">Ad Name</th>
                                        <th class="sortable" data-sort="results" data-type="number">Results</th>
                                        <th class="sortable" data-sort="cpr" data-type="number">CPR</th>
                                        <th class="sortable" data-sort="reach" data-type="number">Reach</th>
                                        <th class="sortable" data-sort="spend" data-type="number">Spend</th>
                                        <th class="sortable" data-sort="date" data-type="date">Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="campaignsTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="finances" class="section">
                <div class="card">
                    <div class="card-header"><span><i class="fas fa-file-invoice me-2"></i>Financial Documents</span></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100 mb-2" onclick="showCreateDocumentModal('quotation')">
                                    <i class="fas fa-file-alt me-2"></i>Create Quotation
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success w-100 mb-2" onclick="showCreateDocumentModal('invoice')">
                                    <i class="fas fa-file-invoice me-2"></i>Create Invoice
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-warning w-100 mb-2" onclick="showCreateDocumentModal('receipt')">
                                    <i class="fas fa-receipt me-2"></i>Create Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><span><i class="fas fa-folder-open me-2"></i>Document Archive</span></div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" id="financeTabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#quotationsTab" role="tab">Quotations</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#invoicesTab" role="tab">Invoices</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#receiptsTab" role="tab">Receipts</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="quotationsTab" role="tabpanel">
                                <div class="table-responsive"><table class="table table-hover" id="quotationsTable"><thead><tr><th class="sortable" data-sort="number" data-type="string">Quote #</th><th class="sortable" data-sort="client" data-type="string">Client</th><th class="sortable" data-sort="type" data-type="string">Type</th><th class="sortable" data-sort="date" data-type="date">Date</th><th class="sortable" data-sort="amount" data-type="number">Amount</th><th>Actions</th></tr></thead><tbody id="quotationsTableBody"></tbody></table></div>
                            </div>
                            <div class="tab-pane fade" id="invoicesTab" role="tabpanel">
                                <div class="table-responsive"><table class="table table-hover" id="invoicesTable"><thead><tr><th class="sortable" data-sort="number" data-type="string">Invoice #</th><th class="sortable" data-sort="client" data-type="string">Client</th><th class="sortable" data-sort="type" data-type="string">Type</th><th class="sortable" data-sort="date" data-type="date">Date</th><th class="sortable" data-sort="amount" data-type="number">Amount</th><th>Actions</th></tr></thead><tbody id="invoicesTableBody"></tbody></table></div>
                            </div>
                            <div class="tab-pane fade" id="receiptsTab" role="tabpanel">
                                <div class="table-responsive"><table class="table table-hover" id="receiptsTable"><thead><tr><th class="sortable" data-sort="number" data-type="string">Receipt #</th><th class="sortable" data-sort="client" data-type="string">Client</th><th class="sortable" data-sort="type" data-type="string">Type</th><th class="sortable" data-sort="date" data-type="date">Date</th><th class="sortable" data-sort="amount" data-type="number">Amount</th><th>Actions</th></tr></thead><tbody id="receiptsTableBody"></tbody></table></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="profile" class="section">
                <div class="card">
                    <div class="card-header">
                        <span><i class="fas fa-user-circle me-2"></i>My Profile</span>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" id="profileUsername" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" id="profileRole" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="profileFullName" placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="profileEmail" placeholder="Enter your email">
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <span><i class="fas fa-key me-2"></i>Change Password</span>
                    </div>
                    <div class="card-body">
                        <form id="changePasswordForm" class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="currentPassword" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" required minlength="6">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" required minlength="6">
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-lock me-2"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div id="users" class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-cog me-2"></i>Managed Users</span>
                        <button class="btn btn-light btn-sm" onclick="showAddUserModal()">
                            <i class="fas fa-plus me-2"></i>Add User
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="usersTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="username" data-type="string">Username</th>
                                        <th class="sortable" data-sort="role" data-type="string">Role</th>
                                        <th class="sortable" data-sort="passwordStatus" data-type="string">Password Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="modal fade" id="clientDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-building me-2"></i>Client Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-2 text-center">
                            <img id="clientDetailLogo" class="logo-preview" style="width: 120px; height: 120px;" src="" alt="Logo">
                        </div>
                        <div class="col-md-10">
                            <h3 id="clientDetailName" class="mb-3"></h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-id-card text-primary me-2"></i><strong>Partner ID:</strong> <span id="clientDetailPartnerId"></span></p>
                                    <p class="mb-2"><i class="fas fa-industry text-primary me-2"></i><strong>Industry:</strong> <span id="clientDetailIndustry"></span></p>
                                    <p class="mb-2"><i class="fas fa-calendar text-primary me-2"></i><strong>Renewal Date:</strong> <span id="clientDetailRenewal"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-share-alt text-primary me-2"></i><strong>Managing Platforms:</strong> <span id="clientDetailPlatforms"></span></p>
                                    <p class="mb-2"><i class="fas fa-dollar-sign text-primary me-2"></i><strong>Total Ad Budget:</strong> Rs. <span id="clientDetailTotalAdBudget">0.00</span></p>
                                    <p class="mb-2"><i class="fas fa-dollar-sign text-primary me-2"></i><strong>Ad Budget Remaining:</strong> Rs. <span id="clientDetailAdBudget">0.00</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-credit-card me-2"></i>Content Credit Summary</h5>
                                <div class="row mt-3">
                                    <div class="col-md-2 text-center">
                                        <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                            <h4 class="text-primary mb-0" id="clientDetailPackage">0</h4>
                                            <small>Package Credits</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                            <h4 class="text-success mb-0" id="clientDetailExtra">0</h4>
                                            <small>Extra Credits</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                            <h4 class="text-warning mb-0" id="clientDetailCarried">0</h4>
                                            <small>Carried Forward</small>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                            <h4 class="text-danger mb-0" id="clientDetailUsed">0</h4>
                                            <small>Used Credits</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                            <h3 class="mb-0" id="clientDetailAvailable">0</h3>
                                            <small>Available Credits</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-2-4">
                            <button class="btn btn-secondary w-100 mb-2" onclick="showEditClientModal()">
                                <i class="fas fa-edit me-2"></i>Edit Client Info
                            </button>
                        </div>
                        <div class="col-md-2-4">
                            <button class="btn btn-warning w-100 mb-2" onclick="showEditCreditsModal()">
                                <i class="fas fa-coins me-2"></i>Edit Credits
                            </button>
                        </div>
                        <div class="col-md-2-4">
                            <button class="btn btn-primary w-100 mb-2" id="btnManageContent" onclick="manageClientContent()">
                                <i class="fas fa-file-alt me-2"></i>Manage Content
                            </button>
                        </div>
                        <div class="col-md-2-4">
                            <button class="btn btn-success w-100 mb-2" id="btnViewCampaigns" onclick="viewClientCampaigns()">
                                <i class="fas fa-bullhorn me-2"></i>View Campaigns
                            </button>
                        </div>
                        <div class="col-md-2-4">
                            <button class="btn btn-info w-100 mb-2" onclick="generateClientReport()">
                                <i class="fas fa-chart-bar me-2"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Credits Modal -->
    <div class="modal fade" id="editCreditsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Monthly Credits</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCreditsForm">
                        <div class="mb-3">
                            <label class="form-label">Package Credits (Monthly Base)</label>
                            <input type="number" class="form-control" id="editPackageCredits" min="0" value="40" required>
                            <small class="form-text text-muted">Base monthly allocation (default: 40 credits)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Extra Credits (One-time)</label>
                            <input type="number" class="form-control" id="editExtraCredits" min="0" value="0" required>
                            <small class="form-text text-muted">Additional credits for this month only</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="resetUsedCredits">
                                <label class="form-check-label" for="resetUsedCredits">
                                    Reset Used Credits to 0
                                </label>
                                <small class="form-text text-muted d-block">Check this to start a fresh cycle</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info" id="currentCreditsStatus">
                            <strong>Current Status:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Carried Forward: <span id="statusCarried">0</span></li>
                                <li>Used: <span id="statusUsed">0</span></li>
                                <li>Total: <span id="statusTotal">0</span></li>
                                <li>Available: <span id="statusAvailable">0</span></li>
                            </ul>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editCreditsForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Client Info Modal -->
    <div class="modal fade" id="editClientModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Client Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editClientForm">
                        <input type="hidden" id="editClientId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Partner ID</label>
                                <input type="text" class="form-control" id="editPartnerId" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company/Brand Name</label>
                                <input type="text" class="form-control" id="editCompanyName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monthly Renewal Date</label>
                                <input type="date" class="form-control" id="editRenewalDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Managing Platforms</label>
                                <input type="text" class="form-control" id="editManagingPlatforms" placeholder="e.g., Facebook, Instagram, Google">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Industry Category</label>
                                <select class="form-select" id="editIndustry" required>
                                    <option value="">Select Industry</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Food & Beverage">Food & Beverage</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Education">Education</option>
                                    <option value="Real Estate">Real Estate</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Ad Budget</label>
                                <input type="number" class="form-control" id="editTotalAdBudget" min="0" step="0.01" placeholder="0.00">
                                <small class="text-muted">Total advertising budget for this client</small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label"><i class="fas fa-image me-2"></i>Company/Brand Logo</label>
                                
                                <!-- Current Logo Display -->
                                <div id="editCurrentLogo" class="mb-3" style="display: none;">
                                    <div class="alert alert-light d-flex align-items-center">
                                        <img id="editCurrentLogoImg" src="" alt="Current Logo" class="img-thumbnail me-3" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                        <div>
                                            <strong>Current Logo</strong><br>
                                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeEditLogo()">
                                                <i class="fas fa-trash"></i> Remove Logo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Upload New Logo -->
                                <input type="file" class="form-control" id="editLogoUpload" accept="image/*" onchange="handleEditLogoUpload(this)">
                                <small class="text-muted">Upload new logo to replace existing (Max: 2MB, Recommended: Square format)</small>
                                
                                <!-- New Logo Preview -->
                                <div id="editLogoPreview" class="mt-2" style="display: none;">
                                    <div class="alert alert-success d-flex align-items-center">
                                        <img id="editLogoPreviewImg" src="" alt="New Logo Preview" class="img-thumbnail me-3" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                        <div>
                                            <strong>New Logo Preview</strong><br>
                                            <small class="text-success">This logo will replace the current one when you save</small><br>
                                            <button type="button" class="btn btn-sm btn-warning mt-2" onclick="cancelEditLogoUpload()">
                                                <i class="fas fa-undo"></i> Cancel Upload
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Client Information
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addClientModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addClientForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Partner ID</label>
                                <input type="text" class="form-control" id="partnerId" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company/Brand Name</label>
                                <input type="text" class="form-control" id="companyName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Monthly Renewal Date</label>
                                <input type="date" class="form-control" id="renewalDate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Package Content Credits</label>
                                <input type="number" class="form-control" id="newClientPackageCredits" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Managing Platforms</label>
                                <input type="text" class="form-control" id="managingPlatforms" placeholder="e.g., Facebook, Instagram, Google">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Industry Category</label>
                                <select class="form-select" id="industry" required>
                                    <option value="">Select Industry</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Food & Beverage">Food & Beverage</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Education">Education</option>
                                    <option value="Real Estate">Real Estate</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label"><i class="fas fa-image me-2"></i>Company/Brand Logo (Optional)</label>
                                <input type="file" class="form-control" id="clientLogoUpload" accept="image/*" onchange="handleClientLogoUpload(this)">
                                <small class="text-muted">Upload company logo (Max: 2MB, Recommended: Square format)</small>
                                <div id="clientLogoPreview" class="mt-2" style="display: none;">
                                    <img id="clientLogoPreviewImg" src="" alt="Logo Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeClientLogo()">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Client</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addContentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Content Credit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addContentForm">
                        <div class="mb-3">
                            <label class="form-label">Creative Name</label>
                            <input type="text" class="form-control" id="creativeName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content Type</label>
                            <select class="form-select" id="contentType" required>
                                <option value="">Select Type</option>
                                <option value="Graphical post">Graphical post</option>
                                <option value="Video">Video</option>
                                <option value="Reel">Reel</option>
                                <option value="Multiple creative content">Multiple creative content</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Credits Allocated</label>
                            <input type="number" class="form-control" id="creditsAllocated" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Published Date (Optional)</label>
                            <input type="date" class="form-control" id="publishedDate">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-link me-2"></i>Content URL (Optional)</label>
                            <input type="url" class="form-control" id="contentUrl" placeholder="https://example.com/content">
                            <small class="text-muted">Link to the published content (social media post, video, etc.)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-image me-2"></i>Content Image (Optional)</label>
                            <input type="file" class="form-control" id="contentImageUpload" accept="image/*" onchange="handleContentImageUpload(this)">
                            <small class="text-muted">Upload an image related to this content (Max: 5MB)</small>
                            <div id="contentImagePreview" class="mt-2" style="display: none;">
                                <img id="contentImagePreviewImg" src="" alt="Content Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeContentImage()">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Content</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewContentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Content Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary"><i class="fas fa-file-alt me-2"></i>Content Information</h6>
                                    <div class="mb-2">
                                        <strong>Creative Name:</strong><br>
                                        <span id="viewCreativeName" class="text-dark"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Content Type:</strong><br>
                                        <span id="viewContentType" class="text-dark"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Credits Allocated:</strong><br>
                                        <span id="viewCredits" class="badge bg-warning fs-6"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary"><i class="fas fa-calendar me-2"></i>Timeline</h6>
                                    <div class="mb-2">
                                        <strong>Status:</strong><br>
                                        <span id="viewStatus" class="badge"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Published Date:</strong><br>
                                        <span id="viewPublishedDate" class="text-dark"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary"><i class="fas fa-building me-2"></i>Client Information</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Client Name:</strong><br>
                                            <span id="viewClientName" class="text-dark"></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Partner ID:</strong><br>
                                            <span id="viewPartnerId" class="text-dark"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3" id="contentMediaSection" style="display: none;">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary"><i class="fas fa-images me-2"></i>Content Media</h6>
                                    <div id="contentImageSection" style="display: none;">
                                        <strong>Content Image:</strong><br>
                                        <img id="viewContentImage" src="" alt="Content Image" class="img-thumbnail mt-2" style="max-width: 300px; max-height: 300px;">
                                    </div>
                                    <div id="contentUrlSection" style="display: none;" class="mt-3">
                                        <strong>Content URL:</strong><br>
                                        <a id="viewContentUrl" href="" target="_blank" class="text-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>View Content
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="manageCreditsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Month End Credit Process</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Client:</strong> <span id="manageCreditsClientName"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Current Renewal Date:</strong> <span id="currentRenewalDate"></span></p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Current Credit Status</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Package Credits:</strong> <span id="managePackageCredits">0</span></p>
                                <p class="mb-1"><strong>Extra Credits:</strong> <span id="manageExtraCredits">0</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Carried Forward:</strong> <span id="manageCarriedCredits">0</span></p>
                                <p class="mb-1"><strong>Used Credits:</strong> <span id="manageUsedCredits">0</span></p>
                            </div>
                        </div>
                        <hr>
                        <p class="mb-0"><strong>Available Credits (Max Carry):</strong> <span class="fs-5 text-success fw-bold" id="manageCreditsAvailable">0</span></p>
                    </div>
                    
                    <form id="manageCreditsForm">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-forward me-2"></i>Credits to Carry Forward</label>
                            <input type="number" class="form-control" id="creditsToCarry" required min="0">
                            <small class="text-muted">Enter <strong>0</strong> to expire all remaining credits. Maximum: <span id="maxCreditsToCarry">0</span> credits</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-calendar-alt me-2"></i>New Renewal Date</label>
                            <input type="date" class="form-control" id="newRenewalDate" required>
                            <small class="text-muted">Set the renewal date for the next billing cycle</small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This process will reset used credits to 0 and update the renewal date. This action cannot be undone.
                        </div>
                        
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-recycle me-2"></i>Process Month End
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCampaignModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCampaignForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Platform</label>
                                <select class="form-select" id="campaignPlatform" required>
                                    <option value="">Select Platform</option>
                                    <option value="Meta Ads">Meta Ads (Facebook & Instagram)</option>
                                    <option value="Google Ads">Google Ads</option>
                                    <option value="LinkedIn Ads">LinkedIn Ads</option>
                                    <option value="TikTok Ads">TikTok Ads</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ad ID</label>
                                <input type="text" class="form-control" id="adId" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ad Name</label>
                                <input type="text" class="form-control" id="adName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Result Type</label>
                                <select class="form-select" id="resultType" required>
                                    <option value="Clicks">Clicks</option>
                                    <option value="Impressions">Impressions</option>
                                    <option value="Conversions">Conversions</option>
                                    <option value="Leads">Leads</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Results</label>
                                <input type="number" class="form-control" id="results" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Per Result (CPR)</label>
                                <input type="number" step="0.01" class="form-control" id="cpr" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reach</label>
                                <input type="number" class="form-control" id="reach" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Impressions</label>
                                <input type="number" class="form-control" id="impressions" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Spend (Rs.)</label>
                                <input type="number" step="0.01" class="form-control" id="spend" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quality Ranking</label>
                                <select class="form-select" id="qualityRanking">
                                    <option value="Above Average">Above Average</option>
                                    <option value="Average">Average</option>
                                    <option value="Below Average">Below Average</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Conversion Rate Ranking</label>
                                <select class="form-select" id="conversionRanking">
                                    <option value="Above Average">Above Average</option>
                                    <option value="Average">Average</option>
                                    <option value="Below Average">Below Average</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Evidence Image</label>
                                <input type="file" class="form-control" id="evidenceImageUpload" accept="image/*" onchange="handleEvidenceImageUpload(this)">
                                <input type="hidden" id="evidenceImageUrl">
                                <div id="evidenceImagePreview" class="mt-2" style="display: none;">
                                    <img id="evidenceImagePreviewImg" src="" alt="Evidence Preview" style="max-width: 150px; max-height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeEvidenceImage()">Remove</button>
                                </div>
                                <small class="text-muted">Upload campaign performance evidence/screenshot</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Creative Image</label>
                                <input type="file" class="form-control" id="creativeImageUpload" accept="image/*" onchange="handleCreativeImageUpload(this)">
                                <input type="hidden" id="creativeImageUrl">
                                <div id="creativeImagePreview" class="mt-2" style="display: none;">
                                    <img id="creativeImagePreviewImg" src="" alt="Creative Preview" style="max-width: 150px; max-height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeCreativeImage()">Remove</button>
                                </div>
                                <small class="text-muted">Upload ad creative/design used in campaign</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save Campaign</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="campaignDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-bullhorn me-2"></i>Campaign Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Platform:</strong> <span class="badge bg-primary" id="detailPlatform"></span></p>
                            <p><strong>Ad ID:</strong> <span id="detailAdId"></span></p>
                            <p><strong>Ad Name:</strong> <span id="detailAdName"></span></p>
                            <p><strong>Result Type:</strong> <span id="detailResultType"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Results:</strong> <span id="detailResults"></span></p>
                            <p><strong>CPR:</strong> Rs. <span id="detailCPR"></span></p>
                            <p><strong>Reach:</strong> <span id="detailReach"></span></p>
                            <p><strong>Impressions:</strong> <span id="detailImpressions"></span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <p class="mb-1"><strong>Spend:</strong> Rs. <span id="detailSpend"></span></p>
                                <p class="mb-1"><strong>Quality Ranking:</strong> <span id="detailQuality"></span></p>
                                <p class="mb-0"><strong>Conversion Ranking:</strong> <span id="detailConversion"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-bar me-2"></i>Evidence Image</h6>
                            <div id="evidenceImageContainer" class="mb-3">
                                <p class="text-muted">No evidence image uploaded</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-image me-2"></i>Creative Image</h6>
                            <div id="creativeImageContainer" class="mb-3">
                                <p class="text-muted">No creative image uploaded</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Campaign Modal -->
    <div class="modal fade" id="editCampaignModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCampaignForm">
                        <input type="hidden" id="editCampaignId">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Platform</label>
                                <select class="form-select" id="editCampaignPlatform" required>
                                    <option value="">Select Platform</option>
                                    <option value="Meta Ads">Meta Ads (Facebook & Instagram)</option>
                                    <option value="Google Ads">Google Ads</option>
                                    <option value="LinkedIn Ads">LinkedIn Ads</option>
                                    <option value="TikTok Ads">TikTok Ads</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ad ID</label>
                                <input type="text" class="form-control" id="editAdId" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ad Name</label>
                                <input type="text" class="form-control" id="editAdName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Result Type</label>
                                <select class="form-select" id="editResultType" required>
                                    <option value="Clicks">Clicks</option>
                                    <option value="Impressions">Impressions</option>
                                    <option value="Conversions">Conversions</option>
                                    <option value="Leads">Leads</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Results</label>
                                <input type="number" class="form-control" id="editResults" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Per Result (CPR)</label>
                                <input type="number" step="0.01" class="form-control" id="editCpr" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reach</label>
                                <input type="number" class="form-control" id="editReach" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Impressions</label>
                                <input type="number" class="form-control" id="editImpressions" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Spend (Rs.)</label>
                                <input type="number" step="0.01" class="form-control" id="editSpend" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quality Ranking</label>
                                <select class="form-select" id="editQualityRanking">
                                    <option value="Above Average">Above Average</option>
                                    <option value="Average">Average</option>
                                    <option value="Below Average">Below Average</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Conversion Rate Ranking</label>
                                <select class="form-select" id="editConversionRanking">
                                    <option value="Above Average">Above Average</option>
                                    <option value="Average">Average</option>
                                    <option value="Below Average">Below Average</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Evidence Image</label>
                                <!-- Current Evidence Image -->
                                <div id="editCurrentEvidenceImage" style="display: none;" class="mb-2">
                                    <img id="editCurrentEvidenceImageImg" src="" alt="Current Evidence" style="max-width: 150px; max-height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <p class="text-muted small mb-0">Current evidence image</p>
                                </div>
                                <input type="file" class="form-control" id="editEvidenceImageUpload" accept="image/*" onchange="handleEditEvidenceImageUpload(this)">
                                <input type="hidden" id="editEvidenceImageUrl">
                                <div id="editEvidenceImagePreview" class="mt-2" style="display: none;">
                                    <img id="editEvidenceImagePreviewImg" src="" alt="Evidence Preview" style="max-width: 150px; max-height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeEditEvidenceImage()">Remove</button>
                                </div>
                                <small class="text-muted">Upload new evidence image (optional - leave empty to keep current)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Creative Image</label>
                                <!-- Current Creative Image -->
                                <div id="editCurrentCreativeImage" style="display: none;" class="mb-2">
                                    <img id="editCurrentCreativeImageImg" src="" alt="Current Creative" style="max-width: 150px; max-height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <p class="text-muted small mb-0">Current creative image</p>
                                </div>
                                <input type="file" class="form-control" id="editCreativeImageUpload" accept="image/*" onchange="handleEditCreativeImageUpload(this)">
                                <input type="hidden" id="editCreativeImageUrl">
                                <div id="editCreativeImagePreview" class="mt-2" style="display: none;">
                                    <img id="editCreativeImagePreviewImg" src="" alt="Creative Preview" style="max-width: 150px; max-height: 150px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeEditCreativeImage()">Remove</button>
                                </div>
                                <small class="text-muted">Upload new creative image (optional - leave empty to keep current)</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Campaign</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="documentForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Select Client</label>
                                <select class="form-select" id="docClientSelect" required></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" id="docDate" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Select Item Types for This Document</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="itemType1" value="Monthly Payment">
                                            <label class="form-check-label" for="itemType1">
                                                Monthly Payment
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="itemType2" value="Extra Content Credits">
                                            <label class="form-check-label" for="itemType2">
                                                Extra Content Credits
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="itemType3" value="Ad Budget">
                                            <label class="form-check-label" for="itemType3">
                                                Ad Budget
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="itemType4" value="Other Service">
                                            <label class="form-check-label" for="itemType4">
                                                Other Service
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Select one or more item types to include in this document</small>
                                <div class="mt-2">
                                    <span class="badge bg-primary" id="selectedCount" style="display: none;">
                                        <span id="countNumber">0</span> item type(s) selected
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3" id="descriptionField" style="display: none;">
                                <label class="form-label">Description <span class="text-muted">(Required for Other Service)</span></label>
                                <input type="text" class="form-control" id="docDescription">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity (Credits or Units)</label>
                                <input type="number" class="form-control" id="docQuantity" value="1" required min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Price (Rs.)</label>
                                <input type="number" step="0.01" class="form-control" id="docUnitPrice" required min="0">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Generate Document</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewDocumentModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>Document View</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="documentPreview" class="document-preview"></div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <small class="text-muted"><i class="fas fa-eye me-1"></i>Read-only document view</small>
                    </div>
                    <button class="btn btn-primary" onclick="printDocument()"><i class="fas fa-print me-2"></i>Print</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label class="form-label">User Role</label>
                            <select class="form-select" id="newUserRole" required>
                                <option value="">Select Role</option>
                                <option value="marketer">Digital Marketer</option>
                                <option value="finance">Finance Manager</option>
                            </select>
                            <small class="text-muted">Admins cannot create new Admin accounts.</small>
                        </div>
                        <div class="mb-3">
						<label class="form-label">Username</label>
						<input type="text" class="form-control" id="newUsername" required>
                            <small class="text-muted">This is the unique username for the new user.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Temporary Password (Min 6 Chars)</label>
                            <input type="password" class="form-control" id="tempPassword" required minlength="6">
                            <small class="text-danger">The user will be **forced to change this** on their first login.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Temporary Password Warning Banner -->
    <div id="tempPasswordWarning" class="alert alert-warning alert-dismissible fade show" style="display: none; position: fixed; top: 0; left: 0; right: 0; z-index: 1050; margin: 0; border-radius: 0;">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Security Recommendation:</strong>
                <span class="ms-2">You're using a temporary password. For account security, please change it to a permanent one.</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Custom Notification System -->
    <div id="notificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>
    
    <!-- Custom Confirmation Modal -->
    <div class="modal fade" id="customConfirmModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalTitle">
                        <i class="fas fa-question-circle me-2"></i>Confirmation Required
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i id="confirmModalIcon" class="fas fa-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p id="confirmModalMessage" class="mb-0">Are you sure you want to proceed?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="confirmModalCancel">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmModalConfirm">
                        <i class="fas fa-check me-2"></i>Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Preview Modal -->
    <div class="modal fade" id="invoicePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice me-2"></i>Invoice Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="invoicePreviewContent">
                        <!-- Invoice content will be populated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" onclick="printInvoicePDF()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveInvoice()">
                        <i class="fas fa-save me-2"></i>Save Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // DATA STRUCTURE & STORAGE (LOCAL MOCK-UP)
        // ============================================
        let currentUser = { username: '', role: '', isTempPassword: false };

        // --- RBAC DEFINITION ---
        const permissions = {
            admin: {
                sections: ['dashboard', 'clients', 'content', 'campaigns', 'finances', 'profile', 'users', 'settings'],
                canAddClient: true,
                canDeleteClient: true,
                canManageContent: true,
                canManageCampaigns: true,
                canManageFinances: true,
                canManageUsers: true
            },
            marketer: {
                sections: ['dashboard', 'clients', 'content', 'campaigns', 'profile'],
                canAddClient: false,
                canDeleteClient: false,
                canManageContent: true,
                canManageCampaigns: true,
                canManageFinances: false,
                canManageUsers: false
            },
            finance: {
                sections: ['dashboard', 'clients', 'finances', 'profile'],
                canAddClient: false,
                canDeleteClient: false,
                canManageContent: false,
                canManageCampaigns: false,
                canManageFinances: true,
                canManageUsers: false
            }
        };

        // NOTE: This initial data will be overwritten by loadAllDataFromPHP() on success, 
        // but it provides defaults if the database connection fails.
        let appData = {
            clients: [],
            contentCredits: [],
            campaigns: [],
            documents: { quotations: [], invoices: [], receipts: [] },
            users: [
                { id: 1, username: 'admin', role: 'admin', password: 'password', isTempPassword: false },
                { id: 2, username: 'marketer', role: 'marketer', password: 'password', isTempPassword: false },
                { id: 3, username: 'finance', role: 'finance', password: 'password', isTempPassword: false }
            ]
        };

        let selectedClientId = null;
        let selectedClientContentId = null;
        let selectedClientCampaignId = null;
        let campaignImageFiles = [];

        // Default company info (fallback)
        const DEFAULT_COMPANY_INFO = {
            name: 'Ayonion Studios',
            tagline: 'Creative Digital Solutions',
            email: 'info@ayonionstudios.com',
            tel: '+94 (70) 610 1035',
            address: 'No.59/1/C, Kaluwala road, Kossinna, Ganemulla.',
            bank: 'NDB Bank',
            account: '101001037178',
            accountName: 'Ayonion Studios (pvt) Ltd'
        };

        // Dynamic company info (loaded from settings)
        let COMPANY_INFO = { ...DEFAULT_COMPANY_INFO };

        // ============================================
        // STORAGE & DATA SYNC FUNCTIONS (PHP/MySQL)
        // ============================================

        function saveToLocalStorage() {
            // Used to cache data locally, but data is considered authoritative from PHP
            localStorage.setItem('ayonionData', JSON.stringify(appData));
            localStorage.setItem('selectedClientContentId', selectedClientContentId);
            localStorage.setItem('selectedClientCampaignId', selectedClientCampaignId);
        }

        function loadFromLocalStorage() {
            // Fallback function to load local cache if PHP server fails
            const saved = localStorage.getItem('ayonionData');
            if (saved) {
                const loadedData = JSON.parse(saved);
                appData.clients = loadedData.clients || [];
                appData.contentCredits = loadedData.contentCredits || [];
                appData.campaigns = loadedData.campaigns || [];
                appData.documents = loadedData.documents || { quotations: [], invoices: [], receipts: [] };
                appData.users = loadedData.users || [
                    { id: 1, username: 'admin', role: 'admin', password: 'password', isTempPassword: false },
                    { id: 2, username: 'marketer', role: 'marketer', password: 'password', isTempPassword: false },
                    { id: 3, username: 'finance', role: 'finance', password: 'password', isTempPassword: false }
                ];
            }
            selectedClientContentId = parseInt(localStorage.getItem('selectedClientContentId')) || (appData.clients.length > 0 ? appData.clients[0].id : null);
            selectedClientCampaignId = parseInt(localStorage.getItem('selectedClientCampaignId')) || (appData.clients.length > 0 ? appData.clients[0].id : null);
        }

        // ============================================
        // TABLE SORTING FUNCTIONS
        // ============================================
        let sortStates = {}; // Store sort state for each table

        function initializeTableSorting() {
            // Add click handlers to all sortable headers
            document.querySelectorAll('.sortable').forEach(header => {
                header.addEventListener('click', function() {
                    const table = this.closest('table');
                    const tableId = table.id;
                    const sortField = this.dataset.sort;
                    const sortType = this.dataset.type;
                    
                    // Toggle sort direction
                    if (!sortStates[tableId]) sortStates[tableId] = {};
                    const currentSort = sortStates[tableId][sortField] || 'none';
                    const newSort = currentSort === 'asc' ? 'desc' : 'asc';
                    
                    // Clear all sort indicators for this table
                    table.querySelectorAll('.sortable').forEach(h => {
                        h.classList.remove('asc', 'desc');
                    });
                    
                    // Set new sort state
                    sortStates[tableId] = { [sortField]: newSort };
                    this.classList.add(newSort);
                    
                    // Sort the table
                    sortTable(table, sortField, sortType, newSort);
                });
            });
        }

        function sortTable(table, field, type, direction) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            rows.sort((a, b) => {
                let aVal = a.dataset[field] || '';
                let bVal = b.dataset[field] || '';
                
                // Handle different data types
                if (type === 'number') {
                    aVal = parseFloat(aVal.replace(/[^0-9.-]/g, '')) || 0;
                    bVal = parseFloat(bVal.replace(/[^0-9.-]/g, '')) || 0;
                } else if (type === 'date') {
                    aVal = new Date(aVal).getTime() || 0;
                    bVal = new Date(bVal).getTime() || 0;
                } else {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }
                
                if (direction === 'asc') {
                    return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
                } else {
                    return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
                }
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        async function loadAllDataFromPHP() {
            try {
                showAlert('Loading latest data from server...', 'info', 2000);
                const response = await fetch('handler_data.php');
                const data = await response.json();

                if (data.success) {
                    // Overwrite in-memory appData with fresh data from SQL backend
                    appData.clients = data.clients || [];
                    appData.contentCredits = data.contentCredits || [];
                    appData.campaigns = data.campaigns || [];
                    // Ensure all document types are populated
                    appData.documents.quotations = (data.documents && data.documents.quotations) || [];
                    appData.documents.invoices = (data.documents && data.documents.invoices) || [];
                    appData.documents.receipts = (data.documents && data.documents.receipts) || [];
                    
                    // NOTE: Users are loaded via the successful login, but we update the cache too
                    
                    saveToLocalStorage(); 
                    console.log('✅ Data loaded from SQL successfully.');
                } else {
                    throw new Error(data.message || 'Failed to fetch data from PHP handler.');
                }
            } catch (error) {
                console.error('❌ Error loading from PHP/SQL:', error);
                showAlert('Unable to connect to server. Using cached data. Please check your connection.', 'warning');
                loadFromLocalStorage(); // Fallback
            }
        }

        // ============================================
        // LOGIN SYSTEM (PHP Handlers)
        // ============================================
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const role = document.getElementById('userRole').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            const loginBtn = this.querySelector('button[type="submit"]');
            const originalText = loginBtn.innerHTML;
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';

            try {
                const response = await fetch('handler_login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({ username, password, role })
                });
                
                const result = await response.json();

                if (result.success) {
                    const user = result.user;
                    currentUser = { username: user.username, role: user.role, isTempPassword: user.isTempPassword };

                    document.getElementById('loginPage').style.display = 'none';
                    document.getElementById('mainApp').style.display = 'block';
                    document.getElementById('currentUser').textContent = user.username.charAt(0).toUpperCase() + user.username.slice(1);
                    document.getElementById('currentRole').textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);

                    await initializeApp();
                    if (currentUser.isTempPassword) {
                        showTempPasswordWarning();
                    }
                } else {
                    throw new Error(result.message);
                }

                loginBtn.disabled = false;
                loginBtn.innerHTML = originalText;

            } catch (error) {
                console.error('Login error:', error);
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Login';
                showAlert('Login failed. Please check your username and password.', 'danger');
            }
        });

        function showTempPasswordWarning() {
            const warning = document.getElementById('tempPasswordWarning');
            if (warning) {
                warning.style.display = 'block';
            }
        }


        async function logout() {
            const confirmed = await showConfirm('Are you sure you want to logout?', 'Logout Confirmation', 'warning');
            if (!confirmed) return;
            
            try {
                await fetch('logout.php', { method: 'POST' });
            } catch (_) {}
            currentUser = { username: '', role: '', isTempPassword: false };
            document.getElementById('loginPage').style.display = 'flex';
            document.getElementById('mainApp').style.display = 'none';
            document.getElementById('loginForm').reset();
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
        }

        function hasPermission(action) {
            return permissions[currentUser.role][action] || false;
        }

        function applyRoleBasedUI() {
            const userPermissions = permissions[currentUser.role];

            const addClientBtn = document.getElementById('addClientBtn');
            if (addClientBtn) {
                addClientBtn.style.display = userPermissions.canAddClient ? 'inline-block' : 'none';
            }

            const actionsHeader = document.getElementById('clientActionsHeader');
            if (actionsHeader) {
                actionsHeader.style.display = userPermissions.canDeleteClient ? 'table-cell' : 'none';
            }

            // Hide Settings tab if not admin
            if (currentUser.role !== 'admin') {
                const settingsLink = document.querySelector('[data-section="settings"]');
                if (settingsLink) {
                    settingsLink.parentElement.style.display = 'none';
                }
            }
        }

        function checkNavigationAccess(sectionId) {
            const userPermissions = permissions[currentUser.role];
            if (!userPermissions.sections.includes(sectionId)) {
                showAlert('Access denied to this section', 'danger');
                return false;
            }
            return true;
        }


        // ============================================
        // APP INITIALIZATION & NAVIGATION
        // ============================================
        async function initializeApp() {
            await loadAllDataFromPHP();
            buildNavigation();
            applyRoleBasedUI();
            navigateToSection('dashboard');
            checkRenewalDates();
            document.getElementById('currentUser').textContent = currentUser.username ? (currentUser.username.charAt(0).toUpperCase() + currentUser.username.slice(1)) : '';
            document.getElementById('currentRole').textContent = currentUser.role ? (currentUser.role.charAt(0).toUpperCase() + currentUser.role.slice(1)) : '';
            
            // Load company settings for sidebar header and reports
            await loadCompanySettings();
        }

        // Load company settings for reports and sidebar
        async function loadCompanySettings() {
            try {
                const res = await fetch('handler_settings.php?action=get', { credentials: 'same-origin' });
                const data = await res.json();
                if (data.success && data.settings) {
                    const settings = data.settings;
                    // Update COMPANY_INFO for reports
                    COMPANY_INFO = {
                        ...DEFAULT_COMPANY_INFO,
                        name: settings.company_name || DEFAULT_COMPANY_INFO.name,
                        email: settings.email || DEFAULT_COMPANY_INFO.email,
                        tel: settings.phone || DEFAULT_COMPANY_INFO.tel,
                        address: settings.address || DEFAULT_COMPANY_INFO.address,
                        logoUrl: settings.logo_url || ''
                    };
                    // Update sidebar header
                    updateSidebarHeader(settings.company_name, settings.logo_url);
                }
            } catch (_) {
                // Use default company info if settings can't be loaded
                COMPANY_INFO = { ...DEFAULT_COMPANY_INFO };
            }
        }

        // Restore session on refresh
        (async function restoreSessionOnLoad() {
            try {
                const res = await fetch('session_check.php', { method: 'GET', credentials: 'same-origin' });
                const result = await res.json();
                if (result && result.success && result.user) {
                    const user = result.user;
                    currentUser = { username: user.username, role: user.role, isTempPassword: user.isTempPassword };
                    document.getElementById('loginPage').style.display = 'none';
                    document.getElementById('mainApp').style.display = 'block';
                    await initializeApp();
                    if (currentUser.isTempPassword) {
                        showTempPasswordWarning();
                    }
                }
            } catch (_) {
                // ignore; user will stay on login screen
            }
        })();
        
        function buildNavigation() {
            const navMenu = document.getElementById('navMenu');
            const menuItems = [
                { id: 'dashboard', icon: 'home', label: 'Dashboard' },
                { id: 'clients', icon: 'users', label: 'Client Profiles' },
                { id: 'content', icon: 'file-alt', label: 'Content Credits' },
                { id: 'campaigns', icon: 'bullhorn', label: 'Ad Campaigns' },
                { id: 'finances', icon: 'file-invoice-dollar', label: 'Financial Management' },
                { id: 'profile', icon: 'user-circle', label: 'My Profile' },
                { id: 'users', icon: 'user-cog', label: 'Manage Users' },
                { id: 'settings', icon: 'gear', label: 'Admin Settings' }
            ];

            const userPermissions = permissions[currentUser.role];
            navMenu.innerHTML = menuItems
                .filter(item => userPermissions.sections.includes(item.id))
                .map(item => `
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="${item.id}">
                            <i class="fas fa-${item.icon}"></i>
                            <span>${item.label}</span>
                        </a>
                    </li>
                `).join('');

            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    navigateToSection(this.dataset.section);
                });
            });
        }

        // ============================================
        // PROFILE MANAGEMENT
        // ============================================
        async function loadProfile() {
            try {
                const res = await fetch('handler_users.php?action=get_profile', { credentials: 'same-origin' });
                const data = await res.json();
                if (data.success) {
                    const profile = data.profile || {};
                    document.getElementById('profileUsername').value = profile.username || '';
                    document.getElementById('profileRole').value = (profile.role || '').charAt(0).toUpperCase() + (profile.role || '').slice(1);
                    document.getElementById('profileFullName').value = profile.full_name || '';
                    document.getElementById('profileEmail').value = profile.email || '';
                } else {
                    showAlert('Failed to load profile: ' + (data.message || ''), 'danger');
                }
            } catch (_) {
                showAlert('Network error loading profile', 'danger');
            }
        }

        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const payload = {
                fullName: document.getElementById('profileFullName').value.trim(),
                email: document.getElementById('profileEmail').value.trim()
            };
            try {
                const res = await fetch('handler_users.php?action=update_profile', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (data.success) {
                    showAlert('Profile updated successfully! ✅', 'success');
                } else {
                    showAlert('Failed to update profile: ' + (data.message || ''), 'danger');
                }
            } catch (_) {
                showAlert('Network error updating profile', 'danger');
            }
        });

        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                showAlert('New passwords do not match!', 'danger');
                return;
            }

            if (newPassword.length < 6) {
                showAlert('Password must be at least 6 characters long!', 'danger');
                return;
            }

            const payload = {
                currentPassword: currentPassword,
                newPassword: newPassword
            };

            try {
                const res = await fetch('handler_users.php?action=change_password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (data.success) {
                    showAlert('Password changed successfully! 🔒', 'success');
                    document.getElementById('changePasswordForm').reset();
                } else {
                    showAlert('Failed to change password: ' + (data.message || ''), 'danger');
                }
            } catch (_) {
                showAlert('Network error changing password', 'danger');
            }
        });

        async function loadSettings() {
            if (currentUser.role !== 'admin') { showAlert('Access denied to Settings', 'danger'); return; }
            try {
                const res = await fetch('handler_settings.php?action=get', { credentials: 'same-origin' });
                const data = await res.json();
                if (data.success) {
                    const s = data.settings || {};
                    document.getElementById('settingsCompanyName').value = s.company_name || '';
                    document.getElementById('settingsLogoUrl').value = s.logo_url || '';
                    document.getElementById('settingsEmail').value = s.email || '';
                    document.getElementById('settingsPhone').value = s.phone || '';
                    document.getElementById('settingsAddress').value = s.address || '';
                    
                    // Show existing logo if available
                    if (s.logo_url) {
                        document.getElementById('logoPreviewImg').src = s.logo_url;
                        document.getElementById('logoPreview').style.display = 'block';
                    }
                    
                    // Update sidebar immediately
                    updateSidebarHeader(s.company_name, s.logo_url);
                } else {
                    showAlert('Failed to load settings: ' + (data.message || ''), 'danger');
                }
            } catch (_) {
                showAlert('Network error loading settings', 'danger');
            }
        }

        document.getElementById('settingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (currentUser.role !== 'admin') { showAlert('Access denied', 'danger'); return; }
            const payload = {
                companyName: document.getElementById('settingsCompanyName').value.trim(),
                logoUrl: document.getElementById('settingsLogoUrl').value.trim(),
                email: document.getElementById('settingsEmail').value.trim(),
                phone: document.getElementById('settingsPhone').value.trim(),
                address: document.getElementById('settingsAddress').value.trim()
            };
            try {
                const res = await fetch('handler_settings.php?action=update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    showAlert('Settings saved', 'success');
                    // Update sidebar immediately after saving
                    const companyName = document.getElementById('settingsCompanyName').value;
                    const logoUrl = document.getElementById('settingsLogoUrl').value;
                    updateSidebarHeader(companyName, logoUrl);
                    // Reload company settings for reports
                    await loadCompanySettings();
                } else {
                    showAlert('Failed to save settings: ' + (data.message || ''), 'danger');
                }
            } catch (_) {
                showAlert('Network error saving settings', 'danger');
            }
        });

        // Logo upload handler - File System Storage
        async function handleLogoUpload(input) {
            const file = input.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert('Please select a valid image file.', 'danger');
                input.value = '';
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showAlert('Logo file is too large. Please choose an image smaller than 2MB.', 'danger');
                input.value = '';
                return;
            }
            
            // Upload file to server
            const formData = new FormData();
            formData.append('logo', file);
            
            try {
                const response = await fetch('upload_logo_handler_simple.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('settingsLogoUrl').value = result.logo_url;
                    
                    // Show preview
                    const preview = document.getElementById('logoPreview');
                    const previewImg = document.getElementById('logoPreviewImg');
                    previewImg.src = result.logo_url;
                    preview.style.display = 'block';
                    
                    showAlert('Logo uploaded successfully!', 'success');
                } else {
                    showAlert('Upload failed: ' + result.message, 'danger');
                    input.value = '';
                }
            } catch (error) {
                showAlert('Upload error: ' + error.message, 'danger');
                input.value = '';
            }
        }

        function removeLogo() {
            document.getElementById('settingsLogoFile').value = '';
            document.getElementById('settingsLogoUrl').value = '';
            document.getElementById('logoPreview').style.display = 'none';
        }

        // ============================================
        // AUTO CARRY FORWARD SYSTEM
        // ============================================
        
        async function runAutoCarryForward() {
            const confirmed = await showConfirm(
                'This will process all clients whose renewal dates have passed and automatically carry forward their unused credits. Continue?',
                'Run Auto Carry Forward',
                'warning'
            );
            
            if (!confirmed) return;
            
            try {
                showAlert('Processing auto carry forward... Please wait.', 'info', 3000);
                
                const response = await fetch('handler_auto_carry_forward.php?manual=true', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert(`✅ ${result.message}`, 'success', 5000);
                    
                    // Show detailed results in a modal
                    if (result.results && result.results.length > 0) {
                        showCarryForwardResults(result.results, result.processed_count, result.error_count);
                    }
                    
                    // Reload data to reflect changes
                    await loadAllDataFromPHP();
                    
                    // Reload current view if on clients or content page
                    if (document.getElementById('clients').classList.contains('active')) {
                        loadClients();
                    } else if (document.getElementById('content').classList.contains('active')) {
                        loadContentCredits();
                    }
                } else {
                    showAlert('Auto carry forward failed: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Auto carry forward error:', error);
                showAlert('Failed to run auto carry forward: ' + error.message, 'danger');
            }
        }
        
        function showCarryForwardResults(results, successCount, errorCount) {
            let html = `
                <div style="max-height: 400px; overflow-y: auto;">
                    <div class="alert alert-${errorCount > 0 ? 'warning' : 'success'}">
                        <strong>Summary:</strong> ${successCount} client(s) processed successfully${errorCount > 0 ? `, ${errorCount} error(s)` : ''}.
                    </div>
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Carried Forward</th>
                                <th>New Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            results.forEach(r => {
                if (r.status === 'success') {
                    html += `
                        <tr>
                            <td>${r.client_name}</td>
                            <td><span class="badge bg-info">${r.carried_forward} credits</span></td>
                            <td><span class="badge bg-success">${r.new_total} total</span></td>
                            <td><i class="fas fa-check-circle text-success"></i> Success</td>
                        </tr>
                    `;
                } else {
                    html += `
                        <tr>
                            <td>${r.client_name}</td>
                            <td colspan="2"><small class="text-danger">${r.error}</small></td>
                            <td><i class="fas fa-exclamation-circle text-danger"></i> Error</td>
                        </tr>
                    `;
                }
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            document.getElementById('documentPreview').innerHTML = html;
            showDocumentModal();
        }
        
        async function viewCarryForwardLog() {
            try {
                const response = await fetch('logs/auto_carry_forward.log');
                if (response.ok) {
                    const logContent = await response.text();
                    
                    let html = `
                        <div style="background: #1e1e1e; color: #d4d4d4; padding: 20px; border-radius: 8px; font-family: 'Courier New', monospace; max-height: 600px; overflow-y: auto;">
                            <h5 style="color: #4ec9b0; margin-bottom: 15px;">
                                <i class="fas fa-file-alt"></i> Auto Carry Forward Process Log
                            </h5>
                            <pre style="color: #d4d4d4; margin: 0; white-space: pre-wrap;">${logContent || 'No log entries found.'}</pre>
                        </div>
                    `;
                    
                    document.getElementById('documentPreview').innerHTML = html;
                    showDocumentModal();
                } else {
                    showAlert('Log file not found. No carry forward process has been run yet.', 'info');
                }
            } catch (error) {
                showAlert('Unable to load log file: ' + error.message, 'warning');
            }
        }

        // ✅ NEW: Content image upload handler
        async function handleContentImageUpload(input) {
            const file = input.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert('Please select a valid image file.', 'danger');
                input.value = '';
                return;
            }
            
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('Image file is too large. Please choose an image smaller than 5MB.', 'danger');
                input.value = '';
                return;
            }
            
            // Upload file to server
            const formData = new FormData();
            formData.append('contentImage', file);
            
            try {
                const response = await fetch('upload_content_image.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store the image URL in a hidden field or data attribute
                    input.dataset.imageUrl = result.image_url;
                    
                    // Show preview
                    const preview = document.getElementById('contentImagePreview');
                    const previewImg = document.getElementById('contentImagePreviewImg');
                    previewImg.src = result.image_url;
                    preview.style.display = 'block';
                    
                    showAlert('Content image uploaded successfully!', 'success');
                } else {
                    showAlert('Upload failed: ' + result.message, 'danger');
                    input.value = '';
                }
            } catch (error) {
                showAlert('Upload error: ' + error.message, 'danger');
                input.value = '';
            }
        }

        function removeContentImage() {
            const input = document.getElementById('contentImageUpload');
            const preview = document.getElementById('contentImagePreview');
            
            input.value = '';
            input.dataset.imageUrl = '';
            preview.style.display = 'none';
        }

        // ✅ NEW: Clear content image preview function
        function clearContentImagePreview() {
            const input = document.getElementById('contentImageUpload');
            const preview = document.getElementById('contentImagePreview');
            const previewImg = document.getElementById('contentImagePreviewImg');
            
            // Reset input
            input.value = '';
            input.dataset.imageUrl = '';
            
            // Hide preview
            preview.style.display = 'none';
            
            // Clear image source
            previewImg.src = '';
        }

        // Client logo upload handler for Add Client modal
        async function handleClientLogoUpload(input) {
            const file = input.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert('Please select a valid image file.', 'danger');
                input.value = '';
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showAlert('Logo file is too large. Please choose an image smaller than 2MB.', 'danger');
                input.value = '';
                return;
            }
            
            // Upload file to server
            const formData = new FormData();
            formData.append('logo', file);
            
            try {
                const response = await fetch('upload_logo_handler_simple.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store the logo URL in the input's data attribute
                    input.dataset.logoUrl = result.logo_url;
                    
                    // Show preview
                    const preview = document.getElementById('clientLogoPreview');
                    const previewImg = document.getElementById('clientLogoPreviewImg');
                    previewImg.src = result.logo_url;
                    preview.style.display = 'block';
                    
                    showAlert('Logo uploaded successfully!', 'success');
                } else {
                    showAlert('Upload failed: ' + result.message, 'danger');
                    input.value = '';
                }
            } catch (error) {
                showAlert('Upload error: ' + error.message, 'danger');
                input.value = '';
            }
        }

        function removeClientLogo() {
            const input = document.getElementById('clientLogoUpload');
            const preview = document.getElementById('clientLogoPreview');
            
            if (input) {
                input.value = '';
                input.dataset.logoUrl = '';
            }
            if (preview) preview.style.display = 'none';
        }

        function clearClientLogoPreview() {
            const input = document.getElementById('clientLogoUpload');
            const preview = document.getElementById('clientLogoPreview');
            const previewImg = document.getElementById('clientLogoPreviewImg');
            
            if (input) {
                input.value = '';
                input.dataset.logoUrl = '';
            }
            if (previewImg) previewImg.src = '';
            if (preview) preview.style.display = 'none';
        }

        // ✅ NEW: Add modal event listener to clear image preview when modal is hidden
        document.addEventListener('DOMContentLoaded', function() {
            const addContentModal = document.getElementById('addContentModal');
            if (addContentModal) {
                addContentModal.addEventListener('hidden.bs.modal', function() {
                    clearContentImagePreview();
                });
            }
            
            const addClientModal = document.getElementById('addClientModal');
            if (addClientModal) {
                addClientModal.addEventListener('hidden.bs.modal', function() {
                    clearClientLogoPreview();
                });
            }
        });

        // ✅ NEW: View full content image
        function viewContentImage(imageUrl) {
            // Create a modal to show the full image
            const imageModal = document.createElement('div');
            imageModal.className = 'modal fade';
            imageModal.id = 'imageModal';
            imageModal.innerHTML = `
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-image me-2"></i>Content Image</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${imageUrl}" alt="Content Image" class="img-fluid" style="max-height: 70vh; object-fit: contain;">
                        </div>
                        <div class="modal-footer">
                            <a href="${imageUrl}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(imageModal);
            const bsModal = new bootstrap.Modal(imageModal);
            bsModal.show();
            
            // Remove modal from DOM when hidden
            imageModal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(imageModal);
            });
        }

        function updateSidebarHeader(companyName, logoUrl) {
            const sidebarCompanyName = document.getElementById('sidebarCompanyName');
            const sidebarLogo = document.getElementById('sidebarLogo');
            
            if (sidebarCompanyName) {
                sidebarCompanyName.textContent = companyName || 'Ayonion';
            }
            
            if (sidebarLogo) {
                if (logoUrl && (logoUrl.startsWith('data:image/') || logoUrl.startsWith('uploads/'))) {
                    // Clear any previous error handlers
                    sidebarLogo.onerror = null;
                    sidebarLogo.onload = null;
                    
                    // Set up error handling before setting src
                    sidebarLogo.onerror = function() {
                        console.warn('Failed to load logo image - hiding logo');
                        sidebarLogo.style.display = 'none';
                        sidebarLogo.onerror = null; // Remove handler to prevent multiple calls
                    };
                    
                    sidebarLogo.onload = function() {
                        console.log('Logo loaded successfully');
                        sidebarLogo.style.display = 'block';
                        sidebarLogo.onload = null; // Remove handler
                    };
                    
                    // Set the source after handlers are in place
                    sidebarLogo.src = logoUrl;
                } else {
                    sidebarLogo.style.display = 'none';
                }
            }
        }
        
        function navigateToSection(sectionId) {
            if (!checkNavigationAccess(sectionId)) return;

            document.querySelectorAll('.sidebar .nav-link').forEach(l => l.classList.remove('active'));
            const link = document.querySelector(`[data-section="${sectionId}"]`);
            if (link) link.classList.add('active');

            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');

            const titles = {
                dashboard: 'Dashboard',
                clients: 'Client Profiles',
                content: 'Content Credits',
                campaigns: 'Ad Campaigns',
                finances: 'Financial Management',
                profile: 'My Profile',
                users: 'User Management',
                settings: 'Admin Settings'
            };
            document.getElementById('pageTitle').textContent = titles[sectionId];

            if(sectionId === 'dashboard') loadDashboard();
            if(sectionId === 'clients') loadClientsTable();
            if(sectionId === 'content') {
                populateClientSelect('contentClientSelect', loadContentCredits);
                document.getElementById('contentClientSelect').value = selectedClientContentId;
                loadContentCredits();
            }
            if(sectionId === 'campaigns') {
                populateClientSelect('campaignClientSelect', loadCampaigns);
                document.getElementById('campaignClientSelect').value = selectedClientCampaignId;
                loadCampaigns();
            }
            if(sectionId === 'finances') {
                loadAllFinancials();
                const triggerEl = document.querySelector('#financeTabs a[href="#quotationsTab"]');
                if (triggerEl) {
                    new bootstrap.Tab(triggerEl).show();
                }
            }
            if(sectionId === 'profile') {
                loadProfile();
            }
            if(sectionId === 'users') {
                loadUsersTable();
            }
            if(sectionId === 'settings') {
                loadSettings();
            }
        }

        // Mobile sidebar toggle + close on outside touch/click or Esc
        document.addEventListener('click', function(e) {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebarEl = document.querySelector('.sidebar');
            const target = e.target;
            // Toggle when hamburger clicked
            if (target && toggleBtn && (target.id === 'sidebarToggle' || toggleBtn.contains(target))) {
                document.body.classList.toggle('sidebar-open');
                return;
            }
            // Close if clicking outside when open
            if (document.body.classList.contains('sidebar-open')) {
                const clickedInsideSidebar = sidebarEl && sidebarEl.contains(target);
                if (!clickedInsideSidebar) {
                    document.body.classList.remove('sidebar-open');
                }
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
        });

        // ============================================
        // USER MANAGEMENT
        // ============================================

        async function loadUsersTable() {
            if (!hasPermission('canManageUsers')) {
                document.getElementById('usersTableBody').innerHTML = `<tr><td colspan="4" class="text-center">Access Denied</td></tr>`;
                return;
            }

            const tbody = document.getElementById('usersTableBody');
            try {
                const res = await fetch('handler_users.php?action=list');
                const result = await res.json();
                if (!result.success) throw new Error(result.message || 'Failed to load users');
                // keep local mirror minimal for now
                appData.users = result.users;
            } catch (e) {
                // fallback to local data if server call fails
                console.warn('Users list fetch failed, using local data:', e.message);
            }
            const filteredUsers = (appData.users || []).filter(u => u.role !== 'admin');

            if (filteredUsers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-user-tag"></i>
                        <h4>No Marketer or Finance Users</h4>
                        <p>Add a new user using the button above</p>
                    </div>
                </td></tr>`;
                return;
            }

            tbody.innerHTML = filteredUsers.map(u => {
                const isTemp = u.isTempPassword ? 'Temporary' : 'Permanent';
                const statusColor = u.isTempPassword ? 'warning' : 'success';
                const deleteButton = u.username !== currentUser.username 
                    ? `<button class="btn btn-sm btn-danger" onclick="deleteManagedUser('${u.username}')"><i class="fas fa-trash"></i></button>`
                    : `<button class="btn btn-sm btn-secondary" disabled>Current User</button>`;
                
                return `
                <tr data-username="${u.username.toLowerCase()}"
                    data-role="${u.role.toLowerCase()}"
                    data-passwordstatus="${isTemp.toLowerCase()}">
                    <td>${u.username}</td>
                    <td>${u.role.charAt(0).toUpperCase() + u.role.slice(1)}</td>
                    <td><span class="badge bg-${statusColor}">${isTemp}</span></td>
                    <td>${deleteButton}</td>
                </tr>`;
            }).join('');
        }
        
		function showAddUserModal() {
            if (!hasPermission('canManageUsers')) {
                showAlert('Access Denied. Only Admins can manage users.', 'warning');
                return;
            }
			document.getElementById('addUserForm').reset();

            new bootstrap.Modal(document.getElementById('addUserModal')).show();
        }
        
		document.getElementById('newUserRole').addEventListener('change', function() {
			// Username is manual now; no auto-generation on role change
		});

        // NOTE: User add/delete needs full PHP handler implementation.
		document.getElementById('addUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('newUsername').value.trim();
            const role = document.getElementById('newUserRole').value;
            const tempPassword = document.getElementById('tempPassword').value;

			if (!username) { showAlert('Username is required.', 'danger'); return; }
            if (!role) { showAlert('Please select a role.', 'danger'); return; }
            if (tempPassword.length < 6) { showAlert('Temporary password must be at least 6 characters.', 'danger'); return; }

			// Client-side uniqueness check across all users (case-insensitive)
			try {
				const listRes = await fetch('handler_users.php?action=list');
				const listJson = await listRes.json();
				if (listJson.success) {
					const exists = (listJson.users || []).some(u => (u.username || '').toLowerCase() === username.toLowerCase());
					if (exists) { showAlert('Username already exists. Please choose another.', 'danger'); return; }
				}
			} catch (_) { /* if listing fails, fall back to server-side duplicate handling */ }

            try {
                const response = await fetch('handler_users.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password: tempPassword, role })
                });
                const result = await response.json();
                if (result.success) {
                    await loadUsersTable();
					document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
					bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    this.reset();
                    const roleLabel = role === 'marketer' ? 'Digital Marketer' : (role === 'finance' ? 'Finance Manager' : role);
                    showAlert(`User **${username}** (${roleLabel}) added. Initial password: **${tempPassword}**.`, 'success');
                } else {
                    showAlert(result.message || 'Failed to create user.', 'danger');
                }
            } catch (err) {
                showAlert('Network error creating user.', 'danger');
            }
        });

        async function deleteManagedUser(username) {
            if (!hasPermission('canManageUsers')) {
                showAlert('Access Denied. Only Admins can delete users.', 'warning');
                return;
            }
            if (username === currentUser.username) {
                showAlert('You cannot delete your own account while logged in.', 'danger');
                return;
            }
            const confirmed = await showConfirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`, 'Delete User', 'danger');
            if (!confirmed) return;
            try {
                // Fetch id by listing users first
                const res = await fetch('handler_users.php?action=list');
                const result = await res.json();
                if (!result.success) throw new Error('Failed to load users');
                const user = result.users.find(u => u.username === username);
                if (!user) { showAlert('User not found on server.', 'danger'); return; }
                const delRes = await fetch(`handler_users.php?action=delete&id=${user.id}`);
                const delJson = await delRes.json();
                if (delJson.success) {
                    await loadUsersTable();
                    showAlert(`User ${username} deleted.`, 'danger');
                } else {
                    showAlert(delJson.message || 'Failed to delete user.', 'danger');
                }
            } catch (e) {
                showAlert('Network error deleting user.', 'danger');
            }
        }


        // ============================================
        // CLIENT MANAGEMENT (PHP Handlers)
        // ============================================

        function populateClientSelect(selectId, callback) {
            const select = document.getElementById(selectId);
            const current = select.value;
            select.innerHTML = '<option value="">Choose client...</option>' +
                appData.clients.map(c => `<option value="${c.id}">${c.companyName}</option>`).join('');
            select.value = current;
            if(callback) callback();
        }

        function showAddClientModal() {
            if (!hasPermission('canAddClient')) {
                showAlert('Access Denied. Only Admins can add clients.', 'warning');
                return;
            }
            document.getElementById('addClientForm').reset();
            clearClientLogoPreview();
            new bootstrap.Modal(document.getElementById('addClientModal')).show();
        }

        // FIX: Client Add using PHP handler
        document.getElementById('addClientForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const companyName = document.getElementById('companyName').value.trim();
            const packageCreditsValue = parseInt(document.getElementById('newClientPackageCredits').value) || 0;
            
            // Get uploaded logo URL if available
            const logoInput = document.getElementById('clientLogoUpload');
            const logoUrl = logoInput && logoInput.dataset.logoUrl ? logoInput.dataset.logoUrl : '';

            const newClientData = {
                partnerId: document.getElementById('partnerId').value.trim(),
                companyName: companyName,
                renewalDate: document.getElementById('renewalDate').value,
                packageCredits: packageCreditsValue,
                managingPlatforms: document.getElementById('managingPlatforms').value,
                industry: document.getElementById('industry').value,
                logoUrl: logoUrl
            };
            
            try {
                const response = await fetch('handler_clients.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newClientData)
                });
                
                const result = await response.json();

                if (result.success) {
                    await loadAllDataFromPHP(); // Reload all data from server
                    loadClientsTable();
				document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
				bootstrap.Modal.getInstance(document.getElementById('addClientModal')).hide();
                    this.reset();
                    showAlert('Client profile created successfully! 🎉', 'success');
                    loadDashboard();
                } else {
                    showAlert('Failed to save client on server: ' + result.message, 'danger');
                }

            } catch (error) {
                console.error('Network Error:', error);
                showAlert('Network error when trying to save client.', 'danger');
            }
        });

        function loadClientsTable() {
            const tbody = document.getElementById('clientsTableBody');
            const canDelete = hasPermission('canDeleteClient');

           

            if (appData.clients.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h4>No Clients Yet</h4>
                        <p>Start by adding your first client</p>
                    </div>
                </td></tr>`;
                return;
            }

            tbody.innerHTML = appData.clients.map(c => {
                const totalCredits = c.packageCredits + c.extraCredits + c.carriedForwardCredits;
                const available = totalCredits - c.usedCredits;
                const totalAdBudget = parseFloat(c.totalAdBudget) || 0.00;
                const totalSpent = parseFloat(c.totalSpent) || 0.00;
                const remainingBudget = totalAdBudget - totalSpent;

                const deleteButton = canDelete
                    ? `<button class="btn btn-sm btn-danger" onclick="deleteClient(${c.id})"><i class="fas fa-trash"></i></button>`
                    : '';

                return `
                <tr onclick="showClientDetails(${c.id})" 
                    data-partnerid="${c.partnerId.toLowerCase()}"
                    data-companyname="${c.companyName.toLowerCase()}"
                    data-renewaldate="${c.renewalDate}"
                    data-availablecredits="${available}"
                    data-adbudget="${remainingBudget}"
                    data-platforms="${(c.managingPlatforms || '-').toLowerCase()}"
                    data-industry="${c.industry.toLowerCase()}">
                    <td style="cursor: pointer;">
                        ${c.logoUrl ? `<img src="${c.logoUrl}" class="logo-preview" alt="Logo" onerror="this.style.display='none'; this.parentNode.innerHTML='<i class=\\'fas fa-building fa-2x text-muted\\'></i>'">` : '<i class="fas fa-building fa-2x text-muted"></i>'}
                    </td>
                    <td style="cursor: pointer;">${c.partnerId}</td>
                    <td>${c.companyName}</td>
                    <td>${formatDate(c.renewalDate)}</td>
                    <td><span class="badge bg-${available > 0 ? 'success' : 'danger'}">${available}</span></td>
                    <td>Rs. ${remainingBudget.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td>${c.managingPlatforms || '-'}</td>
                    <td>${c.industry}</td>
                    <td onclick="event.stopPropagation();">${deleteButton}</td>
                </tr>`;
            }).join('');
        }

        function showClientDetails(clientId) {
            const client = appData.clients.find(c => c.id === clientId);
            if (!client) return;

            selectedClientId = clientId;
            const totalCredits = client.packageCredits + client.extraCredits + client.carriedForwardCredits;
            const available = totalCredits - client.usedCredits;
            const totalAdBudget = parseFloat(client.totalAdBudget) || 0.00;
            const totalSpent = parseFloat(client.totalSpent) || 0.00;
            const remainingBudget = totalAdBudget - totalSpent;

            const logoElement = document.getElementById('clientDetailLogo');
            if (logoElement) {
                if (client.logoUrl) {
                    logoElement.src = client.logoUrl;
                    logoElement.onerror = function() {
                        this.style.display = 'none';
                        this.parentNode.innerHTML = '<i class="fas fa-building fa-3x text-muted"></i>';
                    };
                } else {
                    logoElement.style.display = 'none';
                    logoElement.parentNode.innerHTML = '<i class="fas fa-building fa-3x text-muted"></i>';
                }
            }
            // Safely update client detail elements
            const nameEl = document.getElementById('clientDetailName');
            if (nameEl) nameEl.textContent = client.companyName;
            
            const partnerEl = document.getElementById('clientDetailPartnerId');
            if (partnerEl) partnerEl.textContent = client.partnerId;
            
            const industryEl = document.getElementById('clientDetailIndustry');
            if (industryEl) industryEl.textContent = client.industry;
            
            const renewalEl = document.getElementById('clientDetailRenewal');
            if (renewalEl) renewalEl.textContent = formatDate(client.renewalDate);
            
            const platformsEl = document.getElementById('clientDetailPlatforms');
            if (platformsEl) platformsEl.textContent = client.managingPlatforms || '-';
            
            const totalBudgetEl = document.getElementById('clientDetailTotalAdBudget');
            if (totalBudgetEl) totalBudgetEl.textContent = totalAdBudget.toLocaleString(undefined, { minimumFractionDigits: 2 });
            
            const budgetEl = document.getElementById('clientDetailAdBudget');
            if (budgetEl) budgetEl.textContent = remainingBudget.toLocaleString(undefined, { minimumFractionDigits: 2 });

            const packageEl = document.getElementById('clientDetailPackage');
            if (packageEl) packageEl.textContent = client.packageCredits;
            
            const extraEl = document.getElementById('clientDetailExtra');
            if (extraEl) extraEl.textContent = client.extraCredits;
            
            const carriedEl = document.getElementById('clientDetailCarried');
            if (carriedEl) carriedEl.textContent = client.carriedForwardCredits;
            
            const usedEl = document.getElementById('clientDetailUsed');
            if (usedEl) usedEl.textContent = client.usedCredits;
            
            const availableEl = document.getElementById('clientDetailAvailable');
            if (availableEl) availableEl.textContent = available;

            const canManageContent = hasPermission('canManageContent');
            const canManageCampaigns = hasPermission('canManageCampaigns');

            const manageContentBtn = document.getElementById('btnManageContent');
            if (manageContentBtn) manageContentBtn.style.display = canManageContent ? 'block' : 'none';
            
            const viewCampaignsBtn = document.getElementById('btnViewCampaigns');
            if (viewCampaignsBtn) viewCampaignsBtn.style.display = canManageCampaigns ? 'block' : 'none';

            new bootstrap.Modal(document.getElementById('clientDetailsModal')).show();
        }

        function showEditCreditsModal() {
            if (!selectedClientId) {
                showAlert('No client selected.', 'danger');
                return;
            }

            const client = appData.clients.find(c => c.id === selectedClientId);
            if (!client) {
                showAlert('Client not found.', 'danger');
                return;
            }

            // Populate form with current values
            document.getElementById('editPackageCredits').value = client.packageCredits;
            document.getElementById('editExtraCredits').value = client.extraCredits;
            document.getElementById('resetUsedCredits').checked = false;

            // Show current status
            const totalCredits = client.packageCredits + client.extraCredits + client.carriedForwardCredits;
            const available = totalCredits - client.usedCredits;
            
            document.getElementById('statusCarried').textContent = client.carriedForwardCredits;
            document.getElementById('statusUsed').textContent = client.usedCredits;
            document.getElementById('statusTotal').textContent = totalCredits;
            document.getElementById('statusAvailable').textContent = available;

            // Close client details modal and open edit modal
            bootstrap.Modal.getInstance(document.getElementById('clientDetailsModal')).hide();
            new bootstrap.Modal(document.getElementById('editCreditsModal')).show();
        }

        // Edit Credits Form Submission
        document.getElementById('editCreditsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const packageCredits = parseInt(document.getElementById('editPackageCredits').value);
            const extraCredits = parseInt(document.getElementById('editExtraCredits').value);
            const resetUsedCredits = document.getElementById('resetUsedCredits').checked;

            try {
                const response = await fetch('handler_clients.php?action=update_credits', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        clientId: selectedClientId,
                        packageCredits: packageCredits,
                        extraCredits: extraCredits,
                        resetUsedCredits: resetUsedCredits
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editCreditsModal')).hide();
                    
                    // Reload clients data and reopen client details
                    await loadClients();
                    showClientDetails(selectedClientId);
                } else {
                    showAlert(result.message, 'danger');
                }
            } catch (error) {
                showAlert('Error updating credits: ' + error.message, 'danger');
            }
        });

        // Edit Client Info Modal Functions
        function showEditClientModal() {
            if (!selectedClientId) {
                showAlert('No client selected', 'warning');
                return;
            }

            const client = appData.clients.find(c => c.id === selectedClientId);
            if (!client) {
                showAlert('Client not found', 'danger');
                return;
            }

            // Populate form fields
            document.getElementById('editClientId').value = client.id;
            document.getElementById('editPartnerId').value = client.partnerId;
            document.getElementById('editCompanyName').value = client.companyName;
            document.getElementById('editRenewalDate').value = client.renewalDate;
            document.getElementById('editManagingPlatforms').value = client.managingPlatforms || '';
            document.getElementById('editIndustry').value = client.industry;
            document.getElementById('editTotalAdBudget').value = client.totalAdBudget || 0;

            // Handle logo display
            const currentLogoDiv = document.getElementById('editCurrentLogo');
            const currentLogoImg = document.getElementById('editCurrentLogoImg');
            const editLogoUpload = document.getElementById('editLogoUpload');
            
            if (client.logoUrl) {
                currentLogoImg.src = client.logoUrl;
                currentLogoDiv.style.display = 'block';
                editLogoUpload.dataset.currentLogoUrl = client.logoUrl;
            } else {
                currentLogoDiv.style.display = 'none';
                editLogoUpload.dataset.currentLogoUrl = '';
            }

            // Reset upload fields
            editLogoUpload.value = '';
            editLogoUpload.dataset.newLogoUrl = '';
            document.getElementById('editLogoPreview').style.display = 'none';

            // Close client details and show edit modal
            const clientDetailsModal = bootstrap.Modal.getInstance(document.getElementById('clientDetailsModal'));
            if (clientDetailsModal) clientDetailsModal.hide();
            
            new bootstrap.Modal(document.getElementById('editClientModal')).show();
        }

        async function handleEditLogoUpload(input) {
            const file = input.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert('Please select a valid image file.', 'danger');
                input.value = '';
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showAlert('Logo file is too large. Please choose an image smaller than 2MB.', 'danger');
                input.value = '';
                return;
            }
            
            // Upload file to server
            const formData = new FormData();
            formData.append('logo', file);
            
            try {
                const response = await fetch('upload_logo_handler_simple.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store the new logo URL
                    input.dataset.newLogoUrl = result.logo_url;
                    
                    // Show preview
                    const preview = document.getElementById('editLogoPreview');
                    const previewImg = document.getElementById('editLogoPreviewImg');
                    previewImg.src = result.logo_url;
                    preview.style.display = 'block';
                    
                    showAlert('New logo uploaded successfully! Save to apply changes.', 'success');
                } else {
                    showAlert('Upload failed: ' + result.message, 'danger');
                    input.value = '';
                }
            } catch (error) {
                showAlert('Upload error: ' + error.message, 'danger');
                input.value = '';
            }
        }

        function removeEditLogo() {
            const editLogoUpload = document.getElementById('editLogoUpload');
            const currentLogoDiv = document.getElementById('editCurrentLogo');
            
            // Mark for removal by setting a special flag
            editLogoUpload.dataset.removeLogo = 'true';
            editLogoUpload.dataset.newLogoUrl = '';
            
            // Hide current logo display
            currentLogoDiv.style.display = 'none';
            
            showAlert('Logo will be removed when you save changes.', 'info');
        }

        function cancelEditLogoUpload() {
            const editLogoUpload = document.getElementById('editLogoUpload');
            const preview = document.getElementById('editLogoPreview');
            const currentLogoDiv = document.getElementById('editCurrentLogo');
            
            // Clear upload
            editLogoUpload.value = '';
            editLogoUpload.dataset.newLogoUrl = '';
            editLogoUpload.dataset.removeLogo = '';
            preview.style.display = 'none';
            
            // Show current logo if it exists
            if (editLogoUpload.dataset.currentLogoUrl) {
                currentLogoDiv.style.display = 'block';
            }
        }

        // Edit Client Form Submission
        document.getElementById('editClientForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const clientId = parseInt(document.getElementById('editClientId').value);
            const editLogoUpload = document.getElementById('editLogoUpload');
            
            // Determine logo URL to use
            let logoUrl = editLogoUpload.dataset.currentLogoUrl || '';
            
            if (editLogoUpload.dataset.removeLogo === 'true') {
                logoUrl = ''; // Remove logo
            } else if (editLogoUpload.dataset.newLogoUrl) {
                logoUrl = editLogoUpload.dataset.newLogoUrl; // Use new logo
            }
            
            const updateData = {
                clientId: clientId,
                partnerId: document.getElementById('editPartnerId').value.trim(),
                companyName: document.getElementById('editCompanyName').value.trim(),
                renewalDate: document.getElementById('editRenewalDate').value,
                managingPlatforms: document.getElementById('editManagingPlatforms').value.trim(),
                industry: document.getElementById('editIndustry').value,
                totalAdBudget: parseFloat(document.getElementById('editTotalAdBudget').value) || 0,
                logoUrl: logoUrl
            };

            try {
                const response = await fetch('handler_clients.php?action=update_client', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(updateData)
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('✅ Client information updated successfully!', 'success');
                    
                    // Reload data
                    await loadAllDataFromPHP();
                    loadClientsTable();
                    
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('editClientModal')).hide();
                    
                    // Reopen client details to show updated info
                    setTimeout(() => {
                        showClientDetails(clientId);
                    }, 300);
                } else {
                    showAlert('Failed to update client: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Update client error:', error);
                showAlert('Network error: ' + error.message, 'danger');
            }
        });

        function manageClientContent() {
            if (!hasPermission('canManageContent')) { showAlert('Access Denied. Marketer/Admin role required.', 'danger'); return; }
			document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
			bootstrap.Modal.getInstance(document.getElementById('clientDetailsModal')).hide();
            navigateToSection('content');
            setTimeout(() => {
                document.getElementById('contentClientSelect').value = selectedClientId;
                selectedClientContentId = selectedClientId;
                loadContentCredits();
                saveToLocalStorage();
            }, 100);
        }

        function viewClientCampaigns() {
            if (!hasPermission('canManageCampaigns')) { showAlert('Access Denied. Marketer/Admin role required.', 'danger'); return; }
			document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
			bootstrap.Modal.getInstance(document.getElementById('clientDetailsModal')).hide();
            navigateToSection('campaigns');
            setTimeout(() => {
                document.getElementById('campaignClientSelect').value = selectedClientId;
                selectedClientCampaignId = selectedClientId;
                loadCampaigns();
                saveToLocalStorage();
            }, 100);
        }

        function generateClientReport() {
			document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
			bootstrap.Modal.getInstance(document.getElementById('clientDetailsModal')).hide();
            if (permissions[currentUser.role].sections.includes('content')) {
                document.getElementById('contentClientSelect').value = selectedClientId;
                generateContentReport();
            } else {
                showAlert('Access Denied. Cannot access content data for reports with your role.', 'danger');
            }
        }

        // FIX: Client Delete using PHP handler
        async function deleteClient(id) {
            if (!hasPermission('canDeleteClient')) {
                showAlert('Access Denied. Only Admins can delete clients.', 'warning');
                return;
            }
            const confirmed = await showConfirm('Are you sure you want to delete this client? All related data (content credits, campaigns, documents) will be permanently removed.', 'Delete Client', 'danger');
            if (confirmed) {
                try {
                    const response = await fetch(`handler_clients.php?action=delete&id=${id}`, { method: 'GET' });
                    const result = await response.json();

                    if (result.success) {
                        await loadAllDataFromPHP(); // Reload all data
                        loadClientsTable();
                        loadDashboard();
                        showAlert('Client and all related data deleted', 'danger');
                    } else {
                        showAlert('Failed to delete client: ' + result.message, 'danger');
                    }
                } catch (error) {
                    showAlert('Network error during deletion.', 'danger');
                }
            }
        }


        // ============================================
        // CONTENT CREDIT MANAGEMENT (PHP Handlers)
        // ============================================

        function loadContentCredits() {
            const clientId = parseInt(document.getElementById('contentClientSelect').value);
            const tbody = document.getElementById('contentTableBody');
            const info = document.getElementById('contentCreditsInfo');

            selectedClientContentId = clientId;
            saveToLocalStorage();

            if (!clientId) {
                info.style.display = 'none';
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <h4>Select a Client</h4>
                        <p>Choose a client to view content credits</p>
                    </div>
                </td></tr>`;
                return;
            }

            const client = appData.clients.find(c => c.id === clientId);
            const contents = appData.contentCredits.filter(c => c.clientId === clientId);
            const totalCredits = client.packageCredits + client.extraCredits + client.carriedForwardCredits;
            const available = totalCredits - client.usedCredits;

            info.style.display = 'block';
            document.getElementById('packageCredits').textContent = client.packageCredits;
            document.getElementById('extraCredits').textContent = client.extraCredits;
            document.getElementById('carriedCredits').textContent = client.carriedForwardCredits;
            document.getElementById('totalCredits').textContent = totalCredits;
            document.getElementById('usedCredits').textContent = client.usedCredits;
            document.getElementById('availableCredits').textContent = available;

            if (contents.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <h4>No Content Records</h4>
                        <p>Add your first content item</p>
                    </div>
                </td></tr>`;
                return;
            }

            // Group contents by month/year
            const now = new Date();
            const currentMonth = now.getMonth();
            const currentYear = now.getFullYear();
            
            const groupedContents = {};
            contents.forEach(c => {
                const contentDate = new Date(c.startDate);
                const monthKey = `${contentDate.getFullYear()}-${String(contentDate.getMonth()).padStart(2, '0')}`;
                const monthLabel = contentDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
                
                if (!groupedContents[monthKey]) {
                    groupedContents[monthKey] = {
                        label: monthLabel,
                        date: contentDate,
                        items: []
                    };
                }
                groupedContents[monthKey].items.push(c);
            });

            // Sort items within each group by ID (newest first)
            Object.values(groupedContents).forEach(group => {
                group.items.sort((a, b) => b.id - a.id);
            });

            // Sort groups by date (newest first)
            const sortedGroups = Object.entries(groupedContents).sort((a, b) => b[1].date - a[1].date);
            
            // Generate HTML with grouped sections
            let html = '';
            sortedGroups.forEach(([monthKey, group], index) => {
                const isCurrentMonth = group.date.getMonth() === currentMonth && group.date.getFullYear() === currentYear;
                const collapseId = `month-${monthKey}`;
                const itemCount = group.items.length;
                const totalCreditsInMonth = group.items.reduce((sum, item) => sum + item.credits, 0);
                
                // Month header row
                html += `
                    <tr class="table-secondary" style="cursor: pointer; background: ${isCurrentMonth ? '#e7f3ff' : '#f8f9fa'};" 
                        data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                        <td colspan="8">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong style="font-size: 1.1em; color: ${isCurrentMonth ? '#0d6efd' : '#6c757d'};">
                                    <i class="fas fa-calendar-alt me-2"></i>${group.label}
                                    ${isCurrentMonth ? '<span class="badge bg-primary ms-2">Current Month</span>' : ''}
                                </strong>
                                <span class="text-muted">
                                    <i class="fas fa-list me-1"></i>${itemCount} item${itemCount !== 1 ? 's' : ''} 
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-coins me-1"></i>${totalCreditsInMonth} credit${totalCreditsInMonth !== 1 ? 's' : ''}
                                    <i class="fas fa-chevron-down ms-2" id="icon-${collapseId}"></i>
                                </span>
                            </div>
                        </td>
                    </tr>
                `;
                
                // Month content rows (collapsible)
                const collapseClass = isCurrentMonth ? 'collapse show' : 'collapse';
                html += group.items.map(c => `
                    <tr class="${collapseClass}" id="${collapseId}" onmouseover="this.style.cursor='default'">
                        <td onclick="event.stopPropagation();">
                            <input type="checkbox" class="form-check-input content-checkbox" data-content-id="${c.id}" onchange="updateSelectedCount()">
                        </td>
                        <td>${c.creative}</td>
                        <td>${c.contentType}</td>
                        <td><span class="badge bg-warning">${c.credits}</span></td>
                        <td>${c.publishedDate ? formatDate(c.publishedDate) : '-'}</td>
                        <td>
                            ${c.imageUrl ? 
                                `<img src="${c.imageUrl}" alt="Content Preview" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" onclick="viewContentImage('${c.imageUrl}')" title="Click to view full image">` : 
                                '<span class="text-muted">-</span>'
                            }
                        </td>
                        <td>
                            ${c.contentUrl ? 
                                `<a href="${c.contentUrl}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Content">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>` : 
                                '<span class="text-muted">-</span>'
                            }
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info me-1" onclick="viewContent(${c.id})" title="View Details"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-warning me-1" onclick="editContent(${c.id})" title="Edit"><i class="fas fa-pen"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteContent(${c.id})" title="Delete"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('');
            });
            
            tbody.innerHTML = html;
            
            // Add collapse event listeners to rotate chevron icons
            sortedGroups.forEach(([monthKey]) => {
                const collapseId = `month-${monthKey}`;
                const collapseElement = document.getElementById(collapseId);
                const icon = document.getElementById(`icon-${collapseId}`);
                
                if (collapseElement && icon) {
                    collapseElement.addEventListener('show.bs.collapse', () => {
                        icon.classList.remove('fa-chevron-down');
                        icon.classList.add('fa-chevron-up');
                    });
                    collapseElement.addEventListener('hide.bs.collapse', () => {
                        icon.classList.remove('fa-chevron-up');
                        icon.classList.add('fa-chevron-down');
                    });
                }
            });
            
            // Initialize selected count after loading content
            updateSelectedCount();
        }

        // Multi-select functions for content report
        function toggleSelectAllContents() {
            const selectAll = document.getElementById('selectAllContents');
            const checkboxes = document.querySelectorAll('.content-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.content-checkbox:checked');
            const count = checkboxes.length;
            const selectedCountSpan = document.getElementById('selectedCount');
            const generateSelectedBtn = document.getElementById('generateSelectedReportBtn');
            const selectAllCheckbox = document.getElementById('selectAllContents');
            
            if (selectedCountSpan) {
                selectedCountSpan.textContent = count;
            }
            
            if (generateSelectedBtn) {
                generateSelectedBtn.style.display = count > 0 ? 'inline-block' : 'none';
            }
            
            // Update "Select All" checkbox state
            const allCheckboxes = document.querySelectorAll('.content-checkbox');
            if (selectAllCheckbox && allCheckboxes.length > 0) {
                selectAllCheckbox.checked = count === allCheckboxes.length;
                selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
            }
        }

        async function generateSelectedContentReport() {
            const checkboxes = document.querySelectorAll('.content-checkbox:checked');
            
            if (checkboxes.length === 0) {
                showAlert('Please select at least one content item.', 'warning');
                return;
            }
            
            const clientId = parseInt(document.getElementById('contentClientSelect').value);
            if (!clientId) {
                showAlert('Please select a client first.', 'warning');
                return;
            }
            
            const client = appData.clients.find(c => c.id === clientId);
            if (!client) {
                showAlert('Client not found.', 'danger');
                return;
            }
            
            // Get selected content IDs
            const selectedIds = Array.from(checkboxes).map(cb => parseInt(cb.dataset.contentId));
            
            // Filter contents by selected IDs
            const selectedContents = appData.contentCredits.filter(c => selectedIds.includes(c.id));
            
            if (selectedContents.length === 0) {
                showAlert('No valid content items found.', 'danger');
                return;
            }
            
            // Calculate totals for selected items
            const totalSelectedCredits = selectedContents.reduce((sum, c) => sum + c.credits, 0);
            
            // Generate report data
            const reportData = {
                client: client,
                contents: selectedContents,
                companyInfo: COMPANY_INFO,
                isSelectedReport: true,
                selectedCount: selectedContents.length,
                totalSelectedCredits: totalSelectedCredits
            };
            
            // Generate PDF using server-side generation
            try {
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                
                const response = await fetch('generate_content_report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(reportData)
                });
                
                if (response.ok) {
                    const htmlContent = await response.text();
                    printWindow.document.write(htmlContent);
                    
                    printWindow.onload = function() {
                        setTimeout(() => {
                            printWindow.print();
                        }, 1000);
                    };
                    
                    showAlert(`Selected content report (${selectedContents.length} items) generated successfully! 📄`, 'success');
                    return;
                }
            } catch (error) {
                console.log('Server PDF generation failed, falling back to browser print', error);
            }
            
            // Fallback to browser print
            let tableRows = selectedContents.map(c => `
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">
                        ${c.imageUrl ? `<img src="${c.imageUrl}" alt="${c.creative}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; display: block; margin-bottom: 5px;">` : ''}
                        <div style="font-weight: bold;">${c.creative}</div>
                    </td>
                    <td style="padding: 10px;">${c.contentType}</td>
                    <td style="padding: 10px; text-align: center;">${c.credits}</td>
                    <td style="padding: 10px;">${c.publishedDate ? formatDate(c.publishedDate) : '-'}</td>
                    <td style="padding: 10px;"><span style="color: ${getStatusColor(c.status) === 'success' ? '#10b981' : '#f59e0b'};">${c.status}</span></td>
                </tr>
            `).join('');

            const html = `
                <div style="font-family: Arial; padding: 20px;">
                    <div style="display: flex; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px;">
                        ${client.logoUrl ? `<img src="${client.logoUrl}" style="width: 80px; height: 80px; margin-right: 20px; border-radius: 8px;">` : ''}
                        <div>
                            <h1 style="margin: 0; color: #1f2937; font-size: 28px;">${client.companyName}</h1>
                            <p style="margin: 5px 0 0 0; color: #6b7280;">Content Credit Report - Selected Items</p>
                            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
                                <strong>${selectedContents.length}</strong> items selected | 
                                <strong>${totalSelectedCredits}</strong> total credits
                            </p>
                        </div>
                    </div>
                    
                    <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 5px;"><strong>Partner ID:</strong></td>
                                <td style="padding: 5px;">${client.partnerId}</td>
                                <td style="padding: 5px;"><strong>Industry:</strong></td>
                                <td style="padding: 5px;">${client.industry}</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px;"><strong>Report Date:</strong></td>
                                <td style="padding: 5px;">${new Date().toLocaleDateString()}</td>
                                <td style="padding: 5px;"><strong>Renewal Date:</strong></td>
                                <td style="padding: 5px;">${formatDate(client.renewalDate)}</td>
                            </tr>
                        </table>
                    </div>

                    <h2 style="color: #1f2937; font-size: 20px; margin-bottom: 15px;">Selected Content Details</h2>
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb;">
                        <thead style="background: #f3f4f6;">
                            <tr>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Creative</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Type</th>
                                <th style="padding: 12px; text-align: center; border-bottom: 2px solid #e5e7eb;">Credits</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Published Date</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #e5e7eb;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>

                    <div style="margin-top: 30px; padding: 15px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                            <strong>Note:</strong> This report includes only the selected content items (${selectedContents.length} of ${appData.contentCredits.filter(c => c.clientId === clientId).length} total items).
                        </p>
                    </div>
                    
                    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 12px;">
                        <p>${COMPANY_INFO.name} | ${COMPANY_INFO.address}</p>
                        <p>${COMPANY_INFO.phone} | ${COMPANY_INFO.email}</p>
                        <p style="margin-top: 10px;">Generated on ${new Date().toLocaleString()}</p>
                    </div>
                </div>
            `;

            const printWindow = window.open('', '_blank', 'width=800,height=600');
            printWindow.document.write(html);
            printWindow.document.close();
            printWindow.onload = function() {
                setTimeout(() => {
                    printWindow.print();
                }, 500);
            };
            
            showAlert(`Selected content report (${selectedContents.length} items) generated successfully! 📄`, 'success');
        }

        function showAddContentModal() {
            if (!hasPermission('canManageContent')) {
                showAlert('Access Denied. Marketer/Admin role required.', 'warning');
                return;
            }
            const clientId = document.getElementById('contentClientSelect').value;
            if(!clientId) { showAlert('Please select a client first', 'warning'); return; }
            
            // Reset form and clear image preview
            document.getElementById('addContentForm').reset();
            clearContentImagePreview();
            
            // Set modal to "Add" mode
            document.getElementById('addContentModal').querySelector('.modal-title').textContent = 'Add Content Credit';
            document.getElementById('addContentForm').dataset.mode = 'add';
            document.getElementById('addContentForm').dataset.editId = '';
            
            new bootstrap.Modal(document.getElementById('addContentModal')).show();
        }

        // ✅ NEW: Edit content function
        function editContent(id) {
            if (!hasPermission('canManageContent')) {
                showAlert('Access Denied. Marketer/Admin role required.', 'warning');
                return;
            }
            
            const content = appData.contentCredits.find(c => c.id === id);
            if (!content) {
                showAlert('Content not found.', 'danger');
                return;
            }

            // Populate form with existing data
            document.getElementById('creativeName').value = content.creative;
            document.getElementById('contentType').value = content.contentType;
            document.getElementById('creditsAllocated').value = content.credits;
            document.getElementById('publishedDate').value = content.publishedDate || '';
            document.getElementById('contentUrl').value = content.contentUrl || '';
            
            // Handle image preview if image exists
            if (content.imageUrl) {
                const preview = document.getElementById('contentImagePreview');
                const previewImg = document.getElementById('contentImagePreviewImg');
                previewImg.src = content.imageUrl;
                preview.style.display = 'block';
                document.getElementById('contentImageUpload').dataset.imageUrl = content.imageUrl;
            } else {
                clearContentImagePreview();
            }
            
            // Set modal to "Edit" mode
            document.getElementById('addContentModal').querySelector('.modal-title').textContent = 'Edit Content Credit';
            document.getElementById('addContentForm').dataset.mode = 'edit';
            document.getElementById('addContentForm').dataset.editId = id;
            
            new bootstrap.Modal(document.getElementById('addContentModal')).show();
        }

        // FIX: Add/Edit Content using PHP handler
        document.getElementById('addContentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const mode = this.dataset.mode || 'add';
            const editId = this.dataset.editId || null;
            const clientId = parseInt(document.getElementById('contentClientSelect').value);
            const credits = parseInt(document.getElementById('creditsAllocated').value);
            const client = appData.clients.find(c => c.id === clientId);
            const totalCredits = client.packageCredits + client.extraCredits + client.carriedForwardCredits;
            const available = totalCredits - client.usedCredits;

            // For edit mode, add back the original credits before checking
            let availableForEdit = available;
            if (mode === 'edit' && editId) {
                const originalContent = appData.contentCredits.find(c => c.id === parseInt(editId));
                if (originalContent) {
                    availableForEdit = available + originalContent.credits;
                }
            }

            if (credits > availableForEdit) {
                showAlert(`Insufficient credits! Only ${availableForEdit} credits available`, 'danger');
                return;
            }

            const contentData = {
                clientId: clientId,
                credits: credits,
                creative: document.getElementById('creativeName').value,
                contentType: document.getElementById('contentType').value,
                startDate: new Date().toISOString().split('T')[0], // Use current date as default
                status: 'In Progress', // Default status
                publishedDate: document.getElementById('publishedDate').value || null,
                contentUrl: document.getElementById('contentUrl').value || null,
                imageUrl: document.getElementById('contentImageUpload').dataset.imageUrl || null
            };

            try {
                let url = 'handler_content.php?action=add';
                let successMessage = 'Content credit added successfully! 📸';
                
                if (mode === 'edit' && editId) {
                    url = `handler_content.php?action=edit&id=${editId}`;
                    successMessage = 'Content credit updated successfully! ✏️';
                }
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(contentData)
                });

                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(text || `HTTP ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    await loadAllDataFromPHP(); // Reload all data (new content + updated client credits)
                    loadContentCredits();
                    loadClientsTable(); 
					document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
					bootstrap.Modal.getInstance(document.getElementById('addContentModal')).hide();
                    this.reset();
                    
                    // ✅ NEW: Clear image preview and reset image upload
                    clearContentImagePreview();
                    
                    showAlert(successMessage, 'success');
                    loadDashboard();
                } else {
                    showAlert('Failed to save content: ' + result.message, 'danger');
                }

            } catch (error) {
                console.error('Network Error:', error);
                showAlert('Network error or server issue when saving content.', 'danger');
            }
        });

        // ✅ FIXED: Implemented fetch call for content deletion
        async function deleteContent(id) {
            if (!hasPermission('canManageContent')) {
                showAlert('Access Denied. Marketer/Admin role required.', 'warning');
                return;
            }
            const confirmed = await showConfirm('Are you sure you want to delete this content record? This will revert the used credits back to the client.', 'Delete Content', 'warning');
            if (confirmed) {
                try {
                    const response = await fetch(`handler_content.php?action=delete&id=${id}`, { method: 'GET', credentials: 'same-origin' });
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(text || `HTTP ${response.status}`);
                    }
                    const result = await response.json();
                    if (result.success) {
                        await loadAllDataFromPHP();
                        loadContentCredits();
                        loadDashboard();
                        showAlert('Content deleted and credits reverted.', 'success');
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    showAlert('Error deleting content: ' + error.message, 'danger');
                }
            }
        }

        // ✅ NEW: View content details function
        function viewContent(id) {
            const content = appData.contentCredits.find(c => c.id === id);
            if (!content) {
                showAlert('Content not found.', 'danger');
                return;
            }

            const client = appData.clients.find(c => c.id === content.clientId);
            if (!client) {
                showAlert('Client information not found.', 'danger');
                return;
            }

            // Populate the view modal with content details
            document.getElementById('viewCreativeName').textContent = content.creative;
            document.getElementById('viewContentType').textContent = content.contentType;
            document.getElementById('viewCredits').textContent = content.credits;
            
            // Set status badge with appropriate color
            const statusElement = document.getElementById('viewStatus');
            statusElement.textContent = content.status;
            statusElement.className = `badge bg-${getStatusColor(content.status)}`;
            
            // Set published date or show "Not published"
            const publishedDateElement = document.getElementById('viewPublishedDate');
            if (content.publishedDate) {
                publishedDateElement.textContent = formatDate(content.publishedDate);
            } else {
                publishedDateElement.textContent = 'Not published';
                publishedDateElement.className = 'text-muted';
            }

            // Populate client information
            document.getElementById('viewClientName').textContent = client.companyName;
            document.getElementById('viewPartnerId').textContent = client.partnerId;

            // Handle content media (image and URL)
            const mediaSection = document.getElementById('contentMediaSection');
            const imageSection = document.getElementById('contentImageSection');
            const urlSection = document.getElementById('contentUrlSection');
            const viewContentImage = document.getElementById('viewContentImage');
            const viewContentUrl = document.getElementById('viewContentUrl');

            // Reset media sections
            imageSection.style.display = 'none';
            urlSection.style.display = 'none';
            mediaSection.style.display = 'none';

            // Show image if available
            if (content.imageUrl) {
                viewContentImage.src = content.imageUrl;
                imageSection.style.display = 'block';
                mediaSection.style.display = 'block';
            }

            // Show URL if available
            if (content.contentUrl) {
                viewContentUrl.href = content.contentUrl;
                viewContentUrl.textContent = content.contentUrl;
                urlSection.style.display = 'block';
                mediaSection.style.display = 'block';
            }

            // Show the modal
            new bootstrap.Modal(document.getElementById('viewContentModal')).show();
        }

        function showManageCreditsModal() {
            if (!hasPermission('canManageContent')) {
                showAlert('Access Denied. Marketer/Admin role required for month-end processing.', 'warning');
                return;
            }
            const clientId = document.getElementById('contentClientSelect').value;
            if(!clientId) { showAlert('Please select a client first', 'warning'); return; }

            const client = appData.clients.find(c => c.id === parseInt(clientId));
            const totalCredits = client.packageCredits + client.extraCredits + client.carriedForwardCredits;
            const available = totalCredits - client.usedCredits;

            // Populate client information
            document.getElementById('manageCreditsClientName').textContent = client.companyName;
            document.getElementById('currentRenewalDate').textContent = formatDate(client.renewalDate);
            document.getElementById('managePackageCredits').textContent = client.packageCredits;
            document.getElementById('manageExtraCredits').textContent = client.extraCredits;
            document.getElementById('manageCarriedCredits').textContent = client.carriedForwardCredits;
            document.getElementById('manageUsedCredits').textContent = client.usedCredits;
            document.getElementById('manageCreditsAvailable').textContent = available;
            
            // Set up credits to carry field
            const creditsToCarryField = document.getElementById('creditsToCarry');
            const maxCreditsSpan = document.getElementById('maxCreditsToCarry');
            creditsToCarryField.max = Math.max(0, available);
            creditsToCarryField.value = Math.max(0, available);
            maxCreditsSpan.textContent = Math.max(0, available);
            
            // Set up new renewal date (default to next month from current renewal date)
            const currentRenewalDate = new Date(client.renewalDate);
            const nextRenewalDate = new Date(currentRenewalDate);
            nextRenewalDate.setMonth(nextRenewalDate.getMonth() + 1);
            document.getElementById('newRenewalDate').value = nextRenewalDate.toISOString().split('T')[0];

            new bootstrap.Modal(document.getElementById('manageCreditsModal')).show();
        }

        // ✅ IMPLEMENTED: Month-end form submission
        document.getElementById('manageCreditsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const clientId = parseInt(document.getElementById('contentClientSelect').value);
            const creditsToCarry = parseInt(document.getElementById('creditsToCarry').value);
            const newRenewalDate = document.getElementById('newRenewalDate').value;
            
            // Validation
            if (!clientId) {
                showAlert('Please select a client first.', 'warning');
                return;
            }
            
            if (creditsToCarry < 0) {
                showAlert('Credits to carry cannot be negative.', 'warning');
                return;
            }
            
            if (!newRenewalDate) {
                showAlert('Please select a new renewal date.', 'warning');
                return;
            }
            
            // Confirm the action
            const confirmed = await showConfirm(
                `Are you sure you want to process month-end for this client?\n\nThis will:\n- Reset used credits to 0\n- Carry forward ${creditsToCarry} credits\n- Update renewal date to ${newRenewalDate}\n\nThis action cannot be undone.`,
                'Month-End Process Confirmation',
                'warning'
            );
            if (!confirmed) return;
            
            try {
                const response = await fetch('handler_month_end.php?action=process', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        clientId: clientId,
                        creditsToCarry: creditsToCarry,
                        newRenewalDate: newRenewalDate
                    })
                });
                
                if (!response.ok) {
                    const text = await response.text();
                    throw new Error(text || `HTTP ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    // Close modal and refresh data
                    bootstrap.Modal.getInstance(document.getElementById('manageCreditsModal')).hide();
                    
                    // Reload all data to reflect changes
                    await loadAllDataFromPHP();
                    loadContentCredits();
                    loadClientsTable();
                    loadDashboard();
                    
                    // Show success message with details
                    const details = result.details;
                    showAlert(`Month-end process completed successfully!\n\nCredits carried forward: ${details.creditsCarried}\nCredits expired: ${details.creditsExpired}\nNew renewal date: ${formatDate(details.newRenewalDate)}`, 'success');
                } else {
                    showAlert('Failed to process month-end: ' + result.message, 'danger');
                }
                
            } catch (error) {
                console.error('Month-end process error:', error);
                showAlert('Error processing month-end: ' + error.message, 'danger');
            }
        });

        function checkRenewalDates() {
            // Check for clients with upcoming renewal dates
            const today = new Date();
            const upcomingRenewals = appData.clients.filter(client => {
                const renewalDate = new Date(client.renewalDate);
                const daysUntilRenewal = Math.ceil((renewalDate - today) / (1000 * 60 * 60 * 24));
                return daysUntilRenewal <= 7 && daysUntilRenewal >= 0;
            });
            
            if (upcomingRenewals.length > 0) {
                const clientNames = upcomingRenewals.map(c => c.companyName).join(', ');
                showAlert(`Upcoming renewals in the next 7 days: ${clientNames}`, 'info');
            }
        }

        // Helper function to show document modal with proper ARIA handling
        function showDocumentModal() {
            const modalElement = document.getElementById('viewDocumentModal');
            const modal = new bootstrap.Modal(modalElement);
            
            // Fix ARIA accessibility issue (only add listeners once)
            if (!modalElement.hasAttribute('data-aria-fixed')) {
                modalElement.addEventListener('shown.bs.modal', function() {
                    modalElement.removeAttribute('aria-hidden');
                    // Ensure document preview is read-only
                    const documentPreview = document.getElementById('documentPreview');
                    if (documentPreview) {
                        documentPreview.setAttribute('contenteditable', 'false');
                        documentPreview.style.userSelect = 'text';
                        documentPreview.style.webkitUserSelect = 'text';
                        documentPreview.style.mozUserSelect = 'text';
                        documentPreview.style.msUserSelect = 'text';
                    }
                });
                modalElement.addEventListener('hidden.bs.modal', function() {
                    modalElement.setAttribute('aria-hidden', 'true');
                });
                modalElement.setAttribute('data-aria-fixed', 'true');
            }
            
            modal.show();
        }

        async function generateContentReport(reportClientId = null) {
            // ... (Report generation remains purely front-end/local) ...
            const clientId = reportClientId || parseInt(document.getElementById('contentClientSelect').value);
            if (!clientId) { showAlert('Please select a client first.', 'warning'); return; }

            const client = appData.clients.find(c => c.id === clientId);
            const contents = appData.contentCredits.filter(c => c.clientId === clientId);

            const totalCredits = client.packageCredits + client.extraCredits + client.carriedForwardCredits;
            const available = totalCredits - client.usedCredits;

            let tableRows = contents.map(c => `
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">
                        ${c.imageUrl ? `<img src="${c.imageUrl}" alt="${c.creative}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; display: block; margin-bottom: 5px;">` : ''}
                        <div style="font-weight: bold;">${c.creative}</div>
                    </td>
                    <td style="padding: 10px;">${c.contentType}</td>
                    <td style="padding: 10px; text-align: center;">${c.credits}</td>
                    <td style="padding: 10px;">${c.publishedDate ? formatDate(c.publishedDate) : '-'}</td>
                    <td style="padding: 10px;"><span style="color: ${getStatusColor(c.status) === 'success' ? '#10b981' : '#f59e0b'};">${c.status}</span></td>
                </tr>
            `).join('');

            if (contents.length === 0) {
                tableRows = '<tr><td colspan="5" style="padding: 20px; text-align: center;">No content records found.</td></tr>';
            }

            // Generate PDF using server-side generation
            const reportData = {
                client: client,
                contents: contents,
                companyInfo: COMPANY_INFO
            };
            
            // Generate PDF using browser's built-in PDF generation
            try {
                // Create a new window for PDF generation
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                
                // Send data to server for HTML generation
                const response = await fetch('generate_content_report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(reportData)
                });
                
                if (response.ok) {
                    const htmlContent = await response.text();
                    printWindow.document.write(htmlContent);
                    
                    // Wait for content to load, then trigger print
                    printWindow.onload = function() {
                        setTimeout(() => {
                            printWindow.print();
                        }, 1000);
                    };
                    
                    showAlert('Content report generated successfully! 📄', 'success');
                    return;
                }
            } catch (error) {
                console.log('Server PDF generation failed, falling back to browser print');
            }
            
            // Fallback to browser print
            const html = `
                <div style="font-family: Arial; padding: 20px;">
                    <div style="display: flex; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px;">
                        ${COMPANY_INFO.logoUrl ? `<img src="${COMPANY_INFO.logoUrl}" alt="Logo" style="height: 60px; margin-right: 20px; object-fit: contain;">` : ''}
                        <div>
                            <h1 style="color: #6366f1; margin: 0; font-size: 2rem;">${COMPANY_INFO.name}</h1>
                            <p style="color: #666; margin: 5px 0;">Content Credit Usage Report</p>
                        </div>
                    </div>
                    <div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h3 style="color: #6366f1; margin-bottom: 15px;">${client.companyName} (${client.partnerId})</h3>
                        <p><strong>Reporting Cycle:</strong> Ends on ${formatDate(client.renewalDate)}</p>
                        <p><strong>Generated:</strong> ${formatDate(new Date())}</p>
                    </div>
                    <div style="margin: 20px 0; padding: 15px; background: #cff4fc; border-radius: 8px;">
                        <h4 style="margin-bottom: 10px;">Credit Summary</h4>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr><td style="padding: 5px;"><strong>Package Credits:</strong></td><td>${client.packageCredits}</td></tr>
                            <tr><td style="padding: 5px;"><strong>Extra Credits:</strong></td><td>${client.extraCredits}</td></tr>
                            <tr><td style="padding: 5px;"><strong>Carried Credits:</strong></td><td>${client.carriedForwardCredits}</td></tr>
                            <tr><td style="padding: 5px;"><strong>TOTAL Credits:</strong></td><td>${totalCredits}</td></tr>
                            <tr style="border-top: 2px solid #0dcaf0;"><td style="padding: 5px;"><strong>Used Credits:</strong></td><td>${client.usedCredits}</td></tr>
                            <tr><td style="padding: 5px;"><strong>Available Credits:</strong></td><td style="color: #10b981; font-weight: bold;">${available}</td></tr>
                        </table>
                    </div>
                    <h4 style="margin-top: 30px; margin-bottom: 15px;">Content Item Details</h4>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px;">
                        <thead>
                            <tr style="background: #6366f1; color: white;">
                                <th style="padding: 10px; text-align: left; width: 30%;">Creative</th>
                                <th style="padding: 10px; width: 20%;">Type</th>
                                <th style="padding: 10px; width: 10%;">Credits</th>
                                <th style="padding: 10px; width: 20%;">Published Date</th>
                                <th style="padding: 10px; width: 20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody>${tableRows}</tbody>
                    </table>
                    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #666; font-size: 14px;">
                        <p>${COMPANY_INFO.name} | ${COMPANY_INFO.tagline}</p>
                        <p style="margin: 5px 0;">Generated on ${formatDate(new Date())}</p>
                    </div>
                </div>
            `;

            // Add read-only header to document
            const readOnlyHeader = `
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 8px 12px; margin-bottom: 20px; font-size: 14px; color: #6c757d;">
                    <i class="fas fa-lock me-2"></i>Document View - Read Only
                </div>
            `;
            document.getElementById('documentPreview').innerHTML = readOnlyHeader + html;
            showDocumentModal();
        }

        // ============================================
        // CAMPAIGN MANAGEMENT (PHP Handlers)
        // ============================================

        function loadCampaigns() {
            const clientId = parseInt(document.getElementById('campaignClientSelect').value);
            const tbody = document.getElementById('campaignsTableBody');
            const budgetInfo = document.getElementById('campaignBudgetInfo');

            selectedClientCampaignId = clientId;
            saveToLocalStorage();

            if (!clientId) {
                const defaultBudget = '0.00';
                budgetInfo.style.display = 'block';
                document.getElementById('totalAdBudget').textContent = defaultBudget;
                document.getElementById('totalSpent').textContent = defaultBudget;
                document.getElementById('remainingBudget').textContent = defaultBudget;

                tbody.innerHTML = `<tr><td colspan="8" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-bullhorn"></i>
                        <h4>Select a Client</h4>
                        <p>Choose a client to view campaigns</p>
                    </div>
                </td></tr>`;
                return;
            }

            const client = appData.clients.find(c => c.id === clientId);

            const totalAdBudget = parseFloat(client.totalAdBudget) || 0.00;
            const totalSpent = parseFloat(client.totalSpent) || 0.00;
            const remaining = totalAdBudget - totalSpent;

            const campaigns = appData.campaigns.filter(c => c.clientId === clientId)
                .sort((a, b) => b.id - a.id); // Sort by ID descending (newest first)

            budgetInfo.style.display = 'block';
            document.getElementById('totalAdBudget').textContent = totalAdBudget.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('totalSpent').textContent = totalSpent.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('remainingBudget').textContent = remaining.toLocaleString(undefined, { minimumFractionDigits: 2 });

            if (campaigns.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-ad"></i>
                        <h4>No Campaigns Yet</h4>
                        <p>Add your first campaign</p>
                    </div>
                </td></tr>`;
                return;
            }

            tbody.innerHTML = campaigns.map(c => `
                <tr onclick="viewCampaignDetails(${c.id})"
                    data-platform="${c.platform.toLowerCase()}"
                    data-adname="${c.adName.toLowerCase()}"
                    data-results="${c.results}"
                    data-cpr="${c.cpr}"
                    data-reach="${c.reach}"
                    data-spend="${c.spend}"
                    data-date="${c.dateAdded}">
                    <td onclick="event.stopPropagation();">
                        <input type="checkbox" class="campaign-checkbox" value="${c.id}" onchange="updateSelectedCampaigns()">
                    </td>
                    <td><span class="badge bg-primary">${c.platform}</span></td>
                    <td>${c.adName}</td>
                    <td>${c.results.toLocaleString()} ${c.resultType}</td>
                    <td>Rs. ${c.cpr.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td>${c.reach.toLocaleString()}</td>
                    <td>Rs. ${c.spend.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td>${formatDateTime(c.dateAdded)}</td>
                    <td onclick="event.stopPropagation();">
                        <button class="btn btn-sm btn-info" onclick="viewCampaignDetails(${c.id})" title="View"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-warning" onclick="showEditCampaignModal(${c.id})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCampaign(${c.id})" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`).join('');
        }

        function showAddCampaignModal() {
            if (!hasPermission('canManageCampaigns')) {
                showAlert('Access Denied. Marketer/Admin role required.', 'warning');
                return;
            }
            const clientId = document.getElementById('campaignClientSelect').value;
            if(!clientId) { showAlert('Please select a client first', 'warning'); return; }
            document.getElementById('addCampaignForm').reset();
            clearEvidenceImagePreview();
            clearCreativeImagePreview();
            new bootstrap.Modal(document.getElementById('addCampaignModal')).show();
        }

        // Handle Evidence Image Upload
        async function handleEvidenceImageUpload(input) {
            if (!input.files || !input.files[0]) return;
            
            const file = input.files[0];
            const formData = new FormData();
            formData.append('contentImage', file);
            
            try {
                const response = await fetch('upload_content_image.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success && result.image_url) {
                    document.getElementById('evidenceImageUrl').value = result.image_url;
                    document.getElementById('evidenceImagePreviewImg').src = result.image_url;
                    document.getElementById('evidenceImagePreview').style.display = 'block';
                    showAlert('Evidence image uploaded successfully! ✅', 'success');
                } else {
                    throw new Error(result.message || 'Upload failed');
                }
            } catch (error) {
                showAlert('Error uploading evidence image: ' + error.message, 'danger');
                input.value = '';
            }
        }

        // Handle Creative Image Upload
        async function handleCreativeImageUpload(input) {
            if (!input.files || !input.files[0]) return;
            
            const file = input.files[0];
            const formData = new FormData();
            formData.append('contentImage', file);
            
            try {
                const response = await fetch('upload_content_image.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success && result.image_url) {
                    document.getElementById('creativeImageUrl').value = result.image_url;
                    document.getElementById('creativeImagePreviewImg').src = result.image_url;
                    document.getElementById('creativeImagePreview').style.display = 'block';
                    showAlert('Creative image uploaded successfully! ✅', 'success');
                } else {
                    throw new Error(result.message || 'Upload failed');
                }
            } catch (error) {
                showAlert('Error uploading creative image: ' + error.message, 'danger');
                input.value = '';
            }
        }

        function removeEvidenceImage() {
            document.getElementById('evidenceImageUrl').value = '';
            document.getElementById('evidenceImageUpload').value = '';
            document.getElementById('evidenceImagePreview').style.display = 'none';
        }

        function removeCreativeImage() {
            document.getElementById('creativeImageUrl').value = '';
            document.getElementById('creativeImageUpload').value = '';
            document.getElementById('creativeImagePreview').style.display = 'none';
        }

        function clearEvidenceImagePreview() {
            document.getElementById('evidenceImageUrl').value = '';
            document.getElementById('evidenceImageUpload').value = '';
            document.getElementById('evidenceImagePreview').style.display = 'none';
        }

        function clearCreativeImagePreview() {
            document.getElementById('creativeImageUrl').value = '';
            document.getElementById('creativeImageUpload').value = '';
            document.getElementById('creativeImagePreview').style.display = 'none';
        }

        // FIX: Add Campaign using PHP handler
        document.getElementById('addCampaignForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const clientId = parseInt(document.getElementById('campaignClientSelect').value);
            const spend = parseFloat(document.getElementById('spend').value);
            const client = appData.clients.find(c => c.id === clientId);

            const totalAdBudget = parseFloat(client.totalAdBudget) || 0.00;
            const totalSpent = parseFloat(client.totalSpent) || 0.00;
            const remaining = totalAdBudget - totalSpent;

            if (spend > remaining) {
                showAlert(`Insufficient ad budget! Only Rs. ${remaining.toLocaleString(undefined, { minimumFractionDigits: 2 })} remaining`, 'danger');
                return;
            }

            const newCampaignData = {
                clientId: clientId,
                platform: document.getElementById('campaignPlatform').value,
                adId: document.getElementById('adId').value,
                adName: document.getElementById('adName').value,
                resultType: document.getElementById('resultType').value,
                results: parseInt(document.getElementById('results').value),
                cpr: parseFloat(document.getElementById('cpr').value),
                reach: parseInt(document.getElementById('reach').value),
                impressions: parseInt(document.getElementById('impressions').value),
                spend: spend,
                qualityRanking: document.getElementById('qualityRanking').value,
                conversionRanking: document.getElementById('conversionRanking').value,
                evidenceImageUrl: document.getElementById('evidenceImageUrl').value || null,
                creativeImageUrl: document.getElementById('creativeImageUrl').value || null
            };

            try {
                const response = await fetch('handler_campaigns.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newCampaignData)
                });
                
                const result = await response.json();

                if (result.success) {
                    await loadAllDataFromPHP(); // Reload all data (new campaign + updated client spent)
                    loadCampaigns();
					document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
					bootstrap.Modal.getInstance(document.getElementById('addCampaignModal')).hide();
                    showAlert('Campaign added successfully! 📢', 'success');
                    loadDashboard();
                } else {
                    showAlert('Failed to save campaign: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Network Error:', error);
                showAlert('Network error or server issue when saving campaign.', 'danger');
            }
        });

        function viewCampaignDetails(id) {
            const campaign = appData.campaigns.find(c => c.id === id);
            if (!campaign) return;

            document.getElementById('detailPlatform').textContent = campaign.platform;
            document.getElementById('detailAdId').textContent = campaign.adId;
            document.getElementById('detailAdName').textContent = campaign.adName;
            document.getElementById('detailResultType').textContent = campaign.resultType;
            document.getElementById('detailResults').textContent = campaign.results.toLocaleString();
            document.getElementById('detailCPR').textContent = campaign.cpr.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('detailReach').textContent = campaign.reach.toLocaleString();
            document.getElementById('detailImpressions').textContent = campaign.impressions.toLocaleString();
            document.getElementById('detailSpend').textContent = campaign.spend.toLocaleString(undefined, { minimumFractionDigits: 2 });
            document.getElementById('detailQuality').textContent = campaign.qualityRanking;
            document.getElementById('detailConversion').textContent = campaign.conversionRanking;

            // Display Evidence Image
            const evidenceImageContainer = document.getElementById('evidenceImageContainer');
            if (campaign.evidenceImageUrl) {
                evidenceImageContainer.innerHTML = `
                    <img src="${campaign.evidenceImageUrl}" alt="Evidence" 
                         style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" 
                         onclick="window.open('${campaign.evidenceImageUrl}', '_blank')" 
                         title="Click to view full size">
                `;
            } else {
                evidenceImageContainer.innerHTML = '<p class="text-muted">No evidence image uploaded</p>';
            }

            // Display Creative Image
            const creativeImageContainer = document.getElementById('creativeImageContainer');
            if (campaign.creativeImageUrl) {
                creativeImageContainer.innerHTML = `
                    <img src="${campaign.creativeImageUrl}" alt="Creative" 
                         style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" 
                         onclick="window.open('${campaign.creativeImageUrl}', '_blank')" 
                         title="Click to view full size">
                `;
            } else {
                creativeImageContainer.innerHTML = '<p class="text-muted">No creative image uploaded</p>';
            }

            new bootstrap.Modal(document.getElementById('campaignDetailsModal')).show();
        }
        
        // ✅ FIXED: Implemented fetch call for campaign deletion with system confirmation
        async function deleteCampaign(id) {
            if (!hasPermission('canManageCampaigns')) {
                showAlert('Access Denied. Marketer/Admin role required.', 'warning');
                return;
            }
            
            const confirmed = await showConfirm(
                'Delete this campaign? This will revert the spent budget.',
                'Delete Campaign',
                'warning'
            );
            
            if (confirmed) {
                try {
                    const response = await fetch(`handler_campaigns.php?action=delete&id=${id}`, { method: 'GET' });
                    const result = await response.json();
                    if (result.success) {
                        await loadAllDataFromPHP();
                        loadCampaigns();
                        loadDashboard();
                        showAlert('Campaign deleted and budget reverted.', 'success');
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    showAlert('Error deleting campaign: ' + error.message, 'danger');
                }
            }
        }

        // Show Edit Campaign Modal
        function showEditCampaignModal(campaignId) {
            if (!hasPermission('canManageCampaigns')) {
                showAlert('Access Denied. Marketer/Admin role required.', 'warning');
                return;
            }

            const campaign = appData.campaigns.find(c => c.id === campaignId);
            if (!campaign) {
                showAlert('Campaign not found', 'danger');
                return;
            }

            // Populate form fields
            document.getElementById('editCampaignId').value = campaign.id;
            document.getElementById('editCampaignPlatform').value = campaign.platform;
            document.getElementById('editAdId').value = campaign.adId;
            document.getElementById('editAdName').value = campaign.adName;
            document.getElementById('editResultType').value = campaign.resultType;
            document.getElementById('editResults').value = campaign.results;
            document.getElementById('editCpr').value = campaign.cpr;
            document.getElementById('editReach').value = campaign.reach;
            document.getElementById('editImpressions').value = campaign.impressions;
            document.getElementById('editSpend').value = campaign.spend;
            document.getElementById('editQualityRanking').value = campaign.qualityRanking;
            document.getElementById('editConversionRanking').value = campaign.conversionRanking;

            // Set hidden field values for existing images
            document.getElementById('editEvidenceImageUrl').value = campaign.evidenceImageUrl || '';
            document.getElementById('editCreativeImageUrl').value = campaign.creativeImageUrl || '';

            // Show current evidence image if exists
            if (campaign.evidenceImageUrl) {
                document.getElementById('editCurrentEvidenceImage').style.display = 'block';
                document.getElementById('editCurrentEvidenceImageImg').src = campaign.evidenceImageUrl;
            } else {
                document.getElementById('editCurrentEvidenceImage').style.display = 'none';
            }

            // Show current creative image if exists
            if (campaign.creativeImageUrl) {
                document.getElementById('editCurrentCreativeImage').style.display = 'block';
                document.getElementById('editCurrentCreativeImageImg').src = campaign.creativeImageUrl;
            } else {
                document.getElementById('editCurrentCreativeImage').style.display = 'none';
            }

            // Clear new upload previews
            document.getElementById('editEvidenceImagePreview').style.display = 'none';
            document.getElementById('editCreativeImagePreview').style.display = 'none';
            document.getElementById('editEvidenceImageUpload').value = '';
            document.getElementById('editCreativeImageUpload').value = '';

            new bootstrap.Modal(document.getElementById('editCampaignModal')).show();
        }

        // Handle Edit Evidence Image Upload
        async function handleEditEvidenceImageUpload(input) {
            if (!input.files || !input.files[0]) return;
            
            const file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                showAlert('Evidence image must be less than 5MB', 'danger');
                input.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('contentImage', file);

            try {
                const response = await fetch('upload_content_image.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    document.getElementById('editEvidenceImageUrl').value = result.imageUrl;
                    document.getElementById('editEvidenceImagePreviewImg').src = result.imageUrl;
                    document.getElementById('editEvidenceImagePreview').style.display = 'block';
                    showAlert('Evidence image uploaded successfully! ✅', 'success');
                } else {
                    throw new Error(result.message || 'Upload failed');
                }
            } catch (error) {
                showAlert('Error uploading evidence image: ' + error.message, 'danger');
                input.value = '';
            }
        }

        // Handle Edit Creative Image Upload
        async function handleEditCreativeImageUpload(input) {
            if (!input.files || !input.files[0]) return;
            
            const file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                showAlert('Creative image must be less than 5MB', 'danger');
                input.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('contentImage', file);

            try {
                const response = await fetch('upload_content_image.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    document.getElementById('editCreativeImageUrl').value = result.imageUrl;
                    document.getElementById('editCreativeImagePreviewImg').src = result.imageUrl;
                    document.getElementById('editCreativeImagePreview').style.display = 'block';
                    showAlert('Creative image uploaded successfully! ✅', 'success');
                } else {
                    throw new Error(result.message || 'Upload failed');
                }
            } catch (error) {
                showAlert('Error uploading creative image: ' + error.message, 'danger');
                input.value = '';
            }
        }

        // Remove edit evidence image
        function removeEditEvidenceImage() {
            document.getElementById('editEvidenceImageUpload').value = '';
            document.getElementById('editEvidenceImageUrl').value = document.getElementById('editEvidenceImageUrl').dataset.original || '';
            document.getElementById('editEvidenceImagePreview').style.display = 'none';
        }

        // Remove edit creative image
        function removeEditCreativeImage() {
            document.getElementById('editCreativeImageUpload').value = '';
            document.getElementById('editCreativeImageUrl').value = document.getElementById('editCreativeImageUrl').dataset.original || '';
            document.getElementById('editCreativeImagePreview').style.display = 'none';
        }

        // Handle Edit Campaign Form Submit
        document.getElementById('editCampaignForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!hasPermission('canManageCampaigns')) {
                showAlert('Access Denied. Marketer/Admin role required.', 'warning');
                return;
            }

            const campaignId = document.getElementById('editCampaignId').value;
            const clientId = parseInt(document.getElementById('campaignClientSelect').value);

            const spend = parseFloat(document.getElementById('editSpend').value);

            const updatedCampaignData = {
                id: campaignId,
                clientId: clientId,
                platform: document.getElementById('editCampaignPlatform').value,
                adId: document.getElementById('editAdId').value,
                adName: document.getElementById('editAdName').value,
                resultType: document.getElementById('editResultType').value,
                results: parseInt(document.getElementById('editResults').value),
                cpr: parseFloat(document.getElementById('editCpr').value),
                reach: parseInt(document.getElementById('editReach').value),
                impressions: parseInt(document.getElementById('editImpressions').value),
                spend: spend,
                qualityRanking: document.getElementById('editQualityRanking').value,
                conversionRanking: document.getElementById('editConversionRanking').value,
                evidenceImageUrl: document.getElementById('editEvidenceImageUrl').value || null,
                creativeImageUrl: document.getElementById('editCreativeImageUrl').value || null
            };

            try {
                const response = await fetch('handler_campaigns.php?action=edit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(updatedCampaignData)
                });
                
                const result = await response.json();
                if (result.success) {
                    await loadAllDataFromPHP();
                    loadCampaigns();
                    loadDashboard();
                    bootstrap.Modal.getInstance(document.getElementById('editCampaignModal')).hide();
                    showAlert('Campaign updated successfully! 🎉', 'success');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showAlert('Error updating campaign: ' + error.message, 'danger');
            }
        });

        function generateCampaignReport() {
            const clientId = parseInt(document.getElementById('campaignClientSelect').value);
            const startDateStr = document.getElementById('reportStartDate').value;
            const endDateStr = document.getElementById('reportEndDate').value;

            if(!clientId) { showAlert('Please select a client first.', 'warning'); return; }
            if(!startDateStr || !endDateStr) { showAlert('Please select both Start and End dates for the report range.', 'warning'); return; }

            const startDate = new Date(startDateStr + 'T00:00:00');
            const endDate = new Date(endDateStr + 'T23:59:59');

            if (startDate > endDate) {
                showAlert('Start Date cannot be after End Date.', 'danger');
                return;
            }

            const client = appData.clients.find(c => c.id === clientId);

            const campaigns = appData.campaigns.filter(c => {
                const campaignDate = new Date(c.dateAdded);
                return c.clientId === clientId && campaignDate >= startDate && campaignDate <= endDate;
            });

            const totalSpend = campaigns.reduce((sum, c) => sum + c.spend, 0);
            const totalReach = campaigns.reduce((sum, c) => sum + c.reach, 0);
            const totalResults = campaigns.reduce((sum, c) => sum + c.results, 0);
            const totalImpressions = campaigns.reduce((sum, c) => sum + (c.impressions || 0), 0);

            const totalAdBudget = parseFloat(client.totalAdBudget) || 0.00;
            const totalSpent = parseFloat(client.totalSpent) || 0.00;
            const remainingBudget = totalAdBudget - totalSpent;

            const avgCPR = totalResults > 0 ? totalSpend / totalResults : 0;
            
            // Get active platforms
            const platforms = [...new Set(campaigns.map(c => c.platform))].join(', ') || 'N/A';
            
            // Get current month/year for report period
            const reportMonth = new Date(startDateStr).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            
            // Group results by type for metrics
            const resultsByType = {};
            campaigns.forEach(c => {
                if (!resultsByType[c.resultType]) {
                    resultsByType[c.resultType] = 0;
                }
                resultsByType[c.resultType] += c.results;
            });
            
            // Get platform badge color
            function getPlatformBadge(platform) {
                const platformLower = platform.toLowerCase();
                if (platformLower.includes('meta')) return `<span class="platform-badge platform-meta">${platform}</span>`;
                if (platformLower.includes('facebook')) return `<span class="platform-badge platform-facebook">${platform}</span>`;
                if (platformLower.includes('instagram')) return `<span class="platform-badge platform-instagram">${platform}</span>`;
                return `<span class="platform-badge" style="background: #667eea;">${platform}</span>`;
            }

            let evidenceSection = '<div style="page-break-before:always; padding-top: 30px;">';
            evidenceSection += '<h4 style="margin-bottom: 20px; color: #6366f1;">Campaign Evidence (In Range)</h4>';

            if (campaigns.length === 0) {
                evidenceSection += '<p class="text-muted">No campaign data found in the selected range for evidence reporting.</p>';
            } else {
                 campaigns.forEach(c => {
                      let mediaHtml = '';

                      mediaHtml += (c.evidenceUrls || []).map(url => `
                          <a href="${url}" target="_blank" style="display: inline-block; margin-right: 10px; font-size: 12px; color: #6366f1;">${url}</a>
                      `).join('<br>');

                      mediaHtml += (c.evidenceFiles || []).map(dataUrl => `
                          <img src="${dataUrl}" style="max-width: 150px; height: auto; margin: 10px; border: 1px solid #ddd;">
                      `).join('');

                      if (c.evidenceUrls.length > 0 || (c.evidenceFiles && c.evidenceFiles.length > 0)) {
                          evidenceSection += `
                              <div style="margin-bottom: 20px; border: 1px solid #f0f1ff; padding: 15px; border-radius: 8px;">
                                  <h5>${c.adName} (${c.platform}) - Logged on ${formatDateTime(c.dateAdded)}</h5>
                                  <p style="margin-top: 10px;">${mediaHtml}</p>
                              </div>
                          `;
                      }
                 });
            }
            evidenceSection += '</div>';

            const html = `
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Campaign Performance Report - ${client.name}</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; padding: 20px; }
                        .report-container { max-width: 1200px; margin: 0 auto; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; text-align: center; }
                        .header h1 { font-size: 32px; margin-bottom: 10px; }
                        .header p { font-size: 16px; opacity: 0.9; }
                        .client-info { background: #f8f9fa; padding: 30px 40px; border-left: 4px solid #667eea; }
                        .client-info h2 { color: #333; margin-bottom: 15px; font-size: 20px; }
                        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
                        .info-item { display: flex; align-items: center; }
                        .info-label { font-weight: 600; color: #666; min-width: 140px; }
                        .info-value { color: #333; font-weight: 500; }
                        .budget-summary { padding: 40px; background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
                        .budget-summary h2 { color: #333; margin-bottom: 25px; font-size: 24px; text-align: center; }
                        .budget-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
                        .budget-card { background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
                        .budget-card h3 { color: #666; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
                        .budget-card .amount { font-size: 28px; font-weight: bold; color: #333; }
                        .budget-card.total .amount { color: #667eea; }
                        .budget-card.spent .amount { color: #f39c12; }
                        .budget-card.remaining .amount { color: #27ae60; }
                        .campaigns-section { padding: 40px; }
                        .campaigns-section h2 { color: #333; margin-bottom: 25px; font-size: 24px; }
                        .campaign-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                        .campaign-table thead { background: #667eea; color: white; }
                        .campaign-table th { padding: 15px 10px; text-align: left; font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
                        .campaign-table td { padding: 15px 10px; border-bottom: 1px solid #e0e0e0; font-size: 14px; }
                        .campaign-table tbody tr:hover { background: #f8f9fa; }
                        .platform-badge { display: inline-block; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600; color: white; }
                        .platform-meta { background: #1877f2; }
                        .platform-facebook { background: #4267B2; }
                        .platform-instagram { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
                        .metrics-summary { background: #f8f9fa; padding: 40px; border-top: 3px solid #667eea; }
                        .metrics-summary h2 { color: #333; margin-bottom: 25px; font-size: 24px; text-align: center; }
                        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
                        .metric-box { background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #667eea; }
                        .metric-box h3 { color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
                        .metric-box .value { font-size: 24px; font-weight: bold; color: #333; }
                        .footer { background: #333; color: white; padding: 30px 40px; text-align: center; }
                        .footer p { font-size: 14px; opacity: 0.8; }
                        @media print { body { padding: 0; } .report-container { box-shadow: none; } }
                    </style>
                </head>
                <body>
                    <div class="report-container">
                        <div class="header">
                            <h1>Campaign Performance Report</h1>
                            <p>Generated on ${formatDate(new Date())}</p>
                        </div>
                        <div class="client-info">
                            <h2>Client Information</h2>
                            <div class="info-grid">
                                <div class="info-item"><span class="info-label">Client Name:</span><span class="info-value">${client.name}</span></div>
                                <div class="info-item"><span class="info-label">Report Period:</span><span class="info-value">${reportMonth}</span></div>
                                <div class="info-item"><span class="info-label">Total Campaigns:</span><span class="info-value">${campaigns.length}</span></div>
                                <div class="info-item"><span class="info-label">Active Platforms:</span><span class="info-value">${platforms}</span></div>
                            </div>
                        </div>
                        <div class="budget-summary">
                            <h2>Budget Overview</h2>
                            <div class="budget-cards">
                                <div class="budget-card total"><h3>Total Budget</h3><div class="amount">Rs. ${totalAdBudget.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div></div>
                                <div class="budget-card spent"><h3>Amount Spent</h3><div class="amount">Rs. ${totalSpent.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div></div>
                                <div class="budget-card remaining"><h3>Remaining</h3><div class="amount">Rs. ${remainingBudget.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div></div>
                            </div>
                        </div>
                        <div class="campaigns-section">
                            <h2>Campaign Performance Details</h2>
                            <table class="campaign-table">
                                <thead><tr><th>Platform</th><th>Campaign Name</th><th>Results</th><th>CPR</th><th>Reach</th><th>Spend</th></tr></thead>
                                <tbody>
                                    ${campaigns.length > 0 ? campaigns.map(c => `
                                        <tr>
                                            <td>${getPlatformBadge(c.platform)}</td>
                                            <td>${c.adName}</td>
                                            <td>${c.results.toLocaleString()} ${c.resultType}</td>
                                            <td>Rs. ${c.cpr.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                            <td>${c.reach.toLocaleString()}</td>
                                            <td>Rs. ${c.spend.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                        </tr>
                                    `).join('') : '<tr><td colspan="6" style="padding: 30px; text-align: center; color: #999;">No campaigns found in the selected date range.</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                        <div class="metrics-summary">
                            <h2>Key Metrics Summary</h2>
                            <div class="metrics-grid">
                                ${Object.entries(resultsByType).map(([type, count]) => `<div class="metric-box"><h3>Total ${type}</h3><div class="value">${count.toLocaleString()}</div></div>`).join('')}
                                <div class="metric-box"><h3>Total Reach</h3><div class="value">${totalReach.toLocaleString()}</div></div>
                                <div class="metric-box"><h3>Avg CPR</h3><div class="value">Rs. ${Math.round(avgCPR).toLocaleString()}</div></div>
                            </div>
                        </div>
                        <div class="footer">
                            <p>${COMPANY_INFO.name} | ${COMPANY_INFO.tagline}</p>
                            <p style="margin-top: 10px; font-size: 12px;">This report is confidential and intended for the client only.</p>
                        </div>
                    </div>
                </body>
                </html>
            `;

            document.getElementById('documentPreview').innerHTML = html;
            showDocumentModal();
        }

        // ============================================
        // FINANCIAL MANAGEMENT (Mock Handlers)
        // ============================================

        function showCreateDocumentModal(type) {
            // NOTE: This needs a PHP handler update (handler_finance.php)
            if (!hasPermission('canManageFinances')) {
                showAlert('Access Denied. Finance Manager/Admin role required.', 'warning');
                return;
            }
			const titles = { quotation: 'Quotation', invoice: 'Invoice', receipt: 'Receipt' };
			document.getElementById('documentModalTitle').textContent = `Create ${titles[type]}`;
			document.getElementById('documentForm').reset();
			document.getElementById('documentForm').dataset.docType = type;
			populateClientSelect('docClientSelect');
			document.getElementById('docDate').valueAsDate = new Date();
            new bootstrap.Modal(document.getElementById('documentModal')).show();
        }

        function loadAllFinancials() {
            // NOTE: This currently only loads from local appData. Full PHP handler required.
            populateClientSelect('docClientSelect');
            loadFinancialDocuments('quotation');
            loadFinancialDocuments('invoice');
            loadFinancialDocuments('receipt');
        }

        function loadFinancialDocuments(type) {
             // NOTE: This currently only loads from local appData. Full PHP handler required.
            const docs = appData.documents[type + 's'];
            const tbody = document.getElementById(type + 'sTableBody');
            const canDelete = hasPermission('canDeleteClient');

            if (!tbody) return;

            if (docs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-file-invoice"></i>
                        <h4>No ${type.charAt(0).toUpperCase() + type.slice(1)}s Yet</h4>
                        <p>Create your first ${type}</p>
                    </div>
                </td></tr>`;
                return;
            }

            const docNum = { quotation: 'Q', invoice: 'I', receipt: 'R' };
            tbody.innerHTML = docs.map(doc => `
                <tr onclick="viewDocument('${type}', ${doc.id})" style="cursor: pointer;">
                    <td>${docNum[type]}${String(doc.id).slice(-6)}</td>
                    <td>${doc.clientName || doc.client_name || 'Unknown Client'}</td>
                    <td><span class="badge bg-info">${doc.itemType || doc.item_type || 'General'}</span></td>
                    <td>${formatDate(doc.date)}</td>
                    <td>Rs. ${(doc.total || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td onclick="event.stopPropagation();">
                        <button class="btn btn-sm btn-primary" onclick="viewDocument('${type}', ${doc.id})"><i class="fas fa-eye"></i></button>
                        ${canDelete ? `<button class="btn btn-sm btn-danger" onclick="deleteDocument('${type}', ${doc.id})"><i class="fas fa-trash"></i></button>` : ''}
                    </td>
                </tr>`).join('');
        }


        // FIX: Document form submission using PHP handler (wrapped to ensure DOM is ready)
        function initDocumentForm() {
            const documentForm = document.getElementById('documentForm');
            if (!documentForm) {
                console.error('Document form not found');
                return;
            }

            // Add event listeners for item type checkboxes
            const itemTypeCheckboxes = document.querySelectorAll('input[type="checkbox"][id^="itemType"]');
            const selectedCount = document.getElementById('selectedCount');
            const countNumber = document.getElementById('countNumber');
            
            function updateSelectedCount() {
                const checkedBoxes = document.querySelectorAll('input[type="checkbox"][id^="itemType"]:checked');
                const count = checkedBoxes.length;
                
                if (count > 0) {
                    countNumber.textContent = count;
                    selectedCount.style.display = 'inline-block';
                } else {
                    selectedCount.style.display = 'none';
                }
            }
            
            itemTypeCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            
            // Function to handle description field visibility
            function updateDescriptionRequirement() {
                const otherServiceCheckbox = document.getElementById('itemType4');
                const descriptionFieldContainer = document.getElementById('descriptionField');
                const descriptionField = document.getElementById('docDescription');
                
                if (otherServiceCheckbox && descriptionFieldContainer && descriptionField) {
                    if (otherServiceCheckbox.checked) {
                        descriptionFieldContainer.style.display = 'block';
                        descriptionField.setAttribute('required', 'required');
                    } else {
                        descriptionFieldContainer.style.display = 'none';
                        descriptionField.removeAttribute('required');
                        descriptionField.value = ''; // Clear the field when hidden
                    }
                }
            }
            
            // Add event listener for Other Service checkbox
            const otherServiceCheckbox = document.getElementById('itemType4');
            if (otherServiceCheckbox) {
                otherServiceCheckbox.addEventListener('change', updateDescriptionRequirement);
            }
            
            // Initial call
            updateDescriptionRequirement();

            
			documentForm.addEventListener('submit', async function(e) {
                e.preventDefault();
				const form = this;
				const clientSelect = form.querySelector('#docClientSelect');
				const description = form.querySelector('#docDescription');
				const quantity = form.querySelector('#docQuantity');
				const unitPrice = form.querySelector('#docUnitPrice');
				const date = form.querySelector('#docDate');
                
                // Get selected item types
                const selectedItemTypes = [];
                const itemTypeCheckboxes = form.querySelectorAll('input[type="checkbox"][id^="itemType"]:checked');
                
                itemTypeCheckboxes.forEach(checkbox => {
                    selectedItemTypes.push(checkbox.value);
                });
                
                // Check if all elements exist and at least one item type is selected
				if (!clientSelect || !description || !quantity || !unitPrice || !date) {
                    console.error('One or more form elements not found');
                    showAlert('Form error: Missing form elements', 'danger');
                    return;
                }
                
                if (selectedItemTypes.length === 0) {
                    showAlert('Please select at least one item type.', 'warning');
                    return;
                }
                
                // Check if description is required (when Other Service is selected)
                if (selectedItemTypes.includes('Other Service')) {
                    if (!description.value || description.value.trim() === '') {
                        showAlert('Description is required when "Other Service" is selected.', 'warning');
                        description.focus();
                        return;
                    }
                }
                
                const formData = {
					clientId: parseInt(clientSelect.value),
					docType: form.dataset.docType,
					itemTypes: selectedItemTypes,
					description: description.value,
					quantity: parseInt(quantity.value),
					unitPrice: parseFloat(unitPrice.value),
					date: date.value
                };

                if (!formData.clientId) {
                    showAlert('Please select a client.', 'warning');
                    return;
                }

                try {
                    const response = await fetch('handler_finance.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'same-origin',
                        body: JSON.stringify(formData)
                    });

                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(`HTTP ${response.status}: ${text}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        showAlert(result.message, 'success');
                        await loadAllDataFromPHP();
                        loadFinancialDocuments(formData.docType);
						document.activeElement && typeof document.activeElement.blur === 'function' && document.activeElement.blur();
						bootstrap.Modal.getInstance(document.getElementById('documentModal')).hide();
                        this.reset();
                        
                        // ✅ NEW: Trigger automatic print after successful creation
                        const docId = result.documentId || result.id;
                        if (docId) {
                            setTimeout(() => {
                                printDocument(formData.docType, docId);
                            }, 1000); // Small delay to ensure data is loaded
                        }
                    } else {
                        showAlert(result.message || 'Failed to create document.', 'danger');
                    }
                } catch (error) {
                    console.error('Error creating document:', error);
                    showAlert('Error creating document: ' + error.message, 'danger');
                }
            });
        }

        function viewDocument(type, id) {
           // NOTE: This remains local/front-end for now.
            const doc = appData.documents[type + 's'].find(d => String(d.id) === String(id));
            if (!doc) {
                console.warn('Document not found for view:', type, id);
                showAlert('Document not found or not loaded yet.', 'warning');
                return;
            }

            const colors = { quotation: '#6366f1', invoice: '#10b981', receipt: '#f59e0b' };
            const titles = { quotation: 'QUOTATION', invoice: 'INVOICE', receipt: 'RECEIPT' };
            const docNum = { quotation: 'Q', invoice: 'I', receipt: 'R' };

            // Handle multiple line items or single item
            const clientName = doc.clientName || doc.client_name || 'Unknown Client';
            const total = parseFloat(doc.total || 0);
            
            // Check if we have detailed item information (new format)
            let lineItems = [];
            if (doc.itemDetails && Array.isArray(doc.itemDetails)) {
                // New format with detailed item information
                lineItems = doc.itemDetails.map(item => ({
                    description: item.description || item.itemType || 'Service',
                    quantity: parseFloat(item.quantity || 0),
                    unitPrice: parseFloat(item.unitPrice || 0),
                    total: parseFloat(item.total || 0)
                }));
            } else {
                // Old format with single item
                const qty = parseFloat(doc.quantity || 0);
                const unit = parseFloat(doc.unitPrice || doc.unit_price || 0);
                lineItems = [{
                    description: doc.description || 'Service',
                    quantity: qty,
                    unitPrice: unit,
                    total: total
                }];
            }

            // Generate table rows for all line items
            const tableRows = lineItems.map(item => `
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 15px;">${item.description}</td>
                    <td style="padding: 15px; text-align: center;">${item.quantity.toLocaleString()}</td>
                    <td style="padding: 15px; text-align: right;">Rs. ${item.unitPrice.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                    <td style="padding: 15px; text-align: right; font-weight: bold;">Rs. ${item.total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                </tr>
            `).join('');

            const html = `
                <div style="display: flex; min-height: 100vh; font-family: Arial, sans-serif;">
                    <!-- Sidebar -->
                    <div style="width: 30%; background: #2c3e50; color: white; padding: 40px 30px; display: flex; flex-direction: column;">
                        <div style="margin-bottom: 30px; text-align: center;">
                            ${COMPANY_INFO.logoUrl ? `<img src="${COMPANY_INFO.logoUrl}" alt="Logo" style="height: 60px; margin-bottom: 10px; object-fit: contain;">` : ''}
                            <div style="font-size: 14px; color: #bdc3c7; margin-bottom: 40px; text-align: center; white-space: nowrap;">Service beyond expectation</div>
                        </div>
                        
                        <div style="margin-top: auto;">
                            <div style="margin-bottom: 25px;">
                                <div style="display: flex; align-items: center; margin-bottom: 10px; font-size: 12px;">
                                    <span style="width: 16px; margin-right: 10px; text-align: center;">✉</span>
                                    <span>${COMPANY_INFO.email || 'info@ayonionstudios.com'}</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 10px; font-size: 12px;">
                                    <span style="width: 16px; margin-right: 10px; text-align: center;">📞</span>
                                    <span>${COMPANY_INFO.phone || '+94 (70) 610 1035'}</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 10px; font-size: 12px;">
                                    <span style="width: 16px; margin-right: 10px; text-align: center;">📍</span>
                                    <span>${COMPANY_INFO.address || 'No.59/1/C, Kaluwala road, Kossinna, Ganemulla. PV00231937'}</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 10px; font-size: 12px;">
                                    <span style="width: 16px; margin-right: 10px; text-align: center;">🌐</span>
                                    <span><strong>${COMPANY_INFO.website || 'www.ayonionstudios.com'}</strong></span>
                                </div>
                            </div>
                            
                            <div>
                                <div style="font-size: 12px; color: #bdc3c7; margin-bottom: 8px;">Find us on social media:</div>
                                <div style="font-size: 14px; font-weight: bold; margin-bottom: 12px;">ayonionstudios</div>
                                <div style="display: flex; gap: 8px;">
                                    <div style="width: 20px; height: 20px; border-radius: 50%; background: #ff0000; display: flex; align-items: center; justify-content: center; font-size: 10px; color: white;"><i class="fab fa-youtube"></i></div>
                                    <div style="width: 20px; height: 20px; border-radius: 50%; background: #e4405f; display: flex; align-items: center; justify-content: center; font-size: 10px; color: white;"><i class="fab fa-instagram"></i></div>
                                    <div style="width: 20px; height: 20px; border-radius: 50%; background: #3b5998; display: flex; align-items: center; justify-content: center; font-size: 10px; color: white;"><i class="fab fa-facebook-f"></i></div>
                                    <div style="width: 20px; height: 20px; border-radius: 50%; background: #1da1f2; display: flex; align-items: center; justify-content: center; font-size: 10px; color: white;"><i class="fab fa-twitter"></i></div>
                                    <div style="width: 20px; height: 20px; border-radius: 50%; background: #0077b5; display: flex; align-items: center; justify-content: center; font-size: 10px; color: white;"><i class="fab fa-linkedin-in"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Content -->
                    <div style="width: 70%; background: white; padding: 40px; display: flex; flex-direction: column;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #ecf0f1;">
                            <div style="flex: 1;">
                                <div style="font-size: 14px; color: #7f8c8d; margin-bottom: 5px;">Customer</div>
                                <div style="font-size: 18px; font-weight: bold; color: #2c3e50;">${clientName}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 36px; font-weight: bold; color: #2c3e50; margin-bottom: 20px;">${titles[type]}</div>
                                <div style="font-size: 14px; color: #7f8c8d;">
                                    <div style="margin-bottom: 5px;">Date: ${formatDate(doc.date)}</div>
                                    <div style="margin-bottom: 5px;">Quote #: ${docNum[type]}${String(doc.id).slice(-6)}</div>
                                    <div style="margin-bottom: 5px;">Valid Until: ${formatDate(new Date(new Date(doc.date).getTime() + 14 * 24 * 60 * 60 * 1000))}</div>
                                </div>
                            </div>
                        </div>
                        
                        <table style="width: 100%; border-collapse: collapse; margin: 30px 0; font-size: 14px;">
                            <thead>
                                <tr style="background: #f8f9fa; border-bottom: 2px solid #ecf0f1;">
                                    <th style="padding: 15px 10px; text-align: left; font-weight: bold; color: #2c3e50;">Description</th>
                                    <th style="padding: 15px 10px; text-align: right; font-weight: bold; color: #2c3e50;">Unit Price</th>
                                    <th style="padding: 15px 10px; text-align: center; font-weight: bold; color: #2c3e50;">Quantity</th>
                                    <th style="padding: 15px 10px; text-align: right; font-weight: bold; color: #2c3e50;">Amount (Rs.)</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                        
                        <div style="margin-top: 30px; text-align: right;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #ecf0f1;">
                                <span>Subtotal:</span>
                                <span>${total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #ecf0f1;">
                                <span>Discount:</span>
                                <span>-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-top: 2px solid #2c3e50; border-bottom: 2px solid #2c3e50; font-weight: bold; font-size: 18px; margin-top: 10px;">
                                <span>Total:</span>
                                <span>${total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                            </div>
                        </div>
                        
                        <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 16px; font-weight: bold; color: #2c3e50; margin-bottom: 15px;">Thank you</div>
                            <div style="font-size: 14px; line-height: 1.6; color: #555;">
                                Thank you for reaching out Ayonion Studios. We will deliver you the best service possible.<br><br>
                                <strong>Payment Instructions:</strong><br>
                                • All cheques should be crossed and made payable to Ayonion Studios (pvt) Ltd.<br>
                                • A 50% of advance payment is required. (Excluding package payments)<br>
                                • The quotation is valid for two weeks from the day issued.<br>
                                • This is a computer generated quotation, No signature required.<br><br>
                                <div style="margin-top: 20px; padding: 15px; background: #e8f4f8; border-radius: 5px; border-left: 4px solid #3498db;">
                                    <div style="font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Please deposit the advance payment to the below account</div>
                                    <div><strong>Ayonion Studios (pvt) Ltd</strong></div>
                                    <div><strong>101001037178</strong></div>
                                    <div><strong>NDB Bank, Kadawatha Branch</strong></div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid #ecf0f1; text-align: center; color: #7f8c8d; font-size: 14px;">
                            Thank you and have a good day! Team Ayonion Studios.
                        </div>
                    </div>
                </div>
            `;
            // Add read-only header to document
            const readOnlyHeader = `
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 8px 12px; margin-bottom: 20px; font-size: 14px; color: #6c757d;">
                    <i class="fas fa-lock me-2"></i>Document View - Read Only
                </div>
            `;
            document.getElementById('documentPreview').innerHTML = readOnlyHeader + html;
            
            // Store document info in modal for print function
            const modal = document.getElementById('viewDocumentModal');
            if (modal) {
                modal.dataset.docType = type;
                modal.dataset.docId = id;
            }
            
            showDocumentModal();
        }

        async function deleteDocument(type, id) {
            // FIX: Document delete using PHP handler with system confirmation
            if (!hasPermission('canDeleteClient')) {
                showAlert('Access Denied. Only Admins can delete financial documents.', 'warning');
                return;
            }
            
            const confirmed = await showConfirm(
                `Are you sure you want to delete this ${type}? This action is irreversible and will revert profile updates for Receipts.`,
                'Delete Document',
                'danger'
            );
            
            if (confirmed) {
                try {
                    const response = await fetch(`handler_finance.php?action=delete&id=${id}&type=${type}`);
                    const result = await response.json();
                    
                    if (result.success) {
                        showAlert(result.message, 'success');
                        await loadAllDataFromPHP();
                        loadFinancialDocuments(type);
                    } else {
                        showAlert(result.message || 'Failed to delete document.', 'danger');
                    }
                } catch (error) {
                    console.error('Error deleting document:', error);
                    showAlert('Error deleting document: ' + error.message, 'danger');
                }
            }
        }

        // ✅ Print document function
        function printDocument(docType, docId) {
            try {
                // If no parameters provided, try to get from current context
                if (!docType || !docId) {
                    // Try to get from the current document being viewed
                    const currentDoc = getCurrentDocument();
                    if (currentDoc) {
                        docType = currentDoc.docType;
                        docId = currentDoc.id;
                    } else {
                        showAlert('No document selected for printing.', 'warning');
                        return;
                    }
                }
                
                // Validate parameters
                if (!docType || !docId) {
                    showAlert('Invalid document parameters for printing.', 'warning');
                    return;
                }
                
                // Find the document in the current data
                const docs = appData.documents[docType + 's'] || [];
                const doc = docs.find(d => String(d.id) === String(docId));
                
                if (!doc) {
                    showAlert('Document not found. Please refresh and try again.', 'warning');
                    return;
                }
                
                // Create print content directly in frontend
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                const printContent = generatePrintContent(doc, docType);
                
                printWindow.document.write(printContent);
                printWindow.document.close();
                
                showAlert(`${docType.charAt(0).toUpperCase() + docType.slice(1)} opening for printing! 🖨️`, 'info');
            } catch (error) {
                console.error('Print error:', error);
                showAlert('Failed to open document for printing. Please try again.', 'warning');
            }
        }
        
        // Helper function to get current document being viewed
        function getCurrentDocument() {
            // Try to get from modal context or global variables
            if (typeof selectedDocument !== 'undefined' && selectedDocument) {
                return selectedDocument;
            }
            
            // Try to get from the document modal
            const modal = document.getElementById('viewDocumentModal');
            if (modal && modal.dataset.docType && modal.dataset.docId) {
                return {
                    docType: modal.dataset.docType,
                    id: modal.dataset.docId
                };
            }
            
            return null;
        }
        
        // ✅ Generate print content for documents
        function generatePrintContent(doc, docType) {
            const colors = { quotation: '#6366f1', invoice: '#10b981', receipt: '#f59e0b' };
            const titles = { quotation: 'QUOTATION', invoice: 'INVOICE', receipt: 'RECEIPT' };
            const docNum = { quotation: 'Q', invoice: 'I', receipt: 'R' };
            
            const color = colors[docType];
            const title = titles[docType];
            const docNumber = docNum[docType] + String(doc.id).slice(-6);
            const clientName = doc.clientName || doc.client_name || 'Unknown Client';
            const itemType = doc.itemType || doc.item_type || 'General';
            const date = formatDate(doc.date);
            const quantity = doc.quantity || 0;
            const unitPrice = doc.unitPrice || doc.unit_price || 0;
            const total = doc.total || 0;
            const description = doc.description || '';
            
            return `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>${title} - ${docNumber}</title>
                    <style>
                        @media print { 
                            body { margin: 0; } 
                            .no-print { display: none; } 
                        }
                        body { font-family: Arial, sans-serif; padding: 30px; max-width: 800px; margin: 0 auto; }
                        .header { border-bottom: 4px solid ${color}; padding-bottom: 20px; margin-bottom: 30px; }
                        .company-info { text-align: right; }
                        .client-info { margin: 20px 0; }
                        .document-details { margin: 20px 0; }
                        .items-table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                        .items-table th, .items-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                        .items-table th { background-color: #f8f9fa; }
                        .total-section { text-align: right; margin-top: 20px; }
                        .footer { border-top: 2px solid #e5e7eb; padding-top: 20px; margin-top: 40px; text-align: center; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h1 style="color: ${color}; margin: 0; font-size: 2.5rem;">${title}</h1>
                                <p style="margin: 5px 0; font-size: 1.2rem; color: #666;">${docNumber}</p>
                            </div>
                            <div class="company-info">
                                <h3 style="margin: 0; color: #333;">${COMPANY_INFO.name}</h3>
                                <p style="margin: 5px 0; color: #666;">${COMPANY_INFO.tagline}</p>
                                <p style="margin: 5px 0;">${COMPANY_INFO.email}</p>
                                <p style="margin: 5px 0;">${COMPANY_INFO.phone}</p>
                                <p style="margin: 5px 0;">${COMPANY_INFO.address}</p>
                            </div>
                        </div>
                        <div class="client-info">
                            <h4>Bill To:</h4>
                            <p><strong>${clientName}</strong></p>
                        </div>
                        <div class="document-details">
                            <p><strong>Date:</strong> ${date}</p>
                            <p><strong>Item Type:</strong> ${itemType}</p>
                        </div>
                    </div>
                    
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>${description}</td>
                                <td>${quantity}</td>
                                <td>Rs. ${unitPrice.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                <td>Rs. ${total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="total-section">
                        <h3>Total: Rs. ${total.toLocaleString(undefined, { minimumFractionDigits: 2 })}</h3>
                    </div>
                    
                    <div class="footer">
                        <p>${COMPANY_INFO.name} | ${COMPANY_INFO.tagline}</p>
                        <p>Generated on ${formatDate(new Date())}</p>
                    </div>
                    
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                            }, 500);
                        };
                    </script>
                </body>
                </html>
            `;
        }

        // ============================================
        // DASHBOARD
        // ============================================
        function loadDashboard() {
            document.getElementById('totalClients').textContent = appData.clients.length;
            document.getElementById('totalContentUsed').textContent = appData.clients.reduce((sum, c) => sum + c.usedCredits, 0).toLocaleString();
        }


        // ============================================
        // UTILITY FUNCTIONS
        // ============================================
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            // Adjust for timezone offset to prevent date from changing
            const userTimezoneOffset = date.getTimezoneOffset() * 60000;
            const adjustedDate = new Date(date.getTime() + userTimezoneOffset);
            return adjustedDate.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        function formatDateTime(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleString('en-GB', {
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: true
            });
        }

        function getStatusColor(status) {
            const colors = { 'Pending': 'warning', 'In Progress': 'info', 'Published': 'success' };
            return colors[status] || 'secondary';
        }

        // ✅ Enhanced User-Friendly Notification System
        function showAlert(message, type = 'info', duration = 5000) {
            const container = document.getElementById('notificationContainer');
            const notificationId = 'notification-' + Date.now();
            
            // Create notification element
            const notification = document.createElement('div');
            notification.id = notificationId;
            notification.className = 'notification-toast mb-3';
            notification.style.cssText = `
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                border-left: 4px solid ${getNotificationColor(type)};
                padding: 16px;
                margin-bottom: 12px;
                transform: translateX(100%);
                transition: all 0.3s ease;
                max-width: 100%;
                word-wrap: break-word;
            `;
            
            // Get appropriate icon and title
            const { icon, title, bgClass } = getNotificationConfig(type);
            
            notification.innerHTML = `
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="${icon} ${bgClass}" style="font-size: 1.2rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark mb-1">${title}</div>
                        <div class="text-muted small">${message}</div>
                    </div>
                    <button type="button" class="btn-close btn-close-sm" onclick="closeNotification('${notificationId}')" style="margin-left: 8px;"></button>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Auto-close after duration
            if (duration > 0) {
                setTimeout(() => {
                    closeNotification(notificationId);
                }, duration);
            }
        }
        
        function closeNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }
        
        function getNotificationColor(type) {
            const colors = {
                'success': '#28a745',
                'danger': '#dc3545',
                'warning': '#ffc107',
                'info': '#17a2b8',
                'primary': '#007bff'
            };
            return colors[type] || colors['info'];
        }
        
        function getNotificationConfig(type) {
            const configs = {
                'success': {
                    icon: 'fas fa-check-circle',
                    title: 'Success!',
                    bgClass: 'text-success'
                },
                'danger': {
                    icon: 'fas fa-exclamation-circle',
                    title: 'Error',
                    bgClass: 'text-danger'
                },
                'warning': {
                    icon: 'fas fa-exclamation-triangle',
                    title: 'Warning',
                    bgClass: 'text-warning'
                },
                'info': {
                    icon: 'fas fa-info-circle',
                    title: 'Information',
                    bgClass: 'text-info'
                },
                'primary': {
                    icon: 'fas fa-bell',
                    title: 'Notification',
                    bgClass: 'text-primary'
                }
            };
            return configs[type] || configs['info'];
        }
        
        // ✅ Custom Confirmation System
        function showConfirm(message, title = 'Confirmation Required', type = 'warning') {
            return new Promise((resolve) => {
                const modal = document.getElementById('customConfirmModal');
                const modalTitle = document.getElementById('confirmModalTitle');
                const modalMessage = document.getElementById('confirmModalMessage');
                const modalIcon = document.getElementById('confirmModalIcon');
                const cancelBtn = document.getElementById('confirmModalCancel');
                const confirmBtn = document.getElementById('confirmModalConfirm');
                
                // Set content
                modalTitle.innerHTML = `<i class="fas fa-question-circle me-2"></i>${title}`;
                modalMessage.textContent = message;
                
                // Set icon based on type
                const iconConfig = getNotificationConfig(type);
                modalIcon.className = `${iconConfig.icon} ${iconConfig.bgClass}`;
                modalIcon.style.fontSize = '1.5rem';
                
                // Set button colors
                confirmBtn.className = `btn btn-${type === 'danger' ? 'danger' : 'primary'}`;
                
                // Remove existing event listeners
                const newCancelBtn = cancelBtn.cloneNode(true);
                const newConfirmBtn = confirmBtn.cloneNode(true);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                
                // Add new event listeners
                newCancelBtn.addEventListener('click', () => {
                    bootstrap.Modal.getInstance(modal).hide();
                    resolve(false);
                });
                
                newConfirmBtn.addEventListener('click', () => {
                    bootstrap.Modal.getInstance(modal).hide();
                    resolve(true);
                });
                
                // Show modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            });
        }
        

        // ============================================
        // INITIALIZE ON PAGE LOAD
        // ============================================
        window.addEventListener('load', function() {
            loadFromLocalStorage();
            initDocumentForm(); // Initialize document form handler
            
            // Set default date range for reports (if elements exist)
            const reportStartDate = document.getElementById('reportStartDate');
            const reportEndDate = document.getElementById('reportEndDate');
            
            if (reportStartDate && reportEndDate) {
                const today = new Date();
                const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                reportStartDate.value = firstDayOfMonth.toISOString().split('T')[0];
                reportEndDate.value = today.toISOString().split('T')[0];
            }
        });

        window.addEventListener('beforeunload', function() {
            saveToLocalStorage();
        });

        // ============================================
        // INVOICE FUNCTIONALITY
        // ============================================
        let selectedCampaigns = [];
        let currentInvoiceData = null;

        function toggleAllCampaigns() {
            const selectAll = document.getElementById('selectAllCampaigns');
            const checkboxes = document.querySelectorAll('.campaign-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateSelectedCampaigns();
        }

        function updateSelectedCampaigns() {
            const checkboxes = document.querySelectorAll('.campaign-checkbox:checked');
            selectedCampaigns = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            const generateBtn = document.getElementById('generateInvoiceBtn');
            if (selectedCampaigns.length > 0) {
                generateBtn.style.display = 'inline-block';
            } else {
                generateBtn.style.display = 'none';
            }
        }

        function showInvoiceModal() {
            if (selectedCampaigns.length === 0) {
                showAlert('Please select at least one campaign to generate an invoice.', 'warning');
                return;
            }

            const clientId = parseInt(document.getElementById('campaignClientSelect').value);
            const client = appData.clients.find(c => c.id === clientId);
            const campaigns = appData.campaigns.filter(c => selectedCampaigns.includes(c.id));
            
            const totalAmount = campaigns.reduce((sum, c) => sum + parseFloat(c.spend), 0);
            
            currentInvoiceData = {
                clientId: clientId,
                client: client,
                campaigns: campaigns,
                totalAmount: totalAmount
            };

            // Generate invoice preview HTML
            const invoiceHTML = `
                <div class="invoice-preview">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Invoice Preview</h4>
                            <p class="text-muted">Invoice #INV-${Date.now()}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>${client.company_name || client.partner_id}</h5>
                            <p class="text-muted">${client.partner_id}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Bill To:</h6>
                            <p><strong>${client.company_name || 'N/A'}</strong><br>
                            Partner ID: ${client.partner_id}<br>
                            Industry: ${client.industry || 'N/A'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Invoice Details:</h6>
                            <p>Date: ${new Date().toLocaleDateString()}<br>
                            Due Date: ${new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toLocaleDateString()}<br>
                            Status: Draft</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Campaign</th>
                                    <th>Platform</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${campaigns.map(c => `
                                    <tr>
                                        <td>${c.adName}</td>
                                        <td><span class="badge bg-primary">${c.platform}</span></td>
                                        <td>${formatDateTime(c.dateAdded)}</td>
                                        <td class="text-end">Rs. ${parseFloat(c.spend).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total Amount:</th>
                                    <th class="text-end">Rs. ${totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2 })}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;

            document.getElementById('invoicePreviewContent').innerHTML = invoiceHTML;
            new bootstrap.Modal(document.getElementById('invoicePreviewModal')).show();
        }

        async function saveInvoice() {
            if (!currentInvoiceData) {
                showAlert('No invoice data available.', 'error');
                return;
            }

            try {
                const response = await fetch('handler_invoices.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        clientId: currentInvoiceData.clientId,
                        selectedCampaigns: selectedCampaigns,
                        notes: 'Generated from CMS'
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert(`Invoice created successfully! Invoice #${result.invoiceNumber}`, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('invoicePreviewModal')).hide();
                    
                    // Reset selections
                    selectedCampaigns = [];
                    document.querySelectorAll('.campaign-checkbox').forEach(cb => cb.checked = false);
                    document.getElementById('selectAllCampaigns').checked = false;
                    updateSelectedCampaigns();
                } else {
                    showAlert(result.message || 'Failed to create invoice.', 'error');
                }
            } catch (error) {
                console.error('Error creating invoice:', error);
                showAlert('Failed to create invoice. Please try again.', 'error');
            }
        }

        function printInvoicePDF() {
            if (!currentInvoiceData) {
                showAlert('No invoice data available.', 'error');
                return;
            }

            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            const invoiceHTML = document.getElementById('invoicePreviewContent').innerHTML;
            const clientName = currentInvoiceData.client.company_name || currentInvoiceData.client.partner_id;
            
            const htmlContent = '<!DOCTYPE html>' +
                '<html>' +
                '<head>' +
                '<title>Invoice - ' + clientName + '</title>' +
                '<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">' +
                '<style>' +
                '@media print { body { margin: 0; } .no-print { display: none; } }' +
                '.invoice-header { border-bottom: 2px solid #6f42c1; padding-bottom: 20px; margin-bottom: 30px; }' +
                '.invoice-footer { border-top: 2px solid #6f42c1; padding-top: 20px; margin-top: 30px; }' +
                '</style>' +
                '</head>' +
                '<body>' +
                '<div class="container-fluid">' +
                invoiceHTML +
                '</div>' +
                '<scr' + 'ipt>' +
                'window.onload = function() { window.print(); }' +
                '</scr' + 'ipt>' +
                '</body>' +
                '</html>';
            
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            showAlert('Invoice opening for printing! 🖨️', 'info');
        }

        // ============================================
        // INITIALIZE APP ON LOAD
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            initializeTableSorting();
        });

    </script>
</body>
</html>