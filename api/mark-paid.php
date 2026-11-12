<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
ob_clean();

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please login';
    echo json_encode($response);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = (int)($data['order_id'] ?? 0);

if (!$orderId) {
    $response['message'] = 'Invalid order';
    echo json_encode($response);
    exit;
}

try {
    $order = new Order();
    $result = $order->markAsPaid($orderId, $_SESSION['user_id']);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Payment confirmed!';
    } else {
        $response['message'] = 'Failed to confirm payment. Order may not exist or already paid.';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
