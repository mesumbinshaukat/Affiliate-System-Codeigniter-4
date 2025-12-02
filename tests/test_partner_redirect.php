<?php

/**
 * Test Bol.com Partner Platform Redirect
 * Test if the affiliate redirect through partner.bol.com works
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════╗\n";
echo "║          BOL.COM PARTNER PLATFORM REDIRECT TEST                         ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$baseUrl = 'http://localhost:8080';
$cookieFile = sys_get_temp_dir() . '/partner_redirect_test.txt';

// Login and get a product
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

// Get products
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/dashboard/products/search?q=iPhone');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$firstProduct = $data['products'][0];

echo "Testing Product: " . $firstProduct['title'] . "\n";
echo "Product ID: " . $firstProduct['external_id'] . "\n\n";

$affiliateUrl = $firstProduct['affiliate_url'];
echo "Full Affiliate URL:\n$affiliateUrl\n\n";

// Test the partner.bol.com redirect
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Direct Partner Platform URL (with redirects)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$ch = curl_init($affiliateUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
$redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
curl_close($ch);

echo "Initial URL: $affiliateUrl\n";
echo "HTTP Status: $httpCode\n";
echo "Redirect Count: $redirectCount\n";
echo "Final URL: $finalUrl\n\n";

if ($httpCode == 200) {
    echo "✓ REDIRECT SUCCESSFUL!\n";
    echo "✓ Partner platform redirect is working\n\n";
} else {
    echo "✗ REDIRECT FAILED!\n";
    echo "⚠ HTTP Status: $httpCode\n\n";
}

// Test without following redirects to see the intermediate step
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Partner Platform URL (without auto-redirect)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$ch = curl_init($affiliateUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Redirect Location: " . ($redirectUrl ?: 'None') . "\n\n";

if ($httpCode >= 300 && $httpCode < 400) {
    echo "✓ Partner platform returns redirect (HTTP $httpCode)\n";
    echo "  Redirecting to: $redirectUrl\n\n";
} else {
    echo "⚠ Expected redirect (3xx), got HTTP $httpCode\n\n";
}

// Extract and test the final product URL
$parsedUrl = parse_url($affiliateUrl);
parse_str($parsedUrl['query'], $params);
$productUrl = $params['url'];

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3: Direct Product URL (bypassing partner platform)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Product URL: $productUrl\n";

$ch = curl_init($productUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode == 200) {
    echo "✓ Direct product URL works\n\n";
} else {
    echo "✗ Direct product URL failed\n\n";
}

// Cleanup
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "EDGE CASES IDENTIFIED:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "1. Browser vs Server Behavior\n";
echo "   - Browsers may handle redirects differently than curl\n";
echo "   - JavaScript redirects won't work with curl\n";
echo "   - Cookies/session may be required\n\n";

echo "2. Partner Platform Requirements\n";
echo "   - May require specific headers or cookies\n";
echo "   - May check User-Agent\n";
echo "   - May have rate limiting\n\n";

echo "3. Product URL Format\n";
echo "   - URL includes product slug: /iphone-16-zwart/\n";
echo "   - Format: /nl/nl/p/{slug}/{productId}/\n";
echo "   - Must match exactly\n\n";

echo "4. Possible Issues\n";
echo "   - Affiliate ID not approved/active\n";
echo "   - Product no longer available\n";
echo "   - Geographic restrictions\n";
echo "   - Temporary Bol.com issues\n\n";

echo "\n";
