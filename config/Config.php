<?php
// Application configuration
class Config {
    // Base URL of the application
    const BASE_URL = 'http://localhost/library';
    
    // SendGrid API key
    const SENDGRID_API_KEY = 'YOUR_SENDGRID_API_KEY';
    const SENDGRID_FROM_EMAIL = 'library@example.com';
    const SENDGRID_FROM_NAME = 'Library Management System';
    
    // Security settings
    const PASSWORD_MIN_LENGTH = 8;
    const TOKEN_EXPIRY_HOURS = 24;
    const SESSION_LIFETIME = 3600; // 1 hour
    
    // CSRF token settings
    const CSRF_TOKEN_NAME = 'csrf_token';
    const CSRF_TOKEN_LENGTH = 32;
}
?>