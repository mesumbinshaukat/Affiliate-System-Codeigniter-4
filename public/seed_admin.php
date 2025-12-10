<?php

/**
 * Admin User Seeder
 * Visit: http://lijst.wmcdev.nl/seed_admin.php
 * 
 * Creates an admin user with:
 * - Email: admin@lijstje.nl
 * - Password: admin123
 * 
 * SECURITY: Delete this file after running!
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASEPATH', __DIR__ . '/../');

// Load CodeIgniter
try {
    require BASEPATH . 'vendor/autoload.php';
    
    // Load environment
    $dotenv = \Dotenv\Dotenv::createImmutable(BASEPATH);
    $dotenv->load();
    
    // Get database config
    $db_host = $_ENV['database.default.hostname'] ?? 'localhost';
    $db_name = $_ENV['database.default.database'] ?? 'lijst';
    $db_user = $_ENV['database.default.username'] ?? 'root';
    $db_pass = $_ENV['database.default.password'] ?? '';
    
} catch (Exception $e) {
    die('<div style="color: red; font-family: Arial; padding: 20px;"><h2>Error Loading CodeIgniter</h2><p>' . htmlspecialchars($e->getMessage()) . '</p></div>');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin User Seeder</title>
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
        <h1>👤 Admin User Seeder</h1>
        <p><strong>Domain:</strong> lijst.wmcdev.nl</p>
        <p><strong>Environment:</strong> <?= ENVIRONMENT ?></p>
        
        <?php
        
        echo '<div class="step">';
        echo '<h3>Step 1: Testing Database Connection</h3>';
        
        try {
            $db = \Config\Database::connect();
            
            // Test connection
            $db->query('SELECT 1');
            
            echo '<div class="success">✓ Database connection successful!</div>';
            echo '<pre>';
            echo 'Host: ' . $db->hostname . "\n";
            echo 'Database: ' . $db->database . "\n";
            echo 'Username: ' . $db->username . "\n";
            echo '</pre>';
            
        } catch (Exception $e) {
            echo '<div class="error">✗ Database connection failed!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            die('</div></div></body></html>');
        }
        
        echo '</div>';
        
        // Step 2: Check if users table exists
        echo '<div class="step">';
        echo '<h3>Step 2: Checking Users Table</h3>';
        
        $tables = $db->listTables();
        
        if (!in_array('users', $tables)) {
            echo '<div class="error">✗ Users table not found!</div>';
            echo '<p>Please run migrations first: <code>/public/run_migrations.php</code></p>';
            die('</div></div></body></html>');
        }
        
        echo '<div class="success">✓ Users table exists!</div>';
        echo '</div>';
        
        // Step 3: Check if admin already exists
        echo '<div class="step">';
        echo '<h3>Step 3: Checking for Existing Admin</h3>';
        
        $userModel = new \App\Models\UserModel();
        $existingAdmin = $userModel->where('email', 'admin@lijstje.nl')->first();
        
        if ($existingAdmin) {
            echo '<div class="warning">⚠️ Admin user already exists!</div>';
            echo '<table>';
            echo '<tr><th>Field</th><th>Value</th></tr>';
            echo '<tr><td>ID</td><td>' . $existingAdmin['id'] . '</td></tr>';
            echo '<tr><td>Username</td><td>' . esc($existingAdmin['username']) . '</td></tr>';
            echo '<tr><td>Email</td><td>' . esc($existingAdmin['email']) . '</td></tr>';
            echo '<tr><td>Role</td><td>' . esc($existingAdmin['role']) . '</td></tr>';
            echo '<tr><td>Status</td><td>' . esc($existingAdmin['status']) . '</td></tr>';
            echo '<tr><td>Created</td><td>' . $existingAdmin['created_at'] . '</td></tr>';
            echo '</table>';
            echo '<p><strong>No action taken.</strong> Admin user already exists in the database.</p>';
        } else {
            echo '<div class="info">No existing admin found. Proceeding with creation...</div>';
            
            // Step 4: Create admin user
            echo '</div>';
            echo '<div class="step">';
            echo '<h3>Step 4: Creating Admin User</h3>';
            
            try {
                $adminData = [
                    'username' => 'admin',
                    'email' => 'admin@lijstje.nl',
                    'password' => 'admin123', // Will be hashed by the model
                    'first_name' => 'Admin',
                    'last_name' => 'User',
                    'role' => 'admin',
                    'status' => 'active',
                ];
                
                if ($userModel->insert($adminData)) {
                    $adminId = $userModel->getInsertID();
                    $createdAdmin = $userModel->find($adminId);
                    
                    echo '<div class="success">✓ Admin user created successfully!</div>';
                    echo '<table>';
                    echo '<tr><th>Field</th><th>Value</th></tr>';
                    echo '<tr><td>ID</td><td>' . $createdAdmin['id'] . '</td></tr>';
                    echo '<tr><td>Username</td><td>' . esc($createdAdmin['username']) . '</td></tr>';
                    echo '<tr><td>Email</td><td>' . esc($createdAdmin['email']) . '</td></tr>';
                    echo '<tr><td>Role</td><td>' . esc($createdAdmin['role']) . '</td></tr>';
                    echo '<tr><td>Status</td><td>' . esc($createdAdmin['status']) . '</td></tr>';
                    echo '<tr><td>Created</td><td>' . $createdAdmin['created_at'] . '</td></tr>';
                    echo '</table>';
                    
                    echo '<div class="info">';
                    echo '<h4>Login Credentials:</h4>';
                    echo '<table>';
                    echo '<tr><th>Field</th><th>Value</th></tr>';
                    echo '<tr><td>Email</td><td><code>admin@lijstje.nl</code></td></tr>';
                    echo '<tr><td>Password</td><td><code>admin123</code></td></tr>';
                    echo '</table>';
                    echo '<p><strong>⚠️ IMPORTANT:</strong> Change the password immediately after first login!</p>';
                    echo '</div>';
                    
                } else {
                    $errors = $userModel->errors();
                    echo '<div class="error">✗ Failed to create admin user!</div>';
                    echo '<pre>' . htmlspecialchars(json_encode($errors, JSON_PRETTY_PRINT)) . '</pre>';
                }
                
            } catch (Exception $e) {
                echo '<div class="error">✗ Error creating admin user!</div>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            }
        }
        
        echo '</div>';
        
        // Step 5: Database statistics
        echo '<div class="step">';
        echo '<h3>Step 5: Database Statistics</h3>';
        
        try {
            $stats = [];
            foreach ($db->listTables() as $table) {
                $count = $db->table($table)->countAllResults();
                $stats[$table] = $count;
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
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This seeder should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/seed_admin.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (seed_admin.php) via FTP or file manager</li>
                <li>Visit your website: <a href="http://lijst.wmcdev.nl">http://lijst.wmcdev.nl</a></li>
                <li>Login with admin credentials: <code>admin@lijstje.nl</code> / <code>admin123</code></li>
                <li>Change the admin password immediately</li>
                <li>Access admin panel at: <code>/admin</code></li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
