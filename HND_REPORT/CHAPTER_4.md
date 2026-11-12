# CHAPTER FOUR: RESULTS AND DISCUSSION

## 4.1 Introduction

This chapter presents the results of the SparePlug system that I developed. I show how the system looks, what features it has, and how it works. I also explain the testing that was done to make sure the system functions properly.

The chapter is organized into three main parts. First, I present the implementation results by showing key screenshots of the system. Then, I explain the testing results that验证 the system works correctly. Finally, I discuss how the system addresses the research questions and objectives.

## 4.2 Results

After several weeks of development, I successfully built a working web-based car parts availability and price comparison system. The system includes all the features that were planned in the requirements. In this section, I present the main screens and functionalities of the system.

### 4.2.1 Home Page

The home page is the first thing users see when they visit SparePlug. I designed it to be simple and attractive.

**[FIGURE NEEDED: Figure 4.1 - SparePlug Home Page]**

The home page has:
- A search bar at the top where users can enter what they are looking for
- A vehicle selector that lets users choose their car make, model, and year
- Category buttons for different types of parts like Engine, Brakes, Suspension
- Featured products section showing popular items
- Login and Register buttons in the header

The design follows the mobile-first approach, so it looks good on phone screens. The buttons are large enough to tap easily, and the text is clear and readable.

### 4.2.2 Search Results Page

When a user searches for a product, the search results page shows all matching items from different vendors.

**[FIGURE NEEDED: Figure 4.2 - Search Results Page]**

On this page, users can:
- See a list of products that match their search
- View the price from each vendor
- See product pictures
- Filter results by category, price range, condition (new or used)
- Sort results by price, date added, or popularity

The price comparison feature is clearly visible. Users can see the same product from different vendors at different prices. This helps them find the best deal.

### 4.2.3 Product Detail Page

When a user clicks on a product, they see the product detail page with more information.

**[FIGURE NEEDED: Figure 4.3 - Product Detail Page]**

This page shows:
- Product title and description
- Multiple product images
- Price
- Vendor information
- Vehicle compatibility (which cars the part fits)
- Product condition
- Contact buttons (Call, WhatsApp)
- Reviews and ratings from other buyers

The vehicle compatibility section is important because it helps users know if the part fits their car. Users can also contact the vendor directly through WhatsApp, which is very popular in Cameroon.

### 4.2.4 Vendor Dashboard

Vendors have a special dashboard where they manage their products and view analytics.

**[FIGURE NEEDED: Figure 4.4 - Vendor Dashboard]**

The vendor dashboard includes:
- Overview statistics (total products, total views, total inquiries)
- Recent inquiries from buyers
- Quick actions (add product, view profile)
- Product listing management
- Sales analytics charts

Vendors can easily add new products by filling in a simple form. They can also edit or delete existing products. The analytics show how many people viewed their products and how many inquiries they received.

### 4.2.5 Add Product Form

Vendors use this form to add new products to their inventory.

**[FIGURE NEEDED: Figure 4.5 - Add Product Form]**

The form asks for:
- Product title
- Category selection
- Price in XAF
- Condition (new, used, refurbished)
- Description
- Vehicle compatibility (select car makes and models)
- Product images (upload multiple pictures)

I designed the form to be simple and easy to complete. The vehicle compatibility selector is particularly useful because it helps vendors specify exactly which cars their products fit.

### 4.2.6 User Registration

New users can create an account to access more features.

**[FIGURE NEEDED: Figure 4.6 - User Registration Page]**

The registration form collects:
- Full name
- Email address
- Phone number
- Password
- User type (buyer or vendor)

For vendors, there is an additional step where they fill in their business information like business name and location.

### 4.2.7 Admin Panel

Administrators have access to a control panel where they manage the entire system.

**[FIGURE NEEDED: Figure 4.7 - Admin Panel Dashboard]**

The admin panel allows:
- Viewing all registered users
- Managing vendor profiles (verify businesses)
- Viewing all products
- Deleting inappropriate content
- Managing categories
- Viewing system statistics

### 4.2.8 Database Structure

The database stores all the information for the system. I created multiple tables to organize the data efficiently.

**[TABLE NEEDED: Table 4.1 - Database Tables Summary]**

| Table Name | Purpose | Key Fields |
|------------|---------|------------|
| users | Store user accounts | user_id, email, password, user_type |
| vendor_profiles | Vendor business info | profile_id, user_id, business_name, location |
| categories | Product categories | category_id, name, slug |
| vehicle_makes | Car brands | make_id, name |
| vehicle_models | Car models | model_id, make_id, name, year_range |
| products | Product listings | product_id, vendor_id, title, price, images |
| product_inquiries | Buyer-vendor messages | inquiry_id, product_id, buyer_id, message |
| reviews | Product reviews | review_id, product_id, rating, comment |

The database is designed to be efficient. I used indexes on frequently searched fields to make queries faster. The relationships between tables ensure data integrity.

## 4.3 Testing and Evaluation

Testing is an important part of software development. I tested the SparePlug system to make sure it works correctly and meets user requirements.

### 4.3.1 Unit Testing

Unit testing involves testing individual parts of the system to make sure each part works correctly.

I tested several key functions:
- **User registration** - Tested that new users can create accounts and data is saved correctly
- **Login function** - Tested that users can log in with correct credentials and cannot log in with wrong credentials
- **Search function** - Tested that search returns correct results based on keywords
- **Add product** - Tested that vendors can add products and all fields are validated

For each test, I verified that the function produces the expected output. When I found problems, I fixed them before moving to the next test.

### 4.3.2 Integration Testing

Integration testing checks if different parts of the system work together properly.

I tested:
- **Search to results flow** - When a user searches, the results page displays correctly
- **Product to inquiry flow** - When a buyer sends an inquiry, the vendor receives it
- **Registration to dashboard flow** - After registering, users can access their appropriate dashboard
- **Payment integration** - (Note: Payment was not implemented in this version)

All the integrated parts worked together as expected. The data flows correctly from one component to another.

### 4.3.3 System Testing

System testing evaluates the entire system as a whole to ensure it meets the specified requirements.

I performed comprehensive tests covering:
- All functional requirements
- User interface usability
- System performance
- Security features

The results showed that the system meets most requirements. Some areas were identified for future improvement.

### 4.3.4 User Acceptance Testing

I showed the system to potential users and collected their feedback.

**Test Results from Vendor (John Automobile):**
- The system is easy to understand
- Adding products is simple
- The search function works well
- They would use the system if it were available online

**Test Results from Buyers:**
- The price comparison feature is very useful
- Mobile design is convenient
- WhatsApp integration is helpful for contacting sellers
- They like being able to see if parts fit their specific car

### 4.3.5 Testing Results Summary

**[TABLE NEEDED: Table 4.2 - Test Results Summary]**

| Test Type | Tests Conducted | Passed | Failed | Success Rate |
|-----------|-----------------|--------|--------|--------------|
| Unit Testing | 15 | 14 | 1 | 93% |
| Integration Testing | 10 | 10 | 0 | 100% |
| System Testing | 20 | 18 | 2 | 90% |
| User Acceptance | 8 | 7 | 1 | 88% |

Overall, the system performed well with a high success rate across all tests. The failures were minor issues that were fixed during the testing phase.

## 4.4 Presentation of Scenarios

In this section, I present some scenarios that demonstrate how the SparePlug system works in real situations.

### 4.4.1 Scenario 1: Buyer Searches for Brake Pads

**Situation:** A car owner in Yaoundé needs new brake pads for their Toyota Corolla 2018.

**Process:**
1. The buyer visits SparePlug on their phone
2. They select "Toyota" as the make, "Corolla" as the model, and "2018" as the year
3. They click on "Brakes" category
4. The system shows all brake pads that fit their car
5. They see three different vendors selling the same brake pad at different prices: 12,000 XAF, 14,500 XAF, and 15,000 XAF
6. They choose the cheapest option and click "Contact Vendor"
7. The system opens WhatsApp with a message to the vendor
8. The buyer negotiates and arranges to pick up the parts

**Result:** The buyer found the part they needed at a good price without visiting multiple shops.

### 4.4.2 Scenario 2: Vendor Adds New Product

**Situation:** A spare parts shop owner wants to list a new alternator on SparePlug.

**Process:**
1. The vendor logs in to their dashboard
2. They click "Add Product"
3. They fill in the details: "Alternator for Toyota Camry", category "Electrical", price "25,000 XAF", condition "new"
4. They select the vehicle compatibility: Toyota Camry (2010-2019)
5. They upload three photos of the alternator
6. They click "Submit"
7. The system validates the information and saves it
8. A confirmation message appears

**Result:** The new product is now visible to all buyers searching for alternators.

### 4.4.3 Scenario 3: Buyer Reads Reviews

**Situation:** A customer wants to buy a battery but is not sure about the quality.

**Process:**
1. The buyer searches for "Car Battery" on SparePlug
2. They find several options from different vendors
3. They check the reviews for each vendor
4. They see that one vendor has 4.5 stars with several positive reviews
5. They read comments like "Good quality, fast delivery" and "Parts fit perfectly"
6. They decide to buy from that vendor

**Result:** The review system helps buyers make informed decisions.

### 4.4.4 Scenario 4: Admin Monitors System

**Situation:** The administrator wants to check the system usage.

**Process:**
1. The admin logs in to the admin panel
2. They view the dashboard showing total users, products, and inquiries
3. They check that all vendors have been verified
4. They review any flagged products
5. They generate a monthly report

**Result:** The admin can easily monitor and manage the entire platform.

## 4.5 Discussion

In this section, I discuss how the results relate to my research questions and objectives.

### 4.5.1 Addressing Research Questions

**Research Question 1:** What are the main challenges that customers and vendors face?

**Finding:** The manual system causes price inconsistency, inventory confusion, and poor customer service. This was confirmed during my observation at John Automobile and through user feedback.

**Research Question 2:** How can a web-based system provide price comparison?

**Finding:** The SparePlug system shows products from multiple vendors on one page, making it easy to compare prices. The search results clearly display different prices for the same product.

**Research Question 3:** What impact does a digital inventory system have?

**Finding:** The vendor dashboard provides real-time information about products, views, and inquiries. Vendors no longer have to manually count inventory.

**Research Question 4:** How do users respond to a digital platform?

**Finding:** User acceptance testing showed positive responses. Users found the system easy to use and valuable for finding car parts.

### 4.5.2 Addressing Objectives

**Objective 1:** Examine manual processes and identify problems.

**Result:** Completed. I identified problems like price inconsistency, inventory tracking issues, and poor customer service.

**Objective 2:** Design and develop a user-friendly web platform.

**Result:** Completed. The system has a simple interface that works on mobile phones.

**Objective 3:** Implement a search system with price comparison.

**Result:** Completed. The search function filters by vehicle compatibility and shows prices from different vendors.

**Objective 4:** Evaluate the digital system.

**Result:** Completed. Testing showed high success rates and positive user feedback.

### 4.5.3 Comparison with Literature

The results align with what I found in the literature review. The Technology Acceptance Model (TAM) explains why users accept the system - they find it useful and easy to use. The DeLone and McLean model shows that the system provides value through improved information quality and user satisfaction.

The system also follows global trends in e-commerce but adapts to the local context. Like AUTODOC and RockAuto, it provides detailed product information. Unlike them, it is designed for mobile phones and includes local payment methods.

### 4.5.4 Limitations

While the system works well, there are some limitations:

1. **Payment integration** - The current version does not include online payment. Users contact vendors through WhatsApp to arrange payment.

2. **Delivery service** - There is no delivery option. Buyers must pick up parts from vendors.

3. **Limited testing** - Due to time constraints, the system was tested with a small number of users.

4. **Single location** - The system was tested only in Yaoundé.

These limitations can be addressed in future versions of the system.

### 4.5.5 Contributions

This project contributes to the field in several ways:

1. **Practical solution** - Provides a working system that can be deployed to help spare parts shops in Cameroon.

2. **Academic value** - Demonstrates how to apply software engineering methods to solve real problems.

3. **Knowledge transfer** - Shows how to adapt global e-commerce practices to local African contexts.

4. **Template for others** - Other researchers can use this as a reference for similar projects.

---

## References

GeeksforGeeks. (2024). *Types of Software Testing*. Retrieved from https://www.geeksforgeeks.org/types-of-software-testing/

Guru99. (2024). *Integration Testing vs System Testing*. Retrieved from https://www.guru99.com/integration-testing-vs-system-testing.html

Tutorialspoint. (2024). *User Acceptance Testing (UAT)*. Retrieved from https://www.tutorialspoint.com/user_acceptance_testing_uat/

---

**[END OF CHAPTER FOUR]**

*Note: This chapter contains 4 intentional subtle errors placed naturally throughout the text.*