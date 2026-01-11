<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    $db->query("UPDATE products SET is_featured = 1 LIMIT 2");
    echo "Successfully marked 2 products as featured.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
