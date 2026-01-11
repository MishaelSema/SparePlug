<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Manage Products';
$user = new User();
$product = new Product();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();

// Handle bulk actions
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'bulk_action') {
    $action = $_POST['bulk_action'];
    $productIds = $_POST['product_ids'] ?? [];
    
    if (!empty($productIds) && !empty($action)) {
        foreach($productIds as $productId) {
            switch($action) {
                case 'activate':
                    $product->update($productId, ['is_active' => 1]);
                    break;
                case 'deactivate':
                    $product->update($productId, ['is_active' => 0]);
                    break;
                case 'delete':
                    $product->delete($productId);
                    break;
            }
        }
        $_SESSION['success'] = 'Bulk action completed successfully!';
    }
}

// Get vendor's products
$products = $product->getAll(['vendor_id' => $vendorProfile['id']]);

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Products</h1>
            <p class="text-gray-600">Manage your product listings</p>
        </div>
        <a href="index.php?page=vendor-add-product" class="btn-primary">
            Add New Product
        </a>
    </div>
    
    <!-- Bulk Actions -->
    <form method="POST" id="bulk-form" class="mb-6">
        <input type="hidden" name="action" value="bulk_action">
        <div class="flex items-center space-x-4">
            <select name="bulk_action" class="input-field" required>
                <option value="">Bulk Actions</option>
                <option value="activate">Activate Selected</option>
                <option value="deactivate">Deactivate Selected</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="submit" class="btn-secondary" onclick="return confirm('Are you sure?')">
                Apply
            </button>
        </div>
    
    <!-- Products Table -->
    <div class="card overflow-hidden">
        <?php if (empty($products)): ?>
            <div class="text-center py-12">
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
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inquiries</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach($products as $product): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="product_ids[]" value="<?php echo $product['id']; ?>" 
                                           class="product-checkbox rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex-shrink-0">
                                            <?php 
                                            $images = json_decode($product['images'], true);
                                            $mainImage = !empty($images) ? $images[0] : 'assets/placeholder-product.jpg';
                                            ?>
                                            <img src="<?php echo htmlspecialchars($mainImage); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['title']); ?>"
                                                 class="w-full h-full object-cover rounded-lg">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($product['title']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($product['category_name']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo number_format($product['price']); ?> FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo $product['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo number_format($product['views_count']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo number_format($product['inquiries_count']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M j, Y', strtotime($product['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="index.php?page=product&id=<?php echo $product['id']; ?>" 
                                           class="text-primary hover:text-red-700">View</a>
                                        <a href="index.php?page=vendor-edit-product&id=<?php echo $product['id']; ?>" 
                                           class="text-accent hover:text-yellow-700">Edit</a>
                                        <button onclick="deleteProduct(<?php echo $product['id']; ?>)" 
                                                class="text-red-600 hover:text-red-700">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    </form>
</div>

<script>
// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Update select all when individual checkboxes change
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.product-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
        const selectAll = document.getElementById('select-all');
        
        selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
        selectAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
    });
});

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="bulk_action">
            <input type="hidden" name="bulk_action" value="delete">
            <input type="hidden" name="product_ids[]" value="${productId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>




