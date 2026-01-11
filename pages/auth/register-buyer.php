<?php
// session_start() removed as handled by index.php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth-functions.php';

$pageTitle = 'Register as Buyer';
$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'register') {
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $registerMethod = $_POST['register_method'] ?? 'email'; // email, phone, google
        
        // Validation
        if ($registerMethod === 'email' || $registerMethod === 'phone') {
            if (empty($firstName) || empty($lastName) || empty($password)) {
                $error = 'Please fill in all required fields';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters';
            } else {
                // Check if email/phone exists
                $existing = false;
                if (!empty($email)) {
                    $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
                } elseif (!empty($phone)) {
                    $existing = $db->fetch("SELECT id FROM users WHERE phone = ?", [$phone]);
                }
                
                if ($existing) {
                    $error = 'This email/phone is already registered';
                } else {
                    // Register user
                    $userData = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'password' => $password,
                        'user_type' => 'buyer',
                        'status' => 'active'
                    ];
                    
                    if ($registerMethod === 'email' && !empty($email)) {
                        $userData['email'] = $email;
                    }
                    if ($registerMethod === 'phone' && !empty($phone)) {
                        $userData['phone'] = $phone;
                    }
                    
                    try {
                        $userId = $auth->register($userData);
                        
                        // Send verification
                        if ($registerMethod === 'email') {
                            $auth->sendEmailVerification($userId);
                            $success = 'Registration successful! Please check your email to verify your account.';
                        } else {
                            // Send phone verification
                            $auth->createPhoneVerification($userId, $phone);
                            $auth->sendSMSVerification($userId, $phone);
                            $_SESSION['pending_phone_verification'] = $userId;
                            header('Location: index.php?page=verify-phone');
                            exit;
                        }
                    } catch (Exception $e) {
                        $error = 'Registration failed: ' . $e->getMessage();
                    }
                }
            }
        }
    }
}

ob_start();
?>

<div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-4">
    <div class="w-full max-w-lg animate-fade-in-up">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 font-heading">Create Account</h1>
            <p class="text-gray-500 mt-2">Join SparePlug to find the best auto parts</p>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-sm">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-8">
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Register Method Tabs -->
            <div class="flex p-1 bg-gray-50 rounded-xl mb-6">
                <button type="button" onclick="switchMethod('email')" class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all shadow-sm bg-white text-gray-900 register-method ring-1 ring-gray-200" data-method="email" id="tab-email">
                    Email
                </button>
                <button type="button" onclick="switchMethod('phone')" class="flex-1 py-2.5 text-sm font-semibold rounded-lg text-gray-500 hover:text-gray-900 transition-all register-method" data-method="phone" id="tab-phone">
                    Phone
                </button>
            </div>

            <!-- Email Registration Form -->
            <form method="POST" action="" id="emailForm" class="register-form space-y-4">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="register_method" value="email">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required class="input-modern" placeholder="John">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required class="input-modern" placeholder="Doe">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" required class="input-modern" placeholder="john@example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="input-modern" placeholder="Minimum 8 characters">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="confirm_password" required class="input-modern" placeholder="Re-enter password">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-3.5 shadow-lg shadow-accent/20">
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Phone Registration Form -->
            <form method="POST" action="" id="phoneForm" class="register-form hidden space-y-4">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="register_method" value="phone">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required class="input-modern" placeholder="John">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required class="input-modern" placeholder="Doe">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" required class="input-modern" placeholder="+237 6XX XXX XXX">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="input-modern" placeholder="Minimum 8 characters">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="confirm_password" required class="input-modern" placeholder="Re-enter password">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-3.5 shadow-lg shadow-accent/20">
                        Create Account
                    </button>
                </div>
            </form>


            
             <p class="text-center text-sm text-gray-600 mt-6">
                Already have an account? <a href="index.php?page=login" class="text-primary hover:text-accent font-bold">Log in</a>
            </p>
        </div>
        
        <p class="text-center text-sm text-gray-500 mt-6">
            Are you a vendor? <a href="index.php?page=vendor-signup" class="text-accent font-bold hover:underline">Register as a Vendor</a>
        </p>
    </div>
</div>

<script>
function switchMethod(method) {
    // Hide all forms
    document.querySelectorAll('.register-form').forEach(form => form.classList.add('hidden'));
    
    // Reset tab styles
    document.querySelectorAll('.register-method').forEach(tab => {
        tab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm', 'ring-1', 'ring-gray-200');
        tab.classList.add('text-gray-500', 'hover:text-gray-900');
    });
    
    // Set active tab style
    const activeTab = document.getElementById('tab-' + method);
    activeTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm', 'ring-1', 'ring-gray-200');
    activeTab.classList.remove('text-gray-500', 'hover:text-gray-900');
    
    // Show selected form
    document.getElementById(method + 'Form').classList.remove('hidden');
}

function googleSignIn() {
    alert('Google Sign In integration coming soon');
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
