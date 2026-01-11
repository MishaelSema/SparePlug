<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/Mailer.php';

$pageTitle = 'Manage Products';
$user = new User();

// Check if user is admin
if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

$db = new Database();
$product = new Product();

// Handle Actions
if (isset($_GET['delete'])) {
    $productId = $_GET['delete'];
    $prod = $db->fetch("SELECT * FROM products WHERE id = ?", [$productId]);
    if ($prod) {
        // Delete images
        $images = json_decode($prod['images'], true) ?: [];
        foreach ($images as $img) {
            if (file_exists($img)) unlink($img);
        }
        $db->delete('products', 'id = ?', [$productId]);
        
        // Notify vendor
        Notification::send($prod['vendor_id'], "Your product '{$prod['title']}' has been removed by an administrator.", 'warning');
        
        // Also send Email
        $vendor = $db->fetch("SELECT u.email FROM users u JOIN vendor_profiles vp ON u.id = vp.user_id WHERE vp.id = ?", [$prod['vendor_id']]);
        if ($vendor) {
            $mailer = new Mailer();
            $mailer->send($vendor['email'], "Product Removed: " . $prod['title'], "Your product '{$prod['title']}' has been removed by an administrator for non-compliance with our terms.");
        }
        
        $_SESSION['admin_success'] = 'Product deleted successfully.';
    }
    header('Location: index.php?page=admin-products');
    exit;
}

if ($_POST && isset($_POST['action']) && $_POST['action'] === 'edit_product') {
    $productId = $_POST['product_id'];
    $data = [
        'title' => $_POST['title'],
        'price' => $_POST['price'],
        'condition' => $_POST['condition'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'description' => $_POST['description']
    ];
    // Check if it was promoted to featured
    if ($data['is_featured'] && $productId) {
        $prodInfo = $db->fetch("SELECT p.title, u.email FROM products p JOIN vendor_profiles vp ON p.vendor_id = vp.id JOIN users u ON vp.user_id = u.id WHERE p.id = ?", [$productId]);
        if ($prodInfo) {
            $mailer = new Mailer();
            $mailer->sendPromotionNotification($prodInfo['email'], $prodInfo['title']);
        }
    }

    $db->update('products', $data, 'id = ?', [$productId]);
    $_SESSION['admin_success'] = 'Product updated successfully.';
    header('Location: index.php?page=admin-products');
    exit;
}

// Get all products
$allProducts = $db->fetchAll("
    SELECT p.*, c.name as category_name, vp.business_name, vp.user_id as vendor_user_id
    FROM products p
    JOIN categories c ON p.category_id = c.id
    JOIN vendor_profiles vp ON p.vendor_id = vp.id
    ORDER BY p.created_at DESC
");

ob_start();
?>

<div class="p-8 space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Products</h1>
        <p class="text-gray-600 mt-1">View and manage all product listings</p>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">All Products</h2>
                <span class="text-sm text-gray-500">Total: <?php echo count($allProducts); ?></span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($allProducts as $prod): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($prod['title']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($prod['category_name']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($prod['business_name']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo number_format($prod['price']); ?> XAF</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded <?php echo $prod['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $prod['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($prod['is_featured']): ?>
                                <span class="px-2 py-1 text-xs font-bold rounded bg-yellow-100 text-yellow-800 flex items-center gap-1 w-fit">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    Featured
                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs italic">Standard</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo number_format($prod['views_count']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick='openEditModal(<?php echo json_encode($prod); ?>)' class="text-primary hover:text-red-700 mr-3">Edit</button>
                            <a href="index.php?page=admin-products&delete=<?php echo $prod['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this product?')"
                               class="text-red-600 hover:text-red-800">Remove</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Product</h2>
            <button onclick="document.getElementById('editProductModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="edit_product">
            <input type="hidden" name="product_id" id="edit_product_id">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Product Title</label>
                <input type="text" name="title" id="edit_title" required class="input-modern">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Price (XAF)</label>
                    <input type="number" name="price" id="edit_price" required class="input-modern">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Condition</label>
                    <select name="condition" id="edit_condition" class="input-modern">
                        <option value="new">New</option>
                        <option value="used">Used</option>
                        <option value="refurbished">Refurbished</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" id="edit_description" rows="4" class="input-modern"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit_is_active">
                    <label for="edit_is_active" class="text-sm font-bold text-gray-700">Active Listing</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" id="edit_is_featured">
                    <label for="edit_is_featured" class="text-sm font-bold text-gray-700">Featured on Home</label>
                </div>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3.5 mt-4">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openEditModal(product) {
    document.getElementById('edit_product_id').value = product.id;
    document.getElementById('edit_title').value = product.title;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_condition').value = product.condition;
    document.getElementById('edit_description').value = product.description;
    document.getElementById('edit_is_active').checked = product.is_active == 1;
    document.getElementById('edit_is_featured').checked = product.is_featured == 1;
    document.getElementById('editProductModal').classList.remove('hidden');
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





