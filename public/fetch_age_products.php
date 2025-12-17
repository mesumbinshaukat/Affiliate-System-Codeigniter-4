<?php
/**
 * Age-Based Product Fetcher
 * Fetches 300-400 products from multiple categories via Bol.com API
 * Categorizes them by age ranges and stores in JSON file
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
        
        // Remove quotes if present
        if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
            (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
            $value = substr($value, 1, -1);
        }
        
        $_ENV[$key] = $value;
    }
}

// Load .env file
loadEnv(__DIR__ . '/../.env');

// Get API credentials
$clientId = $_ENV['BOL_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['BOL_CLIENT_SECRET'] ?? '';

if (!$clientId || !$clientSecret) {
    die('Error: BOL_CLIENT_ID or BOL_CLIENT_SECRET not set in .env file');
}

// Categories to fetch products from (diverse range for all ages)
$categories = [
    'Boeken' => 'books',
    'Elektronica' => 'electronics',
    'Mode' => 'fashion',
    'Sport & Outdoor' => 'sports',
    'Speelgoed' => 'toys',
    'Huishouden' => 'home',
    'Gezondheid & Schoonheid' => 'health',
    'Muziek & Film' => 'media',
    'Gaming' => 'gaming',
    'Kantoor & School' => 'office',
];

// Age-based search terms for better product matching
$ageSearchTerms = [
    '18-25' => ['student', 'gaming', 'tech gadgets', 'sneakers', 'headphones'],
    '25-35' => ['home decor', 'fitness', 'smart home', 'coffee maker', 'desk lamp'],
    '35-50' => ['garden tools', 'kitchen appliances', 'home improvement', 'wellness', 'travel'],
    '50-65' => ['gardening', 'health supplements', 'reading', 'hobby', 'comfort'],
    '65+' => ['health care', 'comfort', 'reading glasses', 'mobility aids', 'wellness'],
];

// Age range definitions
$ageRanges = [
    '18-25' => ['min' => 18, 'max' => 25],
    '25-35' => ['min' => 25, 'max' => 35],
    '35-50' => ['min' => 35, 'max' => 50],
    '50-65' => ['min' => 50, 'max' => 65],
    '65+' => ['min' => 65, 'max' => 120],
];

$allProducts = [];
$productsByAgeRange = [];
$productIds = [];

// Initialize age ranges
foreach ($ageRanges as $range => $values) {
    $productsByAgeRange[$range] = [];
}

echo "<h2>Fetching Products from Bol.com API</h2>";
echo "<p>This may take a few minutes...</p>";
echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 500px; overflow-y: auto;'>";

// Get access token using Basic Auth (same as BolComAPI)
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
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['access_token'])) {
            return $data['access_token'];
        }
    }
    
    echo "Token Error (HTTP $httpCode): " . substr($response, 0, 200) . "<br>";
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

// Get access token
$accessToken = getAccessToken($clientId, $clientSecret);

if (!$accessToken) {
    die('Error: Could not obtain access token from Bol.com API');
}

echo "✓ Access token obtained<br>";
flush();

// Fetch products from various searches
$searchQueries = [
    'laptop', 'smartphone', 'headphones', 'book', 'fitness', 'home decor',
    'coffee maker', 'gaming', 'camera', 'watch', 'shoes', 'backpack',
    'desk lamp', 'plant pot', 'kitchen gadgets', 'garden tools', 'yoga mat',
    'bluetooth speaker', 'power bank', 'desk organizer', 'water bottle',
];

$productCount = 0;
$maxProducts = 400;

foreach ($searchQueries as $query) {
    if ($productCount >= $maxProducts) {
        break;
    }
    
    echo "Fetching products for: <strong>$query</strong>... ";
    flush();
    
    $products = fetchProductsFromAPI($query, $accessToken, 50);
    
    if (!empty($products)) {
        $addedCount = 0;
        $skippedAge = 0;
        $skippedImage = 0;
        
        foreach ($products as $product) {
            if ($productCount >= $maxProducts) {
                break;
            }
            
            // Skip products with age-related keywords in title
            $title = $product['title'] ?? '';
            if (preg_match('/\d+\s*(jaar|jarig|verjaardag|jubileum|anniversary)/i', $title)) {
                $skippedAge++;
                continue;
            }
            
            // Extract image URL (from image_url field in formatted response)
            $imageUrl = $product['image_url'] ?? '';
            
            // Get product ID
            $productId = $product['external_id'] ?? $product['id'] ?? '';
            
            // Skip if no product ID
            if (empty($productId)) {
                continue;
            }
            
            // Skip if already added
            if (in_array($productId, $productIds)) {
                continue;
            }
            
            // Use a placeholder image if none provided
            if (empty($imageUrl)) {
                $imageUrl = 'https://via.placeholder.com/300x300?text=' . urlencode(substr($title, 0, 20));
            }
            
            $productData = [
                'id' => $productId,
                'ean' => $product['ean'] ?? '',
                'title' => $title,
                'description' => $product['description'] ?? '',
                'price' => $product['price'] ?? 0,
                'image' => $imageUrl,
                'rating' => $product['rating'] ?? null,
                'url' => $product['affiliate_url'] ?? '',
            ];
            
            // Categorize by age range based on product characteristics
            $ageCategory = categorizeProductByAge($productData);
            
            $allProducts[] = $productData;
            $productIds[] = $productId;
            $productsByAgeRange[$ageCategory][] = $productData;
            $productCount++;
            $addedCount++;
        }
        
        echo "✓ Added " . $addedCount . " products (Skipped: $skippedAge age-related, $skippedImage no-image) (Total: $productCount)<br>";
    } else {
        echo "✗ No products found<br>";
    }
    
    flush();
    sleep(1); // Rate limiting
}

echo "</div>";

// Function to categorize product by age
function categorizeProductByAge($product) {
    $title = strtolower($product['title']);
    $description = strtolower($product['description']);
    $price = $product['price'] ?? 0;
    
    // Keywords for different age groups
    $keywords = [
        '18-25' => ['gaming', 'headphone', 'sneaker', 'tech', 'laptop', 'phone', 'speaker', 'camera', 'backpack', 'watch'],
        '25-35' => ['home', 'coffee', 'desk', 'lamp', 'kitchen', 'decor', 'fitness', 'yoga', 'smart', 'organizer'],
        '35-50' => ['garden', 'tool', 'kitchen', 'home improvement', 'wellness', 'health', 'comfort', 'furniture'],
        '50-65' => ['garden', 'health', 'comfort', 'reading', 'hobby', 'wellness', 'supplement'],
        '65+' => ['health', 'comfort', 'mobility', 'reading', 'wellness', 'care'],
    ];
    
    $scores = [
        '18-25' => 0,
        '25-35' => 0,
        '35-50' => 0,
        '50-65' => 0,
        '65+' => 0,
    ];
    
    // Score based on keywords
    foreach ($keywords as $range => $words) {
        foreach ($words as $word) {
            if (strpos($title, $word) !== false || strpos($description, $word) !== false) {
                $scores[$range]++;
            }
        }
    }
    
    // Price-based categorization
    if ($price < 50) {
        $scores['18-25'] += 2;
        $scores['25-35'] += 1;
    } elseif ($price < 150) {
        $scores['25-35'] += 2;
        $scores['35-50'] += 1;
    } elseif ($price < 500) {
        $scores['35-50'] += 2;
        $scores['50-65'] += 1;
    } else {
        $scores['50-65'] += 2;
        $scores['65+'] += 1;
    }
    
    // Find category with highest score
    $maxScore = max($scores);
    if ($maxScore === 0) {
        // Default distribution if no keywords match
        $ranges = ['18-25', '25-35', '35-50', '50-65', '65+'];
        return $ranges[array_rand($ranges)];
    }
    
    foreach ($scores as $range => $score) {
        if ($score === $maxScore) {
            return $range;
        }
    }
    
    return '25-35'; // Default fallback
}

// Save to JSON file
$jsonFile = __DIR__ . '/age_based_products.json';
$jsonData = [
    'generated_at' => date('Y-m-d H:i:s'),
    'total_products' => $productCount,
    'products_by_age_range' => $productsByAgeRange,
];

if (file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
    echo "<h3 style='color: green;'>✓ Success!</h3>";
    echo "<p>Saved <strong>$productCount</strong> products to <code>$jsonFile</code></p>";
    echo "<p>Age range distribution:</p>";
    echo "<ul>";
    foreach ($productsByAgeRange as $range => $products) {
        echo "<li><strong>$range</strong>: " . count($products) . " products</li>";
    }
    echo "</ul>";
    echo "<p><a href='javascript:location.reload()'>Refresh</a> to run again or check the JSON file.</p>";
} else {
    echo "<h3 style='color: red;'>✗ Error saving JSON file</h3>";
    echo "<p>Could not write to: <code>$jsonFile</code></p>";
}
?>
