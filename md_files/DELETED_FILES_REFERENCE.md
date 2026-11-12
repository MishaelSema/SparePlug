# Deleted Files Reference - SparePlug HND Project

> **IMPORTANT**: These files were one-time fixes/scripts that have been incorporated into `database/complete_schema.sql`. You no longer need them - just import that SQL file on a new PC.

---

## PHP Scripts (Development/Debug Tools)

| File | What it did | Now handled by |
|------|-------------|----------------|
| `add_column.php` | Added `last_password_change` column to users table | complete_schema.sql |
| `check_time.php` | Diagnosed timezone sync issues between PHP and MySQL | Not needed |
| `debug_reset_tokens.php` | Debug password reset token expiration issues | Not needed |
| `cleanup_social_login.php` | Removed social_login table and google_id column | complete_schema.sql |
| `diagnostic.php` | Full system diagnostics (DB connection, admin user, sessions, login test) | Not needed |
| `reset_admin.php` | Created or reset admin user (email: admin@spareplug.cm, password: admin123) | complete_schema.sql |
| `fix_schema.php` | Added auth columns (email_verification_token, phone_verified, 2FA, etc.) to users table | complete_schema.sql |
| `fix_auth_schema.php` | Same as above - duplicate script | complete_schema.sql |
| `migrate.php` | Added logo_url, cover_url to vendor_profiles and created notifications table | complete_schema.sql |
| `upgrade_schema.php` | Added image_url to categories, created subscription_plans table with seed data | complete_schema.sql |
| `inspect_schema.php` | Viewed all database tables and their columns | Not needed |
| `inspect_schema_2.php` | Viewed specific tables (categories, subscriptions) | Not needed |
| `dbg_errors.php` | Debug include/require errors in config files | Not needed |
| `seed_featured.php` | Marked 2 products as featured | Not needed |
| `debug_featured.php` | Debugged is_featured column in products table | Not needed |
| `add_sample_data.php` | Added sample vendor, products, and reviews | complete_schema.sql has basic sample data |

---

## SQL Files (Consolidated/Deleted)

| File | What it had | Now handled by |
|------|-------------|----------------|
| `spareplug_schema.sql` | Original schema (incomplete, missing auth tables) | complete_schema.sql |
| `spareplug_schema_fixed.sql` | Partial fix (still incomplete) | complete_schema.sql |
| `fix_auth_tables.sql` | Auth tables (login_logs, social_logins, 2FA, etc.) | complete_schema.sql |
| `auth_schema_updates.sql` | Auth column additions to users table | complete_schema.sql |
| `migration_add_admin.sql` | Added admin user type and default admin | complete_schema.sql |
| `cleanup_database.sql` | Cleanup operations | Not needed |

---

## Duplicate Files

| File | Why it existed | Replacement |
|------|----------------|-------------|
| `pages/login.php` | Duplicate of `pages/auth/login.php` | Use `pages/auth/login.php` |

---

## How to Restore on New PC

If something goes wrong and you need to re-run any of these fixes:

1. **Import the complete schema**:
   ```sql
   mysql -u root -p spareplug_db < database/complete_schema.sql
   ```

2. **Admin credentials** (pre-created in schema):
   - Email: `admin@spareplug.cm`
   - Password: `admin123`

3. **If you need to manually run specific fixes**:

   - **Add auth columns to existing database**:
     ```sql
     ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL;
     ALTER TABLE users ADD COLUMN phone_verified BOOLEAN DEFAULT FALSE;
     ALTER TABLE users ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE;
     ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(255) NULL;
     ALTER TABLE users ADD COLUMN last_password_change TIMESTAMP NULL;
     ```

   - **Create admin user manually**:
     ```sql
     INSERT INTO users (email, password, first_name, last_name, user_type, is_verified, status)
     VALUES ('admin@spareplug.cm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 1, 'active');
     ```
     (Password hash is for 'admin123')

   - **Add subscription plans**:
     ```sql
     INSERT INTO subscription_plans (name, price, features) VALUES
     ('Free', 0, JSON_ARRAY('10 listings', 'Basic support')),
     ('Basic', 15000, JSON_ARRAY('50 listings', 'Featured', 'Verified badge')),
     ('Premium', 35000, JSON_ARRAY('Unlimited', 'Top search', 'Analytics', '24/7 support'));
     ```

---

## Files Kept

| Category | Files |
|----------|-------|
| **Main Application** | `index.php`, `config/`, `includes/`, `pages/`, `api/` |
| **Database** | `database/complete_schema.sql`, `database/infinityfree_schema.sql` |
| **Documentation** | All files in `md_files/` folder |
| **Assets** | `assets/`, `uploads/` |

---

**Summary**: You deleted 22 dev/debug files that were one-time fixes. The `complete_schema.sql` now contains everything needed to set up the database from scratch on any new PC.

*Generated: March 2026*