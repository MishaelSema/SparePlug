-- =====================================================
-- COMPLETE FIX - Run these steps in order
-- =====================================================

-- STEP 1: Check if products table exists and its structure
DESCRIBE products;

-- STEP 2: If 'images' column is missing, add it:
ALTER TABLE products ADD COLUMN images TEXT AFTER compatibility;

-- STEP 3: Now try importing your data again
-- Or if data already imported, verify:
SELECT id, title, images FROM products LIMIT 5;
