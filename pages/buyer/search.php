<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Search Parts';
$product = new Product();
$category = new Category();

// Get filter parameters
$search = $_GET['q'] ?? '';
$catId = $_GET['category'] ?? '';
$year = $_GET['year'] ?? '';
$make = $_GET['make'] ?? '';
$model = $_GET['model'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$condition = $_GET['condition'] ?? '';
$location = $_GET['location'] ?? '';
$page = $_GET['p'] ?? 1;

// Build filters array
$filters = [
    'search' => $search,
    'category_id' => $catId,
    'year' => $year,
    'make' => $make,
    'model' => $model,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'condition' => $condition,
    'location' => $location,
    'page' => $page,
    'limit' => 12
];

// Get products
$results = $product->getAll($filters);
$allCategories = $category->getAll();
// Vehicle data now fetched from API client-side

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 animate-fade-in-up">
        <h1 class="text-3xl font-bold text-gray-900 font-heading">
            Search Results
            <?php if ($search): ?>
                <span class="text-gray-500 font-normal text-xl">for "<?php echo htmlspecialchars($search); ?>"</span>
            <?php endif; ?>
        </h1>
        <p class="text-gray-500 mt-2">Found <?php echo count($results); ?> parts matching your criteria</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-soft sticky top-24 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-bold text-lg text-gray-900">Filters</h2>
                    <a href="index.php?page=search" class="text-sm text-accent hover:underline">Reset</a>
                </div>

                <form action="index.php" method="GET" class="space-y-6">
                    <input type="hidden" name="page" value="search">
                    <?php if($search): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                    
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="input-modern bg-gray-50 text-sm">
                            <option value="">All Categories</option>
                            <?php foreach($allCategories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $catId == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Vehicle Compatibility -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                            <select name="year" id="year-select" class="input-modern bg-gray-50 text-sm">
                                <option value="">Any Year</option>
                                <?php for($y = date('Y') + 1; $y >= 1990; $y--): ?>
                                    <option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>>
                                        <?php echo $y; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="relative min-w-0">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Make</label>
                            <select name="make" id="make-select" class="input-modern bg-gray-50 text-sm truncate">
                                <option value="">Select Make</option>
                                <?php if($make): ?>
                                    <option value="<?php echo htmlspecialchars($make); ?>" selected><?php echo htmlspecialchars($make); ?></option>
                                <?php endif; ?>
                            </select>
                            <div id="make-loader" class="hidden absolute right-8 top-9">
                                <div class="w-4 h-4 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>
                        
                        <div class="relative min-w-0">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                            <select name="model" id="model-select" class="input-modern bg-gray-50 text-sm truncate">
                                <option value="">Select Model</option>
                                <?php if($model): ?>
                                    <option value="<?php echo htmlspecialchars($model); ?>" selected><?php echo htmlspecialchars($model); ?></option>
                                <?php endif; ?>
                            </select>
                            <div id="model-loader" class="hidden absolute right-8 top-9">
                                <div class="w-4 h-4 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range (FCFA)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($minPrice); ?>" class="input-modern py-2 text-sm">
                            <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($maxPrice); ?>" class="input-modern py-2 text-sm">
                        </div>
                    </div>

                    <!-- Condition -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Condition</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="condition" value="" <?php echo $condition === '' ? 'checked' : ''; ?> class="text-accent focus:ring-accent">
                                <span class="ml-2 text-sm text-gray-600">Any</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="condition" value="new" <?php echo $condition === 'new' ? 'checked' : ''; ?> class="text-accent focus:ring-accent">
                                <span class="ml-2 text-sm text-gray-600">New</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="condition" value="used" <?php echo $condition === 'used' ? 'checked' : ''; ?> class="text-accent focus:ring-accent">
                                <span class="ml-2 text-sm text-gray-600">Used</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full shadow-md mt-4">Apply Filters</button>
                </form>
            </div>
        </div>

        <!-- Results Grid -->
        <div class="lg:col-span-3">
            <?php if(empty($results)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center animate-fade-in-up">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No parts found</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-8">
                        We couldn't find any parts matching your specific criteria. Try adjusting your filters or search for something else.
                    </p>
                    <a href="index.php?page=search" class="btn-secondary inline-flex items-center">
                        Clear All Filters
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($results as $product): ?>
                        <?php 
                        $images = json_decode($product['images'], true);
                        $mainImage = !empty($images) ? $images[0] : 'assets/placeholder-product.jpg';
                        ?>
                        <div class="group card-modern flex flex-col h-full animate-fade-in-up">
                            <!-- Image -->
                            <div class="relative overflow-hidden rounded-t-2xl aspect-[4/3] bg-gray-100 shrink-0">
                                <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute top-3 right-3">
                                    <span class="bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                        <?php echo ucfirst($product['condition']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="mb-auto">
                                    <div class="flex text-yellow-400 text-xs mb-2">
                                        <?php 
                                        $rating = $product['avg_rating'] ?? 0;
                                        for($i=1; $i<=5; $i++) echo ($i <= $rating) ? '★' : '<span class="text-gray-200">★</span>'; 
                                        ?>
                                        <span class="text-gray-400 ml-1">(<?php echo $product['review_count'] ?? 0; ?>)</span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 text-lg leading-tight line-clamp-2 mb-2 group-hover:text-accent transition-colors">
                                        <a href="index.php?page=product&id=<?php echo $product['id']; ?>">
                                            <?php echo htmlspecialchars($product['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="text-xs text-gray-500 flex items-center gap-1 mb-2">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <?php echo htmlspecialchars($product['city']); ?>
                                    </p>
                                    <div class="flex flex-col gap-1">
                                        <p class="text-xs font-bold text-gray-700"><?php echo htmlspecialchars($product['business_name']); ?></p>
                                        <div class="flex flex-wrap gap-1">
                                            <?php echo getVendorBadge($product['subscription_plan'], $product['is_verified']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-lg font-bold text-primary"><?php echo number_format($product['price']); ?> <span class="text-xs font-normal text-gray-500">FCFA</span></span>
                                    <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="p-2 text-gray-400 hover:text-accent transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const API_BASE_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles';
const currentMake = '<?php echo htmlspecialchars($make); ?>';
const currentModel = '<?php echo htmlspecialchars($model); ?>';

document.addEventListener('DOMContentLoaded', function() {
    loadMakes();
    
    document.getElementById('make-select').addEventListener('change', function() {
        loadModels();
    });
    
    document.getElementById('year-select').addEventListener('change', function() {
        if (document.getElementById('make-select').value) {
            loadModels();
        }
    });

    if (currentMake) {
        loadModels();
    }
});

function loadMakes() {
    const makeSelect = document.getElementById('make-select');
    const loader = document.getElementById('make-loader');
    
    loader.classList.remove('hidden');
    
    fetch(`${API_BASE_URL}/GetMakesForVehicleType/passenger%20car?format=json`)
        .then(res => res.json())
        .then(data => {
            const currentVal = makeSelect.value;
            makeSelect.innerHTML = '<option value="">Select Make</option>';
            
            const sorted = data.Results.sort((a,b) => a.MakeName.localeCompare(b.MakeName));
            sorted.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.MakeName;
                opt.textContent = m.MakeName;
                if (m.MakeName === currentVal || m.MakeName === currentMake) opt.selected = true;
                makeSelect.appendChild(opt);
            });
        })
        .finally(() => loader.classList.add('hidden'));
}

function loadModels() {
    const make = document.getElementById('make-select').value;
    const year = document.getElementById('year-select').value;
    const modelSelect = document.getElementById('model-select');
    const loader = document.getElementById('model-loader');
    
    if (!make) {
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        return;
    }
    
    loader.classList.remove('hidden');
    
    let url = `${API_BASE_URL}/GetModelsForMake/${make}?format=json`;
    if (year) {
        url = `${API_BASE_URL}/GetModelsForMakeYear/make/${make}/modelyear/${year}?format=json`;
    }
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const currentVal = modelSelect.value;
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            
            if (data.Results) {
                const sorted = data.Results.sort((a,b) => a.Model_Name.localeCompare(b.Model_Name));
                sorted.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.Model_Name;
                    opt.textContent = m.Model_Name;
                    if (m.Model_Name === currentVal || m.Model_Name === currentModel) opt.selected = true;
                    modelSelect.appendChild(opt);
                });
            }
        })
        .finally(() => loader.classList.add('hidden'));
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
