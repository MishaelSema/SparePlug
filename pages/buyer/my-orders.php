<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'My Orders';
$user = new User();

if (!$user->isLoggedIn()) {
    header('Location: index.php?page=login&redirect=index.php?page=my-orders');
    exit;
}

$order = new Order();
$orders = $order->getByBuyer($_SESSION['user_id']);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 font-heading">My Orders</h1>
        <p class="text-gray-500 mt-2">Track and manage your orders</p>
    </div>

    <?php if (empty($orders)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-8">
                You haven't placed any orders yet. Start shopping to see your orders here.
            </p>
            <a href="index.php?page=search" class="btn-primary">
                Browse Products
            </a>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($orders as $o): 
                $images = json_decode($o['product_images'], true);
                $mainImage = !empty($images) ? $images[0] : 'assets/placeholder-product.jpg';
                
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'paid' => 'bg-blue-100 text-blue-800',
                    'processing' => 'bg-purple-100 text-purple-800',
                    'ready_for_pickup' => 'bg-green-100 text-green-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'completed' => 'bg-gray-100 text-gray-800',
                    'disputed' => 'bg-red-100 text-red-800'
                ];
                $color = $statusColors[$o['fulfillment_status']] ?? 'bg-gray-100 text-gray-800';
            ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Product Image -->
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                            <img src="<?php echo htmlspecialchars($mainImage); ?>" class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Order Info -->
                        <div class="flex-grow">
                            <div class="flex flex-wrap justify-between items-start gap-4">
                                <div>
                                    <p class="text-xs text-gray-400">Order #</p>
                                    <p class="font-bold text-gray-900"><?php echo htmlspecialchars($o['order_number']); ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $color; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $o['fulfillment_status'])); ?>
                                </span>
                            </div>
                            
                            <h3 class="font-bold text-lg text-gray-900 mt-2">
                                <?php echo htmlspecialchars($o['product_title']); ?>
                            </h3>
                            
                            <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-600">
                                <div>
                                    <span class="text-gray-400">Vendor:</span>
                                    <?php echo htmlspecialchars($o['vendor_name']); ?>
                                </div>
                                <div>
                                    <span class="text-gray-400">Qty:</span>
                                    <?php echo $o['quantity']; ?>
                                </div>
                                <div>
                                    <span class="text-gray-400">Total:</span>
                                    <span class="font-bold text-accent"><?php echo number_format($o['total_amount']); ?> XAF</span>
                                </div>
                                <div>
                                    <span class="text-gray-400">Payment:</span>
                                    <?php echo ucfirst($o['payment_status']); ?>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mt-4">
                                <?php if ($o['payment_status'] === 'pending'): ?>
                                    <button onclick="markAsPaid(<?php echo $o['id']; ?>)" class="btn-primary text-sm py-2">
                                        I Have Paid
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($o['fulfillment_status'] === 'ready_for_pickup' || $o['fulfillment_status'] === 'delivered'): ?>
                                    <button onclick="confirmOrder(<?php echo $o['id']; ?>)" class="btn-primary text-sm py-2">
                                        Confirm Receipt
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($o['fulfillment_status'] !== 'completed' && $o['dispute_status'] === 'none'): ?>
                                    <button onclick="openDispute(<?php echo $o['id']; ?>)" class="text-sm text-red-600 hover:text-red-700 font-medium py-2">
                                        Report Issue
                                    </button>
                                <?php endif; ?>
                                
                                <a href="index.php?page=order-detail&id=<?php echo $o['id']; ?>" class="btn-secondary text-sm py-2">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Confirm Receipt Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Confirm Receipt</h2>
        <p class="text-gray-600 mb-6">
            Have you received the product? Once confirmed, payment will be released to the vendor from escrow.
        </p>
        <div class="flex gap-3">
            <button onclick="document.getElementById('confirmModal').classList.add('hidden')" class="btn-secondary flex-1">
                Not Yet
            </button>
            <button onclick="submitConfirmReceipt()" id="confirmReceiptBtn" class="btn-primary flex-1">
                Yes, I Received It
            </button>
        </div>
    </div>
</div>

<!-- Dispute Modal -->
<div id="disputeModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Report Issue</h2>
        <textarea id="dispute_reason" rows="4" class="input-modern mb-4" placeholder="Describe the issue..."></textarea>
        <div class="flex gap-3">
            <button onclick="document.getElementById('disputeModal').classList.add('hidden')" class="btn-secondary flex-1">
                Cancel
            </button>
            <button onclick="submitDispute()" class="btn-primary flex-1 bg-red-600 hover:bg-red-700">
                Submit Report
            </button>
        </div>
    </div>
</div>

<script>
let currentOrderId = null;

function confirmOrder(orderId) {
    currentOrderId = orderId;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function markAsPaid(orderId) {
    document.getElementById('paid_order_id').value = orderId;
    document.getElementById('confirmPaidModal').classList.remove('hidden');
}

function submitPaidConfirmation() {
    const orderId = document.getElementById('paid_order_id').value;
    const btn = document.getElementById('confirmPaidBtn');
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
    fetch('api/mark-paid.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({order_id: orderId})
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('confirmPaidModal').classList.add('hidden');
        if (data.success) {
            document.getElementById('paidSuccessModal').classList.remove('hidden');
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.textContent = 'Yes, I Paid';
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Yes, I Paid';
        alert('Error submitting. Please try again.');
    });
}

function submitConfirmReceipt() {
    const btn = document.getElementById('confirmReceiptBtn');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Processing...';
    }
    
    fetch('api/confirm-order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({order_id: currentOrderId})
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('confirmModal').classList.add('hidden');
        if (data.success) {
            alert('Receipt confirmed! Payment has been released to the vendor.');
            location.reload();
        } else {
            alert('Error: ' + data.message);
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Yes, I Received It';
            }
        }
    })
    .catch(err => {
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Yes, I Received It';
        }
        alert('Error. Please try again.');
    });
}

function openDispute(orderId) {
    currentOrderId = orderId;
    document.getElementById('disputeModal').classList.remove('hidden');
}

function submitDispute() {
    const reason = document.getElementById('dispute_reason').value;
    if (!reason.trim()) {
        alert('Please describe the issue');
        return;
    }
    
    fetch('api/open-dispute.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({order_id: currentOrderId, reason: reason})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Report submitted. We will review and contact you shortly.');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
    document.getElementById('disputeModal').classList.add('hidden');
}
</script>

<!-- Confirm Paid Modal -->
<div id="confirmPaidModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 text-center">
        <input type="hidden" id="paid_order_id">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Payment</h3>
        <p class="text-gray-600 mb-4">Have you made the payment? Click Yes to confirm.</p>
        <div class="flex gap-3">
            <button onclick="document.getElementById('confirmPaidModal').classList.add('hidden')" class="btn-secondary flex-1">Not Yet</button>
            <button onclick="submitPaidConfirmation()" id="confirmPaidBtn" class="btn-primary flex-1">Yes, I Paid</button>
        </div>
    </div>
</div>

<!-- Paid Success Modal -->
<div id="paidSuccessModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
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
