<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth-functions.php';

$pageTitle = 'Verify Email';
$auth = new Auth();
$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: index.php?page=login');
    exit;
}

if ($auth->verifyEmail($token)) {
    $success = 'Email verified successfully! You can now sign in.';
} else {
    $error = 'Invalid or expired verification link';
}

ob_start();
?>

<div class="p-8">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">S</span>
                </div>
                <span class="text-2xl font-bold text-gray-900">SparePlug</span>
            </div>
        </div>

        <?php if ($error): ?>
        <div class="card p-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Verification Failed</h2>
            <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($error); ?></p>
            <a href="index.php?page=login" class="btn-primary">Go to Login</a>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="card p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Email Verified!</h2>
            <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($success); ?></p>
            <a href="index.php?page=login" class="btn-primary">Sign In</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





