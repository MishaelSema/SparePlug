# sparePlug Deployment Guide - InfinityFree

## ✅ Pre-Deployment Checklist

### 1. Database Setup
- [x] Database credentials updated in `config/config.php`
- [ ] Import database schema to InfinityFree MySQL
- [ ] Run `fix_schema.php` on live server to add missing columns

### 2. File Upload
Upload these files/folders to `htdocs` on InfinityFree:
```
✅ Required Files:
- index.php
- logout.php
- /api/
- /assets/
- /config/
- /database/
- /includes/
- /pages/
- /uploads/ (create empty directory)
- .htaccess

⚠️ DO NOT Upload:
- fix_schema.php (upload temporarily, delete after use)
- inspect_schema.php (delete)
- inspect_schema_2.php (delete)
- migrate.php (delete)
- /vendor/ (if exists)
- .git/
- README.md
- DEPLOYMENT_GUIDE.md
```

### 3. Directory Permissions
Set these permissions via File Manager:
- `uploads/` → 755
- `uploads/products/` → 755
- `uploads/categories/` → 755
- `uploads/vendors/` → 755

### 4. Database Import Steps

**Option A - Via phpMyAdmin:**
1. Login to InfinityFree control panel
2. Go to phpMyAdmin
3. Select your database: `if0_40751516_spareplug`
4. Click "Import"
5. Upload `database/spareplug_schema_fixed.sql`
6. Click "Go"

**Option B - Via SQL:**
Copy and paste SQL from `database/spareplug_schema_fixed.sql` into SQL tab

### 5. Post-Upload Configuration

1. **Run Schema Fix:**
   - Upload `fix_schema.php` to root
   - Visit: `http://spareplug.42web.io/fix_schema.php`
   - Delete the file after successful run

2. **Test Critical Features:**
   - [ ] Homepage loads
   - [ ] Product search works
   - [ ] Vendor signup works
   - [ ] Login works
   - [ ] WhatsApp buttons work
   - [ ] Image uploads work

3. **Security Cleanup:**
   ```bash
   Delete these files from server:
   - fix_schema.php
   - inspect_schema.php
   - inspect_schema_2.php
   - migrate.php
   ```

## 🔧 InfinityFree Specific Notes

### File Upload Methods:
1. **FTP Client (Recommended):**
   - Host: `ftpupload.net`
   - Username: `if0_40751516`
   - Port: 21
   - Use FileZilla or WinSCP

2. **Online File Manager:**
   - Login to control panel
   - Use built-in file manager
   - Upload via ZIP (faster for many files)

### Common Issues:

**Issue: "Database connection failed"**
- Solution: Double-check credentials in `config/config.php`
- Ensure database exists in control panel

**Issue: "500 Internal Server Error"**
- Solution: Check `.htaccess` syntax
- Check file permissions
- Enable error display temporarily

**Issue: Images not uploading**
- Solution: Check `uploads/` directory permissions (755)
- Ensure directory exists

**Issue: WhatsApp links not working**
- Solution: Verify phone numbers in vendor profiles include country code (+237)

## 📱 Testing Checklist

After deployment, test:
- [ ] Homepage displays correctly
- [ ] Search functionality
- [ ] Product detail pages
- [ ] Vendor registration
- [ ] Login/Logout
- [ ] WhatsApp integration
- [ ] Image uploads
- [ ] Category browsing
- [ ] Vendor dashboard
- [ ] Admin panel (if applicable)

## 🚀 Going Live

1. Update `BASE_URL` if using custom domain
2. Enable SSL certificate (free in InfinityFree)
3. Update WhatsApp support number
4. Test all payment integrations (if any)
5. Monitor error logs regularly

## 📞 Support

- InfinityFree Forum: https://forum.infinityfree.net
- Documentation: https://infinityfree.net/support

## 🔐 Security Reminders

- Never commit `config/config.php` with real credentials to Git
- Regularly backup your database
- Keep PHP files updated
- Monitor for suspicious activity
- Use strong passwords for admin accounts
