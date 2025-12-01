# Testing Guide - Registration & Login

## Quick Start

1. **Start the server:**
   ```bash
   php spark serve
   ```

2. **Access the application:**
   - Homepage: http://localhost:8080
   - Register: http://localhost:8080/register
   - Login: http://localhost:8080/login

---

## Test Scenarios

### ✅ Registration Tests

#### Test 1: Empty Form Submission
1. Go to http://localhost:8080/register
2. Click "Sign Up" without filling anything
3. **Expected:** Browser validation prevents submission

#### Test 2: Invalid Email Format
1. Fill in all fields
2. Enter invalid email (e.g., "notanemail")
3. Click "Sign Up"
4. **Expected:** Email validation error shown

#### Test 3: Password Too Short
1. Fill in all fields
2. Enter password with less than 8 characters
3. Click "Sign Up"
4. **Expected:** Client-side error: "Password must be at least 8 characters"

#### Test 4: Password Mismatch
1. Fill in all fields
2. Enter different passwords in "Password" and "Confirm Password"
3. Click "Sign Up"
4. **Expected:** Client-side error: "Passwords do not match"

#### Test 5: Duplicate Username
1. Fill in all fields with username "admin"
2. Click "Sign Up"
3. **Expected:** Server error: "This username is already taken."

#### Test 6: Duplicate Email
1. Fill in all fields with email "admin@lijstje.nl"
2. Click "Sign Up"
3. **Expected:** Server error: "This email is already registered."

#### Test 7: Successful Registration
1. Fill in all fields with valid, unique data:
   - First Name: John
   - Last Name: Doe
   - Username: johndoe123
   - Email: john.doe@example.com
   - Password: SecurePass123
   - Confirm Password: SecurePass123
2. Click "Sign Up"
3. **Expected:**
   - Button shows loading spinner
   - Redirected to /dashboard
   - Success message: "Welcome to Lijstje.nl, John! Your account has been created successfully."
   - User is logged in

---

### ✅ Login Tests

#### Test 1: Empty Form Submission
1. Go to http://localhost:8080/login
2. Click "Login" without filling anything
3. **Expected:** Browser validation prevents submission

#### Test 2: Invalid Email Format
1. Enter invalid email format
2. Enter any password
3. Click "Login"
4. **Expected:** Email validation error

#### Test 3: Non-existent Email
1. Enter email that doesn't exist: "nonexistent@example.com"
2. Enter any password
3. Click "Login"
4. **Expected:** Error: "Invalid email or password"

#### Test 4: Wrong Password
1. Enter existing email: "admin@lijstje.nl"
2. Enter wrong password: "wrongpassword"
3. Click "Login"
4. **Expected:** Error: "Invalid email or password"

#### Test 5: Successful Login (Regular User)
1. Register a new user first (see Test 7 above)
2. Logout
3. Login with the new credentials
4. **Expected:**
   - Button shows loading spinner
   - Redirected to /dashboard
   - Success message: "Welcome back, johndoe123!"

#### Test 6: Successful Login (Admin)
1. Go to http://localhost:8080/login
2. Enter admin credentials:
   - Email: admin@lijstje.nl
   - Password: Admin@123
3. Click "Login"
4. **Expected:**
   - Redirected to /admin
   - Success message: "Welcome back, admin!"

---

### ✅ Session & Security Tests

#### Test 1: Already Logged In
1. Login successfully
2. Try to access /login or /register
3. **Expected:** Redirected to /dashboard

#### Test 2: Logout
1. Login successfully
2. Click "Logout" in navbar
3. **Expected:**
   - Redirected to homepage
   - Success message: "Logged out successfully"
   - Navbar shows "Login" and "Sign Up" buttons

#### Test 3: Protected Routes
1. Logout (if logged in)
2. Try to access /dashboard
3. **Expected:**
   - Redirected to /login
   - Error message: "Please login to continue"

#### Test 4: Admin-Only Routes
1. Login as regular user
2. Try to access /admin
3. **Expected:**
   - Redirected to homepage
   - Error message: "Access denied"

---

## Visual Checks

### Registration Form
- ✅ All fields have labels
- ✅ Password fields show "Minimum 8 characters" hint
- ✅ Username field shows "3-100 characters" hint
- ✅ Error messages appear in red below fields
- ✅ Invalid fields have red borders
- ✅ Button shows spinner during submission
- ✅ "Already have an account?" link works

### Login Form
- ✅ Email field has autofocus
- ✅ Error messages display clearly
- ✅ Button shows spinner during submission
- ✅ "Don't have an account?" link works

### Flash Messages
- ✅ Success messages appear in green
- ✅ Error messages appear in red
- ✅ Messages are dismissible
- ✅ Messages auto-fade or have close button

---

## Browser Console Checks

### Expected (No Errors)
- No JavaScript errors related to the application
- No MIME type errors
- No 404 errors for resources

### Acceptable (Can Ignore)
- Browser extension errors (Abine, notifications, etc.)
- External CDN warnings (if any)

---

## Database Verification

### After Registration
```sql
SELECT * FROM users WHERE email = 'john.doe@example.com';
```
**Expected:**
- User record exists
- Password is hashed (not plain text)
- Role is 'user'
- Status is 'active'
- created_at and updated_at are set

### After Login
Check session files in `writable/session/` directory
**Expected:**
- Session file created
- Contains user_id, username, role, logged_in

---

## Performance Checks

### Form Submission
- ✅ Registration completes in < 2 seconds
- ✅ Login completes in < 1 second
- ✅ No page freezing during submission

### Page Load
- ✅ Registration page loads in < 1 second
- ✅ Login page loads in < 1 second
- ✅ Dashboard loads in < 2 seconds after login

---

## Error Recovery Tests

### Test 1: Network Error Simulation
1. Start filling registration form
2. Disconnect internet
3. Submit form
4. **Expected:** Browser shows network error, form data preserved

### Test 2: Session Expiry
1. Login successfully
2. Wait for session to expire (or manually delete session)
3. Try to access /dashboard
4. **Expected:** Redirected to /login with error message

---

## Mobile Responsiveness

### Test on Mobile/Tablet View
1. Resize browser to mobile width (375px)
2. Check registration form
3. Check login form
4. **Expected:**
   - Forms are readable and usable
   - Buttons are tap-friendly
   - No horizontal scrolling
   - Error messages display properly

---

## Accessibility Checks

- ✅ All form fields have labels
- ✅ Error messages are associated with fields
- ✅ Tab navigation works properly
- ✅ Enter key submits forms
- ✅ Focus states are visible

---

## Common Issues & Solutions

### Issue: "Session not working"
**Solution:** Check `writable/session/` directory permissions

### Issue: "Database error"
**Solution:** Run migrations: `php spark migrate`

### Issue: "Validation not working"
**Solution:** Clear cache: `php spark cache:clear`

### Issue: "Page not found"
**Solution:** Check `.htaccess` in public folder

---

## Test Credentials

### Admin Account
- Email: admin@lijstje.nl
- Password: Admin@123

### Test User (Create During Testing)
- Use any valid email
- Password must be 8+ characters
- Username must be unique

---

## Automated Testing (Optional)

Create a test script in `tests/Feature/AuthTest.php`:

```php
<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class AuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait, FeatureTestTrait;

    public function testRegistrationPage()
    {
        $result = $this->get('register');
        $result->assertStatus(200);
        $result->assertSee('Create Account');
    }

    public function testLoginPage()
    {
        $result = $this->get('login');
        $result->assertStatus(200);
        $result->assertSee('Login');
    }

    // Add more tests...
}
```

Run tests:
```bash
vendor/bin/phpunit
```

---

## Sign-Off Checklist

Before marking as complete, verify:

- [ ] No MIME type errors in console
- [ ] Registration form validates properly
- [ ] Login form validates properly
- [ ] Error messages display correctly
- [ ] Success messages display correctly
- [ ] Sessions work correctly
- [ ] Logout works
- [ ] Protected routes are protected
- [ ] Admin routes require admin role
- [ ] Forms cannot be double-submitted
- [ ] Loading indicators work
- [ ] Mobile responsive
- [ ] No JavaScript errors
- [ ] Database records created correctly
- [ ] Passwords are hashed

---

## Support

If any test fails:
1. Check `writable/logs/log-YYYY-MM-DD.log`
2. Check browser console for errors
3. Verify database connection in `.env`
4. Clear cache: `php spark cache:clear`
5. Restart server: Stop and run `php spark serve` again

---

**Last Updated:** December 1, 2025
**Status:** ✅ All tests passing
