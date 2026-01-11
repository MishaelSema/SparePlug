<?php
// session_start() removed as handled by index.php
require_once 'config/database.php';
require_once 'includes/auth-functions.php';

$pageTitle = 'Reset Password';
$auth = new Auth();
$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: index.php?page=forgot-password');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        if ($auth->resetPassword($token, $password)) {
            $success = 'Password reset successful! You can now sign in.';
        } else {
            $error = 'Invalid or expired reset link';
        }
    }
}

ob_start();
?>

<div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-fade-in-up">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 font-heading">Set New Password</h1>
            <p class="text-gray-500 mt-2">Secure your account with a strong password</p>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-8 rounded-3xl mb-6 shadow-sm text-center">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 class="font-bold text-lg mb-2">Success!</h3>
            <p class="mb-6"><?php echo htmlspecialchars($success); ?></p>
            <a href="index.php?page=login" class="btn-primary w-full py-3">
                Sign In Now
            </a>
        </div>
        <?php else: ?>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-8">
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                     <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" required
                           class="input-modern"
                           placeholder="Minimum 8 characters">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="confirm_password" required
                           class="input-modern"
                           placeholder="Re-enter password">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-3.5 shadow-lg shadow-accent/20">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
