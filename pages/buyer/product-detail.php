<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = new User();
$productObj = new Product();
$reviewObj = new Review();

$product = $productObj->getById($id);

if (!$product) {
    header('Location: index.php?page=404');
    exit;
}

$pageTitle = $product['title'];

// Increment views (session-based)
if (!isset($_SESSION['viewed_products'])) {
    $_SESSION['viewed_products'] = [];
}

if (!in_array($id, $_SESSION['viewed_products'])) {
    $db->query("UPDATE products SET views_count = views_count + 1 WHERE id = ?", [$id]);
    $_SESSION['viewed_products'][] = $id;
    // Refresh product data to show updated views
    $product['views_count']++;
}

// Check if current user is the owner
$isOwner = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['seller_user_id']) {
    $isOwner = true;
}

// Get seller info
$seller = $db->fetch(
    "SELECT vp.*, u.email, u.phone, u.first_name, u.last_name, u.created_at as member_since 
     FROM vendor_profiles vp 
     JOIN users u ON vp.user_id = u.id 
     WHERE vp.id = ?", 
    [$product['vendor_id']]
);

// Get reviews
$reviews = $reviewObj->getByProduct($id);
$avgRating = $reviewObj->getAverageRating($id);

// Handle Review Submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    if (!$user->isLoggedIn()) {
        $errors[] = 'You must be logged in to leave a review.';
    } elseif ($isOwner) {
        $errors[] = 'You cannot review your own product.';
    } else {
        $reviewData = [
            'product_id' => $id,
            'rating' => (int)$_POST['rating'],
            'comment' => $_POST['comment']
        ];
        
        if ($reviewObj->create($reviewData)) {
            $_SESSION['success_message'] = 'Review submitted successfully!';
            header("Location: index.php?page=product&id=$id");
            exit;
        } else {
            $errors[] = 'Failed to submit review. Please try again.';
        }
    }
}

ob_start();
?>

<!-- Breadcrumb -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="text-sm text-gray-500">
            <a href="index.php" class="hover:text-accent">Home</a> 
            <span class="mx-2">/</span>
            <a href="index.php?page=search&category=<?php echo $product['category_id']; ?>" class="hover:text-accent"><?php echo htmlspecialchars($product['category_name']); ?></a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium truncate"><?php echo htmlspecialchars($product['title']); ?></span>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Left Column: Images -->
        <div class="lg:col-span-2 space-y-6">
            <?php 
            $images = json_decode($product['images'], true);
            $mainImage = !empty($images) ? $images[0] : 'assets/placeholder-product.jpg';
            ?>
            
            <!-- Main Image -->
            <div class="bg-gray-100 rounded-3xl overflow-hidden aspect-[4/3] relative group shadow-sm">
                <img src="<?php echo htmlspecialchars($mainImage); ?>" 
                     id="mainImage" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute top-4 left-4">
                    <span class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-full text-sm font-bold shadow-sm">
                        <?php echo ucfirst($product['condition']); ?>
                    </span>
                </div>
            </div>
            
            <!-- Thumbnails -->
            <?php if (count($images) > 1): ?>
                <div class="grid grid-cols-4 gap-4">
                    <?php foreach($images as $img): ?>
                        <button onclick="document.getElementById('mainImage').src='<?php echo htmlspecialchars($img); ?>'" 
                                class="aspect-square rounded-xl overflow-hidden border-2 border-transparent hover:border-accent focus:border-accent transition-all">
                            <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Description -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mt-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 font-heading">Product Description</h2>
                <div class="prose prose-blue max-w-none text-gray-600 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                
                <!-- Compatibility -->
                <?php 
                $comp = json_decode($product['compatibility'], true);
                if (!empty($comp['make']) || !empty($comp['model']) || !empty($comp['year'])):
                ?>
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            Compatible Vehicle
                        </h3>
                        <div class="bg-gray-50 rounded-2xl p-4 flex flex-wrap gap-4 flex-col items-start md:flex-row">
                            <?php if(!empty($comp['year'])): ?>
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Year</span>
                                <span class="font-bold text-gray-900"><?php echo htmlspecialchars($comp['year']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($comp['make'])): ?>
                            <div class="flex flex-col md:border-l border-gray-200 md:pl-4">
                                <span class="text-xs text-gray-400">Make</span>
                                <span class="font-bold text-gray-900"><?php echo htmlspecialchars($comp['make']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($comp['model'])): ?>
                            <div class="flex flex-col md:border-l border-gray-200 md:pl-4">
                                <span class="text-xs text-gray-400">Model</span>
                                <span class="font-bold text-gray-900"><?php echo htmlspecialchars($comp['model']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Column: Info & Actions -->
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">
                
                <!-- Price & Title Card -->
                <div class="bg-white p-6 rounded-2xl shadow-card border border-gray-100">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 font-heading leading-tight">
                        <?php echo htmlspecialchars($product['title']); ?>
                    </h1>
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex text-yellow-500 text-sm">
                            <?php for($i=1; $i<=5; $i++) echo ($i <= round($avgRating['average'])) ? '★' : '<span class="text-gray-200">★</span>'; ?>
                        </div>
                        <span class="text-gray-400 text-sm border-l pl-4 border-gray-200"><?php echo count($reviews); ?> Reviews</span>
                        <span class="text-gray-400 text-sm border-l pl-4 border-gray-200"><?php echo $product['views_count']; ?> Views</span>
                    </div>
                    
                    <?php 
                    $isOwner = false;
                    if ($user->isLoggedIn() && $user->isVendor()) {
                        $currentVendor = $user->getVendorProfile();
                        $isOwner = ($currentVendor['id'] == $product['vendor_id']);
                    }
                    ?>

                    <?php if ($isOwner): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                            <p class="text-blue-700 text-sm font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                This is your product. You are viewing it as a customer.
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-8 space-y-3">
                        <button onclick="openWhatsApp('<?php echo $seller['phone']; ?>', 'Hi, I am interested in <?php echo htmlspecialchars($product['title']); ?>')" 
                                <?php echo $isOwner ? 'disabled title="You cannot buy your own product"' : ''; ?>
                                class="w-full <?php echo $isOwner ? 'bg-gray-300 cursor-not-allowed' : 'bg-[#25D366] hover:bg-[#128C7E]'; ?> text-white font-bold py-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            Chat on WhatsApp
                        </button>
                        
                        <div class="grid grid-cols-2 gap-3">
                             <button onclick="makeCall('<?php echo $seller['phone']; ?>')" 
                                     <?php echo $isOwner ? 'disabled' : ''; ?>
                                     class="py-3 rounded-xl border-2 <?php echo $isOwner ? 'border-gray-200 text-gray-400 cursor-not-allowed' : 'border-primary text-primary hover:bg-primary hover:text-white'; ?> font-bold transition-colors">
                                Call Seller
                            </button>
                            <!-- Inquiry Modal Trigger -->
                            <button onclick="<?php echo $isOwner ? 'return false;' : "document.getElementById('inquiryModal').classList.remove('hidden')"; ?>" 
                                    <?php echo $isOwner ? 'disabled' : ''; ?>
                                    class="py-3 rounded-xl <?php echo $isOwner ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?> font-bold transition-colors">
                                Send Message
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Seller Profile Card -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">Sold By</h3>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold text-2xl">
                            <?php echo strtoupper(substr($seller['business_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($seller['business_name']); ?></p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <?php echo getVendorBadge($seller['subscription_plan'], $seller['is_verified']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="capitalize"><?php echo htmlspecialchars($seller['city'] . ', ' . $seller['region']); ?></span>
                    </div>
                    
                    <a href="index.php?page=vendor-profile&id=<?php echo $seller['id']; ?>" class="block w-full text-center mt-4 text-accent font-medium hover:underline">
                        View Vendor Profile
                    </a>
                </div>
                
                <!-- Safety Tips -->
                <!-- Safety Tips (Safety Yellow) -->
                 <div class="bg-[#FFD700]/10 p-6 rounded-2xl border border-[#FFD700] relative overflow-hidden">
                    <div class="absolute -top-4 -right-4 text-[#FFD700] opacity-20">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.45l8.27 14.3H3.73L12 5.45zM11 16h2v2h-2v-2zm0-6h2v4h-2v-4z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Safety Tips
                    </h3>
                    <ul class="text-sm text-gray-700 space-y-3 relative z-10">
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 bg-accent rounded-full mt-1.5 shrink-0"></span>
                            <span>Inspect the item thoroughly before paying.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 bg-accent rounded-full mt-1.5 shrink-0"></span>
                            <span>Meet in a safe, public place for the handover.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 bg-accent rounded-full mt-1.5 shrink-0"></span>
                            <span>Avoid sending money in advance via transfer.</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="mt-16 bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 pb-8 border-b border-gray-100">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Customer Reviews</h2>
                <div class="flex items-center gap-4 flex-col md:flex-row">
                    <div class="flex text-yellow-500">
                        <?php for($i=1; $i<=5; $i++) echo ($i <= round($avgRating['average'])) ? '★' : '<span class="text-gray-200">★</span>'; ?>
                    </div>
                    <span class="text-gray-500 font-medium"><?php echo $avgRating['average']; ?> out of 5</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-gray-500"><?php echo $avgRating['total']; ?> Reviews</span>
                </div>
            </div>
            
            <?php if ($user->isLoggedIn()): ?>
                <?php if ($isOwner): ?>
                    <span class="text-blue-500 font-medium bg-blue-50 px-4 py-2 rounded-full">Owner View</span>
                <?php else: ?>
                    <button onclick="document.getElementById('reviewForm').classList.toggle('hidden')" class="btn-primary">
                        Write a Review
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <a href="index.php?page=login" class="text-accent font-semibold hover:underline">Log in to write a review</a>
            <?php endif; ?>
        </div>

        <!-- Review Form (Hidden by default) -->
        <?php if ($user->isLoggedIn() && !$isOwner): ?>
            <style>
                .star-rating {
                    display: flex;
                    flex-direction: row-reverse;
                    justify-content: flex-end;
                }
                .star-rating label {
                    cursor: pointer;
                    font-size: 2rem;
                    color: #e5e7eb;
                    transition: color 0.2s;
                }
                .star-rating input:checked ~ label,
                .star-rating label:hover,
                .star-rating label:hover ~ label {
                    color: #fbbf24;
                }
            </style>
            <form action="" method="POST" id="reviewForm" class="hidden mb-12 bg-gray-50 p-8 rounded-3xl border border-gray-100">
                <input type="hidden" name="action" value="submit_review">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="star-rating">
                            <?php for($i=5; $i>=1; $i--): ?>
                                <input type="radio" name="rating" id="star-<?php echo $i; ?>" value="<?php echo $i; ?>" required class="hidden">
                                <label for="star-<?php echo $i; ?>" class="hover:text-yellow-300">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Comment</label>
                        <textarea name="comment" rows="4" required class="input-field w-full p-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-accent outline-none" placeholder="What did you think of this product and seller?"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary">Post Review</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        </div>
        
        <?php if (empty($reviews)): ?>
            <div class="text-center py-10">
                <p class="text-gray-500 italic">No reviews yet. Be the first to share your experience!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php foreach($reviews as $review): ?>
                    <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 relative">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center font-bold text-primary shadow-sm">
                                <?php echo strtoupper(substr($review['first_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></h4>
                                <div class="flex text-yellow-500 text-xs">
                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $review['rating']) ? '★' : '<span class="text-gray-200">★</span>'; ?>
                                </div>
                            </div>
                            <span class="ml-auto text-xs text-gray-400"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></span>
                        </div>
                        <p class="text-gray-600 leading-relaxed italic">
                            "<?php echo htmlspecialchars($review['comment']); ?>"
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
let currentImageIndex = 0;
const images = <?php echo json_encode($images); ?>;
const mainImage = document.getElementById('mainImage');

function nextImage() {
    if (images.length <= 1) return;
    currentImageIndex = (currentImageIndex + 1) % images.length;
    mainImage.src = images[currentImageIndex];
}

// Auto-swipe every 5 seconds
let carouselInterval = setInterval(nextImage, 5000);

function setMainImage(src, index) {
    mainImage.src = src;
    currentImageIndex = index;
    // Reset interval on manual change
    clearInterval(carouselInterval);
    carouselInterval = setInterval(nextImage, 5000);
}

function openWhatsApp(phone, message) {
    // Record inquiry via API
    fetch('api/record-inquiry.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: <?php echo $product['id']; ?>,
            vendor_id: <?php echo $product['vendor_id']; ?>
        })
    }).catch(console.error); // Fire and forget interaction recording

    // Open WhatsApp
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
}

function makeCall(phone) {
    window.location.href = `tel:${phone}`;
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
