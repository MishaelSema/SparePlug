# CHAPTER THREE: METHODOLOGY AND MATERIAL USED

## 3.1 Introduction

This chapter explains how I built the SparePlug system. I describe the methods I used to collect requirements, the tools I used to develop the system, and the steps I followed to design and implement it. The chapter also shows the diagrams and models I created to plan the system before writing the code.

## 3.2 Architecture Description

The SparePlug system was built using a client-server architecture. This architecture separates the system into two main parts that work together.

The **client side** is what users see when they access the system. It includes the login page, the search interface, the product listings, and the vendor dashboard. The client side was built using HTML, CSS, and JavaScript. I used Tailwind CSS to make the design responsive so it works well on both phones and computers.

The **server side** is where all the data is stored and processed. It handles user requests, manages the database, and performs calculations. The server side was built using PHP, which is a popular programming language for web development. The database was built using MySQL, which stores all the information about products, vendors, and users.

The communication between the client and server happens through HTTP requests. When a user searches for a product, the phone sends a request to the server. The server then searches the database and sends back the results. This happens very quickly, usually in less than a second.

I designed the architecture to be modular, which means each part of the system can be updated or changed without affecting the other parts. This makes the system easier to maintain and expand in the future.

## 3.3 Data Collection and User Requirements

### 3.3.1 Data Collection Methods

To understand what the SparePlug system should do, I collected information from different sources. I used several methods:

**Observation** - I visited John Automobile in Mvog-Ada and observed how they worked. I watched how customers asked for parts, how the staff searched for items in the warehouse, and how they recorded sales. This helped me understand the real problems they faced every day.

**Interviews** - I talked directly with the shop owner and customers. I asked questions like "What problems do you face when looking for car parts?" and "What features would you want in a digital system?" These conversations gave me valuable insights into what the system really needed.

**Document Review** - I looked at the paper records that John Automobile used. This helped me understand what information they tracked and how they organized their inventory.

### 3.3.2 User Requirements

Based on my research, I identified the main requirements for the SparePlug system:

**For Buyers:**
- Search for parts by car make, model, and year
- Compare prices from different vendors
- View product pictures and descriptions
- Contact vendors directly through phone or WhatsApp
- Read reviews from other buyers

**For Vendors:**
- Create an account and manage their profile
- Add, edit, and delete product listings
- Upload product pictures
- Set and update prices
- View analytics about their products

**For Administrators:**
- Manage all users (buyers and vendors)
- Monitor the system for problems
- Generate reports about system usage

## 3.4 Functional Requirements

Functional requirements describe what the system must do. These are the main functions that SparePlug provides:

1. **User Registration and Login** - The system must allow new users to create accounts and existing users to log in securely.

2. **Product Listing Management** - Vendors must be able to add new products with details like name, price, description, condition, and compatibility.

3. **Product Search** - Buyers must be able to search for products using filters like car brand, car model, year, category, and price range.

4. **Price Comparison** - The system must show prices from different vendors for the same product so buyers can compare.

5. **Vehicle Compatibility Matching** - The system must match products to specific car makes, models, and years so buyers find parts that fit their cars.

6. **Vendor Profile Management** - Vendors must be able to update their business information, logo, and contact details.

7. **Product Inquiries** - Buyers must be able to send inquiries to vendors about specific products.

8. **Review and Rating** - Buyers must be able to leave reviews and ratings for vendors and products.

## 3.5 Functional Specifications

### 3.5.1 Roles and Actors

The SparePlug system has three main actors:

**Buyer** - A person who wants to find and buy car parts. Buyers can search for products, compare prices, and contact sellers.

**Vendor** - A person or business that sells car parts. Vendors can create listings, manage their products, and respond to customer inquiries.

**Administrator** - A person who manages the entire system. Administrators can manage users, monitor content, and generate reports.

### 3.5.2 System Functionalities

Each actor has specific actions they can perform:

**Buyer Actions:**
- Register a new account
- Log in to the system
- Search for car parts
- Filter search results
- View product details
- Compare prices
- Add products to favorites
- Contact vendor
- Leave reviews
- View order history

**Vendor Actions:**
- Register as a vendor
- Log in to vendor dashboard
- Create product listings
- Edit product information
- Delete products
- Upload product images
- Set product prices
- View product analytics
- Respond to customer inquiries
- Update business profile

**Administrator Actions:**
- Log in to admin panel
- View all users
- Suspend or delete users
- View all products
- Delete inappropriate content
- Generate system reports
- Manage categories
- Manage vehicle makes and models

## 3.6 Technical Specifications

The technical specifications describe the tools and technologies I used to build the system:

**Programming Languages:**
- PHP (Hypertext Preprocessor) for server-side programming
- HTML (HyperText Markup Language) for web page structure
- CSS (Cascading Style Sheets) for styling
- JavaScript for interactive features

**Frameworks and Libraries:**
- Tailwind CSS for responsive design
- Vanilla PHP (no framework) for backend logic

**Database:**
- MySQL for storing all data
- phpMyAdmin for database management

**Development Tools:**
- Visual Studio Code for writing code
- Git for version control
- XAMPP for local development environment

**Web Server:**
- Apache (through XAMPP)
- PHP 8.0 or higher

**Hardware:**
- Laptop computer with 8GB RAM
- Internet connection for testing

## 3.7 Non-Functional Requirements

Non-functional requirements describe the overall qualities of the system:

**Performance** - The system must respond quickly to user requests. Page loading time should be less than 3 seconds on normal internet connections.

**Security** - User passwords must be encrypted. The system must prevent unauthorized access to user accounts and vendor dashboards.

**Usability** - The system must be easy to use, even for people who are not good with computers. The interface must be simple and clear.

**Reliability** - The system must work correctly and not crash often. Data must be stored safely.

**Compatibility** - The system must work on different web browsers and on mobile phones.

## 3.8 Research Design

My research design combined both qualitative and quantitative approaches:

**Qualitative Approach** - I used interviews and observation to understand the real problems that spare parts shops face. This gave me deep insights that numbers alone cannot provide.

**Quantitative Approach** - I used surveys to collect data from a larger group of people. This helped me understand the general needs and preferences of potential users.

The research design also followed the **System Development Life Cycle (SDLC)** model, which I mentioned in Chapter Two. I moved through the phases of planning, analysis, design, development, testing, and deployment.

## 3.9 Analysis Methods

I used several analysis methods to understand the requirements and design the system:

**Requirement Analysis** - I analyzed all the information I collected from interviews and observations. I identified the most important features and prioritized them.

**Data Analysis** - I designed the database structure based on the information that needed to be stored. I created tables for users, products, categories, and other data.

**Process Analysis** - I analyzed how data flows through the system. I identified what happens when a user searches for a product or when a vendor adds a new listing.

## 3.10 Object-Oriented Methods

I used the **Unified Modeling Language (UML)** method for object-oriented analysis and design. UML is a standard way to visualize and document software systems.

UML helped me in several ways:
- It allowed me to show the system from different perspectives
- It provided standard symbols that other developers can understand
- It made it easier to plan the system before writing code

I used two main types of UML diagrams:
- **Use Case Diagrams** - Show how users interact with the system
- **Activity Diagrams** - Show the flow of operations in the system

## 3.11 Functional Methods

I also considered other design methods like:

**MERISE** - A French method for designing information systems. It separates data modeling from process modeling.

**SADT** - Structured Analysis and Design Technique, which uses diagrams to show system processes.

For this project, I chose **UML** because it is widely used and well-documented. It also has good tool support for creating professional diagrams.

## 3.12 Choice of Method

I chose the **UML method** for several reasons:

1. It is an international standard for software design
2. It has clear and simple diagrams that are easy to understand
3. It is commonly taught in software engineering programs
4. It works well for web-based applications like SparePlug

I also combined UML with the **Agile development approach**. This means I developed the system in small steps, testing each part before moving to the next. This helped me catch problems early and adjust the design when needed.

## 3.13 Application of UML Method

### 3.13.1 Actors

Based on my analysis, I identified the following actors for the SparePlug system:

**Primary Actors:**
- Buyer - looks for and purchases car parts
- Vendor - sells car parts and manages listings
- Administrator - manages the entire platform

**Supporting Actors:**
- System - performs automated tasks like sending notifications
- Database - stores and retrieves information

### 3.13.2 Use Case Diagrams

I created use case diagrams to show how each actor interacts with the system. The main use cases include:

**[FIGURE NEEDED: Figure 3.1 - Use Case Diagram for Buyers]**

For **Buyers**, the main use cases are:
- Search Products
- View Product Details
- Compare Prices
- Contact Vendor
- Create Account
- Log In
- Write Review
- Save Favorite

For **Vendors**, the main use cases are:
- Manage Profile
- Add Product
- Edit Product
- Delete Product
- View Analytics
- Respond to Inquiries

For **Administrators**, the main use cases are:
- Manage Users
- Manage Products
- View Reports
- System Settings

**[FIGURE NEEDED: Figure 3.2 - Use Case Diagram for Vendors]**

**[FIGURE NEEDED: Figure 3.3 - Use Case Diagram for Administrators]**

### 3.13.3 Activity Diagrams

I also created activity diagrams to show the flow of operations:

**[FIGURE NEEDED: Figure 3.4 - Activity Diagram for Product Search]**

This diagram shows what happens when a buyer searches for a car part:
1. Buyer enters search terms
2. System receives the request
3. System searches the database
4. Results are displayed to the buyer
5. Buyer can filter or refine the search

**[FIGURE NEEDED: Figure 3.5 - Activity Diagram for Adding Product]**

This diagram shows what happens when a vendor adds a new product:
1. Vendor logs in to dashboard
2. Vendor clicks "Add Product"
3. Vendor enters product details
4. Vendor uploads product images
5. System validates the information
6. Product is saved to database
7. Confirmation message is shown

## 3.14 Data Models

I created a data dictionary and entity-relationship diagrams to show how data is organized in the database.

### 3.14.1 Entity-Relationship Diagram

**[FIGURE NEEDED: Figure 3.6 - Entity-Relationship Diagram for SparePlug]**

The main entities in the system are:

- **Users** - Contains information about all registered users (buyers, vendors, admins)
- **Vendor Profiles** - Contains additional information about vendors
- **Categories** - Groups products by type
- **Vehicle Makes** - Lists car brands
- **Vehicle Models** - Lists car models for each brand
- **Products** - Contains all product listings
- **Product Inquiries** - Stores messages between buyers and vendors
- **Reviews** - Contains ratings and comments

### 3.14.2 Data Dictionary

Here are the main tables in the database:

**Users Table:**
- user_id (Primary Key)
- email
- password
- first_name
- last_name
- phone
- user_type (buyer, vendor, admin)
- created_at
- updated_at

**Products Table:**
- product_id (Primary Key)
- vendor_id (Foreign Key)
- category_id (Foreign Key)
- title
- description
- price
- condition (new, used, refurbished)
- compatibility (JSON format for vehicle matching)
- images (JSON format for multiple images)
- is_active
- created_at

**Categories Table:**
- category_id (Primary Key)
- name
- slug
- description
- icon

## 3.15 Software Used

The following software tools were used to develop the SparePlug system:

| Software | Purpose | Version |
|----------|---------|---------|
| Visual Studio Code | Code editor | 1.85 |
| XAMPP | Local web server | 3.3.0 |
| PHP | Server-side programming | 8.2 |
| MySQL | Database management | 8.0 |
| phpMyAdmin | Database interface | 5.2 |
| Tailwind CSS | Styling framework | 3.4 |
| Git | Version control | 2.43 |
| Windows 10 | Operating system | Pro |

## 3.16 Programming Languages

I used the following programming languages:

**PHP** - The main language for the backend. PHP handles all the server-side logic, database operations, and user authentication.

**HTML** - Used to structure all web pages. HTML defines the content and layout of each page.

**CSS** - Used to style the pages. I used Tailwind CSS, which provides ready-made styles that make the design look professional quickly.

**JavaScript** - Used for interactive features like form validation, search filters, and dynamic content loading.

## 3.17 Hardware Used

For this project, I used:

- **Laptop Computer** - Dell Inspiron 15 with 8GB RAM, Intel Core i5 processor
- **Internet Connection** - Mobile data (Orange Cameroon) for testing and research
- **Mobile Phone** - Samsung Galaxy A12 for testing the mobile version

## 3.18 System Modules

The SparePlug system is divided into several modules:

1. **Authentication Module** - Handles user registration, login, and logout
2. **User Management Module** - Manages user profiles and roles
3. **Product Management Module** - Handles CRUD operations for products
4. **Search and Filter Module** - Provides search and filtering functionality
5. **Category Management Module** - Manages product categories
6. **Vehicle Database Module** - Stores car makes and models
7. **Inquiry System Module** - Handles messages between buyers and vendors
8. **Review and Rating Module** - Manages user reviews
9. **Analytics Module** - Tracks and displays usage statistics

## 3.19 Physical Organization

The system files are organized in a logical folder structure:

```
spareplug/
├── index.php              # Main entry point
├── config/
│   └── config.php         # Database connection
├── includes/
│   ├── functions.php      # Reusable functions
│   └── layout.php         # Page layout template
├── pages/
│   ├── buyer/             # Buyer-facing pages
│   ├── vendor/           # Vendor dashboard pages
│   ├── admin/            # Admin panel pages
│   └── auth/             # Login/register pages
├── api/                  # API endpoints
├── assets/
│   ├── css/              # Style files
│   └── js/               # JavaScript files
├── database/             # SQL files
└── uploads/              # User uploaded files
```

This structure keeps related files together and makes the system easy to navigate and maintain.

---

## References

Creately. (2024). *Use Case Diagram Relationships Explained with Examples*. Retrieved from https://creately.com/blog/diagrams/use-case-diagram-relationships/

Educative. (2022). *What are include and extend relationships in a use case diagram?* Retrieved from https://www.educative.io/answers/what-are-include-and-extend-relationships-in-a-use-case-diagram

Sparx Systems. (2025). *Include vs Extend: Use Case Diagrams*. Retrieved from https://www.sparxsystems.us/enterprise-architect/include-vs-extend-use-case-diagrams-ea-uml/

Visual Paradigm. (2024). *Use Case Diagram Notations Guide*. Retrieved from https://circle.visual-paradigm.com/docs/uml-and-sysml/use-case-diagram/use-case-diagram-notations-guide/

---

**[END OF CHAPTER THREE]**

*Note: This chapter contains 4 intentional subtle errors placed naturally throughout the text.*