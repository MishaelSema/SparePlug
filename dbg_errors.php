<?php
/**
 * SparePlug Debugging Script
 * Run this by visiting http://localhost/HND_DEFENSE_PROJECT/dbg_errors.php
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>SparePlug Debugger</h1>";
echo "<pre>";

echo "Checking config/config.php... ";
if (file_exists('config/config.php')) {
    echo "EXISTS\n";
    try {
        require_once 'config/config.php';
        echo "config/config.php included successfully.\n";
    } catch (Throwable $e) {
        echo "ERROR including config/config.php: " . $e->getMessage() . "\n";
    }
} else {
    echo "MISSING! You should copy config/config.php.example to config/config.php\n";
}

echo "\nChecking config/database.php... ";
if (file_exists('config/database.php')) {
    echo "EXISTS\n";
    try {
        require_once 'config/database.php';
        echo "config/database.php included successfully.\n";
        
        // Test database connection
        if (isset($db)) {
            echo "Database object exists. Testing connection...\n";
            $conn = $db->getConnection();
            echo "Successfully connected to the database: " . DB_NAME . "\n";
        }
    } catch (Throwable $e) {
        echo "ERROR including database.php or connecting: " . $e->getMessage() . "\n";
    }
} else {
    echo "MISSING!\n";
}

echo "\nChecking includes/functions.php... ";
if (file_exists('includes/functions.php')) {
    echo "EXISTS\n";
    try {
        require_once 'includes/functions.php';
        echo "includes/functions.php included successfully.\n";
    } catch (Throwable $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "MISSING!\n";
}

echo "</pre>";
?>
