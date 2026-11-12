<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'My Profile';
$user = new User();

if (!$user->isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

$currentUser = $user->getCurrentUser();
$vendorProfile = null;

if ($user->isVendor()) {
    $vendorProfile = $user->getVendorProfile();
}

$errors = [];
$success = '';

if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $updateData = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ];
    
    if (empty($updateData['first_name']) || empty($updateData['last_name'])) {
        $errors[] = 'Name fields are required';
    }
    
    if (empty($errors)) {
        $db->update('users', $updateData, 'id = ?', [$_SESSION['user_id']]);
        $_SESSION['first_name'] = $updateData['first_name'];
        $_SESSION['last_name'] = $updateData['last_name'];
        $success = 'Profile updated successfully!';
        $currentUser = array_merge($currentUser, $updateData);
    }
}

ob_start();
?>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-600">Manage your account settings</p>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <div class="card p-8">
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($currentUser['first_name'] ?? ''); ?>" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($currentUser['last_name'] ?? ''); ?>" class="input-field" required>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" class="input-field bg-gray-50" disabled>
                <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" class="input-field" placeholder="+237 XXX XXX XXX">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                <div class="input-field bg-gray-50">
                    <?php if ($user->isVendor()): ?>
                        <span class="text-green-600 font-medium">Vendor - <?php echo htmlspecialchars($vendorProfile['business_name'] ?? 'Shop'); ?></span>
                        <a href="index.php?page=vendor-dashboard" class="text-primary hover:underline ml-2">→ Dashboard</a>
                    <?php else: ?>
                        <span class="text-gray-600">Buyer</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
