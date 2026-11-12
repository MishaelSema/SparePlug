<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Route handling
switch($page) {
    case 'home':
        include 'pages/buyer/home.php';
        break;
    case 'search':
        include 'pages/buyer/search.php';
        break;
    case 'product':
        include 'pages/buyer/product-detail.php';
        break;
    case 'reviews':
        include 'pages/buyer/reviews.php';
        break;
    case 'login':
        include 'pages/auth/login.php';
        break;
    case 'register-buyer':
        include 'pages/auth/register-buyer.php';
        break;
    case 'forgot-password':
        include 'pages/auth/forgot-password.php';
        break;
    case 'reset-password':
        include 'pages/auth/reset-password.php';
        break;
    case 'verify-email':
        include 'pages/auth/verify-email.php';
        break;
    case 'verify-phone':
        include 'pages/auth/verify-phone.php';
        break;
    case 'profile':
        include 'pages/buyer/profile.php';
        break;
    case 'vendor-signup':
        include 'pages/vendor/signup.php';
        break;
    case 'vendor-verify':
        include 'pages/vendor/verify.php';
        break;
    case 'vendor-dashboard':
        include 'pages/vendor/dashboard.php';
        break;
    case 'vendor-products':
        include 'pages/vendor/products.php';
        break;
    case 'vendor-notifications':
        include 'pages/vendor/notifications.php';
        break;
    case 'vendor-add-product':
        include 'pages/vendor/add-product.php';
        break;
    case 'vendor-edit-product':
        include 'pages/vendor/edit-product.php';
        break;
    case 'vendor-edit-profile':
        include 'pages/vendor/edit-profile.php';
        break;
    case 'vendor-pricing':
        include 'pages/vendor/pricing.php';
        break;
    case 'vendor-profile':
        include 'pages/buyer/vendor-profile.php';
        break;
    case 'categories':
        include 'pages/buyer/categories.php';
        break;
    case 'my-orders':
        include 'pages/buyer/my-orders.php';
        break;
    case 'about':
        include 'pages/about.php';
        break;
    case 'contact':
        include 'pages/contact.php';
        break;
    case 'faq':
        include 'pages/faq.php';
        break;
    case 'safety-tips':
        include 'pages/safety-tips.php';
        break;
    case 'terms':
        include 'pages/terms.php';
        break;
        break;
    case 'vendor-subscription':
        include 'pages/vendor/subscription.php';
        break;
    case 'vendor-analytics':
        include 'pages/vendor/analytics.php';
        break;
    case 'vendor-inquiries':
        include 'pages/vendor/inquiries.php';
        break;
    case 'vendor-orders':
        include 'pages/vendor/orders.php';
        break;
    case 'order-detail':
        include 'pages/buyer/order-detail.php';
        break;
    case 'admin-dashboard':
        include 'pages/admin/dashboard.php';
        break;
    case 'admin-users':
        include 'pages/admin/users.php';
        break;
    case 'admin-products':
        include 'pages/admin/products.php';
        break;
    case 'admin-categories':
        include 'pages/admin/categories.php';
        break;
    case 'admin-subscriptions':
        include 'pages/admin/subscriptions.php';
        break;
    case 'admin-analytics':
        include 'pages/admin/analytics.php';
        break;
    case 'admin-settings':
        include 'pages/admin/settings.php';
        break;
    case 'admin-financials':
        include 'pages/admin/financials.php';
        break;
    default:
        // 404 Page
        http_response_code(404);
        $pageTitle = 'Page Not Found';
        ob_start();
        ?>
        <div class="min-h-[60vh] flex flex-col items-center justify-center text-center p-8">
            <h1 class="text-6xl font-bold text-gray-200 mb-4">404</h1>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Page Not Found</h2>
            <p class="text-gray-600 mb-8">The page you are looking for does not exist.</p>
            <a href="index.php" class="btn-primary">Go Home</a>
        </div>
        <?php
        $content = ob_get_clean();
        include 'includes/layout.php';
        break;
}
?>