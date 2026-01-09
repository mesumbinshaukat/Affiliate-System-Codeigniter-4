<?php

/**
 * Migration Runner - Social Authentication (Facebook & Google OAuth)
 * Visit: http://localhost:8080/run_migration_social_auth.php
 * 
 * Adds provider, provider_id, provider_token, and email_verified fields to users table
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
    <title>Migration Runner - Social Authentication</title>
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
        .btn-facebook {
            background: #1877f2;
        }
        .btn-facebook:hover {
            background: #0d5dbf;
        }
        .btn-google {
            background: #db4437;
        }
        .btn-google:hover {
            background: #c23321;
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
        .oauth-icon {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Migration Runner - Social Authentication (OAuth)</h1>
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
        
        // Step 2: Add provider column
        echo '<div class="step">';
        echo '<h3>Step 2: Adding provider Column to users Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'provider'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column provider already exists in users table</div>';
            } else {
                $sql = "ALTER TABLE users ADD COLUMN provider VARCHAR(50) NULL COMMENT 'OAuth provider: facebook, google, etc.' AFTER password";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ provider column added to users table successfully!</div>';
                } else {
                    throw new Exception('Failed to add provider column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding provider column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Add provider_id column
        echo '<div class="step">';
        echo '<h3>Step 3: Adding provider_id Column to users Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'provider_id'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column provider_id already exists in users table</div>';
            } else {
                $sql = "ALTER TABLE users ADD COLUMN provider_id VARCHAR(255) NULL COMMENT 'Unique ID from OAuth provider' AFTER provider";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ provider_id column added to users table successfully!</div>';
                } else {
                    throw new Exception('Failed to add provider_id column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding provider_id column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Add provider_token column
        echo '<div class="step">';
        echo '<h3>Step 4: Adding provider_token Column to users Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'provider_token'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column provider_token already exists in users table</div>';
            } else {
                $sql = "ALTER TABLE users ADD COLUMN provider_token TEXT NULL COMMENT 'OAuth access token (encrypted)' AFTER provider_id";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ provider_token column added to users table successfully!</div>';
                } else {
                    throw new Exception('Failed to add provider_token column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding provider_token column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 5: Add email_verified column
        echo '<div class="step">';
        echo '<h3>Step 5: Adding email_verified Column to users Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'email_verified'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column email_verified already exists in users table</div>';
            } else {
                $sql = "ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Email verification status' AFTER provider_token";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ email_verified column added to users table successfully!</div>';
                } else {
                    throw new Exception('Failed to add email_verified column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding email_verified column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 6: Add index for faster lookups
        echo '<div class="step">';
        echo '<h3>Step 6: Adding Index for Provider Authentication</h3>';
        
        try {
            // Check if index already exists
            $result = $mysqli->query("SHOW INDEX FROM users WHERE Key_name = 'idx_provider_auth'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Index idx_provider_auth already exists</div>';
            } else {
                $sql = "ALTER TABLE users ADD INDEX idx_provider_auth (provider, provider_id)";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Index idx_provider_auth added successfully!</div>';
                    echo '<div class="info">This index will speed up social login lookups</div>';
                } else {
                    throw new Exception('Failed to add index: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding index!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 7: Verify users table structure
        echo '<div class="step">';
        echo '<h3>Step 7: Verifying users Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM users");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $socialAuthColumns = [
                'provider' => 'varchar(50)',
                'provider_id' => 'varchar(255)',
                'provider_token' => 'text',
                'email_verified' => 'tinyint(1)'
            ];
            
            foreach ($socialAuthColumns as $col => $expectedType) {
                $exists = isset($columns[$col]);
                $status = $exists ? '✓ Present' : '✗ Missing';
                $actualType = $exists ? $columns[$col] : 'N/A';
                echo '<tr><td><strong>' . $col . '</strong></td><td>' . $actualType . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            $allPresent = isset($columns['provider']) && isset($columns['provider_id']) && 
                         isset($columns['provider_token']) && isset($columns['email_verified']);
            
            if ($allPresent) {
                echo '<div class="success">✓ All social authentication columns are properly configured!</div>';
            } else {
                echo '<div class="warning">⚠️ Some columns are missing. Please review the errors above.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying users table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 8: Check OAuth configuration
        echo '<div class="step">';
        echo '<h3>Step 8: Checking OAuth Configuration</h3>';
        
        $facebook_id = $env['FACEBOOK_APP_ID'] ?? null;
        $facebook_secret = $env['FACEBOOK_APP_SECRET'] ?? null;
        $google_id = $env['GOOGLE_CLIENT_ID'] ?? null;
        $google_secret = $env['GOOGLE_CLIENT_SECRET'] ?? null;
        
        echo '<table>';
        echo '<tr><th>Provider</th><th>Configuration</th><th>Status</th></tr>';
        
        $facebook_configured = !empty($facebook_id) && !empty($facebook_secret);
        $google_configured = !empty($google_id) && !empty($google_secret);
        
        echo '<tr>';
        echo '<td><span class="oauth-icon">📘</span> Facebook</td>';
        echo '<td>App ID: ' . ($facebook_id ? substr($facebook_id, 0, 10) . '...' : 'Not set') . '</td>';
        echo '<td>' . ($facebook_configured ? '✓ Configured' : '✗ Not configured') . '</td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td><span class="oauth-icon">🔴</span> Google</td>';
        echo '<td>Client ID: ' . ($google_id ? substr($google_id, 0, 20) . '...' : 'Not set') . '</td>';
        echo '<td>' . ($google_configured ? '✓ Configured' : '✗ Not configured') . '</td>';
        echo '</tr>';
        
        echo '</table>';
        
        if ($facebook_configured && $google_configured) {
            echo '<div class="success">✓ Both Facebook and Google OAuth are configured in .env file!</div>';
        } else {
            echo '<div class="warning">⚠️ OAuth credentials are missing in .env file. Social login will not work until configured.</div>';
        }
        
        echo '</div>';
        
        // Close database connection
        $mysqli->close();
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This migration runner should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/run_migration_social_auth.php</code></p>
        </div>
        
        <div class="info">
            <h3>✅ Migration Complete! Next Steps:</h3>
            <ol>
                <li><strong>Delete this file</strong> (run_migration_social_auth.php) via FTP or file manager</li>
                <li><strong>Configure OAuth providers:</strong>
                    <ul>
                        <li>Facebook: Add callback URL in Facebook Developer Console</li>
                        <li>Google: Add redirect URI in Google Cloud Console</li>
                    </ul>
                </li>
                <li><strong>Test Facebook login:</strong> Visit <a href="/login">/login</a> and click "Inloggen met Facebook"</li>
                <li><strong>Test Google login:</strong> Visit <a href="/login">/login</a> and click "Inloggen met Google"</li>
                <li><strong>Verify database:</strong> Check that social login creates users with provider data</li>
            </ol>
        </div>
        
        <div class="success">
            <h3>🎉 Social Authentication Features</h3>
            <p><strong>What's New:</strong></p>
            <ul>
                <li>✓ <strong>Facebook Login</strong> - One-click login with Facebook account</li>
                <li>✓ <strong>Google Login</strong> - One-click login with Google account</li>
                <li>✓ <strong>Auto Account Creation</strong> - New users created automatically</li>
                <li>✓ <strong>Account Linking</strong> - Links social accounts to existing emails</li>
                <li>✓ <strong>Profile Import</strong> - Imports name, email, and profile picture</li>
                <li>✓ <strong>Email Verification</strong> - Tracks verified emails from providers</li>
                <li>✓ <strong>Token Storage</strong> - Stores OAuth tokens for future API calls</li>
                <li>✓ <strong>Security</strong> - CSRF protection, blocked user prevention</li>
                <li>✓ <strong>Error Handling</strong> - Comprehensive error handling and logging</li>
            </ul>
        </div>
        
        <div class="info">
            <h3>📋 OAuth Callback URLs</h3>
            <p>Configure these URLs in your provider consoles:</p>
            <pre>http://localhost:8080/auth/social/callback
https://yourdomain.com/auth/social/callback (for production)</pre>
            
            <p><strong>Facebook Developer Console:</strong></p>
            <ul>
                <li>Go to: <a href="https://developers.facebook.com/apps/" target="_blank">https://developers.facebook.com/apps/</a></li>
                <li>Navigate to: Facebook Login > Settings</li>
                <li>Add callback URL to "Valid OAuth Redirect URIs"</li>
            </ul>
            
            <p><strong>Google Cloud Console:</strong></p>
            <ul>
                <li>Go to: <a href="https://console.cloud.google.com/" target="_blank">https://console.cloud.google.com/</a></li>
                <li>Navigate to: APIs & Services > Credentials</li>
                <li>Add callback URL to "Authorized redirect URIs"</li>
            </ul>
        </div>
        
        <div class="step">
            <h3>📚 Documentation</h3>
            <p>Comprehensive documentation has been created:</p>
            <ul>
                <li><code>SOCIAL_AUTH_SETUP.md</code> - Complete setup guide</li>
                <li><code>OAUTH_TESTING_GUIDE.md</code> - Testing scenarios and verification</li>
                <li><code>OAUTH_IMPLEMENTATION_SUMMARY.md</code> - Implementation details</li>
            </ul>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
        <a href="/login" class="btn btn-facebook">Test Facebook Login</a>
        <a href="/login" class="btn btn-google">Test Google Login</a>
        <a href="/dashboard" class="btn" style="background: #28a745;">Go to Dashboard</a>
    </div>
</body>
</html>
