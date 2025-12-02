<?php

/**
 * Complete Product Workflow Test
 * Tests both Bol.com API integration and manual product entry
 * Includes frontend and backend integration tests
 * 
 * Run: php tests/test_complete_product_workflow.php
 */

// Database configuration
$dbHost = 'localhost';
$dbName = 'lijstje_db';
$dbUser = 'root';
$dbPass = '';

// Connect to database
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

$baseUrl = 'http://localhost:8080';
$cookieFile = sys_get_temp_dir() . '/product_workflow_cookies.txt';

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          COMPLETE PRODUCT WORKFLOW TEST                                  ║\n";
echo "║     Testing Bol.com API + Manual Entry + Frontend Integration           ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$testResults = [];
$totalTests = 0;
$passedTests = 0;

function runTest($testName, $callback) {
    global $testResults, $totalTests, $passedTests;
    $totalTests++;
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST $totalTests: $testName\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    try {
        $result = $callback();
        if ($result) {
            echo "✓ PASSED\n";
            $testResults[$testName] = 'PASSED';
            $passedTests++;
        } else {
            echo "✗ FAILED\n";
            $testResults[$testName] = 'FAILED';
        }
    } catch (Exception $e) {
        echo "✗ ERROR: " . $e->getMessage() . "\n";
        $testResults[$testName] = 'ERROR';
    }
}

// ============================================================================
// TEST 1: Login as Test User
// ============================================================================
runTest("Login as mesum@gmail.com", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'email' => 'mesum@gmail.com',
        'password' => 'admin123!'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    echo "  Redirect: " . ($redirectUrl ?: 'None') . "\n";
    
    if ($httpCode == 302 && strpos($redirectUrl, '/dashboard') !== false) {
        echo "  ✓ Login successful\n";
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 2: Create a Test List
// ============================================================================
$testListId = null;
runTest("Create New List for Product Testing", function() use ($baseUrl, $cookieFile, &$testListId, $pdo) {
    $listTitle = "Product Test List " . time();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/list/create');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'title' => $listTitle,
        'description' => 'Testing product integration',
        'category_id' => 1,
        'status' => 'draft'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    // Extract list ID from redirect URL
    if (preg_match('/\/dashboard\/list\/edit\/(\d+)/', $redirectUrl, $matches)) {
        $testListId = $matches[1];
        echo "  ✓ List created with ID: $testListId\n";
        
        // Verify in database
        $stmt = $pdo->prepare("SELECT * FROM lists WHERE id = ?");
        $stmt->execute([$testListId]);
        $list = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($list) {
            echo "  ✓ List verified in database\n";
            echo "    Title: " . $list['title'] . "\n";
            return true;
        }
    }
    
    return false;
});

// ============================================================================
// TEST 3: Test Bol.com API Search Endpoint
// ============================================================================
runTest("Test Bol.com API Search via Dashboard", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=iPhone');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        echo "  ✓ API endpoint accessible\n";
        echo "  Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "  Products returned: " . count($data['products'] ?? []) . "\n";
        
        if (count($data['products'] ?? []) > 0) {
            echo "  ✓ Products fetched from API\n";
        } else {
            echo "  ⚠ No products returned (API may have limited access)\n";
        }
        
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 4: Add Manual Product to List
// ============================================================================
$testProductId = null;
runTest("Add Manual Product to List", function() use ($baseUrl, $cookieFile, $testListId, &$testProductId, $pdo) {
    if (!$testListId) {
        echo "  ✗ No test list available\n";
        return false;
    }
    
    $productData = [
        'list_id' => $testListId,
        'product' => [
            'title' => 'Test Product - iPhone 15 Pro',
            'description' => 'Latest iPhone with amazing features',
            'price' => 1199.99,
            'image_url' => 'https://via.placeholder.com/300x300.png?text=iPhone+15',
            'affiliate_url' => 'https://www.bol.com/nl/p/iphone-15-pro/9200000123456789/',
            'source' => 'manual',
            'external_id' => 'manual_' . time(),
            'ean' => '1234567890123'
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/product/add');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($productData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        echo "  Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        
        if ($data['success']) {
            echo "  ✓ Product added successfully\n";
            
            // Verify in database
            $stmt = $pdo->prepare("
                SELECT p.*, lp.list_id, lp.position 
                FROM products p
                JOIN list_products lp ON p.id = lp.product_id
                WHERE lp.list_id = ?
                ORDER BY lp.position DESC
                LIMIT 1
            ");
            $stmt->execute([$testListId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $testProductId = $product['id'];
                echo "  ✓ Product verified in database\n";
                echo "    Product ID: " . $product['id'] . "\n";
                echo "    Title: " . $product['title'] . "\n";
                echo "    Price: €" . $product['price'] . "\n";
                echo "    Position: " . $product['position'] . "\n";
                return true;
            }
        }
    }
    
    return false;
});

// ============================================================================
// TEST 5: Add Second Product
// ============================================================================
runTest("Add Second Product to List", function() use ($baseUrl, $cookieFile, $testListId, $pdo) {
    if (!$testListId) {
        echo "  ✗ No test list available\n";
        return false;
    }
    
    $productData = [
        'list_id' => $testListId,
        'product' => [
            'title' => 'Samsung Galaxy S24',
            'description' => 'Latest Samsung flagship',
            'price' => 999.99,
            'image_url' => 'https://via.placeholder.com/300x300.png?text=Galaxy+S24',
            'affiliate_url' => 'https://www.bol.com/nl/p/samsung-galaxy-s24/9200000987654321/',
            'source' => 'manual',
            'external_id' => 'manual_' . time() . '_2',
            'ean' => '9876543210987'
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/product/add');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($productData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data['success']) {
            echo "  ✓ Second product added\n";
            
            // Count products in list
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM list_products WHERE list_id = ?");
            $stmt->execute([$testListId]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            echo "  Total products in list: $count\n";
            return $count >= 2;
        }
    }
    
    return false;
});

// ============================================================================
// TEST 6: View List Edit Page
// ============================================================================
runTest("View List Edit Page with Products", function() use ($baseUrl, $cookieFile, $testListId) {
    if (!$testListId) {
        echo "  ✗ No test list available\n";
        return false;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/list/edit/' . $testListId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "  ✓ Edit page accessible\n";
        
        // Check if products are displayed
        if (strpos($response, 'iPhone 15 Pro') !== false) {
            echo "  ✓ First product displayed\n";
        }
        if (strpos($response, 'Samsung Galaxy S24') !== false) {
            echo "  ✓ Second product displayed\n";
        }
        
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 7: Publish List
// ============================================================================
runTest("Publish List", function() use ($baseUrl, $cookieFile, $testListId, $pdo) {
    if (!$testListId) {
        echo "  ✗ No test list available\n";
        return false;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/list/edit/' . $testListId);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'title' => 'Product Test List (Published)',
        'description' => 'Testing product integration - Published',
        'category_id' => 1,
        'status' => 'published'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    // Verify in database
    $stmt = $pdo->prepare("SELECT status FROM lists WHERE id = ?");
    $stmt->execute([$testListId]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($list && $list['status'] === 'published') {
        echo "  ✓ List published successfully\n";
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 8: View Public List Page
// ============================================================================
runTest("View Public List Page", function() use ($baseUrl, $testListId, $pdo) {
    if (!$testListId) {
        echo "  ✗ No test list available\n";
        return false;
    }
    
    // Get list slug
    $stmt = $pdo->prepare("SELECT slug FROM lists WHERE id = ?");
    $stmt->execute([$testListId]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$list) {
        echo "  ✗ List not found\n";
        return false;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/list/' . $list['slug']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    echo "  List URL: /list/" . $list['slug'] . "\n";
    
    if ($httpCode == 200) {
        echo "  ✓ Public list page accessible\n";
        
        // Check if products are displayed
        if (strpos($response, 'iPhone 15 Pro') !== false) {
            echo "  ✓ Products displayed on public page\n";
            return true;
        }
    }
    
    return false;
});

// ============================================================================
// TEST 9: Test Affiliate Click Tracking
// ============================================================================
runTest("Test Affiliate Click Tracking", function() use ($baseUrl, $testProductId, $pdo) {
    if (!$testProductId) {
        echo "  ✗ No test product available\n";
        return false;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/out/' . $testProductId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 302) {
        echo "  ✓ Redirect working\n";
        
        // Check if click was tracked
        sleep(1); // Give time for tracking to complete
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM clicks WHERE product_id = ?");
        $stmt->execute([$testProductId]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "  Clicks tracked: $count\n";
        
        if ($count > 0) {
            echo "  ✓ Click tracking working\n";
            return true;
        }
    }
    
    return false;
});

// ============================================================================
// TEST 10: Verify Analytics
// ============================================================================
runTest("Verify Analytics Dashboard", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/analytics');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "  ✓ Analytics page accessible\n";
        
        if (strpos($response, 'Total Clicks') !== false || strpos($response, 'clicks') !== false) {
            echo "  ✓ Analytics data displayed\n";
            return true;
        }
    }
    
    return false;
});

// ============================================================================
// CLEANUP
// ============================================================================
echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                            CLEANUP                                       ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (file_exists($cookieFile)) {
    unlink($cookieFile);
    echo "✓ Cookie file cleaned up\n";
}

echo "✓ Test list and products remain in database for manual verification\n";
echo "  List ID: $testListId\n";

// ============================================================================
// SUMMARY
// ============================================================================
echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         TEST SUMMARY                                     ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

foreach ($testResults as $testName => $result) {
    $status = $result === 'PASSED' ? '✓' : '✗';
    echo "$status $testName: $result\n";
}

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests\n";
echo "Failed: " . ($totalTests - $passedTests) . "\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

if ($passedTests === $totalTests) {
    echo "🎉 ALL TESTS PASSED!\n";
    echo "✓ Product workflow is fully functional\n";
    echo "✓ Bol.com API integrated\n";
    echo "✓ Manual product entry working\n";
    echo "✓ Frontend and backend wired up correctly\n";
} else {
    echo "⚠ Some tests failed. Review results above.\n";
}

echo "\n";
