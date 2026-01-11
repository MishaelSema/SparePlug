<?php
require_once '../config/database.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
$vendorId = isset($data['vendor_id']) ? (int)$data['vendor_id'] : 0;

if (!$productId || !$vendorId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    // 1. Increment inquiry count on product
    $db->query("UPDATE products SET inquiries_count = inquiries_count + 1 WHERE id = ?", [$productId]);

    // 2. If user is logged in, record the inquiry record
    session_start();
    if (isset($_SESSION['user_id'])) {
        $buyerId = $_SESSION['user_id'];
        
        // Prevent duplicate recent inquiries to avoid spamming the table (optional check)
        // For now, simple insert
        $db->insert('product_inquiries', [
            'product_id' => $productId,
            'buyer_id' => $buyerId,
            'message' => 'User initiated WhatsApp contact',
            'contact_method' => 'whatsapp',
            'status' => 'pending'
        ]);
        
        // Also update analytics for vendor
        // (Assuming analytics table has structure for this, otherwise skip)
        // Metric: 'inquiry_whatsapp'
        $today = date('Y-m-d');
        // Check if analytics row exists
        $analytics = $db->fetch(
            "SELECT * FROM analytics WHERE vendor_id = ? AND metric_type = 'total_inquiries' AND date_recorded = ?",
            [$vendorId, $today]
        );
        
        if ($analytics) {
            $db->query(
                "UPDATE analytics SET metric_value = metric_value + 1 WHERE id = ?", 
                [$analytics['id']]
            );
        } else {
            $db->insert('analytics', [
                'vendor_id' => $vendorId,
                'metric_type' => 'total_inquiries',
                'metric_value' => 1,
                'date_recorded' => $today,
                'metadata' => json_encode(['source' => 'whatsapp'])
            ]);
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
