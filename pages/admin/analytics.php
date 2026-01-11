<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Platform Analytics';
$user = new User();

// Check if user is admin
if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

$db = new Database();

// Get platform statistics
$totalUsers = $db->fetch("SELECT COUNT(*) as count FROM users")['count'];
$totalVendors = $db->fetch("SELECT COUNT(*) as count FROM users WHERE user_type = 'vendor'")['count'];
$totalBuyers = $db->fetch("SELECT COUNT(*) as count FROM users WHERE user_type = 'buyer'")['count'];
$totalProducts = $db->fetch("SELECT COUNT(*) as count FROM products WHERE is_active = 1")['count'];
$totalInquiries = $db->fetch("SELECT COUNT(*) as count FROM product_inquiries")['count'];
$totalReviews = $db->fetch("SELECT COUNT(*) as count FROM reviews")['count'];

// Recent activity
$recentRegistrations = $db->fetchAll("SELECT * FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY created_at DESC");

ob_start();
?>

<div class="p-8 space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Platform Analytics</h1>
        <p class="text-gray-600 mt-1">Comprehensive platform insights and metrics</p>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalUsers); ?></p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?php echo number_format($totalBuyers); ?> buyers, <?php echo number_format($totalVendors); ?> vendors
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Products</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalProducts); ?></p>
                    <p class="text-xs text-gray-500 mt-1">Currently listed</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Interactions</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalInquiries + $totalReviews); ?></p>
                    <p class="text-xs text-gray-500 mt-1">
                        <?php echo number_format($totalInquiries); ?> inquiries, <?php echo number_format($totalReviews); ?> reviews
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Recent Registrations (Last 7 Days)</h2>
        </div>
        <div class="p-6">
            <?php if (count($recentRegistrations) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($recentRegistrations as $userData): ?>
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                            <?php echo strtoupper(substr($userData['first_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($userData['email']); ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs font-medium rounded <?php 
                            echo $userData['user_type'] === 'admin' ? 'bg-red-100 text-red-800' : 
                                ($userData['user_type'] === 'vendor' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'); 
                        ?>">
                            <?php echo ucfirst($userData['user_type']); ?>
                        </span>
                        <p class="text-xs text-gray-500 mt-1"><?php echo date('M d, Y', strtotime($userData['created_at'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-gray-500">No recent registrations</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





