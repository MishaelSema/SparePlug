<?php
// Sample Data Generator for SparePlug
// Run this once to populate the database with sample data

require_once 'config/database.php';

try {
    // Add sample vendor
    $vendorData = [
        'first_name' => 'Jean',
        'last_name' => 'Pierre',
        'email' => 'jean@example.com',
        'phone' => '+237123456789',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'user_type' => 'vendor',
        'is_verified' => true
    ];
    
    $userId = $db->insert('users', $vendorData);
    
    // Add vendor profile
    $vendorProfile = [
        'user_id' => $userId,
        'business_name' => 'Auto Parts Plus',
        'business_type' => 'company',
        'description' => 'Leading supplier of quality auto parts in Douala',
        'address' => '123 Commercial Street, Douala',
        'city' => 'Douala',
        'region' => 'Littoral',
        'is_verified' => true,
        'subscription_plan' => 'basic'
    ];
    
    $vendorProfileId = $db->insert('vendor_profiles', $vendorProfile);
    
    // Add sample products
    $products = [
        [
            'vendor_id' => $vendorProfileId,
            'category_id' => 1, // Engine Parts
            'title' => 'Toyota Corolla Engine Oil Filter',
            'description' => 'High-quality engine oil filter compatible with Toyota Corolla 2000-2020. Original quality replacement part.',
            'price' => 15000,
            'condition' => 'new',
            'compatibility' => json_encode(['make_id' => 1, 'model_id' => 1, 'year' => '2000-2020']),
            'images' => json_encode(['assets/placeholder-product.svg']),
            'is_featured' => true,
            'is_active' => true
        ],
        [
            'vendor_id' => $vendorProfileId,
            'category_id' => 2, // Brake System
            'title' => 'Honda Civic Brake Pads Set',
            'description' => 'Complete set of brake pads for Honda Civic. Excellent stopping power and durability.',
            'price' => 45000,
            'condition' => 'new',
            'compatibility' => json_encode(['make_id' => 2, 'model_id' => 6, 'year' => '2010-2024']),
            'images' => json_encode(['assets/placeholder-product.svg']),
            'is_featured' => true,
            'is_active' => true
        ],
        [
            'vendor_id' => $vendorProfileId,
            'category_id' => 3, // Suspension
            'title' => 'Nissan Altima Shock Absorber',
            'description' => 'Front shock absorber for Nissan Altima. Smooth ride and improved handling.',
            'price' => 85000,
            'condition' => 'used',
            'compatibility' => json_encode(['make_id' => 3, 'model_id' => 10, 'year' => '2015-2020']),
            'images' => json_encode(['assets/placeholder-product.svg']),
            'is_featured' => false,
            'is_active' => true
        ],
        [
            'vendor_id' => $vendorProfileId,
            'category_id' => 4, // Electrical
            'title' => 'Ford Focus Alternator',
            'description' => 'High-output alternator for Ford Focus. Reliable power generation.',
            'price' => 120000,
            'condition' => 'refurbished',
            'compatibility' => json_encode(['make_id' => 4, 'model_id' => 11, 'year' => '2012-2018']),
            'images' => json_encode(['assets/placeholder-product.svg']),
            'is_featured' => true,
            'is_active' => true
        ],
        [
            'vendor_id' => $vendorProfileId,
            'category_id' => 5, // Body Parts
            'title' => 'BMW 3 Series Front Bumper',
            'description' => 'Original style front bumper for BMW 3 Series. Perfect fit and finish.',
            'price' => 180000,
            'condition' => 'used',
            'compatibility' => json_encode(['make_id' => 5, 'model_id' => 12, 'year' => '2010-2019']),
            'images' => json_encode(['assets/placeholder-product.svg']),
            'is_featured' => false,
            'is_active' => true
        ],
        [
            'vendor_id' => $vendorProfileId,
            'category_id' => 6, // Interior
            'title' => 'Mercedes C-Class Dashboard',
            'description' => 'Complete dashboard assembly for Mercedes C-Class. Excellent condition.',
            'price' => 250000,
            'condition' => 'used',
            'compatibility' => json_encode(['make_id' => 6, 'model_id' => 13, 'year' => '2014-2021']),
            'images' => json_encode(['assets/placeholder-product.svg']),
            'is_featured' => true,
            'is_active' => true
        ]
    ];
    
    foreach ($products as $product) {
        $db->insert('products', $product);
    }
    
    // Add some sample reviews
    $reviews = [
        [
            'product_id' => 1,
            'buyer_id' => 1, // Assuming first user is a buyer
            'vendor_id' => $vendorProfileId,
            'rating' => 5,
            'title' => 'Excellent Quality',
            'comment' => 'Great product, fast delivery, exactly as described.',
            'is_verified' => true
        ],
        [
            'product_id' => 2,
            'buyer_id' => 1,
            'vendor_id' => $vendorProfileId,
            'rating' => 4,
            'title' => 'Good Value',
            'comment' => 'Good quality brake pads, installation was easy.',
            'is_verified' => true
        ]
    ];
    
    foreach ($reviews as $review) {
        $db->insert('reviews', $review);
    }
    
    echo "Sample data added successfully!<br>";
    echo "Vendor: Auto Parts Plus<br>";
    echo "Products: 6 sample products<br>";
    echo "Reviews: 2 sample reviews<br>";
    echo "<a href='index.php'>Go to Homepage</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>



