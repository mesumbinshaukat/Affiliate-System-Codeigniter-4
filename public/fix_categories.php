<?php
/**
 * Quick Fix for Categories Table
 * Visit: https://lijst.wmcdev.nl/fix_categories.php
 * DELETE AFTER USE!
 */

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

$host = $env['database.default.hostname'] ?? 'localhost';
$database = $env['database.default.database'] ?? 'lijst';
$username = $env['database.default.username'] ?? 'wmcdev';
$password = $env['database.default.password'] ?? '';

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    
    echo "<h1>Fixing Categories Table</h1>";
    
    // Drop foreign key constraints first
    $conn->query("ALTER TABLE lists DROP FOREIGN KEY lists_category_fk");
    echo "<p>✓ Dropped foreign key constraint</p>";
    
    // Drop the old table
    $conn->query("DROP TABLE IF EXISTS categories");
    echo "<p>✓ Dropped old categories table</p>";
    
    // Create new table with correct schema
    $sql = "CREATE TABLE `categories` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    echo "<p>✓ Created new categories table with correct schema</p>";
    
    // Re-add foreign key
    $conn->query("ALTER TABLE lists ADD CONSTRAINT lists_category_fk FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
    echo "<p>✓ Re-added foreign key constraint</p>";
    
    // Insert default categories
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
        $now = date('Y-m-d H:i:s');
        $sql = "INSERT INTO categories (name, slug, icon, status, created_at, updated_at) 
                VALUES ('{$cat['name']}', '{$cat['slug']}', '{$cat['icon']}', 'active', '$now', '$now')";
        $conn->query($sql);
    }
    
    echo "<p>✓ Inserted " . count($categories) . " categories</p>";
    
    $conn->close();
    
    echo "<h2 style='color: green;'>✅ Categories table fixed!</h2>";
    echo "<p><a href='index.php'>Go to Homepage</a></p>";
    echo "<p><strong>DELETE THIS FILE NOW!</strong></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
