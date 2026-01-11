<?php
require_once 'config/database.php';
$db = new Database();

try {
    // Add columns to vendor_profiles if they don't exist
    $db->query("ALTER TABLE vendor_profiles ADD COLUMN IF NOT EXISTS logo_url VARCHAR(255) DEFAULT NULL");
    $db->query("ALTER TABLE vendor_profiles ADD COLUMN IF NOT EXISTS cover_url VARCHAR(255) DEFAULT NULL");

    // Create notifications table
    $db->query("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "Migration successful!";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}
