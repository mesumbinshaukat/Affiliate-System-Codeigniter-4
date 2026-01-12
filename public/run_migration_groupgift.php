<?php

/**
 * Migration Runner - Group Gift & Crowdfunding
 * Visit: http://localhost:8080/run_migration_groupgift.php
 * 
 * Adds group gift fields to list_products and creates contributions table
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
$db_name = $env['database.default.database'] ?? 'lijstje_db';
$db_user = $env['database.default.username'] ?? 'root';
$db_pass = $env['database.default.password'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Migration Runner - Group Gift & Crowdfunding</title>
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
        <h1>🎁 Migration Runner - Group Gift & Crowdfunding</h1>
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
        
        // Step 2: Add group gift fields to list_products
        echo '<div class="step">';
        echo '<h3>Step 2: Adding Group Gift Fields to list_products Table</h3>';
        
        try {
            // Check if is_group_gift column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM `list_products` LIKE 'is_group_gift'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Group gift fields already exist in list_products table</div>';
            } else {
                $sql = "ALTER TABLE `list_products` 
                    ADD COLUMN `is_group_gift` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Enable group contributions for this product' AFTER `custom_note`,
                    ADD COLUMN `target_amount` DECIMAL(10,2) NULL COMMENT 'Target amount for group gift in EUR' AFTER `is_group_gift`";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Group gift fields added successfully!</div>';
                } else {
                    throw new Exception('Failed to add group gift fields: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding group gift fields!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Add section_id foreign key if needed
        echo '<div class="step">';
        echo '<h3>Step 3: Adding Foreign Key for section_id</h3>';
        
        try {
            // Check if section_id column exists
            $result = $mysqli->query("SHOW COLUMNS FROM `list_products` LIKE 'section_id'");
            
            if ($result->num_rows === 0) {
                echo '<div class="info">ℹ️ section_id column does not exist (may be from previous migration)</div>';
            } else {
                // Check if foreign key already exists
                $result = $mysqli->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                                         WHERE TABLE_SCHEMA = '$db_name' 
                                         AND TABLE_NAME = 'list_products' 
                                         AND COLUMN_NAME = 'section_id' 
                                         AND CONSTRAINT_NAME LIKE 'fk_%'");
                
                if ($result->num_rows > 0) {
                    echo '<div class="info">ℹ️ Foreign key constraint already exists for section_id</div>';
                } else {
                    $sql = "ALTER TABLE `list_products` 
                            ADD CONSTRAINT `fk_list_products_section` 
                            FOREIGN KEY (`section_id`) REFERENCES `list_sections` (`id`) 
                            ON DELETE SET NULL ON UPDATE CASCADE";
                    
                    if ($mysqli->query($sql)) {
                        echo '<div class="success">✓ Foreign key constraint added successfully!</div>';
                    } else {
                        throw new Exception('Failed to add foreign key: ' . $mysqli->error);
                    }
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding foreign key!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Create contributions table
        echo '<div class="step">';
        echo '<h3>Step 4: Creating contributions Table</h3>';
        
        try {
            // Check if table already exists
            $result = $mysqli->query("SHOW TABLES LIKE 'contributions'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Table contributions already exists</div>';
            } else {
                $sql = "CREATE TABLE `contributions` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `list_product_id` INT(11) UNSIGNED NOT NULL COMMENT 'Reference to list_products table',
                    `contributor_name` VARCHAR(255) NOT NULL COMMENT 'Name of the person contributing',
                    `contributor_email` VARCHAR(255) NULL COMMENT 'Optional email for notifications',
                    `amount` DECIMAL(10,2) NOT NULL COMMENT 'Contribution amount in EUR',
                    `message` TEXT NULL COMMENT 'Optional message from contributor',
                    `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Hide contributor name from public view',
                    `status` ENUM('pending', 'completed', 'refunded') NOT NULL DEFAULT 'completed' COMMENT 'Contribution status',
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    KEY `list_product_id` (`list_product_id`),
                    KEY `status` (`status`),
                    CONSTRAINT `fk_contributions_list_product` FOREIGN KEY (`list_product_id`) REFERENCES `list_products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Table contributions created successfully!</div>';
                } else {
                    throw new Exception('Failed to create contributions table: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating contributions table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 5: Verify contributions table structure
        echo '<div class="step">';
        echo '<h3>Step 5: Verifying contributions Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM contributions");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'list_product_id', 'contributor_name', 'contributor_email', 'amount', 'message', 'is_anonymous', 'status', 'created_at', 'updated_at'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td><strong>' . $col . '</strong></td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (count($columns) >= count($requiredColumns)) {
                echo '<div class="success">✓ contributions table is properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error verifying table structure!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Final summary
        echo '<div class="step">';
        echo '<h3>✅ Migration Complete!</h3>';
        echo '<div class="success">';
        echo '<p><strong>Group Gift feature has been successfully installed!</strong></p>';
        echo '<p>You can now:</p>';
        echo '<ul>';
        echo '<li>Toggle "Groepscadeau" on products in the list editor</li>';
        echo '<li>Set target amounts for expensive items</li>';
        echo '<li>Allow visitors to contribute money towards gifts</li>';
        echo '<li>Track contributions in your dashboard</li>';
        echo '</ul>';
        echo '</div>';
        echo '<div class="warning">';
        echo '<p><strong>⚠️ SECURITY WARNING:</strong></p>';
        echo '<p>Please <strong>DELETE THIS FILE</strong> (<code>run_migration_groupgift.php</code>) immediately for security reasons!</p>';
        echo '</div>';
        echo '</div>';
        
        $mysqli->close();
        
        ?>
    </div>
</body>
</html>
