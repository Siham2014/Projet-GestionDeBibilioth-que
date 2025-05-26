<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/UserController.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$db = new Database();
$userController = new UserController($db->getConnection());

// Router simple
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        $userController->dashboard();
        break;
    case 'recherche':
        $userController->rechercherLivres();
        break;
    case 'profil':
        $userController->profil();
        break;
    case 'reclamation':
        $userController->afficherReclamation();
        break;
    default:
        header('Location: /user/dashboard.php');
        exit;
}