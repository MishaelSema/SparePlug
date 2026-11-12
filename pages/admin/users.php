<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/Mailer.php';

$pageTitle = 'Manage Users';
$user = new User();

// Check if user is admin
if (!$user->isAdmin()) {
    header('Location: index.php?page=login');
    exit;
}

$db = new Database();
require_once 'includes/Mailer.php';
$mailer = new Mailer();

// Handle vendor verification
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'verify_vendor') {
        $vendorId = $_POST['vendor_id'] ?? null;
        if ($vendorId) {
            $db->update('vendor_profiles', ['is_verified' => 1], 'id = ?', [$vendorId]);
            
            $vendorUser = $db->fetch("SELECT u.email, u.first_name, vp.business_name FROM users u JOIN vendor_profiles vp ON u.id = vp.user_id WHERE vp.id = ?", [$vendorId]);
            if ($vendorUser) {
                $mailer->sendVerificationApproved($vendorUser['email'], $vendorUser['business_name']);
            }
            
            $_SESSION['admin_success'] = 'Vendor verified successfully!';
        }
    } elseif ($_POST['action'] === 'reject_vendor') {
        $vendorId = $_POST['vendor_id'] ?? null;
        $reason = $_POST['reject_reason'] ?? 'Documents could not be verified';
        if ($vendorId) {
            $db->update('vendor_profiles', ['is_verified' => 0], 'id = ?', [$vendorId]);
            
            $vendorUser = $db->fetch("SELECT u.email, u.first_name, vp.business_name FROM users u JOIN vendor_profiles vp ON u.id = vp.user_id WHERE vp.id = ?", [$vendorId]);
            if ($vendorUser) {
                $mailer->sendVerificationRejected($vendorUser['email'], $vendorUser['business_name'], $reason);
            }
            
            $_SESSION['admin_success'] = 'Vendor verification rejected.';
        }
    } elseif ($_POST['action'] === 'unverify_vendor') {
        $vendorId = $_POST['vendor_id'] ?? null;
        if ($vendorId) {
            $db->update('vendor_profiles', ['is_verified' => 0], 'id = ?', [$vendorId]);
            $_SESSION['admin_success'] = 'Vendor verification removed.';
        }
    } elseif ($_POST['action'] === 'suspend_user') {
        $userId = $_POST['user_id'] ?? null;
        if ($userId) {
            $db->update('users', ['status' => 'suspended'], 'id = ?', [$userId]);
            
            // Send email notification
            $userData = $db->fetch("SELECT first_name, last_name, email FROM users WHERE id = ?", [$userId]);
            if ($userData) {
                $mailer = new Mailer();
                $mailer->sendAccountStatusUpdate($userData['email'], $userData['first_name'] . ' ' . $userData['last_name'], 'suspended');
            }
            
            $_SESSION['admin_success'] = 'User suspended.';
        }
    } elseif ($_POST['action'] === 'activate_user') {
        $userId = $_POST['user_id'] ?? null;
        if ($userId) {
            $db->update('users', ['status' => 'active'], 'id = ?', [$userId]);
            
            // Send email notification
            $userData = $db->fetch("SELECT first_name, last_name, email FROM users WHERE id = ?", [$userId]);
            if ($userData) {
                $mailer = new Mailer();
                $mailer->sendAccountStatusUpdate($userData['email'], $userData['first_name'] . ' ' . $userData['last_name'], 'active');
            }
            
            $_SESSION['admin_success'] = 'User activated.';
        }
    } elseif ($_POST['action'] === 'send_notification') {
        $userId = $_POST['user_id'];
        $message = $_POST['message'];
        $type = $_POST['type'] ?? 'info';
        
        if ($userId && $message) {
            Notification::send($userId, $message, $type);
            
            // Also send as Email with type-based styling
            $userData = $db->fetch("SELECT first_name, last_name, email FROM users WHERE id = ?", [$userId]);
            if ($userData) {
                $mailer = new Mailer();
                $mailer->sendAdminNotification($userData['email'], $userData['first_name'] . ' ' . $userData['last_name'], $message, $type);
            }
            
            $_SESSION['admin_success'] = 'Notification sent successfully!';
        }
    }
}

// Get all users with vendor info
$allUsers = $db->fetchAll("
    SELECT u.*, 
           vp.is_verified as vendor_verified,
           vp.business_name,
           vp.business_type,
           vp.verification_documents,
           vp.id as vendor_id
    FROM users u
    LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
    ORDER BY u.created_at DESC
");

// Get pending verification vendors count
$pendingCount = $db->fetch("SELECT COUNT(*) as count FROM vendor_profiles WHERE is_verified = 0 AND verification_documents IS NOT NULL")['count'] ?? 0;

ob_start();
?>

<div class="p-8 space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manage Users</h1>
                <p class="text-gray-600 mt-1">View and manage all platform users</p>
            </div>
            <?php if ($pendingCount > 0): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2">
                <span class="text-yellow-800 font-medium"><?php echo $pendingCount; ?> pending verification<?php echo $pendingCount > 1 ? 's' : ''; ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">All Users</h2>
                <span class="text-sm text-gray-500">Total: <?php echo count($allUsers); ?></span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verification</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($allUsers as $userData): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                                    <?php echo strtoupper(substr($userData['first_name'], 0, 1)); ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($userData['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded <?php 
                                echo $userData['user_type'] === 'admin' ? 'bg-red-100 text-red-800' : 
                                    ($userData['user_type'] === 'vendor' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'); 
                            ?>">
                                <?php echo ucfirst($userData['user_type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded <?php 
                                echo $userData['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                    ($userData['status'] === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); 
                            ?>">
                                <?php echo ucfirst($userData['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($userData['user_type'] === 'vendor'): ?>
                                <?php if ($userData['vendor_verified']): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">Verified</span>
                                <?php elseif ($userData['verification_documents']): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Pending Review</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-600">Not Submitted</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('M d, Y', strtotime($userData['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <?php if ($userData['user_type'] === 'vendor'): ?>
                                    <?php if (!$userData['vendor_verified']): ?>
                                        <?php if ($userData['verification_documents']): ?>
                                            <button onclick="viewDocuments(<?php echo $userData['vendor_id']; ?>, '<?php echo htmlspecialchars($userData['business_name']); ?>')" class="text-blue-600 hover:text-blue-800 text-xs font-bold">
                                                📄 View Docs
                                            </button>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="verify_vendor">
                                                <input type="hidden" name="vendor_id" value="<?php echo $userData['vendor_id']; ?>">
                                                <button type="submit" class="text-green-600 hover:text-green-800" 
                                                        onclick="return confirm('Verify this vendor?')">✓ Approve</button>
                                            </form>
                                            <button onclick="showRejectModal(<?php echo $userData['vendor_id']; ?>)" class="text-red-600 hover:text-red-800">✗ Reject</button>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">Awaiting submission</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="unverify_vendor">
                                            <input type="hidden" name="vendor_id" value="<?php echo $userData['vendor_id']; ?>">
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800" 
                                                    onclick="return confirm('Remove verification from this vendor?')">✗ Unverify</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($userData['user_type'] !== 'admin'): ?>
                                    <?php if ($userData['status'] === 'active'): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="suspend_user">
                                            <input type="hidden" name="user_id" value="<?php echo $userData['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800" 
                                                    onclick="return confirm('Suspend this user?')">Suspend</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="activate_user">
                                            <input type="hidden" name="user_id" value="<?php echo $userData['id']; ?>">
                                            <button type="submit" class="text-green-600 hover:text-green-800" 
                                                    onclick="return confirm('Activate this user?')">Activate</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <button onclick='openNotifyModal(<?php echo $userData['id']; ?>, "<?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?>")' 
                                        class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-wider">
                                    Notify
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Send Notification Modal -->
<div id="notifyModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Send Notification</h2>
            <button onclick="document.getElementById('notifyModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-4">
            <input type="hidden" name="action" value="send_notification">
            <input type="hidden" name="user_id" id="notify_user_id">
            
            <div class="p-3 bg-blue-50 text-blue-700 text-sm rounded-xl mb-4">
                Sending message to: <span id="notify_user_name" class="font-bold"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Message Type</label>
                <select name="type" class="input-modern">
                    <option value="info">Information (Blue)</option>
                    <option value="success">Success (Green)</option>
                    <option value="warning">Warning (Yellow)</option>
                    <option value="error">Action Required (Red)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Message Content</label>
                <textarea name="message" required rows="4" class="input-modern" placeholder="Type your message to the vendor..."></textarea>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3.5 mt-4">Send Message</button>
        </form>
    </div>
</div>

<script>
function openNotifyModal(userId, userName) {
    document.getElementById('notify_user_id').value = userId;
    document.getElementById('notify_user_name').textContent = userName;
    document.getElementById('notifyModal').classList.remove('hidden');
}

function showRejectModal(vendorId) {
    document.getElementById('reject_vendor_id').value = vendorId;
    document.getElementById('rejectModal').classList.remove('hidden');
}
</script>

<!-- Reject Verification Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Reject Verification</h2>
        <p class="text-gray-600 mb-4">Please provide a reason for rejecting this vendor's verification.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="reject_vendor">
            <input type="hidden" name="vendor_id" id="reject_vendor_id" value="">
            
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2">Rejection Reason</label>
                <select name="reject_reason" class="input-modern">
                    <option value="Documents are unclear or unreadable">Documents are unclear or unreadable</option>
                    <option value="Business registration document is invalid">Business registration document is invalid</option>
                    <option value="Documents appear to be tampered with">Documents appear to be tampered with</option>
                    <option value="Business information does not match documents">Business information does not match documents</option>
                    <option value="Other">Other (specify in message)</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="btn-secondary flex-1">Cancel</button>
                <button type="submit" class="btn-primary flex-1 bg-red-600 hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>

<!-- View Documents Modal -->
<div id="docsModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Verification Documents</h2>
            <button onclick="document.getElementById('docsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="mb-4">
            <span class="text-sm text-gray-500">Vendor: </span>
            <span id="docs_vendor_name" class="font-semibold text-gray-900"></span>
        </div>
        
        <div id="docs_content" class="space-y-4">
            <!-- Documents will be loaded here -->
        </div>
        
        <div class="mt-6 flex gap-3">
            <button onclick="document.getElementById('docsModal').classList.add('hidden')" class="btn-secondary flex-1">Close</button>
        </div>
    </div>
</div>

<script>
function viewDocuments(vendorId, vendorName) {
    document.getElementById('docs_vendor_name').textContent = vendorName;
    document.getElementById('docsModal').classList.remove('hidden');
    
    // Fetch documents via AJAX
    fetch('api/get-vendor-docs.php?vendor_id=' + vendorId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('docs_content');
            if (data.success && data.documents) {
                const docs = data.documents;
                let html = '';
                
                if (docs.registration_doc) {
                    html += '<div class="border border-gray-200 rounded-lg p-4">';
                    html += '<p class="font-medium text-gray-900 mb-2">Business Registration Document</p>';
                    html += '<a href="' + docs.registration_doc + '" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2">';
                    html += '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
                    html += 'View Document</a>';
                    if (docs.uploaded_at) {
                        html += '<p class="text-xs text-gray-500 mt-2">Uploaded: ' + docs.uploaded_at + '</p>';
                    }
                    html += '</div>';
                } else {
                    html += '<p class="text-gray-500">No documents found.</p>';
                }
                
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-gray-500">No documents found.</p>';
            }
        })
        .catch(err => {
            document.getElementById('docs_content').innerHTML = '<p class="text-red-500">Error loading documents.</p>';
        });
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>

