<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$makeId = isset($_GET['make_id']) ? (int)$_GET['make_id'] : 0;

if ($makeId <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $models = $db->fetchAll(
        "SELECT id, name FROM vehicle_models WHERE make_id = ? AND is_active = 1 ORDER BY name",
        [$makeId]
    );
    
    echo json_encode($models);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch models']);
}
?>




