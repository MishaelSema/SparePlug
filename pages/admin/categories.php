<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Manage Categories';
$user = new User();

// Check if user is admin
if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

$db = new Database();
$category = new Category();

// Handle Add Category
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add_category') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $slug = strtolower(str_replace(' ', '-', $name));
    
    // Handle Image Upload
    $imageUrl = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'cat_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imageUrl = $targetPath;
        } else {
            $_SESSION['admin_error'] = 'Failed to move uploaded file.';
        }
    }
    
    if ($name) {
        $db->insert('categories', [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'image_url' => $imageUrl,
            'is_active' => 1
        ]);
        $_SESSION['admin_success'] = 'Category added successfully!';
    }
}

// Handle Edit Category
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'edit_category') {
    $categoryId = $_POST['category_id'];
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    $data = [
        'name' => $name,
        'description' => $description,
        'is_active' => $isActive
    ];
    
    // Handle Image Upload if new one
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'cat_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $data['image_url'] = $targetPath;
        } else {
            $_SESSION['admin_error'] = 'Failed to move uploaded file.';
        }
    }
    
    $db->update('categories', $data, 'id = ?', [$categoryId]);
    $_SESSION['admin_success'] = 'Category updated successfully!';
    header('Location: index.php?page=admin-categories');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $db->delete('categories', 'id = ?', [$_GET['delete']]);
    $_SESSION['admin_success'] = 'Category deleted.';
    header('Location: index.php?page=admin-categories');
    exit;
}

$allCategories = $db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
?>

<div class="p-8 space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Categories</h1>
            <p class="text-gray-600 mt-1">Organize product categories for SparePlug</p>
        </div>
        <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="btn-primary">
            + Add New Category
        </button>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($allCategories as $cat): ?>
        <div class="bg-white rounded-3xl shadow-soft overflow-hidden border border-gray-100 group hover:shadow-lg transition-all">
            <div class="relative h-40">
                <img src="<?php echo $cat['image_url'] ?: 'assets/placeholder-product.svg'; ?>" 
                     alt="<?php echo htmlspecialchars($cat['name']); ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    <h3 class="text-lg font-bold text-white"><?php echo htmlspecialchars($cat['name']); ?></h3>
                </div>
                <div class="absolute top-4 right-4">
                    <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg <?php echo $cat['is_active'] ? 'bg-green-500 text-white' : 'bg-red-500 text-white'; ?>">
                        <?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </div>
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-500 line-clamp-2 mb-4"><?php echo htmlspecialchars($cat['description']); ?></p>
                <div class="flex items-center justify-between border-t border-gray-50 pt-4">
                    <button onclick='openEditModal(<?php echo json_encode($cat); ?>)' class="text-sm font-bold text-primary hover:text-accent transition-colors">Edit</button>
                    <a href="index.php?page=admin-categories&delete=<?php echo $cat['id']; ?>" 
                       onclick="return confirm('Delete this category?')"
                       class="text-sm font-bold text-red-500 hover:text-red-700 transition-colors">Delete</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Add New Category</h2>
            <button onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="action" value="add_category">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Category Name</label>
                <input type="text" name="name" required class="input-modern" placeholder="e.g. Engine Parts">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="input-modern" placeholder="Brief description..."></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Category Image</label>
                <div class="relative group cursor-pointer border-2 border-dashed border-gray-200 rounded-2xl p-4 text-center hover:border-accent transition-colors">
                    <input type="file" name="image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2 group-hover:text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-xs text-gray-500">Click to upload or drag and drop</p>
                </div>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3.5 mt-4">Create Category</button>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Category</h2>
            <button onclick="document.getElementById('editCategoryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="action" value="edit_category">
            <input type="hidden" name="category_id" id="edit_cat_id">
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Category Name</label>
                <input type="text" name="name" id="edit_cat_name" required class="input-modern">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" id="edit_cat_description" rows="3" class="input-modern"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Change Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="input-modern">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="edit_cat_is_active">
                <label for="edit_cat_is_active" class="text-sm font-bold text-gray-700">Active Category</label>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3.5 mt-4">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openEditModal(cat) {
    document.getElementById('edit_cat_id').value = cat.id;
    document.getElementById('edit_cat_name').value = cat.name;
    document.getElementById('edit_cat_description').value = cat.description;
    document.getElementById('edit_cat_is_active').checked = cat.is_active == 1;
    document.getElementById('editCategoryModal').classList.remove('hidden');
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





