-- =====================================================
-- Add document upload fields to vendor_profiles
-- =====================================================

ALTER TABLE vendor_profiles 
ADD COLUMN verification_status ENUM('pending', 'submitted', 'approved', 'rejected') DEFAULT 'pending' AFTER is_verified,
ADD COLUMN verification_documents JSON AFTER verification_status,
ADD COLUMN admin_notes TEXT AFTER verification_documents,
ADD COLUMN verified_at TIMESTAMP NULL AFTER admin_notes,
ADD COLUMN verified_by INT NULL AFTER verified_at;

-- Add foreign key for verified_by
ALTER TABLE vendor_profiles 
ADD FOREIGN KEY (verified_by) REFERENCES users(id);

-- Index for faster queries
ALTER TABLE vendor_profiles ADD INDEX idx_verification_status (verification_status);
