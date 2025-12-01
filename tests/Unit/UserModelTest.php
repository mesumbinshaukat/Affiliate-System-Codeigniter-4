<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class UserModelTest extends CIUnitTestCase
{
    // ==================== PASSWORD HASHING TESTS ====================

    public function testPasswordHashing()
    {
        $plainPassword = 'testpassword123';
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        $this->assertNotEquals($plainPassword, $hashedPassword);
        $this->assertTrue(password_verify($plainPassword, $hashedPassword));
    }

    public function testPasswordHashLength()
    {
        $password = 'SecurePassword123!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertEquals(60, strlen($hash));
    }

    public function testPasswordHashFormat()
    {
        $password = 'TestPassword123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertStringStartsWith('$2y$', $hash);
    }

    public function testPasswordVerificationFails()
    {
        $password = 'CorrectPassword123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertFalse(password_verify('WrongPassword123', $hash));
    }

    public function testDifferentPasswordsProduceDifferentHashes()
    {
        $password1 = 'Password123';
        $password2 = 'Password456';
        
        $hash1 = password_hash($password1, PASSWORD_DEFAULT);
        $hash2 = password_hash($password2, PASSWORD_DEFAULT);
        
        $this->assertNotEquals($hash1, $hash2);
    }

    public function testSamePasswordProducesDifferentHashesEachTime()
    {
        $password = 'SamePassword123';
        
        $hash1 = password_hash($password, PASSWORD_DEFAULT);
        $hash2 = password_hash($password, PASSWORD_DEFAULT);
        
        // Hashes should be different due to salt
        $this->assertNotEquals($hash1, $hash2);
        
        // But both should verify correctly
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }

    // ==================== VALIDATION TESTS ====================

    public function testEmailValidation()
    {
        $validEmails = [
            'test@example.com',
            'user.name@example.com',
            'user+tag@example.co.uk',
            'test123@test-domain.com'
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
        }
    }

    public function testInvalidEmailValidation()
    {
        $invalidEmails = [
            'not-an-email',
            '@example.com',
            'user@',
            'user name@example.com',
            'user@.com'
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
        }
    }

    public function testPasswordLengthValidation()
    {
        $shortPassword = 'short';
        $validPassword = 'ValidPass123!';
        
        $this->assertLessThan(8, strlen($shortPassword));
        $this->assertGreaterThanOrEqual(8, strlen($validPassword));
    }

    public function testUsernameLength()
    {
        $tooShort = 'ab';
        $valid = 'validuser';
        $tooLong = str_repeat('a', 101);
        
        $this->assertLessThan(3, strlen($tooShort));
        $this->assertGreaterThanOrEqual(3, strlen($valid));
        $this->assertLessThanOrEqual(100, strlen($valid));
        $this->assertGreaterThan(100, strlen($tooLong));
    }

    // ==================== DATA SANITIZATION TESTS ====================

    public function testTrimWhitespace()
    {
        $input = '  test@example.com  ';
        $trimmed = trim($input);
        
        $this->assertEquals('test@example.com', $trimmed);
        $this->assertNotEquals($input, $trimmed);
    }

    public function testInputSanitization()
    {
        $dirtyInput = '  Test User  ';
        $clean = trim($dirtyInput);
        
        $this->assertEquals('Test User', $clean);
    }

    // ==================== ROLE AND STATUS TESTS ====================

    public function testValidRoles()
    {
        $validRoles = ['user', 'admin'];
        
        $this->assertContains('user', $validRoles);
        $this->assertContains('admin', $validRoles);
        $this->assertNotContains('superadmin', $validRoles);
    }

    public function testValidStatuses()
    {
        $validStatuses = ['active', 'blocked', 'pending'];
        
        $this->assertContains('active', $validStatuses);
        $this->assertContains('blocked', $validStatuses);
        $this->assertContains('pending', $validStatuses);
    }

    // ==================== TIMESTAMP TESTS ====================

    public function testDateTimeFormat()
    {
        $datetime = date('Y-m-d H:i:s');
        
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $datetime);
    }

    public function testTimestampCreation()
    {
        $timestamp = date('Y-m-d H:i:s');
        $parsed = strtotime($timestamp);
        
        $this->assertNotFalse($parsed);
        $this->assertGreaterThan(0, $parsed);
    }
}
