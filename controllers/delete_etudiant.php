<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Etudiant.php';

// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /Projet-Cherradi/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    $etudiant = new Etudiant($db);

    if ($etudiant->delete($_GET['id'])) {
        $_SESSION['success'] = "L'étudiant a été supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'étudiant.";
    }
} else {
    $_SESSION['error'] = "ID de l'étudiant non spécifié.";
}

header('Location: /Projet-Cherradi/views/admin/dashboard.php');
exit();
