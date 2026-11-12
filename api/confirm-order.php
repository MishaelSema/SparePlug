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

if (!$orderId) {
    $response['message'] = 'Invalid order';
    echo json_encode($response);
    exit;
}

$order = new Order();
$result = $order->confirmDelivery($orderId, $_SESSION['user_id']);

if ($result) {
    $response['success'] = true;
    $response['message'] = 'Order confirmed successfully';
} else {
    $response['message'] = 'Failed to confirm order';
}

echo json_encode($response);
