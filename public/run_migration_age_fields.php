<?php

/**
 * Migration Runner - Add Age Fields to Categories
 * Visit: http://localhost/run_migration_age_fields.php
 * 
 * Adds min_age and max_age columns to categories table
 * 
 * SECURITY: Delete this file after running!
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
$envFile = __DIR__ . '/../.env';
$env = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B'\"");
        $env[$key] = $value;
    }
}

// Get database config from .env
$db_host = $env['database.default.hostname'] ?? 'localhost';
$db_name = $env['database.default.database'] ?? 'lijst';
$db_user = $env['database.default.username'] ?? 'root';
$db_pass = $env['database.default.password'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Migration Runner - Add Age Fields</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #E31E24;
            border-bottom: 3px solid #E31E24;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            border-left: 4px solid #E31E24;
            background: #f8f9fa;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #E31E24;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #c41a1f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Migration Runner - Add Age Fields to Categories</h1>
        <p><strong>Mode:</strong> Standalone (No CodeIgniter required)</p>
        
        <?php
        
        echo '<div class="step">';
        echo '<h3>Step 1: Testing Database Connection</h3>';
        
        try {
            // Create database connection
            $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
            
            // Check connection
            if ($mysqli->connect_error) {
                throw new Exception('Connection failed: ' . $mysqli->connect_error);
            }
            
            // Test connection
            $mysqli->query('SELECT 1');
            
            echo '<div class="success">✓ Database connection successful!</div>';
            echo '<pre>';
            echo 'Host: ' . $db_host . "\n";
            echo 'Database: ' . $db_name . "\n";
            echo 'Username: ' . $db_user . "\n";
            echo '</pre>';
            
        } catch (Exception $e) {
            echo '<div class="error">✗ Database connection failed!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<div class="warning">Check your .env file for correct database credentials</div>';
            die('</div></div></body></html>');
        }
        
        echo '</div>';
        
        // Step 2: Check if categories table exists
        echo '<div class="step">';
        echo '<h3>Step 2: Checking Categories Table</h3>';
        
        $result = $mysqli->query("SHOW TABLES LIKE 'categories'");
        
        if ($result->num_rows == 0) {
            echo '<div class="error">✗ Categories table not found!</div>';
            echo '<p>Please run migrations first: <code>/public/run_migrations.php</code></p>';
            die('</div></div></body></html>');
        }
        
        echo '<div class="success">✓ Categories table exists!</div>';
        echo '</div>';
        
        // Step 3: Check if columns already exist
        echo '<div class="step">';
        echo '<h3>Step 3: Checking for Existing Age Columns</h3>';
        
        $result = $mysqli->query("SHOW COLUMNS FROM categories LIKE 'min_age'");
        $min_age_exists = $result->num_rows > 0;
        
        $result = $mysqli->query("SHOW COLUMNS FROM categories LIKE 'max_age'");
        $max_age_exists = $result->num_rows > 0;
        
        if ($min_age_exists && $max_age_exists) {
            echo '<div class="info">ℹ️ Age columns already exist!</div>';
            echo '<div class="success">✓ Migration already applied</div>';
        } else {
            echo '<div class="warning">Age columns not found - proceeding with migration</div>';
        }
        
        echo '</div>';
        
        // Step 4: Show current categories structure
        echo '<div class="step">';
        echo '<h3>Step 4: Current Categories Table Structure</h3>';
        
        $result = $mysqli->query("DESCRIBE categories");
        echo '<table>';
        echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td><code>' . htmlspecialchars($row['Field']) . '</code></td>';
            echo '<td>' . htmlspecialchars($row['Type']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Null']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Key']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Default'] ?? 'NULL') . '</td>';
            echo '<td>' . htmlspecialchars($row['Extra']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        echo '</div>';
        
        // Step 5: Run migration
        echo '<div class="step">';
        echo '<h3>Step 5: Running Migration</h3>';
        
        try {
            // Add min_age column if it doesn't exist
            if (!$min_age_exists) {
                $sql = "ALTER TABLE categories ADD COLUMN min_age INT(3) UNSIGNED NULL COMMENT 'Minimum age for this category'";
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Added min_age column</div>';
                } else {
                    throw new Exception('Failed to add min_age column: ' . $mysqli->error);
                }
            } else {
                echo '<div class="info">ℹ️ min_age column already exists</div>';
            }
            
            // Add max_age column if it doesn't exist
            if (!$max_age_exists) {
                $sql = "ALTER TABLE categories ADD COLUMN max_age INT(3) UNSIGNED NULL COMMENT 'Maximum age for this category'";
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Added max_age column</div>';
                } else {
                    throw new Exception('Failed to add max_age column: ' . $mysqli->error);
                }
            } else {
                echo '<div class="info">ℹ️ max_age column already exists</div>';
            }
            
            echo '<div class="success">✓ Migration completed successfully!</div>';
            
        } catch (Exception $e) {
            echo '<div class="error">✗ Error running migration!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 6: Show updated table structure
        echo '<div class="step">';
        echo '<h3>Step 6: Updated Categories Table Structure</h3>';
        
        $result = $mysqli->query("DESCRIBE categories");
        echo '<table>';
        echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>';
        while ($row = $result->fetch_assoc()) {
            $highlight = ($row['Field'] === 'min_age' || $row['Field'] === 'max_age') ? 'style="background: #fff3cd;"' : '';
            echo '<tr ' . $highlight . '>';
            echo '<td><code>' . htmlspecialchars($row['Field']) . '</code></td>';
            echo '<td>' . htmlspecialchars($row['Type']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Null']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Key']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Default'] ?? 'NULL') . '</td>';
            echo '<td>' . htmlspecialchars($row['Extra']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        echo '</div>';
        
        // Step 7: Show sample data
        echo '<div class="step">';
        echo '<h3>Step 7: Sample Categories Data</h3>';
        
        $result = $mysqli->query("SELECT id, name, slug, min_age, max_age, status FROM categories ORDER BY id LIMIT 10");
        
        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Slug</th><th>Min Age</th><th>Max Age</th><th>Status</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['slug']) . '</td>';
                echo '<td>' . ($row['min_age'] ?? 'Not set') . '</td>';
                echo '<td>' . ($row['max_age'] ?? 'Not set') . '</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
        echo '</div>';
        
        // Close database connection
        $mysqli->close();
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This migration runner should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/run_migration_age_fields.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (run_migration_age_fields.php) via FTP or file manager</li>
                <li>The age fields are now available in the categories table</li>
                <li>You can now set min_age and max_age values for each category</li>
                <li>Products will be filtered based on user age and category age restrictions</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
