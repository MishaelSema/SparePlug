<?php
require_once 'config/database.php';

echo "<h2>Timezone Diagnostics</h2>";

// PHP Time
$phpTime = date('Y-m-d H:i:s');
$phpTz = date_default_timezone_get();
echo "<strong>PHP Time:</strong> $phpTime <br>";
echo "<strong>PHP Timezone:</strong> $phpTz <br>";

// MySQL Time
try {
    $row = $db->fetch("SELECT NOW() as mysql_time, @@global.time_zone as global_tz, @@session.time_zone as session_tz");
    echo "<strong>MySQL Time (NOW()):</strong> " . $row['mysql_time'] . "<br>";
    echo "<strong>MySQL Global TZ:</strong> " . $row['global_tz'] . "<br>";
    echo "<strong>MySQL Session TZ:</strong> " . $row['session_tz'] . "<br>";
    
    $diff = strtotime($phpTime) - strtotime($row['mysql_time']);
    echo "<br><strong>Difference (PHP - MySQL):</strong> " . $diff . " seconds<br>";
    
    if (abs($diff) > 60) {
        echo "<h3 style='color:red'>WARNING: Significant time difference detected!</h3>";
    } else {
        echo "<h3 style='color:green'>Time synchronization looks good.</h3>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

echo "<hr>";
echo "<h3>Latest 5 Reset Tokens</h3>";
$resets = $db->fetchAll("SELECT * FROM password_resets ORDER BY created_at DESC LIMIT 5");

echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Token (Prefix)</th><th>Created</th><th>Expires</th><th>Used</th><th>Status (PHP Calc)</th></tr>";
foreach ($resets as $r) {
    $tokenShort = substr($r['token'], 0, 10) . "...";
    $status = $r['used'] ? "USED" : ((strtotime($r['expires_at']) < time()) ? "EXPIRED" : "VALID");
    echo "<tr>
        <td>{$r['id']}</td>
        <td>{$tokenShort}</td>
        <td>{$r['created_at']}</td>
        <td>{$r['expires_at']}</td>
        <td>{$r['used']}</td>
        <td>{$status}</td>
    </tr>";
}
echo "</table>";
?>
