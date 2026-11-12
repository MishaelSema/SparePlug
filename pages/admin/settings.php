<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Payment Settings';
$user = new User();

if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

$db = new Database();

// Handle settings update
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_settings') {
    $settings = [
        'momo_number' => $_POST['momo_number'] ?? '',
        'momo_name' => $_POST['momo_name'] ?? '',
        'orange_number' => $_POST['orange_number'] ?? '',
        'orange_name' => $_POST['orange_name'] ?? '',
        'company_name' => $_POST['company_name'] ?? 'SparePlug',
        'company_phone' => $_POST['company_phone'] ?? ''
    ];
    
    foreach ($settings as $key => $value) {
        $db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?", [$key, $value, $value]);
    }
    
    $_SESSION['admin_success'] = 'Payment settings updated!';
    header('Location: index.php?page=admin-settings');
    exit;
}

// Get current settings
$settings = [];
$allSettings = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($allSettings as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

ob_start();
?>

<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Payment Settings</h1>
        <p class="text-gray-600 mt-1">Configure payment details shown to customers</p>
    </div>
    
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
            <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
        </div>
    <?php endif; ?>
    
    <div class="card p-8 max-w-2xl">
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update_settings">
            
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Payment Details</p>
                        <p>These details will be shown to customers when they place orders. They will pay directly to these numbers.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">MTN MoMo</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">MoMo Number</label>
                    <input type="tel" name="momo_number" value="<?php echo htmlspecialchars($settings['momo_number'] ?? ''); ?>" class="input-field" placeholder="+237 6XX XXX XXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                    <input type="text" name="momo_name" value="<?php echo htmlspecialchars($settings['momo_name'] ?? ''); ?>" class="input-field" placeholder="SparePlug Shop">
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2 mt-4">Orange Money</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Orange Number</label>
                    <input type="tel" name="orange_number" value="<?php echo htmlspecialchars($settings['orange_number'] ?? ''); ?>" class="input-field" placeholder="+237 6XX XXX XXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                    <input type="text" name="orange_name" value="<?php echo htmlspecialchars($settings['orange_name'] ?? ''); ?>" class="input-field" placeholder="SparePlug Shop">
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2 mt-4">Company Info</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company/Shop Name</label>
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($settings['company_name'] ?? 'SparePlug'); ?>" class="input-field" placeholder="Your shop name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Support Phone</label>
                    <input type="tel" name="company_phone" value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>" class="input-field" placeholder="+237 6XX XXX XXX">
                </div>
            </div>
            
            <div class="flex justify-end pt-4">
                <button type="submit" class="btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
