<?php
session_start();

require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController();
$authController->resetPassword();

require_once __DIR__ . '/../views/layouts/header.php';
require_once __DIR__ . '/../views/auth/reset-password.php';
require_once __DIR__ . '/../views/layouts/footer.php'; 