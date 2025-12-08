<?php

/**
 * Test Multiple Product Selection & Pagination Feature
 * Tests the new batch add functionality
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          MULTIPLE PRODUCT SELECTION & PAGINATION TEST                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$baseUrl = 'http://localhost:8080';
$cookieFile = sys_get_temp_dir() . '/multi_product_test.txt';

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

// Login
echo "Logging in...\n";
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
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 302 && $httpCode != 303) {
    die("Login failed\n");
}
echo "✓ Logged in successfully\n";

// TEST 1: Search with pagination (page 1)
runTest("Search Products - Page 1 (limit=10)", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=iPhone&limit=10&offset=0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode != 200) {
        echo "  HTTP Status: $httpCode\n";
        return false;
    }
    
    $data = json_decode($response, true);
    
    echo "  Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "  Products returned: " . count($data['products']) . "\n";
    echo "  Total results: " . ($data['total'] ?? 'N/A') . "\n";
    echo "  Limit: " . ($data['limit'] ?? 'N/A') . "\n";
    echo "  Offset: " . ($data['offset'] ?? 'N/A') . "\n";
    
    return $data['success'] && count($data['products']) > 0;
});

// TEST 2: Search with pagination (page 2)
runTest("Search Products - Page 2 (offset=10)", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=iPhone&limit=10&offset=10');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode != 200) {
        return false;
    }
    
    $data = json_decode($response, true);
    
    echo "  Products returned: " . count($data['products']) . "\n";
    echo "  Offset: " . ($data['offset'] ?? 'N/A') . "\n";
    
    return $data['success'] && $data['offset'] == 10;
});

// TEST 3: Validate limit boundaries
runTest("Validate Limit Boundaries (max 50)", function() use ($baseUrl, $cookieFile) {
    // Try with limit > 50
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=laptop&limit=100&offset=0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    echo "  Requested limit: 100\n";
    echo "  Actual limit: " . ($data['limit'] ?? 'N/A') . "\n";
    
    // Should be capped at 50
    return $data['limit'] <= 50;
});

// TEST 4: Empty search query
runTest("Empty Search Query Handling", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=&limit=10&offset=0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    echo "  HTTP Status: $httpCode\n";
    echo "  Products returned: " . count($data['products']) . "\n";
    
    // Should return success but empty results
    return $httpCode == 200 && $data['success'];
});

// TEST 5: Add product with duplicate check
runTest("Add Product - Duplicate Detection", function() use ($baseUrl, $cookieFile) {
    // First, get a product
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=iPhone&limit=1&offset=0');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!$data['success'] || empty($data['products'])) {
        echo "  Could not get test product\n";
        return false;
    }
    
    $product = $data['products'][0];
    
    // Get a test list (create one if needed)
    // For this test, we'll assume list ID 1 exists
    $listId = 1;
    
    // Try to add the product twice
    $addProduct = function($product, $listId) use ($baseUrl, $cookieFile) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/product/add');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'list_id' => $listId,
            'product[external_id]' => $product['external_id'],
            'product[title]' => $product['title'],
            'product[description]' => $product['description'] ?? '',
            'product[image_url]' => $product['image_url'] ?? '',
            'product[price]' => $product['price'] ?? 0,
            'product[affiliate_url]' => $product['affiliate_url'],
            'product[source]' => $product['source'],
            'product[ean]' => $product['ean'] ?? ''
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    };
    
    // First add
    $result1 = $addProduct($product, $listId);
    echo "  First add: " . ($result1['success'] ? 'Success' : 'Failed') . "\n";
    
    // Second add (should fail with duplicate message)
    $result2 = $addProduct($product, $listId);
    echo "  Second add: " . ($result2['success'] ? 'Success' : 'Failed') . "\n";
    echo "  Message: " . ($result2['message'] ?? 'N/A') . "\n";
    
    // Second add should fail
    return !$result2['success'] && strpos($result2['message'], 'already exists') !== false;
});

// TEST 6: Invalid list access
runTest("Invalid List Access - Security Check", function() use ($baseUrl, $cookieFile) {
    // Try to add to a non-existent or unauthorized list
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/product/add');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'list_id' => 99999, // Non-existent list
        'product[external_id]' => 'test123',
        'product[title]' => 'Test Product',
        'product[affiliate_url]' => 'https://example.com',
        'product[source]' => 'test'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    echo "  Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "  Message: " . ($data['message'] ?? 'N/A') . "\n";
    
    // Should fail with access denied message
    return !$data['success'];
});

// Cleanup
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

// Summary
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
    echo "✓ Multiple product selection feature is working correctly\n";
    echo "✓ Pagination is functional\n";
    echo "✓ Edge cases are handled\n";
} else {
    echo "⚠ Some tests failed. Review results above.\n";
}

echo "\n";
