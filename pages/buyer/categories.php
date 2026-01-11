<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Browse Categories';
$category = new Category();
$categories = $category->getAll();

ob_start();
?>

<!-- Hero Section -->
<div class="relative bg-white overflow-hidden">
    <div class="absolute inset-0">
         <div class="absolute inset-y-0 right-0 w-1/2 bg-gray-50 rounded-l-3xl transform translate-x-1/2 rotate-12"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl font-heading mb-4">
                Explore Auto Parts
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Browse our comprehensive catalog of high-quality spare parts for every vehicle make and model.
            </p>
        </div>
    </div>
</div>

<!-- Categories Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php 
        foreach ($categories as $cat): 
            $imgUrl = $cat['image_url'] ?: 'assets/placeholder-product.svg';
        ?>
        <a href="index.php?page=search&category=<?php echo $cat['id']; ?>" 
           class="group relative h-80 rounded-[2.5rem] overflow-hidden shadow-soft hover:shadow-2xl transition-all duration-500 hover:-translate-y-3">
            <img src="<?php echo $imgUrl; ?>" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
            
            <div class="absolute inset-0 p-8 flex flex-col justify-end">
                <div class="w-12 h-1 bg-accent mb-4 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                <h3 class="text-2xl font-bold text-white mb-2 group-hover:text-accent transition-colors duration-300">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </h3>
                <?php if (!empty($cat['description'])): ?>
                <p class="text-sm text-gray-300 line-clamp-2 transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-500">
                    <?php echo htmlspecialchars($cat['description']); ?>
                </p>
                <?php endif; ?>
                
                <div class="mt-4 flex items-center text-xs font-bold text-accent uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    Browse Catalog →
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- CTA Section -->
<div class="relative bg-white py-16 sm:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-primary rounded-3xl overflow-hidden shadow-xl lg:grid lg:grid-cols-2 lg:gap-4">
            <div class="pt-10 pb-12 px-6 sm:pt-16 sm:px-16 lg:py-16 lg:pr-0 xl:py-20 xl:px-20">
                <div class="lg:self-center">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                        <span class="block">Cant find what you need?</span>
                    </h2>
                    <p class="mt-4 text-lg leading-6 text-indigo-100">
                        Our verified vendors add new parts daily. Use our advanced search or contact support for help.
                    </p>
                    <a href="index.php?page=search" class="mt-8 bg-white border border-transparent rounded-lg shadow px-6 py-3 inline-flex items-center text-base font-medium text-primary hover:bg-gray-50 transition-colors">
                        Search All Parts
                    </a>
                </div>
            </div>
            <div class="relative -mt-6 aspect-w-5 aspect-h-3 md:aspect-w-2 md:aspect-h-1">
                <!-- Abstract pattern or image -->
                <div class="absolute inset-0 bg-gradient-to-br from-primary-dark to-primary opacity-50 transform translate-x-16 translate-y-16 rounded-full blur-3xl"></div>
                <div class="transform translate-x-6 translate-y-6 lg:translate-x-0 lg:translate-y-0 flex items-center justify-center">
                    <svg class="w-64 h-64 text-white opacity-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function for demo icons (in real app, this might be in functions.php)
function getCategoryIcon($iconName) {
    $icons = [
        'engine' => '⚙️',
        'brake' => '🛑',
        'suspension' => '🚙',
        'electrical' => '⚡',
        'body' => '🚗',
        'interior' => '💺',
        'wheels' => '🛞',
        'exhaust' => '💨'
    ];
    return $icons[$iconName] ?? '🔧';
}

$content = ob_get_clean();
include 'includes/layout.php';
?>
