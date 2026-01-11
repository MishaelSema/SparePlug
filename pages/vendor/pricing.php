<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Pricing Plans';
$user = new User();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();

// Handle subscription upgrade request
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'submit_payment') {
    $planType = $_POST['plan_type'];
    $billingCycle = $_POST['billing_cycle'];
    $transactionId = trim($_POST['transaction_id']);
    
    // Calculate pricing
    $pricing = [
        'free' => ['monthly' => 0, 'annual' => 0],
        'basic' => ['monthly' => 15000, 'annual' => 150000],
        'premium' => ['monthly' => 35000, 'annual' => 350000]
    ];
    
    $amount = $pricing[$planType][$billingCycle];
    
    if (empty($transactionId)) {
        $_SESSION['error'] = 'Please enter a valid Transaction ID.';
    } else {
        // Create pending subscription record
        $subscriptionData = [
            'vendor_id' => $vendorProfile['id'],
            'plan_type' => $planType,
            'amount' => $amount,
            'billing_cycle' => $billingCycle,
            'status' => 'pending', // Pending approval
            'starts_at' => date('Y-m-d H:i:s'), // Will be updated on approval
            'expires_at' => $billingCycle === 'annual' ? date('Y-m-d H:i:s', strtotime('+1 year')) : date('Y-m-d H:i:s', strtotime('+1 month')),
            'payment_reference' => $transactionId
        ];
        
        try {
            // Check if already has pending
            $existing = $db->fetch("SELECT id FROM subscriptions WHERE vendor_id = ? AND status = 'pending'", [$vendorProfile['id']]);
            if ($existing) {
                $_SESSION['error'] = 'You already have a pending subscription request. Please wait for approval.';
            } else {
                $db->insert('subscriptions', $subscriptionData);
                $_SESSION['success'] = 'Payment submitted! Your plan will be active once we verify the transaction.';
                header('Location: index.php?page=vendor-dashboard');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to submit payment. Please try again.';
        }
    }
}

// Get current subscription
$currentSubscription = $db->fetch(
    "SELECT * FROM subscriptions WHERE vendor_id = ? AND status = 'active' ORDER BY expires_at DESC LIMIT 1",
    [$vendorProfile['id']]
);

// Get pending subscription
$pendingSubscription = $db->fetch(
    "SELECT * FROM subscriptions WHERE vendor_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1",
    [$vendorProfile['id']]
);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Choose Your Plan</h1>
        <p class="text-lg text-gray-600">Select the perfect plan for your business needs</p>
    </div>
    
    <!-- Status Messages -->
    <?php if ($pendingSubscription): ?>
        <div class="card p-6 mb-8 bg-yellow-50 border-yellow-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-900">Activation Pending</h3>
                    <p class="text-yellow-700">
                        We are verifying your payment for the <strong><?php echo ucfirst($pendingSubscription['plan_type']); ?></strong> plan. 
                        Ref: <?php echo htmlspecialchars($pendingSubscription['payment_reference']); ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($currentSubscription): ?>
        <div class="card p-6 mb-8 bg-blue-50 border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900">Current Plan: <?php echo ucfirst($currentSubscription['plan_type']); ?></h3>
                    <p class="text-blue-700">
                        Expires on <?php echo date('F j, Y', strtotime($currentSubscription['expires_at'])); ?>
                        (<?php echo ceil((strtotime($currentSubscription['expires_at']) - time()) / 86400); ?> days remaining)
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-blue-900">
                        <?php echo number_format($currentSubscription['amount']); ?> FCFA
                    </p>
                    <p class="text-sm text-blue-700"><?php echo ucfirst($currentSubscription['billing_cycle']); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Pricing Toggle -->
    <div class="flex justify-center mb-8">
        <div class="bg-gray-100 p-1 rounded-lg">
            <button id="monthly-toggle" class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 bg-white text-gray-900 shadow-sm">
                Monthly
            </button>
            <button id="annual-toggle" class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 text-gray-500 hover:text-gray-900">
                Annual (Save 17%)
            </button>
        </div>
    </div>
    
    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Free Plan -->
        <div class="card p-8 relative">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Free</h3>
                <p class="text-gray-600 mb-6">Perfect for getting started</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900">0</span>
                    <span class="text-gray-600">FCFA</span>
                    <span class="text-sm text-gray-500">/month</span>
                </div>
                
                <ul class="space-y-3 mb-8 text-left">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-600">Up to 10 product listings</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-600">Basic seller profile</span>
                    </li>
                    <li class="flex items-center">
                         <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-600">Customer inquiries</span>
                    </li>
                </ul>
                
                <button class="btn-secondary w-full" disabled>
                    Free Forever
                </button>
            </div>
        </div>
        
        <!-- Basic Plan -->
        <div class="card p-8 relative border-2 border-primary">
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                <span class="bg-primary text-white px-4 py-1 rounded-full text-sm font-medium">Most Popular</span>
            </div>
            
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Basic</h3>
                <p class="text-gray-600 mb-6">Great for growing businesses</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-primary monthly-price">15,000</span>
                    <span class="text-4xl font-bold text-primary annual-price hidden">150,000</span>
                    <span class="text-gray-600">FCFA</span>
                    <span class="text-sm text-gray-500">/month</span>
                </div>
                
                <ul class="space-y-3 mb-8 text-left">
                     <li class="flex items-center">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-600">Up to 50 product listings</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-600">Featured product promotion</span>
                    </li>
                </ul>
                
                <button onclick="openPaymentModal('basic')" class="btn-primary w-full" <?php echo ($pendingSubscription) ? 'disabled' : ''; ?>>
                    <?php echo ($pendingSubscription) ? 'Pending Approval' : 'Upgrade to Basic'; ?>
                </button>
            </div>
        </div>
        
        <!-- Premium Plan -->
        <div class="card p-8 relative">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Premium</h3>
                <p class="text-gray-600 mb-6">For established businesses</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-primary monthly-price">35,000</span>
                    <span class="text-4xl font-bold text-primary annual-price hidden">350,000</span>
                    <span class="text-gray-600">FCFA</span>
                    <span class="text-sm text-gray-500">/month</span>
                </div>
                
                <ul class="space-y-3 mb-8 text-left">
                     <li class="flex items-center">
                         <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-600">Unlimited product listings</span>
                    </li>
                    <li class="flex items-center">
                         <svg class="w-5 h-5 text-success mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-600">Advanced Analytics</span>
                    </li>
                </ul>
                
                <button onclick="openPaymentModal('premium')" class="btn-primary w-full" <?php echo ($pendingSubscription) ? 'disabled' : ''; ?>>
                    <?php echo ($pendingSubscription) ? 'Pending Approval' : 'Upgrade to Premium'; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Complete Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <h4 class="font-medium text-blue-900 mb-2">Payment Instructions</h4>
                <p class="text-sm text-blue-700 mb-1">1. Deposit <span id="modal-price" class="font-bold"></span> FCFA to:</p>
                <p class="text-sm font-bold text-blue-900">MTN Mobile Money: 671234567 (SparePlug Inc)</p>
                <p class="text-sm font-bold text-blue-900">Orange Money: 691234567 (SparePlug Inc)</p>
            </div>
            
            <form method="POST" id="payment-form">
                <input type="hidden" name="action" value="submit_payment">
                <input type="hidden" name="plan_type" id="modal-plan-type">
                <input type="hidden" name="billing_cycle" id="modal-billing-cycle">
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction ID / Reference</label>
                    <input type="text" name="transaction_id" required class="input-field" placeholder="e.g. 123456789">
                    <p class="text-xs text-gray-500 mt-1">Enter the transaction ID from your SMS confirmation</p>
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="closePaymentModal()" class="btn-secondary flex-1">Cancel</button>
                    <button type="submit" class="btn-primary flex-1">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentCycle = 'monthly';
const prices = {
    'basic': { 'monthly': '15,000', 'annual': '150,000' },
    'premium': { 'monthly': '35,000', 'annual': '350,000' }
};

document.getElementById('monthly-toggle').addEventListener('click', function() {
    this.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    this.classList.remove('text-gray-500');
    document.getElementById('annual-toggle').classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    document.getElementById('annual-toggle').classList.add('text-gray-500');
    document.querySelectorAll('.monthly-price').forEach(el => el.classList.remove('hidden'));
    document.querySelectorAll('.annual-price').forEach(el => el.classList.add('hidden'));
    currentCycle = 'monthly';
});

document.getElementById('annual-toggle').addEventListener('click', function() {
    this.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    this.classList.remove('text-gray-500');
    document.getElementById('monthly-toggle').classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    document.getElementById('monthly-toggle').classList.add('text-gray-500');
    document.querySelectorAll('.monthly-price').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.annual-price').forEach(el => el.classList.remove('hidden'));
    currentCycle = 'annual';
});

function openPaymentModal(planType) {
    document.getElementById('modal-plan-type').value = planType;
    document.getElementById('modal-billing-cycle').value = currentCycle;
    document.getElementById('modal-price').textContent = prices[planType][currentCycle];
    document.getElementById('payment-modal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
