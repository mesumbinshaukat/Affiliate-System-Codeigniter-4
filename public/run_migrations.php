<?php

/**
 * Web-based Migration Runner
 * Visit: http://lijst.wmcdev.nl/run_migrations.php
 * 
 * SECURITY: Delete this file after running migrations!
 */

// Security token - change this to a random string
define('MIGRATION_TOKEN', 'secure_token_' . md5('lijst_wmcdev_nl_2024'));

// Check if token is provided
$providedToken = $_GET['token'] ?? '';
if ($providedToken !== MIGRATION_TOKEN) {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Migration Runner - Access Denied</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .error { background: #fee; border: 1px solid #fcc; padding: 20px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="error">
            <h2>Access Denied</h2>
            <p>Invalid security token. Please provide the correct token in the URL:</p>
            <code>http://lijst.wmcdev.nl/run_migrations.php?token=YOUR_TOKEN</code>
            <p><strong>Token:</strong> ' . MIGRATION_TOKEN . '</p>
        </div>
    </body>
    </html>
    ');
}

// Load CodeIgniter
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsConfig = require __DIR__ . '/../app/Config/Paths.php';
$paths = new \Config\Paths();
foreach ($pathsConfig as $key => $value) {
    $paths->$key = $value;
}

// Initialize app
$app = \Config\Services::codeigniter();
$app->initialize();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Migration Runner</title>
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
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
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
            border-left: 4px solid #2563eb;
            background: #f8f9fa;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Database Migration Runner</h1>
        <p><strong>Domain:</strong> lijst.wmcdev.nl</p>
        <p><strong>Environment:</strong> <?= ENVIRONMENT ?></p>
        
        <?php
        
        echo '<div class="step">';
        echo '<h3>Step 1: Testing Database Connection</h3>';
        
        try {
            $db = \Config\Database::connect();
            
            // Test connection
            $db->query('SELECT 1');
            
            echo '<div class="success">✓ Database connection successful!</div>';
            echo '<pre>';
            echo 'Host: ' . $db->hostname . "\n";
            echo 'Database: ' . $db->database . "\n";
            echo 'Username: ' . $db->username . "\n";
            echo '</pre>';
            
        } catch (Exception $e) {
            echo '<div class="error">✗ Database connection failed!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<div class="warning">';
            echo '<strong>Troubleshooting:</strong><br>';
            echo '1. Check .env file database credentials<br>';
            echo '2. Ensure database "lijst" exists in phpMyAdmin<br>';
            echo '3. Verify database user has proper permissions';
            echo '</div>';
            die('</div></div></body></html>');
        }
        
        echo '</div>';
        
        // Step 2: Check existing tables
        echo '<div class="step">';
        echo '<h3>Step 2: Checking Existing Tables</h3>';
        
        $tables = $db->listTables();
        
        if (empty($tables)) {
            echo '<div class="info">No tables found. Database is empty.</div>';
        } else {
            echo '<div class="success">Found ' . count($tables) . ' existing table(s):</div>';
            echo '<pre>' . implode("\n", $tables) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Run migrations
        echo '<div class="step">';
        echo '<h3>Step 3: Running Migrations</h3>';
        
        try {
            $migrate = \Config\Services::migrations();
            
            // Get current version
            $currentVersion = $migrate->getVersion();
            echo '<p><strong>Current Version:</strong> ' . ($currentVersion ?: 'None') . '</p>';
            
            // Run migrations
            if ($migrate->latest()) {
                $newVersion = $migrate->getVersion();
                echo '<div class="success">✓ Migrations completed successfully!</div>';
                echo '<p><strong>New Version:</strong> ' . $newVersion . '</p>';
                
                // List tables after migration
                $tablesAfter = $db->listTables();
                echo '<div class="info">';
                echo '<strong>Tables created:</strong><br>';
                echo '<pre>' . implode("\n", $tablesAfter) . '</pre>';
                echo '</div>';
                
            } else {
                echo '<div class="warning">No new migrations to run.</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="error">✗ Migration failed!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Verify tables
        echo '<div class="step">';
        echo '<h3>Step 4: Verifying Database Structure</h3>';
        
        $requiredTables = ['users', 'lists', 'products', 'list_products', 'clicks', 'categories'];
        $allTablesExist = true;
        
        echo '<ul>';
        foreach ($requiredTables as $table) {
            if (in_array($table, $tables) || in_array($table, $db->listTables())) {
                echo '<li style="color: green;">✓ ' . $table . '</li>';
            } else {
                echo '<li style="color: red;">✗ ' . $table . ' (missing)</li>';
                $allTablesExist = false;
            }
        }
        echo '</ul>';
        
        if ($allTablesExist) {
            echo '<div class="success">✓ All required tables exist!</div>';
        } else {
            echo '<div class="error">✗ Some tables are missing. Check migration files.</div>';
        }
        
        echo '</div>';
        
        // Step 5: Test data
        echo '<div class="step">';
        echo '<h3>Step 5: Database Statistics</h3>';
        
        try {
            $stats = [];
            foreach ($db->listTables() as $table) {
                $count = $db->table($table)->countAllResults();
                $stats[$table] = $count;
            }
            
            echo '<table style="width: 100%; border-collapse: collapse;">';
            echo '<tr style="background: #f8f9fa;"><th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Table</th><th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Records</th></tr>';
            foreach ($stats as $table => $count) {
                echo '<tr><td style="padding: 10px; border: 1px solid #dee2e6;">' . $table . '</td><td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">' . $count . '</td></tr>';
            }
            echo '</table>';
            
        } catch (Exception $e) {
            echo '<div class="error">Could not retrieve statistics: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This migration runner should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/run_migrations.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (run_migrations.php) via FTP</li>
                <li>Visit your website: <a href="http://lijst.wmcdev.nl">http://lijst.wmcdev.nl</a></li>
                <li>Test user registration and login</li>
                <li>Check if all features work correctly</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
