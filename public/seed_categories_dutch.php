<?php

/**
 * Categories Seeder (Standalone) - Dutch Version
 * Visit: http://localhost/seed_categories_dutch.php
 * 
 * Drops existing English categories and seeds Dutch ones:
 * - Elektronica
 * - Baby & Kinderen
 * - Huis & Wonen
 * - Mode
 * - Sport & Buiten
 * - Boeken & Media
 * - Schoonheid & Gezondheid
 * - Speelgoed & Spellen
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
    <title>Categories Seeder - Dutch</title>
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
        <h1>📂 Categories Seeder - Dutch Version</h1>
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
        
        // Step 2: Check if categories table exists
        echo '<div class="step">';
        echo '<h3>Step 2: Checking Categories Table</h3>';
        
        $result = $mysqli->query("SHOW TABLES LIKE 'categories'");
        
        if ($result->num_rows == 0) {
            echo '<div class="error">✗ Categories table not found!</div>';
            echo '<p>Please run migrations first: <code>/public/run_migrations.php</code></p>';
            die('</div></div></body></html>');
        }
        
        echo '<div class="success">✓ Categories table exists!</div>';
        echo '</div>';
        
        // Step 3: Show existing categories
        echo '<div class="step">';
        echo '<h3>Step 3: Existing Categories</h3>';
        
        $result = $mysqli->query("SELECT id, name, slug, icon, status FROM categories ORDER BY id");
        
        if ($result->num_rows > 0) {
            echo '<div class="warning">⚠️ Found ' . $result->num_rows . ' existing categories (English)</div>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Slug</th><th>Icon</th><th>Status</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['slug']) . '</td>';
                echo '<td>' . htmlspecialchars($row['icon']) . '</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="info">No existing categories found.</div>';
        }
        
        echo '</div>';
        
        // Step 4: Disable foreign key checks and seed Dutch categories
        echo '<div class="step">';
        echo '<h3>Step 4: Seeding Dutch Categories</h3>';
        
        try {
            // Disable foreign key checks
            if (!$mysqli->query("SET FOREIGN_KEY_CHECKS=0")) {
                throw new Exception('Failed to disable foreign key checks: ' . $mysqli->error);
            }
            echo '<div class="info">ℹ️ Foreign key checks disabled</div>';
            
            // Truncate categories table
            if (!$mysqli->query("TRUNCATE TABLE categories")) {
                throw new Exception('Failed to truncate categories table: ' . $mysqli->error);
            }
            echo '<div class="success">✓ Categories table truncated</div>';
            
            // Dutch categories
            $categories = [
                ['name' => 'Elektronica', 'slug' => 'electronics', 'icon' => 'laptop', 'status' => 'active'],
                ['name' => 'Baby & Kinderen', 'slug' => 'baby-kids', 'icon' => 'baby-carriage', 'status' => 'active'],
                ['name' => 'Huis & Wonen', 'slug' => 'home-living', 'icon' => 'home', 'status' => 'active'],
                ['name' => 'Mode', 'slug' => 'fashion', 'icon' => 'shirt', 'status' => 'active'],
                ['name' => 'Sport & Buiten', 'slug' => 'sports-outdoor', 'icon' => 'dumbbell', 'status' => 'active'],
                ['name' => 'Boeken & Media', 'slug' => 'books-media', 'icon' => 'book', 'status' => 'active'],
                ['name' => 'Schoonheid & Gezondheid', 'slug' => 'beauty-health', 'icon' => 'heart', 'status' => 'active'],
                ['name' => 'Speelgoed & Spellen', 'slug' => 'toys-games', 'icon' => 'gamepad', 'status' => 'active'],
            ];
            
            $inserted = 0;
            foreach ($categories as $category) {
                $name = $category['name'];
                $slug = $category['slug'];
                $icon = $category['icon'];
                $status = $category['status'];
                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');
                
                $stmt = $mysqli->prepare("INSERT INTO categories (name, slug, icon, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssss', $name, $slug, $icon, $status, $created_at, $updated_at);
                
                if ($stmt->execute()) {
                    $inserted++;
                } else {
                    throw new Exception('Failed to insert category: ' . $mysqli->error);
                }
            }
            
            // Re-enable foreign key checks
            if (!$mysqli->query("SET FOREIGN_KEY_CHECKS=1")) {
                throw new Exception('Failed to enable foreign key checks: ' . $mysqli->error);
            }
            echo '<div class="success">✓ Successfully seeded ' . $inserted . ' Dutch categories!</div>';
            echo '<div class="info">ℹ️ Foreign key checks re-enabled</div>';
            
            // Show inserted categories
            $result = $mysqli->query("SELECT id, name, slug, icon, status FROM categories ORDER BY id");
            echo '<table>';
            echo '<tr><th>ID</th><th>Name (Dutch)</th><th>Slug</th><th>Icon</th><th>Status</th></tr>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['slug']) . '</td>';
                echo '<td>' . htmlspecialchars($row['icon']) . '</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            
        } catch (Exception $e) {
            echo '<div class="error">✗ Error seeding categories!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
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
            <p>This seeder should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/seed_categories_dutch.php</code></p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li>Delete this file (seed_categories_dutch.php) via FTP or file manager</li>
                <li>Visit your website to see the Dutch categories</li>
                <li>All category names are now in Dutch</li>
            </ol>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>
</html>
