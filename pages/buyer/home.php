<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Home';
$user = new User();
$product = new Product();
$category = new Category();

// Fetch Data
$featuredProducts = $product->getAll(['featured' => true, 'limit' => 12]);
$categories = $category->getAll(['limit' => 6]);
// Vehicle makes are now loaded via API client-side

ob_start();
?>

<!-- Hero Section -->
<section class="relative bg-primary overflow-hidden">
    <!-- Background Image with Fade from Left to Right -->
    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-cover bg-right bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'); opacity: 0.8;"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/80 to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            <!-- Hero Content -->
            <div class="text-center lg:text-left space-y-8 animate-fade-in-up">
                <!-- Subtitle removed -->
                
                <h1 class="text-5xl lg:text-7xl font-bold text-white leading-tight font-heading">
                    <span data-translate="findPerfect">Find the Perfect</span> <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-500"><span data-translate="partsForYourRide">Parts for Your Ride</span></span>
                </h1>
                
                <p class="text-xl text-gray-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed" data-translate="connectDirect">
                    Connect directly with verified vendors. Get quality spare parts delivered or picked up instantly. fast, secure, and reliable.
                </p>
                
                <!-- Search & Filters (Premium Row) -->
                <div class="bg-white/95 backdrop-blur-md p-4 rounded-3xl shadow-2xl shadow-black/20 max-w-full lg:max-w-4xl mx-auto lg:mx-0 transform transition-all hover:shadow-glow/20 overflow-hidden">
                    <form action="index.php" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                        <input type="hidden" name="page" value="search">
                        
                        <!-- Year -->
                        <div class="relative min-w-0">
                            <select name="year" id="year-select" class="w-full pl-3 pr-8 py-3 bg-gray-100/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-accent transition-all appearance-none truncate">
                                <option value="" data-translate="year">Year</option>
                                <?php for($y = date('Y') + 1; $y >= 1990; $y--): ?>
                                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="absolute right-3 top-3.5 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        </div>

                        <!-- Make -->
                        <div class="relative min-w-0">
                            <select name="make" id="make-select" class="w-full pl-3 pr-8 py-3 bg-gray-100/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-accent transition-all appearance-none truncate">
                                <option value="" data-translate="make">Make</option>
                            </select>
                            <div id="make-loader" class="hidden absolute right-3 top-3.5">
                                <div class="w-4 h-4 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>

                        <!-- Model -->
                        <div class="relative min-w-0">
                            <select name="model" id="model-select" class="w-full pl-3 pr-8 py-3 bg-gray-100/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-accent transition-all appearance-none truncate">
                                <option value="" data-translate="model">Model</option>
                            </select>
                            <div id="model-loader" class="hidden absolute right-3 top-3.5">
                                <div class="w-4 h-4 border-2 border-accent border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        </div>

                        <!-- Part Name -->
                        <div class="relative col-span-1">
                            <input type="text" name="q" placeholder="Part name..." data-translate-placeholder="partName"
                                   class="w-full pl-4 pr-4 py-3 bg-gray-100/50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-accent transition-all">
                        </div>

                        <button type="submit" class="bg-accent hover:bg-accent-hover text-white py-3 rounded-2xl font-bold transition-all shadow-lg shadow-red-500/30 hover:-translate-y-0.5 active:translate-y-0 col-span-2 md:col-span-1">
                            <span data-translate="explore">Explore</span>
                        </button>
                    </form>
                </div>
                
                <!-- Trust Badges removed -->
            </div>

            <!-- Hero Visual Removed -->
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2 font-heading" data-translate="shopByCategory">Shop by Category</h2>
            <div class="w-16 h-1 bg-accent mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8 text-center">
            <?php foreach($categories as $cat): 
                $imgUrl = $cat['image_url'] ?: 'assets/placeholder-product.svg';
            ?>
                <a href="index.php?page=search&category=<?php echo $cat['id']; ?>" 
                   class="group relative h-64 rounded-[2rem] overflow-hidden shadow-soft hover:shadow-2xl transition-all duration-500 hover:-translate-y-3">
                    <img src="<?php echo $imgUrl; ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    
                    <div class="absolute inset-0 p-6 flex flex-col justify-end">
                        <span class="text-[10px] font-bold text-accent uppercase tracking-widest mb-2 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500" data-translate="exploreParts">Explore Parts</span>
                        <h3 class="font-bold text-white text-lg group-hover:text-accent transition-colors duration-300 leading-tight"><?php echo htmlspecialchars($cat['name']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popular Vehicle Makes -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2 font-heading" data-translate="popularVehicleMakes">Popular Vehicle Makes</h2>
            <div class="w-16 h-1 bg-accent mx-auto rounded-full"></div>
            <p class="text-gray-500 mt-4" data-translate="findPartsFor">Find parts for the most popular cars in Cameroon</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?php 
            $popularMakes = ['Toyota', 'Honda', 'Nissan', 'Ford', 'BMW', 'Mercedes', 'Hyundai', 'Kia', 'Peugeot', 'Renault'];
            $makeColors = [
                'Toyota' => 'bg-red-600',
                'Honda' => 'bg-blue-600', 
                'Nissan' => 'bg-red-500',
                'Ford' => 'bg-blue-700',
                'BMW' => 'bg-blue-900',
                'Mercedes' => 'bg-gray-700',
                'Hyundai' => 'bg-blue-500',
                'Kia' => 'bg-red-600',
                'Peugeot' => 'bg-blue-500',
                'Renault' => 'bg-yellow-500'
            ];
            foreach($popularMakes as $make): 
            ?>
                <a href="index.php?page=search&make=<?php echo urlencode($make); ?>" 
                   class="group flex items-center gap-4 p-4 rounded-2xl border border-gray-200 hover:border-accent hover:shadow-lg transition-all">
                    <div class="w-12 h-12 rounded-xl <?php echo $makeColors[$make] ?? 'bg-gray-600'; ?> flex items-center justify-center text-white font-bold text-lg shadow-md">
                        <?php echo substr($make, 0, 1); ?>
                    </div>
                    <span class="font-bold text-gray-900 group-hover:text-accent transition-colors"><?php echo $make; ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose SparePlug -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2 font-heading" data-translate="whyChooseSparePlug">Why Choose SparePlug</h2>
            <div class="w-16 h-1 bg-accent mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-3xl shadow-soft hover:shadow-xl transition-all text-center group">
                <div class="w-16 h-16 mx-auto mb-6 bg-accent/10 rounded-2xl flex items-center justify-center group-hover:bg-accent group-hover:text-white transition-colors">
                    <svg class="w-8 h-8 text-accent group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3" data-translate="verifiedVendors">Verified Vendors</h3>
                <p class="text-gray-500" data-translate="verifiedVendorsDesc">All sellers are verified to ensure authentic parts and trustworthy transactions.</p>
            </div>
            
            <div class="bg-white p-8 rounded-3xl shadow-soft hover:shadow-xl transition-all text-center group">
                <div class="w-16 h-16 mx-auto mb-6 bg-accent/10 rounded-2xl flex items-center justify-center group-hover:bg-accent group-hover:text-white transition-colors">
                    <svg class="w-8 h-8 text-accent group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3" data-translate="directCommunication">Direct Communication</h3>
                <p class="text-gray-500" data-translate="directCommunicationDesc">Chat directly with sellers via WhatsApp, phone, or in-app messaging.</p>
            </div>
            
            <div class="bg-white p-8 rounded-3xl shadow-soft hover:shadow-xl transition-all text-center group">
                <div class="w-16 h-16 mx-auto mb-6 bg-accent/10 rounded-2xl flex items-center justify-center group-hover:bg-accent group-hover:text-white transition-colors">
                    <svg class="w-8 h-8 text-accent group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3" data-translate="fairPricing">Fair Pricing</h3>
                <p class="text-gray-500" data-translate="fairPricingDesc">Compare prices across multiple vendors to get the best deal in XAF.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2 font-heading" data-translate="featuredParts">Featured Parts</h2>
                <div class="w-16 h-1 bg-accent rounded-full"></div>
            </div>
            <a href="index.php?page=search" class="text-accent font-semibold hover:text-red-700 flex items-center gap-1">
                <span data-translate="viewAll">View All</span> <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach($featuredProducts as $product): ?>
                <?php 
                $images = json_decode($product['images'], true);
                $mainImage = !empty($images) ? $images[0] : 'assets/placeholder-product.jpg';
                ?>
                <div class="group card-modern">
                    <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="relative block overflow-hidden rounded-t-2xl aspect-[4/3] bg-gray-100">
                        <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <!-- Premium Badge -->
                        <?php if($product['subscription_plan'] === 'premium'): ?>
                            <div class="absolute top-3 left-3">
                                <span class="bg-accent text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-lg flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    PREMIUM
                                </span>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <span class="text-xs font-bold text-accent"><?php echo ucfirst($product['condition']); ?></span>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-1 group-hover:text-accent transition-colors">
                            <a href="index.php?page=product&id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['title']); ?></a>
                        </h3>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex text-yellow-400 text-xs">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <svg class="w-3 h-3 <?php echo $i <= round((float)($product['avg_rating'] ?? 0)) ? 'fill-current' : 'text-gray-300'; ?>" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <?php endfor; ?>
                            </div>
                            <span class="text-[10px] font-bold text-gray-400">(<?php echo $product['review_count']; ?>)</span>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 font-bold mb-1"><?php echo htmlspecialchars($product['business_name']); ?></p>
                            <div class="flex flex-wrap gap-1">
                                <?php echo getVendorBadge($product['subscription_plan'], $product['is_verified']); ?>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                            <span class="text-xl font-bold text-primary"><?php echo number_format($product['price']); ?> <span class="text-xs">XAF</span></span>
                            <a href="index.php?page=product&id=<?php echo $product['id']; ?>" class="w-8 h-8 rounded-full bg-primary/5 flex items-center justify-center group-hover:bg-accent group-hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Information Section -->
<section class="py-12 bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 font-heading">Your Trusted Auto Parts Partner</h2>
            <p class="text-gray-600 leading-relaxed">
                Finding the right part shouldn't be a struggle. We connect you with a network of verified local vendors to ensure quality, reliability, and fair pricing for every search.
            </p>
        </div>
    </div>
</section>


<!-- Call to Action -->
<section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-primary-dark"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h2 class="text-4xl lg:text-5xl font-bold mb-6 font-heading" data-translate="readyToSell">Ready to Sell Your Parts?</h2>
        <p class="text-xl text-gray-300 mb-10 max-w-2xl mx-auto">Join hundreds of verified vendors and reach thousands of car owners across Cameroon today.</p>
        <a href="index.php?page=vendor-signup" class="inline-block bg-accent text-white font-bold text-lg px-10 py-4 rounded-xl shadow-glow hover:bg-accent-hover transform hover:-translate-y-1 transition-all duration-300" data-translate="startSellingNow">
            Start Selling Now
        </a>
    </div>
</section>

<script>
const API_BASE_URL = 'https://vpic.nhtsa.dot.gov/api/vehicles';

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
});

function loadMakes() {
    const makeSelect = document.getElementById('make-select');
    const loader = document.getElementById('make-loader');
    
    loader.classList.remove('hidden');
    
    fetch(`${API_BASE_URL}/GetMakesForVehicleType/passenger%20car?format=json`)
        .then(res => res.json())
        .then(data => {
            makeSelect.innerHTML = '<option value="">Make</option>';
            const sorted = data.Results.sort((a,b) => a.MakeName.localeCompare(b.MakeName));
            sorted.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.MakeName;
                opt.textContent = m.MakeName;
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
        modelSelect.innerHTML = '<option value="">Model</option>';
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
            modelSelect.innerHTML = '<option value="">Model</option>';
            if (data.Results) {
                const sorted = data.Results.sort((a,b) => a.Model_Name.localeCompare(b.Model_Name));
                sorted.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.Model_Name;
                    opt.textContent = m.Model_Name;
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
