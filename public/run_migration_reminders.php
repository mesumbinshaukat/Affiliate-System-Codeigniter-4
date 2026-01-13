<?php

/**
 * Migration Runner - Event Reminders
 * Visit: http://localhost:8080/run_migration_reminders.php
 * 
 * Adds event_date and reminder fields to lists table and creates list_reminders table
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
    <title>Migration Runner - Event Reminders</title>
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
        <h1>🔔 Migration Runner - Event Reminders</h1>
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
        
        // Step 2: Add event reminder fields to lists table
        echo '<div class="step">';
        echo '<h3>Step 2: Adding Event Reminder Fields to lists Table</h3>';
        
        try {
            // Check if event_date column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM `lists` LIKE 'event_date'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Event reminder fields already exist in lists table</div>';
            } else {
                $sql = "ALTER TABLE `lists` 
                    ADD COLUMN `event_date` DATE NULL COMMENT 'Event date for the list (e.g., birthday, anniversary)' AFTER `description`,
                    ADD COLUMN `reminder_enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Enable automatic email reminders' AFTER `event_date`,
                    ADD COLUMN `reminder_intervals` VARCHAR(100) NULL COMMENT 'Comma-separated reminder intervals in days (e.g., 30,14,7)' AFTER `reminder_enabled`";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Event reminder fields added successfully!</div>';
                } else {
                    throw new Exception('Failed to add event reminder fields: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding event reminder fields!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Create list_reminders table
        echo '<div class="step">';
        echo '<h3>Step 3: Creating list_reminders Table</h3>';
        
        try {
            // Check if table already exists
            $result = $mysqli->query("SHOW TABLES LIKE 'list_reminders'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Table list_reminders already exists</div>';
            } else {
                $sql = "CREATE TABLE `list_reminders` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `list_id` INT(11) UNSIGNED NOT NULL COMMENT 'Reference to lists table',
                    `recipient_email` VARCHAR(255) NOT NULL COMMENT 'Email address of reminder recipient',
                    `recipient_name` VARCHAR(255) NULL COMMENT 'Name of reminder recipient',
                    `reminder_type` VARCHAR(50) NOT NULL COMMENT 'Type of reminder: collaborator, invited_person',
                    `days_before` INT(11) NOT NULL COMMENT 'Days before event (e.g., 30, 14, 7)',
                    `sent_at` DATETIME NULL COMMENT 'When the reminder was sent',
                    `status` ENUM('pending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
                    `created_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_unique_reminder` (`list_id`, `recipient_email`, `days_before`),
                    CONSTRAINT `fk_list_reminders_list` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Table list_reminders created successfully!</div>';
                } else {
                    throw new Exception('Failed to create list_reminders table: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating list_reminders table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Verify list_reminders table structure
        echo '<div class="step">';
        echo '<h3>Step 4: Verifying list_reminders Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM list_reminders");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'list_id', 'recipient_email', 'recipient_name', 'reminder_type', 'days_before', 'sent_at', 'status', 'created_at'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td><strong>' . $col . '</strong></td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (count($columns) >= count($requiredColumns)) {
                echo '<div class="success">✓ list_reminders table is properly configured!</div>';
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
        echo '<p><strong>Event Reminder system has been successfully installed!</strong></p>';
        echo '<p>You can now:</p>';
        echo '<ul>';
        echo '<li>Set event dates (like birthdays) for lists</li>';
        echo '<li>Enable automatic email reminders</li>';
        echo '<li>Customize reminder intervals (30, 14, 7 days before)</li>';
        echo '<li>Automatically send reminders to collaborators</li>';
        echo '<li>Run: <code>php spark reminders:send</code> daily via cron</li>';
        echo '</ul>';
        echo '</div>';
        echo '<div class="info">';
        echo '<p><strong>📅 Next Steps:</strong></p>';
        echo '<ol>';
        echo '<li>Set up a cron job to run <code>php spark reminders:send</code> daily</li>';
        echo '<li>Configure your email settings in <code>.env</code></li>';
        echo '<li>Test by creating a list with an event date</li>';
        echo '</ol>';
        echo '</div>';
        echo '<div class="warning">';
        echo '<p><strong>⚠️ SECURITY WARNING:</strong></p>';
        echo '<p>Please <strong>DELETE THIS FILE</strong> (<code>run_migration_reminders.php</code>) immediately for security reasons!</p>';
        echo '</div>';
        echo '</div>';
        
        $mysqli->close();
        
        ?>
    </div>
</body>
</html>
