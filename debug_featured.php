<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    
    echo "--- Products Table Structure ---\n";
    $columns = $db->fetchAll("DESCRIBE products");
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    echo "\n--- Featured Products Count ---\n";
    $count = $db->fetch("SELECT COUNT(*) as count FROM products WHERE is_featured = 1 AND is_active = 1");
    echo "Found " . $count['count'] . " featured products.\n";
    
    echo "\n--- All Products Count ---\n";
    $allCount = $db->fetch("SELECT COUNT(*) as count FROM products");
    echo "Found " . $allCount['count'] . " total products.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
