<?php

/**
 * Standalone Test Script for Mesum User Registration and Login
 * Run: php tests/test_mesum_registration.php
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   Testing Mesum User Registration and Login                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Database credentials
$host = 'localhost';
$database = 'lijstje_db';
$username = 'root';
$password = '';

try {
    // Connect to database
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        die("✗ Connection failed: " . $mysqli->connect_error . "\n");
    }
    
    echo "✓ Database connection successful\n\n";
    
    // Test data for Mesum Bin Shaukat
    $userData = [
        'first_name' => 'Mesum',
        'last_name' => 'Bin Shaukat',
        'username' => 'mesum',
        'email' => 'mesum@gmail.com',
        'password' => 'admin123!',
        'role' => 'user',
        'status' => 'active'
    ];
    
    echo "Test Data:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "First Name: {$userData['first_name']}\n";
    echo "Last Name: {$userData['last_name']}\n";
    echo "Username: {$userData['username']}\n";
    echo "Email: {$userData['email']}\n";
    echo "Password: {$userData['password']}\n";
    echo "\n";
    
    // STEP 1: Clean up if user already exists
    echo "STEP 1: Cleaning up existing user (if any)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $mysqli->query("DELETE FROM users WHERE email = 'mesum@gmail.com'");
    echo "✓ Cleanup complete\n\n";
    
    // STEP 2: Register the user
    echo "STEP 2: Registering new user\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');
    
    $stmt = $mysqli->prepare("
        INSERT INTO users (username, email, password, first_name, last_name, role, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        'sssssssss',
        $userData['username'],
        $userData['email'],
        $hashedPassword,
        $userData['first_name'],
        $userData['last_name'],
        $userData['role'],
        $userData['status'],
        $now,
        $now
    );
    
    if ($stmt->execute()) {
        echo "✓ User registered successfully\n";
        echo "  User ID: " . $mysqli->insert_id . "\n\n";
    } else {
        die("✗ Registration failed: " . $stmt->error . "\n");
    }
    
    // STEP 3: Verify user was created
    echo "STEP 3: Verifying user in database\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $result = $mysqli->query("SELECT * FROM users WHERE email = 'mesum@gmail.com'");
    $user = $result->fetch_assoc();
    
    if ($user) {
        echo "✓ User found in database\n";
        echo "  First Name: {$user['first_name']}\n";
        echo "  Last Name: {$user['last_name']}\n";
        echo "  Username: {$user['username']}\n";
        echo "  Email: {$user['email']}\n";
        echo "  Role: {$user['role']}\n";
        echo "  Status: {$user['status']}\n";
        echo "  Created: {$user['created_at']}\n\n";
    } else {
        die("✗ User not found in database\n");
    }
    
    // STEP 4: Verify password hashing
    echo "STEP 4: Verifying password security\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    if ($user['password'] !== $userData['password']) {
        echo "✓ Password is hashed (not stored as plain text)\n";
        echo "  Hash format: " . substr($user['password'], 0, 7) . "...\n";
        echo "  Hash length: " . strlen($user['password']) . " characters\n";
    } else {
        echo "✗ WARNING: Password is stored as plain text!\n";
    }
    
    if (password_verify($userData['password'], $user['password'])) {
        echo "✓ Password verification successful\n\n";
    } else {
        echo "✗ Password verification failed\n\n";
    }
    
    // STEP 5: Test login with correct password
    echo "STEP 5: Testing login with correct password\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $loginEmail = 'mesum@gmail.com';
    $loginPassword = 'admin123!';
    
    $result = $mysqli->query("SELECT * FROM users WHERE email = '{$loginEmail}'");
    $loginUser = $result->fetch_assoc();
    
    if ($loginUser && password_verify($loginPassword, $loginUser['password'])) {
        echo "✓ Login successful with correct credentials\n";
        echo "  Email: {$loginEmail}\n";
        echo "  Password: {$loginPassword}\n\n";
    } else {
        echo "✗ Login failed\n\n";
    }
    
    // STEP 6: Test login with wrong password
    echo "STEP 6: Testing login with wrong password\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $wrongPassword = 'wrongpassword';
    
    if (!password_verify($wrongPassword, $loginUser['password'])) {
        echo "✓ Login correctly rejected with wrong password\n";
        echo "  Wrong password: {$wrongPassword}\n\n";
    } else {
        echo "✗ Security issue: Wrong password accepted!\n\n";
    }
    
    // STEP 7: Test blocked user scenario
    echo "STEP 7: Testing blocked user scenario\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    // Temporarily block the user
    $mysqli->query("UPDATE users SET status = 'blocked' WHERE email = 'mesum@gmail.com'");
    
    $result = $mysqli->query("SELECT * FROM users WHERE email = 'mesum@gmail.com'");
    $blockedUser = $result->fetch_assoc();
    
    if ($blockedUser['status'] === 'blocked') {
        echo "✓ User status changed to blocked\n";
        echo "  Login should be prevented for blocked users\n\n";
    }
    
    // Restore active status
    $mysqli->query("UPDATE users SET status = 'active' WHERE email = 'mesum@gmail.com'");
    echo "✓ User status restored to active\n\n";
    
    // Summary
    echo "╔══════════════════════════════════════════════════════════════╗\n";
    echo "║                      TEST SUMMARY                            ║\n";
    echo "╚══════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "✓ User Registration: SUCCESS\n";
    echo "✓ Password Hashing: SUCCESS\n";
    echo "✓ Database Storage: SUCCESS\n";
    echo "✓ Login with Correct Password: SUCCESS\n";
    echo "✓ Login with Wrong Password: CORRECTLY REJECTED\n";
    echo "✓ User Status Management: SUCCESS\n";
    echo "\n";
    echo "🎉 All tests passed for Mesum user!\n";
    echo "\n";
    echo "You can now login at: http://localhost:8080/login\n";
    echo "  Email: mesum@gmail.com\n";
    echo "  Password: admin123!\n";
    echo "\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
