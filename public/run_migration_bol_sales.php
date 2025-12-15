<?php

/**
 * Migration Runner - Create Bol.com Sales Tables
 * Visit: http://localhost/run_migration_bol_sales.php
 * 
 * Creates clicks and sales tables for Bol.com affiliate sales tracking
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
    <title>Migration Runner - Create Bol.com Sales Tables</title>
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
        <h1>💰 Migration Runner - Create Bol.com Sales Tables</h1>
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
        
        // Step 2: Add sub_id column to clicks table
        echo '<div class="step">';
        echo '<h3>Step 2: Adding sub_id Column to Clicks Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM clicks LIKE 'sub_id'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column sub_id already exists in clicks table</div>';
            } else {
                $sql = "ALTER TABLE clicks ADD COLUMN sub_id VARCHAR(255) NULL COMMENT 'Tracking subId for commission attribution (format: user_id_list_id)'";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ sub_id column added to clicks table successfully!</div>';
                } else {
                    throw new Exception('Failed to add sub_id column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding sub_id column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Create sales table
        echo '<div class="step">';
        echo '<h3>Step 3: Creating Sales Table</h3>';
        
        try {
            // Check if table already exists
            $result = $mysqli->query("SHOW TABLES LIKE 'sales'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Sales table already exists</div>';
            } else {
                $sql = "CREATE TABLE IF NOT EXISTS sales (
                    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    sub_id VARCHAR(255) NOT NULL COMMENT 'Tracking subId from affiliate link (format: user_id_list_id)',
                    order_id VARCHAR(50) NOT NULL COMMENT 'Bol.com order ID',
                    product_id VARCHAR(50) NULL COMMENT 'Bol.com product ID',
                    quantity INT(11) DEFAULT 1,
                    commission DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Commission amount in EUR',
                    revenue_excl_vat DECIMAL(10,2) NULL COMMENT 'Order revenue excluding VAT',
                    status VARCHAR(50) DEFAULT 'pending' COMMENT 'Commission status: pending, approved, rejected',
                    user_id INT(11) UNSIGNED NULL COMMENT 'List owner user ID (extracted from subId)',
                    list_id INT(11) UNSIGNED NULL COMMENT 'List ID (extracted from subId)',
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    KEY sub_id (sub_id),
                    KEY order_id (order_id),
                    KEY user_id (user_id),
                    KEY list_id (list_id),
                    KEY status (status),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
                    FOREIGN KEY (list_id) REFERENCES lists(id) ON DELETE SET NULL ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Sales table created successfully!</div>';
                } else {
                    throw new Exception('Failed to create sales table: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating sales table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Verify tables and columns
        echo '<div class="step">';
        echo '<h3>Step 4: Verifying Tables and Columns</h3>';
        
        try {
            // Check clicks table
            $result = $mysqli->query("SHOW COLUMNS FROM clicks LIKE 'sub_id'");
            $clicksSubIdExists = $result->num_rows > 0;
            
            // Check sales table
            $result = $mysqli->query("SHOW TABLES LIKE 'sales'");
            $salesTableExists = $result->num_rows > 0;
            
            echo '<table>';
            echo '<tr><th>Component</th><th>Status</th></tr>';
            echo '<tr><td>clicks.sub_id column</td><td>' . ($clicksSubIdExists ? '✓ Exists' : '✗ Missing') . '</td></tr>';
            echo '<tr><td>sales table</td><td>' . ($salesTableExists ? '✓ Exists' : '✗ Missing') . '</td></tr>';
            echo '</table>';
            
            if ($clicksSubIdExists && $salesTableExists) {
                echo '<div class="success">✓ All required tables and columns created successfully!</div>';
            } else {
                echo '<div class="warning">⚠️ Some components may not have been created properly</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying tables: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 5: Database statistics
        echo '<div class="step">';
        echo '<h3>Step 5: Database Statistics</h3>';
        
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
            <p>File to delete: <code>/public/run_migration_bol_sales.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (run_migration_bol_sales.php) via FTP or file manager</li>
                <li>Set up cron job for daily report fetching: <code>0 2 * * * cd /path/to/project && php spark fetch:bol-reports</code></li>
                <li>Visit dashboard to view sales and commission data</li>
                <li>Visit admin analytics to view global sales overview</li>
                <li>Monitor application logs for any errors</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
