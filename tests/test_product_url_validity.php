<?php

/**
 * Test Product URL Validity
 * Verify that the URLs from API actually work on Bol.com
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
echo "║          PRODUCT URL VALIDITY TEST                                      ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$bolApi = new App\Libraries\BolComAPI();

// Get some real products
echo "Fetching real products from Bol.com API...\n\n";
$response = $bolApi->searchProducts('iPhone', 5);

if (!$response['success'] || empty($response['products'])) {
    echo "ERROR: Could not fetch products\n";
    exit(1);
}

$products = $response['products'];

echo "Testing " . count($products) . " product URLs...\n";
echo str_repeat('=', 78) . "\n\n";

$validCount = 0;
$invalidCount = 0;

foreach ($products as $index => $product) {
    echo "Product " . ($index + 1) . ": " . substr($product['title'], 0, 50) . "...\n";
    echo str_repeat('-', 78) . "\n";
    
    // Parse affiliate URL to get actual product URL
    $affiliateUrl = $product['affiliate_url'];
    $parsedUrl = parse_url($affiliateUrl);
    
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $params);
        $productUrl = $params['url'] ?? '';
        
        if (!empty($productUrl)) {
            echo "Product URL: $productUrl\n";
            
            // Test the URL
            $ch = curl_init($productUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            
            echo "HTTP Status: $httpCode\n";
            
            if ($httpCode == 200) {
                echo "✓ URL IS VALID!\n";
                if ($finalUrl !== $productUrl) {
                    echo "  (Redirected to: $finalUrl)\n";
                }
                $validCount++;
            } else {
                echo "✗ URL FAILED!\n";
                echo "  Final URL: $finalUrl\n";
                $invalidCount++;
            }
        } else {
            echo "✗ No product URL found in affiliate link\n";
            $invalidCount++;
        }
    } else {
        echo "✗ Could not parse affiliate URL\n";
        $invalidCount++;
    }
    
    echo "\n";
    sleep(1); // Rate limiting
}

echo str_repeat('=', 78) . "\n";
echo "SUMMARY\n";
echo str_repeat('=', 78) . "\n";
echo "Total Products Tested: " . count($products) . "\n";
echo "Valid URLs: $validCount\n";
echo "Invalid URLs: $invalidCount\n";
echo "Success Rate: " . round(($validCount / count($products)) * 100, 2) . "%\n";
echo "\n";

if ($validCount === count($products)) {
    echo "🎉 ALL PRODUCT URLs ARE VALID!\n";
    echo "✓ Affiliate links will work correctly\n";
} else {
    echo "⚠ Some URLs failed - investigating...\n";
}

echo "\n";
