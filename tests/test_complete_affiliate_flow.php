<?php

/**
 * Complete Affiliate Flow Test
 * Tests the entire flow from API → Database → Redirect → Bol.com
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          COMPLETE AFFILIATE FLOW TEST                                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$baseUrl = 'http://localhost:8080';
$cookieFile = sys_get_temp_dir() . '/affiliate_flow_cookies.txt';

// Step 1: Login
echo "STEP 1: Login\n";
echo str_repeat('-', 78) . "\n";

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
curl_close($ch);

if ($httpCode == 302 || $httpCode == 303) {
    echo "✓ Login successful\n\n";
} else {
    echo "✗ Login failed (HTTP $httpCode)\n";
    exit(1);
}

// Step 2: Search for products via API
echo "STEP 2: Search Products via Dashboard API\n";
echo str_repeat('-', 78) . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=iPhone');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    echo "✗ API search failed (HTTP $httpCode)\n";
    exit(1);
}

$data = json_decode($response, true);

if (!$data['success'] || empty($data['products'])) {
    echo "✗ No products returned\n";
    exit(1);
}

$firstProduct = $data['products'][0];
echo "✓ Products fetched: " . count($data['products']) . "\n";
echo "  First Product: " . $firstProduct['title'] . "\n";
echo "  Price: €" . $firstProduct['price'] . "\n";
echo "  Product ID: " . $firstProduct['external_id'] . "\n\n";

// Step 3: Analyze the affiliate URL
echo "STEP 3: Analyze Affiliate URL Structure\n";
echo str_repeat('-', 78) . "\n";

$affiliateUrl = $firstProduct['affiliate_url'];
echo "Affiliate URL: $affiliateUrl\n\n";

$parsedUrl = parse_url($affiliateUrl);
if (isset($parsedUrl['query'])) {
    parse_str($parsedUrl['query'], $params);
    
    echo "Affiliate URL Parameters:\n";
    foreach ($params as $key => $value) {
        if ($key === 'url') {
            echo "  $key: $value\n";
            $actualProductUrl = $value;
        } else {
            echo "  $key: $value\n";
        }
    }
    echo "\n";
    
    if (isset($actualProductUrl)) {
        echo "Extracted Product URL: $actualProductUrl\n\n";
        
        // Step 4: Test the actual product URL
        echo "STEP 4: Test Product URL on Bol.com\n";
        echo str_repeat('-', 78) . "\n";
        
        $ch = curl_init($actualProductUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        echo "Testing URL: $actualProductUrl\n";
        echo "HTTP Status: $httpCode\n";
        echo "Final URL: $finalUrl\n\n";
        
        if ($httpCode == 200) {
            echo "✓ PRODUCT URL IS VALID!\n";
            echo "✓ Affiliate links will work correctly\n\n";
        } else {
            echo "✗ PRODUCT URL FAILED!\n";
            echo "⚠ This means users will see 404 error on Bol.com\n\n";
            
            // Try to diagnose the issue
            echo "DIAGNOSIS:\n";
            echo str_repeat('-', 78) . "\n";
            
            // Check if it's a URL format issue
            $productId = $firstProduct['external_id'];
            
            $testUrls = [
                "https://www.bol.com/nl/nl/p/$productId/",
                "https://www.bol.com/nl/p/$productId/",
                "https://www.bol.com/nl/nl/p/$productId",
                "https://www.bol.com/nl/p/$productId",
            ];
            
            echo "Testing alternative URL formats:\n\n";
            
            foreach ($testUrls as $index => $testUrl) {
                echo ($index + 1) . ". $testUrl\n";
                
                $ch = curl_init($testUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                echo "   HTTP $httpCode " . ($httpCode == 200 ? '✓ WORKS!' : '✗ Failed') . "\n";
                
                sleep(1);
            }
        }
    }
}

// Cleanup
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         TEST COMPLETE                                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
