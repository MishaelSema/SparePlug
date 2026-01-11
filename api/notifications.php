<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$notif = new Notification();

if ($action === 'mark_read' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $notif->markAsRead($id);
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
