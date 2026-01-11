<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Subscription Management';
$user = new User();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();

// Handle subscription upgrade/downgrade
$success = '';
$error = '';

if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'upgrade_subscription') {
        $newPlan = $_POST['plan'];
        $currentPlan = $vendorProfile['subscription_plan'];
        
        // Plan hierarchy (can only upgrade, not downgrade easily)
        $planHierarchy = ['free' => 0, 'basic' => 1, 'premium' => 2];
        
        if ($planHierarchy[$newPlan] > $planHierarchy[$currentPlan]) {
            // Update subscription
            $db->update('vendor_profiles', 
                ['subscription_plan' => $newPlan], 
                'id = ?', 
                [$vendorProfile['id']]
            );
            
            // Create subscription record
            $db->insert('subscriptions', [
                'vendor_id' => $vendorProfile['id'],
                'plan_type' => $newPlan,
                'amount' => $newPlan === 'basic' ? 15000 : 35000,
                'currency' => 'XAF',
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'starts_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 month'))
            ]);
            
            $success = "Upgraded to {$newPlan} plan successfully!";
            $vendorProfile = $user->getVendorProfile(); // Refresh
        } else {
            $error = 'You can only upgrade to a higher plan.';
        }
    }
}

// Get subscription details
$currentPlan = $vendorProfile['subscription_plan'] ?? 'free';

// Get subscription history
$subscriptionHistory = $db->fetchAll(
    "SELECT * FROM subscriptions WHERE vendor_id = ? ORDER BY created_at DESC LIMIT 10",
    [$vendorProfile['id']]
);

// Get current product count
$productCount = $db->fetch(
    "SELECT COUNT(*) as count FROM products WHERE vendor_id = ? AND is_active = 1",
    [$vendorProfile['id']]
)['count'];

// Plan limits
$planLimits = [
    'free' => 10,
    'basic' => 50,
    'premium' => 999999
];

ob_start();
?>

<div class="p-8 space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Subscription Management</h1>
        <p class="text-gray-600 mt-1">Manage your subscription plan and billing</p>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Current Plan -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Current Plan</h2>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 capitalize">
                        <?php echo $currentPlan; ?> Plan
                    </h3>
                    <p class="text-gray-600 mt-1">
                        <?php 
                        $planDetails = [
                            'free' => '10 products, Basic features',
                            'basic' => '50 products, Featured listings',
                            'premium' => 'Unlimited products, Full analytics'
                        ];
                        echo $planDetails[$currentPlan] ?? 'Basic features';
                        ?>
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        Products: <?php echo $productCount; ?> / <?php echo $planLimits[$currentPlan] == 999999 ? 'Unlimited' : $planLimits[$currentPlan]; ?>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold text-primary">
                        <?php echo $currentPlan === 'free' ? 'Free' : ($currentPlan === 'basic' ? '15,000' : '35,000'); ?> 
                        <span class="text-sm font-normal text-gray-600">XAF/month</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Plans -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Upgrade Your Plan</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Free Plan -->
                <div class="border rounded-lg p-6 <?php echo $currentPlan === 'free' ? 'border-primary shadow-md' : 'border-gray-200'; ?>">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Free</h3>
                        <p class="text-3xl font-bold text-primary mb-4">
                            Free
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2 mb-6">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                10 products
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Basic features
                            </li>
                        </ul>
                        <?php if ($currentPlan === 'free'): ?>
                            <span class="btn-secondary w-full inline-block text-center">
                                Current Plan
                            </span>
                        <?php else: ?>
                            <button disabled class="btn-secondary w-full opacity-50 cursor-not-allowed">
                                Downgrade
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Basic Plan -->
                <div class="border rounded-lg p-6 <?php echo $currentPlan === 'basic' ? 'border-primary shadow-md' : 'border-gray-200'; ?>">
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-2">
                            <span class="bg-accent text-white text-xs font-bold px-2 py-1 rounded">POPULAR</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Basic</h3>
                        <p class="text-3xl font-bold text-primary mb-4">
                            15,000 <span class="text-sm font-normal text-gray-600">XAF/mo</span>
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2 mb-6">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                50 products
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                3 featured listings/month
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Priority support
                            </li>
                        </ul>
                        <?php if ($currentPlan === 'basic'): ?>
                            <span class="btn-secondary w-full inline-block text-center">
                                Current Plan
                            </span>
                        <?php else: ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="upgrade_subscription">
                                <input type="hidden" name="plan" value="basic">
                                <button type="submit" class="btn-primary w-full" 
                                        onclick="return confirm('Upgrade to Basic plan for 15,000 XAF/month?')">
                                    Upgrade
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Premium Plan -->
                <div class="border rounded-lg p-6 <?php echo $currentPlan === 'premium' ? 'border-primary shadow-md' : 'border-gray-200'; ?>">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Premium</h3>
                        <p class="text-3xl font-bold text-primary mb-4">
                            35,000 <span class="text-sm font-normal text-gray-600">XAF/mo</span>
                        </p>
                        <ul class="text-sm text-gray-600 space-y-2 mb-6">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Unlimited products
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                10 featured listings/month
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Full analytics access
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Priority support
                            </li>
                        </ul>
                        <?php if ($currentPlan === 'premium'): ?>
                            <span class="btn-secondary w-full inline-block text-center">
                                Current Plan
                            </span>
                        <?php else: ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="upgrade_subscription">
                                <input type="hidden" name="plan" value="premium">
                                <button type="submit" class="btn-primary w-full" 
                                        onclick="return confirm('Upgrade to Premium plan for 35,000 XAF/month?')">
                                    Upgrade
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription History -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Subscription History</h2>
        </div>
        <div class="p-6">
            <?php if (empty($subscriptionHistory)): ?>
                <p class="text-center text-gray-500 py-8">No subscription history yet.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($subscriptionHistory as $sub): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 capitalize">
                                        <?php echo $sub['plan_type']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo number_format($sub['amount']); ?> <?php echo $sub['currency']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded <?php 
                                        echo $sub['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                                    ?>">
                                        <?php echo ucfirst($sub['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($sub['starts_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($sub['expires_at'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Payment Methods</h2>
        </div>
        <div class="p-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-1">Payment Integration Coming Soon</p>
                        <p>For now, subscription upgrades will be processed manually. Contact support for assistance.</p>
                        <p class="mt-2">
                            <strong>Supported payment methods (coming soon):</strong><br>
                            • MTN Mobile Money<br>
                            • Orange Money<br>
                            • Credit/Debit Cards
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





