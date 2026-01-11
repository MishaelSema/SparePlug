# APPENDICES

## APPENDIX A: QUESTIONNAIRES

### A.1 Vendor Survey Questionnaire

**Purpose:** To understand vendor needs and challenges in the auto spare parts market

**Instructions:** Please answer all questions based on your experience as an auto parts vendor.

---

**SECTION 1: DEMOGRAPHIC INFORMATION**

1. Business Name: _______________________

2. Business Type:
   - [ ] Individual Seller
   - [ ] Company
   - [ ] Garage/Workshop
   - [ ] Auto Dealer

3. Location:
   - City: _______________________
   - Region: _______________________

4. Years in Business: _______________________

5. Number of Products Stocked: _______________________

---

**SECTION 2: CURRENT BUSINESS PRACTICES**

6. How do customers currently find your products?
   - [ ] Physical store visits
   - [ ] Word of mouth
   - [ ] Social media (Facebook, WhatsApp)
   - [ ] Other: _______________________

7. What challenges do you face in reaching customers?
   - [ ] Limited visibility
   - [ ] High marketing costs
   - [ ] Competition
   - [ ] Limited reach
   - [ ] Other: _______________________

8. How do customers contact you?
   - [ ] Phone call
   - [ ] WhatsApp
   - [ ] Email
   - [ ] In-person visit
   - [ ] Social media

9. Do you have an online presence?
   - [ ] Yes - Website
   - [ ] Yes - Social media only
   - [ ] No

---

**SECTION 3: PLATFORM INTEREST**

10. Would you use an online marketplace for auto parts?
   - [ ] Yes
   - [ ] No
   - [ ] Maybe

11. What features are most important to you?
   - [ ] Easy product listing
   - [ ] Customer inquiries
   - [ ] Analytics/reports
   - [ ] Featured listings
   - [ ] Verification badge

12. What monthly subscription amount would you pay?
   - [ ] Free only
   - [ ] 5,000 - 15,000 XAF
   - [ ] 15,000 - 35,000 XAF
   - [ ] 35,000+ XAF

13. How many products would you list?
   - [ ] Less than 10
   - [ ] 10 - 50
   - [ ] 50 - 100
   - [ ] 100+

---

**SECTION 4: CONCERNS AND REQUIREMENTS**

14. What concerns do you have about online selling?
   - [ ] Trust/security
   - [ ] Technical skills
   - [ ] Payment security
   - [ ] Customer communication
   - [ ] Competition

15. What support would you need?
   - [ ] Training on platform use
   - [ ] Help with product listing
   - [ ] Marketing support
   - [ ] Technical support

---

### A.2 Buyer Survey Questionnaire

**Purpose:** To understand buyer needs and preferences when searching for auto parts

**Instructions:** Please answer based on your experience purchasing auto parts.

---

**SECTION 1: DEMOGRAPHIC INFORMATION**

1. Age Range:
   - [ ] 18-25
   - [ ] 26-35
   - [ ] 36-45
   - [ ] 46+

2. Vehicle Ownership:
   - [ ] Yes - Own vehicle(s)
   - [ ] No - Use garage services
   - [ ] No - Not a vehicle owner

3. Location:
   - City: _______________________
   - Region: _______________________

---

**SECTION 2: PURCHASING HABITS**

4. How often do you purchase auto parts?
   - [ ] Weekly
   - [ ] Monthly
   - [ ] Quarterly
   - [ ] Annually
   - [ ] As needed

5. How do you currently find auto parts?
   - [ ] Physical store search
   - [ ] Ask garage mechanics
   - [ ] Social media (Facebook, WhatsApp)
   - [ ] Online search
   - [ ] Word of mouth

6. What information is most important when choosing a vendor?
   - [ ] Price
   - [ ] Location/proximity
   - [ ] Product quality
   - [ ] Vendor reputation
   - [ ] Product availability

7. How do you prefer to contact sellers?
   - [ ] WhatsApp
   - [ ] Phone call
   - [ ] Email
   - [ ] In-person visit
   - [ ] Social media message

---

**SECTION 3: ONLINE PLATFORM PREFERENCES**

8. Would you use an online marketplace for auto parts?
   - [ ] Yes
   - [ ] No
   - [ ] Maybe

9. What features are most important?
   - [ ] Search by vehicle compatibility
   - [ ] Price comparison
   - [ ] Vendor reviews
   - [ ] Verified vendors
   - [ ] Product images

10. What device would you use?
    - [ ] Smartphone
    - [ ] Tablet
    - [ ] Computer
    - [ ] All of the above

11. Do you prefer online payment or direct payment?
    - [ ] Online payment (credit card, mobile money)
    - [ ] Direct payment (cash, bank transfer)
    - [ ] Either option

---

**SECTION 4: CONCERNS**

12. What concerns do you have about online platforms?
    - [ ] Product quality/fraud
    - [ ] Vendor credibility
    - [ ] Payment security
    - [ ] Delivery/shipping
    - [ ] Technical difficulties

---

## APPENDIX B: SYSTEM SCREENSHOTS

### B.1 Authentication Screens

**Screenshot B.1.1:** Buyer Registration Page
- Location: `pages/auth/register-buyer.php`
- Description: Shows email/phone/Google registration options

**Screenshot B.1.2:** Login Page
- Location: `pages/auth/login.php`
- Description: Email or phone login with social login options

**Screenshot B.1.3:** Forgot Password Page
- Location: `pages/auth/forgot-password.php`
- Description: Password reset request form

**Screenshot B.1.4:** Email Verification Page
- Location: `pages/auth/verify-email.php`
- Description: Success/failure message after verification

### B.2 Buyer Interface Screens

**Screenshot B.2.1:** Homepage
- Location: `pages/buyer/home.php`
- Description: Hero section, featured products, categories

**Screenshot B.2.2:** Search Results Page
- Location: `pages/buyer/search.php`
- Description: Filtered product grid with sidebar filters

**Screenshot B.2.3:** Product Detail Page
- Location: `pages/buyer/product-detail.php`
- Description: Full product information with contact buttons

### B.3 Vendor Interface Screens

**Screenshot B.3.1:** Vendor Dashboard
- Location: `pages/vendor/dashboard.php`
- Description: Statistics, recent activity, quick actions

**Screenshot B.3.2:** Add Product Page
- Location: `pages/vendor/add-product.php`
- Description: Product form with subscription limit display

**Screenshot B.3.3:** Products Management
- Location: `pages/vendor/products.php`
- Description: Product table with bulk actions

**Screenshot B.3.4:** Inquiry Management
- Location: `pages/vendor/inquiries.php`
- Description: Inquiry list with contact buttons

**Screenshot B.3.5:** Subscription Management
- Location: `pages/vendor/subscription.php`
- Description: Plan comparison and upgrade options

**Screenshot B.3.6:** Edit Profile Page
- Location: `pages/vendor/edit-profile.php`
- Description: Shop profile editing with verification status

### B.4 Admin Interface Screens

**Screenshot B.4.1:** Admin Dashboard
- Location: `pages/admin/dashboard.php`
- Description: Platform statistics and metrics

**Screenshot B.4.2:** User Management
- Location: `pages/admin/users.php`
- Description: Users table with verification buttons

**Screenshot B.4.3:** Product Management (Admin)
- Location: `pages/admin/products.php`
- Description: All platform products view

---

## APPENDIX C: CODE SNIPPETS

### C.1 Database Connection

```php
// config/database.php
class Database {
    private $connection;
    
    public function __construct() {
        $this->connection = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }
}
```

### C.2 User Authentication

```php
// includes/functions.php
public function login($email, $password) {
    $user = $this->db->fetch(
        "SELECT * FROM users WHERE email = ? AND status = 'active'",
        [$email]
    );
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        return $user;
    }
    return false;
}
```

### C.3 Product Search with Filters

```php
// includes/functions.php - Product class
public function getAll($filters = []) {
    $sql = "SELECT p.*, c.name as category_name, vp.business_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN vendor_profiles vp ON p.vendor_id = vp.id
            WHERE p.is_active = 1";
    
    // Add filters dynamically
    if (!empty($filters['search'])) {
        $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    }
    
    // Execute with prepared statements
    return $this->db->fetchAll($sql, $params);
}
```

### C.4 Subscription Limit Check

```php
// pages/vendor/add-product.php
$subscriptionPlan = $vendorProfile['subscription_plan'] ?? 'free';
$maxProducts = [
    'free' => 10,
    'basic' => 50,
    'premium' => 999999
];
$productLimit = $maxProducts[$subscriptionPlan] ?? 10;
$canAddProduct = $currentProductCount < $productLimit;
```

### C.5 WhatsApp Integration

```javascript
// pages/buyer/product-detail.php
function openWhatsApp(phone, message) {
    const cleanPhone = phone.replace(/\D/g, '');
    const encodedMessage = encodeURIComponent(message);
    window.open(`https://wa.me/${cleanPhone}?text=${encodedMessage}`, '_blank');
}
```

---

## APPENDIX D: DATABASE SCHEMA

### D.1 Complete SQL Schema

```sql
-- See: database/spareplug_schema_fixed.sql
-- See: database/auth_schema_updates.sql

-- Key Tables:
-- users (user accounts)
-- vendor_profiles (vendor business info)
-- products (product listings)
-- categories (product categories)
-- vehicle_makes (car manufacturers)
-- vehicle_models (car models)
-- product_inquiries (buyer inquiries)
-- reviews (product reviews)
-- subscriptions (vendor subscriptions)
-- email_verifications
-- phone_verifications
-- password_resets
-- two_factor_codes
-- social_logins
-- login_logs
```

### D.2 Entity Relationship Diagram

```
[ERD Diagram - See Chapter 3, Section 3.5.1]
```

---

## APPENDIX E: GANTT CHART

### E.1 Project Timeline

```
MONTH          | ACTIVITY
---------------|----------------------------------------
Months 1-2     | Research & Literature Review
               | Requirements Analysis
               | Database Design
               | System Architecture Design
---------------|----------------------------------------
Months 3-4     | Authentication System Development
               | Admin Module Development
               | Database Implementation
---------------|----------------------------------------
Months 5-6     | Vendor Module Development
               | Product Management
               | Shop Profile Setup
---------------|----------------------------------------
Months 7-8     | Buyer Module Development
               | Search & Filtering
               | Product Detail Pages
---------------|----------------------------------------
Month 9        | Inquiry Management
               | Subscription Management
               | Integration Testing
---------------|----------------------------------------
Month 10       | Security Testing
               | Usability Testing
               | Bug Fixes
---------------|----------------------------------------
Months 11-12   | Documentation
               | Report Writing
               | Final Presentation Preparation
```

### E.2 Detailed Timeline

**Phase 1: Planning & Design (Months 1-2)**
- Week 1-2: Problem identification, literature review
- Week 3-4: Requirements gathering, market research
- Week 5-6: Database design, ERD creation
- Week 7-8: System architecture, UI/UX mockups

**Phase 2: Core Development (Months 3-6)**
- Weeks 9-12: Authentication system
- Weeks 13-16: Admin panel
- Weeks 17-20: Vendor registration & profile
- Weeks 21-24: Product management

**Phase 3: Feature Development (Months 7-9)**
- Weeks 25-28: Buyer search & browse
- Weeks 29-32: Inquiry management
- Weeks 33-36: Subscription system

**Phase 4: Testing & Refinement (Months 10-11)**
- Weeks 37-40: Functional testing
- Weeks 41-44: Security testing, bug fixes
- Weeks 45-46: User acceptance testing

**Phase 5: Documentation (Month 12)**
- Weeks 47-48: Report writing
- Weeks 49-50: Final review
- Weeks 51-52: Presentation preparation

---

## APPENDIX F: TESTING RESULTS

### F.1 Test Case Execution Summary

| Test ID | Module | Test Case | Status | Notes |
|---------|--------|-----------|--------|-------|
| TC-001 | Auth | User Registration | ✅ PASS | All methods working |
| TC-002 | Auth | Login | ✅ PASS | Email and phone login |
| TC-003 | Auth | Password Reset | ✅ PASS | Email link generated |
| TC-004 | Vendor | Add Product | ✅ PASS | Limits enforced |
| TC-005 | Vendor | Edit Product | ✅ PASS | Updates correctly |
| TC-006 | Vendor | Subscription Upgrade | ✅ PASS | Plan changed |
| TC-007 | Buyer | Search | ✅ PASS | Filters working |
| TC-008 | Buyer | Contact Seller | ✅ PASS | WhatsApp/Phone/Email |
| TC-009 | Admin | Verify Vendor | ✅ PASS | Status updated |
| TC-010 | Security | SQL Injection | ✅ PASS | Protected |
| TC-011 | Security | XSS | ✅ PASS | Sanitized output |
| TC-012 | Performance | Page Load | ✅ PASS | < 2 seconds |
| TC-013 | Usability | Mobile View | ✅ PASS | Responsive |
| TC-014 | Usability | Navigation | ✅ PASS | Intuitive |
| TC-015 | Integration | End-to-End | ✅ PASS | All flows working |

---

## APPENDIX G: SYSTEM REQUIREMENTS

### G.1 Minimum Requirements

**Server:**
- PHP 7.4+
- MySQL 5.7+
- Apache 2.4+
- 1GB RAM
- 10GB storage

**Client:**
- Modern browser (Chrome, Firefox, Safari, Edge)
- JavaScript enabled
- Internet connection

### G.2 Recommended Requirements

**Server:**
- PHP 8.0+
- MySQL 8.0+
- 2GB RAM
- 50GB storage
- SSL certificate

**Client:**
- Mobile device (Android/iOS)
- 4G/WiFi connection
- Latest browser version

---

## APPENDIX H: INSTALLATION GUIDE

### H.1 Step-by-Step Installation

[See: README.md for detailed installation instructions]

1. Install WAMP Server
2. Clone/download project files
3. Import database schema
4. Configure database connection
5. Set file permissions
6. Access application

---

## APPENDIX I: USER MANUALS

### I.1 Vendor User Manual

[Guide for vendors on how to use the platform]

### I.2 Buyer User Manual

[Guide for buyers on how to search and contact vendors]

### I.3 Admin User Manual

[Guide for admins on platform management]

---

**End of Appendices**




