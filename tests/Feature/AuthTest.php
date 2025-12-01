<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class AuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    // ==================== PAGE LOAD TESTS ====================

    public function testRegistrationPageLoads()
    {
        $result = $this->get('/register');
        $result->assertStatus(200);
        $result->assertSee('Create Account');
    }

    public function testLoginPageLoads()
    {
        $result = $this->get('/login');
        $result->assertStatus(200);
        $result->assertSee('Login');
    }

    public function testLogoutRedirects()
    {
        $result = $this->get('/logout');
        $result->assertRedirect();
    }

    public function testDashboardRequiresAuth()
    {
        $result = $this->get('/dashboard');
        $result->assertRedirect();
    }

    public function testAdminRequiresAuth()
    {
        $result = $this->get('/admin');
        $result->assertRedirect();
    }

    // ==================== BACKEND DATABASE TESTS ====================

    public function testUserCanBeCreatedInDatabase()
    {
        $db = \Config\Database::connect();
        $uniqueId = time() . rand(1000, 9999);
        
        $db->table('users')->insert([
            'username' => 'testuser' . $uniqueId,
            'email' => 'test' . $uniqueId . '@example.com',
            'password' => password_hash('TestPassword123!', PASSWORD_DEFAULT),
            'first_name' => 'Test',
            'last_name' => 'User',
            'role' => 'user',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $user = $db->table('users')
            ->where('email', 'test' . $uniqueId . '@example.com')
            ->get()
            ->getRowArray();

        $this->assertNotNull($user);
        $this->assertEquals('testuser' . $uniqueId, $user['username']);
        $this->assertEquals('user', $user['role']);
        $this->assertEquals('active', $user['status']);

        // Clean up
        $db->table('users')->where('email', 'test' . $uniqueId . '@example.com')->delete();
    }

    public function testPasswordIsHashedInDatabase()
    {
        $db = \Config\Database::connect();
        $uniqueId = time() . rand(1000, 9999);
        $plainPassword = 'SecurePassword123!';
        
        $db->table('users')->insert([
            'username' => 'hashtest' . $uniqueId,
            'email' => 'hashtest' . $uniqueId . '@example.com',
            'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
            'first_name' => 'Hash',
            'last_name' => 'Test',
            'role' => 'user',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $user = $db->table('users')
            ->where('email', 'hashtest' . $uniqueId . '@example.com')
            ->get()
            ->getRowArray();

        $this->assertNotEquals($plainPassword, $user['password']);
        $this->assertTrue(password_verify($plainPassword, $user['password']));

        // Clean up
        $db->table('users')->where('email', 'hashtest' . $uniqueId . '@example.com')->delete();
    }

    public function testUserCanBeRetrievedByEmail()
    {
        $db = \Config\Database::connect();
        
        // Use existing admin user
        $user = $db->table('users')
            ->where('email', 'admin@lijstje.nl')
            ->get()
            ->getRowArray();

        $this->assertNotNull($user);
        $this->assertEquals('admin', $user['role']);
        $this->assertEquals('active', $user['status']);
    }

    public function testUserStatusCanBeBlocked()
    {
        $db = \Config\Database::connect();
        $uniqueId = time() . rand(1000, 9999);
        
        $db->table('users')->insert([
            'username' => 'blockedtest' . $uniqueId,
            'email' => 'blocked' . $uniqueId . '@example.com',
            'password' => password_hash('TestPassword123!', PASSWORD_DEFAULT),
            'first_name' => 'Blocked',
            'last_name' => 'Test',
            'role' => 'user',
            'status' => 'blocked',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $user = $db->table('users')
            ->where('email', 'blocked' . $uniqueId . '@example.com')
            ->get()
            ->getRowArray();

        $this->assertEquals('blocked', $user['status']);

        // Clean up
        $db->table('users')->where('email', 'blocked' . $uniqueId . '@example.com')->delete();
    }

    public function testMultipleUsersCanExist()
    {
        $db = \Config\Database::connect();
        
        $count = $db->table('users')->countAllResults();
        
        $this->assertGreaterThan(0, $count);
    }

    public function testAdminUserExists()
    {
        $db = \Config\Database::connect();
        
        $admin = $db->table('users')
            ->where('role', 'admin')
            ->get()
            ->getRowArray();

        $this->assertNotNull($admin);
        $this->assertEquals('admin', $admin['role']);
    }

    // ==================== SPECIFIC USER REGISTRATION AND LOGIN TEST ====================

    public function testRegisterAndLoginMesumUser()
    {
        $db = \Config\Database::connect();
        
        // First, clean up if user already exists
        $db->table('users')->where('email', 'mesum@gmail.com')->delete();
        
        // Test data for Mesum Bin Shaukat
        $userData = [
            'first_name' => 'Mesum',
            'last_name' => 'Bin Shaukat',
            'username' => 'mesum',
            'email' => 'mesum@gmail.com',
            'password' => 'admin123!',
            'password_confirm' => 'admin123!'
        ];
        
        // STEP 1: Register the user via POST request
        $result = $this->post('/register', $userData);
        
        // Should redirect after registration
        $result->assertRedirect();
        
        // STEP 2: Verify user was created in database
        $user = $db->table('users')
            ->where('email', 'mesum@gmail.com')
            ->get()
            ->getRowArray();
        
        $this->assertNotNull($user, 'User should be created in database');
        $this->assertEquals('Mesum', $user['first_name']);
        $this->assertEquals('Bin Shaukat', $user['last_name']);
        $this->assertEquals('mesum', $user['username']);
        $this->assertEquals('mesum@gmail.com', $user['email']);
        $this->assertEquals('user', $user['role']);
        $this->assertEquals('active', $user['status']);
        
        // STEP 3: Verify password was hashed (not stored as plain text)
        $this->assertNotEquals('admin123!', $user['password'], 'Password should be hashed');
        $this->assertTrue(
            password_verify('admin123!', $user['password']),
            'Password hash should verify correctly'
        );
        
        // STEP 4: Test login with the registered credentials
        $loginData = [
            'email' => 'mesum@gmail.com',
            'password' => 'admin123!'
        ];
        
        $loginResult = $this->post('/login', $loginData);
        
        // Should redirect after successful login
        $loginResult->assertRedirect();
        
        // STEP 5: Verify user still exists and is active
        $userAfterLogin = $db->table('users')
            ->where('email', 'mesum@gmail.com')
            ->get()
            ->getRowArray();
        
        $this->assertNotNull($userAfterLogin);
        $this->assertEquals('active', $userAfterLogin['status']);
        
        // STEP 6: Test login with wrong password should fail
        $wrongPasswordData = [
            'email' => 'mesum@gmail.com',
            'password' => 'wrongpassword'
        ];
        
        $wrongLoginResult = $this->post('/login', $wrongPasswordData);
        $wrongLoginResult->assertRedirect();
        
        // STEP 7: Verify password verification works correctly
        $this->assertFalse(
            password_verify('wrongpassword', $user['password']),
            'Wrong password should not verify'
        );
        
        // Clean up - remove test user
        $db->table('users')->where('email', 'mesum@gmail.com')->delete();
    }
}
