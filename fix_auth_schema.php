<?php
require_once 'config/database.php';

echo "Adding missing authentication columns to users table...\n\n";

$queries = [
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_token VARCHAR(255) NULL AFTER verification_token",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS email_verification_expires TIMESTAMP NULL AFTER email_verification_token",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_verified BOOLEAN DEFAULT FALSE AFTER phone",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_verification_code VARCHAR(10) NULL AFTER phone_verified",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_verification_expires TIMESTAMP NULL AFTER phone_verification_code",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS two_factor_enabled BOOLEAN DEFAULT FALSE AFTER is_verified",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS google_id VARCHAR(255) NULL AFTER two_factor_secret",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) NULL AFTER google_id",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_expires TIMESTAMP NULL AFTER password_reset_token",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_password_change TIMESTAMP NULL AFTER password_reset_expires"
];

foreach ($queries as $query) {
    try {
        $db->query($query);
        echo "✓ Executed: " . substr($query, 0, 80) . "...\n";
    } catch (Exception $e) {
        // Column might already exist, that's okay
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "- Skipped (already exists): " . substr($query, 0, 60) . "...\n";
        } else {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n✅ Schema update complete!\n";
echo "You can now try registration again.\n";
?>
