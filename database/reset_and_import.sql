-- =====================================================
-- COMPLETE DATABASE RESET
-- Run ALL these queries first to clear everything
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS email_verifications;
DROP TABLE IF EXISTS login_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS product_inquiries;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS social_logins;
DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS vehicle_makes;
DROP TABLE IF EXISTS vendor_profiles;

SET FOREIGN_KEY_CHECKS = 1;

-- Now import your live if0_40751516_spareplug.sql file
-- Go to phpMyAdmin Import tab and select the file
