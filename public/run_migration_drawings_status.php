<?php

/**
 * Migration Runner - Add Status to Drawing Participants
 * Visit: http://localhost/run_migration_drawings_status.php
 * 
 * Adds status column to drawing_participants table for invitation acceptance workflow
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
    <title>Migration Runner - Add Status to Drawing Participants</title>
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
        <h1>🎲 Migration Runner - Add Status to Drawing Participants</h1>
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
        
        // Step 2: Check if drawing_participants table exists
        echo '<div class="step">';
        echo '<h3>Step 2: Checking Drawing Participants Table</h3>';
        
        try {
            $result = $mysqli->query("SHOW TABLES LIKE 'drawing_participants'");
            if ($result->num_rows === 0) {
                echo '<div class="error">✗ drawing_participants table not found!</div>';
                echo '<div class="warning">Please run the drawings migration first: run_migration_drawings.php</div>';
                die('</div></div></body></html>');
            }
            echo '<div class="success">✓ drawing_participants table found!</div>';
        } catch (Exception $e) {
            echo '<div class="error">✗ Error checking table: ' . htmlspecialchars($e->getMessage()) . '</div>';
            die('</div></div></body></html>');
        }
        
        echo '</div>';
        
        // Step 3: Check if status column already exists
        echo '<div class="step">';
        echo '<h3>Step 3: Checking for Status Column</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM drawing_participants LIKE 'status'");
            $statusExists = $result->num_rows > 0;
            
            if ($statusExists) {
                echo '<div class="info">ℹ️ Status column already exists</div>';
            } else {
                echo '<div class="info">Status column not found, will be created</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error checking column: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 4: Add status column if it doesn't exist
        echo '<div class="step">';
        echo '<h3>Step 4: Adding Status Column</h3>';
        
        try {
            if (!$statusExists) {
                $sql = "ALTER TABLE drawing_participants ADD COLUMN status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending' COMMENT 'Participation status: pending (invitation sent), accepted (user accepted), declined (user declined)'";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Status column added successfully!</div>';
                } else {
                    throw new Exception('Failed to add status column: ' . $mysqli->error);
                }
            } else {
                echo '<div class="success">✓ Status column already exists, skipping</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding status column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 5: Verify column
        echo '<div class="step">';
        echo '<h3>Step 5: Verifying Status Column</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM drawing_participants WHERE Field = 'status'");
            if ($result->num_rows > 0) {
                $column = $result->fetch_assoc();
                echo '<div class="success">✓ Status column verified!</div>';
                echo '<table>';
                echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>';
                echo '<tr>';
                echo '<td>' . $column['Field'] . '</td>';
                echo '<td>' . $column['Type'] . '</td>';
                echo '<td>' . $column['Null'] . '</td>';
                echo '<td>' . $column['Default'] . '</td>';
                echo '</tr>';
                echo '</table>';
            } else {
                echo '<div class="error">✗ Status column not found after migration!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error verifying column: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Close database connection
        $mysqli->close();
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This migration runner should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/run_migration_drawings_status.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (run_migration_drawings_status.php) via FTP or file manager</li>
                <li>Users can now accept or decline drawing invitations</li>
                <li>Drawing creators will see acceptance status of participants</li>
                <li>Drawings can only be drawn when all participants have accepted</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
