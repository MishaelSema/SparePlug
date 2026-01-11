<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Edit Shop Profile';
$user = new User();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();
$notification = new Notification(); // Triggers auto-schema updates for missing columns
$operatingHours = json_decode($vendorProfile['operating_hours'], true) ?? [];

// Handle form submission
$errors = [];
$success = '';

if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    // Validate required fields
    if (empty($_POST['business_name'])) $errors[] = 'Business name is required';
    if (empty($_POST['business_type'])) $errors[] = 'Business type is required';
    
    if (empty($errors)) {
        try {
            // Prepare operating hours JSON
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
            
            // Update vendor profile
            $updateData = [
                'business_name' => $_POST['business_name'],
                'business_type' => $_POST['business_type'],
                'description' => $_POST['description'] ?? '',
                'address' => $_POST['address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'region' => $_POST['region'] ?? '',
                'website' => $_POST['website'] ?? '',
                'facebook' => $_POST['facebook'] ?? '',
                'instagram' => $_POST['instagram'] ?? '',
                'twitter' => $_POST['twitter'] ?? '',
                'google_map_url' => $_POST['google_map_url'] ?? '',
                'services' => $_POST['services'] ?? '',
                'other_info' => $_POST['other_info'] ?? '',
                'operating_hours' => json_encode($newOperatingHours)
            ];
            
            // Handle Logo Upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . $vendorProfile['id'] . '_' . time() . '.' . $ext;
                $targetPath = 'uploads/logos/' . $filename;
                if (!is_dir('uploads/logos')) mkdir('uploads/logos', 0777, true);
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                    $updateData['logo_url'] = $targetPath;
                }
            }

            // Handle Cover Upload
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
                $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $filename = 'cover_' . $vendorProfile['id'] . '_' . time() . '.' . $ext;
                $targetPath = 'uploads/covers/' . $filename;
                if (!is_dir('uploads/covers')) mkdir('uploads/covers', 0777, true);
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetPath)) {
                    $updateData['cover_url'] = $targetPath;
                }
            }
            
            $user->updateVendorProfile($updateData);
            
            $_SESSION['vendor_success'] = 'Profile updated successfully!';
            header("Location: index.php?page=vendor-profile&id=" . $vendorProfile['id']);
            exit;
        } catch (Exception $e) {
            $errors[] = 'An error occurred. Please try again.';
        }
    }
}

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Shop Profile</h1>
        <p class="text-gray-600">Update your business information and settings</p>
    </div>
    
    <!-- Verification Status -->
    <?php if (!$vendorProfile['is_verified']): ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold text-yellow-800">Verification Pending</p>
                <p class="text-sm text-yellow-700">
                    Your shop is pending verification. You can still update your profile. 
                    Verification typically takes 1-2 business days.
                </p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="font-semibold text-green-800">Verified Seller</p>
                <p class="text-sm text-green-700">
                    Your shop has been verified. You now have a verified badge.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
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
        
        <form method="POST" class="space-y-6" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile">
            
            <!-- Business Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h3>
                
                <!-- Logo & Cover Upload -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Logo</label>
                        <div class="relative group w-32 h-32 bg-gray-100 rounded-3xl overflow-hidden border-2 border-dashed border-gray-200 hover:border-accent transition-colors">
                            <?php if(!empty($vendorProfile['logo_url'])): ?>
                                <img src="<?php echo $vendorProfile['logo_url']; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-white text-xs font-bold">Change Logo</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shop Cover Image (Hero)</label>
                        <div class="relative group h-32 bg-gray-100 rounded-3xl overflow-hidden border-2 border-dashed border-gray-200 hover:border-accent transition-colors">
                            <?php if(!empty($vendorProfile['cover_url'])): ?>
                                <img src="<?php echo $vendorProfile['cover_url']; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="cover" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-white text-xs font-bold">Change Cover</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                        <input type="text" name="business_name" required 
                               value="<?php echo htmlspecialchars($vendorProfile['business_name']); ?>"
                               class="input-field" placeholder="Your business or shop name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Type *</label>
                        <select name="business_type" required class="input-field">
                            <option value="individual" <?php echo $vendorProfile['business_type'] === 'individual' ? 'selected' : ''; ?>>Individual Seller</option>
                            <option value="company" <?php echo $vendorProfile['business_type'] === 'company' ? 'selected' : ''; ?>>Company</option>
                            <option value="garage" <?php echo $vendorProfile['business_type'] === 'garage' ? 'selected' : ''; ?>>Garage/Workshop</option>
                            <option value="dealer" <?php echo $vendorProfile['business_type'] === 'dealer' ? 'selected' : ''; ?>>Auto Dealer</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" 
                               value="<?php echo htmlspecialchars($vendorProfile['city'] ?? ''); ?>"
                               class="input-field" placeholder="Douala, Yaoundé, etc.">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                        <select name="region" class="input-field">
                            <option value="">Select Region</option>
                            <?php 
                            $regions = ['Littoral', 'Centre', 'Ouest', 'Nord-Ouest', 'Sud-Ouest', 'Adamaoua', 'Est', 'Extrême-Nord', 'Nord', 'Sud'];
                            foreach($regions as $region): 
                            ?>
                            <option value="<?php echo $region; ?>" <?php echo ($vendorProfile['region'] ?? '') === $region ? 'selected' : ''; ?>>
                                <?php echo $region; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Address</label>
                        <textarea name="address" rows="3" 
                                  class="input-field" 
                                  placeholder="Full business address"><?php echo htmlspecialchars($vendorProfile['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                        <textarea name="description" rows="4" 
                                  class="input-field" 
                                  placeholder="Tell customers about your business, specialties, and experience"><?php echo htmlspecialchars($vendorProfile['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Online Presence & Social Media -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Online Presence & Social Media</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                        <input type="url" name="website" 
                               value="<?php echo htmlspecialchars($vendorProfile['website'] ?? ''); ?>"
                               class="input-field" placeholder="https://yourwebsite.com">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                        <input type="url" name="facebook" 
                               value="<?php echo htmlspecialchars($vendorProfile['facebook'] ?? ''); ?>"
                               class="input-field" placeholder="https://facebook.com/yourshop">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                        <input type="url" name="instagram" 
                               value="<?php echo htmlspecialchars($vendorProfile['instagram'] ?? ''); ?>"
                               class="input-field" placeholder="https://instagram.com/yourshop">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                        <input type="url" name="twitter" 
                               value="<?php echo htmlspecialchars($vendorProfile['twitter'] ?? ''); ?>"
                               class="input-field" placeholder="https://twitter.com/yourshop">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Maps URL</label>
                        <input type="url" name="google_map_url" 
                               value="<?php echo htmlspecialchars($vendorProfile['google_map_url'] ?? ''); ?>"
                               class="input-field" placeholder="Share link from Google Maps">
                    </div>
                </div>
            </div>

            <!-- Services & Other Info -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Services & Additional Information</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Services Offered</label>
                        <textarea name="services" rows="3" 
                                  class="input-field" 
                                  placeholder="List your services (e.g., Installation, Mobile Service, Delivery)"><?php echo htmlspecialchars($vendorProfile['services'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Other Information (Good to Know)</label>
                        <textarea name="other_info" rows="3" 
                                  class="input-field" 
                                  placeholder="Any other details customers should know?"><?php echo htmlspecialchars($vendorProfile['other_info'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Operating Hours</h3>
                <div class="space-y-4">
                    <?php 
                    $daysArr = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach($daysArr as $index => $day): 
                        $dayLower = strtolower($day);
                        $isChecked = isset($operatingHours[$dayLower]);
                        $openTime = $operatingHours[$dayLower]['open'] ?? '08:00';
                        $closeTime = $operatingHours[$dayLower]['close'] ?? '17:00';
                    ?>
                    <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center space-x-3 min-w-[140px]">
                            <input type="checkbox" name="operating_days[]" value="<?php echo $dayLower; ?>" 
                                   class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-accent"
                                   id="day_<?php echo $index; ?>"
                                   <?php echo $isChecked ? 'checked' : ''; ?>>
                            <label for="day_<?php echo $index; ?>" class="text-sm font-bold text-gray-700">
                                <?php echo $day; ?>
                            </label>
                        </div>
                        
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-gray-400 ml-1">Opening Time</label>
                                <input type="time" name="operating_hours[<?php echo $index; ?>][open]" 
                                       class="input-field py-3 text-base" 
                                       value="<?php echo $openTime; ?>">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-gray-400 ml-1">Closing Time</label>
                                <input type="time" name="operating_hours[<?php echo $index; ?>][close]" 
                                       class="input-field py-3 text-base" 
                                       value="<?php echo $closeTime; ?>">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-gray-500 mt-4 italic">
                    * Select the days your shop is open and specify the operating hours clearly for customers.
                </p>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="index.php?page=vendor-dashboard" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





