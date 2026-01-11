# Future Updates & Roadmap - SparePlug

This document outlines the planned extensions for SparePlug (Phase 2 & 3), which can be included in **Chapter 4 (Suggestions)** or **Chapter 5 (Conclusion & Recommendations)** of the HND report.

## 1. Advanced Vendor Analytics Dashboard
While the current MVP focuses on listing and connecting, the future version will activate the `analytics` module.
- **Engagement Tracking:** Vendors will see heatmaps of which parts are viewed most.
- **Search Insight:** Data on "Searched but not found" parts to help vendors know what stock to buy.
- **Conversion Rates:** Tracking how many views turn into WhatsApp clicks.

## 2. Automated Subscription & Payment Integration
The current system manually assigns the 'Free' plan. Future updates will include:
- **Mobile Money API:** Integration with MTN MoMo and Orange Money for instant plan upgrades.
- **Automated Expiry:** A cron job to automatically downgrade vendors to the 'Free' plan when their period ends.
- **Tiered Listing Limits:** Enforcing the `FREE_LISTINGS_LIMIT` programmatically.

## 3. Real-Time In-App Messaging
Currently, SparePlug relies on external WhatsApp connectivity.
- **Legacy Support:** Keep WhatsApp for quick trust.
- **In-App Chat:** Build a socket-based internal chat to keep users on the platform and log all business communications for safety.

## 4. Mobile Application (Android/iOS)
Using the existing PHP backend as a RESTful API to power:
- **Push Notifications:** Alerting buyers when a part they "Watchlisted" becomes available.
- **Image Scanning:** Allowing vendors to upload parts by simply taking a photo.

## 5. AI Compatibility Checker
Integrating a Machine Learning model to:
- **Smart Search:** Interpret natural language (e.g., "the small plastic thing under the Corolla 2010 hood").
- **Automatic Tagging:** Automatically identifying the part category from the uploaded photo.

---

### Note for Defense:
Mentioning these "Future Updates" shows the jurors that you have a **Long-term Vision** for the project and that the database architecture (like the `analytics` and `subscriptions` tables) was designed with scalability in mind from Day 1.
