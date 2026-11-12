<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Order Details';
$user = new User();

if (!$user->isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);

if (!$orderId) {
    header('Location: index.php?page=my-orders');
    exit;
}

$order = new Order();
$orderData = $order->getById($orderId);

// Check if user owns this order
if (!$orderData || ($orderData['buyer_id'] != $_SESSION['user_id'] && !$user->isAdmin())) {
    header('Location: index.php?page=my-orders');
    exit;
}

$product = new Product();
$productData = $product->getById($orderData['product_id']);

$vendor = $db->fetch("SELECT * FROM vendor_profiles WHERE id = ?", [$orderData['vendor_id']]);

// Get payment settings
$paymentSettings = [];
$settingsData = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($settingsData as $s) {
    $paymentSettings[$s['setting_key']] = $s['setting_value'];
}

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="index.php?page=my-orders" class="text-sm text-gray-600 hover:text-primary mb-2 inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Orders
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($orderData['order_number']); ?></h1>
        </div>
        <span class="px-3 py-1 rounded-full text-sm font-bold <?php 
            $colors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'paid' => 'bg-blue-100 text-blue-800',
                'processing' => 'bg-purple-100 text-purple-800',
                'ready_for_pickup' => 'bg-green-100 text-green-800',
                'delivered' => 'bg-green-100 text-green-800',
                'completed' => 'bg-gray-100 text-gray-800'
            ];
            echo $colors[$orderData['fulfillment_status']] ?? 'bg-gray-100 text-gray-800';
        ?>">
            <?php echo ucfirst(str_replace('_', ' ', $orderData['fulfillment_status'])); ?>
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Order Summary -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Product:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($orderData['product_title']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Quantity:</span>
                    <span class="font-medium"><?php echo $orderData['quantity']; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Unit Price:</span>
                    <span class="font-medium"><?php echo number_format($orderData['unit_price']); ?> XAF</span>
                </div>
                <hr>
                <div class="flex justify-between">
                    <span class="text-gray-900 font-bold">Total:</span>
                    <span class="text-accent font-bold text-xl"><?php echo number_format($orderData['total_amount']); ?> XAF</span>
                </div>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Payment</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Method:</span>
                    <span class="font-medium"><?php echo ucfirst(str_replace('_', ' ', $orderData['payment_method'])); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium <?php echo $orderData['payment_status'] === 'paid' ? 'text-green-600' : 'text-yellow-600'; ?>">
                        <?php echo ucfirst($orderData['payment_status']); ?>
                    </span>
                </div>
                <?php if ($orderData['payment_status'] === 'pending'): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-4">
                        <p class="text-sm text-yellow-800 mb-3">Please transfer <strong><?php echo number_format($orderData['total_amount']); ?> XAF</strong> to:</p>
                        
                        <?php if ($orderData['payment_method'] === 'momo' && !empty($paymentSettings['momo_number'])): ?>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs font-bold text-blue-600 uppercase">MTN MoMo</p>
                                <p class="text-xl font-bold text-blue-900"><?php echo htmlspecialchars($paymentSettings['momo_number']); ?></p>
                                <p class="text-sm text-blue-700">Name: <?php echo htmlspecialchars($paymentSettings['momo_name']); ?></p>
                            </div>
                        <?php elseif ($orderData['payment_method'] === 'orange_money' && !empty($paymentSettings['orange_number'])): ?>
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                <p class="text-xs font-bold text-orange-600 uppercase">Orange Money</p>
                                <p class="text-xl font-bold text-orange-900"><?php echo htmlspecialchars($paymentSettings['orange_number']); ?></p>
                                <p class="text-sm text-orange-700">Name: <?php echo htmlspecialchars($paymentSettings['orange_name']); ?></p>
                            </div>
                        <?php else: ?>
                            <p class="text-sm text-red-600">Payment numbers not set. Contact support.</p>
                        <?php endif; ?>
                        
                        <p class="text-xs text-yellow-700 mt-2">After payment, click "I Have Made Payment" below.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Vendor Info -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Seller</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Shop:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($orderData['vendor_name']); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Location:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($orderData['vendor_city']); ?></span>
                </div>
            </div>
            <a href="index.php?page=vendor-profile&id=<?php echo $orderData['vendor_id']; ?>" class="btn-secondary w-full mt-4 text-center">View Shop</a>
        </div>
        
        <!-- Delivery Info -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Delivery</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Method:</span>
                    <span class="font-medium"><?php echo ucfirst($orderData['delivery_method']); ?></span>
                </div>
                <?php if (!empty($orderData['delivery_notes'])): ?>
                <div>
                    <span class="text-gray-600">Notes:</span>
                    <p class="font-medium"><?php echo htmlspecialchars($orderData['delivery_notes']); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <?php if ($orderData['payment_status'] === 'pending'): ?>
    <div class="mt-6">
        <button onclick="document.getElementById('confirmPaymentModal').classList.remove('hidden')" class="btn-primary">
            I Have Made Payment
        </button>
    </div>
    
    <!-- Confirm Payment Modal -->
    <div id="confirmPaymentModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Payment</h3>
            <p class="text-gray-600 mb-4">Have you made the payment of <span class="font-bold text-accent"><?php echo number_format($orderData['total_amount']); ?> XAF</span>?</p>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-left">
                <p class="text-sm text-yellow-800">By confirming, you acknowledge that payment has been made. The seller will be notified to proceed with your order.</p>
            </div>
            
            <div class="flex gap-3">
                <button onclick="document.getElementById('confirmPaymentModal').classList.add('hidden')" class="btn-secondary flex-1">Not Yet</button>
                <button onclick="confirmPayment(<?php echo $orderData['id']; ?>)" class="btn-primary flex-1">Yes, I Paid</button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function confirmPayment(orderId) {
    fetch('api/mark-paid.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({order_id: orderId})
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('confirmPaymentModal').classList.add('hidden');
        if (data.success) {
            document.getElementById('paymentConfirmedModal').classList.remove('hidden');
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>

<!-- Payment Confirmed Modal -->
<div id="paymentConfirmedModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Payment Confirmed!</h3>
        <p class="text-gray-600 mb-4">The seller has been notified and will prepare your order.</p>
        <button onclick="location.reload()" class="btn-primary w-full">OK</button>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
