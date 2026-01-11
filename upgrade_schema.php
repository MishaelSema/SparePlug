<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    
    // 1. Add image_url to categories if it doesn't exist
    $categoriesCols = $db->fetchAll("SHOW COLUMNS FROM categories LIKE 'image_url'");
    if (empty($categoriesCols)) {
        $db->query("ALTER TABLE categories ADD COLUMN image_url VARCHAR(255) AFTER icon");
        echo "Added image_url column to categories.\n";
    }
    
    // 2. Create subscription_plans table
    $db->query("CREATE TABLE IF NOT EXISTS subscription_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        billing_cycle ENUM('monthly', 'annual') DEFAULT 'monthly',
        features JSON,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Created subscription_plans table.\n";
    
    // Seed default plans if empty
    $plansCount = $db->fetch("SELECT COUNT(*) as count FROM subscription_plans")['count'];
    if ($plansCount == 0) {
        $plans = [
            ['name' => 'Free', 'price' => 0, 'billing_cycle' => 'monthly', 'features' => json_encode(['10 products', 'Basic support'])],
            ['name' => 'Basic', 'price' => 5000, 'billing_cycle' => 'monthly', 'features' => json_encode(['50 products', 'Priority support', 'Verified badge'])],
            ['name' => 'Premium', 'price' => 15000, 'billing_cycle' => 'monthly', 'features' => json_encode(['Unlimited products', 'Top of search results', 'WhatsApp direct link', 'Verified badge'])]
        ];
        foreach ($plans as $plan) {
            $db->insert('subscription_plans', $plan);
        }
        echo "Seeded default subscription plans.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
