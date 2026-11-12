<?php
require_once __DIR__ . '/database.php';

$db = new Database();

try {
    $db->query("
    CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(50) UNIQUE NOT NULL,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Settings table created!<br>";
    
    // Add default payment settings if empty
    $count = $db->fetch("SELECT COUNT(*) as cnt FROM settings")['cnt'];
    if ($count == 0) {
        $db->query("INSERT INTO settings (setting_key, setting_value) VALUES 
            ('momo_number', '+237 6XX XXX XXX'),
            ('momo_name', 'Your Shop Name'),
            ('orange_number', '+237 6XX XXX XXX'),
            ('orange_name', 'Your Shop Name'),
            ('company_name', 'SparePlug'),
            ('company_phone', '+237 6XX XXX XXX')");
        echo "Default settings added!";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}