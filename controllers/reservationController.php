<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../models/Book.php';

$db = new Database();
$pdo = $db->getConnection();

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$reservationModel = new Reservation($pdo);
$bookModel = new Book($pdo);

$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

if (!$action || !$id) {
    header('Location: /Projet-Cherradi/views/admin/dashboard.php');
    exit;
}

// Récupérer la réservation et l'utilisateur
$reservation = $reservationModel->getReservationById($id);
if (!$reservation) {
    $_SESSION['error'] = "Réservation introuvable.";
    header('Location: /Projet-Cherradi/views/admin/dashboard.php');
    exit;
}

$etudiant_id = $reservation['etudiant_id'];
$book_id = $reservation['book_id'];

if ($action === 'accept') {
    $reservationModel->updateStatus($id, 'acceptee');
    $reservationModel->addNotification($etudiant_id, "Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.", "acceptation");
    $_SESSION['success'] = "Réservation acceptée.";
} elseif ($action === 'reject') {
    $reservationModel->updateStatus($id, 'refusee');
    $reservationModel->addNotification($etudiant_id, "Votre réservation a été refusée par l'administrateur.", "refus");
    $reservationModel->incrementBookQuantity($book_id);
    $_SESSION['success'] = "Réservation refusée et quantité réajustée.";
} elseif ($action === 'return') {
    $etat_physique = $_POST['etat_physique'] ?? null;
    $description_dommages = $_POST['description_dommages'] ?? null;

    if ($etat_physique === null) {
        $_SESSION['error'] = "Veuillez spécifier l'état physique du livre.";
    } else {
        if ($reservationModel->markAsReturned($id, $etat_physique, $description_dommages)) {
            if ($bookModel->incrementQuantity($book_id)) {
                $_SESSION['success'] = "Livre marqué comme rendu avec succès.";
            } else {
                $_SESSION['error'] = "Livre marqué comme rendu, mais erreur lors de la mise à jour de la quantité du livre.";
            }
        } else {
            $_SESSION['error'] = "Erreur lors du marquage du livre comme rendu.";
        }
    }
}

header('Location: /Projet-Cherradi/views/admin/dashboard.php');
exit; 