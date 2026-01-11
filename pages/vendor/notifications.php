<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$user = new User();
$notification = new Notification();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=login');
    exit;
}

$currentUser = $user->getCurrentUser();
$notifications = $notification->getForUser($currentUser['id']);

// Mark all as read if requested
if (isset($_GET['mark_read'])) {
    foreach ($notifications as $n) {
        if (!$n['is_read']) {
            $notification->markAsRead($n['id']);
        }
    }
    header('Location: index.php?page=vendor-notifications');
    exit;
}

$pageTitle = 'Notifications';
ob_start();
?>

<div class="p-8 max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600">Stay updated on your shop activity</p>
        </div>
        <?php if (!empty($notifications)): ?>
        <a href="index.php?page=vendor-notifications&mark_read=1" class="text-sm font-semibold text-accent hover:underline">
            Mark all as read
        </a>
        <?php endif; ?>
    </div>

    <div class="space-y-4">
        <?php if (empty($notifications)): ?>
            <div class="card p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">All caught up!</h3>
                <p class="text-gray-600">You don't have any notifications at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div class="card p-5 flex gap-4 <?php echo !$n['is_read'] ? 'border-l-4 border-accent bg-accent/5' : 'bg-white'; ?> transition-all hover:shadow-md">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center <?php 
                            echo $n['type'] === 'success' ? 'bg-green-100 text-green-600' : 
                                ($n['type'] === 'warning' ? 'bg-yellow-100 text-yellow-600' : 
                                ($n['type'] === 'error' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600')); 
                        ?>">
                            <?php if ($n['type'] === 'success'): ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <?php elseif ($n['type'] === 'warning'): ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <?php else: ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($n['message']); ?></p>
                            <span class="text-xs text-gray-500 whitespace-nowrap ml-4">
                                <?php echo date('M j, H:i', strtotime($n['created_at'])); ?>
                            </span>
                        </div>
                        <?php if (!$n['is_read']): ?>
                            <button onclick="markRead(<?php echo $n['id']; ?>)" class="text-xs font-bold text-accent mt-2 hover:underline">Mark as read</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function markRead(id) {
    fetch('api/notifications.php?action=mark_read&id=' + id)
        .then(() => window.location.reload());
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
