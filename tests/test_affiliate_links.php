<?php

/**
 * Affiliate Link Testing - Find Edge Cases
 * Tests actual product URLs and affiliate link generation
 * 
 * Run: php tests/test_affiliate_links.php
 */

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

require_once __DIR__ . '/../app/Libraries/BolComAPI.php';

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          AFFILIATE LINK TESTING - EDGE CASE DETECTION                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$bolApi = new App\Libraries\BolComAPI();

// Test 1: Get real products and inspect URLs
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Fetch Real Products and Inspect URL Structure\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$searchQueries = ['iPhone', 'laptop', 'boek'];

foreach ($searchQueries as $query) {
    echo "\nSearching for: '$query'\n";
    echo str_repeat('-', 78) . "\n";
    
    $response = $bolApi->searchProducts($query, 3);
    
    if ($response['success'] && !empty($response['products'])) {
        foreach ($response['products'] as $index => $product) {
            echo "\nProduct " . ($index + 1) . ":\n";
            echo "  Title: " . substr($product['title'], 0, 60) . "...\n";
            echo "  Product ID: " . $product['external_id'] . "\n";
            echo "  EAN: " . ($product['ean'] ?? 'N/A') . "\n";
            echo "  Affiliate URL: " . $product['affiliate_url'] . "\n";
            
            // Parse the affiliate URL to extract the actual product URL
            $parsedUrl = parse_url($product['affiliate_url']);
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $params);
                if (isset($params['url'])) {
                    echo "  → Decoded Product URL: " . $params['url'] . "\n";
                    
                    // Test if the product URL is valid
                    $ch = curl_init($params['url']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode == 200) {
                        echo "  ✓ Product URL is VALID (HTTP $httpCode)\n";
                    } else {
                        echo "  ✗ Product URL FAILED (HTTP $httpCode)\n";
                    }
                }
            }
        }
    } else {
        echo "  No products found or error occurred\n";
    }
    
    sleep(2); // Rate limiting
}

// Test 2: Inspect raw API response structure
echo "\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Inspect Raw API Response for URL Structure\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Get access token
$clientId = $_ENV['BOL_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['BOL_CLIENT_SECRET'] ?? '';

if (empty($clientId) || empty($clientSecret)) {
    echo "ERROR: Credentials not configured\n";
    exit(1);
}

$ch = curl_init('https://login.bol.com/token?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
    'Accept: application/json'
]);
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);
$token = $tokenData['access_token'] ?? '';

if (empty($token)) {
    echo "ERROR: Could not get access token\n";
    exit(1);
}

// Make API request and inspect raw response
$params = [
    'search-term' => 'iPhone',
    'country-code' => 'NL',
    'page-size' => 2,
    'page' => 1,
    'include-offer' => 'true',
    'include-image' => 'true',
    'include-rating' => 'true',
    'sort' => 'RELEVANCE'
];

$url = 'https://api.bol.com/marketing/catalog/v1/products/search?' . http_build_query($params);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Accept-Language: nl',
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['results']) && !empty($data['results'])) {
    echo "\nFirst Product Raw Data:\n";
    echo str_repeat('-', 78) . "\n";
    
    $firstProduct = $data['results'][0];
    
    echo "Product Structure:\n";
    echo json_encode($firstProduct['product'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    
    echo "\n\nURL Analysis:\n";
    if (isset($firstProduct['product']['urls'])) {
        echo "URLs found in product:\n";
        foreach ($firstProduct['product']['urls'] as $urlData) {
            echo "  - Key: " . ($urlData['key'] ?? 'N/A') . "\n";
            echo "    Value: " . ($urlData['value'] ?? 'N/A') . "\n";
        }
    } else {
        echo "No 'urls' field in product data\n";
    }
    
    echo "\nProduct ID: " . ($firstProduct['product']['bolProductId'] ?? 'N/A') . "\n";
    echo "EAN: " . ($firstProduct['product']['ean'] ?? 'N/A') . "\n";
}

// Test 3: Edge Cases
echo "\n\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Edge Case Detection\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

echo "\nPotential Edge Cases:\n";
echo "1. Product URL format mismatch\n";
echo "   - API may return different URL formats\n";
echo "   - Constructed URL: https://www.bol.com/nl/nl/p/{productId}/\n";
echo "   - Actual URL may be: https://www.bol.com/nl/p/{productId}/ (without /nl/)\n";
echo "\n";

echo "2. Product ID format issues\n";
echo "   - Product IDs may have different formats\n";
echo "   - Need to verify exact format from API\n";
echo "\n";

echo "3. URL encoding in affiliate link\n";
echo "   - Special characters in product URL may break\n";
echo "   - Need proper URL encoding\n";
echo "\n";

echo "4. Missing product URLs from API\n";
echo "   - Some products may not have URL in response\n";
echo "   - Fallback construction may be incorrect\n";
echo "\n";

// Test 4: Test different URL formats
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4: Test Different URL Formats\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$testProductId = '9300000189439876'; // Real product ID from earlier test

$urlFormats = [
    'https://www.bol.com/nl/nl/p/' . $testProductId . '/',
    'https://www.bol.com/nl/p/' . $testProductId . '/',
    'https://www.bol.com/nl/nl/p/' . $testProductId,
    'https://www.bol.com/nl/p/' . $testProductId,
];

echo "\nTesting different URL formats for product ID: $testProductId\n\n";

foreach ($urlFormats as $index => $testUrl) {
    echo "Format " . ($index + 1) . ": $testUrl\n";
    
    $ch = curl_init($testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "  ✓ VALID (HTTP $httpCode)\n";
        echo "  Final URL: $finalUrl\n";
    } else {
        echo "  ✗ FAILED (HTTP $httpCode)\n";
    }
    echo "\n";
    
    sleep(1);
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         EDGE CASE SUMMARY                                ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Review the test results above to identify:\n";
echo "1. Correct URL format from Bol.com API\n";
echo "2. Which URL construction format works\n";
echo "3. Any encoding or format issues\n";
echo "4. HTTP status codes for different formats\n";
echo "\n";
