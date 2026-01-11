<?php
require_once 'config/database.php';

// Set timezone to match what the app likely uses (or default)
date_default_timezone_set('Africa/Douala'); // Assuming Cameroon based on context, or UTC

echo "Current Server Time: " . date('Y-m-d H:i:s') . "\n";
echo "Current Timezone: " . date_default_timezone_get() . "\n\n";

echo "--- Latest 5 Password Resets ---\n";
$resets = $db->fetchAll("SELECT * FROM password_resets ORDER BY created_at DESC LIMIT 5");

foreach ($resets as $r) {
    echo "ID: " . $r['id'] . "\n";
    echo "User ID: " . $r['user_id'] . "\n";
    echo "Token: " . $r['token'] . "\n";
    echo "Abstract Token (first 10 chars): " . substr($r['token'], 0, 10) . "...\n";
    echo "Expires At: " . $r['expires_at'] . "\n";
    echo "Used: " . $r['used'] . "\n";
    
    $expires = strtotime($r['expires_at']);
    $now = time();
    $diff = $expires - $now;
    
    echo "Status: ";
    if ($r['used']) {
        echo "[USED]";
    } elseif ($diff < 0) {
        echo "[EXPIRED " . abs($diff) . "s ago]";
    } else {
        echo "[VALID (expires in {$diff}s)]";
    }
    echo "\n----------------\n";
}
?>
