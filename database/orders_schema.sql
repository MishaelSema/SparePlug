-- =====================================================
-- ORDERS TABLE - For Escrow Payment System
-- =====================================================

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    buyer_id INT NOT NULL,
    vendor_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) DEFAULT 10.00,
    commission_amount DECIMAL(10,2) DEFAULT 0,
    seller_amount DECIMAL(10,2) DEFAULT 0,
    
    -- Payment
    payment_status ENUM('pending', 'paid', 'released', 'refunded', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('momo', 'orange_money', 'cash') DEFAULT 'momo',
    payment_reference VARCHAR(100),
    payment_proof VARCHAR(255),
    paid_at TIMESTAMP NULL,
    
    -- Fulfillment
    fulfillment_status ENUM('pending', 'processing', 'shipped', 'ready_for_pickup', 'delivered', 'completed', 'disputed') DEFAULT 'pending',
    delivery_method ENUM('delivery', 'pickup') DEFAULT 'pickup',
    delivery_address TEXT,
    delivery_notes TEXT,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    
    -- Escrow
    escrow_status ENUM('held', 'released', 'refunded') DEFAULT 'held',
    released_at TIMESTAMP NULL,
    
    -- Buyer confirmation
    buyer_confirmed BOOLEAN DEFAULT FALSE,
    confirmed_at TIMESTAMP NULL,
    
    -- Dispute
    dispute_reason TEXT,
    dispute_status ENUM('none', 'opened', 'resolved', 'closed') DEFAULT 'none',
    resolved_at TIMESTAMP NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (vendor_id) REFERENCES vendor_profiles(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    
    INDEX idx_buyer (buyer_id),
    INDEX idx_vendor (vendor_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (payment_status, fulfillment_status),
    INDEX idx_created (created_at)
);

-- Order statuses history
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    status_type ENUM('payment', 'fulfillment', 'escrow', 'dispute') NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    note TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Commission rates config
CREATE TABLE IF NOT EXISTS commission_rates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_type ENUM('free', 'basic', 'premium') NOT NULL,
    rate DECIMAL(5,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default commission rates
INSERT INTO commission_rates (plan_type, rate, is_active) VALUES
('free', 15.00, TRUE),
('basic', 10.00, TRUE),
('premium', 5.00, TRUE)
ON DUPLICATE KEY UPDATE rate=rate;
