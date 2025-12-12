<?php

/**
 * Migration Runner - Add Status Column to Drawing Participants
 * Visit: http://localhost/add_status_column.php
 * 
 * Checks if status column exists in drawing_participants table
 * If not, adds it with ENUM type for invitation acceptance workflow
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
    <title>Migration Runner - Add Status Column</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #E31E24;
            padding-bottom: 10px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #E31E24;
            border-radius: 4px;
        }
        .step h3 {
            margin-top: 0;
            color: #333;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 12px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #bee5eb;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #ffeeba;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .button-group {
            margin-top: 20px;
            text-align: center;
        }
        a.btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background-color: #E31E24;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        a.btn:hover {
            background-color: #c41820;
        }
        a.btn-secondary {
            background-color: #6c757d;
        }
        a.btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Migration Runner - Add Status Column</h1>
        
        <?php
        // Step 1: Connect to database
        echo '<div class="step">';
        echo '<h3>Step 1: Connecting to Database</h3>';
        
        try {
            $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
            
            if ($mysqli->connect_error) {
                throw new Exception('Connection failed: ' . $mysqli->connect_error);
            }
            
            echo '<div class="success">✓ Connected to database successfully</div>';
        } catch (Exception $e) {
            echo '<div class="error">✗ Connection failed!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '</div></div></body></html>';
            exit;
        }
        
        echo '</div>';
        
        // Step 2: Check if drawing_participants table exists
        echo '<div class="step">';
        echo '<h3>Step 2: Checking Table Existence</h3>';
        
        try {
            $result = $mysqli->query("SHOW TABLES LIKE 'drawing_participants'");
            
            if ($result && $result->num_rows > 0) {
                echo '<div class="success">✓ Table "drawing_participants" exists</div>';
            } else {
                throw new Exception('Table "drawing_participants" not found');
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error checking table: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '</div></div></body></html>';
            exit;
        }
        
        echo '</div>';
        
        // Step 3: Check if status column exists
        echo '<div class="step">';
        echo '<h3>Step 3: Checking Status Column</h3>';
        
        $statusExists = false;
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM drawing_participants LIKE 'status'");
            
            if ($result && $result->num_rows > 0) {
                echo '<div class="success">✓ Status column already exists</div>';
                $statusExists = true;
            } else {
                echo '<div class="info">ℹ️ Status column not found, will be created</div>';
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
                    echo '<div class="info">Column details: ENUM(pending, accepted, declined) with default value "pending"</div>';
                } else {
                    throw new Exception('Failed to add status column: ' . $mysqli->error);
                }
            } else {
                echo '<div class="success">✓ Status column already exists, no action needed</div>';
                echo '<div class="info">The drawing_participants table is ready for invitation acceptance workflow</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding status column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 5: Verify the column
        echo '<div class="step">';
        echo '<h3>Step 5: Verifying Column</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM drawing_participants WHERE Field = 'status'");
            
            if ($result && $result->num_rows > 0) {
                $column = $result->fetch_assoc();
                echo '<div class="success">✓ Status column verified!</div>';
                echo '<pre>';
                echo 'Field: ' . htmlspecialchars($column['Field']) . "\n";
                echo 'Type: ' . htmlspecialchars($column['Type']) . "\n";
                echo 'Null: ' . htmlspecialchars($column['Null']) . "\n";
                echo 'Default: ' . htmlspecialchars($column['Default'] ?? 'NULL') . "\n";
                echo '</pre>';
            } else {
                echo '<div class="warning">⚠️ Status column could not be verified</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error verifying column: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 6: Summary
        echo '<div class="step">';
        echo '<h3>✅ Migration Complete</h3>';
        echo '<div class="success">The drawing_participants table is now ready for the invitation acceptance workflow!</div>';
        echo '<p><strong>Next steps:</strong></p>';
        echo '<ul>';
        echo '<li>The status column is now available in the database</li>';
        echo '<li>Users can accept/decline invitations</li>';
        echo '<li>Invitation status will be tracked as: pending, accepted, or declined</li>';
        echo '<li>Delete this file for security purposes</li>';
        echo '</ul>';
        echo '</div>';
        
        // Close database connection
        $mysqli->close();
        ?>
        
        <div class="button-group">
            <a href="javascript:location.reload()" class="btn">🔄 Run Again</a>
            <a href="/" class="btn btn-secondary">🏠 Go Home</a>
        </div>
    </div>
</body>
</html>
