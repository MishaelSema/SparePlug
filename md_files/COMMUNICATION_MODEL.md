# SparePlug Communication Model

## How Communication Works

### ✅ **OFF-PLATFORM Communication** (All Actual Conversations)

**Buyer → Vendor Contact:**
1. Buyer sees vendor's contact information (WhatsApp, phone, email) on product listing
2. Buyer contacts vendor **directly** via:
   - **WhatsApp** (opens with pre-filled message about the product)
   - **Phone call** (direct dial)
   - **Email** (opens email client with pre-filled message)

3. All conversations, negotiations, transactions happen **outside the platform**
4. Platform is NOT involved in actual communication

---

### 📊 **INQUIRY TRACKING** (Platform's Role)

The platform tracks **interest/leads**, NOT actual conversations:

**What Buyers Do:**
- Click "Contact Seller" button
- Choose contact method (WhatsApp, phone, email)
- Platform logs this as an "inquiry" for tracking purposes
- Buyer is redirected to their chosen contact method

**What Vendors See:**
- **Inquiry Dashboard** showing:
  - Which product was inquired about
  - Buyer's name and basic info
  - Date/time of inquiry
  - Which method buyer chose (WhatsApp/phone/email)
  - Status (new, contacted, resolved)

**What Vendors CANNOT See:**
- ❌ The actual message content (no in-app messaging)
- ❌ The actual conversation that happened off-platform
- ❌ Any transaction details
- ❌ Delivery status

**What Vendors CAN Do:**
- See inquiry statistics (count, trends)
- Mark inquiries as contacted/resolved
- Add internal notes
- Track which products get most inquiries

---

## Implementation Model

### For Buyers:
```php
// When buyer clicks "Contact Seller"
1. Show vendor's public contact info:
   - WhatsApp number (click to open WhatsApp)
   - Phone number (click to dial)
   - Email (click to open email client)

2. Log the inquiry to database:
   - user_id (buyer)
   - product_id
   - vendor_id
   - contact_method chosen
   - timestamp
   - status: 'inquiry_made'
```

### For Vendors:
```php
// Vendor sees inquiry dashboard
1. List of inquiries with:
   - Product name
   - Buyer name
   - Date/time
   - Contact method chosen
   - Status

2. Vendor actions:
   - Mark as "contacted" (they reached out externally)
   - Mark as "resolved" (completed externally)
   - Add internal notes
   - Filter by status/date

3. NO messaging interface on platform
4. NO way to respond through platform
```

---

## Database Schema for Inquiries

```sql
CREATE TABLE product_inquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    vendor_id INT NOT NULL,
    contact_method ENUM('whatsapp', 'phone', 'email') NOT NULL,
    status ENUM('new', 'contacted', 'resolved', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    contacted_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    vendor_notes TEXT NULL, -- Internal notes vendor can add
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id)
);
```

---

## Vendor Inquiry Page Features

**What Vendors Will See:**

1. **Inquiry List**
   - Product name (with link)
   - Buyer name
   - Contact method icon
   - Date/time
   - Status badge

2. **Quick Actions**
   - "Mark as Contacted" button
   - "Mark as Resolved" button
   - "Add Note" button
   - Filter buttons (All, New, Contacted, Resolved)

3. **Statistics**
   - Total inquiries
   - New inquiries count
   - Conversion rate (inquiries to sales - estimates)
   - Most inquired products

4. **No Messaging Interface**
   - No in-app chat
   - No message composer
   - Just tracking and status management

---

## User Experience Flow

### Buyer Side:
```
1. Browse product listing
2. Click "Contact Seller" button
3. See 3 contact options:
   - 🟢 WhatsApp (with pre-filled message)
   - 📞 Phone (direct call)
   - 📧 Email (opens email client)
4. Choose method → Opens external app
5. Buyer talks to vendor outside platform
6. All negotiation and transaction happens externally
```

### Vendor Side:
```
1. Login to vendor dashboard
2. See "Inquiries" section
3. View list of buyers who showed interest
4. See which contact method they chose
5. Click product to see details
6. Vendor contacts buyer externally using provided info
7. Mark inquiry status as "contacted"
8. Eventually mark as "resolved" when done
```

---

## Summary

❌ **What the Platform DOES NOT Do:**
- No in-app messaging
- No chat system
- No message storage
- No conversation threads
- No response interface

✅ **What the Platform DOES Do:**
- Tracks who contacted which vendor
- Shows vendor their contact info
- Tracks inquiry status (new, contacted, resolved)
- Provides statistics and analytics
- Helps vendors manage leads

**This is a CONNECTOR platform, not a COMMUNICATION platform.**





