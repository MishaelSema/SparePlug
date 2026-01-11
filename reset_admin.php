<?php
require_once 'config/database.php';

echo "<h1>Admin Password Reset</h1>";

// Check if admin exists
$admin = $db->fetch("SELECT * FROM users WHERE email = 'admin@spareplug.cm'");

if (!$admin) {
    echo "<p style='color: orange;'>Admin user not found. Creating new admin...</p>";
    
    // Create admin user
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    try {
        $db->insert('users', [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@spareplug.cm',
            'password' => $password,
            'user_type' => 'admin',
            'status' => 'active',
            'is_verified' => 1
        ]);
        
        echo "<p style='color: green;'>✓ Admin user created successfully!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: blue;'>Admin user exists. Resetting password...</p>";
    
    // Reset password
    $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    try {
        $db->update('users', 
            ['password' => $newPassword], 
            'email = ?', 
            ['admin@spareplug.cm']
        );
        
        echo "<p style='color: green;'>✓ Admin password reset successfully!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<h2>Login Credentials:</h2>";
echo "<p><strong>Email:</strong> admin@spareplug.cm</p>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><a href='index.php?page=login'>Go to Login Page</a></p>";

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ IMPORTANT: Delete this file (reset_admin.php) immediately after use!</strong></p>";
?>
