<?php

/**
 * Migration Runner - List Co-Owners & Collaboration
 * Visit: http://localhost:8080/run_migration_collaborators.php
 * 
 * Adds list_collaborators and list_invitations tables for managing co-owners
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
    <title>Migration Runner - List Collaboration</title>
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
        .feature-icon {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Migration Runner - List Co-Owners & Collaboration</h1>
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
        
        // Step 2: Create list_collaborators table
        echo '<div class="step">';
        echo '<h3>Step 2: Creating list_collaborators Table</h3>';
        
        try {
            // Check if table already exists
            $result = $mysqli->query("SHOW TABLES LIKE 'list_collaborators'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Table list_collaborators already exists</div>';
            } else {
                $sql = "CREATE TABLE `list_collaborators` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `list_id` INT(11) UNSIGNED NOT NULL COMMENT 'Reference to lists table',
                    `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Co-owner user ID',
                    `role` ENUM('owner', 'editor') NOT NULL DEFAULT 'editor' COMMENT 'owner = original creator, editor = co-owner with edit rights',
                    `can_invite` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Can this collaborator invite others',
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_list_user` (`list_id`, `user_id`),
                    KEY `list_id` (`list_id`),
                    KEY `user_id` (`user_id`),
                    CONSTRAINT `fk_collaborators_list` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk_collaborators_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Table list_collaborators created successfully!</div>';
                } else {
                    throw new Exception('Failed to create list_collaborators table: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating list_collaborators table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Create list_invitations table
        echo '<div class="step">';
        echo '<h3>Step 3: Creating list_invitations Table</h3>';
        
        try {
            // Check if table already exists
            $result = $mysqli->query("SHOW TABLES LIKE 'list_invitations'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Table list_invitations already exists</div>';
            } else {
                $sql = "CREATE TABLE `list_invitations` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `list_id` INT(11) UNSIGNED NOT NULL COMMENT 'Reference to lists table',
                    `inviter_id` INT(11) UNSIGNED NOT NULL COMMENT 'User who sent the invitation',
                    `invitee_email` VARCHAR(255) NOT NULL COMMENT 'Email of person being invited',
                    `invitee_id` INT(11) UNSIGNED NULL COMMENT 'User ID if invitee has an account',
                    `token` VARCHAR(64) NOT NULL COMMENT 'Unique invitation token',
                    `status` ENUM('pending', 'accepted', 'rejected', 'expired') NOT NULL DEFAULT 'pending',
                    `message` TEXT NULL COMMENT 'Personal message from inviter',
                    `expires_at` DATETIME NULL COMMENT 'Invitation expiry (default 7 days)',
                    `responded_at` DATETIME NULL COMMENT 'When invitation was accepted/rejected',
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_token` (`token`),
                    KEY `invitee_email` (`invitee_email`),
                    KEY `invitee_id` (`invitee_id`),
                    KEY `status` (`status`),
                    KEY `list_id` (`list_id`),
                    CONSTRAINT `fk_invitations_list` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk_invitations_inviter` FOREIGN KEY (`inviter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk_invitations_invitee` FOREIGN KEY (`invitee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Table list_invitations created successfully!</div>';
                } else {
                    throw new Exception('Failed to create list_invitations table: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating list_invitations table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Verify list_collaborators table structure
        echo '<div class="step">';
        echo '<h3>Step 4: Verifying list_collaborators Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM list_collaborators");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'list_id', 'user_id', 'role', 'can_invite', 'created_at', 'updated_at'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td><strong>' . $col . '</strong></td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (isset($columns['role'])) {
                echo '<div class="success">✓ list_collaborators table is properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying list_collaborators table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 5: Verify list_invitations table structure
        echo '<div class="step">';
        echo '<h3>Step 5: Verifying list_invitations Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM list_invitations");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'list_id', 'inviter_id', 'invitee_email', 'invitee_id', 'token', 'status', 'message', 'expires_at', 'responded_at', 'created_at', 'updated_at'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td><strong>' . $col . '</strong></td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (isset($columns['token'])) {
                echo '<div class="success">✓ list_invitations table is properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying list_invitations table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Close database connection
        $mysqli->close();
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This migration runner should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/run_migration_collaborators.php</code></p>
        </div>
        
        <div class="info">
            <h3>✅ Migration Complete! Next Steps:</h3>
            <ol>
                <li><strong>Delete this file</strong> (run_migration_collaborators.php) via FTP or file manager</li>
                <li><strong>Test co-owner invitation:</strong> Go to a list edit page → "Samenwerken" tab</li>
                <li><strong>Invite someone:</strong> Enter their email address and send invitation</li>
                <li><strong>Check invitations:</strong> Visit /dashboard/invitations to see pending invites</li>
                <li><strong>Accept invitation:</strong> Click accept link to become a co-owner</li>
                <li><strong>Edit as co-owner:</strong> Co-owners can now fully edit the list</li>
            </ol>
        </div>
        
        <div class="success">
            <h3>🎉 List Co-Owner Features</h3>
            <p><strong>What's New:</strong></p>
            <ul>
                <li>✓ <strong>Invite Co-Owners</strong> - Send email invitations to collaborate on lists</li>
                <li>✓ <strong>Email-Based Invites</strong> - Invite anyone by email (with or without account)</li>
                <li>✓ <strong>Automatic Account Linking</strong> - Invites link to accounts when user registers</li>
                <li>✓ <strong>Full Edit Rights</strong> - Co-owners can add/remove products and edit list details</li>
                <li>✓ <strong>Owner Controls</strong> - Original owner can remove co-owners and manage permissions</li>
                <li>✓ <strong>Personal Messages</strong> - Include custom message with invitations</li>
                <li>✓ <strong>Invitation Management</strong> - View, accept, reject, or cancel invitations</li>
                <li>✓ <strong>7-Day Expiry</strong> - Invitations automatically expire after 7 days</li>
                <li>✓ <strong>Dashboard Integration</strong> - See all lists you own or collaborate on</li>
                <li>✓ <strong>Security</strong> - Permission checks prevent unauthorized access</li>
            </ul>
        </div>
        
        <div class="step">
            <h3>📋 Use Cases</h3>
            <ul>
                <li><strong>Couples:</strong> Share a wedding wishlist with your partner</li>
                <li><strong>Families:</strong> Collaborate on birthday or holiday wishlists</li>
                <li><strong>Friends:</strong> Create group gift lists for shared celebrations</li>
                <li><strong>Events:</strong> Manage event wishlists with multiple organizers</li>
                <li><strong>Teams:</strong> Coordinate office or team wishlists together</li>
            </ul>
        </div>
        
        <div class="info">
            <h3>🔧 Technical Details</h3>
            <p><strong>Database Tables:</strong></p>
            <ul>
                <li><code>list_collaborators</code> - Stores active co-owner relationships</li>
                <li><code>list_invitations</code> - Manages pending, accepted, and rejected invitations</li>
            </ul>
            
            <p><strong>Key Features:</strong></p>
            <ul>
                <li>Foreign key constraints ensure data integrity</li>
                <li>Unique token for each invitation (64-character secure hash)</li>
                <li>Cascade delete removes collaborators when list is deleted</li>
                <li>Role-based permissions (owner vs editor)</li>
                <li>Email verification for invitees</li>
            </ul>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
        <a href="/dashboard/lists" class="btn" style="background: #28a745;">View My Lists</a>
        <a href="/dashboard/invitations" class="btn" style="background: #17a2b8;">View Invitations</a>
    </div>
</body>
</html>
