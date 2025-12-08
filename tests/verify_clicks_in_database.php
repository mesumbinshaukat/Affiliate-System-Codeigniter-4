<?php

/**
 * Verify Clicks in Database
 * Direct database check to confirm clicks are being recorded
 * 
 * Run: php tests/verify_clicks_in_database.php
 */

// Load CodeIgniter
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsConfig = require __DIR__ . '/../app/Config/Paths.php';
$paths = new \Config\Paths();
foreach ($pathsConfig as $key => $value) {
    $paths->$key = $value;
}

$app = \Config\Services::codeigniter();
$app->initialize();

// Load database
$db = \Config\Database::connect();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          DATABASE CLICK VERIFICATION                                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check if clicks table exists
echo "Checking database structure...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$tables = $db->listTables();
if (in_array('clicks', $tables)) {
    echo "✓ Table 'clicks' exists\n\n";
} else {
    echo "✗ Table 'clicks' NOT FOUND!\n";
    echo "  Please run migrations to create the table.\n";
    exit(1);
}

// Get table structure
echo "Table Structure:\n";
$fields = $db->getFieldData('clicks');
foreach ($fields as $field) {
    echo "  - {$field->name} ({$field->type})\n";
}
echo "\n";

// Get total click count
echo "Click Statistics:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$totalClicks = $db->table('clicks')->countAllResults();
echo "Total Clicks Recorded: $totalClicks\n\n";

if ($totalClicks == 0) {
    echo "⚠ No clicks found in database.\n";
    echo "  This could mean:\n";
    echo "  1. No one has clicked on affiliate links yet\n";
    echo "  2. Click tracking is not working\n";
    echo "  3. Database connection issue\n\n";
} else {
    echo "✓ Clicks are being recorded!\n\n";
    
    // Get recent clicks
    echo "Recent Clicks (Last 10):\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $recentClicks = $db->table('clicks')
        ->select('clicks.*, products.title as product_title, lists.title as list_title')
        ->join('products', 'products.id = clicks.product_id', 'left')
        ->join('lists', 'lists.id = clicks.list_id', 'left')
        ->orderBy('clicks.created_at', 'DESC')
        ->limit(10)
        ->get()
        ->getResultArray();
    
    foreach ($recentClicks as $index => $click) {
        echo "\n" . ($index + 1) . ". Click ID: {$click['id']}\n";
        echo "   Product: " . ($click['product_title'] ?? 'N/A') . " (ID: {$click['product_id']})\n";
        echo "   List: " . ($click['list_title'] ?? 'N/A') . " (ID: " . ($click['list_id'] ?? 'N/A') . ")\n";
        echo "   User ID: " . ($click['user_id'] ?? 'Guest') . "\n";
        echo "   IP Address: " . ($click['ip_address'] ?? 'N/A') . "\n";
        echo "   User Agent: " . substr($click['user_agent'] ?? 'N/A', 0, 50) . "...\n";
        echo "   Clicked At: {$click['created_at']}\n";
    }
    
    // Click statistics by product
    echo "\n\nClicks by Product:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $productStats = $db->query("
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
    ")->getResultArray();
    
    if (empty($productStats)) {
        echo "No product click data available.\n";
    } else {
        foreach ($productStats as $stat) {
            echo sprintf("  %-50s : %d clicks\n", 
                substr($stat['title'], 0, 50), 
                $stat['click_count']
            );
        }
    }
    
    // Click statistics by list
    echo "\n\nClicks by List:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $listStats = $db->query("
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
    ")->getResultArray();
    
    if (empty($listStats)) {
        echo "No list click data available.\n";
    } else {
        foreach ($listStats as $stat) {
            echo sprintf("  %-50s : %d clicks\n", 
                substr($stat['title'], 0, 50), 
                $stat['click_count']
            );
        }
    }
    
    // Clicks by date
    echo "\n\nClicks by Date (Last 7 Days):\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $dateStats = $db->query("
        SELECT 
            DATE(created_at) as click_date,
            COUNT(*) as click_count
        FROM clicks
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY click_date DESC
    ")->getResultArray();
    
    if (empty($dateStats)) {
        echo "No recent click data available.\n";
    } else {
        foreach ($dateStats as $stat) {
            echo sprintf("  %s : %d clicks\n", $stat['click_date'], $stat['click_count']);
        }
    }
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         VERIFICATION COMPLETE                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if ($totalClicks > 0) {
    echo "✅ SUCCESS: Click tracking is working correctly!\n";
    echo "   - Clicks are being recorded in the database\n";
    echo "   - Total clicks: $totalClicks\n";
    echo "   - Data is properly structured\n";
    echo "   - Analytics should be available in dashboard\n";
} else {
    echo "⚠ WARNING: No clicks recorded yet.\n";
    echo "   Run the click tracking test first:\n";
    echo "   php tests/test_affiliate_click_tracking.php\n";
}

echo "\n";
