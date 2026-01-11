-- Add missing authentication columns to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255) NULL AFTER verification_token,
ADD COLUMN IF NOT EXISTS email_verification_expires TIMESTAMP NULL AFTER email_verification_token,
ADD COLUMN IF NOT EXISTS phone_verified BOOLEAN DEFAULT FALSE AFTER phone,
ADD COLUMN IF NOT EXISTS phone_verification_code VARCHAR(10) NULL AFTER phone_verified,
ADD COLUMN IF NOT EXISTS phone_verification_expires TIMESTAMP NULL AFTER phone_verification_code,
ADD COLUMN IF NOT EXISTS two_factor_enabled BOOLEAN DEFAULT FALSE AFTER is_verified,
ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled,
ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL AFTER two_factor_secret,
ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) NULL AFTER google_id,
ADD COLUMN IF NOT EXISTS password_reset_expires TIMESTAMP NULL AFTER password_reset_token,
ADD COLUMN IF NOT EXISTS last_password_change TIMESTAMP NULL AFTER password_reset_expires;
