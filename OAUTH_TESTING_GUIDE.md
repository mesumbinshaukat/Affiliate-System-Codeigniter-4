# OAuth Testing Guide - Facebook & Google Login

## Prerequisites

### 1. Configure Facebook App
1. Go to https://developers.facebook.com/apps/
2. Select your app (ID: 4334745516806899)
3. Navigate to **Settings > Basic**
4. Add **App Domains**: `localhost`
5. Navigate to **Facebook Login > Settings**
6. Add **Valid OAuth Redirect URIs**: 
   ```
   http://localhost:8080/auth/social/callback
   ```
7. Save changes

### 2. Configure Google OAuth
1. Go to https://console.cloud.google.com/
2. Select your project
3. Navigate to **APIs & Services > Credentials**
4. Edit OAuth 2.0 Client (ID: 411095013340-g1peggj1p5il1i6p0tqu5vfoj6eskod5)
5. Add **Authorized redirect URIs**:
   ```
   http://localhost:8080/auth/social/callback
   ```
6. Save changes

### 3. Verify Server is Running
```bash
php spark serve
```
Server should be running at: http://localhost:8080

## Test Scenarios

### Scenario 1: New User Registration via Facebook

**Steps:**
1. Open http://localhost:8080/register
2. Click "Registreren met Facebook" button
3. Login to Facebook (if not already logged in)
4. Grant permissions when prompted
5. You should be redirected back to the application

**Expected Results:**
- ✅ New user account created
- ✅ Username auto-generated from Facebook profile
- ✅ Email imported from Facebook
- ✅ Profile picture imported
- ✅ Default list created
- ✅ User logged in automatically
- ✅ Redirected to dashboard with success message
- ✅ Database record has `provider='facebook'` and `provider_id` set

**Database Verification:**
```sql
SELECT id, username, email, provider, provider_id, email_verified, avatar 
FROM users 
WHERE provider = 'facebook' 
ORDER BY created_at DESC 
LIMIT 1;
```

### Scenario 2: New User Registration via Google

**Steps:**
1. Open http://localhost:8080/register
2. Click "Registreren met Google" button
3. Select Google account
4. Grant permissions when prompted
5. You should be redirected back to the application

**Expected Results:**
- ✅ New user account created
- ✅ Username auto-generated from Google profile
- ✅ Email imported from Google
- ✅ Profile picture imported
- ✅ Default list created
- ✅ User logged in automatically
- ✅ Redirected to dashboard with success message
- ✅ Database record has `provider='google'` and `provider_id` set

### Scenario 3: Existing User Login via Facebook

**Steps:**
1. Logout if logged in
2. Open http://localhost:8080/login
3. Click "Inloggen met Facebook" button
4. Login to Facebook (same account as Scenario 1)
5. You should be redirected back to the application

**Expected Results:**
- ✅ Existing user account found
- ✅ OAuth token updated in database
- ✅ User logged in automatically
- ✅ Redirected to dashboard with "Welcome back" message
- ✅ No duplicate account created

### Scenario 4: Existing User Login via Google

**Steps:**
1. Logout if logged in
2. Open http://localhost:8080/login
3. Click "Inloggen met Google" button
4. Select Google account (same as Scenario 2)
5. You should be redirected back to the application

**Expected Results:**
- ✅ Existing user account found
- ✅ OAuth token updated in database
- ✅ User logged in automatically
- ✅ Redirected to dashboard with "Welcome back" message
- ✅ No duplicate account created

### Scenario 5: Account Linking (Email Match)

**Steps:**
1. Create a regular account via email: test@example.com
2. Logout
3. Try to login with Facebook/Google using the SAME email
4. You should be redirected back to the application

**Expected Results:**
- ✅ Existing email account found
- ✅ Social provider linked to existing account
- ✅ `provider` and `provider_id` fields updated
- ✅ `email_verified` set to 1
- ✅ User logged into existing account
- ✅ No duplicate account created

**Database Verification:**
```sql
SELECT id, username, email, provider, provider_id, email_verified 
FROM users 
WHERE email = 'test@example.com';
```

### Scenario 6: User Cancels OAuth

**Steps:**
1. Open http://localhost:8080/login
2. Click "Inloggen met Facebook" or "Inloggen met Google"
3. Click "Cancel" on the OAuth consent screen
4. You should be redirected back to login page

**Expected Results:**
- ✅ Redirected to /login
- ✅ Error message: "You cancelled the login process."
- ✅ No account created
- ✅ Session cleaned up

### Scenario 7: Duplicate Username Handling

**Steps:**
1. Create user with username "john" manually in database
2. Try to register via Facebook with name "John Doe"
3. System should auto-generate unique username

**Expected Results:**
- ✅ New account created with username "john1" or "john2"
- ✅ No database constraint error
- ✅ User logged in successfully

### Scenario 8: Missing Email from Provider

**Steps:**
1. Configure Facebook app to NOT request email permission
2. Try to register via Facebook
3. Account should still be created

**Expected Results:**
- ✅ Account created without email
- ✅ Username generated from display name
- ✅ User logged in successfully
- ✅ Email field is NULL in database

### Scenario 9: Blocked User Prevention

**Steps:**
1. Create a social login account
2. Admin blocks the user (set status='blocked')
3. Logout and try to login again via social

**Expected Results:**
- ✅ Login blocked
- ✅ Error message: "Your account has been blocked. Please contact support."
- ✅ User NOT logged in
- ✅ Redirected to login page

### Scenario 10: Provider API Error

**Steps:**
1. Temporarily disable internet connection
2. Try to login via Facebook/Google
3. Re-enable internet

**Expected Results:**
- ✅ Error message: "Unable to connect to [Provider]. Please try again later."
- ✅ Error logged in `writable/logs/`
- ✅ User redirected to login page
- ✅ Session cleaned up

## Error Checking

### Check Application Logs
```bash
# Windows
Get-Content writable\logs\log-2026-01-08.log -Tail 50

# Linux/Mac
tail -f writable/logs/log-$(date +%Y-%m-%d).log
```

### Check Hybridauth Debug Log
```bash
# Windows
Get-Content writable\logs\hybridauth.log -Tail 50

# Linux/Mac
tail -f writable/logs/hybridauth.log
```

### Common Errors and Solutions

#### "Invalid OAuth Callback URL"
**Cause:** Callback URL not configured in provider console
**Solution:** Add `http://localhost:8080/auth/social/callback` to provider settings

#### "Authentication session expired"
**Cause:** Session lost between redirect and callback
**Solution:** 
- Check session configuration
- Ensure cookies are enabled
- Clear browser cache

#### "Invalid social login provider"
**Cause:** Trying to use unsupported provider
**Solution:** Only use 'facebook' or 'google'

#### "Provider is currently disabled"
**Cause:** Provider disabled in SocialAuth config
**Solution:** Check `app/Config/SocialAuth.php` - ensure `enabled => true`

#### "Unable to retrieve your profile information"
**Cause:** Provider didn't return required data
**Solution:** 
- Check provider permissions
- Verify app is not in development mode restrictions
- Check provider API status

## Browser Developer Tools

### Check Network Requests
1. Open DevTools (F12)
2. Go to Network tab
3. Initiate social login
4. Look for:
   - Redirect to provider (302)
   - Callback request (GET /auth/social/callback)
   - Final redirect (302 to dashboard)

### Check Console for Errors
1. Open DevTools (F12)
2. Go to Console tab
3. Look for JavaScript errors
4. Verify no CORS errors

### Check Cookies
1. Open DevTools (F12)
2. Go to Application > Cookies
3. Verify `lijstje_session` cookie exists
4. Check cookie has proper domain and path

## Database Verification Queries

### View All Social Auth Users
```sql
SELECT 
    id, 
    username, 
    email, 
    provider, 
    provider_id, 
    email_verified,
    created_at 
FROM users 
WHERE provider IS NOT NULL 
ORDER BY created_at DESC;
```

### Count Users by Provider
```sql
SELECT 
    provider, 
    COUNT(*) as user_count 
FROM users 
WHERE provider IS NOT NULL 
GROUP BY provider;
```

### Find Linked Accounts
```sql
SELECT 
    id, 
    username, 
    email, 
    provider, 
    created_at 
FROM users 
WHERE email IN (
    SELECT email 
    FROM users 
    WHERE email IS NOT NULL 
    GROUP BY email 
    HAVING COUNT(*) > 1
);
```

### Check Recent Social Logins
```sql
SELECT 
    id, 
    username, 
    email, 
    provider, 
    updated_at 
FROM users 
WHERE provider IS NOT NULL 
ORDER BY updated_at DESC 
LIMIT 10;
```

## Performance Testing

### Load Test Social Login
```bash
# Test 100 concurrent social login initiations
ab -n 100 -c 10 http://localhost:8080/auth/social/facebook
```

### Monitor Response Times
- Initial redirect: < 200ms
- OAuth callback: < 500ms
- Account creation: < 1000ms
- Total flow: < 5 seconds

## Security Checklist

- [ ] OAuth callback URL uses HTTPS in production
- [ ] CSRF protection via state parameter
- [ ] Provider credentials in .env (not hardcoded)
- [ ] Blocked users cannot login
- [ ] Email verification status tracked
- [ ] OAuth tokens stored securely
- [ ] Error messages don't leak sensitive info
- [ ] Rate limiting on OAuth endpoints
- [ ] Session timeout configured properly
- [ ] Audit logging for social logins

## Production Readiness

Before deploying to production:

1. **Update Callback URLs**
   - Facebook: Add production domain
   - Google: Add production domain

2. **Environment Variables**
   - Update `app.baseURL` to production URL
   - Enable `app.forceGlobalSecureRequests`

3. **Security**
   - Enable OAuth token encryption
   - Implement rate limiting
   - Set up monitoring/alerts

4. **Testing**
   - Test all scenarios on staging
   - Verify SSL certificate
   - Test from multiple devices/browsers

5. **Documentation**
   - Update user documentation
   - Train support team
   - Prepare rollback plan

## Troubleshooting Commands

### Clear All Sessions
```bash
php spark cache:clear
rm -rf writable/session/*
```

### Reset Test User
```sql
DELETE FROM users WHERE email = 'test@example.com';
```

### View Recent Errors
```bash
grep "ERROR\|CRITICAL" writable/logs/log-$(date +%Y-%m-%d).log | tail -20
```

### Test OAuth Configuration
```bash
php spark
# In spark console:
$config = new \Config\SocialAuth();
print_r($config->config);
```

## Support Resources

- **Hybridauth Docs:** https://hybridauth.github.io/
- **Facebook OAuth:** https://developers.facebook.com/docs/facebook-login
- **Google OAuth:** https://developers.google.com/identity/protocols/oauth2
- **CodeIgniter Docs:** https://codeigniter.com/user_guide/

## Success Criteria

All tests pass when:
- ✅ New users can register via Facebook
- ✅ New users can register via Google
- ✅ Existing users can login via Facebook
- ✅ Existing users can login via Google
- ✅ Email accounts are linked properly
- ✅ Duplicate usernames handled
- ✅ User cancellation handled gracefully
- ✅ Blocked users cannot login
- ✅ Errors logged and displayed properly
- ✅ No security vulnerabilities
- ✅ Performance meets requirements
- ✅ Database integrity maintained
