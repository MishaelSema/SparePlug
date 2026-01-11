<?php
require_once 'config/database.php';

try {
    $db->query("ALTER TABLE users ADD COLUMN last_password_change TIMESTAMP NULL DEFAULT NULL");
    echo "Successfully added 'last_password_change' column to 'users' table.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), "Duplicate column") !== false) {
        echo "Column 'last_password_change' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
