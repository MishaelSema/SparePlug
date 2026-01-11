<?php
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = new Database();
    echo "--- Tables ---\n";
    $tables = $db->fetchAll("SHOW TABLES");
    foreach ($tables as $table) {
        $name = array_values($table)[0];
        echo $name . "\n";
        echo "Columns for $name:\n";
        $columns = $db->fetchAll("DESCRIBE $name");
        foreach ($columns as $col) {
            echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
