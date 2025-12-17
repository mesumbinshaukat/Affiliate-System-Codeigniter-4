<?php
/**
 * Product Age Categorizer
 * Categorizes 400+ fetched products into age groups based on product characteristics
 */

echo "<h2>Categorizing Products by Age Groups</h2>";
echo "<p>Reading products from age_based_products.json and categorizing by age...</p>";
echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace; max-height: 600px; overflow-y: auto;'>";

// Load products from JSON
$jsonFile = __DIR__ . '/age_based_products.json';

if (!file_exists($jsonFile)) {
    die('Error: age_based_products.json not found. Run fetch_products_simple.php first.');
}

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

if (!$data || empty($data['products'])) {
    die('Error: No products found in JSON file.');
}

$products = $data['products'];
echo "✓ Loaded " . count($products) . " products from JSON<br>";
flush();

// Age group keywords and characteristics
$ageKeywords = [
    '18-25' => [
        'keywords' => ['gaming', 'headphone', 'sneaker', 'tech', 'laptop', 'phone', 'speaker', 'camera', 'backpack', 'watch', 'keyboard', 'mouse', 'monitor', 'tablet', 'microphone', 'tripod', 'case', 'charger', 'cable', 'adapter'],
        'priceRange' => [0, 200],
        'weight' => 1.0,
    ],
    '25-35' => [
        'keywords' => ['home', 'coffee', 'desk', 'lamp', 'kitchen', 'decor', 'fitness', 'yoga', 'smart', 'organizer', 'pillow', 'blanket', 'towel', 'bedsheet', 'chair', 'table', 'shelf', 'cabinet', 'mirror', 'frame'],
        'priceRange' => [20, 300],
        'weight' => 1.0,
    ],
    '35-50' => [
        'keywords' => ['garden', 'tool', 'home improvement', 'wellness', 'health', 'comfort', 'furniture', 'power', 'drill', 'saw', 'hammer', 'screwdriver', 'blender', 'slow cooker', 'vacuum', 'lawn mower'],
        'priceRange' => [50, 500],
        'weight' => 1.0,
    ],
    '50-65' => [
        'keywords' => ['garden', 'health', 'comfort', 'reading', 'hobby', 'wellness', 'supplement', 'book', 'puzzle', 'craft', 'knit', 'sewing', 'gardening', 'outdoor'],
        'priceRange' => [10, 400],
        'weight' => 1.0,
    ],
    '65+' => [
        'keywords' => ['health', 'comfort', 'mobility', 'reading', 'wellness', 'care', 'book', 'puzzle', 'craft', 'gardening', 'outdoor', 'walking'],
        'priceRange' => [0, 300],
        'weight' => 1.0,
    ],
];

// Categorize products
function categorizeProduct($product, $ageKeywords) {
    $title = strtolower($product['title'] ?? '');
    $description = strtolower($product['description'] ?? '');
    $price = $product['price'] ?? 0;
    
    $scores = [
        '18-25' => 0,
        '25-35' => 0,
        '35-50' => 0,
        '50-65' => 0,
        '65+' => 0,
    ];
    
    // Score based on keywords
    foreach ($ageKeywords as $ageGroup => $config) {
        foreach ($config['keywords'] as $keyword) {
            if (strpos($title, $keyword) !== false) {
                $scores[$ageGroup] += 3;
            } elseif (strpos($description, $keyword) !== false) {
                $scores[$ageGroup] += 1;
            }
        }
        
        // Score based on price range
        $minPrice = $config['priceRange'][0];
        $maxPrice = $config['priceRange'][1];
        
        if ($price >= $minPrice && $price <= $maxPrice) {
            $scores[$ageGroup] += 2;
        }
    }
    
    // Find age group with highest score
    $maxScore = max($scores);
    
    if ($maxScore === 0) {
        // Default distribution if no keywords match
        $ranges = ['18-25', '25-35', '35-50', '50-65', '65+'];
        return $ranges[array_rand($ranges)];
    }
    
    foreach ($scores as $ageGroup => $score) {
        if ($score === $maxScore) {
            return $ageGroup;
        }
    }
    
    return '25-35'; // Default fallback
}

// Categorize all products
$productsByAgeRange = [
    '18-25' => [],
    '25-35' => [],
    '35-50' => [],
    '50-65' => [],
    '65+' => [],
];

foreach ($products as $product) {
    $ageGroup = categorizeProduct($product, $ageKeywords);
    $productsByAgeRange[$ageGroup][] = $product;
}

echo "✓ Categorization complete<br><br>";
echo "<strong>Distribution by Age Group:</strong><br>";

foreach ($productsByAgeRange as $ageGroup => $groupProducts) {
    $count = count($groupProducts);
    $percentage = round(($count / count($products)) * 100, 1);
    echo "• <strong>$ageGroup</strong>: $count products ($percentage%)<br>";
}

echo "<br>";
flush();

// Save categorized products
$categorizedData = [
    'generated_at' => date('Y-m-d H:i:s'),
    'total_products' => count($products),
    'products_by_age_range' => $productsByAgeRange,
];

if (file_put_contents($jsonFile, json_encode($categorizedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
    echo "✓ Saved categorized products to age_based_products.json<br>";
} else {
    echo "✗ Error saving JSON file<br>";
}

echo "</div>";

echo "<h3 style='color: green;'>✓ Complete!</h3>";
echo "<p>Products have been categorized by age groups and saved to <code>age_based_products.json</code></p>";
echo "<p>The AgeBasedProducts library will now serve random products based on user age.</p>";
?>
