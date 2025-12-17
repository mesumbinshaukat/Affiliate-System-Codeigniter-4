<?php

/**
 * Migration Runner - Add Gender Field & Personalization Features
 * Visit: http://localhost/run_migration_gender.php
 * 
 * Adds gender column to users table for personalized product suggestions
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
    <title>Migration Runner - Add Gender Field & Personalization</title>
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
        <h1>👤 Migration Runner - Add Gender Field & Personalization</h1>
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
        
        // Step 2: Add gender column to users table
        echo '<div class="step">';
        echo '<h3>Step 2: Adding Gender Column to Users Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'gender'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column gender already exists in users table</div>';
            } else {
                $sql = "ALTER TABLE users ADD COLUMN gender ENUM('male', 'female', 'other') NULL DEFAULT NULL COMMENT 'User gender: male, female, or other' AFTER date_of_birth";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Gender column added to users table successfully!</div>';
                } else {
                    throw new Exception('Failed to add gender column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding gender column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Verify users table structure
        echo '<div class="step">';
        echo '<h3>Step 3: Verifying Users Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM users");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'username', 'email', 'first_name', 'last_name', 'date_of_birth', 'gender'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                $statusClass = isset($columns[$col]) ? 'success' : 'error';
                echo '<tr><td>' . $col . '</td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (isset($columns['gender'])) {
                echo '<div class="success">✓ Gender column is properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 4: Database statistics
        echo '<div class="step">';
        echo '<h3>Step 4: Database Statistics</h3>';
        
        try {
            $result = $mysqli->query("SHOW TABLES");
            $stats = [];
            
            while ($row = $result->fetch_row()) {
                $table = $row[0];
                $count_result = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
                $count_row = $count_result->fetch_assoc();
                $stats[$table] = $count_row['count'];
            }
            
            echo '<table>';
            echo '<tr><th>Table</th><th style="text-align: right;">Records</th></tr>';
            foreach ($stats as $table => $count) {
                echo '<tr><td>' . $table . '</td><td style="text-align: right;">' . $count . '</td></tr>';
            }
            echo '</table>';
            
        } catch (Exception $e) {
            echo '<div class="error">Could not retrieve statistics: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Close database connection
        $mysqli->close();
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This migration runner should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/run_migration_gender.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (run_migration_gender.php) via FTP or file manager</li>
                <li>Run CodeIgniter migration: <code>php spark migrate</code></li>
                <li>Test user registration with gender field</li>
                <li>Visit user profile page to view/edit gender</li>
                <li>Test personalized product suggestions in list editor</li>
                <li>Test product filters (sort, category, price)</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
