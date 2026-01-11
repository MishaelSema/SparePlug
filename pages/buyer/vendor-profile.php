<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$vendorId = $_GET['id'] ?? null;
if (!$vendorId) {
    header('Location: index.php?page=home');
    exit;
}

$user = new User();
$productObj = new Product();
$notification = new Notification(); // Triggers schema updates for logo_url and cover_url

// Fetch vendor profile
$vendor = $db->fetch(
    "SELECT vp.*, u.first_name, u.last_name, u.created_at as joined_date 
     FROM vendor_profiles vp 
     JOIN users u ON vp.user_id = u.id 
     WHERE vp.id = ?", 
    [$vendorId]
);

if (!$vendor) {
    header('Location: index.php?page=404');
    exit;
}

$pageTitle = $vendor['business_name'];

// Identify if current viewer is the owner
$isOwner = false;
$currentUser = $user->getCurrentUser();
if ($currentUser && $currentUser['id'] == $vendor['user_id']) {
    $isOwner = true;
}

// Handle Quick Edit Updates
if ($isOwner && $_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'quick_update_description') {
        $db->update('vendor_profiles', ['description' => $_POST['description']], 'id = ?', [$vendor['id']]);
        header("Location: index.php?page=vendor-profile&id=" . $vendor['id']);
        exit;
    }

    if ($_POST['action'] === 'quick_update_socials') {
        $data = [
            'website' => $_POST['website'],
            'facebook' => $_POST['facebook'],
            'instagram' => $_POST['instagram'],
            'twitter' => $_POST['twitter'],
            'google_map_url' => $_POST['google_map_url']
        ];
        $db->update('vendor_profiles', $data, 'id = ?', [$vendor['id']]);
        header("Location: index.php?page=vendor-profile&id=" . $vendor['id']);
        exit;
    }

    if ($_POST['action'] === 'quick_update_hours') {
        $newOperatingHours = [];
        if (isset($_POST['operating_days']) && isset($_POST['operating_hours'])) {
            $days = $_POST['operating_days'];
            $hours = $_POST['operating_hours'];
            $daysArr = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            
            foreach ($days as $day) {
                $dayIndex = array_search(ucfirst($day), $daysArr);
                if ($dayIndex !== false && isset($hours[$dayIndex])) {
                    $newOperatingHours[$day] = [
                        'open' => $hours[$dayIndex]['open'] ?? '08:00',
                        'close' => $hours[$dayIndex]['close'] ?? '17:00'
                    ];
                }
            }
        }
        $db->update('vendor_profiles', ['operating_hours' => json_encode($newOperatingHours)], 'id = ?', [$vendor['id']]);
        header("Location: index.php?page=vendor-profile&id=" . $vendor['id']);
        exit;
    }

    // Image Uploads (Existing)
    $targetDir = '';
    
    if ($_POST['action'] === 'quick_update_logo' && isset($_FILES['logo'])) {
        $targetDir = 'uploads/logos/';
        $prefix = 'logo_';
        $fileKey = 'logo';
        $dbKey = 'logo_url';
    } elseif ($_POST['action'] === 'quick_update_cover' && isset($_FILES['cover'])) {
        $targetDir = 'uploads/covers/';
        $prefix = 'cover_';
        $fileKey = 'cover';
        $dbKey = 'cover_url';
    }

    if ($targetDir && $_FILES[$fileKey]['error'] === 0) {
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
        $filename = $prefix . $vendor['id'] . '_' . time() . '.' . $ext;
        $targetPath = $targetDir . $filename;
        
        if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
            $db->update('vendor_profiles', [$dbKey => $targetPath], 'id = ?', [$vendor['id']]);
            header("Location: index.php?page=vendor-profile&id=" . $vendor['id']);
            exit;
        }
    }
}

// Get vendor's active products
$products = $productObj->getAll(['vendor_id' => $vendorId, 'is_active' => 1]);

ob_start();
?>

<div class="bg-gray-50 min-h-screen pb-20">
    <!-- Vendor Header -->
    <div class="relative min-h-[400px] flex items-center overflow-hidden">
        <!-- Cover Image -->
        <div class="absolute inset-0 z-0">
            <?php if(!empty($vendor['cover_url'])): ?>
                <img src="<?php echo $vendor['cover_url']; ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full bg-primary"></div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
        </div>

        <?php if($isOwner): ?>
        <!-- Quick Edit Cover -->
        <form id="quickCoverForm" method="POST" enctype="multipart/form-data" class="absolute top-8 right-8 z-20">
            <input type="hidden" name="action" value="quick_update_cover">
            <label class="btn-primary flex items-center gap-2 cursor-pointer shadow-2xl backdrop-blur-md bg-white/20 hover:bg-white/30 border border-white/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Change Cover
                <input type="file" name="cover" class="hidden" onchange="this.form.submit()">
            </label>
        </form>
        <?php endif; ?>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-20 pb-16">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- Store Logo -->
                <div class="relative group">
                    <div class="w-32 h-32 bg-white rounded-3xl flex items-center justify-center text-5xl font-bold text-accent shadow-2xl transform -rotate-3 hover:rotate-0 transition-transform duration-500 overflow-hidden">
                        <?php if(!empty($vendor['logo_url'])): ?>
                            <img src="<?php echo $vendor['logo_url']; ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?php echo strtoupper(substr($vendor['business_name'], 0, 1)); ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($isOwner): ?>
                    <!-- Quick Edit Logo -->
                    <form id="quickLogoForm" method="POST" enctype="multipart/form-data" class="absolute inset-0 z-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <input type="hidden" name="action" value="quick_update_logo">
                        <label class="w-full h-full flex items-center justify-center bg-black/40 cursor-pointer rounded-3xl">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <input type="file" name="logo" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>
                    <?php endif; ?>
                </div>
                
                <div class="text-center md:text-left text-white space-y-2">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <h1 class="text-4xl font-bold font-heading"><?php echo htmlspecialchars($vendor['business_name']); ?></h1>
                        <?php if($isOwner): ?>
                            <a href="index.php?page=vendor-edit-profile" class="text-xs bg-white/10 hover:bg-white/20 px-3 py-1 rounded-full border border-white/20 transition-colors w-max mx-auto md:mx-0">Edit Full Profile</a>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-300 flex items-center justify-center md:justify-start gap-2">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <?php echo htmlspecialchars($vendor['city']); ?>, <?php echo htmlspecialchars($vendor['region']); ?>
                    </p>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-4">
                        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-medium border border-white/20">Joined <?php echo date('M Y', strtotime($vendor['joined_date'])); ?></span>
                        <?php echo getVendorBadge($vendor['subscription_plan'], $vendor['is_verified']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <?php if (isset($_SESSION['vendor_success'])): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-3xl shadow-soft animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php 
                        echo $_SESSION['vendor_success']; 
                        unset($_SESSION['vendor_success']);
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-soft border border-gray-100 relative group/section">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-gray-900">About Shop</h2>
                        <?php if($isOwner): ?>
                        <button onclick="document.getElementById('editDescriptionModal').classList.remove('hidden')" class="p-2 bg-accent/5 text-accent hover:bg-accent hover:text-white rounded-xl transition-all shadow-sm" title="Edit Description">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($vendor['description'] ?? 'No description provided.')); ?>
                    </p>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-soft border border-gray-100 relative group/section">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-gray-900">Contact & Socials</h2>
                        <?php if($isOwner): ?>
                        <button onclick="document.getElementById('editSocialsModal').classList.remove('hidden')" class="p-2 bg-accent/5 text-accent hover:bg-accent hover:text-white rounded-xl transition-all shadow-sm" title="Edit Socials">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-4">
                        <?php if(!empty($vendor['website'])): ?>
                        <a href="<?php echo htmlspecialchars($vendor['website']); ?>" target="_blank" class="flex items-center gap-3 text-sm text-gray-600 hover:text-primary transition-colors">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            Official Website
                        </a>
                        <?php endif; ?>

                        <div class="flex gap-4 pt-2">
                            <?php if(!empty($vendor['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($vendor['facebook']); ?>" target="_blank" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.248h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <?php endif; ?>

                            <?php if(!empty($vendor['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($vendor['instagram']); ?>" target="_blank" class="w-10 h-10 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.805.249 2.227.412.56.216.96.474 1.38.894.42.42.678.82.894 1.38.163.422.358 1.057.412 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.249 1.805-.412 2.227-.216.56-.474.96-.894 1.38-.42.42-.82.678-1.38.894-.422.163-1.057.358-2.227.412-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.805-.249-2.227-.412-.56-.216-.96-.474-1.38-.894-.42-.42-.678-.82-.894-1.38-.163-.422-.358-1.057-.412-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.17.249-1.805.412-2.227.216-.56.474-.96.894-1.38.42-.42.82-.678 1.38-.894.422-.163 1.057-.358 2.227-.412 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-1.277.057-2.15.26-2.914.557-.79.307-1.458.718-2.122 1.381-.663.663-1.074 1.332-1.381 2.122-.298.763-.5 1.637-.557 2.914-.058 1.28-.072 1.688-.072 4.947s.014 3.667.072 4.947c.057 1.277.26 2.15.557 2.914.307.79.718 1.458 1.381 2.122.663.663 1.332 1.074 2.122 1.381.763.298 1.637.5 2.914.557 1.28.058 1.688.072 4.947.072s3.667-.014 4.947-.072c1.277-.057 2.150-.26 2.914-.557.79-.307 1.458-.718 2.122-1.381.663-.663 1.074-1.332 1.381-2.122.298-.763.5-1.637.557-2.914.058-1.28.072-1.688.072-4.947s-.014-3.667-.072-4.947c-.057-1.277-.26-2.15-.557-2.914-.307-.79-.718-1.458-1.381-2.122-.663-.663-1.332-1.074-2.122-1.381-.763-.298-1.637-.5-2.914-.557-1.28-.058-1.688-.072-4.947-.072z"/><path d="M12 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.162 6.162 6.162 6.162-2.759 6.162-6.162-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.791-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.441 1.441 1.441c.795 0 1.439-.645 1.439-1.441s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <?php endif; ?>

                            <?php if(!empty($vendor['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($vendor['twitter']); ?>" target="_blank" class="w-10 h-10 bg-sky-50 text-sky-500 rounded-xl flex items-center justify-center hover:bg-sky-500 hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-soft border border-gray-100 relative group/section">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-gray-900">Operating Hours</h2>
                        <?php if($isOwner): ?>
                        <button onclick="document.getElementById('editHoursModal').classList.remove('hidden')" class="p-2 bg-accent/5 text-accent hover:bg-accent hover:text-white rounded-xl transition-all shadow-sm" title="Edit Hours">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2">
                        <?php 
                        $hours = json_decode($vendor['operating_hours'], true) ?? [];
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        foreach($days as $day):
                            $isOpen = isset($hours[$day]);
                        ?>
                        <div class="flex justify-between text-sm">
                            <span class="capitalize text-gray-500"><?php echo $day; ?></span>
                            <?php if($isOpen): ?>
                                <span class="text-gray-900 font-medium"><?php echo $hours[$day]['open']; ?> - <?php echo $hours[$day]['close']; ?></span>
                            <?php else: ?>
                                <span class="text-red-500 font-medium text-xs bg-red-50 px-2 py-0.5 rounded-full">Closed</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if(!empty($vendor['google_map_url'])): ?>
                <div class="bg-white p-2 rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
                    <?php if(strpos($vendor['google_map_url'], 'google.com/maps/embed') !== false): ?>
                        <iframe 
                            src="<?php echo htmlspecialchars($vendor['google_map_url']); ?>" 
                            width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" 
                            class="rounded-xl">
                        </iframe>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($vendor['google_map_url']); ?>" target="_blank" 
                           class="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors group">
                            <svg class="w-12 h-12 text-accent mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-sm font-bold text-gray-900">View Shop Location</span>
                            <span class="text-xs text-blue-500 hover:underline">Open in Google Maps</span>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Products & Details -->
            <div class="lg:col-span-3 space-y-8">
                <?php if(!empty($vendor['services']) || !empty($vendor['other_info'])): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php if(!empty($vendor['services'])): ?>
                    <div class="bg-white p-8 rounded-3xl shadow-soft border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5">
                            <svg class="w-24 h-24 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-8 14H7v-2h4v2zm5-4H7v-2h9v2zm0-4H7V7h9v2z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span class="w-2 h-8 bg-accent rounded-full"></span>
                            Services Offered
                        </h2>
                        <div class="text-gray-600 text-sm leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($vendor['services'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($vendor['other_info'])): ?>
                    <div class="bg-accent/5 p-8 rounded-3xl border border-accent/20 relative">
                        <div class="absolute top-4 right-4 text-accent">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Good to Know</h2>
                        <div class="text-gray-700 text-sm leading-relaxed italic">
                            "<?php echo nl2br(htmlspecialchars($vendor['other_info'])); ?>"
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div>
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Listed Products (<?php echo count($products); ?>)</h2>
                    </div>

                <?php if (empty($products)): ?>
                    <div class="bg-white p-20 text-center rounded-3xl shadow-soft border border-gray-100">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No products found</h3>
                        <p class="text-gray-500">This vendor hasn't listed any products yet.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach($products as $p): ?>
                            <?php 
                            $images = json_decode($p['images'], true);
                            $mainImage = !empty($images) ? $images[0] : 'assets/placeholder-product.jpg';
                            ?>
                            <div class="group card-modern">
                                <div class="relative aspect-[4/3] overflow-hidden rounded-t-2xl">
                                    <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute top-3 right-3">
                                        <span class="bg-white/90 backdrop-blur-sm text-xs font-bold px-2 py-1 rounded-full text-gray-900 shadow-sm">
                                            <?php echo ucfirst($p['condition']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-bold text-gray-900 line-clamp-2 min-h-[3rem] group-hover:text-accent transition-colors">
                                        <a href="index.php?page=product&id=<?php echo $p['id']; ?>">
                                            <?php echo htmlspecialchars($p['title']); ?>
                                        </a>
                                    </h3>
                                    <div class="flex items-center justify-between mt-4">
                                        <span class="text-lg font-bold text-primary"><?php echo number_format($p['price']); ?> <span class="text-xs font-normal">FCFA</span></span>
                                        <a href="index.php?page=product&id=<?php echo $p['id']; ?>" class="p-2 bg-gray-50 hover:bg-accent hover:text-white rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
</div>


<!-- Edit Description Modal -->
<div id="editDescriptionModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Shop Description</h2>
            <button onclick="document.getElementById('editDescriptionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="quick_update_description">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="8" class="input-modern w-full" required><?php echo htmlspecialchars($vendor['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn-primary w-full py-3">Save Description</button>
        </form>
    </div>
</div>

<!-- Edit Socials Modal -->
<div id="editSocialsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Socials & Contact</h2>
            <button onclick="document.getElementById('editSocialsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="quick_update_socials">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Website URL</label>
                    <input type="url" name="website" value="<?php echo htmlspecialchars($vendor['website'] ?? ''); ?>" class="input-modern w-full" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Facebook URL</label>
                    <input type="url" name="facebook" value="<?php echo htmlspecialchars($vendor['facebook'] ?? ''); ?>" class="input-modern w-full" placeholder="https://facebook.com/...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Instagram URL</label>
                    <input type="url" name="instagram" value="<?php echo htmlspecialchars($vendor['instagram'] ?? ''); ?>" class="input-modern w-full" placeholder="https://instagram.com/...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Twitter URL</label>
                    <input type="url" name="twitter" value="<?php echo htmlspecialchars($vendor['twitter'] ?? ''); ?>" class="input-modern w-full" placeholder="https://twitter.com/...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Google Maps Embed URL</label>
                    <input type="url" name="google_map_url" value="<?php echo htmlspecialchars($vendor['google_map_url'] ?? ''); ?>" class="input-modern w-full" placeholder="https://google.com/maps/embed/...">
                    <p class="text-[10px] text-gray-500 mt-1 italic">Paste the 'src' link from the Google Maps iframe embed code.</p>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full py-3">Save Socials</button>
        </form>
    </div>
</div>

<!-- Edit Hours Modal -->
<div id="editHoursModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Operating Hours</h2>
            <button onclick="document.getElementById('editHoursModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-2">
            <input type="hidden" name="action" value="quick_update_hours">
            <div class="grid grid-cols-1 gap-2">
                <?php 
                $daysArr = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $currentHours = json_decode($vendor['operating_hours'], true) ?? [];
                foreach($daysArr as $idx => $dName): 
                    $isSelected = isset($currentHours[$dName]);
                    $oTime = $currentHours[$dName]['open'] ?? '08:00';
                    $cTime = $currentHours[$dName]['close'] ?? '17:00';
                ?>
                <div class="flex items-center space-x-3 p-2 bg-gray-50 rounded-xl border border-gray-100">
                    <input type="checkbox" name="operating_days[]" value="<?php echo $dName; ?>" 
                           id="qd_<?php echo $idx; ?>"
                           <?php echo $isSelected ? 'checked' : ''; ?>
                           class="rounded border-gray-300 text-accent focus:ring-accent">
                    <label for="qd_<?php echo $idx; ?>" class="flex-1 text-xs font-bold text-gray-700 capitalize">
                        <?php echo $dName; ?>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="time" name="operating_hours[<?php echo $idx; ?>][open]" 
                               value="<?php echo $oTime; ?>"
                               class="bg-white border border-gray-200 rounded-lg px-2 py-1 text-[10px] focus:ring-1 focus:ring-accent outline-none">
                        <span class="text-gray-400 text-[10px]">-</span>
                        <input type="time" name="operating_hours[<?php echo $idx; ?>][close]" 
                               value="<?php echo $cTime; ?>"
                               class="bg-white border border-gray-200 rounded-lg px-2 py-1 text-[10px] focus:ring-1 focus:ring-accent outline-none">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn-primary w-full py-3 mt-4">Save Hours</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
