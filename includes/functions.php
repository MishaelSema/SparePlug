<?php
require_once dirname(__DIR__) . '/config/database.php';

class User {
    private $db;
    
    public function __construct() {
        global $db;
        if (!isset($db) || $db === null) {
            $db = new Database();
        }
        $this->db = $db;
    }
    
    public function register($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Generate verification token
        $data['verification_token'] = bin2hex(random_bytes(32));
        
        return $this->db->insert('users', $data);
    }
    
    public function login($email, $password) {
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$email]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->db->update('users', 
                ['last_login' => date('Y-m-d H:i:s')], 
                'id = ?', 
                [$user['id']]
            );
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            return $user;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return $this->db->fetch(
            "SELECT * FROM users WHERE id = ?",
            [$_SESSION['user_id']]
        );
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isVendor() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'vendor';
    }
    
    public function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    }
    
    public function getVendorProfile($userId = null) {
        if (!$userId) {
            $userId = $_SESSION['user_id'];
        }
        
        return $this->db->fetch(
            "SELECT vp.*, u.email, u.phone, u.first_name, u.last_name 
             FROM vendor_profiles vp 
             JOIN users u ON vp.user_id = u.id 
             WHERE vp.user_id = ?",
            [$userId]
        );
    }
    
    public function createVendorProfile($data, $userId = null) {
        // Use provided userId or session userId
        $data['user_id'] = $userId ?? $_SESSION['user_id'] ?? null;
        if (!$data['user_id']) {
            throw new Exception('User ID is required to create vendor profile');
        }
        return $this->db->insert('vendor_profiles', $data);
    }
    
    public function updateVendorProfile($data, $userId = null) {
        if (!$userId) {
            $userId = $_SESSION['user_id'];
        }
        
        return $this->db->update('vendor_profiles', $data, 'user_id = ?', [$userId]);
    }
}

class Product {
    private $db;
    
    public function __construct() {
        global $db;
        if (!isset($db) || $db === null) {
            $db = new Database();
        }
        $this->db = $db;
    }
    
    public function getCount($filters = []) {
        $sql = "SELECT COUNT(DISTINCT p.id) as total
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN vendor_profiles vp ON p.vendor_id = vp.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['vendor_id'])) {
            $sql .= " AND p.vendor_id = ?";
            $params[] = $filters['vendor_id'];
        } else {
            $sql .= " AND p.is_active = 1";
        }

        if (isset($filters['featured'])) {
            $sql .= " AND p.is_featured = ?";
            $params[] = $filters['featured'] ? 1 : 0;
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $catFilter = $filters['category_id'] ?? $filters['category'] ?? null;
        if (!empty($catFilter)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $catFilter;
        }
        
        if (!empty($filters['year'])) {
            $sql .= " AND JSON_EXTRACT(p.compatibility, '$.year') = ?";
            $params[] = $filters['year'];
        }

        if (!empty($filters['make'])) {
            $sql .= " AND JSON_EXTRACT(p.compatibility, '$.make') = ?";
            $params[] = $filters['make'];
        }

        if (!empty($filters['model'])) {
            $sql .= " AND JSON_EXTRACT(p.compatibility, '$.model') = ?";
            $params[] = $filters['model'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['condition'])) {
            $sql .= " AND p.`condition` = ?";
            $params[] = $filters['condition'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND (vp.city LIKE ? OR vp.region LIKE ?)";
            $params[] = '%' . $filters['location'] . '%';
            $params[] = '%' . $filters['location'] . '%';
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total'];
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT p.*, c.name as category_name, vp.business_name, vp.city, vp.region, 
                       vp.is_verified, vp.subscription_plan,
                       AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
                FROM products p
                JOIN categories c ON p.category_id = c.id
                JOIN vendor_profiles vp ON p.vendor_id = vp.id
                LEFT JOIN reviews r ON p.id = r.product_id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['vendor_id'])) {
            $sql .= " AND p.vendor_id = ?";
            $params[] = $filters['vendor_id'];
        } else {
            $sql .= " AND p.is_active = 1";
        }

        if (isset($filters['featured'])) {
            $sql .= " AND p.is_featured = ?";
            $params[] = $filters['featured'] ? 1 : 0;
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $catFilter = $filters['category_id'] ?? $filters['category'] ?? null;
        if (!empty($catFilter)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $catFilter;
        }
        
        if (!empty($filters['year'])) {
            $sql .= " AND JSON_EXTRACT(p.compatibility, '$.year') = ?";
            $params[] = $filters['year'];
        }

        if (!empty($filters['make'])) {
            $sql .= " AND JSON_EXTRACT(p.compatibility, '$.make') = ?";
            $params[] = $filters['make'];
        }

        if (!empty($filters['model'])) {
            $sql .= " AND JSON_EXTRACT(p.compatibility, '$.model') = ?";
            $params[] = $filters['model'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['condition'])) {
            $sql .= " AND p.`condition` = ?";
            $params[] = $filters['condition'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND (vp.city LIKE ? OR vp.region LIKE ?)";
            $params[] = '%' . $filters['location'] . '%';
            $params[] = '%' . $filters['location'] . '%';
        }
        
        // Add sorting
        if (!empty($filters['sort'])) {
            switch($filters['sort']) {
                case 'price_low':
                    $sql .= " GROUP BY p.id ORDER BY p.price ASC";
                    break;
                case 'price_high':
                    $sql .= " GROUP BY p.id ORDER BY p.price DESC";
                    break;
                case 'rating':
                    $sql .= " GROUP BY p.id ORDER BY avg_rating DESC";
                    break;
                case 'popular':
                    $sql .= " GROUP BY p.id ORDER BY p.views_count DESC";
                    break;
                default:
                    $sql .= " GROUP BY p.id ORDER BY FIELD(vp.subscription_plan, 'premium', 'basic', 'free') ASC, avg_rating DESC, p.created_at DESC";
            }
        } else {
            // Default ranking: Subscription Plan > Rating > Newest
            $sql .= " GROUP BY p.id ORDER BY FIELD(vp.subscription_plan, 'premium', 'basic', 'free') ASC, avg_rating DESC, p.created_at DESC";
        }
        
        if (!empty($filters['offset'])) {
            $sql .= " LIMIT " . (int)$filters['offset'] . ", " . (int)$filters['limit'];
        } elseif (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($id) {
        return $this->db->fetch(
            "SELECT p.*, c.name as category_name, 
                    vp.business_name, vp.city, vp.region, vp.logo_url, vp.is_verified, vp.subscription_plan,
                    vp.user_id as seller_user_id,
                    u.email, u.phone,
                    AVG(r.rating) as avg_rating, COUNT(r.id) as review_count
             FROM products p
             JOIN categories c ON p.category_id = c.id
             JOIN vendor_profiles vp ON p.vendor_id = vp.id
             JOIN users u ON vp.user_id = u.id
             LEFT JOIN reviews r ON p.id = r.product_id
             WHERE p.id = ? AND p.is_active = 1
             GROUP BY p.id",
            [$id]
        );
    }
    
    public function create($data) {
        $data['vendor_id'] = $this->getVendorId();
        return $this->db->insert('products', $data);
    }
    
    public function update($id, $data) {
        return $this->db->update('products', $data, 'id = ? AND vendor_id = ?', 
            [$id, $this->getVendorId()]);
    }
    
    public function delete($id) {
        return $this->db->update('products', ['is_active' => 0], 
            'id = ? AND vendor_id = ?', [$id, $this->getVendorId()]);
    }
    
    public function incrementViews($id) {
        $this->db->query(
            "UPDATE products SET views_count = views_count + 1 WHERE id = ?",
            [$id]
        );
    }
    
    private function getVendorId() {
        $user = new User();
        $profile = $user->getVendorProfile();
        return $profile['id'];
    }
}

class Review {
    private $db;
    
    public function __construct() {
        global $db;
        if (!isset($db) || $db === null) {
            $db = new Database();
        }
        $this->db = $db;
    }
    
    public function create($data) {
        $data['buyer_id'] = $_SESSION['user_id'];
        
        // Fetch product and its vendor's user_id
        $product = $this->db->fetch(
            "SELECT p.vendor_id, vp.user_id as vendor_user_id 
             FROM products p 
             JOIN vendor_profiles vp ON p.vendor_id = vp.id 
             WHERE p.id = ?", 
            [$data['product_id']]
        );

        if (!$product) return false;

        // Block if buyer is the vendor owner
        if ($data['buyer_id'] == $product['vendor_user_id']) {
            return false;
        }

        if (!isset($data['vendor_id'])) {
            $data['vendor_id'] = $product['vendor_id'];
        }
        
        return $this->db->insert('reviews', $data);
    }
    
    public function getByProduct($productId) {
        return $this->db->fetchAll(
            "SELECT r.*, u.first_name, u.last_name
             FROM reviews r
             JOIN users u ON r.buyer_id = u.id
             WHERE r.product_id = ?
             ORDER BY r.created_at DESC",
            [$productId]
        );
    }
    
    public function getAverageRating($productId) {
        $result = $this->db->fetch(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
             FROM reviews WHERE product_id = ?",
            [$productId]
        );
        
        return [
            'average' => $result['avg_rating'] ? round($result['avg_rating'], 1) : 0,
            'total' => $result['total_reviews'] ?? 0
        ];
    }
}

class Category {
    private $db;
    
    public function __construct() {
        global $db;
        if (!isset($db) || $db === null) {
            $db = new Database();
        }
        $this->db = $db;
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM categories WHERE is_active = 1";
        $params = [];
        
        if (!empty($filters['parent_id'])) {
            $sql .= " AND parent_id = ?";
            $params[] = $filters['parent_id'];
        } else {
            $sql .= " AND parent_id IS NULL";
        }
        
        $sql .= " ORDER BY sort_order, name";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getById($id) {
        return $this->db->fetch(
            "SELECT * FROM categories WHERE id = ? AND is_active = 1",
            [$id]
        );
    }
    
    public function getSubcategories($parentId) {
        return $this->db->fetchAll(
            "SELECT * FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY sort_order, name",
            [$parentId]
        );
    }
}

class Inquiry {
    private $db;
    
    public function __construct() {
        global $db;
        if (!isset($db) || $db === null) {
            $db = new Database();
        }
        $this->db = $db;
    }
    
    public function create($data) {
        $data['buyer_id'] = $_SESSION['user_id'];
        
        // Increment inquiry count for product
        $this->db->query(
            "UPDATE products SET inquiries_count = inquiries_count + 1 WHERE id = ?",
            [$data['product_id']]
        );
        
        return $this->db->insert('product_inquiries', $data);
    }
    
    public function getByVendor($vendorId) {
        return $this->db->fetchAll(
            "SELECT pi.*, p.title as product_title, u.first_name, u.last_name, u.phone
             FROM product_inquiries pi
             JOIN products p ON pi.product_id = p.id
             JOIN users u ON pi.buyer_id = u.id
             WHERE p.vendor_id = ?
             ORDER BY pi.created_at DESC",
            [$vendorId]
        );
    }
    
    public function updateStatus($id, $status) {
        return $this->db->update('product_inquiries', 
            ['status' => $status], 
            'id = ?', 
            [$id]
        );
    }
}
/**
 * Helper to display vendor rank/verification badges
 */
function getVendorBadge($plan, $isVerified) {
    $badges = '';
    
    // Verification Badge
    if ($isVerified) {
        $badges .= '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-wider shadow-sm">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            Verified
        </span>';
    }
    
    // Plan Rank Badge
    if ($plan === 'premium') {
        $badges .= '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-bold uppercase tracking-wider shadow-sm ml-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            Gold Partner
        </span>';
    } elseif ($plan === 'basic') {
        $badges .= '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[10px] font-bold uppercase tracking-wider shadow-sm ml-1">
            Silver Partner
        </span>';
    }
    
    return $badges;
}

/**
 * Notification System
 */
class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureSchema();
    }

    private function ensureSchema() {
        try {
            // Create notifications table if not exists
            $this->db->query("CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                message TEXT NOT NULL,
                type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )");

            // Add logo_url and cover_url to vendor_profiles if they don't exist
            // Note: ADD COLUMN IF NOT EXISTS is MariaDB 10.2.19+, but standard MySQL might need a different check
            // We'll use a safe check via DESCRIBE or just let it fail silently if it exists
            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN logo_url VARCHAR(255) DEFAULT NULL");
            } catch (Exception $e) {}
            
            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN cover_url VARCHAR(255) DEFAULT NULL");
            } catch (Exception $e) {}

            // Socials and Additional Info
            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN website VARCHAR(255) DEFAULT NULL");
            } catch (Exception $e) {}

            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN facebook VARCHAR(255) DEFAULT NULL");
            } catch (Exception $e) {}

            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN instagram VARCHAR(255) DEFAULT NULL");
            } catch (Exception $e) {}

            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN twitter VARCHAR(255) DEFAULT NULL");
            } catch (Exception $e) {}

            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN google_map_url TEXT DEFAULT NULL");
            } catch (Exception $e) {}

            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN services TEXT DEFAULT NULL");
            } catch (Exception $e) {}

            try {
                $this->db->query("ALTER TABLE vendor_profiles ADD COLUMN other_info TEXT DEFAULT NULL");
            } catch (Exception $e) {}

        } catch (Exception $e) {
            // Log error or ignore if silent
        }
    }

    public static function send($userId, $message, $type = 'info') {
        $db = new Database();
        return $db->insert('notifications', [
            'user_id' => $userId,
            'message' => $message,
            'type' => $type
        ]);
    }

    public function getForUser($userId, $unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = ? ";
        if ($unreadOnly) $sql .= "AND is_read = 0 ";
        $sql .= "ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function markAsRead($id) {
        return $this->db->update('notifications', ['is_read' => 1], 'id = ?', [$id]);
    }

    public function getUnreadCount($userId) {
        $res = $this->db->fetch("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0", [$userId]);
        return $res['count'] ?? 0;
    }
}
?>
