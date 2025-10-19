<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration - AYONION CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-database me-2"></i>Database Migration</h4>
                    </div>
                    <div class="card-body">
                        <div id="status" class="mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-spinner fa-spin me-2"></i>Checking database structure...
                            </div>
                        </div>
                        
                        <button id="runMigration" class="btn btn-primary" onclick="runMigration()">
                            <i class="fas fa-play me-2"></i>Run Migration
                        </button>
                        
                        <button id="checkStatus" class="btn btn-info ms-2" onclick="checkDatabase()">
                            <i class="fas fa-search me-2"></i>Check Database
                        </button>
                        
                        <div id="results" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function checkDatabase() {
            try {
                const response = await fetch('check_database.php');
                const data = await response.json();
                
                let html = '<div class="alert alert-info"><h6>Database Structure:</h6><ul>';
                data.current_columns.forEach(col => {
                    html += `<li>${col}</li>`;
                });
                html += '</ul></div>';
                
                html += '<div class="alert alert-' + (data.has_content_url ? 'success' : 'warning') + '">';
                html += `Content URL Column: ${data.has_content_url ? '✅ Exists' : '❌ Missing'}`;
                html += '</div>';
                
                html += '<div class="alert alert-' + (data.has_image_url ? 'success' : 'warning') + '">';
                html += `Image URL Column: ${data.has_image_url ? '✅ Exists' : '❌ Missing'}`;
                html += '</div>';
                
                html += '<div class="alert alert-' + (data.has_status ? 'success' : 'warning') + '">';
                html += `Status Column: ${data.has_status ? '✅ Exists' : '❌ Missing'}`;
                html += '</div>';
                
                html += '<div class="alert alert-' + (data.has_published_date ? 'success' : 'warning') + '">';
                html += `Published Date Column: ${data.has_published_date ? '✅ Exists' : '❌ Missing'}`;
                html += '</div>';
                
                document.getElementById('results').innerHTML = html;
            } catch (error) {
                document.getElementById('results').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            }
        }
        
        async function runMigration() {
            document.getElementById('status').innerHTML = '<div class="alert alert-warning"><i class="fas fa-spinner fa-spin me-2"></i>Running migration...</div>';
            
            try {
                const response = await fetch('migrate_database.php');
                const data = await response.json();
                
                let alertClass = data.success ? 'alert-success' : 'alert-danger';
                document.getElementById('status').innerHTML = `<div class="alert ${alertClass}">${data.message}</div>`;
                
                if (data.success) {
                    setTimeout(() => {
                        checkDatabase();
                    }, 1000);
                }
            } catch (error) {
                document.getElementById('status').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            }
        }
        
        // Check database on page load
        window.onload = function() {
            checkDatabase();
        };
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
