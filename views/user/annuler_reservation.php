<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

$etudiant_id = $_SESSION['user_id'] ?? null;
$reservation_id = $_POST['reservation_id'] ?? null;

if (!$etudiant_id || !$reservation_id) {
    $_SESSION['error'] = "Erreur lors de l'annulation de la réservation.";
    header('Location: dashboard.php');
    exit;
}

// Vérifier que la réservation appartient à l'utilisateur et est en attente
$stmt = $pdo->prepare('SELECT * FROM reservations WHERE id = ? AND etudiant_id = ? AND statut = "en_attente"');
$stmt->execute([$reservation_id, $etudiant_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    $_SESSION['error'] = "Impossible d'annuler cette réservation.";
    header('Location: dashboard.php');
    exit;
}

// Supprimer la réservation
$stmt = $pdo->prepare('DELETE FROM reservations WHERE id = ?');
$stmt->execute([$reservation_id]);

// Réajuster la quantité du livre
$stmt = $pdo->prepare('UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?');
$stmt->execute([$reservation['book_id']]);

$_SESSION['success'] = "Réservation annulée avec succès.";
header('Location: dashboard.php');
exit; 