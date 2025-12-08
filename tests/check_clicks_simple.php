<?php

/**
 * Simple Database Click Check
 * Direct MySQL connection to verify clicks
 */

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          DATABASE CLICK VERIFICATION                                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Database connection
$host = $_ENV['database.default.hostname'] ?? 'localhost';
$database = $_ENV['database.default.database'] ?? 'lijstje_db';
$username = $_ENV['database.default.username'] ?? 'root';
$password = $_ENV['database.default.password'] ?? '';

echo "Connecting to database...\n";
echo "Host: $host\n";
echo "Database: $database\n\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected successfully\n\n";
} catch (PDOException $e) {
    die("✗ Connection failed: " . $e->getMessage() . "\n");
}

// Check if clicks table exists
echo "Checking table structure...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$stmt = $pdo->query("SHOW TABLES LIKE 'clicks'");
if ($stmt->rowCount() > 0) {
    echo "✓ Table 'clicks' exists\n\n";
} else {
    echo "✗ Table 'clicks' NOT FOUND!\n";
    exit(1);
}

// Get total clicks
$stmt = $pdo->query("SELECT COUNT(*) as total FROM clicks");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$totalClicks = $result['total'];

echo "Click Statistics:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total Clicks: $totalClicks\n\n";

if ($totalClicks == 0) {
    echo "⚠ No clicks found in database.\n";
    echo "  Run the tracking test to generate clicks:\n";
    echo "  php tests/test_affiliate_click_tracking.php\n\n";
    exit(0);
}

echo "✓ Clicks are being recorded!\n\n";

// Recent clicks
echo "Recent Clicks (Last 10):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$stmt = $pdo->query("
    SELECT 
        c.id,
        c.product_id,
        c.list_id,
        c.user_id,
        c.ip_address,
        c.created_at,
        p.title as product_title,
        l.title as list_title
    FROM clicks c
    LEFT JOIN products p ON p.id = c.product_id
    LEFT JOIN lists l ON l.id = c.list_id
    ORDER BY c.created_at DESC
    LIMIT 10
");

$clicks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($clicks as $index => $click) {
    echo "\n" . ($index + 1) . ". Click ID: {$click['id']}\n";
    echo "   Product: " . ($click['product_title'] ?? 'N/A') . " (ID: {$click['product_id']})\n";
    echo "   List: " . ($click['list_title'] ?? 'N/A') . " (ID: " . ($click['list_id'] ?? 'N/A') . ")\n";
    echo "   User ID: " . ($click['user_id'] ?? 'Guest') . "\n";
    echo "   IP: " . ($click['ip_address'] ?? 'N/A') . "\n";
    echo "   Time: {$click['created_at']}\n";
}

// Clicks by product
echo "\n\nTop Products by Clicks:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$stmt = $pdo->query("
    SELECT 
        p.id,
        p.title,
        COUNT(c.id) as click_count
    FROM products p
    LEFT JOIN clicks c ON c.product_id = p.id
    GROUP BY p.id, p.title
    HAVING click_count > 0
    ORDER BY click_count DESC
    LIMIT 10
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($products)) {
    echo "No product click data.\n";
} else {
    foreach ($products as $product) {
        $title = substr($product['title'], 0, 60);
        echo sprintf("  %-62s : %d clicks\n", $title, $product['click_count']);
    }
}

// Clicks by list
echo "\n\nTop Lists by Clicks:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$stmt = $pdo->query("
    SELECT 
        l.id,
        l.title,
        COUNT(c.id) as click_count
    FROM lists l
    LEFT JOIN clicks c ON c.list_id = l.id
    GROUP BY l.id, l.title
    HAVING click_count > 0
    ORDER BY click_count DESC
    LIMIT 10
");

$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($lists)) {
    echo "No list click data.\n";
} else {
    foreach ($lists as $list) {
        $title = substr($list['title'], 0, 60);
        echo sprintf("  %-62s : %d clicks\n", $title, $list['click_count']);
    }
}

// Clicks by date
echo "\n\nClicks by Date (Last 7 Days):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$stmt = $pdo->query("
    SELECT 
        DATE(created_at) as click_date,
        COUNT(*) as click_count
    FROM clicks
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY click_date DESC
");

$dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($dates)) {
    echo "No recent clicks.\n";
} else {
    foreach ($dates as $date) {
        echo sprintf("  %s : %d clicks\n", $date['click_date'], $date['click_count']);
    }
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         VERIFICATION COMPLETE                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "✅ SUCCESS: Click tracking is working!\n";
echo "   - Total clicks recorded: $totalClicks\n";
echo "   - Data is properly stored in database\n";
echo "   - Analytics are available\n";
echo "\n";
echo "View analytics at: http://localhost:8080/dashboard/analytics\n";
echo "\n";
