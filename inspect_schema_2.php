<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    foreach(['categories', 'subscriptions'] as $t) {
        echo "Table: $t\n";
        $columns = $db->fetchAll("DESCRIBE $t");
        foreach ($columns as $col) {
            echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
