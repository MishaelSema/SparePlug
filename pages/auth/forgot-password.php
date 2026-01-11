<?php
// session_start() removed as handled by index.php
require_once 'config/database.php';
require_once 'includes/auth-functions.php';

$pageTitle = 'Forgot Password';
$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } else {
        if ($auth->forgotPassword($email)) {
            $success = 'If an account exists with this email, a reset link has been sent.';
        } else {
            // Don't reveal if email exists, same message for security
            $success = 'If an account exists with this email, a reset link has been sent.';
        }
    }
}

ob_start();
?>

<div class="min-h-[calc(100vh-80px)] flex items-center justify-center p-4">
    <div class="w-full max-w-md animate-fade-in-up">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 font-heading">Reset Password</h1>
            <p class="text-gray-500 mt-2">Enter your email to receive recovery instructions</p>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-8 rounded-3xl mb-6 shadow-sm text-center">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="font-bold text-lg mb-2">Check your inbox</h3>
            <p class="mb-6"><?php echo htmlspecialchars($success); ?></p>
            <a href="index.php?page=login" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Login
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" required
                           class="input-modern"
                           placeholder="john@example.com">
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn-primary w-full py-3.5 shadow-lg shadow-accent/20">
                        Send Recovery Link
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center bg-gray-50 -mx-8 -mb-8 p-4 rounded-b-3xl">
                <a href="index.php?page=login" class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Sign In
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
