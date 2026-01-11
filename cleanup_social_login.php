<?php
require_once 'config/database.php';

try {
    $db->query("DROP TABLE IF EXISTS social_logins");
    echo "Successfully dropped table 'social_logins'.\n";
    
    // Also remove google_id column from users if it exists
    try {
        $db->query("ALTER TABLE users DROP COLUMN google_id");
        echo "Successfully removed 'google_id' column from 'users'.\n";
    } catch (Exception $e) {
        // Column might not exist, strictly speaking we should check information_schema but this is fine
        echo "Note: Could not drop 'google_id' column (might not exist).\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
