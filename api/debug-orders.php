<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';

$db = new Database();

echo "<pre style='background:#1e1e1e;color:#00ff00;padding:20px;border-radius:10px;font-family:monospace;'>";
echo "=== Testing Database ===\n\n";

// Check tables
$tables = ['orders', 'commission_rates', 'order_status_history'];
foreach ($tables as $table) {
    try {
        $db->query("SELECT 1 FROM $table LIMIT 1");
        echo "✓ $table exists\n";
    } catch (Exception $e) {
        echo "✗ $table: " . $e->getMessage() . "\n";
    }
}

// Try inserting a test order
echo "\n=== Testing Order Insert ===\n";
try {
    $testOrder = [
        'order_number' => 'TEST-' . time(),
        'buyer_id' => 1,
        'vendor_id' => 1,
        'product_id' => 1,
        'quantity' => 1,
        'unit_price' => 1000,
        'total_amount' => 1000,
        'commission_rate' => 10,
        'commission_amount' => 100,
        'seller_amount' => 900,
        'payment_status' => 'pending',
        'payment_method' => 'momo',
        'fulfillment_status' => 'pending',
        'delivery_method' => 'pickup',
        'escrow_status' => 'held'
    ];
    
    $result = $db->insert('orders', $testOrder);
    echo "✓ Test order created! ID: $result\n";
    
    // Delete test order
    $db->query("DELETE FROM orders WHERE id = ?", [$result]);
    echo "✓ Test order cleaned up\n";
    
} catch (Exception $e) {
    echo "✗ Insert error: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
echo "</pre>";