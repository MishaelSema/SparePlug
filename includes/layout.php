<?php
// Immediate Suspension Check
if (isset($_SESSION['user_id'])) {
    $checkUser = $db->fetch("SELECT status FROM users WHERE id = ?", [$_SESSION['user_id']]);
    if ($checkUser && $checkUser['status'] === 'suspended') {
        session_destroy();
        header('Location: index.php?page=login&error=suspended');
        exit;
    }
}

// Get vendor profile for verification badge
$layoutVendorProfile = null;
$layoutIsVerified = true;
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'vendor' && isset($_SESSION['user_id'])) {
    $layoutVendorProfile = $db->fetch("SELECT is_verified, verification_documents FROM vendor_profiles WHERE user_id = ?", [$_SESSION['user_id']]);
    $layoutIsVerified = $layoutVendorProfile['is_verified'] ?? false;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>sparePlug - Premium Auto Parts Marketplace</title>
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - sparePlug' : 'sparePlug - Premium Auto Parts Marketplace'; ?>">
    <meta property="og:description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Cameroon\'s premier marketplace for authentic auto spare parts. Connect with verified vendors today.'; ?>">
    <meta property="og:image" content="<?php echo isset($pageImage) ? $pageImage : 'http://' . $_SERVER['HTTP_HOST'] . preg_replace('#/[^/]*$#', '', $_SERVER['SCRIPT_NAME']) . '/assets/sparePlugLogoForLightBackgroundsPNG.png'; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - sparePlug' : 'sparePlug - Premium Auto Parts Marketplace'; ?>">
    <meta property="twitter:description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Cameroon\'s premier marketplace for authentic auto spare parts. Connect with verified vendors today.'; ?>">
    <meta property="twitter:image" content="<?php echo isset($pageImage) ? $pageImage : 'http://' . $_SERVER['HTTP_HOST'] . preg_replace('#/[^/]*$#', '', $_SERVER['SCRIPT_NAME']) . '/assets/sparePlugLogoForLightBackgroundsPNG.png'; ?>">

    <!-- Adaptive Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/sparePlugFaviconLightSVG.svg" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/svg+xml" href="assets/sparePlugFaviconDarkSVG.svg" media="(prefers-color-scheme: dark)">
    <!-- Fallback for older browsers -->
    <link rel="alternate icon" type="image/png" href="assets/sparePlugFaviconLightPNG.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts: Outfit (Headings) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'heading': ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#0F172A', // Midnight Blue
                            light: '#1E293B',
                            dark: '#020617',
                        },
                        accent: {
                            DEFAULT: '#EF4444', // Vibrant Red
                            hover: '#DC2626',
                        },
                        gray: {
                            50: '#F8FAFC',
                            100: '#F1F5F9',
                            200: '#E2E8F0',
                            300: '#CBD5E1',
                            400: '#94A3B8',
                            500: '#64748B',
                            600: '#475569',
                            700: '#334155',
                            800: '#1E293B',
                            900: '#0F172A',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'glow': '0 0 15px rgba(239, 68, 68, 0.3)',
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025)',
                    },
                    backdropBlur: {
                        'xs': '2px',
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        /* Base Overrides */
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        
        /* Glassmorphism Utilities */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        /* Modern Card Styles */
        .card-modern {
            @apply bg-white border border-gray-100 rounded-2xl shadow-sm transition-all duration-300 hover:shadow-card hover:-translate-y-1;
        }
        
        /* Modern Button Styles */
        .btn-primary {
            @apply bg-accent text-white px-6 py-2.5 rounded-xl font-semibold shadow-lg shadow-red-500/20 transition-all duration-300 hover:bg-accent-hover hover:shadow-red-500/40 active:scale-95 flex items-center justify-center;
        }
        
        .btn-secondary {
            @apply bg-white text-gray-700 border border-gray-200 px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-gray-50 hover:border-gray-300 active:scale-95 flex items-center justify-center;
        }

        .btn-outline {
            @apply border border-white/20 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-white/10 active:scale-95 block text-center;
        }
        
        /* Input Styles */
        .input-modern {
            @apply w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all duration-200;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Prevent selects from expanding too wide */
        select.input-modern, select.input-field {
            max-width: 100%;
        }
    </style>
</head>
<body class="font-sans bg-gray-50 text-gray-800 antialiased selection:bg-accent selection:text-white">

    <!-- Top Navigation (Sticky & Glass) -->
    <nav class="fixed w-full z-50 glass border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo & Brand -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <img src="assets/sparePlugLogoForLightBackgroundsSVG.svg" alt="sparePlug Logo" class="h-10 w-auto">
                    </a>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:ml-12 md:flex md:space-x-8">
                        <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors duration-200 py-2 border-b-2 border-transparent hover:border-accent" data-translate="home">Home</a>
                        <a href="index.php?page=search" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors duration-200 py-2 border-b-2 border-transparent hover:border-accent" data-translate="findParts">Find Parts</a>
                        <a href="index.php?page=categories" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors duration-200 py-2 border-b-2 border-transparent hover:border-accent" data-translate="categories">Categories</a>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    
                    <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])): ?>
                        <?php if ($_SESSION['user_type'] === 'vendor'): 
                            $notif = new Notification();
                            $unreadCount = $notif->getUnreadCount($_SESSION['user_id']);
                        ?>
                        <a href="index.php?page=vendor-notifications" class="relative p-2 text-gray-500 hover:text-accent transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <?php if ($unreadCount > 0): ?>
                                <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-accent text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                    <?php echo $unreadCount; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <?php endif; ?>

                        <!-- User Menu -->
                        <div class="relative group">
                            <button class="flex items-center gap-3 pl-3 pr-2 py-1.5 rounded-full border border-gray-200 hover:border-gray-300 transition-all">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                                </div>
                                <span class="text-sm font-medium text-gray-700 hidden sm:block">
                                    <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                                </span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            
                            <!-- Dropdown -->
                            <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Signed in as</p>
                                    <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                                </div>
                                <div class="py-2">
                                    <?php if ($_SESSION['user_type'] === 'vendor'): ?>
                                        <?php if (!$layoutIsVerified): ?>
                                            <div class="px-4 py-2 bg-yellow-50 border-b border-yellow-100">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    <span class="text-xs font-semibold text-yellow-800">Verification Required</span>
                                                </div>
                                                <p class="text-xs text-yellow-700 mt-1">Upload documents to start selling</p>
                                                <a href="index.php?page=vendor-verify" class="text-xs text-primary font-medium mt-2 inline-block hover:underline">Upload Now →</a>
                                            </div>
                                        <?php endif; ?>
                                        <a href="index.php?page=vendor-dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Vendor Dashboard</a>
                                        <?php if (!$layoutIsVerified): ?>
                                        <a href="index.php?page=vendor-verify" class="block px-4 py-2 text-sm text-yellow-700 bg-yellow-50 hover:bg-yellow-100">⚠ Verify Your Shop</a>
                                        <?php endif; ?>
                                        <a href="index.php?page=vendor-orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Orders</a>
                                    <?php elseif ($_SESSION['user_type'] === 'admin'): ?>
                                        <a href="index.php?page=admin-dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Admin Dashboard</a>
                                        <a href="index.php?page=admin-financials" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">💰 Financials</a>
                                        <a href="index.php?page=admin-settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">⚙️ Payment Settings</a>
                                    <?php else: ?>
                                        <a href="index.php?page=my-orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">My Orders</a>
                                        <a href="index.php?page=profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">My Profile</a>
                                    <?php endif; ?>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Sign Out</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="hidden md:flex items-center gap-3">
                            <a href="index.php?page=login" class="text-sm font-semibold text-gray-700 hover:text-primary transition-colors">Log In</a>
                            <a href="index.php?page=vendor-signup" class="btn-primary text-sm px-5 py-2">
                                Become a Vendor
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button class="md:hidden p-2 text-gray-600 hover:text-primary" id="mobile-menu-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div class="md:hidden hidden bg-white border-b border-gray-200 absolute w-full z-40 top-20 shadow-lg" id="mobile-menu">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50" data-translate="home">Home</a>
                <a href="index.php?page=search" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50" data-translate="findParts">Find Parts</a>
                <a href="index.php?page=categories" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50" data-translate="categories">Categories</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="index.php?page=login" class="block text-center w-full btn-secondary mb-3" data-translate="login">Log In</a>
                        <a href="index.php?page=vendor-signup" class="block text-center w-full btn-primary" data-translate="becomeVendor">Become a Vendor</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper: Add padding for fixed nav -->
    <div class="pt-20 min-h-screen flex flex-col">
        <main class="flex-grow">
            <?php echo $content; ?>
        </main>
        
        <!-- Premium Footer -->
        <footer class="bg-primary text-white pt-16 pb-8 border-t border-white/10 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                    <!-- Brand Column -->
                    <div>
                        <div class="mb-6">
                            <img src="assets/sparePlugLogoForDarkBackgroundsSVG.svg" alt="sparePlug Logo" class="h-10 w-auto">
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6" data-translate="aboutSparePlug">
                            Cameroon's premier marketplace for authentic auto spare parts. We connect you with verified vendors for quality you can trust.
                        </p>
                    </div>

                    <!-- Links Column 1 -->
                    <div>
                        <h3 class="text-white font-heading font-semibold mb-6" data-translate="quickLinks">Marketplace</h3>
                        <ul class="space-y-4 text-sm text-gray-400">
                            <li><a href="index.php?page=search" class="hover:text-white transition-colors" data-translate="exploreParts">Search Parts</a></li>
                            <li><a href="index.php?page=categories" class="hover:text-white transition-colors" data-translate="categories">Browse Categories</a></li>
                        </ul>
                    </div>

                    <!-- Links Column 2 -->
                    <div>
                        <h3 class="text-white font-heading font-semibold mb-6" data-translate="support">Support</h3>
                        <ul class="space-y-4 text-sm text-gray-400">
                            <li><a href="index.php?page=about" class="hover:text-white transition-colors" data-translate="aboutUs">About Us</a></li>
                            <li><a href="index.php?page=contact" class="hover:text-white transition-colors" data-translate="contactUs">Contact Us</a></li>
                            <li><a href="index.php?page=faq" class="hover:text-white transition-colors">FAQ</a></li>
                            <li><a href="index.php?page=safety-tips" class="hover:text-white transition-colors">Safety Tips</a></li>
                            <li><a href="index.php?page=terms" class="hover:text-white transition-colors" data-translate="terms">Terms of Service</a></li>
                        </ul>
                    </div>

                    <!-- Newsletter/Contact -->
                    <div>
                        <h3 class="text-white font-heading font-semibold mb-6">Contact</h3>
                        <ul class="space-y-4 text-sm text-gray-400">
                            <li>mishaelsema@gmail.com</li>
                            <li>Yaoundé, Cameroon</li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                    <p>&copy; <?php echo date('Y'); ?> sparePlug Ltd. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Est. 2024</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Mobile Menu Script -->
    <script>
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if(mobileBtn && mobileMenu){
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
    
    <!-- Language Translation System -->
    <script>
        const translations = {
            en: {
                // Navigation
                home: 'Home',
                findParts: 'Find Parts',
                categories: 'Categories',
                login: 'Log In',
                becomeVendor: 'Become a Vendor',
                vendorDashboard: 'Vendor Dashboard',
                myProducts: 'My Products',
                adminDashboard: 'Admin Dashboard',
                myProfile: 'My Profile',
                signOut: 'Sign Out',
                
                // Auth
                email: 'Email',
                password: 'Password',
                confirmPassword: 'Confirm Password',
                firstName: 'First Name',
                lastName: 'Last Name',
                phone: 'Phone',
                rememberMe: 'Remember Me',
                forgotPassword: 'Forgot Password?',
                createAccount: 'Create Account',
                alreadyHaveAccount: 'Already have an account?',
                dontHaveAccount: "Don't have an account?",
                signUp: 'Sign Up',
                signIn: 'Sign In',
                or: 'or',
                continueWithGoogle: 'Continue with Google',
                resetPassword: 'Reset Password',
                backToLogin: 'Back to Login',
                enterEmail: 'Enter your email',
                sendResetLink: 'Send Reset Link',
                
                // Search
                searchResults: 'Search Results',
                for: 'for',
                foundParts: 'parts matching your criteria',
                filters: 'Filters',
                reset: 'Reset',
                applyFilters: 'Apply Filters',
                noPartsFound: 'No parts found',
                clearAllFilters: 'Clear All Filters',
                cantFindParts: "We couldn't find any parts matching your specific criteria. Try adjusting your filters or search for something else.",
                
                // Filters
                allCategories: 'All Categories',
                anyYear: 'Any Year',
                selectMake: 'Select Make',
                selectModel: 'Select Model',
                priceRange: 'Price Range (FCFA)',
                min: 'Min',
                max: 'Max',
                conditionLabel: 'Condition',
                any: 'Any',
                new: 'New',
                used: 'Used',
                category: 'Category',
                year: 'Year',
                make: 'Make',
                model: 'Model',
                
                // Home
                shopByCategory: 'Shop by Category',
                featuredParts: 'Featured Parts',
                viewAll: 'View All',
                explore: 'Explore',
                partName: 'Part name...',
                popularVehicleMakes: 'Popular Vehicle Makes',
                findPartsFor: 'Find parts for the most popular cars in Cameroon',
                exploreParts: 'Explore Parts',
                
                // Product
                productDetails: 'Product Details',
                sellerInfo: 'Seller Information',
                contactSeller: 'Contact Seller',
                specifications: 'Specifications',
                reviews: 'Reviews',
                writeReview: 'Write a Review',
                priceLabel: 'Price',
                conditionLabel: 'Condition',
                location: 'Location',
                posted: 'Posted',
                viewAllReviews: 'View All Reviews',
                noReviewsYet: 'No reviews yet',
                beFirstReview: 'Be the first to review this product',
                addToCart: 'Add to Cart',
                buyNow: 'Buy Now',
                description: 'Description',
                relatedProducts: 'Related Products',
                compatibleVehicle: 'Compatible Vehicle',
                customerReviews: 'Customer Reviews',
                soldBy: 'Sold By',
                viewVendorProfile: 'View Vendor Profile',
                safetyTips: 'Safety Tips',
                whatsapp: 'Chat on WhatsApp',
                callSeller: 'Call Seller',
                sendMessage: 'Send Message',
                loginToReview: 'Log in to write a review',
                views: 'Views',
                outOf: 'out of 5',
                
                // Vendor Profile
                vendorProfile: 'Vendor Profile',
                verified: 'Verified',
                contactInfo: 'Contact Information',
                businessHours: 'Business Hours',
                products: 'Products',
                reviewsLabel: 'Reviews',
                rating: 'Rating',
                memberSince: 'Member Since',
                viewShop: 'View Shop',
                
                // Categories
                browseCategories: 'Browse Categories',
                allCategoriesTitle: 'All Categories',
                exploreAutoParts: 'Explore Auto Parts',
                browseCatalog: 'Browse our comprehensive catalog of high-quality spare parts for every vehicle make and model.',
                browseCatalogLink: 'Browse Catalog →',
                cantFindWhat: "Cant find what you need?",
                vendorsAddDaily: "Our verified vendors add new parts daily. Use our advanced search or contact support for help.",
                searchAllParts: "Search All Parts",
                
                // Auth Pages
                welcomeBack: 'Welcome Back',
                loginSubtitle: 'Sign in to continue to SparePlug',
                registerSubtitle: 'Create your account to get started',
                forgotPasswordSubtitle: 'Enter your email to reset your password',
                resetPasswordSubtitle: 'Create a new password for your account',
                
                // About
                aboutUs: 'About Us',
                ourStory: 'Our Story',
                mission: 'Our Mission',
                vision: 'Our Vision',
                team: 'Our Team',
                
                // Contact
                contactUs: 'Contact Us',
                getInTouch: 'Get in Touch',
                sendMessage: 'Send Message',
                yourName: 'Your Name',
                subject: 'Subject',
                message: 'Message',
                name: 'Name',
                
                // FAQ
                faq: 'FAQ',
                frequentlyAsked: 'Frequently Asked Questions',
                question: 'Question',
                answer: 'Answer',
                
                // Safety
                safetyTips: 'Safety Tips',
                staySafe: 'Stay Safe',
                
                // Terms
                termsConditions: 'Terms & Conditions',
                acceptTerms: 'I accept the Terms and Conditions',
                
                // Auth Labels
                orContinueWith: 'or continue with',
                byContinuing: 'By continuing, you agree to our',
                
                // Vendor
                addProduct: 'Add Product',
                editProduct: 'Edit Product',
                deleteProduct: 'Delete Product',
                productTitle: 'Product Title',
                productDescription: 'Product Description',
                regularPrice: 'Regular Price',
                salePrice: 'Sale Price',
                sku: 'SKU',
                stock: 'Stock',
                status: 'Status',
                active: 'Active',
                inactive: 'Inactive',
                save: 'Save',
                cancel: 'Cancel',
                delete: 'Delete',
                edit: 'Edit',
                view: 'View',
                dashboard: 'Dashboard',
                analytics: 'Analytics',
                inquiries: 'Inquiries',
                pricing: 'Pricing',
                subscription: 'Subscription',
                upgrade: 'Upgrade',
                currentPlan: 'Current Plan',
                billingCycle: 'Billing Cycle',
                monthly: 'Monthly',
                annual: 'Annual',
                features: 'Features',
                limit: 'Limit',
                unlimited: 'Unlimited',
                listings: 'listings',
                
                // Admin
                users: 'Users',
                productsLabel: 'Products',
                totalSales: 'Total Sales',
                totalRevenue: 'Total Revenue',
                pendingOrders: 'Pending Orders',
                recentOrders: 'Recent Orders',
                manage: 'Manage',
                settings: 'Settings',
                
                // Messages
                success: 'Success',
                error: 'Error',
                warning: 'Warning',
                info: 'Info',
                loading: 'Loading...',
                noData: 'No data available',
                confirmDelete: 'Are you sure you want to delete this?',
                yes: 'Yes',
                no: 'No',
                ok: 'OK',
                close: 'Close',
                
                // Misc
                submit: 'Submit',
                submitReview: 'Submit Review',
                sortBy: 'Sort By',
                newest: 'Newest',
                priceLowHigh: 'Price: Low to High',
                priceHighLow: 'Price: High to Low',
                bestRated: 'Best Rated',
                popular: 'Popular',
                results: 'results',
                page: 'Page',
                of: 'of',
                previous: 'Previous',
                next: 'Next',
                first: 'First',
                last: 'Last',
                showing: 'Showing',
                to: 'to',
                
                // Why Choose
                whyChooseSparePlug: 'Why Choose SparePlug',
                verifiedVendors: 'Verified Vendors',
                verifiedVendorsDesc: 'All sellers are verified to ensure authentic parts and trustworthy transactions.',
                directCommunication: 'Direct Communication',
                directCommunicationDesc: 'Chat directly with sellers via WhatsApp, phone, or in-app messaging.',
                fairPricing: 'Fair Pricing',
                fairPricingDesc: 'Compare prices across multiple vendors to get the best deal in XAF.',
                trustedPartner: 'Your Trusted Auto Parts Partner',
                trustedPartnerDesc: "Finding the right part shouldn't be a struggle. We connect you with a network of verified local vendors to ensure quality, reliability, and fair pricing for every search.",
                readyToSell: 'Ready to Sell Your Parts?',
                startSellingNow: 'Start Selling Now',
                findPerfect: 'Find the Perfect',
                partsForYourRide: 'Parts for Your Ride',
                connectDirect: 'Connect directly with verified vendors. Get quality spare parts delivered or picked up instantly. fast, secure, and reliable.',
                
                // Footer
                aboutSparePlug: "Cameroon's premier marketplace for authentic auto spare parts. We connect you with verified vendors for quality you can trust.",
                quickLinks: 'Quick Links',
                support: 'Support',
                allRightsReserved: 'All rights reserved.',
                terms: 'Terms & Conditions',
                privacy: 'Privacy Policy'
            },
            fr: {
                // Navigation
                home: 'Accueil',
                findParts: 'Trouver des pièces',
                categories: 'Catégories',
                login: 'Connexion',
                becomeVendor: 'Devenir vendeur',
                vendorDashboard: 'Tableau de bord',
                myProducts: 'Mes produits',
                adminDashboard: 'Admin Dashboard',
                myProfile: 'Mon profil',
                signOut: 'Déconnexion',
                
                // Auth
                email: 'Email',
                password: 'Mot de passe',
                confirmPassword: 'Confirmer le mot de passe',
                firstName: 'Prénom',
                lastName: 'Nom',
                phone: 'Téléphone',
                rememberMe: 'Se souvenir de moi',
                forgotPassword: 'Mot de passe oublié?',
                createAccount: 'Créer un compte',
                alreadyHaveAccount: 'Déjà un compte?',
                dontHaveAccount: 'Pas de compte?',
                signUp: 'S\'inscrire',
                signIn: 'Se connecter',
                or: 'ou',
                continueWithGoogle: 'Continuer avec Google',
                resetPassword: 'Réinitialiser le mot de passe',
                backToLogin: 'Retour à la connexion',
                enterEmail: 'Entrez votre email',
                sendResetLink: 'Envoyer le lien',
                
                // Search
                searchResults: 'Résultats de recherche',
                for: 'pour',
                foundParts: 'pièces correspondant à vos critères',
                filters: 'Filtres',
                reset: 'Réinitialiser',
                applyFilters: 'Appliquer',
                noPartsFound: 'Aucune pièce trouvée',
                clearAllFilters: 'Effacer tout',
                cantFindParts: "Aucune pièce ne correspond à vos critères. Essayez d'ajuster vos filtres ou recherchez autre chose.",
                
                // Filters
                allCategories: 'Toutes les catégories',
                anyYear: 'Toute année',
                selectMake: 'Sélectionner marque',
                selectModel: 'Sélectionner modèle',
                priceRange: 'Fourchette de prix (FCFA)',
                min: 'Min',
                max: 'Max',
                conditionLabel: 'État',
                any: 'Tous',
                new: 'Neuf',
                used: 'Occasion',
                category: 'Catégorie',
                year: 'Année',
                make: 'Marque',
                model: 'Modèle',
                
                // Home
                shopByCategory: 'Acheter par catégorie',
                featuredParts: 'Pièces en vedette',
                viewAll: 'Voir tout',
                explore: 'Explorer',
                partName: 'Nom de la pièce...',
                popularVehicleMakes: 'Marques populaires',
                findPartsFor: 'Trouvez des pièces pour les voitures les plus populaires au Cameroun',
                exploreParts: 'Explorer les pièces',
                
                // Product
                productDetails: 'Détails du produit',
                sellerInfo: 'Informations vendeur',
                contactSeller: 'Contacter le vendeur',
                specifications: 'Spécifications',
                reviews: 'Avis',
                writeReview: 'Écrire un avis',
                priceLabel: 'Prix',
                conditionLabel: 'État',
                location: 'Emplacement',
                posted: 'Publié',
                viewAllReviews: 'Voir tous les avis',
                noReviewsYet: 'Pas encore d\'avis',
                beFirstReview: 'Soyez le premier à donner votre avis sur ce produit',
                addToCart: 'Ajouter au panier',
                buyNow: 'Acheter maintenant',
                description: 'Description',
                relatedProducts: 'Produits similaires',
                compatibleVehicle: 'Véhicule compatible',
                customerReviews: 'Avis clients',
                soldBy: 'Vendu par',
                viewVendorProfile: 'Voir le profil du vendeur',
                safetyTips: 'Conseils de sécurité',
                whatsapp: 'Discuter sur WhatsApp',
                callSeller: 'Appeler le vendeur',
                sendMessage: 'Envoyer un message',
                loginToReview: 'Connectez-vous pour donner votre avis',
                views: 'Vues',
                outOf: 'sur 5',
                
                // Vendor Profile
                vendorProfile: 'Profil du vendeur',
                verified: 'Vérifié',
                contactInfo: 'Informations de contact',
                businessHours: 'Heures d\'ouverture',
                products: 'Produits',
                reviewsLabel: 'Avis',
                rating: 'Note',
                memberSince: 'Membre depuis',
                viewShop: 'Voir la boutique',
                
                // Categories
                browseCategories: 'Parcourir les catégories',
                allCategoriesTitle: 'Toutes les catégories',
                exploreAutoParts: 'Explorer les pièces automobiles',
                browseCatalog: 'Parcourez notre catalogue complet de pièces de rechange de haute qualité pour toutes les marques et modèles de véhicules.',
                browseCatalogLink: 'Parcourir le catalogue →',
                cantFindWhat: "Vous ne trouvez pas ce que vous cherchez?",
                vendorsAddDaily: "Nos vendeurs vérifiés ajoutent de nouvelles pièces quotidiennement. Utilisez notre recherche avancée ou contactez le support.",
                searchAllParts: "Rechercher toutes les pièces",
                
                // Auth Pages
                welcomeBack: 'Bon retour',
                loginSubtitle: 'Connectez-vous pour continuer sur SparePlug',
                registerSubtitle: 'Créez votre compte pour commencer',
                forgotPasswordSubtitle: 'Entrez votre email pour réinitialiser votre mot de passe',
                resetPasswordSubtitle: 'Créez un nouveau mot de passe pour votre compte',
                
                // About
                aboutUs: 'À propos de nous',
                ourStory: 'Notre histoire',
                mission: 'Notre mission',
                vision: 'Notre vision',
                team: 'Notre équipe',
                
                // Contact
                contactUs: 'Contactez-nous',
                getInTouch: 'Entrer en contact',
                sendMessage: 'Envoyer le message',
                yourName: 'Votre nom',
                subject: 'Sujet',
                message: 'Message',
                name: 'Nom',
                
                // FAQ
                faq: 'FAQ',
                frequentlyAsked: 'Questions fréquemment posées',
                question: 'Question',
                answer: 'Réponse',
                
                // Safety
                safetyTips: 'Conseils de sécurité',
                staySafe: 'Restez en sécurité',
                
                // Terms
                termsConditions: 'Conditions générales',
                acceptTerms: 'J\'accepte les conditions générales',
                
                // Auth Labels
                orContinueWith: 'ou continuer avec',
                byContinuing: 'En continuant, vous acceptez notre',
                
                // Vendor
                addProduct: 'Ajouter un produit',
                editProduct: 'Modifier le produit',
                deleteProduct: 'Supprimer le produit',
                productTitle: 'Titre du produit',
                productDescription: 'Description du produit',
                regularPrice: 'Prix régulier',
                salePrice: 'Prix de vente',
                sku: 'SKU',
                stock: 'Stock',
                status: 'Statut',
                active: 'Actif',
                inactive: 'Inactif',
                save: 'Enregistrer',
                cancel: 'Annuler',
                delete: 'Supprimer',
                edit: 'Modifier',
                view: 'Voir',
                dashboard: 'Tableau de bord',
                analytics: 'Analytique',
                inquiries: 'Demandes',
                pricing: 'Tarification',
                subscription: 'Abonnement',
                upgrade: 'Mettre à niveau',
                currentPlan: 'Plan actuel',
                billingCycle: 'Cycle de facturation',
                monthly: 'Mensuel',
                annual: 'Annuel',
                features: 'Fonctionnalités',
                limit: 'Limite',
                unlimited: 'Illimité',
                listings: 'annonces',
                
                // Admin
                users: 'Utilisateurs',
                productsLabel: 'Produits',
                totalSales: 'Ventes totales',
                totalRevenue: 'Revenu total',
                pendingOrders: 'Commandes en attente',
                recentOrders: 'Commandes récentes',
                manage: 'Gérer',
                settings: 'Paramètres',
                
                // Messages
                success: 'Succès',
                error: 'Erreur',
                warning: 'Avertissement',
                info: 'Info',
                loading: 'Chargement...',
                noData: 'Aucune donnée disponible',
                confirmDelete: 'Êtes-vous sûr de vouloir supprimer ceci?',
                yes: 'Oui',
                no: 'Non',
                ok: 'OK',
                close: 'Fermer',
                
                // Misc
                submit: 'Soumettre',
                submitReview: 'Soumettre l\'avis',
                sortBy: 'Trier par',
                newest: 'Plus récent',
                priceLowHigh: 'Prix: croissant',
                priceHighLow: 'Prix: décroissant',
                bestRated: 'Mieux notés',
                popular: 'Populaire',
                results: 'résultats',
                page: 'Page',
                of: 'sur',
                previous: 'Précédent',
                next: 'Suivant',
                first: 'Premier',
                last: 'Dernier',
                showing: 'Affichage',
                to: 'à',
                
                // Why Choose
                whyChooseSparePlug: 'Pourquoi choisir SparePlug',
                verifiedVendors: 'Vendeurs vérifiés',
                verifiedVendorsDesc: 'Tous les vendeurs sont vérifiés pour garantir des pièces authentiques et des transactions sûres.',
                directCommunication: 'Communication directe',
                directCommunicationDesc: 'Discutez directement avec les vendeurs via WhatsApp, téléphone ou messagerie.',
                fairPricing: 'Prix justes',
                fairPricingDesc: 'Comparez les prix entre plusieurs vendeurs pour obtenir le meilleur tarif en XAF.',
                trustedPartner: 'Votre partenaire de confiance',
                trustedPartnerDesc: "Trouver la bonne pièce ne devrait pas être un combat. Nous vous connectons à un réseau de vendeurs locaux vérifiés pour garantir qualité, fiabilité et prix justes pour chaque recherche.",
                readyToSell: 'Prêt à vendre vos pièces?',
                startSellingNow: 'Commencer maintenant',
                findPerfect: 'Trouvez la pièce parfaite',
                partsForYourRide: 'pour votre véhicule',
                connectDirect: 'Connectez-vous directement avec des vendeurs vérifiés. Obtenez des pièces de qualité livrées ou récupérées instantanément.',
                
                // Footer
                aboutSparePlug: 'Le premier marché camerounais pour les pièces automobiles authentiques. Nous vous connectons à des vendeurs vérifiés pour une qualité garantie.',
                quickLinks: 'Liens rapides',
                support: 'Support',
                allRightsReserved: 'Tous droits réservés.',
                terms: 'Conditions',
                privacy: 'Confidentialité'
            }
        };
        
        // Translation system disabled - always use English
        // localStorage removed on page load
        localStorage.removeItem('spareplug_lang');
        
        let currentLang = 'en';
        
        function translatePage() {
            // Translation disabled - do nothing
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            localStorage.removeItem('spareplug_lang');
        });
    </script>
</body>
</html>
