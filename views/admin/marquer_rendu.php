<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['etat'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $etat = $_POST['etat'];
    $description = isset($_POST['description_dommages']) ? trim($_POST['description_dommages']) : '';

    // Validation de la description si l'état est endommagé
    if ($etat === 'endommage' && empty($description)) {
        $_SESSION['error'] = "Veuillez décrire les dommages du livre.";
        header('Location: borrow_status.php');
        exit;
    }

    // Récupérer l'id du livre et de l'étudiant
    $stmt = $pdo->prepare("SELECT book_id, etudiant_id FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $book_id = $row['book_id'];
    $etudiant_id = $row['etudiant_id'];

    if ($etat === 'bon') {
        // Statut rendu, stock +1
        $pdo->prepare("UPDATE reservations SET statut = 'rendu', etat_physique = 'bon', description_dommages = NULL WHERE id = ?")->execute([$reservation_id]);
        $pdo->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?")->execute([$book_id]);
        // Envoyer une notification de remerciement à l'étudiant
        $message = "Merci d'avoir rendu le livre en bon état !";
        $pdo->prepare("INSERT INTO notifications (etudiant_id, message, type) VALUES (?, ?, 'remerciement')")->execute([$etudiant_id, $message]);
    } elseif ($etat === 'endommage' && $description) {
        // Statut rendu, stock +1, etat_physique endommagé, description, ajout blacklist
        $pdo->prepare("UPDATE reservations SET statut = 'rendu', etat_physique = 'endommagé', description_dommages = ? WHERE id = ?")->execute([$description, $reservation_id]);
        $pdo->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?")->execute([$book_id]);
        // Ajouter à la blacklist
        $pdo->prepare("INSERT INTO liste_noire (user_id, motif, date_ajout, details) VALUES (?, 'livre_endommagé', NOW(), ?)")->execute([$etudiant_id, $description]);

        // Annuler toutes les réservations en attente de cet utilisateur et remettre la quantité à jour
        $stmt = $pdo->prepare("SELECT id, book_id FROM reservations WHERE etudiant_id = ? AND statut = 'en_attente'");
        $stmt->execute([$etudiant_id]);
        $pendingReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pendingReservations as $res) {
            // Annuler la réservation
            $pdo->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?")->execute([$res['id']]);
            // Remettre la quantité du livre à jour
            $pdo->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?")->execute([$res['book_id']]);
        }
    }
    // Redirection pour rafraîchir le tableau
    header('Location: /Projet-Cherradi/public/admin-dashboard.php?tab=statutlivres');
    exit;
}
?>