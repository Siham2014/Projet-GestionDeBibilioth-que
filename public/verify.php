<?php
require_once __DIR__ . '/../controllers/AuthController.php';

// Verify account
$authController = new AuthController();
$result = $authController->verifyAccount();

// Redirect to login
header("Location: login.php");
exit();
?>