<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Financial Dashboard';
$user = new User();

if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

$db = new Database();

// Financial stats
$stats = [
    'total_orders' => $db->fetch("SELECT COUNT(*) as c FROM orders")['c'] ?? 0,
    'total_revenue' => $db->fetch("SELECT SUM(total_amount) as t FROM orders WHERE payment_status IN ('paid', 'released')")['t'] ?? 0,
    'pending_payments' => $db->fetch("SELECT SUM(total_amount) as t FROM orders WHERE payment_status = 'pending'")['t'] ?? 0,
    'escrow_held' => $db->fetch("SELECT SUM(seller_amount) as t FROM orders WHERE escrow_status = 'held'")['t'] ?? 0,
    'commission_earned' => $db->fetch("SELECT SUM(commission_amount) as t FROM orders WHERE payment_status IN ('paid', 'released')")['t'] ?? 0,
    'completed_orders' => $db->fetch("SELECT COUNT(*) as c FROM orders WHERE fulfillment_status = 'completed'")['c'] ?? 0,
];

// Recent orders with escrow
$recentOrders = $db->fetchAll("
    SELECT o.*, p.title as product_title, vp.business_name as vendor_name,
           u.first_name as buyer_first_name, u.last_name as buyer_last_name
    FROM orders o
    JOIN products p ON o.product_id = p.id
    JOIN vendor_profiles vp ON o.vendor_id = vp.id
    JOIN users u ON o.buyer_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 20
");

ob_start();
?>

<div class="p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Financial Dashboard</h1>
        <p class="text-gray-600 mt-1">Track platform finances and escrow</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card p-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-green-600"><?php echo number_format($stats['total_revenue']); ?> XAF</p>
            <p class="text-xs text-gray-500">From <?php echo $stats['completed_orders']; ?> completed orders</p>
        </div>
        
        <div class="card p-6 border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Pending Payments</p>
            <p class="text-2xl font-bold text-yellow-600"><?php echo number_format($stats['pending_payments']); ?> XAF</p>
            <p class="text-xs text-gray-500">Awaiting buyer payment</p>
        </div>
        
        <div class="card p-6 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">Escrow Held</p>
            <p class="text-2xl font-bold text-blue-600"><?php echo number_format($stats['escrow_held']); ?> XAF</p>
            <p class="text-xs text-gray-500">Protected until delivery</p>
        </div>
        
        <div class="card p-6 border-l-4 border-purple-500">
            <p class="text-sm font-medium text-gray-500">Platform Commission</p>
            <p class="text-2xl font-bold text-purple-600"><?php echo number_format($stats['commission_earned']); ?> XAF</p>
            <p class="text-xs text-gray-500">Earned from transactions</p>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">All Orders</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Order</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Buyer</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Vendor</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Amount</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Commission</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Payment</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Escrow</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900"><?php echo htmlspecialchars($o['order_number']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($o['product_title']); ?></p>
                                <p class="text-xs text-gray-400"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900"><?php echo htmlspecialchars(($o['buyer_first_name'] ?? '') . ' ' . ($o['buyer_last_name'] ?? ''); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-900"><?php echo htmlspecialchars($o['vendor_name']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-accent"><?php echo number_format($o['total_amount']); ?> XAF</p>
                                <p class="text-xs text-gray-500">Vendor gets: <?php echo number_format($o['seller_amount']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-purple-600 font-medium"><?php echo number_format($o['commission_amount']); ?> XAF</p>
                                <p class="text-xs text-gray-500"><?php echo $o['commission_rate']; ?>%</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $o['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo ucfirst($o['payment_status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $o['escrow_status'] === 'held' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                    <?php echo ucfirst($o['escrow_status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full <?php 
                                    $colors = ['pending' => 'bg-yellow-100 text-yellow-800', 'processing' => 'bg-purple-100 text-purple-800', 'completed' => 'bg-gray-100 text-gray-800'];
                                    echo $colors[$o['fulfillment_status']] ?? 'bg-gray-100 text-gray-800';
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $o['fulfillment_status'])); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
