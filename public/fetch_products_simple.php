<?php
/**
 * Simple Product Fetcher
 * Fetches 400+ products from Bol.com API with images, URLs, titles, and descriptions
 * Age categorization will be done later
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') === false || strpos($line, '#') === 0) {
            continue;
        }
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
            (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
            $value = substr($value, 1, -1);
        }
        
        $_ENV[$key] = $value;
    }
}

loadEnv(__DIR__ . '/../.env');

$clientId = $_ENV['BOL_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['BOL_CLIENT_SECRET'] ?? '';

if (!$clientId || !$clientSecret) {
    die('Error: BOL_CLIENT_ID or BOL_CLIENT_SECRET not set in .env file');
}

echo "<h2>Fetching Products from Bol.com API</h2>";
echo "<p>Fetching 400+ products with images, URLs, titles, and descriptions...</p>";
echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 600px; overflow-y: auto;'>";

// Get access token using Basic Auth
function getAccessToken($clientId, $clientSecret) {
    $credentials = base64_encode($clientId . ':' . $clientSecret);
    
    $ch = curl_init('https://login.bol.com/token?grant_type=client_credentials');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $credentials,
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['access_token'])) {
            return $data['access_token'];
        }
    }
    
    return null;
}

// Fetch products from API
function fetchProductsFromAPI($query, $accessToken, $limit = 50) {
    $params = [
        'search-term' => $query,
        'country-code' => 'NL',
        'page-size' => min($limit, 50),
        'page' => 1,
        'include-offer' => 'true',
        'include-image' => 'true',
        'include-rating' => 'true',
        'sort' => 'RELEVANCE'
    ];
    
    $url = 'https://api.bol.com/marketing/catalog/v1/products/search?' . http_build_query($params);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Accept: application/json',
        'Accept-Language: nl',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['results'] ?? [];
    }
    
    return [];
}

// Search queries to fetch diverse products
$searchQueries = [
    'laptop', 'smartphone', 'headphones', 'book', 'fitness', 'home decor',
    'coffee maker', 'gaming', 'camera', 'watch', 'shoes', 'backpack',
    'desk lamp', 'plant pot', 'kitchen gadgets', 'garden tools', 'yoga mat',
    'bluetooth speaker', 'power bank', 'desk organizer', 'water bottle',
    'tablet', 'monitor', 'keyboard', 'mouse', 'printer', 'router',
    'microphone', 'tripod', 'phone case', 'charger', 'cable', 'adapter',
    'bag', 'wallet', 'sunglasses', 'hat', 'scarf', 'gloves',
    'pillow', 'blanket', 'towel', 'bedsheet', 'mattress', 'chair',
    'table', 'shelf', 'cabinet', 'drawer', 'mirror', 'picture frame',
];

$allProducts = [];
$productIds = [];
$productCount = 0;
$maxProducts = 400;

// Get access token
$accessToken = getAccessToken($clientId, $clientSecret);

if (!$accessToken) {
    die('Error: Could not obtain access token from Bol.com API');
}

echo "✓ Access token obtained<br>";
flush();

// Fetch products from each search query
foreach ($searchQueries as $query) {
    if ($productCount >= $maxProducts) {
        break;
    }
    
    echo "Fetching: <strong>$query</strong>... ";
    flush();
    
    $products = fetchProductsFromAPI($query, $accessToken, 50);
    
    if (!empty($products)) {
        $addedCount = 0;
        
        foreach ($products as $product) {
            if ($productCount >= $maxProducts) {
                break;
            }
            
            // Get product ID
            $productId = $product['bolProductId'] ?? $product['id'] ?? '';
            
            // Skip if no product ID
            if (empty($productId)) {
                continue;
            }
            
            // Skip if already added
            if (in_array($productId, $productIds)) {
                continue;
            }
            
            // Extract data
            $title = $product['title'] ?? '';
            $description = strip_tags($product['description'] ?? $product['shortDescription'] ?? '');
            $price = $product['offer']['price'] ?? 0;
            $imageUrl = $product['image']['url'] ?? '';
            $productUrl = $product['url'] ?? "https://www.bol.com/nl/nl/p/{$productId}/";
            $rating = $product['rating'] ?? null;
            $ean = $product['ean'] ?? '';
            
            // Skip if missing critical data
            if (empty($title) || empty($productUrl)) {
                continue;
            }
            
            $productData = [
                'id' => $productId,
                'ean' => $ean,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'image' => $imageUrl,
                'url' => $productUrl,
                'rating' => $rating,
                'source' => 'bol.com',
            ];
            
            $allProducts[] = $productData;
            $productIds[] = $productId;
            $productCount++;
            $addedCount++;
        }
        
        echo "✓ Added $addedCount products (Total: $productCount)<br>";
    } else {
        echo "✗ No products found<br>";
    }
    
    flush();
    sleep(1); // Rate limiting
}

echo "</div>";

// Save to JSON file
$jsonFile = __DIR__ . '/age_based_products.json';
$jsonData = [
    'generated_at' => date('Y-m-d H:i:s'),
    'total_products' => $productCount,
    'products' => $allProducts,
];

if (file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
    echo "<h3 style='color: green;'>✓ Success!</h3>";
    echo "<p>Saved <strong>$productCount</strong> products to <code>age_based_products.json</code></p>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Review the JSON file to verify products have images, URLs, titles, and descriptions</li>";
    echo "<li>Categorize products by age groups (18-25, 25-35, 35-50, 50-65, 65+)</li>";
    echo "<li>Update the AgeBasedProducts library to use the categorized data</li>";
    echo "<li>Test on the dashboard with different user ages</li>";
    echo "</ol>";
} else {
    echo "<h3 style='color: red;'>✗ Error saving JSON file</h3>";
    echo "<p>Could not write to: <code>$jsonFile</code></p>";
}
?>
