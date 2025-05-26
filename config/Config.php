<?php
// Application configuration
class Config {
    // Base URL of the application
    const BASE_URL = 'http://localhost/Projet-Cherradi/public';
    
    // SendGrid API key
    const SENDGRID_API_KEY = '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';
    const SENDGRID_FROM_EMAIL = 'aya.haiti@etu.uae.ac.ma';
    const SENDGRID_FROM_NAME = 'Système Gestion Bibliothèque';
    
    // Security settings
    const PASSWORD_MIN_LENGTH = 8;
    const TOKEN_EXPIRY_HOURS = 24;
    const SESSION_LIFETIME = 3600; // 1 hour
    
    // CSRF token settings
    const CSRF_TOKEN_NAME = 'csrf_token';
    const CSRF_TOKEN_LENGTH = 32;
}
?>