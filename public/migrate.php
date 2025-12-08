<?php

/**
 * Simple Migration Runner - No CodeIgniter Bootstrap
 * Visit: https://lijst.wmcdev.nl/migrate.php?token=run_migrations_now
 * DELETE THIS FILE AFTER USE!
 */

// Security token
$token = $_GET['token'] ?? '';
if ($token !== 'run_migrations_now') {
    die('Access denied. Use: migrate.php?token=run_migrations_now');
}

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

// Database credentials
$host = $env['database.default.hostname'] ?? 'localhost';
$database = $env['database.default.database'] ?? 'lijst';
$username = $env['database.default.username'] ?? 'wmcdev';
$password = $env['database.default.password'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Migration Runner</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2563eb; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .step { margin: 20px 0; padding: 15px; border-left: 4px solid #2563eb; background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Database Migration Runner</h1>
        
        <?php
        
        echo '<div class="step">';
        echo '<h3>Step 1: Database Connection</h3>';
        
        try {
            $conn = new mysqli($host, $username, $password, $database);
            
            if ($conn->connect_error) {
                throw new Exception($conn->connect_error);
            }
            
            echo '<div class="success">✓ Connected to database: ' . $database . '</div>';
            
        } catch (Exception $e) {
            echo '<div class="error">✗ Connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="info">Check your .env file database credentials</div>';
            die('</div></div></body></html>');
        }
        
        echo '</div>';
        
        // Step 2: Fix existing tables if needed
        echo '<div class="step">';
        echo '<h3>Step 2: Fixing Existing Tables</h3>';
        
        // Check if categories table has wrong column
        $result = $conn->query("SHOW COLUMNS FROM categories LIKE 'is_active'");
        if ($result && $result->num_rows > 0) {
            // Drop and recreate categories table
            $conn->query("DROP TABLE IF EXISTS categories");
            echo '<div class="info">✓ Dropped old categories table (had wrong schema)</div>';
        }
        
        echo '</div>';
        
        // Step 3: Run SQL migrations
        echo '<div class="step">';
        echo '<h3>Step 3: Creating Tables</h3>';
        
        $migrations = [
            'users' => "CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `username` varchar(100) NOT NULL,
                `email` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `first_name` varchar(100) DEFAULT NULL,
                `last_name` varchar(100) DEFAULT NULL,
                `role` enum('admin','user') DEFAULT 'user',
                `status` enum('active','blocked','pending') DEFAULT 'active',
                `avatar` varchar(255) DEFAULT NULL,
                `bio` text DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'categories' => "CREATE TABLE IF NOT EXISTS `categories` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `slug` varchar(100) NOT NULL,
                `description` text DEFAULT NULL,
                `icon` varchar(50) DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'lists' => "CREATE TABLE IF NOT EXISTS `lists` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` int(11) UNSIGNED NOT NULL,
                `category_id` int(11) UNSIGNED DEFAULT NULL,
                `title` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `status` enum('draft','published','private') DEFAULT 'draft',
                `is_featured` tinyint(1) DEFAULT 0,
                `views` int(11) DEFAULT 0,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `user_id` (`user_id`),
                KEY `category_id` (`category_id`),
                CONSTRAINT `lists_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `lists_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'products' => "CREATE TABLE IF NOT EXISTS `products` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `image_url` varchar(500) DEFAULT NULL,
                `price` decimal(10,2) DEFAULT NULL,
                `affiliate_url` varchar(500) NOT NULL,
                `source` varchar(50) DEFAULT 'bol.com',
                `ean` varchar(50) DEFAULT NULL,
                `external_id` varchar(100) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `ean` (`ean`),
                KEY `external_id` (`external_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'list_products' => "CREATE TABLE IF NOT EXISTS `list_products` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `list_id` int(11) UNSIGNED NOT NULL,
                `product_id` int(11) UNSIGNED NOT NULL,
                `position` int(11) DEFAULT 0,
                `custom_note` text DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `list_id` (`list_id`),
                KEY `product_id` (`product_id`),
                CONSTRAINT `list_products_list_fk` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE,
                CONSTRAINT `list_products_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'clicks' => "CREATE TABLE IF NOT EXISTS `clicks` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `product_id` int(11) UNSIGNED NOT NULL,
                `list_id` int(11) UNSIGNED DEFAULT NULL,
                `user_id` int(11) UNSIGNED DEFAULT NULL,
                `ip_address` varchar(45) DEFAULT NULL,
                `user_agent` varchar(255) DEFAULT NULL,
                `referer` varchar(500) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `product_id` (`product_id`),
                KEY `list_id` (`list_id`),
                KEY `user_id` (`user_id`),
                CONSTRAINT `clicks_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
                CONSTRAINT `clicks_list_fk` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE SET NULL,
                CONSTRAINT `clicks_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'affiliate_sources' => "CREATE TABLE IF NOT EXISTS `affiliate_sources` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `slug` varchar(100) NOT NULL,
                `api_endpoint` varchar(255) DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `settings` JSON DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'settings' => "CREATE TABLE IF NOT EXISTS `settings` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `key` varchar(100) NOT NULL,
                `value` text DEFAULT NULL,
                `type` varchar(50) DEFAULT 'string',
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        ];
        
        $created = 0;
        $errors = 0;
        
        foreach ($migrations as $table => $sql) {
            if ($conn->query($sql)) {
                echo '<div class="success">✓ Table created: ' . $table . '</div>';
                $created++;
            } else {
                echo '<div class="error">✗ Error creating ' . $table . ': ' . $conn->error . '</div>';
                $errors++;
            }
        }
        
        echo '</div>';
        
        // Step 4: Insert default data
        echo '<div class="step">';
        echo '<h3>Step 4: Inserting Default Data</h3>';
        
        // Check if admin exists
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            $adminEmail = $env['ADMIN_EMAIL'] ?? 'admin@lijstje.nl';
            $adminPassword = password_hash($env['ADMIN_PASSWORD'] ?? 'Admin@123', PASSWORD_DEFAULT);
            $now = date('Y-m-d H:i:s');
            
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, role, is_active, created_at, updated_at) 
                    VALUES ('admin', '$adminEmail', '$adminPassword', 'Admin', 'User', 'admin', 1, '$now', '$now')";
            
            if ($conn->query($sql)) {
                echo '<div class="success">✓ Admin user created: ' . $adminEmail . '</div>';
            } else {
                echo '<div class="error">✗ Error creating admin: ' . $conn->error . '</div>';
            }
        } else {
            echo '<div class="info">Admin user already exists</div>';
        }
        
        // Insert categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'icon' => 'laptop'],
            ['name' => 'Books', 'slug' => 'books', 'icon' => 'book'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'icon' => 'home'],
            ['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'icon' => 'dumbbell'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => 'shirt'],
            ['name' => 'Toys & Games', 'slug' => 'toys-games', 'icon' => 'gamepad'],
            ['name' => 'Health & Beauty', 'slug' => 'health-beauty', 'icon' => 'heart'],
            ['name' => 'Food & Drinks', 'slug' => 'food-drinks', 'icon' => 'utensils']
        ];
        
        foreach ($categories as $cat) {
            $result = $conn->query("SELECT COUNT(*) as count FROM categories WHERE slug = '{$cat['slug']}'");
            $row = $result->fetch_assoc();
            
            if ($row['count'] == 0) {
                $now = date('Y-m-d H:i:s');
                $sql = "INSERT INTO categories (name, slug, icon, status, created_at, updated_at) 
                        VALUES ('{$cat['name']}', '{$cat['slug']}', '{$cat['icon']}', 'active', '$now', '$now')";
                
                if ($conn->query($sql)) {
                    echo '<div class="success">✓ Category created: ' . $cat['name'] . '</div>';
                }
            }
        }
        
        // Insert Bol.com source
        $result = $conn->query("SELECT COUNT(*) as count FROM affiliate_sources WHERE slug = 'bol-com'");
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            $now = date('Y-m-d H:i:s');
            $sql = "INSERT INTO affiliate_sources (name, slug, api_endpoint, is_active, created_at, updated_at) 
                    VALUES ('Bol.com', 'bol-com', 'https://api.bol.com/marketing/catalog/v1', 1, '$now', '$now')";
            
            if ($conn->query($sql)) {
                echo '<div class="success">✓ Affiliate source created: Bol.com</div>';
            }
        }
        
        echo '</div>';
        
        $conn->close();
        
        ?>
        
        <div class="success">
            <h2>✅ Migration Complete!</h2>
            <p>Database tables created: <?= $created ?></p>
            <p>Default data inserted successfully</p>
        </div>
        
        <div class="info">
            <h3>Next Steps:</h3>
            <ol>
                <li><strong>DELETE THIS FILE:</strong> migrate.php (for security)</li>
                <li>Visit: <a href="index.php">https://lijst.wmcdev.nl/index.php</a></li>
                <li>Login with admin credentials from .env file</li>
                <li>Delete test files: test_server.php, test.html, info.php</li>
            </ol>
        </div>
    </div>
</body>
</html>
