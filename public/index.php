<?php
// Entry point for the application

// Set error reporting in development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../utils/Security.php';

// Start secure session
$security = new Security();
$security->secureSessionStart();

// Redirect to signup page
header("Location: signup.php");
exit();
?>