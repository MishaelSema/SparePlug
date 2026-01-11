# Admin Setup Guide

## Overview
The SparePlug platform now supports three user types:
1. **ADMIN** - Platform administrators with full control
2. **VENDOR** - Sellers who list and manage products
3. **BUYERS** - End customers who browse and purchase parts

## Setting Up Admin

### Step 1: Update Database
If you already have a database, run the migration:
```bash
mysql -u root -p spareplug_db < database/migration_add_admin.sql
```

Or if starting fresh, use the updated schema:
```bash
mysql -u root -p < database/spareplug_schema_fixed.sql
```

### Step 2: Admin Credentials
**Default Admin Account:**
- **Email:** admin@spareplug.cm
- **Password:** admin123

**⚠️ IMPORTANT:** Change this password immediately after first login!

### Step 3: Login as Admin
1. Navigate to the login page
2. Enter admin credentials
3. You'll be redirected to the Admin Dashboard

## Admin Features

### 1. Admin Dashboard (`index.php?page=admin-dashboard`)
- Platform overview statistics
- User counts (total, vendors, buyers)
- Product counts
- Recent users and pending vendor verifications
- Quick action links

### 2. Manage Users (`index.php?page=admin-users`)
- View all registered users
- See user type (admin, vendor, buyer)
- Check user status (active, suspended, pending)
- Manage user accounts

### 3. Manage Products (`index.php?page=admin-products`)
- View all product listings
- See vendor information
- Monitor product status (active/inactive)
- View product metrics (views, inquiries)
- Edit or remove products

### 4. Manage Categories (`index.php?page=admin-categories`)
- Organize product categories
- Add/edit/delete categories
- Control category visibility

### 5. Platform Analytics (`index.php?page=admin-analytics`)
- Comprehensive platform statistics
- User growth metrics
- Product performance data
- Recent activity monitoring

## Sequential Building Approach

Following your request, we're building sequentially by user type:

### ✅ **ADMIN** (COMPLETED)
- ✅ Database schema updated to support admin user type
- ✅ Admin authentication and login
- ✅ Admin Dashboard
- ✅ User Management
- ✅ Product Management
- ✅ Category Management
- ✅ Platform Analytics

### 🔄 **VENDOR** (IN PROGRESS)
- ✅ Signup and registration
- ✅ Dashboard
- ✅ Product management
- ✅ Analytics
- ✅ Inquiries
- ✅ Pricing plans
- ⏳ Add more vendor features as needed

### 🔄 **BUYERS** (IN PROGRESS)
- ✅ Homepage
- ✅ Search
- ✅ Product details
- ✅ Contact vendors
- ⏳ Add more buyer features as needed

## Security Notes

1. **Admin Check:** All admin pages check for admin privileges
2. **Session Management:** Secure session handling for all user types
3. **Access Control:** Users can only access features for their role
4. **Password Security:** Admin passwords should be strong and changed regularly

## Next Steps

Now that ADMIN pages are complete, you can:
1. Login as admin to manage the platform
2. Continue building VENDOR features
3. Continue building BUYER features
4. Add more functionality as needed

## Database Migration

If you need to add the admin user type to an existing database:

```sql
-- Update user_type enum
ALTER TABLE users MODIFY COLUMN user_type ENUM('buyer', 'vendor', 'admin') NOT NULL DEFAULT 'buyer';

-- Add default admin (password: admin123)
INSERT INTO users (email, password, first_name, last_name, user_type, is_verified, status) VALUES
('admin@spareplug.cm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'admin', 1, 'active');
```

## Support

For issues or questions:
- Email: support@spareplug.cm
- Developer: Ekom Mishael Sema





