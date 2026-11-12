<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/database.php';

$db = new Database();

echo "<pre style='background:#1e1e1e;color:#00ff00;padding:20px;border-radius:10px;font-family:monospace;'>";
echo "=== DATABASE DIAGNOSTICS ===\n\n";

// Check users table
echo "=== USERS TABLE ===\n";
try {
    $cols = $db->fetchAll("DESCRIBE users");
    foreach ($cols as $c) {
        echo $c['Field'] . " (" . $c['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check orders table
echo "\n=== ORDERS TABLE ===\n";
try {
    $cols = $db->fetchAll("DESCRIBE orders");
    foreach ($cols as $c) {
        echo $c['Field'] . " (" . $c['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check settings
echo "\n=== SETTINGS ===\n";
try {
    $settings = $db->fetchAll("SELECT * FROM settings");
    foreach ($settings as $s) {
        echo $s['setting_key'] . " = " . $s['setting_value'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check sample order
echo "\n=== SAMPLE ORDER ===\n";
try {
    $order = $db->fetch("SELECT * FROM orders LIMIT 1");
    if ($order) {
        echo "Order #" . $order['order_number'] . "\n";
        echo "Payment: " . $order['payment_status'] . "\n";
        echo "Fulfillment: " . $order['fulfillment_status'] . "\n";
        echo "Escrow: " . $order['escrow_status'] . "\n";
    } else {
        echo "No orders found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check users
echo "\n=== USERS ===\n";
try {
    $users = $db->fetchAll("SELECT id, email, user_type FROM users LIMIT 5");
    foreach ($users as $u) {
        echo "ID: " . $u['id'] . " - " . $u['email'] . " (" . $u['user_type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== END ===\n";
echo "</pre>";