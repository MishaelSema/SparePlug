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
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>sparePlug - Premium Auto Parts Marketplace</title>
    
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
                        <a href="index.php" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors duration-200 py-2 border-b-2 border-transparent hover:border-accent">Home</a>
                        <a href="index.php?page=search" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors duration-200 py-2 border-b-2 border-transparent hover:border-accent">Find Parts</a>
                        <a href="index.php?page=categories" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors duration-200 py-2 border-b-2 border-transparent hover:border-accent">Categories</a>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
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
                                        <a href="index.php?page=vendor-dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Vendor Dashboard</a>
                                        <a href="index.php?page=vendor-products" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">My Products</a>
                                    <?php elseif ($_SESSION['user_type'] === 'admin'): ?>
                                        <a href="index.php?page=admin-dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">Admin Dashboard</a>
                                    <?php else: ?>
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
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50">Home</a>
                <a href="index.php?page=search" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50">Find Parts</a>
                <a href="index.php?page=categories" class="block px-3 py-2 rounded-md text-base font-medium text-gray-900 hover:bg-gray-50">Categories</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="index.php?page=login" class="block text-center w-full btn-secondary mb-3">Log In</a>
                        <a href="index.php?page=vendor-signup" class="block text-center w-full btn-primary">Become a Vendor</a>
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
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">
                            Cameroon's premier marketplace for authentic auto spare parts. We connect you with verified vendors for quality you can trust.
                        </p>
                    </div>

                    <!-- Links Column 1 -->
                    <div>
                        <h3 class="text-white font-heading font-semibold mb-6">Marketplace</h3>
                        <ul class="space-y-4 text-sm text-gray-400">
                            <li><a href="index.php?page=search" class="hover:text-white transition-colors">Search Parts</a></li>
                            <li><a href="index.php?page=categories" class="hover:text-white transition-colors">Browse Categories</a></li>
                        </ul>
                    </div>

                    <!-- Links Column 2 -->
                    <div>
                        <h3 class="text-white font-heading font-semibold mb-6">Support</h3>
                        <ul class="space-y-4 text-sm text-gray-400">
                            <li><a href="index.php?page=about" class="hover:text-white transition-colors">About Us</a></li>
                            <li><a href="index.php?page=contact" class="hover:text-white transition-colors">Contact Us</a></li>
                            <li><a href="index.php?page=faq" class="hover:text-white transition-colors">FAQ</a></li>
                            <li><a href="index.php?page=safety-tips" class="hover:text-white transition-colors">Safety Tips</a></li>
                            <li><a href="index.php?page=terms" class="hover:text-white transition-colors">Terms of Service</a></li>
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
</body>
</html>
