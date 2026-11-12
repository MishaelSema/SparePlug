<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database.php';

$db = new Database();

echo "<pre style='background:#1e1e1e;color:#00ff00;padding:20px;border-radius:10px;font-family:monospace;'>";
echo "=== DATABASE FIXES ===\n\n";

// Fix 1: Settings table
try {
    $db->query("
    CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(50) UNIQUE NOT NULL,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "✓ settings table ready\n";
} catch (Exception $e) {
    echo "✗ settings: " . $e->getMessage() . "\n";
}

// Add default settings
try {
    $db->query("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES 
        ('momo_number', '+237 691324233'),
        ('momo_name', 'SparePlug Shop'),
        ('orange_number', '+237 691324233'),
        ('orange_name', 'SparePlug Shop'),
        ('company_name', 'SparePlug'),
        ('company_phone', '+237 691324233')");
    echo "✓ Payment settings added\n";
} catch (Exception $e) {
    echo "✗ settings data: " . $e->getMessage() . "\n";
}

// Check orders table structure
try {
    $columns = $db->fetchAll("DESCRIBE orders");
    $colNames = array_column($columns, 'Field');
    echo "\nOrders table columns: " . implode(', ', $colNames) . "\n";
} catch (Exception $e) {
    echo "✗ orders check: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
echo "</pre>";
echo "<p style='text-align:center;'><a href='index.php?page=admin-settings' style='background:#dc2626;color:white;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;'>Go to Payment Settings</a></p>";