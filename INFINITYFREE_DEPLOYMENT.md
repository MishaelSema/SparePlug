# InfinityFree Deployment Guide - SparePlug

Follow these steps to successfully host your HND project on InfinityFree.

## 1. Database Configuration
InfinityFree uses specific credentials that differ from your local WAMP setup. You need to update your `config/config.php` file once the files are uploaded to the server.

### Recommended `config/config.php` for InfinityFree:
```php
<?php
// Database configuration for InfinityFree
define('DB_HOST', 'sql210.infinityfree.com');
define('DB_NAME', 'if0_40751516_spareplug'); // Replace XXX with your actual database suffix
define('DB_USER', 'if0_40751516');
define('DB_PASS', 'MishaelSema'); // Enter your actual hosting password

// Application configuration
define('APP_NAME', 'SparePlug');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'https://spareplug.42web.io/'); // Replace with your actual live URL

// Keep other settings like Colors, WhatsApp, etc. the same
define('PRIMARY_COLOR', '#DC2626');
define('ACCENT_COLOR', '#F59E0B');
define('WHATSAPP_API_URL', 'https://api.whatsapp.com/send');
define('SUPPORT_PHONE', '+237679080426');

// Security - Change this for production!
define('ENCRYPTION_KEY', 'a-very-secure-random-string');
?>
```

---

## 2. Database Migration Steps
1. **Export Local Database:**
   - Open phpMyAdmin on your local WAMP.
   - Select `spareplug_db`.
   - Click **Export** -> **Go**. This saves a `.sql` file to your computer.
2. **Create Database on InfinityFree:**
   - Log in to your InfinityFree Client Area.
   - Go to **MySQL Databases**.
   - Create a new database (the full name will look like `if0_40751516_spareplug`).
3. **Import to InfinityFree:**
   - Open **phpMyAdmin** from the InfinityFree Control Panel.
   - Select your new `if0_40751516_XXX` database.
   - Click **Import**, select your local `.sql` file, and click **Go**.

---

## 3. Uploading Files
1. Use an FTP client like **FileZilla**.
2. **Host:** `ftpupload.net`
3. **Username:** `if0_40751516`
4. **Password:** Your hosting password.
5. **IMPORTANT:** Upload all your project files into the `htdocs` folder. InfinityFree only serves files located inside `htdocs`.

---

## 4. Key Differences to Remember
- **Case Sensitivity:** Windows (WAMP) is not case-sensitive, but Linux (InfinityFree) is. Ensure your file calls like `require_once 'includes/Functions.php'` match the actual filename lowercase/uppercase exactly.
- **Port 3306:** This is the default port, so you don't need to specify it in the `DB_HOST` usually.
- **Email (SMTP):** InfinityFree often blocks outgoing SMTP (Gmail) on the free plan. You might need to use their native `mail()` function or a specialized service if Gmail SMTP fails.

---

## 5. Troubleshooting
If you see an "Error 500" or "Database connection failed":
- Double-check that your `DB_NAME` is the full name provided in the Control Panel (including the prefix).
- Ensure your `config.php` does NOT have any extra spaces before `<?php`.
