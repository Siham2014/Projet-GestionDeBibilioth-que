<?php
session_start();

require_once __DIR__ . '/../controllers/AuthController.php';

$controller = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
}

require_once __DIR__ . '/../views/layouts/header.php';
require_once __DIR__ . '/../views/auth/login.php';
require_once __DIR__ . '/../views/layouts/footer.php'; 