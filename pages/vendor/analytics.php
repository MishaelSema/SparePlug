<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Analytics Dashboard';
$user = new User();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();

// Check if user has premium subscription
$subscription = $db->fetch(
    "SELECT * FROM subscriptions WHERE vendor_id = ? AND status = 'active' AND plan_type IN ('basic', 'premium') ORDER BY expires_at DESC LIMIT 1",
    [$vendorProfile['id']]
);

if (!$subscription) {
    $_SESSION['error'] = 'Analytics is only available for Basic and Premium subscribers.';
    header('Location: index.php?page=vendor-pricing');
    exit;
}

// Get analytics data
$analytics = [
    'total_products' => $db->fetch("SELECT COUNT(*) as count FROM products WHERE vendor_id = ?", [$vendorProfile['id']])['count'],
    'active_products' => $db->fetch("SELECT COUNT(*) as count FROM products WHERE vendor_id = ? AND is_active = 1", [$vendorProfile['id']])['count'],
    'total_views' => $db->fetch("SELECT SUM(views_count) as count FROM products WHERE vendor_id = ?", [$vendorProfile['id']])['count'] ?? 0,
    'total_inquiries' => $db->fetch("SELECT COUNT(*) as count FROM product_inquiries pi JOIN products p ON pi.product_id = p.id WHERE p.vendor_id = ?", [$vendorProfile['id']])['count'],
    'avg_rating' => $db->fetch("SELECT AVG(r.rating) as avg FROM reviews r JOIN products p ON r.product_id = p.id WHERE p.vendor_id = ?", [$vendorProfile['id']])['avg'] ?? 0,
    'total_reviews' => $db->fetch("SELECT COUNT(*) as count FROM reviews r JOIN products p ON r.product_id = p.id WHERE p.vendor_id = ?", [$vendorProfile['id']])['count']
];

// Get monthly data for charts (last 6 months)
$monthlyData = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $monthlyData[] = [
        'month' => date('M Y', strtotime("-$i months")),
        'views' => $db->fetch("SELECT SUM(views_count) as count FROM products WHERE vendor_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?", [$vendorProfile['id'], $date])['count'] ?? 0,
        'inquiries' => $db->fetch("SELECT COUNT(*) as count FROM product_inquiries pi JOIN products p ON pi.product_id = p.id WHERE p.vendor_id = ? AND DATE_FORMAT(pi.created_at, '%Y-%m') = ?", [$vendorProfile['id'], $date])['count']
    ];
}

// Get top products by views
$topProducts = $db->fetchAll(
    "SELECT title, views_count, inquiries_count FROM products WHERE vendor_id = ? AND is_active = 1 ORDER BY views_count DESC LIMIT 5",
    [$vendorProfile['id']]
);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Analytics Dashboard</h1>
        <p class="text-gray-600">Track your business performance and growth</p>
    </div>
    
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($analytics['total_products']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-success rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Products</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($analytics['active_products']); ?></p>
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
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($analytics['total_views']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Inquiries</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($analytics['total_inquiries']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Rating</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($analytics['avg_rating'], 1); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($analytics['total_reviews']); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Monthly Performance Chart -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Performance</h3>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Products by Views</h3>
            <?php if (empty($topProducts)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500">No products with views yet</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($topProducts as $index => $product): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo htmlspecialchars($product['title']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo number_format($product['inquiries_count']); ?> inquiries</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900"><?php echo number_format($product['views_count']); ?></p>
                                <p class="text-sm text-gray-500">views</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Conversion Rate -->
    <div class="mt-8">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversion Metrics</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary mb-2">
                        <?php echo $analytics['total_views'] > 0 ? number_format(($analytics['total_inquiries'] / $analytics['total_views']) * 100, 2) : 0; ?>%
                    </div>
                    <p class="text-gray-600">View to Inquiry Rate</p>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-accent mb-2">
                        <?php echo $analytics['total_products'] > 0 ? number_format($analytics['total_views'] / $analytics['total_products'], 0) : 0; ?>
                    </div>
                    <p class="text-gray-600">Avg Views per Product</p>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-success mb-2">
                        <?php echo $analytics['total_products'] > 0 ? number_format($analytics['total_inquiries'] / $analytics['total_products'], 1) : 0; ?>
                    </div>
                    <p class="text-gray-600">Avg Inquiries per Product</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Export Data -->
    <div class="mt-8 text-center">
        <button onclick="exportData()" class="btn-secondary">
            Export Analytics Data
        </button>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Monthly Performance Chart
const ctx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($monthlyData, 'month')); ?>,
        datasets: [{
            label: 'Views',
            data: <?php echo json_encode(array_column($monthlyData, 'views')); ?>,
            borderColor: '#DC2626',
            backgroundColor: 'rgba(220, 38, 38, 0.1)',
            tension: 0.4
        }, {
            label: 'Inquiries',
            data: <?php echo json_encode(array_column($monthlyData, 'inquiries')); ?>,
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});

// Export data function
function exportData() {
    const data = {
        analytics: <?php echo json_encode($analytics); ?>,
        monthlyData: <?php echo json_encode($monthlyData); ?>,
        topProducts: <?php echo json_encode($topProducts); ?>,
        exportDate: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'spareplug-analytics-' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>



