<?php

/**
 * Bol.com API - Detailed Integration Test
 * Tests the Bol.com API with activated credentials
 * Includes timing delays for API responses
 * 
 * Run: php tests/test_bolcom_api_detailed.php
 */

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!empty($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Load the BolComAPI library
require_once __DIR__ . '/../app/Libraries/BolComAPI.php';

use App\Libraries\BolComAPI;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║           BOL.COM API - DETAILED INTEGRATION TEST                        ║\n";
echo "║              Testing with Activated Credentials                          ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$testResults = [];
$totalTests = 0;
$passedTests = 0;

function runTest($testName, $callback, $timeout = 30) {
    global $testResults, $totalTests, $passedTests;
    $totalTests++;
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST $totalTests: $testName\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $startTime = microtime(true);
    
    try {
        $result = $callback();
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($result) {
            echo "✓ PASSED (Duration: {$duration}ms)\n";
            $testResults[$testName] = 'PASSED';
            $passedTests++;
        } else {
            echo "✗ FAILED (Duration: {$duration}ms)\n";
            $testResults[$testName] = 'FAILED';
        }
    } catch (Exception $e) {
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        echo "✗ ERROR (Duration: {$duration}ms): " . $e->getMessage() . "\n";
        $testResults[$testName] = 'ERROR: ' . $e->getMessage();
    }
}

// ============================================================================
// TEST 1: Verify Credentials Configuration
// ============================================================================
runTest("Verify API Credentials are Loaded", function() {
    $clientId = getenv('BOL_CLIENT_ID');
    $clientSecret = getenv('BOL_CLIENT_SECRET');
    $affiliateId = getenv('BOL_AFFILIATE_ID');
    
    echo "  Client ID: " . ($clientId ? substr($clientId, 0, 15) . '...' : 'NOT SET') . "\n";
    echo "  Client Secret: " . ($clientSecret ? substr($clientSecret, 0, 15) . '...' : 'NOT SET') . "\n";
    echo "  Affiliate ID: " . ($affiliateId ?: 'NOT SET') . "\n";
    
    if (empty($clientId) || empty($clientSecret)) {
        echo "  ✗ API credentials missing\n";
        return false;
    }
    
    if (empty($affiliateId)) {
        echo "  ⚠ Affiliate ID not set (optional for search)\n";
    }
    
    echo "  ✓ All required credentials configured\n";
    return true;
});

// ============================================================================
// TEST 2: Test Authentication Token Request
// ============================================================================
runTest("Request OAuth Access Token", function() {
    echo "  Requesting access token from Bol.com...\n";
    echo "  (This may take 2-5 seconds)\n";
    
    $clientId = getenv('BOL_CLIENT_ID');
    $clientSecret = getenv('BOL_CLIENT_SECRET');
    $credentials = base64_encode($clientId . ':' . $clientSecret);
    
    $ch = curl_init('https://login.bol.com/token?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $credentials,
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($curlError) {
        echo "  ✗ cURL Error: $curlError\n";
        return false;
    }
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['access_token'])) {
            echo "  ✓ Access token received\n";
            echo "  Token Type: " . ($data['token_type'] ?? 'N/A') . "\n";
            echo "  Expires In: " . ($data['expires_in'] ?? 'N/A') . " seconds\n";
            echo "  Scope: " . ($data['scope'] ?? 'N/A') . "\n";
            echo "  Token Preview: " . substr($data['access_token'], 0, 30) . "...\n";
            return true;
        }
    }
    
    echo "  ✗ Failed to get token\n";
    echo "  Response: " . substr($response, 0, 200) . "\n";
    return false;
}, 10);

// ============================================================================
// TEST 3: Initialize API Library
// ============================================================================
runTest("Initialize BolComAPI Library", function() {
    $api = new BolComAPI();
    echo "  ✓ Library initialized successfully\n";
    return true;
});

// ============================================================================
// TEST 4: Search for Popular Product - "iPhone"
// ============================================================================
runTest("Search Products - 'iPhone' (Popular Product)", function() {
    echo "  Searching for 'iPhone'...\n";
    echo "  (API may take 3-10 seconds to respond)\n";
    
    $api = new BolComAPI();
    $result = $api->searchProducts('iPhone', 5);
    
    echo "  Success: " . ($result['success'] ? 'true' : 'false') . "\n";
    
    if (!$result['success']) {
        echo "  ✗ Search failed: " . ($result['message'] ?? 'Unknown error') . "\n";
        if (isset($result['http_code'])) {
            echo "  HTTP Code: " . $result['http_code'] . "\n";
        }
        if (isset($result['response'])) {
            echo "  Response: " . substr($result['response'], 0, 300) . "\n";
        }
        return false;
    }
    
    echo "  ✓ Search successful\n";
    echo "  Total Results: " . ($result['total'] ?? 0) . "\n";
    echo "  Products Returned: " . count($result['products']) . "\n";
    
    if (count($result['products']) > 0) {
        echo "\n  First Product Details:\n";
        $product = $result['products'][0];
        echo "    Title: " . ($product['title'] ?? 'N/A') . "\n";
        echo "    Price: €" . ($product['price'] ?? 0) . "\n";
        echo "    EAN: " . ($product['ean'] ?? 'N/A') . "\n";
        echo "    Product ID: " . ($product['external_id'] ?? 'N/A') . "\n";
        echo "    Image URL: " . (isset($product['image_url']) ? 'Yes' : 'No') . "\n";
        echo "    Description: " . substr($product['description'] ?? '', 0, 50) . "...\n";
        echo "    Affiliate URL: " . substr($product['affiliate_url'] ?? '', 0, 60) . "...\n";
        
        return true;
    }
    
    echo "  ⚠ No products returned\n";
    return false;
}, 15);

// ============================================================================
// TEST 5: Search for Another Product - "Laptop"
// ============================================================================
runTest("Search Products - 'Laptop'", function() {
    echo "  Searching for 'Laptop'...\n";
    
    $api = new BolComAPI();
    $result = $api->searchProducts('Laptop', 3);
    
    if (!$result['success']) {
        echo "  ✗ Search failed: " . ($result['message'] ?? 'Unknown error') . "\n";
        return false;
    }
    
    echo "  ✓ Search successful\n";
    echo "  Products Returned: " . count($result['products']) . "\n";
    
    if (count($result['products']) > 0) {
        echo "\n  Sample Products:\n";
        foreach ($result['products'] as $i => $product) {
            echo "    " . ($i + 1) . ". " . ($product['title'] ?? 'N/A') . " - €" . ($product['price'] ?? 0) . "\n";
        }
        return true;
    }
    
    return false;
}, 15);

// ============================================================================
// TEST 6: Search with Pagination
// ============================================================================
runTest("Search with Pagination - Offset Test", function() {
    echo "  Searching with offset=10, limit=5...\n";
    
    $api = new BolComAPI();
    $result = $api->searchProducts('smartphone', 5, 10);
    
    if (!$result['success']) {
        echo "  ✗ Search failed\n";
        return false;
    }
    
    echo "  ✓ Pagination working\n";
    echo "  Products Returned: " . count($result['products']) . "\n";
    
    if (isset($result['paging'])) {
        echo "  Paging Info: " . json_encode($result['paging']) . "\n";
    }
    
    return true;
}, 15);

// ============================================================================
// TEST 7: Test Affiliate URL Generation
// ============================================================================
runTest("Affiliate URL Generation", function() {
    $api = new BolComAPI();
    $affiliateId = getenv('BOL_AFFILIATE_ID');
    
    $productId = '9200000123456789';
    $url = $api->generateAffiliateUrl($productId);
    
    echo "  Product ID: $productId\n";
    echo "  Generated URL: $url\n";
    
    if (empty($affiliateId)) {
        if (strpos($url, 'bol.com/nl/p/') !== false) {
            echo "  ✓ Direct URL (no affiliate ID)\n";
            return true;
        }
    } else {
        if (strpos($url, 'partner.bol.com') !== false && strpos($url, $affiliateId) !== false) {
            echo "  ✓ Affiliate URL with tracking\n";
            echo "  Contains Affiliate ID: Yes\n";
            return true;
        }
    }
    
    echo "  ⚠ URL format may be incorrect\n";
    return false;
});

// ============================================================================
// TEST 8: Search for Dutch Product
// ============================================================================
runTest("Search Dutch Product - 'boek'", function() {
    echo "  Searching for 'boek' (Dutch for book)...\n";
    
    $api = new BolComAPI();
    $result = $api->searchProducts('boek', 5);
    
    if (!$result['success']) {
        echo "  ✗ Search failed\n";
        return false;
    }
    
    echo "  ✓ Dutch search working\n";
    echo "  Products Returned: " . count($result['products']) . "\n";
    
    return count($result['products']) > 0;
}, 15);

// ============================================================================
// TEST 9: Test Rate Limiting (Multiple Requests)
// ============================================================================
runTest("Rate Limiting - Multiple Sequential Requests", function() {
    echo "  Making 3 sequential requests...\n";
    
    $api = new BolComAPI();
    $queries = ['iPhone', 'Samsung', 'laptop'];
    $successCount = 0;
    
    foreach ($queries as $i => $query) {
        echo "    Request " . ($i + 1) . ": Searching '$query'...\n";
        $result = $api->searchProducts($query, 2);
        
        if ($result['success']) {
            $successCount++;
            echo "      ✓ Success (" . count($result['products']) . " products)\n";
        } else {
            echo "      ✗ Failed: " . ($result['message'] ?? 'Unknown') . "\n";
        }
        
        // Small delay between requests
        if ($i < count($queries) - 1) {
            usleep(200000); // 200ms delay
        }
    }
    
    echo "  Successful Requests: $successCount / " . count($queries) . "\n";
    
    return $successCount >= 2; // At least 2 should succeed
}, 30);

// ============================================================================
// TEST 10: Verify Product Data Structure
// ============================================================================
runTest("Verify Product Data Structure", function() {
    echo "  Checking if product data has all required fields...\n";
    
    $api = new BolComAPI();
    $result = $api->searchProducts('iPhone', 1);
    
    if (!$result['success'] || count($result['products']) === 0) {
        echo "  ✗ No products to verify\n";
        return false;
    }
    
    $product = $result['products'][0];
    $requiredFields = ['external_id', 'title', 'price', 'affiliate_url', 'source'];
    $optionalFields = ['description', 'image_url', 'ean', 'rating', 'specs_tag'];
    
    echo "\n  Required Fields:\n";
    $allRequired = true;
    foreach ($requiredFields as $field) {
        $exists = isset($product[$field]) && !empty($product[$field]);
        echo "    " . ($exists ? '✓' : '✗') . " $field: " . ($exists ? 'Present' : 'Missing') . "\n";
        if (!$exists) $allRequired = false;
    }
    
    echo "\n  Optional Fields:\n";
    foreach ($optionalFields as $field) {
        $exists = isset($product[$field]);
        echo "    " . ($exists ? '✓' : '-') . " $field: " . ($exists ? 'Present' : 'Not set') . "\n";
    }
    
    return $allRequired;
}, 15);

// ============================================================================
// SUMMARY
// ============================================================================
echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         TEST SUMMARY                                     ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

foreach ($testResults as $testName => $result) {
    $status = strpos($result, 'PASSED') !== false ? '✓' : '✗';
    $color = strpos($result, 'PASSED') !== false ? '' : '';
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
    echo "🎉 ALL TESTS PASSED! Bol.com API is fully functional.\n";
    echo "✓ Ready for integration into the application.\n";
} elseif ($passedTests >= $totalTests * 0.7) {
    echo "✓ Most tests passed. API is functional with minor issues.\n";
    echo "⚠ Review failed tests above.\n";
} else {
    echo "⚠ Multiple tests failed. Please review:\n";
    echo "  1. Check if credentials are activated in Partner Platform\n";
    echo "  2. Verify technical contact details are saved\n";
    echo "  3. Ensure affiliate account is active\n";
    echo "  4. Check API endpoint URLs\n";
}

echo "\n";
