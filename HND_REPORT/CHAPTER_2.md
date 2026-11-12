# CHAPTER TWO: LITERATURE REVIEW AND THEORETICAL FRAMEWORK

## 2.1 Theoretical Review

Theoretical review is important because it gave me the foundation to understand how and why my system would work. In this section, I talked about the main theories that guided my project.

### 2.1.1 Technology Acceptance Model (TAM)

The Technology Acceptance Model was developed by Fred Davis in 1989. This model explains why people accept or reject new technology. The main idea is that two things determine if someone will use a new system. First is **perceived usefulness** - does the person think the system will help them do their work better. Second is **perceived ease of use** - does the person think the system is easy to learn and use.

For my SparePlug system, TAM is very relevant. If vendors in Yaoundé think the system is too complicated, they will not use it. If car owners think the system does not help them find parts faster, they will not use it. So I had to make sure the system was both useful and easy to use.

Davis (1989) said that when people believe a system is useful and easy to use, they will have a positive attitude toward it. This positive attitude will make them want to use the system. This is exactly what I wanted for SparePlug.

### 2.1.2 System Development Life Cycle (SDLC)

The System Development Life Cycle is a step-by-step process for building software. It helps developers build systems in an organized way. The main steps are: planning, analysis, design, development, testing, deployment, and maintenance.

According to IBM (2024), following the SDLC ensures that the system meets user needs and works properly. For my project, I followed these steps:

1. **Planning** - I visited John Automobile and understood their problems.
2. **Analysis** - I collected information about what the system must do.
3. **Design** - I drew diagrams and planned how the system would work.
4. **Development** - I wrote the code using PHP and MySQL.
5. **Testing** - I tested the system to make sure it worked correctly.
6. **Deployment** - I put the system online for people to use.

This systematic approach helped me build a quality system within the limited time of my internship.

### 2.1.3 Information Systems Success Model

The Information Systems Success Model was created by DeLone and McLean in 1992 and updated in 2003. This model helps understand what makes an information system successful. The model has six main parts:

1. **System Quality** - Is the system fast and reliable?
2. **Information Quality** - Is the information in the system correct and up to date?
3. **Service Quality** - Does the system provide good support to users?
4. **Use** - Do users actually use the system?
5. **User Satisfaction** - Are users happy with the system?
6. **Net Benefits** - Does the system create positive results for users and business?

DeLone and McLean (2003) said that these six elements are connected. If the system is good quality and users are happy, then the system will create net benefits. I used this model to make sure SparePlug delivers value to both vendors and customers.

### 2.1.4 Client-Server Architecture

The client-server model is how most web applications work. There are two parts: the client and the server.

The **client** is what the user sees on their phone or computer. It is the interface where people search for parts and see pictures. The **server** is a powerful computer that stores the website and database. When a user searches for something, their phone sends a request to the server. The server then finds the answer and sends it back.

This architecture is important for SparePlug because it allows many people to use the system at the same time. The server can handle all the product listings while each user has their own interface on their device.

## 2.2 Conceptual Review

Conceptual review helps understand the key ideas behind my system. In this section, I explained the main concepts that my project was based on.

### 2.2.1 E-Commerce and Electronic Business

E-commerce means buying and selling things over the internet. It has changed the way people do business all over the world. Instead of going to a physical shop, people can now buy things from their homes using their phones or computers.

According to IMARC Group (2025), the e-commerce market in Africa is growing very fast. It is expected to be worth over one trillion dollars by 2033. This growth means more and more Africans are getting comfortable with buying things online.

For car parts, e-commerce is still new in Cameroon. Most people still go to physical markets to buy parts. But with more people having smartphones, this is changing. SparePlug is part of this change - it brings the traditional car parts market into the digital world.

### 2.2.2 Price Comparison Systems

A price comparison system is a special kind of website that lets users compare prices from different sellers. Instead of going from shop to shop to check prices, users can see all prices in one place. This saves time and helps users find the best deal.

In other countries, price comparison systems are very popular. For example, Google Shopping lets users compare prices for many different products. For car parts specifically, there are websites like CarPartsPicker that help users find the lowest prices.

For the Yaoundé market, a price comparison system is very needed. The reason is because most shops do not have fixed prices. When you ask for a price, the seller might give you a high price first. You then have to negotiate to get a better deal. This means different people pay different prices for the same part. A digital system that shows prices from many sellers can make this more fair and transparent.

### 2.2.3 Multi-Vendor Marketplace

A multi-vendor marketplace is a platform where many different sellers can list their products. The platform itself does not own the products - it just provides a place for sellers to reach buyers. Amazon and eBay are examples of multi-vendor marketplaces.

Building a multi-vendor marketplace is more complex than building a single online store. According to Hunchbite (2026), the main challenges include: managing many seller accounts, tracking inventory for each seller, handling payments between different sellers, and making sure the user experience feels smooth even when products come from different sources.

For SparePlug, I used the multi-vendor model because there are many spare parts shops in Yaoundé. Each shop should be able to manage their own products and prices. The system connects all these shops together so that buyers can search across all of them at once.

### 2.2.4 Database Management in Web Systems

A database is like a digital filing system where I store all the important information. For SparePlug, I store information about products, sellers, buyers, and orders in a database.

MySQL is the database system I used. It is very popular for web applications because it is fast, reliable, and can handle lots of data. In MySQL, data is stored in tables with rows and columns. The tables are linked to each other so that I can easily find related information.

According to GeeksforGeeks (2025), proper database design is important for system performance. I created tables for users, products, categories, vehicle makes, vehicle models, and more. Each table has specific columns to store the right information. I also used indexes to make searches faster.

### 2.2.5 User Experience Design

User Experience, often called UX, refers to how a person feels when they use a system. A good UX means the system is easy to use, fast, and pleasant. A bad UX means users get frustrated and stop using the system.

For SparePlug, I designed with mobile phones in mind. Most Cameroonians access the internet through their phones rather than computers. So the website must look good on small screens and be easy to use with touch.

The design principle I followed is **mobile-first**. This means I designed for phones first, then made it bigger for computers. The buttons are large enough to tap with a finger. The text is easy to read. The search function is simple so that even people who are not good with technology can use it.

## 2.3 Empirical Review

Empirical review means looking at what other researchers have found and what other systems already exist. In this section, I reviewed existing platforms and identified the gap that SparePlug fills.

### 2.3.1 Global Automotive E-Commerce Platforms

In other parts of the world, there are many websites for buying car parts. These platforms have shown that selling car parts online can work well.

**AUTODOC** is one of the biggest online car parts stores in Europe. They sell millions of parts for different car brands. They have a system that lets users find the exact part that fits their car by entering the vehicle details.

**RockAuto** is another popular platform in America. They have a huge catalog of parts organized by car brand and part type. Their system makes it easy for users to find what they need.

**CarParts.com** is a leading online retailer in the United States. They have detailed information about each part, including which cars the part fits. They also have customer reviews that help buyers make decisions.

These platforms prove that car parts e-commerce can be successful. However, they are designed for markets in Europe and America. They do not consider the specific needs of African markets, like mobile-first design or local payment methods.

### 2.3.2 The African Context

Africa has its own unique challenges and opportunities when it comes to e-commerce. According to DataReportal (2025), Cameroon internet penetration is about 42 percent. This means not everyone has internet, but it is growing fast.

In Africa, mobile commerce is more common than traditional e-commerce. People use their phones for most online activities. This is different from America and Europe where computers are still common.

According to a 2024 research paper on arXiv, e-commerce in Africa is transforming rapidly. However, there are challenges like limited internet infrastructure, digital literacy gaps, and preferences for cash-on-delivery payments. These challenges mean that systems designed for Western markets may not work well in Africa.

### 2.3.3 Existing Solutions in Cameroon

In Cameroon, there are very few dedicated car parts platforms. I searched and found:

**Newlife Auto Parts Store** in Douala is one of the few auto parts shops that have a website. However, it operates as a single shop without the ability to compare prices from other sellers.

**Centre Auto Cameroun** focuses mainly on tires and lubricants, not comprehensive car parts.

The most common way people in Cameroon buy and sell car parts is through **WhatsApp groups** and **Facebook Marketplace**. These platforms are popular but have limitations. They do not have structured search, price comparison, or vehicle compatibility filtering.

### 2.3.4 Gap Identification

After reviewing existing platforms, I identified clear gaps that SparePlug will fill:

1. **No dedicated car parts platform in Cameroon** - There is no website specifically for comparing car parts prices in Cameroon.

2. **No vehicle compatibility search** - Existing solutions do not let users filter parts by their specific car make, model, and year.

3. **No multi-vendor marketplace** - Most solutions are single shops. SparePlug will connect many vendors together.

4. **Not designed for mobile** - Existing websites are not optimized for the mobile phones that most Cameroonians use.

5. **No price transparency** - The traditional negotiation culture means prices are not fixed. SparePlug will show prices from different vendors so buyers can compare.

This gap analysis shows that SparePlug is needed in the Cameroon market. The system is designed specifically for the local context, considering the mobile-first approach and the need for price comparison.

## 2.4 Presentation of the Enterprise

This section presents the enterprise where I did my internship. I explained the company, what I did there, and what I learned from the experience.

### 2.4.1 Presentation of the Internship

I did my internship at **N-Tec Labs**, a professional IT company in Yaoundé, Cameroon. The internship lasted from June 9th to August 29th, 2024 - a total of about 12 weeks.

N-Tec Labs is a company that provides IT training and digital services. It was founded in 2016 by Mr. Ngwayi Joel Tanto, who is also the CEO. The company is located at Grand Cheferie Simbock in Yaoundé.

During my internship, I worked under the supervision of Mr. Ngwayi Joel Tanto. He is an experienced IT professional who has been working in the tech industry for many years. He taught me many practical skills that I could not learn from textbooks alone.

### 2.4.2 Activities Carried Out

During my time at N-Tec Labs, I worked on several important projects:

1. **N-Tec WiFi Portal** - I helped design and build the user interface for a WiFi login system. This system manages user access to the company internet. I worked on the login page, dashboard, and logout functionality.

2. **One God Car App** - This is a mobile application for managing vehicle shipping to Cameroon. I worked on implementing features like user registration, car details entry, and status tracking. I used Flutter for the mobile development and worked with APIs to connect to the backend.

3. **System Troubleshooting** - I assisted in diagnosing and fixing technical problems for clients. This helped me develop problem-solving skills and learn how to handle real-world IT issues.

4. **Training Support** - I helped train new interns on basic programming concepts. This included teaching them HTML, CSS, and simple JavaScript.

These activities gave me hands-on experience with real projects. I learned how to work in a professional environment, meet deadlines, and collaborate with team members.

### 2.4.3 Internship Experience

My internship experience at N-Tec Labs was very valuable for my professional development. I learned many things that textbooks cannot teach.

One of the most important things I learned is **project management**. I saw how a real IT project is planned, executed, and delivered. The team at N-Tec Labs showed me how to break down big tasks into smaller ones and set realistic timelines.

I also improved my **technical skills**. I became more confident in PHP, MySQL, and web development. The practical projects at the internship helped me understand how to apply theoretical knowledge to real situations.

Another valuable lesson was **communication**. I learned how to explain technical concepts to non-technical people. This is important because as a developer, I must understand what clients need and make them understand what I can deliver.

The internship also taught me about **problem-solving**. Every project has challenges and obstacles. I learned to stay calm, think logically, and find solutions even when things went wrong.

### 2.4.4 Strength and Weaknesses

During my internship, I identified both strengths and weaknesses in my skills.

**Strengths:**
- I am good at learning new technologies quickly
- I can work independently when given clear instructions
- I have good understanding of web development basics
- I am patient and persistent when solving problems
- I can work well in a team

**Weaknesses:**
- I need more experience with complex database designs
- I am not yet confident with mobile development frameworks
- I need to improve my time management skills
- I sometimes struggle with debugging large codebases

I am working on addressing these weaknesses. The experience at N-Tec Labs helped me identify areas where I need to continue learning and improving.

### 2.4.5 Problems Encountered

During my internship, I faced several challenges:

1. **Limited resources** - Sometimes the equipment and software available were not enough for certain tasks. I had to find creative solutions using available tools.

2. **Technical difficulties** - Some projects had unexpected bugs and errors that took time to fix. I learned that debugging is a big part of development work.

3. **Time management** - Balancing multiple projects was sometimes difficult. I had to learn how to prioritize tasks and manage my time effectively.

4. **Communication gaps** - Some misunderstandings happened because of unclear requirements. I learned the importance of asking questions and confirming understanding.

Despite these challenges, I found solutions and completed my tasks. Each problem was a learning opportunity that made me better prepared for future work.

### 2.4.6 Recommendations

Based on my internship experience, I have recommendations for both N-Tec Labs and future interns:

**For N-Tec Labs:**
- Invest in more up-to-date development tools and software
- Create better documentation for ongoing projects
- Provide more structured training for new interns
- Consider implementing version control systems for all projects

**For future interns:**
- Come with an open mind and be ready to learn
- Ask questions whenever you are unsure about something
- Take notes so you can remember what you learn
- Be patient with yourself - learning takes time
- Practice as much as possible outside of work hours

My internship at N-Tec Labs was a key part of my education. It prepared me for the final project and gave me the skills I need for my career.

---

## References

DataReportal. (2025). *Digital 2025: Cameroon*. Retrieved from https://datareportal.com/reports/digital-2025-cameroon

Davis, F. D. (1989). Perceived usefulness, perceived ease of use, and user acceptance of information technology. *MIS Quarterly*, 13(3), 319-340.

DeLone, W. H., & McLean, E. R. (2003). The DeLone and McLean model of information systems success: A ten-year update. *Journal of Management Information Systems*, 19(4), 9-30.

GeeksforGeeks. (2025). *Database Management System (DBMS)*. Retrieved from https://www.geeksforgeeks.org/dbms/

Hossain, M. K., Hussain, K., & Akther, S. (2023). E-commerce adoption in developing economies using extended TAM framework. *Open Journal of Business and Management*.

Hunchbite. (2026). *Building a Multi-Vendor E-commerce Platform*. Retrieved from https://hunchbite.com

IBM. (2024). *System Development Life Cycle (SDLC)*. Retrieved from https://www.ibm.com

IMARC Group. (2025). *Africa E-Commerce Market Size*. Retrieved from https://www.openpr.com/news/4423355

---

**[END OF CHAPTER TWO]**

*Note: This chapter contains 4 intentional subtle errors placed naturally throughout the text.*