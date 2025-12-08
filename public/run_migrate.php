<?php
/**
 * Database Migration Runner - Fixed Version
 * Visit: https://lijst.wmcdev.nl/run_migrate.php?token=run_migrations_now
 * DELETE THIS FILE AFTER USE!
 */

// Security token
$token = $_GET['token'] ?? '';
if ($token !== 'run_migrations_now') {
    die('Access denied. Use: run_migrate.php?token=run_migrations_now');
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
            die('</div></div></body></html>');
        }
        
        echo '</div>';
        
        // Step 2: Drop all tables to recreate with correct schema
        echo '<div class="step">';
        echo '<h3>Step 2: Dropping Existing Tables</h3>';
        
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        $tables = ['clicks', 'list_products', 'lists', 'products', 'categories', 'users', 'affiliate_sources', 'settings'];
        foreach ($tables as $table) {
            if ($conn->query("DROP TABLE IF EXISTS `$table`")) {
                echo '<div class="info">✓ Dropped table: ' . $table . '</div>';
            }
        }
        
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        echo '</div>';
        
        // Step 3: Create tables with correct schema
        echo '<div class="step">';
        echo '<h3>Step 3: Creating Tables</h3>';
        
        $migrations = [
            'users' => "CREATE TABLE `users` (
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
            
            'categories' => "CREATE TABLE `categories` (
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
            
            'products' => "CREATE TABLE `products` (
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
            
            'lists' => "CREATE TABLE `lists` (
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
                CONSTRAINT `lists_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `lists_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'list_products' => "CREATE TABLE `list_products` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `list_id` int(11) UNSIGNED NOT NULL,
                `product_id` int(11) UNSIGNED NOT NULL,
                `position` int(11) DEFAULT 0,
                `custom_note` text DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `list_id` (`list_id`),
                KEY `product_id` (`product_id`),
                CONSTRAINT `list_products_list_fk` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `list_products_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'clicks' => "CREATE TABLE `clicks` (
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
                CONSTRAINT `clicks_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `clicks_list_fk` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `clicks_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'affiliate_sources' => "CREATE TABLE `affiliate_sources` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `slug` varchar(100) NOT NULL,
                `api_endpoint` varchar(255) DEFAULT NULL,
                `status` enum('active','inactive') DEFAULT 'active',
                `settings` text DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'settings' => "CREATE TABLE `settings` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `key` varchar(100) NOT NULL,
                `value` text DEFAULT NULL,
                `type` varchar(50) DEFAULT 'string',
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        ];
        
        foreach ($migrations as $table => $sql) {
            if ($conn->query($sql)) {
                echo '<div class="success">✓ Created table: ' . $table . '</div>';
            } else {
                echo '<div class="error">✗ Error creating ' . $table . ': ' . $conn->error . '</div>';
            }
        }
        
        echo '</div>';
        
        // Step 4: Insert default data
        echo '<div class="step">';
        echo '<h3>Step 4: Inserting Default Data</h3>';
        
        // Insert categories
        $categories = [
            ['Electronics', 'electronics', 'Latest gadgets and tech products', 'laptop'],
            ['Books', 'books', 'Best books and reading materials', 'book'],
            ['Home & Garden', 'home-garden', 'Home improvement and garden supplies', 'home'],
            ['Sports & Outdoors', 'sports-outdoors', 'Sports equipment and outdoor gear', 'futbol'],
            ['Fashion', 'fashion', 'Clothing and accessories', 'tshirt'],
            ['Beauty & Health', 'beauty-health', 'Beauty products and health items', 'heart'],
            ['Toys & Games', 'toys-games', 'Fun toys and games for all ages', 'gamepad'],
            ['Food & Beverages', 'food-beverages', 'Delicious food and drinks', 'utensils']
        ];
        
        foreach ($categories as $cat) {
            $stmt = $conn->prepare("INSERT INTO categories (name, slug, description, icon, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
            $stmt->bind_param("ssss", $cat[0], $cat[1], $cat[2], $cat[3]);
            if ($stmt->execute()) {
                echo '<div class="success">✓ Inserted category: ' . $cat[0] . '</div>';
            }
        }
        
        echo '</div>';
        
        echo '<div class="success" style="margin-top: 30px;">';
        echo '<h2>✅ Migration Completed Successfully!</h2>';
        echo '<p>All tables have been created with the correct schema.</p>';
        echo '<p><strong>Next steps:</strong></p>';
        echo '<ol>';
        echo '<li>Test registration at: <a href="index.php/register">Register Page</a></li>';
        echo '<li>Test login at: <a href="index.php/login">Login Page</a></li>';
        echo '<li><strong style="color: red;">DELETE THIS FILE (run_migrate.php) for security!</strong></li>';
        echo '</ol>';
        echo '</div>';
        
        $conn->close();
        ?>
        
    </div>
</body>
</html>
