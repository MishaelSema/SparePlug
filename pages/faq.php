<?php
$pageTitle = 'Frequently Asked Questions';
ob_start();
?>

<div class="bg-gray-50 min-h-screen py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl font-heading">Frequently Asked Questions</h1>
            <p class="mt-4 text-xl text-gray-500">Find answers to common questions about buying and selling on SparePlug.</p>
        </div>

        <div class="space-y-6">
            <?php
            $faqs = [
                'General' => [
                    [
                        'q' => 'What is SparePlug?',
                        'a' => 'SparePlug is a digital marketplace connecting car owners with verified spare parts vendors in Cameroon. We facilitate the discovery of parts, but transactions happen directly between buyer and seller.'
                    ],
                    [
                        'q' => 'Is SparePlug free to use?',
                        'a' => 'For buyers, SparePlug is completely free. We do not charge fees for searching or contacting vendors. Vendors have access to free and premium listing plans.'
                    ]
                ],
                'Buying' => [
                    [
                        'q' => 'How do I buy a part?',
                        'a' => 'Search for your part, click on the listing, and use the "Call", "WhatsApp", or "Inquiry" buttons to contact the seller directly. You arrange payment and delivery with them.'
                    ],
                    [
                        'q' => 'Are the parts guaranteed?',
                        'a' => 'SparePlug verifies vendors, but warranties depend on the individual seller. We recommend asking the vendor about their return policy and warranty before purchasing.'
                    ]
                ],
                'Selling' => [
                    [
                        'q' => 'How do I become a vendor?',
                        'a' => 'Click "Become a Vendor" in the menu, create an account, and set up your shop profile. Once verified, you can start listing products.'
                    ],
                    [
                        'q' => 'How much does it cost to sell?',
                        'a' => 'We offer a Free plan for starters. For more listings and visibility, you can upgrade to our Basic or Premium plans starting at affordable monthly rates.'
                    ]
                ]
            ];

            foreach ($faqs as $category => $items): 
            ?>
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo $category; ?></h2>
                <div class="space-y-4">
                    <?php foreach ($items as $index => $item): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ open: false }">
                        <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFaq(this)">
                            <span class="font-semibold text-gray-900"><?php echo $item['q']; ?></span>
                            <span class="ml-6 flex-shrink-0 text-primary transition-transform duration-300 transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </button>
                        <div class="px-6 pb-4 hidden faq-content">
                            <p class="text-gray-600"><?php echo $item['a']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-16 text-center">
            <h3 class="text-xl font-bold text-gray-900">Still have questions?</h3>
            <p class="mt-2 text-gray-500 mb-6">We are here to help you.</p>
            <a href="index.php?page=contact" class="btn-secondary">Contact Support</a>
        </div>
    </div>
</div>

<script>
function toggleFaq(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('span:last-child');
    
    // Toggle content
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
