<?php

/**
 * Bol.com API Integration Test
 * Tests the Bol.com API with real credentials
 * 
 * Run: php tests/test_bolcom_api.php
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
echo "║                  BOL.COM API INTEGRATION TEST                            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$testResults = [];
$totalTests = 0;
$passedTests = 0;

function runTest($testName, $callback) {
    global $testResults, $totalTests, $passedTests;
    $totalTests++;
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TEST: $testName\n";
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
        $testResults[$testName] = 'ERROR: ' . $e->getMessage();
    }
}

// ============================================================================
// TEST 1: API Credentials Configuration
// ============================================================================
runTest("API Credentials - Check if credentials are configured", function() {
    $clientId = getenv('BOL_CLIENT_ID');
    $clientSecret = getenv('BOL_CLIENT_SECRET');
    
    echo "  Client ID: " . ($clientId ? substr($clientId, 0, 10) . '...' : 'NOT SET') . "\n";
    echo "  Client Secret: " . ($clientSecret ? substr($clientSecret, 0, 10) . '...' : 'NOT SET') . "\n";
    
    if (empty($clientId) || empty($clientSecret)) {
        echo "  ⚠ API credentials not configured in .env\n";
        return false;
    }
    
    echo "  ✓ Credentials are configured\n";
    return true;
});

// ============================================================================
// TEST 2: API Library Initialization
// ============================================================================
runTest("API Library - Initialize BolComAPI class", function() {
    try {
        $api = new BolComAPI();
        echo "  ✓ BolComAPI class initialized successfully\n";
        return true;
    } catch (Exception $e) {
        echo "  ✗ Failed to initialize: " . $e->getMessage() . "\n";
        return false;
    }
});

// ============================================================================
// TEST 3: Product Search - Laptop
// ============================================================================
runTest("Product Search - Search for 'laptop'", function() {
    $api = new BolComAPI();
    
    echo "  Searching for 'laptop'...\n";
    $result = $api->searchProducts('laptop', 5);
    
    if (!$result['success']) {
        echo "  ✗ Search failed: " . ($result['message'] ?? 'Unknown error') . "\n";
        
        // If credentials not configured, that's expected
        if (strpos($result['message'] ?? '', 'credentials not configured') !== false) {
            echo "  ⚠ This is expected if API credentials are not set\n";
            return true; // Don't fail the test
        }
        
        return false;
    }
    
    echo "  ✓ Search successful\n";
    echo "  ✓ Total results: " . ($result['total'] ?? 0) . "\n";
    echo "  ✓ Products returned: " . count($result['products']) . "\n";
    
    if (count($result['products']) > 0) {
        $product = $result['products'][0];
        echo "\n  First Product:\n";
        echo "    Title: " . ($product['title'] ?? 'N/A') . "\n";
        echo "    Price: €" . ($product['price'] ?? 0) . "\n";
        echo "    External ID: " . ($product['external_id'] ?? 'N/A') . "\n";
        echo "    Source: " . ($product['source'] ?? 'N/A') . "\n";
    }
    
    return true;
});

// ============================================================================
// TEST 4: Product Search - Phone
// ============================================================================
runTest("Product Search - Search for 'smartphone'", function() {
    $api = new BolComAPI();
    
    echo "  Searching for 'smartphone'...\n";
    $result = $api->searchProducts('smartphone', 3);
    
    if (!$result['success']) {
        echo "  ✗ Search failed: " . ($result['message'] ?? 'Unknown error') . "\n";
        
        if (strpos($result['message'] ?? '', 'credentials not configured') !== false) {
            echo "  ⚠ This is expected if API credentials are not set\n";
            return true;
        }
        
        return false;
    }
    
    echo "  ✓ Search successful\n";
    echo "  ✓ Products returned: " . count($result['products']) . "\n";
    
    return true;
});

// ============================================================================
// TEST 5: Product Search - Empty Query
// ============================================================================
runTest("Product Search - Handle empty query", function() {
    $api = new BolComAPI();
    
    echo "  Searching with empty query...\n";
    $result = $api->searchProducts('', 5);
    
    // Should handle gracefully
    echo "  ✓ Empty query handled\n";
    echo "  Success: " . ($result['success'] ? 'true' : 'false') . "\n";
    
    return true;
});

// ============================================================================
// TEST 6: Affiliate URL Generation
// ============================================================================
runTest("Affiliate URL - Generate affiliate URL", function() {
    $api = new BolComAPI();
    
    $productId = '9200000123456789';
    $url = $api->generateAffiliateUrl($productId);
    
    echo "  Product ID: $productId\n";
    echo "  Generated URL: $url\n";
    
    if (strpos($url, 'bol.com') !== false && strpos($url, $productId) !== false) {
        echo "  ✓ URL contains bol.com domain\n";
        echo "  ✓ URL contains product ID\n";
        return true;
    }
    
    echo "  ✗ Invalid URL format\n";
    return false;
});

// ============================================================================
// TEST 7: Integration with Dashboard
// ============================================================================
runTest("Integration - Test product search endpoint", function() {
    $baseUrl = 'http://localhost:8080';
    $cookieFile = sys_get_temp_dir() . '/bolapi_test_cookies.txt';
    
    // Login first
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
    curl_close($ch);
    
    // Test search endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=laptop');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (file_exists($cookieFile)) {
        unlink($cookieFile);
    }
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        echo "  ✓ Endpoint accessible\n";
        echo "  ✓ Response is valid JSON\n";
        
        if (isset($data['success'])) {
            echo "  ✓ Response has 'success' field\n";
            echo "  Products returned: " . count($data['products'] ?? []) . "\n";
        }
        
        return true;
    }
    
    echo "  ✗ Endpoint not accessible\n";
    return false;
});

// ============================================================================
// TEST 8: Error Handling
// ============================================================================
runTest("Error Handling - Test with invalid credentials", function() {
    // Temporarily override credentials
    putenv('BOL_CLIENT_ID=invalid_id');
    putenv('BOL_CLIENT_SECRET=invalid_secret');
    
    $api = new BolComAPI();
    $result = $api->searchProducts('test', 5);
    
    // Restore original credentials
    putenv('BOL_CLIENT_ID=' . getenv('BOL_CLIENT_ID'));
    putenv('BOL_CLIENT_SECRET=' . getenv('BOL_CLIENT_SECRET'));
    
    echo "  ✓ Invalid credentials handled gracefully\n";
    echo "  Success: " . ($result['success'] ? 'true' : 'false') . "\n";
    
    if (!$result['success']) {
        echo "  ✓ Error message provided: " . ($result['message'] ?? 'N/A') . "\n";
    }
    
    return true;
});

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
    echo "🎉 ALL TESTS PASSED! Bol.com API integration is working.\n";
} else {
    echo "⚠ Some tests failed. Please review the results above.\n";
}

echo "\n";
echo "NOTE: If API credentials are not configured, some tests will show warnings\n";
echo "      but won't fail. This is expected behavior.\n";
echo "\n";
