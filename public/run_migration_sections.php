<?php

/**
 * Migration Runner - List Sections Feature
 * Visit: http://localhost/run_migration_sections.php
 * 
 * Adds list_sections table and section_id to list_products for organizing products
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
    <title>Migration Runner - List Sections</title>
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
        <h1>📁 Migration Runner - List Sections Feature</h1>
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
        
        // Step 2: Create list_sections table
        echo '<div class="step">';
        echo '<h3>Step 2: Creating list_sections Table</h3>';
        
        try {
            // Check if table already exists
            $result = $mysqli->query("SHOW TABLES LIKE 'list_sections'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Table list_sections already exists</div>';
            } else {
                $sql = "CREATE TABLE `list_sections` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `list_id` INT(11) UNSIGNED NOT NULL COMMENT 'Reference to lists table',
                    `title` VARCHAR(255) NOT NULL COMMENT 'Section title (e.g., Jewelry, Tech, Lifetime)',
                    `position` INT(11) NOT NULL DEFAULT 0 COMMENT 'Display order of sections',
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    KEY `list_id` (`list_id`),
                    CONSTRAINT `fk_list_sections_list` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Table list_sections created successfully!</div>';
                } else {
                    throw new Exception('Failed to create list_sections table: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error creating list_sections table!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 3: Add section_id column to list_products table
        echo '<div class="step">';
        echo '<h3>Step 3: Adding section_id Column to list_products Table</h3>';
        
        try {
            // Check if column already exists
            $result = $mysqli->query("SHOW COLUMNS FROM list_products LIKE 'section_id'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Column section_id already exists in list_products table</div>';
            } else {
                $sql = "ALTER TABLE list_products ADD COLUMN section_id INT(11) UNSIGNED NULL COMMENT 'Optional section/category within the list' AFTER list_id";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ section_id column added to list_products table successfully!</div>';
                } else {
                    throw new Exception('Failed to add section_id column: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding section_id column!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 4: Add foreign key for section_id
        echo '<div class="step">';
        echo '<h3>Step 4: Adding Foreign Key for section_id</h3>';
        
        try {
            // Check if foreign key already exists
            $result = $mysqli->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                                     WHERE TABLE_SCHEMA = '$db_name' 
                                     AND TABLE_NAME = 'list_products' 
                                     AND CONSTRAINT_NAME = 'fk_list_products_section'");
            
            if ($result->num_rows > 0) {
                echo '<div class="info">ℹ️ Foreign key fk_list_products_section already exists</div>';
            } else {
                $sql = "ALTER TABLE list_products ADD CONSTRAINT fk_list_products_section 
                        FOREIGN KEY (section_id) REFERENCES list_sections(id) 
                        ON DELETE SET NULL ON UPDATE CASCADE";
                
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✓ Foreign key fk_list_products_section added successfully!</div>';
                } else {
                    throw new Exception('Failed to add foreign key: ' . $mysqli->error);
                }
            }
        } catch (Exception $e) {
            echo '<div class="error">✗ Error adding foreign key!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        }
        
        echo '</div>';
        
        // Step 5: Verify list_sections table structure
        echo '<div class="step">';
        echo '<h3>Step 5: Verifying list_sections Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM list_sections");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'list_id', 'title', 'position', 'created_at', 'updated_at'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td>' . $col . '</td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (isset($columns['title'])) {
                echo '<div class="success">✓ list_sections table is properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying list_sections table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Step 6: Verify list_products table structure
        echo '<div class="step">';
        echo '<h3>Step 6: Verifying list_products Table Structure</h3>';
        
        try {
            $result = $mysqli->query("SHOW COLUMNS FROM list_products");
            $columns = [];
            
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row['Type'];
            }
            
            echo '<table>';
            echo '<tr><th>Column Name</th><th>Type</th><th>Status</th></tr>';
            
            $requiredColumns = ['id', 'list_id', 'section_id', 'product_id', 'position', 'custom_note', 'claimed_at', 'claimed_by_subid'];
            foreach ($requiredColumns as $col) {
                $status = isset($columns[$col]) ? '✓ Present' : '✗ Missing';
                echo '<tr><td>' . $col . '</td><td>' . ($columns[$col] ?? 'N/A') . '</td><td>' . $status . '</td></tr>';
            }
            
            echo '</table>';
            
            if (isset($columns['section_id'])) {
                echo '<div class="success">✓ section_id column is properly configured!</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying list_products table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        echo '</div>';
        
        // Close database connection
        $mysqli->close();
        
        ?>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p><strong>DELETE THIS FILE IMMEDIATELY!</strong></p>
            <p>This migration runner should be deleted after use for security reasons.</p>
            <p>File to delete: <code>/public/run_migration_sections.php</code></p>
        </div>
        
        <div class="info">
            <h3>✅ Migration Complete! Next Steps:</h3>
            <ol>
                <li><strong>Delete this file</strong> (run_migration_sections.php) via FTP or file manager</li>
                <li><strong>Test section creation:</strong> Edit a list and add sections like "Jewelry", "Tech", "Lifetime"</li>
                <li><strong>Assign products:</strong> Add products to specific sections</li>
                <li><strong>View public list:</strong> Products will be grouped by sections</li>
                <li><strong>Move products:</strong> Drag products between sections (if implemented)</li>
            </ol>
        </div>
        
        <div class="success">
            <h3>🎉 Feature Overview</h3>
            <p><strong>What's New:</strong></p>
            <ul>
                <li>✓ Create custom sections within lists (e.g., "Jewelry", "Tech", "Lifetime")</li>
                <li>✓ Assign products to specific sections (optional)</li>
                <li>✓ Products grouped by sections on public view</li>
                <li>✓ Sections are optional - products without sections still display</li>
                <li>✓ Reorder sections with position field</li>
                <li>✓ Delete sections (products remain, section_id set to NULL)</li>
                <li>✓ Clean, organized product presentation</li>
            </ul>
        </div>
        
        <a href="/" class="btn">Go to Homepage</a>
        <a href="/dashboard/lists" class="btn" style="background: #28a745;">Go to Dashboard</a>
    </div>
</body>
</html>
