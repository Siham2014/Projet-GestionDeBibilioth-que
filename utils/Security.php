<?php
require_once __DIR__ . '/../config/Config.php';

class Security {
    // Start secure session
    public function secureSessionStart() {
        // Set secure session parameters
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_strict_mode', 1);
        
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => Config::SESSION_LIFETIME,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        session_start();
        
        // Regenerate session ID if it's older than 30 minutes
        if (isset($_SESSION['created']) && (time() - $_SESSION['created'] > 1800)) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        } else if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        }
    }
    
    // Generate random token
    public function generateToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    // Create CSRF token
    public function createCsrfToken() {
        $token = $this->generateToken(Config::CSRF_TOKEN_LENGTH);
        $_SESSION[Config::CSRF_TOKEN_NAME] = $token;
        return $token;
    }
    
    // Verify CSRF token
    public function verifyCsrfToken($token) {
        if (empty($_SESSION[Config::CSRF_TOKEN_NAME]) || empty($token)) {
            return false;
        }
        
        $result = hash_equals($_SESSION[Config::CSRF_TOKEN_NAME], $token);
        
        // Generate a new token for the next request
        $this->createCsrfToken();
        
        return $result;
    }
    
    // Sanitize input
    public function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeInput($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    // Check if user has specific role
    public function hasRole($requiredRole) {
        return $this->isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === $requiredRole;
    }
}
?>