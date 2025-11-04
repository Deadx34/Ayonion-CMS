<!DOCTYPE html>
<html>
<head>
    <title>Campaign Images Migration - Ayonion CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 40px; background: #f8f9fa; }
        .container { max-width: 800px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-database"></i> Campaign Images Migration</h2>
        <p class="text-muted">Adding evidence_image_url and creative_image_url columns to campaigns table...</p>
        <hr>
        
<?php
include 'includes/config.php';

try {
    $conn = connect_db();
    
    // Execute migration statements directly
    $migrations = [
        "ALTER TABLE campaigns ADD COLUMN evidence_image_url TEXT DEFAULT NULL",
        "ALTER TABLE campaigns ADD COLUMN creative_image_url TEXT DEFAULT NULL"
    ];
    
    $results = [];
    
    foreach ($migrations as $sql) {
        echo "<div class='alert alert-info'>";
        echo "<strong>Executing:</strong> " . htmlspecialchars(substr($sql, 0, 80)) . "...";
        echo "</div>";
        
        if ($conn->query($sql)) {
            echo "<div class='alert alert-success'>✓ Success</div>";
            $results[] = true;
        } else {
            // Check if it's a "column already exists" error
            if (strpos($conn->error, 'Duplicate column') !== false || 
                strpos($conn->error, 'already exists') !== false) {
                echo "<div class='alert alert-warning'>⚠ Already exists (skipped)</div>";
                $results[] = true;
            } else {
                echo "<div class='alert alert-danger'>✗ Error: " . htmlspecialchars($conn->error) . "</div>";
                $results[] = false;
            }
        }
    }
    
    echo "<hr>";
    
    if (!in_array(false, $results)) {
        echo "<div class='alert alert-success'>";
        echo "<h4>✓ Migration Completed Successfully!</h4>";
        echo "<p>The campaigns table now has the following columns:</p>";
        echo "<ul>";
        echo "<li><strong>evidence_image_url</strong> - TEXT, allows NULL (stores evidence image URLs)</li>";
        echo "<li><strong>creative_image_url</strong> - TEXT, allows NULL (stores creative image URLs)</li>";
        echo "</ul>";
        echo "<p class='mb-0'>You can now upload campaign images in the CMS.</p>";
        echo "</div>";
        echo "<a href='index.html' class='btn btn-primary'>← Back to CMS</a>";
    } else {
        echo "<div class='alert alert-danger'>";
        echo "<h4>✗ Migration Failed</h4>";
        echo "<p>Some statements failed to execute. Please check the errors above.</p>";
        echo "</div>";
        echo "<a href='javascript:location.reload()' class='btn btn-warning'>Retry Migration</a>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h4>✗ Error</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</body>
</html>
