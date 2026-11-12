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

$user = new User();
if (!$user->isVendor()) {
    $response['message'] = 'Access denied';
    echo json_encode($response);
    exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
$newStatus = $_POST['status'] ?? '';

if (!$orderId || !$newStatus) {
    $response['message'] = 'Invalid request';
    echo json_encode($response);
    exit;
}

$vendorProfile = $user->getVendorProfile();
$db = new Database();

// Verify order belongs to this vendor
$order = $db->fetch("SELECT * FROM orders WHERE id = ? AND vendor_id = ?", [$orderId, $vendorProfile['id']]);

if (!$order) {
    $response['message'] = 'Order not found';
    echo json_encode($response);
    exit;
}

$orderClass = new Order();

switch($newStatus) {
    case 'processing':
        $orderClass->updateStatus($orderId, 'fulfillment', 'processing', 'Order accepted by vendor');
        break;
    case 'ready_for_pickup':
        $orderClass->updateStatus($orderId, 'fulfillment', 'ready_for_pickup', 'Order ready for pickup');
        Notification::send($order['buyer_id'], 'Your order is ready for pickup!', 'success');
        break;
    case 'delivered':
        $orderClass->updateStatus($orderId, 'fulfillment', 'delivered', 'Order delivered');
        break;
    default:
        $response['message'] = 'Invalid status';
        echo json_encode($response);
        exit;
}

$response['success'] = true;
$response['message'] = 'Order status updated';

echo json_encode($response);
