<?php

/**
 * Test Raw API Response to Find Correct URL Structure
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

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          RAW API RESPONSE INSPECTION                                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$clientId = $_ENV['BOL_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['BOL_CLIENT_SECRET'] ?? '';

// Get token
echo "Getting access token...\n";
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

echo "Token received: " . substr($token, 0, 20) . "...\n\n";

// Search for products
echo "Searching for iPhone products...\n";
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
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n\n";

$data = json_decode($response, true);

if (isset($data['results']) && !empty($data['results'])) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "FIRST PRODUCT - COMPLETE RAW DATA\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $firstResult = $data['results'][0];
    
    echo "COMPLETE RESULT OBJECT:\n";
    echo json_encode($firstResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "KEY FIELDS EXTRACTION\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $product = $firstResult['product'] ?? [];
    
    echo "Product ID (bolProductId): " . ($product['bolProductId'] ?? 'NOT FOUND') . "\n";
    echo "EAN: " . ($product['ean'] ?? 'NOT FOUND') . "\n";
    echo "Title: " . ($product['title'] ?? 'NOT FOUND') . "\n\n";
    
    echo "URLs Field:\n";
    if (isset($product['urls']) && is_array($product['urls'])) {
        foreach ($product['urls'] as $urlData) {
            echo "  - " . ($urlData['key'] ?? 'NO KEY') . ": " . ($urlData['value'] ?? 'NO VALUE') . "\n";
        }
    } else {
        echo "  NO 'urls' FIELD FOUND\n";
    }
    
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TESTING PRODUCT URLs\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    if (isset($product['urls']) && is_array($product['urls'])) {
        foreach ($product['urls'] as $urlData) {
            $testUrl = $urlData['value'] ?? '';
            if (!empty($testUrl)) {
                echo "Testing: " . $urlData['key'] . "\n";
                echo "URL: $testUrl\n";
                
                $ch = curl_init($testUrl);
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
                    echo "Final URL: $finalUrl\n";
                } else {
                    echo "✗ URL FAILED\n";
                }
                echo "\n";
                
                sleep(1);
            }
        }
    }
    
} else {
    echo "ERROR: No results found or API error\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
}

echo "\n";
