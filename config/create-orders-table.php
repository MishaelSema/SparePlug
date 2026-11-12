<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database.php';

$db = new Database();

echo "<pre style='background:#f0f0f0;padding:20px;border-radius:10px;'>";
echo "=== Creating Database Tables ===\n\n";

// Create orders table
try {
    $db->query("
    CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_number VARCHAR(50) UNIQUE NOT NULL,
        buyer_id INT NOT NULL,
        vendor_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        unit_price DECIMAL(10,2) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        commission_rate DECIMAL(5,2) DEFAULT 10.00,
        commission_amount DECIMAL(10,2) DEFAULT 0,
        seller_amount DECIMAL(10,2) DEFAULT 0,
        payment_status VARCHAR(20) DEFAULT 'pending',
        payment_method VARCHAR(20) DEFAULT 'momo',
        payment_reference VARCHAR(100),
        payment_proof VARCHAR(255),
        paid_at TIMESTAMP NULL,
        fulfillment_status VARCHAR(20) DEFAULT 'pending',
        delivery_method VARCHAR(20) DEFAULT 'pickup',
        delivery_address TEXT,
        delivery_notes TEXT,
        shipped_at TIMESTAMP NULL,
        delivered_at TIMESTAMP NULL,
        escrow_status VARCHAR(20) DEFAULT 'held',
        released_at TIMESTAMP NULL,
        buyer_confirmed TINYINT(1) DEFAULT 0,
        confirmed_at TIMESTAMP NULL,
        dispute_reason TEXT,
        dispute_status VARCHAR(20) DEFAULT 'none',
        resolved_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "✓ Orders table created/verified\n";
} catch (Exception $e) {
    echo "✗ Orders table error: " . $e->getMessage() . "\n";
}

// Create commission_rates table
try {
    $db->query("
    CREATE TABLE IF NOT EXISTS commission_rates (
        id INT PRIMARY KEY AUTO_INCREMENT,
        plan_type VARCHAR(20) NOT NULL,
        rate DECIMAL(5,2) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $db->query("INSERT IGNORE INTO commission_rates (plan_type, rate, is_active) VALUES ('free', 15, 1), ('basic', 10, 1), ('premium', 5, 1)");
    echo "✓ Commission rates table created/verified\n";
} catch (Exception $e) {
    echo "✗ Commission rates error: " . $e->getMessage() . "\n";
}

// Create order_status_history table
try {
    $db->query("
    CREATE TABLE IF NOT EXISTS order_status_history (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        status_type VARCHAR(20) NOT NULL,
        old_status VARCHAR(50),
        new_status VARCHAR(50) NOT NULL,
        note TEXT,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Order status history table created/verified\n";
} catch (Exception $e) {
    echo "✗ Order status history error: " . $e->getMessage() . "\n";
}

// Verify tables exist
echo "\n=== Verifying Tables ===\n";
$tables = ['orders', 'commission_rates', 'order_status_history'];
foreach ($tables as $table) {
    try {
        $count = $db->fetch("SELECT COUNT(*) as cnt FROM $table")['cnt'];
        echo "✓ $table exists ($count rows)\n";
    } catch (Exception $e) {
        echo "✗ $table: " . $e->getMessage() . "\n";
    }
}

echo "\n=== DONE ===\n";
echo "</pre>";
echo "<p style='text-align:center;'><a href='index.php' style='background:#dc2626;color:white;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;'>Go to Homepage</a></p>";