<!DOCTYPE html>
<html>
<head>
    <title>Database Schema Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .skip { color: orange; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔧 Database Schema Fix</h1>
    <p>Adding missing authentication columns to users table...</p>
    
    <?php
    require_once 'config/database.php';
    
    $queries = [
        "ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL AFTER verification_token",
        "ALTER TABLE users ADD COLUMN email_verification_expires TIMESTAMP NULL AFTER email_verification_token",
        "ALTER TABLE users ADD COLUMN phone_verified BOOLEAN DEFAULT FALSE AFTER phone",
        "ALTER TABLE users ADD COLUMN phone_verification_code VARCHAR(10) NULL AFTER phone_verified",
        "ALTER TABLE users ADD COLUMN phone_verification_expires TIMESTAMP NULL AFTER phone_verification_code",
        "ALTER TABLE users ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE AFTER is_verified",
        "ALTER TABLE users ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER two_factor_enabled",
        "ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL AFTER two_factor_secret",
        "ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(255) NULL AFTER google_id",
        "ALTER TABLE users ADD COLUMN password_reset_expires TIMESTAMP NULL AFTER password_reset_token",
        "ALTER TABLE users ADD COLUMN last_password_change TIMESTAMP NULL AFTER password_reset_expires"
    ];
    
    echo "<pre>";
    foreach ($queries as $query) {
        try {
            $db->query($query);
            echo "<span class='success'>✓ Added column: " . substr($query, 25, 50) . "...</span>\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "<span class='skip'>- Column already exists: " . substr($query, 25, 40) . "...</span>\n";
            } else {
                echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span>\n";
            }
        }
    }
    echo "</pre>";
    
    echo "<h2 class='success'>✅ Schema update complete!</h2>";
    echo "<p>You can now try registration again. <a href='index.php?page=vendor-signup'>Go to Vendor Signup</a></p>";
    ?>
</body>
</html>
