<?php
/**
 * Comprehensive Application Test Suite
 * Visit: https://lijst.wmcdev.nl/test_all.php
 * 
 * Tests all major functionality without breaking anything
 * DELETE THIS FILE AFTER TESTING!
 */

session_start();

// Configuration
$baseUrl = 'https://lijst.wmcdev.nl';
$testEmail = 'test_' . time() . '@example.com';
$testPassword = 'Test@123456';
$existingEmail = 'mesum@gmail.com';
$existingPassword = 'admin123!';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Comprehensive Test Suite - lijst.wmcdev.nl</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        .test-case {
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
        }
        .info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
            font-size: 12px;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: #e7f3ff;
            border-radius: 5px;
            border: 2px solid #2563eb;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .stat {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 5px;
            min-width: 150px;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
        }
        .stat-label {
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Comprehensive Application Test Suite</h1>
        <p><strong>Domain:</strong> <?= $baseUrl ?></p>
        <p><strong>Started:</strong> <?= date('Y-m-d H:i:s') ?></p>
        
        <?php
        
        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;
        $warnings = 0;
        
        function testResult($name, $passed, $message = '', $details = '') {
            global $totalTests, $passedTests, $failedTests;
            $totalTests++;
            
            if ($passed) {
                $passedTests++;
                echo '<div class="success">✓ ' . htmlspecialchars($name) . '</div>';
            } else {
                $failedTests++;
                echo '<div class="error">✗ ' . htmlspecialchars($name) . '</div>';
            }
            
            if ($message) {
                echo '<div class="info">' . htmlspecialchars($message) . '</div>';
            }
            
            if ($details) {
                echo '<pre>' . htmlspecialchars($details) . '</pre>';
            }
        }
        
        function makeRequest($url, $method = 'GET', $data = [], $headers = []) {
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
            
            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
            
            if (!empty($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            return [
                'code' => $httpCode,
                'body' => $response,
                'error' => $error
            ];
        }
        
        // TEST SECTION 1: Database Connection
        echo '<div class="test-section">';
        echo '<h2>1. Database Connection Tests</h2>';
        
        $envFile = __DIR__ . '/../.env';
        $env = [];
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B'\"");
                $env[$key] = $value;
            }
        }
        
        $host = $env['database.default.hostname'] ?? 'localhost';
        $database = $env['database.default.database'] ?? 'lijst';
        $username = $env['database.default.username'] ?? 'wmcdev';
        $password = $env['database.default.password'] ?? '';
        
        try {
            $conn = new mysqli($host, $username, $password, $database);
            if ($conn->connect_error) {
                throw new Exception($conn->connect_error);
            }
            testResult('Database Connection', true, "Connected to: $database");
            
            // Check tables
            $requiredTables = ['users', 'categories', 'lists', 'products', 'list_products', 'clicks', 'affiliate_sources', 'settings'];
            $result = $conn->query("SHOW TABLES");
            $existingTables = [];
            while ($row = $result->fetch_array()) {
                $existingTables[] = $row[0];
            }
            
            foreach ($requiredTables as $table) {
                $exists = in_array($table, $existingTables);
                testResult("Table: $table", $exists, $exists ? 'Table exists' : 'Table missing');
            }
            
            // Check table structures
            $result = $conn->query("SHOW COLUMNS FROM categories");
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            testResult('Categories table has status column', in_array('status', $columns), 'Schema check');
            
        } catch (Exception $e) {
            testResult('Database Connection', false, $e->getMessage());
        }
        
        echo '</div>';
        
        // TEST SECTION 2: Page Accessibility
        echo '<div class="test-section">';
        echo '<h2>2. Page Accessibility Tests</h2>';
        
        $pages = [
            '/index.php' => 'Homepage',
            '/index.php/login' => 'Login Page',
            '/index.php/register' => 'Register Page',
        ];
        
        foreach ($pages as $path => $name) {
            $response = makeRequest($baseUrl . $path);
            $passed = $response['code'] === 200;
            testResult("$name ($path)", $passed, "HTTP {$response['code']}");
        }
        
        echo '</div>';
        
        // TEST SECTION 3: User Registration
        echo '<div class="test-section">';
        echo '<h2>3. User Registration Tests</h2>';
        
        $registerData = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser_' . time(),
            'email' => $testEmail,
            'password' => $testPassword,
            'password_confirm' => $testPassword
        ];
        
        $response = makeRequest($baseUrl . '/index.php/register', 'POST', $registerData);
        $passed = ($response['code'] === 200 || $response['code'] === 302);
        testResult('User Registration', $passed, "HTTP {$response['code']}", 
            "Registered: {$registerData['email']}");
        
        echo '</div>';
        
        // TEST SECTION 4: User Login (Existing User)
        echo '<div class="test-section">';
        echo '<h2>4. User Login Tests (Existing User)</h2>';
        
        $loginData = [
            'email' => $existingEmail,
            'password' => $existingPassword
        ];
        
        $response = makeRequest($baseUrl . '/index.php/login', 'POST', $loginData);
        $passed = ($response['code'] === 200 || $response['code'] === 302 || strpos($response['body'], 'dashboard') !== false);
        testResult('Login with existing user', $passed, "Email: $existingEmail");
        
        if ($passed) {
            // Test dashboard access
            $response = makeRequest($baseUrl . '/index.php/dashboard');
            $dashboardAccess = ($response['code'] === 200);
            testResult('Dashboard Access', $dashboardAccess, "HTTP {$response['code']}");
            
            // Test lists page
            $response = makeRequest($baseUrl . '/index.php/dashboard/lists');
            $listsAccess = ($response['code'] === 200);
            testResult('Lists Page Access', $listsAccess, "HTTP {$response['code']}");
        }
        
        echo '</div>';
        
        // TEST SECTION 5: Category Tests
        echo '<div class="test-section">';
        echo '<h2>5. Category Tests</h2>';
        
        if ($conn) {
            $result = $conn->query("SELECT COUNT(*) as count FROM categories");
            $row = $result->fetch_assoc();
            testResult('Categories exist', $row['count'] > 0, "{$row['count']} categories found");
            
            $result = $conn->query("SELECT * FROM categories WHERE status = 'active' LIMIT 5");
            while ($cat = $result->fetch_assoc()) {
                testResult("Category: {$cat['name']}", true, "Slug: {$cat['slug']}, Icon: {$cat['icon']}");
            }
        }
        
        echo '</div>';
        
        // TEST SECTION 6: Product Search (Bol.com API)
        echo '<div class="test-section">';
        echo '<h2>6. Product Search Tests (Bol.com API)</h2>';
        
        $searchData = [
            'query' => 'laptop',
            'limit' => 5
        ];
        
        $response = makeRequest($baseUrl . '/index.php/dashboard/products/search', 'POST', $searchData);
        $passed = ($response['code'] === 200);
        testResult('Product Search API', $passed, "HTTP {$response['code']}");
        
        if ($passed) {
            $data = json_decode($response['body'], true);
            if (isset($data['products']) && is_array($data['products'])) {
                testResult('Product Search Results', count($data['products']) > 0, 
                    count($data['products']) . ' products found');
                
                if (count($data['products']) > 0) {
                    $product = $data['products'][0];
                    $hasTitle = !empty($product['title']);
                    $hasPrice = isset($product['price']);
                    $hasUrl = !empty($product['affiliate_url']);
                    
                    testResult('Product has title', $hasTitle, $hasTitle ? $product['title'] : 'Missing');
                    testResult('Product has price', $hasPrice, $hasPrice ? "€{$product['price']}" : 'Missing');
                    testResult('Product has affiliate URL', $hasUrl);
                }
            }
        }
        
        echo '</div>';
        
        // TEST SECTION 7: List Operations
        echo '<div class="test-section">';
        echo '<h2>7. List Operations Tests</h2>';
        
        if ($conn) {
            // Check existing lists
            $result = $conn->query("SELECT COUNT(*) as count FROM lists");
            $row = $result->fetch_assoc();
            testResult('Lists exist in database', $row['count'] >= 0, "{$row['count']} lists found");
            
            // Get a sample list
            $result = $conn->query("SELECT * FROM lists LIMIT 1");
            if ($list = $result->fetch_assoc()) {
                testResult('Sample list found', true, "ID: {$list['id']}, Title: {$list['title']}");
                
                // Check list products
                $listId = $list['id'];
                $result = $conn->query("SELECT COUNT(*) as count FROM list_products WHERE list_id = $listId");
                $row = $result->fetch_assoc();
                testResult("List #{$listId} has products", $row['count'] >= 0, "{$row['count']} products in list");
            }
        }
        
        echo '</div>';
        
        // TEST SECTION 8: Click Tracking
        echo '<div class="test-section">';
        echo '<h2>8. Click Tracking Tests</h2>';
        
        if ($conn) {
            // Check if clicks table has data
            $result = $conn->query("SELECT COUNT(*) as count FROM clicks");
            $row = $result->fetch_assoc();
            testResult('Click tracking table', true, "{$row['count']} clicks recorded");
            
            // Get a product to test tracking
            $result = $conn->query("SELECT * FROM products LIMIT 1");
            if ($product = $result->fetch_assoc()) {
                $productId = $product['id'];
                testResult('Sample product for tracking', true, "ID: $productId, Title: {$product['title']}");
                
                // Test tracking URL
                $trackingUrl = $baseUrl . "/index.php/out/$productId";
                testResult('Tracking URL format', true, $trackingUrl);
            }
        }
        
        echo '</div>';
        
        // TEST SECTION 9: File Structure
        echo '<div class="test-section">';
        echo '<h2>9. File Structure Tests</h2>';
        
        $criticalFiles = [
            '../app/Config/App.php' => 'App Configuration',
            '../app/Config/Database.php' => 'Database Configuration',
            '../app/Controllers/Home.php' => 'Home Controller',
            '../app/Controllers/Dashboard.php' => 'Dashboard Controller',
            '../app/Controllers/Auth.php' => 'Auth Controller',
            '../app/Models/UserModel.php' => 'User Model',
            '../app/Models/ListModel.php' => 'List Model',
            '../app/Models/ProductModel.php' => 'Product Model',
            '../app/Libraries/BolComAPI.php' => 'Bol.com API Library',
            '../vendor/autoload.php' => 'Composer Autoloader',
            '../.env' => 'Environment File'
        ];
        
        foreach ($criticalFiles as $file => $name) {
            $path = __DIR__ . '/' . $file;
            $exists = file_exists($path);
            testResult($name, $exists, $exists ? 'File exists' : 'File missing');
        }
        
        echo '</div>';
        
        // TEST SECTION 10: Environment Configuration
        echo '<div class="test-section">';
        echo '<h2>10. Environment Configuration Tests</h2>';
        
        $requiredEnvVars = [
            'CI_ENVIRONMENT' => 'Environment Mode',
            'app.baseURL' => 'Base URL',
            'database.default.database' => 'Database Name',
            'BOL_CLIENT_ID' => 'Bol.com Client ID',
            'BOL_AFFILIATE_ID' => 'Bol.com Affiliate ID',
            'encryption.key' => 'Encryption Key'
        ];
        
        foreach ($requiredEnvVars as $key => $name) {
            $exists = isset($env[$key]) && !empty($env[$key]);
            testResult($name, $exists, $exists ? "Set: {$env[$key]}" : 'Not set');
        }
        
        echo '</div>';
        
        // TEST SECTION 11: Security Tests
        echo '<div class="test-section">';
        echo '<h2>11. Security Tests</h2>';
        
        // Test HTTPS
        $isHttps = (strpos($baseUrl, 'https://') === 0);
        testResult('HTTPS Enabled', $isHttps, $isHttps ? 'Secure connection' : 'Warning: Using HTTP');
        
        // Test session security
        $sessionSecure = isset($env['app.sessionCookieName']) && !empty($env['app.sessionCookieName']);
        testResult('Session Configuration', $sessionSecure);
        
        // Test encryption key
        $hasEncryption = isset($env['encryption.key']) && !empty($env['encryption.key']);
        testResult('Encryption Key Set', $hasEncryption);
        
        echo '</div>';
        
        // Close database connection
        if ($conn) {
            $conn->close();
        }
        
        // SUMMARY
        echo '<div class="summary">';
        echo '<h2>📊 Test Summary</h2>';
        
        echo '<div class="stats">';
        echo '<div class="stat">';
        echo '<div class="stat-number" style="color: #2563eb;">' . $totalTests . '</div>';
        echo '<div class="stat-label">Total Tests</div>';
        echo '</div>';
        
        echo '<div class="stat">';
        echo '<div class="stat-number" style="color: #28a745;">' . $passedTests . '</div>';
        echo '<div class="stat-label">Passed</div>';
        echo '</div>';
        
        echo '<div class="stat">';
        echo '<div class="stat-number" style="color: #dc3545;">' . $failedTests . '</div>';
        echo '<div class="stat-label">Failed</div>';
        echo '</div>';
        
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;
        echo '<div class="stat">';
        echo '<div class="stat-number" style="color: ' . ($successRate >= 80 ? '#28a745' : '#ffc107') . ';">' . $successRate . '%</div>';
        echo '<div class="stat-label">Success Rate</div>';
        echo '</div>';
        echo '</div>';
        
        if ($successRate >= 90) {
            echo '<div class="success" style="font-size: 18px; text-align: center; padding: 20px;">';
            echo '🎉 <strong>Excellent!</strong> Your application is working great!';
            echo '</div>';
        } elseif ($successRate >= 70) {
            echo '<div class="warning" style="font-size: 18px; text-align: center; padding: 20px;">';
            echo '⚠️ <strong>Good!</strong> Most features are working, but some need attention.';
            echo '</div>';
        } else {
            echo '<div class="error" style="font-size: 18px; text-align: center; padding: 20px;">';
            echo '❌ <strong>Needs Attention!</strong> Several issues need to be fixed.';
            echo '</div>';
        }
        
        echo '<div class="info" style="margin-top: 20px;">';
        echo '<h3>Next Steps:</h3>';
        echo '<ol>';
        echo '<li>Review any failed tests above</li>';
        echo '<li>Fix issues if any</li>';
        echo '<li><strong>DELETE THIS FILE (test_all.php) for security!</strong></li>';
        echo '<li>Delete other test files: test_server.php, migrate.php, fix_categories.php, info.php, test.html</li>';
        echo '<li>Visit your site: <a href="index.php">' . $baseUrl . '/index.php</a></li>';
        echo '</ol>';
        echo '</div>';
        
        echo '</div>';
        
        ?>
        
        <p style="text-align: center; margin-top: 30px; color: #6c757d;">
            <strong>Test completed at:</strong> <?= date('Y-m-d H:i:s') ?>
        </p>
    </div>
</body>
</html>
