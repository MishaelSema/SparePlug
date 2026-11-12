<?php
/**
 * SparePlug Mailer Class
 * Handles all system email notifications with consistent branding
 */

class Mailer {
    private $host;
    private $port;
    private $user;
    private $pass;
    private $fromName;

    public function __construct() {
        $this->host = SMTP_HOST;
        $this->port = SMTP_PORT;
        $this->user = SMTP_USER;
        $this->pass = SMTP_PASS;
        $this->fromName = APP_NAME;
    }

    /**
     * Send email using direct SMTP communication
     */
    public function send($to, $subject, $body, $altBody = '') {
        $htmlBody = $this->getTemplate($subject, $body);
        
        // SMTP Configuration
        $smtp_host = $this->host;
        $smtp_port = $this->port;
        $smtp_user = $this->user;
        $smtp_pass = $this->pass;
        $from_email = $this->user;
        $from_name = $this->fromName;

        try {
            // Setup connection
            $socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);
            if (!$socket) throw new Exception("Could not connect to SMTP host: $errstr ($errno)");

            $this->getResponse($socket, "220");

            // HELO/EHLO
            fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $this->getResponse($socket, "250");

            // STARTTLS
            fwrite($socket, "STARTTLS\r\n");
            $this->getResponse($socket, "220");
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

            // EHLO again after STARTTLS
            fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $this->getResponse($socket, "250");

            // AUTH LOGIN
            fwrite($socket, "AUTH LOGIN\r\n");
            $this->getResponse($socket, "334");
            fwrite($socket, base64_encode($smtp_user) . "\r\n");
            $this->getResponse($socket, "334");
            fwrite($socket, base64_encode($smtp_pass) . "\r\n");
            $this->getResponse($socket, "235");

            // MAIL FROM
            fwrite($socket, "MAIL FROM:<$from_email>\r\n");
            $this->getResponse($socket, "250");

            // RCPT TO
            fwrite($socket, "RCPT TO:<$to>\r\n");
            $this->getResponse($socket, "250");

            // DATA
            fwrite($socket, "DATA\r\n");
            $this->getResponse($socket, "354");

            // Headers & Body
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $headers .= "To: <$to>\r\n";
            $headers .= "From: $from_name <$from_email>\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "Date: " . date("r") . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            
            fwrite($socket, $headers . "\r\n" . $htmlBody . "\r\n.\r\n");
            $this->getResponse($socket, "250");

            // QUIT
            fwrite($socket, "QUIT\r\n");
            fclose($socket);

            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            return false;
        }
    }

    private function getResponse($socket, $expectedCode) {
        $response = "";
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == " ") break;
        }
        if (substr($response, 0, 3) !== $expectedCode) {
            throw new Exception("SMTP Error: Expected $expectedCode, got: $response");
        }
        return $response;
    }

    /**
     * Centralized HTML Template
     */
    private function getTemplate($title, $content) {
        $primaryColor = PRIMARY_COLOR;
        $accentColor = ACCENT_COLOR;
        // Use SVG for better compatibility or public CDN if available
        $logoUrl = BASE_URL . '/assets/sparePlugLogoForLightBackgroundsSVG.svg';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif; line-height: 1.6; color: #1f2937; margin: 0; padding: 0; background-color: #f3f4f6; }
                .container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
                .header { background: #111827; padding: 40px 20px; text-align: center; border-bottom: 4px solid {$accentColor}; }
                .logo { height: 48px; filter: brightness(0) invert(1); }
                .content { padding: 40px; }
                .footer { background: #f9fafb; padding: 30px; text-align: center; color: #6b7280; font-size: 13px; border-top: 1px solid #e5e7eb; }
                .button { display: inline-block; padding: 14px 28px; background-color: {$primaryColor}; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: bold; margin: 25px 0; text-align: center; }
                .footer-links a { color: {$primaryColor}; text-decoration: none; margin: 0 12px; font-weight: 500; }
                .badge { display: inline-block; padding: 6px 14px; border-radius: 99px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
                .badge-info { background: #eff6ff; color: #2563eb; }
                .badge-success { background: #ecfdf5; color: #059669; }
                .badge-warning { background: #fffbeb; color: #d97706; }
                .h1 { font-size: 24px; font-weight: 800; color: #111827; margin-bottom: 20px; margin-top: 0; }
                .p { margin-bottom: 16px; color: #4b5563; font-size: 16px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='{$logoUrl}' alt='SparePlug' class='logo'>
                </div>
                <div class='content'>
                    <h1 class='h1'>{$title}</h1>
                    <div class='p'>{$content}</div>
                </div>
                <div class='footer'>
                    <p style='margin-bottom: 15px;'>&copy; " . date('Y') . " SparePlug Cameroon. Helping you find the right parts.</p>
                    <div class='footer-links'>
                        <a href='" . BASE_URL . "'>Marketplace</a>
                        <a href='" . BASE_URL . "?page=contact'>Support</a>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Email: Account Verification
     */
    public function sendVerification($to, $name, $link) {
        $subject = "Verify your SparePlug Account";
        $content = "
            <p>Hello {$name},</p>
            <p>Welcome to SparePlug! We're excited to have you on board. To start buying and selling auto parts, please verify your email address by clicking the button below:</p>
            <p style='text-align: center;'>
                <a href='{$link}' class='button'>Verify My Account</a>
            </p>
            <p>This link will expire in 24 hours.</p>
            <p>If you didn't create an account, you can safely ignore this email.</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Password Reset
     */
    public function sendPasswordReset($to, $link) {
        $subject = "Reset Your SparePlug Password";
        $content = "
            <p>You recently requested to reset your password for your SparePlug account. Click the button below to choose a new password:</p>
            <p style='text-align: center;'>
                <a href='{$link}' class='button'>Reset Password</a>
            </p>
            <p>This link will expire in 1 hour. If you did not request a password reset, please ignore this email.</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Product Creation Notification
     * Updated: Now includes verification status context
     */
    public function sendProductNotification($to, $vendorName, $productTitle, $isVerified = true) {
        if ($isVerified) {
            $subject = "Your Product is Live on SparePlug!";
            $content = "
                <p>Hello {$vendorName},</p>
                <p>Great news! Your product <strong>'{$productTitle}'</strong> has been successfully listed and is now visible to thousands of buyers.</p>
                <div class='badge badge-success'>Live & Active</div>
                <p>You can manage your listing from your vendor dashboard.</p>
                <p>Keep your products updated to attract more buyers!</p>";
        } else {
            $subject = "Product Added - Awaiting Verification";
            $content = "
                <p>Hello {$vendorName},</p>
                <p>Your product <strong>'{$productTitle}'</strong> has been added to your inventory.</p>
                <div class='badge badge-warning'>Pending Verification</div>
                <p>Note: Your shop is currently under verification review. Once approved, your products will become visible to buyers.</p>
                <p>Check your vendor dashboard for more details.</p>";
        }
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Vendor Verification Submitted
     */
    public function sendVerificationSubmitted($to, $vendorName) {
        $subject = "Verification Documents Received - SparePlug";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>We have received your verification documents. Our team will review them within <strong>2-3 business days</strong>.</p>
            <div class='badge badge-info'>Under Review</div>
            <p>You can still add products to your inventory, but they won't be visible to buyers until your shop is verified.</p>
            <p>You'll receive an email once your verification is approved.</p>
            <p>If you have any questions, please contact our support team.</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Vendor Verified (Approved)
     */
    public function sendVerificationApproved($to, $vendorName) {
        $subject = "Congratulations! Your Shop is Verified - SparePlug";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>🎉 <strong>Great news!</strong> Your shop has been verified and approved!</p>
            <div class='badge badge-success'>Verified ✓</div>
            <p>Your products are now visible to all buyers on SparePlug. You can start receiving orders and grow your business!</p>
            <p><strong>What's next:</strong></p>
            <ul>
                <li>Add more products to your inventory</li>
                <li>Complete your shop profile with business details</li>
                <li>Consider upgrading to Premium for more features</li>
            </ul>
            <p>Log in to your vendor dashboard to get started.</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Vendor Verification Rejected
     */
    public function sendVerificationRejected($to, $vendorName, $reason = '') {
        $subject = "Verification Update - SparePlug";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>We regret to inform you that your verification documents could not be approved at this time.</p>
            <div class='badge badge-danger'>Not Approved</div>
            " . ($reason ? "<p><strong>Reason:</strong> {$reason}</p>" : "<p>Please review your documents and resubmit.</p>") . "
            <p>To retry the verification process:</p>
            <ol>
                <li>Log in to your vendor dashboard</li>
                <li>Go to Profile Settings</li>
                <li>Upload clearer/valid business documents</li>
                <li>Submit for re-review</li>
            </ol>
            <p>If you believe this is an error, please contact our support team.</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Payment Confirmed (Buyer marked as paid)
     */
    public function sendPaymentConfirmed($to, $vendorName, $orderNumber, $amount) {
        $subject = "Payment Received - Order #{$orderNumber}";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>Good news! The buyer has confirmed payment for Order <strong>#{$orderNumber}</strong>.</p>
            <div class='badge badge-success'>Payment Confirmed</div>
            <p><strong>Amount:</strong> " . number_format($amount) . " XAF</p>
            <p>Please prepare the order for delivery/pickup as soon as possible.</p>
            <p>Log in to your vendor dashboard to manage this order.</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Order Completed (Buyer confirmed receipt)
     */
    public function sendOrderCompleted($to, $vendorName, $orderNumber, $amount) {
        $subject = "Order Completed - #{$orderNumber}";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>The buyer has confirmed receipt of Order <strong>#{$orderNumber}</strong>. 🎉</p>
            <div class='badge badge-success'>Completed</div>
            <p><strong>Order Amount:</strong> " . number_format($amount) . " XAF</p>
            <p>Payment has been released to your account (minus platform commission).</p>
            <p>Thank you for doing business on SparePlug!</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Order Disputed
     */
    public function sendOrderDisputed($to, $vendorName, $orderNumber, $reason) {
        $subject = "Order Dispute - #{$orderNumber}";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>A dispute has been opened for Order <strong>#{$orderNumber}</strong>.</p>
            <div class='badge badge-danger'>Disputed</div>
            <p><strong>Reason:</strong> {$reason}</p>
            <p>Please respond to this dispute through your vendor dashboard as soon as possible.</p>
            <p>Our team will help resolve this matter.</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Account Suspension/Activation
     */
    public function sendAccountStatusUpdate($to, $name, $status) {
        $isSuspended = ($status === 'suspended');
        $subject = $isSuspended ? "Important: Your Account Status Update" : "Great News: Your Account is Re-activated!";
        
        $badgeClass = $isSuspended ? 'badge-warning' : 'badge-success';
        $statusText = $isSuspended ? 'Suspended' : 'Active';
        
        $content = "
            <p>Hello {$name},</p>
            <p>We are writing to inform you that your SparePlug account status has been updated to:</p>
            <div class='badge {$badgeClass}'>{$statusText}</div>
            " . ($isSuspended 
                ? "<p>Your account was suspended due to a violation of our terms of service or for security reasons. Please contact support if you believe this is an error.</p>"
                : "<p>Welcome back! Your account has been re-activated and you can now log in and continue using all features.</p>") . "
            <p>Best regards,<br>The SparePlug Team</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: Promotion to Featured
     */
    public function sendPromotionNotification($to, $productTitle) {
        $subject = "Your Product is Featured!";
        $content = "
            <p>Congratulations!</p>
            <p>Your product <strong>'{$productTitle}'</strong> has been promoted to the <strong>Featured</strong> Parts section on our homepage.</p>
            <div class='badge badge-success' style='background: #F59E0B; color: #fff;'>Featured</div>
            <p>This means your part will get significantly more views and potential buyers!</p>";
        return $this->send($to, $subject, $content);
    }

    /**
     * Email: New Inquiry for Vendor
     */
    public function sendNewInquiry($to, $vendorName, $productTitle, $buyerName, $message) {
        $subject = "New Inquiry for: {$productTitle}";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>You have a new inquiry from <strong>{$buyerName}</strong> for your product <strong>'{$productTitle}'</strong>.</p>
            <div style='background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 4px solid " . ACCENT_COLOR . ";'>
                <p style='font-style: italic; margin: 0;'>\"{$message}\"</p>
            </div>
            <p>Check your dashboard to respond to this message!</p>";
        return $this->send($to, $subject, $content);
    }
    
    /**
     * Email: Admin Notification with type-based styling
     */
    public function sendAdminNotification($to, $name, $message, $type = 'info') {
        $subject = "New Notification from SparePlug Admin";
        
        $typeColors = [
            'info' => ['bg' => '#eff6ff', 'border' => '#2563eb', 'text' => '#1d4ed8', 'label' => 'Information'],
            'success' => ['bg' => '#ecfdf5', 'border' => '#059669', 'text' => '#047857', 'label' => 'Success'],
            'warning' => ['bg' => '#fffbeb', 'border' => '#d97706', 'text' => '#b45309', 'label' => 'Attention Required'],
            'error' => ['bg' => '#fef2f2', 'border' => '#dc2626', 'text' => '#b91c1c', 'label' => 'Action Required']
        ];
        
        $colors = $typeColors[$type] ?? $typeColors['info'];
        
        $content = "
            <p>Hello <strong>{$name}</strong>,</p>
            <p>You have received a new notification from the SparePlug administration team:</p>
            <div style='background: {$colors['bg']}; padding: 20px; border-radius: 12px; border-left: 4px solid {$colors['border']}; margin: 20px 0;'>
                <span style='display: inline-block; padding: 4px 12px; background: {$colors['border']}; color: white; border-radius: 99px; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px;'>{$colors['label']}</span>
                <p style='margin: 0; color: {$colors['text']}; font-size: 16px;'>{$message}</p>
            </div>
            <p>Log in to your dashboard for more details.</p>
            <p>Thank you,<br>The SparePlug Team</p>";
        return $this->send($to, $subject, $content);
    }
    
    /**
     * Email: Order Confirmation to Buyer
     */
    public function sendOrderConfirmation($to, $buyerName, $orderNumber, $amount, $vendorName) {
        $subject = "Order Confirmed - #{$orderNumber}";
        $content = "
            <p>Hello <strong>{$buyerName}</strong>,</p>
            <p>Your order has been placed successfully!</p>
            <div style='background: #f9fafb; padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0;'><strong>Order #:</strong> {$orderNumber}</p>
                <p style='margin: 0 0 10px 0;'><strong>Amount:</strong> " . number_format($amount) . " XAF</p>
                <p style='margin: 0;'><strong>Seller:</strong> {$vendorName}</p>
            </div>
            <p>Please complete your payment to proceed. Your money is held securely in escrow until you confirm receipt of your order.</p>
            <p>Thank you for shopping with SparePlug!</p>";
        return $this->send($to, $subject, $content);
    }
    
    /**
     * Email: New Order Notification to Vendor
     */
    public function sendNewOrderNotification($to, $vendorName, $orderNumber, $amount) {
        $subject = "New Order Received - #{$orderNumber}";
        $content = "
            <p>Hello <strong>{$vendorName}</strong>,</p>
            <p>You have received a new order!</p>
            <div style='background: #f9fafb; padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0;'><strong>Order #:</strong> {$orderNumber}</p>
                <p style='margin: 0;'><strong>Amount:</strong> " . number_format($amount) . " XAF</p>
            </div>
            <p>Log in to your vendor dashboard to view order details and proceed with fulfillment.</p>";
        return $this->send($to, $subject, $content);
    }
    
    /**
     * Email: New Order Alert to Admin
     */
    public function sendAdminNewOrder($to, $orderNumber, $amount, $vendorName) {
        $subject = "New Order on SparePlug - #{$orderNumber}";
        $content = "
            <p>Hello Admin,</p>
            <p>A new order has been placed on the platform.</p>
            <div style='background: #f9fafb; padding: 20px; border-radius: 12px; margin: 20px 0;'>
                <p style='margin: 0 0 10px 0;'><strong>Order #:</strong> {$orderNumber}</p>
                <p style='margin: 0 0 10px 0;'><strong>Amount:</strong> " . number_format($amount) . " XAF</p>
                <p style='margin: 0;'><strong>Vendor:</strong> {$vendorName}</p>
            </div>
            <p>Log in to admin dashboard to monitor.</p>";
        return $this->send($to, $subject, $content);
    }
}
