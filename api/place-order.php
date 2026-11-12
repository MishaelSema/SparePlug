<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');
ob_clean();

$response = ['success' => false, 'message' => ''];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

$user = new User();
$product = new Product();
$order = new Order();
$db = new Database();

// Check if guest checkout (no login required)
$isGuest = !isset($_SESSION['user_id']);
$buyerId = $_SESSION['user_id'] ?? null;

// If guest, get buyer info from form
$guestName = '';
$guestEmail = '';
$guestPhone = '';

if ($isGuest) {
    $guestName = trim($_POST['guest_name'] ?? '');
    $guestEmail = trim($_POST['guest_email'] ?? '');
    $guestPhone = trim($_POST['guest_phone'] ?? '');
    
    if (empty($guestName) || empty($guestEmail) || empty($guestPhone)) {
        $response['message'] = 'Please provide your name, email and phone to place an order.';
        echo json_encode($response);
        exit;
    }
    
    // Check if user exists, if not create one
    $existingUser = $db->fetch("SELECT id FROM users WHERE email = ?", [$guestEmail]);
    if ($existingUser) {
        $buyerId = $existingUser['id'];
    } else {
        // Create temporary buyer account
        $buyerId = $db->insert('users', [
            'email' => $guestEmail,
            'password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT),
            'first_name' => explode(' ', $guestName)[0],
            'last_name' => substr(strstr($guestName, ' '), 1) ?: '',
            'phone' => $guestPhone,
            'user_type' => 'buyer',
            'status' => 'active'
        ]);
    }
}

// Get form data
$productId = (int)($_POST['product_id'] ?? 0);
$vendorId = (int)($_POST['vendor_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);
$deliveryMethod = $_POST['delivery_method'] ?? 'pickup';
$paymentMethod = $_POST['payment_method'] ?? 'momo';
$notes = $_POST['notes'] ?? '';

// Validate
if (!$productId || !$vendorId) {
    $response['message'] = 'Invalid product or vendor';
    echo json_encode($response);
    exit;
}

// Get product details
$productData = $product->getById($productId);
if (!$productData) {
    $response['message'] = 'Product not found';
    echo json_encode($response);
    exit;
}

// Check if user is the vendor
if ($user->isVendor()) {
    $vendorProfile = $user->getVendorProfile();
    if ($vendorProfile && $vendorProfile['id'] == $vendorId) {
        $response['message'] = 'You cannot buy your own products';
        echo json_encode($response);
        exit;
    }
}

$totalAmount = $productData['price'] * $quantity;

// Create order
$orderData = [
    'buyer_id' => $buyerId,
    'vendor_id' => $vendorId,
    'product_id' => $productId,
    'quantity' => $quantity,
    'unit_price' => $productData['price'],
    'total_amount' => $totalAmount,
    'payment_method' => $paymentMethod,
    'delivery_method' => $deliveryMethod,
    'delivery_notes' => $notes
];

try {
    $orderId = $order->create($orderData);
    
    if (!$orderId) {
        $response['message'] = 'Order creation failed. Contact support.';
    } else {
        $orderDetails = $order->getById($orderId);
        
        if (!$orderDetails) {
            $response['message'] = 'Order created but failed to load details. Order ID: ' . $orderId;
        } else {
            $response['success'] = true;
            $response['order_number'] = $orderDetails['order_number'];
            $response['order_id'] = $orderId;
            $response['message'] = 'Order placed successfully!';
            $response['payment_method'] = $paymentMethod;
            
            switch($paymentMethod) {
                case 'momo':
                    $response['payment_instructions'] = "Transfer " . number_format($totalAmount) . " XAF via MTN MoMo.";
                    break;
                case 'orange_money':
                    $response['payment_instructions'] = "Transfer " . number_format($totalAmount) . " XAF via Orange Money.";
                    break;
                case 'cash':
                    $response['payment_instructions'] = "Pay " . number_format($totalAmount) . " XAF on pickup.";
                    break;
            }
        }
    }
} catch (Exception $e) {
    $msg = $e->getMessage();
    $response['message'] = "Error: $msg";
} catch (Error $e) {
    $response['message'] = "Fatal Error: " . $e->getMessage();
}

echo json_encode($response);
