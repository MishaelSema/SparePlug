<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Vendor Dashboard';
$user = new User();
$product = new Product();
$inquiry = new Inquiry();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();
$currentUser = $user->getCurrentUser();

if (!$vendorProfile) {
    // Attempt redirect
    if (!headers_sent()) {
        header('Location: index.php?page=vendor-signup');
        exit;
    } else {
        echo "<script>window.location.href='index.php?page=vendor-signup';</script>";
        exit;
    }
}

$vendorId = $vendorProfile['id'];

// Get dashboard statistics
$stats = [
    'total_products' => $db->fetch("SELECT COUNT(*) as count FROM products WHERE vendor_id = ?", [$vendorId])['count'],
    'active_products' => $db->fetch("SELECT COUNT(*) as count FROM products WHERE vendor_id = ? AND is_active = 1", [$vendorId])['count'],
    'total_views' => $db->fetch("SELECT SUM(views_count) as count FROM products WHERE vendor_id = ?", [$vendorId])['count'] ?? 0,
    'featured_products' => $db->fetch("SELECT COUNT(*) as count FROM products WHERE vendor_id = ? AND is_featured = 1", [$vendorId])['count']
];

// Get recent inquiries
$recentInquiries = $inquiry->getByVendor($vendorProfile['id']);

// Get recent products
$recentProducts = $product->getAll(['vendor_id' => $vendorProfile['id'], 'limit' => 5]);

// Check active subscription status
$subscription = $db->fetch(
    "SELECT * FROM subscriptions WHERE vendor_id = ? AND status = 'active' ORDER BY expires_at DESC LIMIT 1",
    [$vendorProfile['id']]
);

$isSubscriptionActive = $subscription && strtotime($subscription['expires_at']) > time();
$daysUntilExpiry = $subscription ? max(0, ceil((strtotime($subscription['expires_at']) - time()) / 86400)) : 0;

// Check pending subscription
$pendingSubscription = $db->fetch(
    "SELECT * FROM subscriptions WHERE vendor_id = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1",
    [$vendorProfile['id']]
);

ob_start();
?>

<div class="p-8">
    <!-- Welcome Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Welcome back, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
            </h1>
            <p class="text-gray-600">Manage your products and grow your business</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="index.php?page=vendor-profile&id=<?php echo $vendorProfile['id']; ?>" class="btn-secondary flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview My Shop
            </a>
        </div>
    </div>

    <!-- Pending Subscription Alert -->
    <?php if ($pendingSubscription): ?>
        <div class="mb-8">
            <div class="card p-6 bg-blue-50 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Your subscription upgrade to <strong><?php echo ucfirst($pendingSubscription['plan_type']); ?></strong> is pending approval. 
                            We are verifying your payment (Ref: <?php echo htmlspecialchars($pendingSubscription['payment_reference']); ?>).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Active Subscription Status (Only show if expiring soon or inactive, and not pending) -->
    <?php if ((!$isSubscriptionActive || $daysUntilExpiry <= 7) && !$pendingSubscription): ?>
        <div class="mb-8">
            <div class="card p-6 bg-gradient-to-r from-accent to-yellow-500 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">
                            <?php if (!$isSubscriptionActive): ?>
                                Upgrade Your Plan
                            <?php else: ?>
                                Subscription Expires Soon
                            <?php endif; ?>
                        </h3>
                        <p class="text-yellow-100">
                            <?php if (!$isSubscriptionActive): ?>
                                You're on the free plan. Upgrade to get more listings and features.
                            <?php else: ?>
                                Your subscription expires in <?php echo $daysUntilExpiry; ?> days.
                            <?php endif; ?>
                        </p>
                    </div>
                    <a href="index.php?page=vendor-pricing" class="bg-white text-accent px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors duration-200">
                        <?php echo $isSubscriptionActive ? 'Renew' : 'Upgrade'; ?>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_products']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Products</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['active_products']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_views']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Featured Products</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['featured_products']); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-8">
        <!-- Recent Products -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Your Products</h2>
                <a href="index.php?page=vendor-products" class="text-primary hover:underline text-sm">Manage All</a>
            </div>
            
            <?php if (empty($recentProducts)): ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No products yet</h3>
                    <p class="text-gray-600 mb-4">Start by adding your first product</p>
                    <a href="index.php?page=vendor-add-product" class="btn-primary">Add Product</a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($recentProducts as $product): ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0">
                                <?php 
                                $images = json_decode($product['images'], true);
                                $mainImage = !empty($images) ? $images[0] : 'assets/placeholder-product.jpg';
                                ?>
                                <img src="<?php echo htmlspecialchars($mainImage); ?>" 
                                     alt="<?php echo htmlspecialchars($product['title']); ?>"
                                     class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 truncate"><?php echo htmlspecialchars($product['title']); ?></h4>
                                <p class="text-sm text-gray-600"><?php echo number_format($product['price']); ?> FCFA</p>
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span><?php echo $product['views_count']; ?> views</span>
                                    <span><?php echo $product['inquiries_count']; ?> inquiries</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $product['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="index.php?page=vendor-add-product" class="card p-6 hover:shadow-lg transition-all duration-200 text-center">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">Add Product</h3>
                <p class="text-sm text-gray-600">List a new product</p>
            </a>
            
            <a href="index.php?page=vendor-products" class="card p-6 hover:shadow-lg transition-all duration-200 text-center">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">Manage Products</h3>
                <p class="text-sm text-gray-600">Edit and organize listings</p>
            </a>
            
            <a href="index.php?page=vendor-pricing" class="card p-6 hover:shadow-lg transition-all duration-200 text-center">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900">Upgrade Plan</h3>
                <p class="text-sm text-gray-600">Get more features</p>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
