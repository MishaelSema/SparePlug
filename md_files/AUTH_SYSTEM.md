# Authentication System Documentation

## Overview
Comprehensive authentication system for SparePlug platform supporting multiple registration methods, verification systems, and security features.

## Features Implemented

### ✅ Registration
- **Buyer Registration** (`pages/auth/register-buyer.php`)
  - Email registration with verification
  - Phone registration with SMS verification
  - Google OAuth integration (UI ready)
  - Password strength validation
  - Real-time form validation

### ✅ Login
- **Enhanced Login** (`pages/auth/login.php`)
  - Login with email OR phone number
  - Remember me functionality
  - Two-Factor Authentication (2FA) support
  - Social login (Google, Facebook - UI ready)
  - Password visibility toggle

### ✅ Password Management
- **Forgot Password** (`pages/auth/forgot-password.php`)
  - Email-based password reset
  - Secure reset tokens
  - Time-limited reset links (1 hour)

- **Reset Password** (`pages/auth/reset-password.php`)
  - Secure token validation
  - Password strength requirements
  - One-time use tokens

### ✅ Email Verification
- **Email Verification** (`pages/auth/verify-email.php`)
  - Email verification on registration
  - Verification links with 24-hour expiry
  - Success/failure feedback

### ✅ Phone Verification
- **Phone Verification** (`pages/auth/verify-phone.php`)
  - SMS code verification
  - 6-digit codes with 10-minute expiry
  - Maximum 3 verification attempts
  - Resend code functionality

### ✅ Two-Factor Authentication
- **2FA Support** (Integrated in login flow)
  - Optional 2FA for users
  - 6-digit verification codes
  - Email-based 2FA delivery
  - 5-minute code expiry

### ✅ Social Login
- **Google OAuth** (UI Ready)
- **Facebook OAuth** (UI Ready)
- Provider ID tracking
- Auto account creation
- Social login history tracking

## Database Schema

### New Tables
1. **email_verifications** - Track email verification tokens
2. **phone_verifications** - Track SMS verification codes
3. **password_resets** - Manage password reset tokens
4. **two_factor_codes** - Store temporary 2FA codes
5. **social_logins** - Track social authentication
6. **login_logs** - Security and audit logging

### Enhanced Users Table
- `phone_verified` - Phone verification status
- `phone_verification_code` - Temporary codes
- `two_factor_enabled` - 2FA status
- `two_factor_secret` - 2FA secret key
- `google_id` - Google OAuth ID
- `facebook_id` - Facebook OAuth ID
- `password_reset_token` - Password reset tokens
- `email_verification_token` - Email verification tokens

## API Endpoints

### Authentication
- `POST /auth/register` - User registration
- `POST /auth/login` - User login
- `POST /auth/verify-2fa` - 2FA verification
- `POST /auth/forgot-password` - Request password reset
- `POST /auth/reset-password` - Reset password
- `GET /auth/verify-email` - Email verification
- `POST /auth/verify-phone` - Phone verification

### Social Login
- `POST /auth/social-login` - Social authentication
- `GET /auth/social/callback` - OAuth callback

## Usage

### Setup
1. Run the database migrations:
```bash
mysql -u root -p spareplug_db < database/auth_schema_updates.sql
```

### Register as Buyer

**Email Registration:**
```bash
Access: index.php?page=register-buyer
Method: Email
Fields: first_name, last_name, email, password, confirm_password
Verification: Email sent automatically
```

**Phone Registration:**
```bash
Access: index.php?page=register-buyer
Method: Phone
Fields: first_name, last_name, phone, password, confirm_password
Verification: SMS code sent (UI ready)
```

**Google Registration:**
```bash
Access: index.php?page=register-buyer
Method: Google (Click button)
OAuth: Requires Google OAuth setup
```

### Login

**Standard Login:**
```bash
Access: index.php?page=login
Identifier: Email OR Phone
Password: User password
Remember Me: Optional cookie
2FA: If enabled, code required
```

**Social Login:**
```bash
Google: Click Google button
Facebook: Click Facebook button
```

### Password Reset

**Request Reset:**
```bash
Access: index.php?page=forgot-password
Email: Enter email
Action: Reset link sent to email
```

**Complete Reset:**
```bash
Access: index.php?page=reset-password?token=XXX
New Password: Enter new password
Confirm: Confirm new password
```

### Verification

**Email Verification:**
```bash
Token: From email link
Access: index.php?page=verify-email?token=XXX
Result: Email verified, can login
```

**Phone Verification:**
```bash
Code: 6-digit SMS code
Access: index.php?page=verify-phone
Attempts: Maximum 3
Resend: Available after 10 minutes
```

## Security Features

### Password Security
- Bcrypt hashing (PHP password_hash)
- Minimum 8 characters
- One-way encryption
- Last password change tracking

### Token Security
- Cryptographically secure random tokens
- Time-limited expiry
- Single use tokens
- Database cleanup of expired tokens

### 2FA Security
- 6-digit random codes
- 5-minute expiry
- One-time use
- Email delivery

### Login Security
- Failed login logging
- IP address tracking
- User agent tracking
- Attempt rate limiting (todo)
- Account lockout (todo)

### Social Login Security
- OAuth 2.0 protocol
- Provider ID validation
- CSRF protection
- State parameter validation

## TODO: Integration

### Email Service
Currently logs to error log. Integrate:
- PHPMailer
- SendGrid
- Amazon SES
- Mailgun

### SMS Service
Currently logs to error log. Integrate:
- Twilio
- AWS SNS
- TextLocal
- Africa's Talking

### Social OAuth
Currently UI ready. Integrate:
- Google OAuth 2.0 credentials
- Facebook OAuth credentials
- OAuth callback handlers
- State validation

### Additional Features
- Remember me database storage
- Password strength meter
- Account lockout after failed attempts
- Email change verification
- Phone number change verification
- Session management improvements

## Testing

### Test Cases
1. Email registration with verification
2. Phone registration with SMS code
3. Login with email and phone
4. Forgot password flow
5. Email verification flow
6. Phone verification flow
7. 2FA login flow
8. Social login (when integrated)

### Manual Testing
```bash
# Test Email Registration
1. Go to: http://localhost/HND_DEFENSE_PROJECT/index.php?page=register-buyer
2. Fill in form
3. Check email for verification link
4. Click link to verify
5. Attempt login

# Test Login
1. Go to: http://localhost/HND_DEFENSE_PROJECT/index.php?page=login
2. Enter credentials
3. If 2FA enabled, enter code
4. Should redirect to dashboard

# Test Password Reset
1. Go to: http://localhost/HND_DEFENSE_PROJECT/index.php?page=forgot-password
2. Enter email
3. Check email for reset link
4. Click link and reset password
5. Login with new password
```

## Configuration

### Environment Variables Needed
```env
# Email Configuration
SMTP_HOST=your_smtp_host
SMTP_PORT=587
SMTP_USER=your_email
SMTP_PASS=your_password

# SMS Configuration
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=your_number

# OAuth Configuration
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_secret
FACEBOOK_APP_ID=your_app_id
FACEBOOK_APP_SECRET=your_secret
```

## Files Created

### Core Files
- `includes/auth-functions.php` - Authentication logic
- `database/auth_schema_updates.sql` - Database updates

### Pages
- `pages/auth/register-buyer.php` - Buyer registration
- `pages/auth/login.php` - Enhanced login
- `pages/auth/forgot-password.php` - Password reset request
- `pages/auth/reset-password.php` - Password reset form
- `pages/auth/verify-email.php` - Email verification
- `pages/auth/verify-phone.php` - Phone verification

### Router Updates
- Updated `index.php` to handle auth routes

## Support

For issues or questions:
- Email: support@spareplug.cm
- Developer: Ekom Mishael Sema
- Database Issues: Check auth_schema_updates.sql
- Integration Issues: Check AUTH_SYSTEM.md





