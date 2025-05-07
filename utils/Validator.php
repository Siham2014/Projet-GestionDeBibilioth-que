<?php
require_once __DIR__ . '/../config/Config.php';

class Validator {
    // Validate required field
    public function validateRequired($value) {
        return isset($value) && trim($value) !== '';
    }
    
    // Validate string length
    public function validateLength($value, $min, $max) {
        $length = mb_strlen(trim($value), 'UTF-8');
        return $length >= $min && $length <= $max;
    }
    
    // Validate email
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validate phone number (accepts only 10 digits)
    public function validatePhone($phone) {
        return preg_match('/^\d{10}$/', $phone);
    }
    
    // Validate date (YYYY-MM-DD format)
    public function validateDate($date) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $dateTime = DateTime::createFromFormat('Y-m-d', $date);
            return $dateTime && $dateTime->format('Y-m-d') === $date;
        }
        return false;
    }
    
    // Validate username (letters, numbers, and underscores only)
    public function validateUsername($username) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $username);
    }
    
    // Validate password (min length, at least one uppercase, one lowercase, one number, one special character)
    public function validatePassword($password) {
        $minLength = Config::PASSWORD_MIN_LENGTH;
        
        // Check minimum length
        if (strlen($password) < $minLength) {
            return false;
        }
        
        // Check for at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // Check for at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // Check for at least one number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        // Check for at least one special character
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
}
?>