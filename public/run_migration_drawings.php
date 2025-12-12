<?php

/**
 * Migration Runner - Create Drawings Tables
 * Visit: http://localhost/run_migration_drawings.php
 * 
 * Creates drawings and drawing_participants tables for the Loten Trekken feature
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
    <title>Migration Runner - Create Drawings Tables</title>
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
        <h1>🎲 Migration Runner - Create Drawings Tables</h1>
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
        
        // Step 2: Create drawings table
        echo '<div class="step">';
        echo '<h3>Step 2: Creating Drawings Table</h3>';
        
        try {
            $sql = "CREATE TABLE IF NOT EXISTS drawings (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                creator_id INT(11) UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                event_date DATE NULL,
                status ENUM('pending', 'drawn', 'completed') DEFAULT 'pending',
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($mysqli->query($sql)) {
                echo '<div class="success">✓ Drawings table created successfully!</div>';
            } else {
                throw new Exception('Failed to create drawings table: ' . $mysqli->error);
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating drawings table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Create drawing_participants table
        echo '<div class="step">';
        echo '<h3>Step 3: Creating Drawing Participants Table</h3>';
        
        try {
            $sql = "CREATE TABLE IF NOT EXISTS drawing_participants (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                drawing_id INT(11) UNSIGNED NOT NULL,
                user_id INT(11) UNSIGNED NOT NULL,
                assigned_to_user_id INT(11) UNSIGNED NULL COMMENT 'The person this user drew (who they need to buy for)',
                list_id INT(11) UNSIGNED NULL COMMENT 'The wish list of the assigned person',
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (drawing_id) REFERENCES drawings(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
                FOREIGN KEY (list_id) REFERENCES lists(id) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            if ($mysqli->query($sql)) {
                echo '<div class="success">✓ Drawing participants table created successfully!</div>';
            } else {
                throw new Exception('Failed to create drawing_participants table: ' . $mysqli->error);
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating drawing_participants table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Verify tables
        echo '<div class="step">';
        echo '<h3>Step 4: Verifying Tables</h3>';
        
        $result = $mysqli->query("SHOW TABLES LIKE 'drawing%'");
        $tables = [];
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
        
        if (in_array('drawings', $tables) && in_array('drawing_participants', $tables)) {
            echo '<div class="success">✓ Both tables created successfully!</div>';
            echo '<table>';
            echo '<tr><th>Table Name</th><th>Status</th></tr>';
            echo '<tr><td>drawings</td><td>✓ Created</td></tr>';
            echo '<tr><td>drawing_participants</td><td>✓ Created</td></tr>';
            echo '</table>';
        } else {
            echo '<div class="warning">⚠️ Some tables may not have been created properly</div>';
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
            <p>File to delete: <code>/public/run_migration_drawings.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (run_migration_drawings.php) via FTP or file manager</li>
                <li>Visit <code>/drawings</code> to access the Loten Trekken feature</li>
                <li>Create a new drawing event and start inviting participants</li>
                <li>Draw lots to randomly assign participants</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
