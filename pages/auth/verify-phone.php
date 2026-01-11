<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth-functions.php';

$pageTitle = 'Verify Phone';
$auth = new Auth();
$error = '';
$success = '';

$userId = $_SESSION['pending_phone_verification'] ?? null;
if (!$userId) {
    header('Location: index.php?page=register-buyer');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    
    if (empty($code)) {
        $error = 'Please enter the verification code';
    } else {
        $result = $auth->verifyPhone($userId, $code);
        
        if ($result['success']) {
            $success = 'Phone verified successfully!';
            unset($_SESSION['pending_phone_verification']);
        } else {
            $error = $result['message'];
        }
    }
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
            <h1 class="text-3xl font-bold text-gray-900">Verify Your Phone</h1>
            <p class="text-gray-600 mt-2">Enter the 6-digit code sent to your phone</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <div class="text-center">
            <a href="index.php?page=login" class="btn-primary inline-block">Continue</a>
        </div>
        <?php else: ?>

        <div class="card p-8">
            <form method="POST" action="">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                    <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required
                           class="input-field text-center text-2xl tracking-widest"
                           placeholder="000000">
                    <p class="text-xs text-gray-500 mt-1">Check your SMS for the 6-digit code</p>
                </div>

                <button type="submit" class="btn-primary w-full mb-4">Verify</button>
                
                <button type="button" onclick="resendCode()" class="text-sm text-primary hover:text-red-700 w-full">
                    Resend Code
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function resendCode() {
    alert('Resending verification code...');
    // TODO: Implement resend code functionality
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>





