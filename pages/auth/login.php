<?php
// session_start() removed as it's handled by index.php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth-functions.php';

$pageTitle = 'Login';
$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

function checkLoginRateLimit($identifier) {
    $lockoutDuration = 300; // 5 minutes
    $maxAttempts = 5;
    $key = 'login_attempts_' . md5($identifier);
    
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'locked_until' => null];
    
    if ($attempts['locked_until'] && time() < $attempts['locked_until']) {
        $remaining = $attempts['locked_until'] - time();
        return ['locked' => true, 'remaining' => $remaining, 'message' => "Too many failed attempts. Please try again in " . ceil($remaining/60) . " minute(s)."];
    }
    
    if ($attempts['locked_until'] && time() >= $attempts['locked_until']) {
        $attempts = ['count' => 0, 'locked_until' => null];
    }
    
    return ['locked' => false, 'attempts' => $attempts['count']];
}

function recordFailedLogin($identifier) {
    $maxAttempts = 5;
    $lockoutDuration = 300;
    $key = 'login_attempts_' . md5($identifier);
    
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'locked_until' => null];
    $attempts['count']++;
    
    if ($attempts['count'] >= $maxAttempts) {
        $attempts['locked_until'] = time() + $lockoutDuration;
    }
    
    $_SESSION[$key] = $attempts;
}

function clearLoginAttempts($identifier) {
    $key = 'login_attempts_' . md5($identifier);
    unset($_SESSION[$key]);
}

$error = '';
if (isset($_GET['error']) && $_GET['error'] === 'suspended') {
    $error = 'Your account has been suspended by an administrator. Please contact support for more information.';
}
$requires2FA = false;
$twoFAUserId = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $identifier = $_POST['identifier'] ?? ''; // email or phone
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);
        $twoFACode = $_POST['two_fa_code'] ?? '';
        
        $rateLimit = checkLoginRateLimit($identifier);
        if ($rateLimit['locked']) {
            $error = $rateLimit['message'];
        } else {
            if (!empty($twoFACode)) {
                // Verify 2FA
                $twoFAUserId = $_SESSION['two_fa_user_id'] ?? null;
                if ($auth->verify2FA($twoFAUserId, $twoFACode)) {
                    clearLoginAttempts($identifier);
                    ob_clean();
                    header('Location: index.php?page=admin-dashboard');
                    echo '<script>window.location.href="index.php?page=admin-dashboard";</script>';
                    exit;
                } else {
                    $error = 'Invalid 2FA code';
                    $requires2FA = true;
                }
            } else {
                // Regular login
                $result = $auth->login($identifier, $password, $rememberMe);
                
                if ($result['requires_2fa'] ?? false) {
                    clearLoginAttempts($identifier);
                    $requires2FA = true;
                    $twoFAUserId = $result['user_id'];
                    $_SESSION['two_fa_user_id'] = $twoFAUserId;
                    $code = $auth->generate2FACode($twoFAUserId);
                    // Code sent to user
                } elseif ($result['success']) {
                    clearLoginAttempts($identifier);
                    // Redirect based on user type
                    ob_clean();
                    if ($_SESSION['user_type'] === 'admin') {
                        $location = 'index.php?page=admin-dashboard';
                    } elseif ($_SESSION['user_type'] === 'vendor') {
                        $location = 'index.php?page=vendor-dashboard';
                    } else {
                        $location = 'index.php';
                    }
                    header("Location: $location");
                    echo "<script>window.location.href='$location';</script>";
                    exit;
                } else {
                    recordFailedLogin($identifier);
                    $error = $result['message'] ?? 'Invalid credentials';
                    // Log failure for debugging if needed
                    error_log("Login failure: $error for $identifier");
                }
            }
        }
    }
}

ob_start();
?>

<div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-fade-in-up">
        
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg border border-gray-100 mb-6 transform rotate-3 hover:rotate-6 transition-transform overflow-hidden p-3">
                <img src="assets/sparePlugFaviconLightSVG.svg" alt="sparePlug Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-3xl font-bold text-gray-900 font-heading" data-translate="welcomeBack">Welcome Back</h1>
            <p class="text-gray-500 mt-2" data-translate="loginSubtitle">Sign in to access your account</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-8 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-full -mr-16 -mt-16 blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-primary/5 rounded-full -ml-16 -mb-16 blur-2xl pointer-events-none"></div>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($requires2FA): ?>
                <!-- 2FA Form -->
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Verify Identity</h2>
                    <p class="text-sm text-gray-500 mt-1">Enter the 6-digit code sent to your email</p>
                </div>

                <form method="POST" action="" class="space-y-6">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="relative">
                        <input type="text" name="two_fa_code" maxlength="6" pattern="[0-9]{6}" required autofocus
                               class="w-full text-center text-3xl font-bold tracking-[0.5em] py-4 rounded-xl border-2 border-gray-200 focus:border-accent focus:ring-0 transition-colors uppercase placeholder-gray-300"
                               placeholder="000000">
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 text-lg shadow-lg shadow-accent/20">
                        Verify Code
                    </button>
                    
                    <button type="button" onclick="resend2FA()" class="w-full text-sm text-gray-500 hover:text-accent font-medium transition-colors">
                        Didn't receive code? Resend
                    </button>
                </form>

            <?php else: ?>
                <!-- Login Form -->
                <form method="POST" action="" class="space-y-5">
                    <input type="hidden" name="action" value="login">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" data-translate="emailOrPhone">Email or Phone</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 flex">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input type="text" name="identifier" required
                                   class="input-modern pl-12"
                                   placeholder="user@example.com" data-translate-placeholder="emailOrPhone">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700" data-translate="password">Password</label>
                            <a href="index.php?page=forgot-password" class="text-sm font-semibold text-accent hover:text-accent-hover transition-colors" data-translate="forgotPassword">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 flex">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input type="password" name="password" required
                                   class="input-modern pl-12 pr-12"
                                   id="password"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none flex">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eye-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember_me" class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent/20 cursor-pointer">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-800 transition-colors">Remember me for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 text-lg shadow-lg shadow-accent/20">
                        Sign In
                    </button>
                </form>


            <?php endif; ?>
        </div>

        <!-- Footer Links -->
        <div class="text-center mt-8 space-y-3">
            <p class="text-sm text-gray-500">
                New to SparePlug? 
                <a href="index.php?page=register-buyer" class="text-primary font-bold hover:underline">Create buyer account</a>
            </p>
            <p class="text-sm text-gray-500">
                Want to sell parts? 
                <a href="index.php?page=vendor-signup" class="text-accent font-bold hover:underline">Join as a Vendor</a>
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
}

function googleSignIn() {
    // Placeholder for Google Sign In
    alert('Google Sign In integration coming soon!');
}

function resend2FA() {
    // Placeholder for resend logic
    alert('A new code has been sent to your email.');
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
