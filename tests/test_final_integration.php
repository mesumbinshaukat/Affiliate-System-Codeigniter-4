<?php

/**
 * Final Integration Test - Complete System Verification
 * Tests Bol.com API + Frontend + Backend + Database Integration
 * 
 * Run: php tests/test_final_integration.php
 */

$baseUrl = 'http://localhost:8080';
$cookieFile = sys_get_temp_dir() . '/final_integration_cookies.txt';

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          FINAL INTEGRATION TEST - COMPLETE SYSTEM                        ║\n";
echo "║     Bol.com API + Frontend + Backend + Database Verification            ║\n";
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
// TEST 1: User Authentication
// ============================================================================
runTest("User Login - mesum@gmail.com", function() use ($baseUrl, $cookieFile) {
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
    
    // Accept both 302 and 303 redirects (both are valid)
    if (($httpCode == 302 || $httpCode == 303) && strpos($redirectUrl, '/dashboard') !== false) {
        echo "  ✓ Login successful, redirected to dashboard\n";
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 2: Dashboard Access
// ============================================================================
runTest("Access Dashboard", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        if (strpos($response, 'Dashboard') !== false || strpos($response, 'dashboard') !== false) {
            echo "  ✓ Dashboard page loaded\n";
            return true;
        }
    }
    
    return false;
});

// ============================================================================
// TEST 3: Bol.com API Product Search via Dashboard
// ============================================================================
runTest("Bol.com API - Search for 'iPhone' via Dashboard", function() use ($baseUrl, $cookieFile) {
    echo "  Searching for iPhone products...\n";
    
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
        echo "  API Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "  Products Found: " . count($data['products'] ?? []) . "\n";
        
        if ($data['success'] && count($data['products']) > 0) {
            $product = $data['products'][0];
            echo "  ✓ Products fetched successfully\n";
            echo "    First Product: " . ($product['title'] ?? 'N/A') . "\n";
            echo "    Price: €" . ($product['price'] ?? 0) . "\n";
            echo "    Has Image: " . (isset($product['image_url']) ? 'Yes' : 'No') . "\n";
            echo "    Has Affiliate URL: " . (isset($product['affiliate_url']) ? 'Yes' : 'No') . "\n";
            return true;
        }
    }
    
    return false;
});

// ============================================================================
// TEST 4: Search Different Product
// ============================================================================
runTest("Bol.com API - Search for 'Laptop'", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=Laptop');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data['success'] && count($data['products']) > 0) {
            echo "  ✓ Laptop products found: " . count($data['products']) . "\n";
            return true;
        }
    }
    
    return false;
});

// ============================================================================
// TEST 5: Public Homepage
// ============================================================================
runTest("Public Homepage Access", function() use ($baseUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "  ✓ Homepage accessible\n";
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 6: Analytics Page
// ============================================================================
runTest("Analytics Dashboard", function() use ($baseUrl, $cookieFile) {
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
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 7: Lists Page
// ============================================================================
runTest("User Lists Page", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/lists');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "  ✓ Lists page accessible\n";
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 8: Create List Page
// ============================================================================
runTest("Create List Page Access", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/list/create');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 200) {
        echo "  ✓ Create list page accessible\n";
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 9: Logout
// ============================================================================
runTest("User Logout", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/logout');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    // Accept both 302 and 303 redirects
    if (($httpCode == 302 || $httpCode == 303) && strpos($redirectUrl, '/login') !== false) {
        echo "  ✓ Logout successful\n";
        return true;
    }
    
    return false;
});

// ============================================================================
// TEST 10: Verify Session Cleared
// ============================================================================
runTest("Verify Session Cleared After Logout", function() use ($baseUrl, $cookieFile) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    
    echo "  HTTP Status: $httpCode\n";
    
    if ($httpCode == 302 && strpos($redirectUrl, '/login') !== false) {
        echo "  ✓ Dashboard redirects to login (session cleared)\n";
        return true;
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

// ============================================================================
// SUMMARY
// ============================================================================
echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         FINAL TEST SUMMARY                               ║\n";
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
    echo "🎉 ALL INTEGRATION TESTS PASSED!\n\n";
    echo "✅ SYSTEM STATUS: FULLY FUNCTIONAL\n\n";
    echo "✓ User Authentication: Working\n";
    echo "✓ Dashboard: Working\n";
    echo "✓ Bol.com API Integration: Working (100% success)\n";
    echo "✓ Product Search: Working\n";
    echo "✓ Frontend Pages: Working\n";
    echo "✓ Analytics: Working\n";
    echo "✓ Session Management: Working\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "                    🚀 READY FOR PRODUCTION USE 🚀\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
} else {
    echo "⚠ Some integration tests failed. Review results above.\n";
}

echo "\n";
