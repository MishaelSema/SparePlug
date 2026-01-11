<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Login';
$user = new User();

// Handle login form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if ($user->login($email, $password)) {
        // Redirect based on user type
        if ($_SESSION['user_type'] === 'admin') {
            header('Location: index.php?page=admin-dashboard');
        } elseif ($_SESSION['user_type'] === 'vendor') {
            header('Location: index.php?page=vendor-dashboard');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}

ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xl">S</span>
                </div>
                <span class="text-2xl font-bold text-gray-900">SparePlug</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Sign in to your account</h2>
            <p class="mt-2 text-sm text-gray-600">
                Or
                <a href="index.php?page=vendor-signup" class="font-medium text-primary hover:text-red-700">
                    create a vendor account
                </a>
            </p>
        </div>
        
        <!-- Login Form -->
        <div class="card p-8">
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="login">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="input-field mt-1" 
                           placeholder="Enter your email">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="input-field mt-1" 
                           placeholder="Enter your password">
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                               class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                    
                    <div class="text-sm">
                        <a href="#" class="font-medium text-primary hover:text-red-700">
                            Forgot your password?
                        </a>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="btn-primary w-full">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Additional Links -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="index.php?page=vendor-signup" class="font-medium text-primary hover:text-red-700">
                    Sign up as a vendor
                </a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>

