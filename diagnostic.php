<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 sparePlug Diagnostics</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>";

// Test 1: Database Connection
echo "<h2>1. Database Connection</h2>";
try {
    require_once 'config/config.php';
    require_once 'config/database.php';
    
    $db = new Database();
    echo "<p class='success'>✓ Database connected successfully!</p>";
    
    // Test query
    $result = $db->fetch("SELECT COUNT(*) as count FROM users");
    echo "<p class='success'>✓ Found " . $result['count'] . " users in database</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Database Error: " . $e->getMessage() . "</p>";
}

// Test 2: Check if admin exists
echo "<h2>2. Admin User Check</h2>";
try {
    $admin = $db->fetch("SELECT id, email, user_type, status FROM users WHERE email = 'admin@spareplug.cm'");
    if ($admin) {
        echo "<p class='success'>✓ Admin user found</p>";
        echo "<pre>" . print_r($admin, true) . "</pre>";
    } else {
        echo "<p class='error'>✗ Admin user NOT found</p>";
        echo "<p class='warning'>Run reset_admin.php to create admin user</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test 3: Session Check
echo "<h2>3. Session Configuration</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p class='success'>✓ Sessions are working</p>";
    echo "<p>Session ID: " . session_id() . "</p>";
} else {
    echo "<p class='error'>✗ Sessions NOT working</p>";
}

// Test 4: File Permissions
echo "<h2>4. Critical Files Check</h2>";
$files = [
    'index.php',
    'config/config.php',
    'config/database.php',
    'includes/functions.php',
    'pages/auth/login.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ $file exists</p>";
    } else {
        echo "<p class='error'>✗ $file MISSING</p>";
    }
}

// Test 5: Login Test
echo "<h2>5. Test Login Function</h2>";
try {
    require_once 'includes/functions.php';
    $user = new User();
    
    // Try to login
    $testLogin = $user->login('admin@spareplug.cm', 'admin123');
    
    if ($testLogin) {
        echo "<p class='success'>✓ Login function works! Admin can login.</p>";
        echo "<pre>" . print_r($testLogin, true) . "</pre>";
    } else {
        echo "<p class='error'>✗ Login failed - Invalid credentials</p>";
        echo "<p class='warning'>Password hash might be incorrect. Run reset_admin.php</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Login Error: " . $e->getMessage() . "</p>";
}

// Test 6: Check tables
echo "<h2>6. Database Tables</h2>";
try {
    $tables = $db->fetchAll("SHOW TABLES");
    echo "<p class='success'>✓ Found " . count($tables) . " tables:</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        echo "<li>$tableName</li>";
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>If admin user not found: <a href='reset_admin.php'>Run reset_admin.php</a></li>";
echo "<li>If login works here but not on login page: Check browser cookies/cache</li>";
echo "<li>Try logging in: <a href='index.php?page=login'>Go to Login Page</a></li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color:red;'><strong>⚠️ Delete this file (diagnostic.php) after troubleshooting!</strong></p>";
?>
