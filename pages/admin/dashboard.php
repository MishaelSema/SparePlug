<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Admin Dashboard';
$user = new User();

// Check if user is admin
if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

// Get platform statistics
$db = new Database();

// Total users
$totalUsers = $db->fetch("SELECT COUNT(*) as count FROM users")['count'];

// Total vendors
$totalVendors = $db->fetch("SELECT COUNT(*) as count FROM users WHERE user_type = 'vendor'")['count'];

// Total buyers
$totalBuyers = $db->fetch("SELECT COUNT(*) as count FROM users WHERE user_type = 'buyer'")['count'];

// Total active products
$totalProducts = $db->fetch("SELECT COUNT(*) as count FROM products WHERE is_active = 1")['count'];

// Total categories
$totalCategories = $db->fetch("SELECT COUNT(*) as count FROM categories WHERE is_active = 1")['count'];

// Recent users
$recentUsers = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

// Recent vendors waiting for approval
$pendingVendors = $db->fetchAll("
    SELECT vp.*, u.email, u.phone, u.first_name, u.last_name
    FROM vendor_profiles vp 
    JOIN users u ON vp.user_id = u.id 
    WHERE vp.is_verified = 0 
    ORDER BY vp.created_at DESC LIMIT 5
");

ob_start();
?>

<div class="p-8 space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-1">Overview of your platform</p>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="card p-6 border-none shadow-soft bg-white rounded-3xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalUsers); ?></p>
                </div>
                <div class="w-12 h-12 bg-primary/5 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Vendors Card -->
        <div class="card p-6 border-none shadow-soft bg-white rounded-3xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Vendors</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalVendors); ?></p>
                </div>
                <div class="w-12 h-12 bg-primary/5 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="card p-6 border-none shadow-soft bg-white rounded-3xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Active Products</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalProducts); ?></p>
                </div>
                <div class="w-12 h-12 bg-primary/5 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Categories Card -->
        <div class="card p-6 border-none shadow-soft bg-white rounded-3xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Categories</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($totalCategories); ?></p>
                </div>
                <div class="w-12 h-12 bg-primary/5 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="card">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Recent Users</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($recentUsers as $user): ?>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded <?php echo $user['user_type'] === 'vendor' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                            <?php echo ucfirst($user['user_type']); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4">
                    <a href="index.php?page=admin-users" class="text-primary hover:text-red-700 text-sm font-medium">
                        View All Users →
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Vendor Verifications -->
        <div class="card">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Pending Vendor Verifications</h2>
            </div>
            <div class="p-6">
                <?php if (count($pendingVendors) > 0): ?>
                <div class="space-y-4">
                    <?php foreach ($pendingVendors as $vendor): ?>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($vendor['business_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($vendor['business_name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($vendor['city']); ?></p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-500">No pending verifications</p>
                </div>
                <?php endif; ?>
                <div class="mt-4">
                    <a href="index.php?page=admin-users" class="text-primary hover:text-red-700 text-sm font-medium">
                        Manage Vendors →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="index.php?page=admin-users" class="flex items-center space-x-3 p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all group">
                    <div class="w-10 h-10 bg-primary/5 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                        <svg class="w-5 h-5 text-primary group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">Manage Users</p>
                        <p class="text-xs text-gray-500">View and manage all users</p>
                    </div>
                </a>

                <a href="index.php?page=admin-products" class="flex items-center space-x-3 p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all group">
                    <div class="w-10 h-10 bg-primary/5 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                        <svg class="w-5 h-5 text-primary group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">Manage Products</p>
                        <p class="text-xs text-gray-500">View and manage all products</p>
                    </div>
                </a>

                <a href="index.php?page=admin-categories" class="flex items-center space-x-3 p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all group">
                    <div class="w-10 h-10 bg-primary/5 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                        <svg class="w-5 h-5 text-primary group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">Manage Categories</p>
                        <p class="text-xs text-gray-500">Organize product categories</p>
                    </div>
                </a>

                <a href="index.php?page=admin-subscriptions" class="flex items-center space-x-3 p-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-all group">
                    <div class="w-10 h-10 bg-primary/5 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                        <svg class="w-5 h-5 text-primary group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">Subscriptions</p>
                        <p class="text-xs text-gray-500">Approve payments</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
