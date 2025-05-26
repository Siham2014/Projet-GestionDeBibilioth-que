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

// Router simple pour les actions POST
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'reserver':
        $userController->reserverLivre();
        break;
    case 'update_profil':
        $userController->updateProfil();
        break;
    case 'soumettre_reclamation':
        $userController->soumettreReclamation();
        break;
    default:
        header('Location: /user/dashboard.php');
        exit;
}