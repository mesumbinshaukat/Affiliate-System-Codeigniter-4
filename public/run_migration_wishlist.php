<?php

/**
 * Migration Runner - Wishlist Gift Reservation Feature
 * Visit: http://localhost/run_migration_wishlist.php
 * 
 * Adds is_crossable to lists table and claimed tracking to list_products table
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
    <title>Migration Runner - Wishlist Gift Reservation</title>
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
        <h1>🎁 Migration Runner - Wishlist Gift Reservation</h1>
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
        
        // Step 2: Add is_crossable column to lists table
        echo '<div class="step">';
        echo '<h3>Step 2: Adding is_crossable Column to Lists Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM lists LIKE 'is_crossable'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column is_crossable already exists in lists table</div>';
            } else {
                $sql = "ALTER TABLE lists ADD COLUMN is_crossable TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Allow marking items as purchased' AFTER is_featured";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ is_crossable column added to lists table successfully!</div>';
                } else {
                    throw new Exception('Failed to add is_crossable column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding is_crossable column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Add claimed_at column to list_products table
        echo '<div class="step">';
        echo '<h3>Step 3: Adding claimed_at Column to List Products Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM list_products LIKE 'claimed_at'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column claimed_at already exists in list_products table</div>';
            } else {
                $sql = "ALTER TABLE list_products ADD COLUMN claimed_at DATETIME NULL COMMENT 'When item was marked as purchased' AFTER custom_note";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ claimed_at column added to list_products table successfully!</div>';
                } else {
                    throw new Exception('Failed to add claimed_at column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding claimed_at column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Add claimed_by_subid column to list_products table
        echo '<div class="step">';
        echo '<h3>Step 4: Adding claimed_by_subid Column to List Products Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM list_products LIKE 'claimed_by_subid'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column claimed_by_subid already exists in list_products table</div>';
            } else {
                $sql = "ALTER TABLE list_products ADD COLUMN claimed_by_subid VARCHAR(100) NULL COMMENT 'Anonymous tracking ID for purchase' AFTER claimed_at";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ claimed_by_subid column added to list_products table successfully!</div>';
                } else {
                    throw new Exception('Failed to add claimed_by_subid column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding claimed_by_subid column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 5: Add index for claimed_by_subid
        echo '<div class="step">';
        echo '<h3>Step 5: Adding Index for claimed_by_subid</h3>';
        
        try {
            // Check if index already exists
            $result = $mysqli->query("SHOW INDEX FROM list_products WHERE Key_name = 'idx_claimed_by_subid'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Index idx_claimed_by_subid already exists</div>';
            } else {
                $sql = "ALTER TABLE list_products ADD INDEX idx_claimed_by_subid (claimed_by_subid)";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Index idx_claimed_by_subid added successfully!</div>';
                } else {
                    throw new Exception('Failed to add index: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding index!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 6: Verify lists table structure
        echo '<div class="step">';
        echo '<h3>Step 6: Verifying Lists Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM lists");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'user_id', 'category_id', 'title', 'slug', 'description', 'status', 'is_featured', 'is_crossable', 'views'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td>' . $col . '</td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (isset($columns['is_crossable'])) {
                echo '<div class="success">✓ is_crossable column is properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying lists table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 7: Verify list_products table structure
        echo '<div class="step">';
        echo '<h3>Step 7: Verifying List Products Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM list_products");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'list_id', 'product_id', 'position', 'custom_note', 'claimed_at', 'claimed_by_subid', 'created_at'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td>' . $col . '</td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (isset($columns['claimed_at']) && isset($columns['claimed_by_subid'])) {
                echo '<div class="success">✓ Claimed tracking columns are properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying list_products table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 8: Database statistics
        echo '<div class="step">';
        echo '<h3>Step 8: Database Statistics</h3>';
        
        try {
            $stats = [];
            
            // Count lists with crossable enabled
            $result = $mysqli->query("SELECT COUNT(*) as count FROM lists WHERE is_crossable = 1");
            $row = $result->fetch_assoc();
            $stats['Lists with crossable enabled'] = $row['count'];
            
            // Count total lists
            $result = $mysqli->query("SELECT COUNT(*) as count FROM lists");
            $row = $result->fetch_assoc();
            $stats['Total lists'] = $row['count'];
            
            // Count claimed products
            $result = $mysqli->query("SELECT COUNT(*) as count FROM list_products WHERE claimed_at IS NOT NULL");
            $row = $result->fetch_assoc();
            $stats['Claimed products'] = $row['count'];
            
            // Count total list products
            $result = $mysqli->query("SELECT COUNT(*) as count FROM list_products");
            $row = $result->fetch_assoc();
            $stats['Total list products'] = $row['count'];
            
            echo '<table>';
            echo '<tr><th>Metric</th><th style="text-align: right;">Count</th></tr>';
            foreach ($stats as $metric => $count) {
                echo '<tr><td>' . $metric . '</td><td style="text-align: right;">' . $count . '</td></tr>';
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
            <p>File to delete: <code>/public/run_migration_wishlist.php</code></p>
        </div>
        
        <div class="info">
            <h3>✅ Migration Complete! Next Steps:</h3>
            <ol>
                <li><strong>Delete this file</strong> (run_migration_wishlist.php) via FTP or file manager</li>
                <li><strong>Test list creation:</strong> Create a new list and verify the "Sta toe dat items als gekocht gemarkeerd worden" checkbox appears</li>
                <li><strong>Test public view:</strong> Visit a public list and verify the "Ik Kocht Dit" button appears</li>
                <li><strong>Test claiming:</strong> Click "Ik Kocht Dit" and verify the item shows as "Gekocht"</li>
                <li><strong>Set up order processing:</strong> Add cron job: <code>0 * * * * php spark affiliate:process-orders</code></li>
                <li><strong>Review documentation:</strong> See WISHLIST_FEATURE_README.md for complete details</li>
            </ol>
        </div>
        
        <div class="success">
            <h3>🎉 Feature Overview</h3>
            <p><strong>What's New:</strong></p>
            <ul>
                <li>✓ List owners can enable/disable purchase marking per list</li>
                <li>✓ Visitors can manually mark items as purchased ("Ik Kocht Dit" button)</li>
                <li>✓ Claimed items show with green badge and strikethrough</li>
                <li>✓ Affiliate links track specific list items via subId encoding</li>
                <li>✓ Automatic claiming from Bol.com order reports</li>
                <li>✓ GDPR-compliant (no personal data stored)</li>
            </ul>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
        <a href="/dashboard/lists" class="btn" style="background: #28a745;">Go to Dashboard</a>
    </div>
</body>
</html>
