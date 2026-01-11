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
     */
    public function sendProductNotification($to, $vendorName, $productTitle) {
        $subject = "Your Product is Live on SparePlug!";
        $content = "
            <p>Hello {$vendorName},</p>
            <p>Great news! Your product <strong>'{$productTitle}'</strong> has been successfully listed and is now visible to thousands of buyers.</p>
            <div class='badge badge-success'>Live & Active</div>
            <p>You can manage your listing from your vendor dashboard.</p>";
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
}
