<?php

/**
 * Test Registration Form Submission
 * This simulates a form POST to test if the backend is working
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   Testing Registration Form Submission                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Test data
$testData = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'username' => 'johndoe' . time(),
    'email' => 'john' . time() . '@example.com',
    'password' => 'SecurePass123!',
    'password_confirm' => 'SecurePass123!'
];

echo "Test Data:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
foreach ($testData as $key => $value) {
    if ($key !== 'password' && $key !== 'password_confirm') {
        echo "$key: $value\n";
    } else {
        echo "$key: ********\n";
    }
}
echo "\n";

// Simulate POST request
echo "Simulating POST request to /register...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/register');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "HTTP Status Code: $httpCode\n";

// Check for redirect
if ($httpCode >= 300 && $httpCode < 400) {
    preg_match('/Location: (.+)/', $headers, $matches);
    if (isset($matches[1])) {
        $redirectUrl = trim($matches[1]);
        echo "✓ Redirected to: $redirectUrl\n";
        
        if (strpos($redirectUrl, '/dashboard') !== false) {
            echo "✓ Correct redirect to dashboard\n";
        } else {
            echo "⚠ Unexpected redirect location\n";
        }
    }
} else {
    echo "✗ No redirect occurred (HTTP $httpCode)\n";
    echo "\nResponse Headers:\n";
    echo $headers;
    echo "\n";
}

// Check database
echo "\nChecking database...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$mysqli = new mysqli('localhost', 'root', '', 'lijstje_db');

if ($mysqli->connect_error) {
    die("✗ Database connection failed\n");
}

$email = $testData['email'];
$result = $mysqli->query("SELECT * FROM users WHERE email = '$email'");

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "✓ User found in database\n";
    echo "  ID: {$user['id']}\n";
    echo "  Username: {$user['username']}\n";
    echo "  Email: {$user['email']}\n";
    echo "  Name: {$user['first_name']} {$user['last_name']}\n";
    echo "  Role: {$user['role']}\n";
    echo "  Status: {$user['status']}\n";
    
    // Verify password
    if (password_verify($testData['password'], $user['password'])) {
        echo "✓ Password correctly hashed and verified\n";
    } else {
        echo "✗ Password verification failed\n";
    }
    
    // Clean up
    $mysqli->query("DELETE FROM users WHERE email = '$email'");
    echo "✓ Test user cleaned up\n";
} else {
    echo "✗ User NOT found in database\n";
    echo "  This means registration failed\n";
}

$mysqli->close();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST COMPLETE                           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";
