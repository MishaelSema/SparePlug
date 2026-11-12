<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/Mailer.php';

$pageTitle = 'Become a Vendor';
$user = new User();

// Check if user is logged in
$isLoggedIn = $user->isLoggedIn();
$hasProfile = false;
$currentUser = null;

if ($isLoggedIn) {
    if ($user->isVendor()) {
        $hasProfile = $user->getVendorProfile();
        // Redirect only if they HAVE a profile
        if ($hasProfile) {
            header('Location: index.php?page=vendor-dashboard');
            exit;
        }
        $currentUser = $user->getCurrentUser();
    } else {
        // If logged in as buyer, maybe allow becoming a vendor?
        // For now, let's assume they might want to switch.
        // But the loop issue is specifically about VENDOR type users.
    }
}

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'vendor_signup') {
    $errors = [];
    
    // Validate required fields (Common)
    if (empty($_POST['business_name'])) $errors[] = 'Business name is required';
    if (empty($_POST['business_type'])) $errors[] = 'Business type is required';
    
    // Validation for NEW users only
    if (!$isLoggedIn) {
        if (empty($_POST['first_name'])) $errors[] = 'First name is required';
        if (empty($_POST['last_name'])) $errors[] = 'Last name is required';
        if (empty($_POST['email'])) $errors[] = 'Email is required';
        if (empty($_POST['phone'])) $errors[] = 'Phone number is required';
        if (empty($_POST['password'])) $errors[] = 'Password is required';
        
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        $existingUser = $db->fetch("SELECT id FROM users WHERE email = ?", [$_POST['email']]);
        if ($existingUser) {
            $errors[] = 'Email already registered';
        }
        
        if (strlen($_POST['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
    }
    
    if (empty($errors)) {
        try {
            $userId = null;
            
            if ($isLoggedIn && $currentUser) {
                // Use existing user
                $userId = $currentUser['id'];
            } else {
                // Create user account
                $userData = [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                    'password' => $_POST['password'],
                    'user_type' => 'vendor'
                ];
                $userId = $user->register($userData);
            }
            
            if ($userId) {
                // Prepare operating hours JSON
                $operatingHours = [];
                if (isset($_POST['operating_days']) && isset($_POST['operating_hours'])) {
                    $days = $_POST['operating_days'];
                    $hours = $_POST['operating_hours'];
                    $daysArr = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    
                    foreach ($days as $day) {
                        $dayIndex = array_search(ucfirst($day), $daysArr); 
                        if ($dayIndex !== false && isset($hours[$dayIndex])) {
                            $operatingHours[$day] = [
                                'open' => $hours[$dayIndex]['open'] ?? '08:00',
                                'close' => $hours[$dayIndex]['close'] ?? '17:00'
                            ];
                        }
                    }
                }
                
                // Create vendor profile
                $vendorData = [
                    'business_name' => $_POST['business_name'],
                    'business_type' => $_POST['business_type'],
                    'description' => $_POST['description'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'city' => $_POST['city'] ?? '',
                    'region' => $_POST['region'] ?? '',
                    'operating_hours' => json_encode($operatingHours),
                    'user_id' => $userId
                ];

                // Handle Logo Upload
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                    $uploadDir = 'uploads/logos/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                    $filename = 'logo_pending_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $filename)) {
                        $vendorData['logo_url'] = $uploadDir . $filename;
                    }
                }

                // Handle Registration Document Upload
                if (isset($_FILES['business_registration']) && $_FILES['business_registration']['error'] === 0) {
                    $uploadDir = 'uploads/documents/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $ext = pathinfo($_FILES['business_registration']['name'], PATHINFO_EXTENSION);
                    $filename = 'reg_pending_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['business_registration']['tmp_name'], $uploadDir . $filename)) {
                        $vendorData['verification_documents'] = json_encode([
                            'registration_doc' => $uploadDir . $filename,
                            'uploaded_at' => date('Y-m-d H:i:s')
                        ]);
                        
                        $tempMailer = new Mailer();
                        $userEmail = $isLoggedIn ? $currentUser['email'] : $_POST['email'];
                        $tempMailer->sendVerificationSubmitted($userEmail, $_POST['business_name']);
                    }
                }
                
                $user->createVendorProfile($vendorData, $userId);
                
                if ($isLoggedIn) {
                    $_SESSION['success'] = 'Profile completed! Welcome to your dashboard.';
                    header('Location: index.php?page=vendor-dashboard');
                } else {
                    $_SESSION['success'] = 'Account created successfully! Please login to continue.';
                    header('Location: index.php?page=login');
                }
                exit;
            } else {
                $errors[] = 'Failed to create account. Please try again.';
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
    <div class="text-center mb-8">
        <?php if ($isLoggedIn): ?>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Complete Your Shop Profile</h1>
            <p class="text-lg text-gray-600">You're almost there! Setup your shop details to start selling.</p>
        <?php else: ?>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Become a sparePlug Vendor</h1>
            <p class="text-lg text-gray-600">Start selling auto parts and grow your business</p>
        <?php endif; ?>
    </div>
    
    <!-- Benefits Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6 text-center">
            <div class="w-16 h-16 bg-accent rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Free to Start</h3>
            <p class="text-gray-600">List up to 10 products for free</p>
        </div>
        
        <div class="card p-6 text-center">
            <div class="w-16 h-16 bg-accent rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Direct Contact</h3>
            <p class="text-gray-600">Customers contact you directly</p>
        </div>
        
        <div class="card p-6 text-center">
            <div class="w-16 h-16 bg-accent rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Verified Badge</h3>
            <p class="text-gray-600">Build trust with verification</p>
        </div>
    </div>
    
    <!-- Signup Form -->
    <div class="card p-8">
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
            <input type="hidden" name="action" value="vendor_signup">
            
            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <?php if ($isLoggedIn && $currentUser): ?>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-blue-800 text-sm">
                            <span class="font-bold">Logged in as:</span> 
                            <?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?>
                            (<?php echo htmlspecialchars($currentUser['email']); ?>)
                        </p>
                        <p class="text-blue-600 text-xs mt-1">These details will be linked to your vendor profile.</p>
                    </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" required 
                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                               class="input-field">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" required 
                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                               class="input-field">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="input-field">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel" name="phone" required 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                               class="input-field" placeholder="+237 XXX XXX XXX">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" required 
                               class="input-field" placeholder="Minimum 6 characters">
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Business Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                        <input type="text" name="business_name" required 
                               value="<?php echo htmlspecialchars($_POST['business_name'] ?? ''); ?>"
                               class="input-field" placeholder="Your business or shop name">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Type *</label>
                        <select name="business_type" required class="input-field">
                            <option value="">Select Business Type</option>
                            <option value="individual" <?php echo ($_POST['business_type'] ?? '') === 'individual' ? 'selected' : ''; ?>>Individual Seller</option>
                            <option value="company" <?php echo ($_POST['business_type'] ?? '') === 'company' ? 'selected' : ''; ?>>Company</option>
                            <option value="garage" <?php echo ($_POST['business_type'] ?? '') === 'garage' ? 'selected' : ''; ?>>Garage/Workshop</option>
                            <option value="dealer" <?php echo ($_POST['business_type'] ?? '') === 'dealer' ? 'selected' : ''; ?>>Auto Dealer</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="city" 
                               value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>"
                               class="input-field" placeholder="Yaoundé, Douala, etc.">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Region</label>
                        <select name="region" class="input-field">
                            <option value="">Select Region</option>
                            <option value="Littoral" <?php echo ($_POST['region'] ?? '') === 'Littoral' ? 'selected' : ''; ?>>Littoral</option>
                            <option value="Centre" <?php echo ($_POST['region'] ?? '') === 'Centre' ? 'selected' : ''; ?>>Centre</option>
                            <option value="Ouest" <?php echo ($_POST['region'] ?? '') === 'Ouest' ? 'selected' : ''; ?>>Ouest</option>
                            <option value="Nord-Ouest" <?php echo ($_POST['region'] ?? '') === 'Nord-Ouest' ? 'selected' : ''; ?>>Nord-Ouest</option>
                            <option value="Sud-Ouest" <?php echo ($_POST['region'] ?? '') === 'Sud-Ouest' ? 'selected' : ''; ?>>Sud-Ouest</option>
                            <option value="Adamaoua" <?php echo ($_POST['region'] ?? '') === 'Adamaoua' ? 'selected' : ''; ?>>Adamaoua</option>
                            <option value="Est" <?php echo ($_POST['region'] ?? '') === 'Est' ? 'selected' : ''; ?>>Est</option>
                            <option value="Extrême-Nord" <?php echo ($_POST['region'] ?? '') === 'Extrême-Nord' ? 'selected' : ''; ?>>Extrême-Nord</option>
                            <option value="Nord" <?php echo ($_POST['region'] ?? '') === 'Nord' ? 'selected' : ''; ?>>Nord</option>
                            <option value="Sud" <?php echo ($_POST['region'] ?? '') === 'Sud' ? 'selected' : ''; ?>>Sud</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Address</label>
                        <textarea name="address" rows="3" 
                                  class="input-field" 
                                  placeholder="Full business address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
                        <textarea name="description" rows="4" 
                                  class="input-field" 
                                  placeholder="Tell customers about your business, specialties, and experience"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Operating Hours -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Operating Hours</h3>
                <div class="space-y-3">
                    <?php 
                    $daysArr = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach($daysArr as $index => $day): 
                    ?>
                    <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center space-x-3 min-w-[140px]">
                            <input type="checkbox" name="operating_days[]" value="<?php echo strtolower($day); ?>" 
                                   class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-accent"
                                   id="day_<?php echo $index; ?>">
                            <label for="day_<?php echo $index; ?>" class="text-sm font-bold text-gray-700">
                                <?php echo $day; ?>
                            </label>
                        </div>
                        
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-gray-400 ml-1">Opening Time</label>
                                <input type="time" name="operating_hours[<?php echo $index; ?>][open]" 
                                       class="input-field py-3 text-base" value="08:00">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-gray-400 ml-1">Closing Time</label>
                                <input type="time" name="operating_hours[<?php echo $index; ?>][close]" 
                                       class="input-field py-3 text-base" value="17:00">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Select the days your shop is open and specify operating hours
                </p>
            </div>
            
            <!-- Verification Documents -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Verification Documents</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Upload your documents to get verified. This helps build trust with customers.
                    Documents will be reviewed by our team before your shop goes live.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Logo Upload -->
                    <div>
                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-accent transition-all cursor-pointer bg-white group" onclick="document.getElementById('logo-upload').click()">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2 group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-bold text-gray-700">Business Logo</p>
                            <p class="text-[10px] text-gray-400 mt-1">PNG, JPG, Max 2MB</p>
                            <input type="file" name="logo" accept="image/*" class="hidden" id="logo-upload" onchange="updateFileName(this, 'logo-name')">
                            <p id="logo-name" class="text-xs text-accent mt-2 font-medium"></p>
                        </div>
                    </div>
                    
                    <!-- Business Registration -->
                    <div>
                        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-accent transition-all cursor-pointer bg-white group" onclick="document.getElementById('reg-upload').click()">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2 group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-bold text-gray-700">Registration Document</p>
                            <p class="text-[10px] text-gray-400 mt-1">PDF or Image, Max 5MB</p>
                            <input type="file" name="business_registration" accept=".pdf,image/*" class="hidden" id="reg-upload" onchange="updateFileName(this, 'reg-name')">
                            <p id="reg-name" class="text-xs text-accent mt-2 font-medium"></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Get Verified</p>
                                <p>Upload your business registration document. Our team will review it within 2-3 business days. You'll receive an email once approved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Terms and Conditions -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-start">
                    <input type="checkbox" id="terms" required class="mt-1 mr-3">
                    <label for="terms" class="text-sm text-gray-600">
                        I agree to the <a href="javascript:void(0)" class="text-primary hover:underline">Terms of Service</a> 
                        and <a href="javascript:void(0)" class="text-primary hover:underline">Privacy Policy</a>. 
                        I understand that I will be responsible for all transactions and customer interactions.
                    </label>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn-primary px-8 py-3 text-lg">
                    Create Vendor Account
                </button>
            </div>
        </form>
    </div>
    
    <!-- Login Link -->
    <div class="text-center mt-6">
        <p class="text-gray-600">
            Already have an account? 
            <a href="index.php?page=login" class="text-primary hover:underline font-medium">Login here</a>
        </p>
    </div>
</div>

<script>
// File name display
function updateFileName(input, targetId) {
    const fileName = input.files[0] ? input.files[0].name : '';
    document.getElementById(targetId).textContent = fileName ? 'Selected: ' + fileName : '';
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const passwordInput = document.querySelector('input[name="password"]');
    const terms = document.querySelector('#terms').checked;
    
    // Validate password only if field exists (new user)
    if (passwordInput && passwordInput.value.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long');
        return;
    }
    
    if (!terms) {
        e.preventDefault();
        alert('Please accept the terms and conditions');
        return;
    }
});

// Phone number formatting
document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('237')) {
        value = '+' + value;
    } else if (value.startsWith('0')) {
        value = '+237' + value.substring(1);
    } else if (!value.startsWith('+')) {
        value = '+237' + value;
    }
    e.target.value = value;
});
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>




