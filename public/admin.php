<?php
session_start();
require_once __DIR__ . '/../controllers/AdminController.php';

// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /Projet-Cherradi/login.php');
    exit();
}

$adminController = new AdminController();

// Gestion des actions
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'editUser':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $adminController->editUser($id);
        } else {
            $_SESSION['error'] = "ID de l'utilisateur non spécifié.";
            header('Location: /Projet-Cherradi/views/admin/dashboard.php');
            exit();
        }
        break;
        
    default:
        header('Location: /Projet-Cherradi/views/admin/dashboard.php');
        exit();
}
?> 