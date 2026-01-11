<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Manage Inquiries';
$user = new User();
$inquiry = new Inquiry();

// Check if user is logged in and is a vendor
if (!$user->isLoggedIn() || !$user->isVendor()) {
    header('Location: index.php?page=vendor-signup');
    exit;
}

$vendorProfile = $user->getVendorProfile();

// Handle inquiry status update
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $inquiryId = $_POST['inquiry_id'];
    $status = $_POST['status'];
    
    if ($inquiry->updateStatus($inquiryId, $status)) {
        $_SESSION['success'] = 'Inquiry status updated successfully!';
    } else {
        $_SESSION['error'] = 'Failed to update inquiry status.';
    }
}

// Get all inquiries for this vendor
$inquiries = $inquiry->getByVendor($vendorProfile['id']);

// Filter inquiries
$filter = $_GET['filter'] ?? 'all';
$filteredInquiries = $inquiries;

if ($filter !== 'all') {
    $filteredInquiries = array_filter($inquiries, function($inquiry) use ($filter) {
        return $inquiry['status'] === $filter;
    });
}

ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Inquiries</h1>
            <p class="text-gray-600">Respond to customer inquiries about your products</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <a href="?filter=all" 
               class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 <?php echo $filter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'; ?>">
                All (<?php echo count($inquiries); ?>)
            </a>
            <a href="?filter=pending" 
               class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 <?php echo $filter === 'pending' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'; ?>">
                Pending (<?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'pending')); ?>)
            </a>
            <a href="?filter=responded" 
               class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 <?php echo $filter === 'responded' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'; ?>">
                Responded (<?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'responded')); ?>)
            </a>
            <a href="?filter=closed" 
               class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 <?php echo $filter === 'closed' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'; ?>">
                Closed (<?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'closed')); ?>)
            </a>
        </div>
    </div>
    
    <!-- Inquiries List -->
    <?php if (empty($filteredInquiries)): ?>
        <div class="card p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No inquiries found</h3>
            <p class="text-gray-600">
                <?php if ($filter === 'all'): ?>
                    You haven't received any inquiries yet. Start promoting your products to get customer inquiries.
                <?php else: ?>
                    No inquiries match the selected filter.
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php foreach($filteredInquiries as $inquiry): ?>
                <div class="card p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($inquiry['first_name'], 0, 1) . substr($inquiry['last_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">
                                    <?php echo htmlspecialchars($inquiry['first_name'] . ' ' . $inquiry['last_name']); ?>
                                </h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo date('M j, Y \a\t g:i A', strtotime($inquiry['created_at'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 text-sm rounded-full <?php 
                                echo match($inquiry['status']) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'responded' => 'bg-green-100 text-green-800',
                                    'closed' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            ?>">
                                <?php echo ucfirst($inquiry['status']); ?>
                            </span>
                            
                            <!-- Status Update Dropdown -->
                            <div class="relative group">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                                    <form method="POST" class="p-2">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                        
                                        <button type="submit" name="status" value="pending" 
                                                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                                            Mark as Pending
                                        </button>
                                        <button type="submit" name="status" value="responded" 
                                                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                                            Mark as Responded
                                        </button>
                                        <button type="submit" name="status" value="closed" 
                                                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                                            Mark as Closed
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h4 class="font-medium text-gray-900 mb-2">Product: <?php echo htmlspecialchars($inquiry['product_title']); ?></h4>
                        <p class="text-sm text-gray-600">Inquiry about this product</p>
                    </div>
                    
                    <!-- Customer Message -->
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 mb-2">Customer Message:</h4>
                        <p class="text-gray-600 bg-gray-50 p-3 rounded-lg">
                            <?php echo nl2br(htmlspecialchars($inquiry['message'])); ?>
                        </p>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Contact Information:</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p><span class="font-medium">Phone:</span> <?php echo htmlspecialchars($inquiry['phone']); ?></p>
                                <p><span class="font-medium">Preferred Contact:</span> <?php echo ucfirst($inquiry['contact_method']); ?></p>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Quick Actions:</h4>
                            <div class="flex space-x-2">
                                <button onclick="openWhatsApp('<?php echo $inquiry['phone']; ?>', 'Hi <?php echo $inquiry['first_name']; ?>, thank you for your inquiry about <?php echo urlencode($inquiry['product_title']); ?>')" 
                                        class="btn-primary text-sm px-3 py-1">
                                    WhatsApp
                                </button>
                                <button onclick="makeCall('<?php echo $inquiry['phone']; ?>')" 
                                        class="btn-secondary text-sm px-3 py-1">
                                    Call
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function openWhatsApp(phone, message) {
    const cleanPhone = phone.replace(/\D/g, '');
    const encodedMessage = encodeURIComponent(message);
    window.open(`https://wa.me/${cleanPhone}?text=${encodedMessage}`, '_blank');
}

function makeCall(phone) {
    window.location.href = `tel:${phone}`;
}

function sendEmail(email, subject, body) {
    const encodedSubject = encodeURIComponent(subject);
    const encodedBody = encodeURIComponent(body);
    window.location.href = `mailto:${email}?subject=${encodedSubject}&body=${encodedBody}`;
}

// Auto-hide success/error messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    });
}, 5000);
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>



