<?php
require_once 'config/database.php';
require_once 'Mailer.php';

class Auth {
    private $db;
    private $mailer;
    
    public function __construct() {
        global $db;
        $this->db = $db;
        $this->mailer = new Mailer();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    }
    
    // Generate random token
    public function generateToken($length = 64) {
        return bin2hex(random_bytes($length / 2));
    }
    
    // Generate 6-digit code
    public function generateCode($length = 6) {
        return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
    
    // Register new user
    public function register($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Generate verification tokens
        if (empty($data['email_verification_token'])) {
            $data['email_verification_token'] = $this->generateToken();
        }
        if (empty($data['verification_token'])) {
            $data['verification_token'] = bin2hex(random_bytes(32));
        }
        
        // Set default values
        $data['is_verified'] = false;
        $data['two_factor_enabled'] = false;
        
        // Insert user
        $userId = $this->db->insert('users', $data);
        
        // Create email verification record
        $this->createEmailVerification($userId, $data['email']);
        
        return $userId;
    }
    
    // Create email verification
    public function createEmailVerification($userId, $email) {
        $token = $this->generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        return $this->db->insert('email_verifications', [
            'user_id' => $userId,
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
            'verified' => false
        ]);
    }
    
    // Create phone verification
    public function createPhoneVerification($userId, $phone) {
        $code = $this->generateCode(6);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Delete previous unverified codes
        $this->db->delete('phone_verifications', 'user_id = ? AND verified = 0', [$userId]);
        
        return $this->db->insert('phone_verifications', [
            'user_id' => $userId,
            'phone' => $phone,
            'code' => $code,
            'expires_at' => $expiresAt,
            'verified' => false,
            'attempts' => 0
        ]);
    }
    
    // Send email verification
    public function sendEmailVerification($userId) {
        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        $verification = $this->db->fetch(
            "SELECT * FROM email_verifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );
        
        if ($verification && $user) {
            $verifyLink = BASE_URL . "/index.php?page=verify-email&token={$verification['token']}";
            
            $name = $user['first_name'] . ' ' . $user['last_name'];
            return $this->mailer->sendVerification($user['email'], $name, $verifyLink);
        }
        
        return false;
    }
    
    // Send SMS verification (mock for development)
    public function sendSMSVerification($userId, $phone) {
        $verification = $this->db->fetch(
            "SELECT * FROM phone_verifications WHERE user_id = ? AND verified = 0 ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );
        
        if ($verification) {
            // TODO: Integrate with SMS service (Twilio, AWS SNS, etc.)
            // For development, log the code
            error_log("SMS verification code for {$phone}: {$verification['code']}");
            
            return true;
        }
        
        return false;
    }
    
    // Verify email
    public function verifyEmail($token) {
        $verification = $this->db->fetch(
            "SELECT * FROM email_verifications WHERE token = ? AND expires_at > NOW()",
            [$token]
        );
        
        if ($verification && !$verification['verified']) {
            // Update verification status
            $this->db->update('email_verifications',
                ['verified' => true, 'verified_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$verification['id']]
            );
            
            // Update user
            $this->db->update('users',
                ['is_verified' => true, 'email_verification_token' => null],
                'id = ?',
                [$verification['user_id']]
            );
            
            return true;
        }
        
        return false;
    }
    
    // Verify phone
    public function verifyPhone($userId, $code) {
        $verification = $this->db->fetch(
            "SELECT * FROM phone_verifications WHERE user_id = ? AND code = ? AND expires_at > NOW() AND verified = 0",
            [$userId, $code]
        );
        
        if ($verification) {
            // Check attempts
            if ($verification['attempts'] >= 3) {
                return ['success' => false, 'message' => 'Too many failed attempts'];
            }
            
            // Increment attempts
            $this->db->query(
                "UPDATE phone_verifications SET attempts = attempts + 1 WHERE id = ?",
                [$verification['id']]
            );
            
            // Verify
            $this->db->update('phone_verifications',
                ['verified' => true, 'verified_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$verification['id']]
            );
            
            $this->db->update('users',
                ['phone_verified' => true],
                'id = ?',
                [$userId]
            );
            
            return ['success' => true, 'message' => 'Phone verified'];
        }
        
        return ['success' => false, 'message' => 'Invalid or expired code'];
    }
    
    // Login with email or phone
    public function login($identifier, $password, $rememberMe = false) {
        // Try email first
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$identifier]
        );
        
        // If not found, try phone
        if (!$user) {
            $user = $this->db->fetch(
                "SELECT * FROM users WHERE phone = ? AND status = 'active'",
                [$identifier]
            );
        }
        
        if ($user && password_verify($password, $user['password'])) {
            // Check 2FA
            if (isset($user['two_factor_enabled']) && $user['two_factor_enabled']) {
                return ['requires_2fa' => true, 'user_id' => $user['id']];
            }
            
            // Update last login
            $this->db->update('users',
                ['last_login' => date('Y-m-d H:i:s')],
                'id = ?',
                [$user['id']]
            );
            
            // Log login
            $this->logLogin($user['id'], $user['email'], true);
            
            // Set session
            $this->setUserSession($user);
            
            // Set remember me cookie if needed
            if ($rememberMe) {
                $this->setRememberMeCookie($user['id']);
            }
            
            return ['success' => true, 'user' => $user];
        } else {
            // Log failed login
            $this->logLogin(null, $identifier, false, 'Invalid credentials');
            
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }
    
    // Generate 2FA code
    public function generate2FACode($userId) {
        $code = $this->generateCode(6);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        // Delete previous codes
        $this->db->delete('two_factor_codes', 'user_id = ?', [$userId]);
        
        // Insert new code
        $this->db->insert('two_factor_codes', [
            'user_id' => $userId,
            'code' => $code,
            'expires_at' => $expiresAt,
            'used' => false
        ]);
        
        // TODO: Send via email/SMS
        error_log("2FA code for user {$userId}: {$code}");
        
        return $code;
    }
    
    // Verify 2FA
    public function verify2FA($userId, $code) {
        $codeRecord = $this->db->fetch(
            "SELECT * FROM two_factor_codes WHERE user_id = ? AND code = ? AND expires_at > NOW() AND used = 0",
            [$userId, $code]
        );
        
        if ($codeRecord) {
            // Mark as used
            $this->db->update('two_factor_codes', ['used' => true], 'id = ?', [$codeRecord['id']]);
            
            // Get user and set session
            $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
            $this->setUserSession($user);
            
            // Log login
            $this->logLogin($user['id'], $user['email'], true, null, true);
            
            return true;
        }
        
        return false;
    }
    
    // Forgot password
    public function forgotPassword($email) {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user) {
            $token = $this->generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete previous tokens
            $this->db->query("DELETE FROM password_resets WHERE user_id = ?", [$user['id']]);
            
            // Create new token
            $this->db->insert('password_resets', [
                'user_id' => $user['id'],
                'token' => $token,
                'expires_at' => $expiresAt,
                'used' => false
            ]);
            
            // Send email with reset link
            $resetLink = BASE_URL . "/index.php?page=reset-password&token={$token}";
            return $this->mailer->sendPasswordReset($user['email'], $resetLink);
        }
        
        return false;
    }
    
    // Reset password
    public function resetPassword($token, $newPassword) {
        $reset = $this->db->fetch(
            "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0",
            [$token]
        );
        
        if ($reset) {
            // Mark as used
            $this->db->update('password_resets', ['used' => true], 'id = ?', [$reset['id']]);
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->update('users',
                [
                    'password' => $hashedPassword,
                    'last_password_change' => date('Y-m-d H:i:s')
                ],
                'id = ?',
                [$reset['user_id']]
            );
            
            return true;
        }
        
        return false;
    }
    
    // Helper methods
    private function setUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_verified'] = $user['is_verified'];
        $_SESSION['phone_verified'] = $user['phone_verified'] ?? false;
    }
    
    private function logLogin($userId, $email, $success, $reason = null, $twoFactorUsed = false) {
        $this->db->insert('login_logs', [
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'success' => (int)$success,
            'failure_reason' => $reason,
            'two_factor_used' => (int)$twoFactorUsed
        ]);
    }
    
    private function setRememberMeCookie($userId) {
        $token = $this->generateToken(128);
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        setcookie('remember_token', $token, $expires, '/', '', false, true);
        
        // TODO: Store in database
        error_log("Remember me token for user {$userId}: {$token}");
    }
}

