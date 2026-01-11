<?php
$pageTitle = 'Safety Tips';
ob_start();
?>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl font-heading">Safety Tips</h1>
            <p class="mt-4 text-lg text-gray-500">How to stay safe while buying and selling on SparePlug.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
            <div class="p-8">
                <div class="prose prose-blue max-w-none">
                    <h3>1. Meet in Safe Locations</h3>
                    <p>When meeting a seller to inspect or pick up a part, always choose a public, well-lit location. Gas stations, shopping malls, or busy mechanic shops are ideal.</p>

                    <h3>2. Inspect Before You Pay</h3>
                    <p>Never send money before you have seen the item. Inspect the part carefully to ensure it matches the description and is compatible with your vehicle. If possible, bring a mechanic with you.</p>

                    <h3>3. Avoid Cash Transfers</h3>
                    <p>Be wary of sellers who demand payment via wire transfer, mobile money prepayment, or cryptocurrency before delivery. Cash on delivery or secure pickup is always safest.</p>

                    <h3>4. Verify Vendor Identity</h3>
                    <p>Check the vendor's profile on SparePlug. Look for the "Verified" badge and read reviews from other buyers.</p>

                    <h3>5. Trust Your Instincts</h3>
                    <p>If a deal seems too good to be true, it probably is. If a seller is pushy, rude, or refuses to answer questions, walk away.</p>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Report Suspicious Activity:</strong> If you encounter a suspicious seller or listing, please contact us immediately at <a href="mailto:mishaelsema@gmail.com" class="font-medium underline hover:text-yellow-600">mishaelsema@gmail.com</a>.
                                </p>
                            </div>
                        </div>
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
