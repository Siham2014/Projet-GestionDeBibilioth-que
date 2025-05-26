<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

$etudiant_id = $_SESSION['user_id'] ?? null;
$book_id = $_POST['book_id'] ?? null;

// Vérification blacklist
if ($etudiant_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM liste_noire WHERE user_id = ?");
    $stmt->execute([$etudiant_id]);
    $isBlacklisted = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    if ($isBlacklisted) {
        $_SESSION['error'] = "Vous ne pouvez plus réserver de livres car vous êtes dans la liste noire. Veuillez contacter l'administration.";
        header('Location: dashboard.php');
        exit;
    }
}

// Vérification de la limite de réservations
if ($etudiant_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reservations WHERE etudiant_id = ? AND (statut = 'en_attente' OR statut = 'acceptee')");
    $stmt->execute([$etudiant_id]);
    $resCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    if ($resCount >= 3) {
        $_SESSION['error'] = "Vous avez déjà atteint la limite de 3 livres réservés. Veuillez annuler une réservation avant d'en ajouter une nouvelle.";
        header('Location: dashboard.php');
        exit;
    }
    // Vérification si le livre est déjà réservé par l'utilisateur
    if ($book_id) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM reservations WHERE etudiant_id = ? AND book_id = ? AND (statut = 'en_attente' OR statut = 'acceptee')");
        $stmt->execute([$etudiant_id, $book_id]);
        $alreadyReserved = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($alreadyReserved > 0) {
            $_SESSION['error'] = "Vous avez déjà réservé ce livre. Veuillez choisir un autre livre.";
            header('Location: dashboard.php');
            exit;
        }
    }
}

if ($etudiant_id && $book_id) {
    // Vérifier la disponibilité
    $stmt = $pdo->prepare("SELECT available_quantity FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book && $book['available_quantity'] > 0) {
        // Insérer la réservation
        $date_reservation = date('Y-m-d');
        $date_limite = date('Y-m-d', strtotime('+7 days'));
        $stmt = $pdo->prepare("INSERT INTO reservations (etudiant_id, book_id, date_reservation, date_limite) VALUES (?, ?, ?, ?)");
        $stmt->execute([$etudiant_id, $book_id, $date_reservation, $date_limite]);

        // Diminuer la quantité disponible
        $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?");
        $stmt->execute([$book_id]);

        $_SESSION['success'] = "Réservation effectuée avec succès !";
    } else {
        $_SESSION['error'] = "Ce livre n'est plus disponible.";
    }
} else {
    $_SESSION['error'] = "Erreur lors de la réservation.";
}

// Redirige vers le dashboard
header('Location: dashboard.php');
exit;
?>