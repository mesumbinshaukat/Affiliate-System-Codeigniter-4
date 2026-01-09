# Social Authentication Integration - Facebook & Google OAuth

## Overview
This document describes the complete Facebook and Google OAuth integration for the Lijstje.nl affiliate platform.

## Features Implemented

### 1. **OAuth Providers**
- ✅ Facebook Login/Registration
- ✅ Google Login/Registration

### 2. **Core Functionality**
- ✅ One-click social login
- ✅ Automatic account creation for new users
- ✅ Account linking for existing email addresses
- ✅ Default list creation for new social users
- ✅ Profile picture import from social providers
- ✅ Email verification status tracking
- ✅ OAuth token storage for future API calls

### 3. **Security & Error Handling**
- ✅ Provider validation
- ✅ Session management
- ✅ CSRF protection via OAuth state parameter
- ✅ Blocked user detection
- ✅ Comprehensive error logging
- ✅ User-friendly error messages
- ✅ Graceful fallback on failures

## Database Schema

### New Fields Added to `users` Table
```sql
provider VARCHAR(50) NULL           -- 'facebook' or 'google'
provider_id VARCHAR(255) NULL       -- Unique ID from OAuth provider
provider_token TEXT NULL            -- OAuth access token (encrypted)
email_verified TINYINT(1) DEFAULT 0 -- Email verification status
```

### Index Added
```sql
INDEX idx_provider_auth (provider, provider_id)
```

## Configuration

### Environment Variables (.env)
```env
FACEBOOK_APP_ID = your_facebook_app_id_here
FACEBOOK_APP_SECRET = your_facebook_app_secret_here

GOOGLE_CLIENT_ID = your_google_client_id_here
GOOGLE_CLIENT_SECRET = your_google_client_secret_here
```

### OAuth Callback URL
**Important:** Configure this URL in both Facebook and Google developer consoles:
```
http://localhost:8080/auth/social/callback
```

For production, update to:
```
https://yourdomain.com/auth/social/callback
```

## Routes

### Social Authentication Endpoints
```php
GET  /auth/social/facebook          - Initiate Facebook OAuth
GET  /auth/social/google             - Initiate Google OAuth
GET  /auth/social/callback           - OAuth callback handler (all providers)
GET  /auth/social/disconnect         - Disconnect social account (requires auth)
```

## User Flow

### New User Registration via Social Login
1. User clicks "Registreren met Facebook/Google"
2. Redirected to provider's OAuth consent screen
3. User grants permissions
4. Callback receives user profile data
5. System creates new user account with:
   - Auto-generated unique username
   - Random secure password
   - Profile picture from provider
   - Email verification status
   - Provider credentials stored
6. Default list created automatically
7. User logged in and redirected to dashboard

### Existing User Login via Social
1. User clicks "Inloggen met Facebook/Google"
2. System finds existing account by provider_id
3. Updates OAuth token
4. User logged in immediately

### Account Linking (Email Match)
1. User tries social login with email already in system
2. System detects email match
3. Links social provider to existing account
4. Marks email as verified
5. User logged in to existing account

## Edge Cases Handled

### 1. **Missing Email from Provider**
- Username generated from display name or random
- Account created without email
- User can add email later in profile

### 2. **Duplicate Username**
- System auto-generates unique username with counter
- Example: `john` → `john1` → `john2`

### 3. **User Cancels OAuth**
- Graceful redirect to login page
- User-friendly message displayed

### 4. **Provider API Errors**
- Comprehensive error logging
- User sees generic error message
- Session cleaned up properly

### 5. **Blocked User Attempts Login**
- Social login blocked for banned users
- Clear message displayed
- No account creation/update

### 6. **Session Expiration**
- OAuth state validated
- Expired sessions handled gracefully
- User prompted to retry

### 7. **Missing Profile Data**
- Handles missing first/last name
- Handles missing profile picture
- Handles missing email verification

### 8. **Account Disconnection**
- Requires password to be set first
- Prevents account lockout
- Clears provider credentials

## Code Structure

### Files Created/Modified

#### New Files
1. `app/Config/SocialAuth.php` - OAuth configuration
2. `app/Controllers/SocialAuth.php` - OAuth controller
3. `app/Database/Migrations/2026-01-08-000001_AddSocialAuthFieldsToUsersTable.php`

#### Modified Files
1. `app/Models/UserModel.php` - Added social auth methods
2. `app/Config/Routes.php` - Added social auth routes
3. `app/Views/auth/login.php` - Added social login buttons
4. `app/Views/auth/register.php` - Added social registration buttons
5. `.env` - Added OAuth credentials
6. `composer.json` - Added Hybridauth library

### Key Methods in UserModel

```php
findBySocialProvider($provider, $providerId)
createOrUpdateFromSocial($providerData, $provider)
generateUniqueUsername($providerData)
```

### Key Methods in SocialAuthController

```php
login($provider)                    - Initiate OAuth flow
callback()                          - Handle OAuth callback
disconnect()                        - Remove social provider link
createDefaultListForUser($user)     - Create default list for new users
```

## Testing Checklist

### Facebook OAuth
- [ ] New user registration
- [ ] Existing user login
- [ ] Email account linking
- [ ] Profile picture import
- [ ] Error handling (cancel, API errors)
- [ ] Blocked user prevention

### Google OAuth
- [ ] New user registration
- [ ] Existing user login
- [ ] Email account linking
- [ ] Profile picture import
- [ ] Error handling (cancel, API errors)
- [ ] Blocked user prevention

### Edge Cases
- [ ] Missing email from provider
- [ ] Duplicate username handling
- [ ] Session expiration
- [ ] Provider API downtime
- [ ] Account disconnection
- [ ] Multiple social accounts with same email

## Security Considerations

### 1. **Token Storage**
- OAuth tokens stored in database
- Consider encryption for production
- Tokens refreshed on each login

### 2. **CSRF Protection**
- OAuth state parameter validates requests
- Session-based state management

### 3. **Provider Validation**
- Only whitelisted providers allowed
- Provider configuration validated

### 4. **Account Takeover Prevention**
- Email verification status tracked
- Existing accounts linked only by email match
- Provider ID uniqueness enforced

### 5. **Data Privacy**
- Minimal data requested from providers
- Only necessary permissions requested
- User data handled per GDPR guidelines

## Production Deployment

### 1. **Update OAuth Callback URLs**
Update in Facebook and Google developer consoles:
```
https://yourdomain.com/auth/social/callback
```

### 2. **Update .env**
```env
app.baseURL = 'https://yourdomain.com/'
```

### 3. **Enable HTTPS**
- Force HTTPS in production
- Update `app.forceGlobalSecureRequests = true`

### 4. **Token Encryption**
Consider encrypting `provider_token` field:
```php
$encryptedToken = service('encrypter')->encrypt($token);
```

### 5. **Rate Limiting**
Implement rate limiting on OAuth endpoints to prevent abuse.

### 6. **Monitoring**
- Monitor OAuth error rates
- Track successful vs failed authentications
- Alert on unusual patterns

## Troubleshooting

### "Invalid OAuth Callback URL"
- Verify callback URL in provider console matches exactly
- Check for trailing slashes
- Ensure protocol matches (http vs https)

### "Provider Not Enabled"
- Check `SocialAuth.php` config
- Verify credentials in `.env`
- Restart application after config changes

### "Unable to Connect to Provider"
- Check internet connectivity
- Verify provider API status
- Check firewall settings

### "Email Already Registered"
- This is expected behavior for account linking
- User will be logged into existing account
- Provider credentials added to existing account

## Future Enhancements

1. **Additional Providers**
   - Twitter/X OAuth
   - LinkedIn OAuth
   - Apple Sign In

2. **Profile Management**
   - Allow users to link multiple providers
   - Show connected providers in profile
   - Allow disconnection with safeguards

3. **Enhanced Security**
   - Two-factor authentication
   - OAuth token encryption
   - Suspicious login detection

4. **Analytics**
   - Track social login usage
   - Provider preference metrics
   - Conversion rates

## Support

For issues or questions:
1. Check logs in `writable/logs/`
2. Review Hybridauth documentation
3. Verify provider console settings
4. Test with verbose error logging enabled
