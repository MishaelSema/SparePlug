<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Manage Subscriptions';
$user = new User();

// Check if user is admin
if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

$db = new Database();

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'approve') {
        $subId = $_POST['subscription_id'];
        // Fetch subscription details first
        $sub = $db->fetch("SELECT * FROM subscriptions WHERE id = ?", [$subId]);
        
        if ($sub && $sub['status'] === 'pending') {
            // activate subscription
            $db->update('subscriptions', ['status' => 'active'], 'id = ?', [$subId]);
            
            // update vendor profile
            $db->update('vendor_profiles', [
                'subscription_plan' => $sub['plan_type'],
                'subscription_expires' => $sub['expires_at']
            ], 'id = ?', [$sub['vendor_id']]);
            
            $_SESSION['success'] = 'Subscription approved successfully.';
        }
    } elseif ($_POST['action'] === 'reject') {
        $subId = $_POST['subscription_id'];
        $db->update('subscriptions', ['status' => 'cancelled'], 'id = ?', [$subId]);
        $_SESSION['success'] = 'Subscription rejected.';
    } elseif ($_POST['action'] === 'add_plan') {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $cycle = $_POST['billing_cycle'] ?? 'monthly';
        $features = explode("\n", $_POST['features'] ?? '');
        $features = array_filter(array_map('trim', $features));
        
        $db->insert('subscription_plans', [
            'name' => $name,
            'price' => $price,
            'billing_cycle' => $cycle,
            'features' => json_encode(array_values($features))
        ]);
        $_SESSION['success'] = 'New plan added successfully!';
    }
    
    header('Location: index.php?page=admin-subscriptions');
    exit;
}

// Fetch all plans
$plans = $db->fetchAll("SELECT * FROM subscription_plans ORDER BY price ASC");


// Fetch pending subscriptions
$pendingSubs = $db->fetchAll(
    "SELECT s.*, vp.business_name, u.first_name, u.last_name, u.email 
     FROM subscriptions s
     JOIN vendor_profiles vp ON s.vendor_id = vp.id
     JOIN users u ON vp.user_id = u.id
     WHERE s.status = 'pending'
     ORDER BY s.created_at ASC"
);

// Fetch active subscriptions (optional, for view)
$activeSubs = $db->fetchAll(
    "SELECT s.*, vp.business_name 
     FROM subscriptions s
     JOIN vendor_profiles vp ON s.vendor_id = vp.id
     WHERE s.status = 'active'
     ORDER BY s.expires_at ASC
     LIMIT 10"
);

ob_start();
?>

<div class="p-8 space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Subscriptions</h1>
        <p class="text-gray-600 mt-1">Review and approve vendor subscription payments</p>
    </div>

    <!-- Plan Management -->
    <div class="card mb-8">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Subscription Plans</h2>
            <button onclick="document.getElementById('addPlanModal').classList.remove('hidden')" class="btn-primary">
                + Add New Plan
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($plans as $plan): ?>
                <div class="border border-gray-100 rounded-3xl p-6 bg-gray-50/50 hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-bold text-primary"><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <span class="text-primary font-bold"><?php echo number_format($plan['price']); ?> <span class="text-xs uppercase">XAF</span></span>
                    </div>
                    <ul class="space-y-2 mb-6">
                        <?php 
                        $features = json_decode($plan['features'], true) ?: [];
                        foreach ($features as $feature): 
                        ?>
                        <li class="text-sm text-gray-600 flex items-center gap-2">
                            <span class="text-green-500">✓</span> <?php echo htmlspecialchars($feature); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="pt-4 border-t border-gray-200/50 flex justify-between items-center text-xs text-gray-400">
                        <span><?php echo ucfirst($plan['billing_cycle']); ?></span>
                        <span class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-bold uppercase tracking-wider"><?php echo $plan['is_active'] ? 'Active' : 'Hidden'; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="card mb-8">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Pending Approvals</h2>
        </div>
        <div class="p-6">
            <?php if (empty($pendingSubs)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500">No pending subscription requests.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b p-3 font-semibold">Vendor</th>
                                <th class="border-b p-3 font-semibold">Plan</th>
                                <th class="border-b p-3 font-semibold">Amount</th>
                                <th class="border-b p-3 font-semibold">Transaction ID</th>
                                <th class="border-b p-3 font-semibold">Date</th>
                                <th class="border-b p-3 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingSubs as $sub): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($sub['business_name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']); ?></div>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                        <?php echo ucfirst($sub['plan_type']); ?>
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1"><?php echo ucfirst($sub['billing_cycle']); ?></div>
                                </td>
                                <td class="p-3 font-medium">
                                    <?php echo number_format($sub['amount']); ?> FCFA
                                </td>
                                <td class="p-3 font-mono text-sm bg-gray-50">
                                    <?php echo htmlspecialchars($sub['payment_reference']); ?>
                                </td>
                                <td class="p-3 text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($sub['created_at'])); ?>
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Approve this subscription?');">
                                        <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" class="inline-block" onsubmit="return confirm('Reject this subscription?');">
                                        <input type="hidden" name="subscription_id" value="<?php echo $sub['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-sm rounded hover:bg-red-200">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Active Subscriptions Preview -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recently Active Subscriptions</h2>
        </div>
        <div class="p-6">
             <?php if (empty($activeSubs)): ?>
                <p class="text-gray-500">No active subscriptions.</p>
            <?php else: ?>
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="p-2 text-sm text-gray-600">Vendor</th>
                            <th class="p-2 text-sm text-gray-600">Plan</th>
                            <th class="p-2 text-sm text-gray-600">Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach ($activeSubs as $sub): ?>
                            <tr>
                                <td class="p-2 font-medium"><?php echo htmlspecialchars($sub['business_name']); ?></td>
                                <td class="p-2"><?php echo ucfirst($sub['plan_type']); ?></td>
                                <td class="p-2 text-sm <?php echo (strtotime($sub['expires_at']) < time() + 86400*7) ? 'text-red-600' : 'text-green-600'; ?>">
                                    <?php echo date('M d, Y', strtotime($sub['expires_at'])); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Add Plan Modal -->
<div id="addPlanModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Add New Subscription Plan</h2>
            <button onclick="document.getElementById('addPlanModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="add_plan">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Plan Name</label>
                <input type="text" name="name" required class="input-modern" placeholder="e.g. Gold Vendor">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Price (XAF)</label>
                    <input type="number" name="price" required class="input-modern" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Cycle</label>
                    <select name="billing_cycle" class="input-modern">
                        <option value="monthly">Monthly</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Features (One per line)</label>
                <textarea name="features" rows="4" class="input-modern" placeholder="Unlimited products&#10;Verified badge&#10;Priority support"></textarea>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3.5 mt-4">Create Plan</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
