<?php
$pageTitle = 'Terms of Service';
ob_start();
?>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
         <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl font-heading">Terms of Service</h1>
            <p class="mt-4 text-lg text-gray-500">Please read these terms carefully before using SparePlug.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
            <div class="p-8">
                <div class="prose prose-blue max-w-none text-gray-600">
                    <p class="mb-4">Last Updated: <?php echo date('F d, Y'); ?></p>

                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using SparePlug, you accept and agree to be bound by the terms and provision of this agreement.</p>

                    <h3>2. Description of Service</h3>
                    <p>SparePlug provides a digital platform for users to list and search for automotive spare parts. We do not own or sell the products listed (unless explicitly stated) and are not a party to the actual transaction between buyers and sellers.</p>

                    <h3>3. User Accounts</h3>
                    <p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>

                    <h3>4. User Conduct</h3>
                    <p>You agree not to use the service to post listing that are fake, misleading, or illegal. SparePlug reserves the right to remove listing or terminate accounts that violate these policies.</p>

                    <h3>5. Limitation of Liability</h3>
                    <p>SparePlug shall not be liable for any direct, indirect, incidental, special, or consequential damages resulting from the use or the inability to use the service or for cost of procurement of substitute goods and services.</p>

                    <h3>6. Changes to Terms</h3>
                    <p>We reserve the right to modify these terms at any time. We will do so by posting and drawing attention to the updated terms on the Site. Your decision to continue to visit and make use of the Site after such changes have been made constitutes your formal acceptance of the new Terms of Service.</p>
                    
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <p>Questions about the Terms of Service should be sent to us at <a href="mailto:mishaelsema@gmail.com" class="text-primary hover:underline">mishaelsema@gmail.com</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
