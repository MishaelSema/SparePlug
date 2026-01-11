# CHAPTER THREE: SYSTEM ANALYSIS AND DESIGN

## 3.1 Research Design / Methodology

### 3.1.1 Research Approach

This project adopts a **pragmatic research approach**, combining:
- **Action Research**: Actively developing a solution to address identified problems
- **Design Science Research**: Creating and evaluating an artifact (the SparePlug platform)
- **Case Study**: Focus on the Cameroonian auto parts market

### 3.1.2 Development Methodology

**Agile Software Development** with sequential implementation:
1. **Planning Phase**: Requirements gathering, architecture design
2. **Design Phase**: Database design, UI/UX mockups
3. **Implementation Phase**: Sequential build by user type (Admin → Vendor → Buyer)
4. **Testing Phase**: Continuous testing during development
5. **Documentation Phase**: Ongoing documentation

### 3.1.3 Data Collection Methods

1. **Secondary Research**: Literature review on marketplaces, e-commerce, and Cameroon's digital economy
2. **Market Observation**: Analysis of existing platforms and local business practices
3. **Requirements Analysis**: Identification of user needs for buyers, vendors, and admins
4. **Expert Consultation**: Discussions with industry professionals
5. **System Testing**: Functional, security, and usability testing

### 3.1.4 System Development Life Cycle (SDLC)

**Waterfall-Modified Approach:**
- Sequential phases with iterations
- Requirements → Design → Implementation → Testing → Documentation
- Feedback loops for refinement
- Focus on incremental feature delivery

## 3.2 Functional Requirements

### 3.2.1 Authentication Module

**FR-AUTH-01**: System shall support user registration with email
**FR-AUTH-02**: System shall support user registration with phone number
**FR-AUTH-03**: System shall support Google OAuth registration
**FR-AUTH-04**: System shall verify email addresses via verification links
**FR-AUTH-05**: System shall verify phone numbers via SMS codes
**FR-AUTH-06**: System shall support password reset via email
**FR-AUTH-07**: System shall support two-factor authentication (2FA)
**FR-AUTH-08**: System shall support login with email or phone number
**FR-AUTH-09**: System shall hash passwords using bcrypt
**FR-AUTH-10**: System shall manage user sessions securely

### 3.2.2 Buyer Module

**FR-BUYER-01**: System shall allow buyers to browse products without registration
**FR-BUYER-02**: System shall provide advanced search with multiple filters
**FR-BUYER-03**: System shall filter products by vehicle make, model, year
**FR-BUYER-04**: System shall filter products by category, price, location, condition
**FR-BUYER-05**: System shall sort results by relevance, price, date, rating
**FR-BUYER-06**: System shall enable price comparison across multiple vendors for the same part
**FR-BUYER-07**: System shall display parts availability status for each vendor
**FR-BUYER-08**: System shall display product details including images, description, price
**FR-BUYER-09**: System shall show vehicle compatibility information
**FR-BUYER-10**: System shall display seller information and verification status
**FR-BUYER-11**: System shall allow buyers to contact sellers via WhatsApp
**FR-BUYER-12**: System shall allow buyers to contact sellers via phone call
**FR-BUYER-13**: System shall allow buyers to contact sellers via email
**FR-BUYER-14**: System shall track inquiries when buyers contact sellers
**FR-BUYER-15**: System shall display product reviews and ratings
**FR-BUYER-16**: System shall allow buyers to write reviews (after purchase)

### 3.2.3 Vendor Module

**FR-VENDOR-01**: System shall allow vendor registration with business information
**FR-VENDOR-02**: System shall collect shop profile details (name, type, address)
**FR-VENDOR-03**: System shall allow vendors to configure operating hours
**FR-VENDOR-04**: System shall support vendor document upload (registration, tax ID)
**FR-VENDOR-05**: System shall allow vendors to add product listings
**FR-VENDOR-06**: System shall enforce product limits based on subscription plan
**FR-VENDOR-07**: System shall allow vendors to edit product information
**FR-VENDOR-08**: System shall allow vendors to delete/deactivate products
**FR-VENDOR-09**: System shall support bulk product actions
**FR-VENDOR-10**: System shall track product views and inquiries
**FR-VENDOR-11**: System shall display inquiry dashboard for vendors
**FR-VENDOR-12**: System shall allow vendors to update inquiry status
**FR-VENDOR-13**: System shall support subscription plan upgrades
**FR-VENDOR-14**: System shall display subscription history
**FR-VENDOR-15**: System shall allow vendors to edit shop profile

### 3.2.4 Admin Module

**FR-ADMIN-01**: System shall provide admin dashboard with platform statistics
**FR-ADMIN-02**: System shall allow admins to view all users
**FR-ADMIN-03**: System shall allow admins to view all products
**FR-ADMIN-04**: System shall allow admins to manage categories
**FR-ADMIN-05**: System shall allow admins to verify vendor accounts
**FR-ADMIN-06**: System shall allow admins to suspend user accounts
**FR-ADMIN-07**: System shall provide analytics and reporting
**FR-ADMIN-08**: System shall track platform metrics (users, products, inquiries)

### 3.2.5 Product Management

**FR-PRODUCT-01**: System shall store product information (title, description, price)
**FR-PRODUCT-02**: System shall support multiple product images (up to 5)
**FR-PRODUCT-03**: System shall store vehicle compatibility (make, model, year)
**FR-PRODUCT-04**: System shall categorize products
**FR-PRODUCT-05**: System shall track product status (active/inactive)
**FR-PRODUCT-06**: System shall track product views and inquiries
**FR-PRODUCT-07**: System shall support featured product promotion

### 3.2.6 Search and Filtering

**FR-SEARCH-01**: System shall provide text-based search
**FR-SEARCH-02**: System shall filter by category
**FR-SEARCH-03**: System shall filter by vehicle compatibility
**FR-SEARCH-04**: System shall filter by price range
**FR-SEARCH-05**: System shall filter by location (city, region)
**FR-SEARCH-06**: System shall filter by condition (new, used, refurbished)
**FR-SEARCH-07**: System shall filter by seller verification status
**FR-SEARCH-08**: System shall sort results by multiple criteria

## 3.3 Non-Functional Requirements

### 3.3.1 Performance Requirements

**NFR-PERF-01**: System shall load pages within 2 seconds under normal load
**NFR-PERF-02**: System shall support concurrent users (minimum 100)
**NFR-PERF-03**: System shall handle database queries efficiently with proper indexing
**NFR-PERF-04**: System shall optimize images for web delivery

### 3.3.2 Security Requirements

**NFR-SEC-01**: System shall encrypt passwords using bcrypt hashing
**NFR-SEC-02**: System shall prevent SQL injection attacks using prepared statements
**NFR-SEC-03**: System shall prevent XSS attacks through output sanitization
**NFR-SEC-04**: System shall implement session management and timeout
**NFR-SEC-05**: System shall enforce role-based access control
**NFR-SEC-06**: System shall validate all user inputs
**NFR-SEC-07**: System shall log security events and failed login attempts

### 3.3.3 Usability Requirements

**NFR-USE-01**: System shall provide responsive design for mobile, tablet, desktop
**NFR-USE-02**: System shall use consistent navigation and UI elements
**NFR-USE-03**: System shall provide clear error messages
**NFR-USE-04**: System shall support intuitive search and filtering
**NFR-USE-05**: System shall provide help documentation and tooltips
**NFR-USE-06**: System shall support multiple languages (English primary, French planned)

### 3.3.4 Reliability Requirements

**NFR-REL-01**: System shall have 99% uptime availability
**NFR-REL-02**: System shall handle errors gracefully without crashing
**NFR-REL-03**: System shall validate data before database operations
**NFR-REL-04**: System shall provide backup and recovery mechanisms

### 3.3.5 Scalability Requirements

**NFR-SCA-01**: System architecture shall support horizontal scaling
**NFR-SCA-02**: Database design shall accommodate growth (10,000+ products)
**NFR-SCA-03**: System shall support increasing user base
**NFR-SCA-04**: System shall optimize for future feature additions

## 3.4 System Architecture

### 3.4.1 Overall Architecture

SparePlug follows a **3-Tier Architecture**:

```
┌─────────────────────────────────────────┐
│         Presentation Layer               │
│   (HTML, CSS, JavaScript, Tailwind)      │
└─────────────────────────────────────────┘
                    ↕
┌─────────────────────────────────────────┐
│         Application Layer                │
│   (PHP, Business Logic, Controllers)     │
└─────────────────────────────────────────┘
                    ↕
┌─────────────────────────────────────────┐
│         Data Layer                       │
│   (MySQL Database, File Storage)         │
└─────────────────────────────────────────┘
```

### 3.4.2 Technology Stack

**Frontend:**
- HTML5 for structure
- Tailwind CSS for styling
- JavaScript for interactivity
- Responsive design (mobile-first)

**Backend:**
- PHP 7.4+ for server-side logic
- Object-oriented programming
- MVC-like structure
- RESTful routing

**Database:**
- MySQL 5.7+ for data storage
- Normalized relational database
- JSON fields for flexible data
- Indexed queries for performance

**Server:**
- Apache web server
- WAMP environment (development)
- Session management
- File upload handling

### 3.4.3 System Architecture Diagram

```
┌──────────────┐
│   Browser    │
│  (Client)    │
└──────┬───────┘
       │ HTTP/HTTPS
       │
┌──────▼─────────────────────────────────────┐
│         Web Server (Apache)                 │
│  ┌──────────────────────────────────────┐  │
│  │  PHP Application Layer                │  │
│  │  ┌──────────┐  ┌──────────┐          │  │
│  │  │ Routing  │  │ Business │          │  │
│  │  │  (MVC)   │→ │  Logic   │          │  │
│  │  └──────────┘  └──────────┘          │  │
│  │       │             │                 │  │
│  └───────┼─────────────┼─────────────────┘  │
│          │             │                     │
└──────────┼─────────────┼─────────────────────┘
           │             │
    ┌──────▼─────────────▼──────┐
    │   Database (MySQL)        │
    │   ┌─────────────────────┐  │
    │   │  Users             │  │
    │   │  Products          │  │
    │   │  Vendors           │  │
    │   │  Categories        │  │
    │   │  Inquiries         │  │
    │   │  Reviews           │  │
    │   │  Subscriptions     │  │
    │   └─────────────────────┘  │
    └─────────────────────────────┘
```

### 3.4.4 Component Architecture

**User Interface Components:**
- Layout template (header, sidebar, footer)
- Forms (registration, login, product add/edit)
- Tables (product listings, user management)
- Modals (inquiry forms, confirmations)
- Cards (product cards, vendor profiles)

**Business Logic Components:**
- User class (authentication, profile management)
- Product class (CRUD operations, search)
- Inquiry class (tracking, status updates)
- Auth class (verification, password reset)
- Category class (category management)

**Data Access Components:**
- Database class (connection, queries)
- Prepared statements (security)
- JSON handling (flexible data)
- Session management

## 3.5 Database Design

### 3.5.1 Entity-Relationship Diagram (ERD)

```
┌─────────────┐         ┌──────────────┐
│    USERS    │────────▶│ VENDOR_      │
│             │1     1  │ PROFILES    │
│ - id (PK)   │         │             │
│ - email     │         │ - id (PK)   │
│ - password  │         │ - user_id   │
│ - phone     │         │ - business_ │
│ - user_type │         │   name      │
│ - is_verified│        │ - is_verified│
└─────────────┘         └──────┬───────┘
       │                      │
       │                      │
       │                      │ 1
       │                      │
       │                      │ *
       │                      │
       │              ┌───────▼───────┐
       │              │   PRODUCTS     │
       │              │               │
       │              │ - id (PK)     │
       │              │ - vendor_id   │
       │              │ - category_id │
       │              │ - title       │
       │              │ - price       │
       │              └───────┬───────┘
       │                      │
       │                      │ 1
       │                      │
       │                      │ *
       │              ┌───────▼───────┐
       │              │  INQUIRIES    │
       │              │               │
       │              │ - id (PK)     │
       │              │ - product_id  │
       │              │ - buyer_id    │
       │              │ - message     │
       │              └───────────────┘
       │
       │ 1
       │
       │ *
┌──────▼──────────┐
│    REVIEWS      │
│                 │
│ - id (PK)       │
│ - product_id    │
│ - buyer_id      │
│ - rating        │
└─────────────────┘
```

### 3.5.2 Database Schema

**Core Tables:**

1. **users** - User accounts (buyers, vendors, admins)
   - Primary Key: id
   - Unique: email
   - Indexes: email, user_type, status

2. **vendor_profiles** - Extended vendor information
   - Primary Key: id
   - Foreign Key: user_id → users(id)
   - JSON fields: operating_hours, verification_documents

3. **products** - Product listings
   - Primary Key: id
   - Foreign Keys: vendor_id → vendor_profiles(id), category_id → categories(id)
   - JSON fields: compatibility, images
   - Indexes: vendor_id, category_id, is_active

4. **categories** - Product categories
   - Primary Key: id
   - Foreign Key: parent_id (self-referencing for subcategories)

5. **vehicle_makes** - Vehicle manufacturers
   - Primary Key: id

6. **vehicle_models** - Vehicle models
   - Primary Key: id
   - Foreign Key: make_id → vehicle_makes(id)

7. **product_inquiries** - Buyer inquiries
   - Primary Key: id
   - Foreign Keys: product_id → products(id), buyer_id → users(id)
   - Indexes: product_id, buyer_id, status

8. **reviews** - Product reviews
   - Primary Key: id
   - Foreign Keys: product_id, buyer_id, vendor_id

9. **subscriptions** - Vendor subscriptions
   - Primary Key: id
   - Foreign Key: vendor_id → vendor_profiles(id)

**Authentication Tables:**

10. **email_verifications** - Email verification tokens
11. **phone_verifications** - SMS verification codes
12. **password_resets** - Password reset tokens
13. **two_factor_codes** - 2FA codes
14. **social_logins** - OAuth login tracking
15. **login_logs** - Security audit logs

### 3.5.3 Normalization

**Normalization Level: 3NF (Third Normal Form)**

- Eliminated redundant data
- Separated concerns (users vs vendor_profiles)
- Proper foreign key relationships
- JSON fields for flexible, non-relational data (compatibility, images)

### 3.5.4 Database Relationships

- **One-to-One**: users ↔ vendor_profiles (one user = one vendor profile)
- **One-to-Many**: vendors → products (one vendor has many products)
- **One-to-Many**: products → inquiries (one product has many inquiries)
- **One-to-Many**: products → reviews (one product has many reviews)
- **Many-to-One**: products → categories (many products in one category)

## 3.6 Design Tools

### 3.6.1 Development Tools

- **PHPStorm / VS Code**: Code editor
- **WAMP**: Local development server
- **phpMyAdmin**: Database management
- **Git**: Version control
- **Tailwind CSS**: Utility-first CSS framework
- **Chrome DevTools**: Browser debugging

### 3.6.2 Design Tools

- **Figma / Adobe XD**: UI/UX mockups (wireframes)
- **Draw.io**: Database ERD diagrams
- **MySQL Workbench**: Database schema visualization
- **Lucidchart**: Architecture diagrams

### 3.6.3 Testing Tools

- **Browser Testing**: Chrome, Firefox, Safari, Edge
- **Responsive Testing**: Device emulators
- **Security Testing**: Manual code review, SQL injection tests
- **Performance Testing**: Apache Bench, browser profiling


