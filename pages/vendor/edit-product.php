<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Edit Product';
$user = new User();
$product = new Product();
$category = new Category();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();
$vendorId = $vendorProfile['id'];

// Get product ID
$productId = $_GET['id'] ?? null;
if (!$productId) {
    header('Location: index.php?page=vendor-products');
    exit;
}

// Get product details
$productDetails = $db->fetch(
    "SELECT * FROM products WHERE id = ? AND vendor_id = ?",
    [$productId, $vendorId]
);

if (!$productDetails) {
    header('Location: index.php?page=vendor-products');
    exit;
}

$compatibility = json_decode($productDetails['compatibility'], true) ?? [];

// Get categories
$categories = $category->getAll();

// Get vehicle makes
$vehicleMakes = $db->fetchAll("SELECT * FROM vehicle_makes WHERE is_active = 1 ORDER BY name");

// Handle form submission
$errors = [];
$success = '';

if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_product') {
    // Validate required fields
    if (empty($_POST['title'])) $errors[] = 'Product title is required';
    if (empty($_POST['description'])) $errors[] = 'Product description is required';
    if (empty($_POST['price'])) $errors[] = 'Price is required';
    if (empty($_POST['category_id'])) $errors[] = 'Category is required';
    if (empty($_POST['condition'])) $errors[] = 'Condition is required';
    
    // Validate price
    if (!is_numeric($_POST['price']) || $_POST['price'] <= 0) {
        $errors[] = 'Price must be a valid positive number';
    }
    
    if (empty($errors)) {
        try {
            $updateData = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id'],
                'condition' => $_POST['condition'],
                'compatibility' => json_encode([
                    'make_id' => $_POST['make_id'] ?? null,
                    'model_id' => $_POST['model_id'] ?? null,
                    'year' => $_POST['year'] ?? null
                ])
            ];
            
            $db->update('products', $updateData, 'id = ? AND vendor_id = ?', [$productId, $vendorId]);
            
            $success = 'Product updated successfully!';
        } catch (Exception $e) {
            $errors[] = 'An error occurred. Please try again.';
        }
    }
}

// Get current product data
$currentData = $productDetails;
if (!empty($_POST)) {
    $currentData = array_merge($currentData, $_POST);
}

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Product</h1>
        <p class="text-gray-600">Update your product listing</p>
    </div>
    
    <!-- Form -->
    <div class="card p-8">
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update_product">
            
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Title *</label>
                        <input type="text" name="title" required 
                               value="<?php echo htmlspecialchars($currentData['title'] ?? ''); ?>"
                               class="input-field" 
                               placeholder="e.g., Toyota Corolla Brake Pads">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" required class="input-field">
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($currentData['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Condition *</label>
                        <select name="condition" required class="input-field">
                            <option value="">Select Condition</option>
                            <option value="new" <?php echo ($currentData['condition'] ?? '') === 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="used" <?php echo ($currentData['condition'] ?? '') === 'used' ? 'selected' : ''; ?>>Used</option>
                            <option value="refurbished" <?php echo ($currentData['condition'] ?? '') === 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price (FCFA) *</label>
                        <input type="number" name="price" required min="0" step="100"
                               value="<?php echo htmlspecialchars($currentData['price'] ?? ''); ?>"
                               class="input-field" 
                               placeholder="50000">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" rows="4" required 
                                  class="input-field" 
                                  placeholder="Describe the product, its condition, and any important details"><?php echo htmlspecialchars($currentData['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Compatibility -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vehicle Compatibility</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Make</label>
                        <select name="make_id" class="input-field" onchange="loadModels(this.value)">
                            <option value="">Select Make</option>
                            <?php foreach($vehicleMakes as $make): ?>
                                <option value="<?php echo $make['id']; ?>" 
                                        <?php echo ($compatibility['make_id'] ?? '') == $make['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($make['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                        <select name="model_id" id="model-select" class="input-field">
                            <option value="">Select Model</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                        <input type="number" name="year" min="1990" max="2024"
                               value="<?php echo htmlspecialchars($compatibility['year'] ?? ''); ?>"
                               class="input-field" 
                               placeholder="2020">
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="index.php?page=vendor-products" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loadModels(makeId) {
    const modelSelect = document.getElementById('model-select');
    
    if (makeId) {
        fetch(`api/get-models.php?make_id=${makeId}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="">Select Model</option>';
                data.forEach(model => {
                    const selected = model.id == <?php echo $compatibility['model_id'] ?? 'null'; ?> ? 'selected' : '';
                    modelSelect.innerHTML += `<option value="${model.id}" ${selected}>${model.name}</option>`;
                });
            })
            .catch(error => console.error('Error:', error));
    } else {
        modelSelect.innerHTML = '<option value="">Select Model</option>';
    }
}

// Load models if make is already selected
document.addEventListener('DOMContentLoaded', function() {
    const makeSelect = document.querySelector('select[name="make_id"]');
    const selectedModelId = <?php echo $compatibility['model_id'] ?? 'null'; ?>;
    
    if (makeSelect.value) {
        loadModels(makeSelect.value);
    }
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





