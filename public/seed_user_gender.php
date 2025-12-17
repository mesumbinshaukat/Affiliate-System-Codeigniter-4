<?php

/**
 * Seeding Script - Set User Gender to 'man'
 * Visit: http://localhost/seed_user_gender.php
 * 
 * Sets gender field to 'man' for all existing users
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
    <title>Seeding Script - Set User Gender</title>
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
        <h1>👥 Seeding Script - Set User Gender to 'man'</h1>
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
        
        // Step 2: Get current user count
        echo '<div class="step">';
        echo '<h3>Step 2: Checking Existing Users</h3>';
        
        try {
            $result = $mysqli->query("SELECT COUNT(*) as count FROM users");
            $row = $result->fetch_assoc();
            $totalUsers = $row['count'];
            
            echo '<div class="info">ℹ️ Found ' . $totalUsers . ' user(s) in database</div>';
            
            if ($totalUsers === 0) {
                echo '<div class="warning">⚠️ No users found in database. Nothing to seed.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error checking users!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Update gender for all users
        echo '<div class="step">';
        echo '<h3>Step 3: Updating User Gender to "man"</h3>';
        
        try {
            if ($totalUsers > 0) {
                $sql = "UPDATE users SET gender = 'male' WHERE gender IS NULL OR gender = ''";
                
                if ($mysqli->query($sql)) {
                    $affectedRows = $mysqli->affected_rows;
                    echo '<div class="success">✓ Gender updated successfully!</div>';
                    echo '<pre>';
                    echo 'Rows affected: ' . $affectedRows . "\n";
                    echo 'Gender set to: male (man)\n';
                    echo '</pre>';
                } else {
                    throw new Exception('Failed to update gender: ' . $mysqli->error);
                }
            } else {
                echo '<div class="info">ℹ️ No users to update</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error updating gender!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Verify gender update
        echo '<div class="step">';
        echo '<h3>Step 4: Verifying Gender Update</h3>';
        
        try {
            $result = $mysqli->query("SELECT id, username, email, gender FROM users ORDER BY id");
            
            if ($result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Gender</th></tr>';
                
                $maleCount = 0;
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . esc($row['username']) . '</td>';
                    echo '<td>' . esc($row['email']) . '</td>';
                    echo '<td>';
                    if ($row['gender'] === 'male') {
                        echo '<span style="color: green; font-weight: bold;">✓ male</span>';
                        $maleCount++;
                    } else {
                        echo '<span style="color: orange;">NULL</span>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
                
                echo '<div class="success">✓ Verification complete! ' . $maleCount . ' user(s) have gender set to "male"</div>';
            } else {
                echo '<div class="info">ℹ️ No users to verify</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying update: ' . htmlspecialchars($e->getMessage()) . '</div>';
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
        
        /**
         * Helper function to escape output
         */
        function esc($str) {
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        }
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This seeding script should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/seed_user_gender.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (seed_user_gender.php) via FTP or file manager</li>
                <li>Verify users have gender set to "male" in database</li>
                <li>Test personalized product suggestions in list editor</li>
                <li>Verify age-based recommendations are working</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
