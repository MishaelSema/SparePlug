<?php
require_once __DIR__ . '/database.php';

$db = new Database();

$adminEmail = 'admin@spareplug.cm';
$adminPassword = 'admin123';
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

$existingAdmin = $db->fetch("SELECT id FROM users WHERE email = ?", [$adminEmail]);

if ($existingAdmin) {
    $db->update(
        "users", 
        ['password' => $hashedPassword, 'user_type' => 'admin', 'status' => 'active'],
        "email = ?",
        [$adminEmail]
    );
    echo "Admin user updated successfully!\n";
} else {
    $db->insert('users', [
        'email' => $adminEmail,
        'password' => $hashedPassword,
        'first_name' => 'Admin',
        'last_name' => 'User',
        'user_type' => 'admin',
        'status' => 'active'
    ]);
    echo "Admin user created successfully!\n";
}

echo "Email: $adminEmail\n";
echo "Password: $adminPassword\n";