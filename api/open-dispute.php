<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please login';
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? 0;
$reason = $data['reason'] ?? '';

if (!$orderId || !$reason) {
    $response['message'] = 'Invalid request';
    echo json_encode($response);
    exit;
}

$order = new Order();
$result = $order->openDispute($orderId, $_SESSION['user_id'], $reason);

if ($result) {
    $response['success'] = true;
    $response['message'] = 'Dispute opened successfully';
} else {
    $response['message'] = 'Failed to open dispute';
}

echo json_encode($response);
