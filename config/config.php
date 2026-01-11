<?php
// Database configuration for WAMP
define('DB_HOST', 'localhost');
define('DB_NAME', 'spareplug_db');
define('DB_USER', 'root');
define('DB_PASS', '1Mish@elSem@');

// Application configuration
define('APP_NAME', 'SparePlug');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/HND_DEFENSE_PROJECT');

// Design system colors
define('PRIMARY_COLOR', '#DC2626');
define('ACCENT_COLOR', '#F59E0B');
define('SUCCESS_COLOR', '#10B981');
define('BORDER_RADIUS', '8px');

// Business configuration
define('FREE_LISTINGS_LIMIT', 10);
define('CURRENCY', 'XAF');
define('CURRENCY_SYMBOL', 'FCFA');

// File upload settings
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// WhatsApp integration
define('WHATSAPP_API_URL', 'https://api.whatsapp.com/send');
define('SUPPORT_PHONE', '+237679080426');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'mishusema237@gmail.com');
define('SMTP_PASS', 'zdgo fefk hnny fzwr');

// Security
define('ENCRYPTION_KEY', 'your-secret-key-here');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Set Timezone to Africa/Douala (UTC+1) to match MySQL/System time
date_default_timezone_set('Africa/Douala');
?>

