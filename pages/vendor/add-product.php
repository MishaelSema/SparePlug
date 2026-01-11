<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/Mailer.php';

$pageTitle = 'Add Product';
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

// Check subscription limits
$currentProductCount = $db->fetch(
    "SELECT COUNT(*) as count FROM products WHERE vendor_id = ? AND is_active = 1",
    [$vendorId]
)['count'];

$subscriptionPlan = $vendorProfile['subscription_plan'] ?? 'free';
$maxProducts = [
    'free' => 10,
    'basic' => 50,
    'premium' => 999999 // Unlimited for practical purposes
];
$productLimit = $maxProducts[$subscriptionPlan] ?? 10;
$remainingProducts = max(0, $productLimit - $currentProductCount);

// Check if vendor can add more products
$canAddProduct = $currentProductCount < $productLimit;

// Get categories for dropdown
$categories = $category->getAll();

// Vehicle makes will be loaded via NHTSA API in the frontend

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    $errors = [];
    
    // Check if vendor can add more products
    if (!$canAddProduct) {
        $errors[] = 'You have reached your product listing limit. Please upgrade your plan to add more products.';
    }
    
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
            $productData = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'category_id' => $_POST['category_id'],
                'condition' => $_POST['condition'],
                'compatibility' => json_encode([
                    'make' => $_POST['make_name'] ?? null,
                    'model' => $_POST['model_name'] ?? null,
                    'year' => $_POST['year'] ?? null
                ]),
                'images' => '["assets/placeholder-product.jpg"]' // Default
            ];
            
            // Handle Multiple Image Uploads
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $uploadDir = 'uploads/products/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $uploadedImages = [];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'jfif'];
                
                foreach ($_FILES['images']['name'] as $key => $name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileExtension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $newFilename = uniqid('prod_') . '_' . $key . '.' . $fileExtension;
                            $targetPath = $uploadDir . $newFilename;
                            
                            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetPath)) {
                                $uploadedImages[] = $targetPath;
                            }
                        }
                    }
                }
                
                if (!empty($uploadedImages)) {
                    $productData['images'] = json_encode($uploadedImages);
                }
            }
            
            $productId = $product->create($productData);
            
            if ($productId) {
                // Send email notification to vendor
                $mailer = new Mailer();
                $mailer->sendProductNotification($_SESSION['email'], $vendorProfile['business_name'], $_POST['title']);
                
                $_SESSION['success'] = 'Product added successfully!';
                header('Location: index.php?page=vendor-products');
                exit;
            } else {
                $errors[] = 'Failed to add product. Please try again.';
            }
        } catch (Exception $e) {
            $errors[] = 'An error occurred: ' . $e->getMessage();
        }
    }
}

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Product</h1>
                <p class="text-gray-600">List a new auto part for sale</p>
            </div>
            <div class="text-right">
                <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                    <p class="text-sm text-blue-700">
                        <span class="font-semibold">Products:</span> 
                        <?php echo $currentProductCount; ?> / <?php echo $productLimit == 999999 ? 'Unlimited' : $productLimit; ?>
                    </p>
                    <?php if ($subscriptionPlan !== 'premium'): ?>
                    <p class="text-xs text-blue-600 mt-1">
                        <?php echo $remainingProducts; ?> remaining
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!$canAddProduct): ?>
    <!-- Subscription Limit Warning -->
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold">Product Limit Reached</p>
                <p class="text-sm mt-1">
                    You've reached your limit of <?php echo $productLimit; ?> products on the <?php echo ucfirst($subscriptionPlan); ?> plan. 
                    <a href="index.php?page=vendor-pricing" class="underline font-medium">Upgrade your plan</a> to list more products.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Form -->
    <div class="card p-2">
        <?php if (!$canAddProduct): ?>
        <div class="bg-gray-50 p-8 text-center border border-gray-200 rounded-lg">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-gray-600 mb-4">You've reached your product listing limit.</p>
            <a href="index.php?page=vendor-pricing" class="btn-primary inline-block">
                Upgrade Plan
            </a>
        </div>
        <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action" value="add_product">
            
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Title *</label>
                        <input type="text" name="title" required 
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                               class="input-field" 
                               placeholder="e.g., Toyota Corolla Brake Pads">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" required class="input-field">
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Condition *</label>
                        <select name="condition" required class="input-field">
                            <option value="">Select Condition</option>
                            <option value="new" <?php echo ($_POST['condition'] ?? '') === 'new' ? 'selected' : ''; ?>>New</option>
                            <option value="used" <?php echo ($_POST['condition'] ?? '') === 'used' ? 'selected' : ''; ?>>Used</option>
                            <option value="refurbished" <?php echo ($_POST['condition'] ?? '') === 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price (FCFA) *</label>
                        <input type="number" name="price" required min="0" step="100"
                               value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
                               class="input-field" 
                               placeholder="50000">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" rows="4" required 
                                  class="input-field" 
                                  placeholder="Describe the product, its condition, and any important details"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Compatibility -->
            <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Vehicle Compatibility</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5 ml-1">Year</label>
                        <select name="year" id="year-select" class="input-field" onchange="checkSelection()">
                            <option value="">Select Year</option>
                            <?php 
                            $currentYear = date('Y');
                            for ($y = $currentYear; $y >= 1990; $y--) {
                                $selected = ($_POST['year'] ?? '') == $y ? 'selected' : '';
                                echo "<option value=\"$y\" $selected>$y</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5 ml-1">Make</label>
                        <div class="relative">
                            <select name="make_name" id="make-select" class="input-field pr-10" onchange="loadModels(this.value)">
                                <option value="">Select Make</option>
                            </select>
                            <div id="make-loader" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                <div class="w-4 h-4 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>
                        <p id="make-error" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5 ml-1">Model</label>
                        <div class="relative">
                            <select name="model_name" id="model-select" class="input-field pr-10">
                                <option value="">Select Model</option>
                            </select>
                            <div id="model-loader" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                                <div class="w-4 h-4 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>
                        <p id="model-error" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>
                </div>
            </div>
            
            <!-- Images -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Images (Multiple)</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-accent transition-all duration-300 group bg-white">
                    <input type="file" name="images[]" id="image-upload" class="hidden" accept="image/*" multiple onchange="previewImages(this)">
                    <label for="image-upload" class="cursor-pointer block">
                        <div id="images-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-4 hidden">
                            <!-- Previews will be injected here -->
                        </div>
                        <div id="upload-placeholder" class="py-4">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-8 h-8 text-gray-400 group-hover:text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-gray-900 mb-1 font-semibold">Click to upload images</p>
                            <p class="text-xs text-gray-500">You can select multiple photos (JPG, PNG, WebP)</p>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="index.php?page=vendor-products" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" id="submit-btn" class="btn-primary opacity-50 cursor-not-allowed" disabled>
                    Add Product
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
function previewImages(input) {
    const grid = document.getElementById('images-grid');
    const placeholder = document.getElementById('upload-placeholder');
    
    grid.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        grid.classList.remove('hidden');
        placeholder.classList.add('hidden');
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group aspect-square rounded-xl overflow-hidden bg-gray-100 border border-gray-200';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <span class="text-white text-[10px] font-bold uppercase tracking-wider bg-black/50 px-2 py-1 rounded">Image ${index + 1}</span>
                    </div>
                `;
                grid.appendChild(div);
            }
            
            reader.readAsDataURL(file);
        });
    } else {
        grid.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
    validateForm();
}

function validateForm() {
    const btn = document.getElementById('submit-btn');
    const form = document.querySelector('form');
    
    const requiredFields = [
        'title', 'category_id', 'condition', 'price', 'description', 
        'year', 'make_name', 'model_name'
    ];
    
    let isValid = true;
    
    // Check text/select fields
    requiredFields.forEach(name => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field || !field.value.trim()) {
            isValid = false;
        }
    });
    
    // Check images
    const imagesInput = document.getElementById('image-upload');
    if (!imagesInput.files || imagesInput.files.length === 0) {
        isValid = false;
    }
    
    if (isValid) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

const API_BASE_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles';

function loadMakes() {
    const makeSelect = document.getElementById('make-select');
    const makeLoader = document.getElementById('make-loader');
    const makeError = document.getElementById('make-error');
    
    makeSelect.disabled = true;
    makeLoader.classList.remove('hidden');
    makeError.classList.add('hidden');
    
    // We try to fetch makes for passenger cars first as it's a smaller, more relevant dataset
    // Fallback to GetAllMakes if needed
    fetch(`${API_BASE_URL}/GetMakesForVehicleType/passenger%20car?format=json`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (!data.Results) throw new Error('No results found');
            
            makeSelect.innerHTML = '<option value="">Select Make</option>';
            const sortedMakes = data.Results.sort((a, b) => a.MakeName.localeCompare(b.MakeName));
            
            sortedMakes.forEach(make => {
                const option = document.createElement('option');
                option.value = make.MakeName;
                option.textContent = make.MakeName;
                makeSelect.appendChild(option);
            });
            
            makeSelect.disabled = false;
            makeLoader.classList.add('hidden');
            
            // Re-select if needed
            const savedMake = "<?php echo $_POST['make_name'] ?? ''; ?>";
            if (savedMake) {
                makeSelect.value = savedMake;
                loadModels(savedMake);
            }
        })
        .catch(error => {
            console.error('Error fetching makes:', error);
            makeError.textContent = 'Failed to load makes. Please refresh.';
            makeError.classList.remove('hidden');
            makeLoader.classList.add('hidden');
            makeSelect.innerHTML = '<option value="">Error Loading</option>';
        });
}

function loadModels(makeName) {
    const modelSelect = document.getElementById('model-select');
    const modelLoader = document.getElementById('model-loader');
    const modelError = document.getElementById('model-error');
    const yearSelect = document.getElementById('year-select');
    const year = yearSelect.value;
    
    if (!makeName) {
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        return;
    }
    
    modelSelect.disabled = true;
    modelLoader.classList.remove('hidden');
    modelError.classList.add('hidden');
    
    // Using modelyear if available makes results more accurate
    let url = `${API_BASE_URL}/GetModelsForMake/${encodeURIComponent(makeName)}?format=json`;
    if (year) {
        url = `${API_BASE_URL}/GetModelsForMakeYear/make/${encodeURIComponent(makeName)}/modelyear/${year}?format=json`;
    }
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            if (data.Results && data.Results.length > 0) {
                const sortedModels = data.Results.sort((a, b) => a.Model_Name.localeCompare(b.Model_Name));
                sortedModels.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.Model_Name;
                    option.textContent = model.Model_Name;
                    modelSelect.appendChild(option);
                });
            } else {
                modelSelect.innerHTML = '<option value="">No models found</option>';
            }
            
            modelSelect.disabled = false;
            modelLoader.classList.add('hidden');
            
            const savedModel = "<?php echo $_POST['model_name'] ?? ''; ?>";
            if (savedModel) {
                modelSelect.value = savedModel;
            }
        })
        .catch(error => {
            console.error('Error fetching models:', error);
            modelError.textContent = 'Failed to load models.';
            modelError.classList.remove('hidden');
            modelLoader.classList.add('hidden');
            modelSelect.innerHTML = '<option value="">Error Loading</option>';
        });
}

function checkSelection() {
    const makeSelect = document.getElementById('make-select');
    if (makeSelect.value) {
        loadModels(makeSelect.value);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadMakes();
    
    // Add validation listeners
    const form = document.querySelector('form');
    const fields = form.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        field.addEventListener('input', validateForm);
        field.addEventListener('change', validateForm);
    });
    
    // Initial validation check
    validateForm();
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>




