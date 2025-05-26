<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/AuthController.php';

$db = new Database();
$authController = new AuthController($db->getConnection());

// Router simple pour l'authentification
$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        $authController->login();
        break;
    case 'logout':
        $authController->logout();
        break;
    default:
        header('Location: /auth/login.php');
        exit;
}