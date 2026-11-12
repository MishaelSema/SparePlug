<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();

try {
    $result = $db->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20)");
    echo "Added phone column to users table\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "Phone column already exists\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}