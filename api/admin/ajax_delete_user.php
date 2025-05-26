<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/Etudiant.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit();
}

if (isset($_POST['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    $etudiant = new Etudiant($db);

    if ($etudiant->delete($_POST['id'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'ID manquant']);
}
exit(); 