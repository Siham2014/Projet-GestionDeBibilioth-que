<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

$etudiant_id = $_SESSION['user_id'] ?? null;
if (!$etudiant_id) {
    header('Location: /Projet-Cherradi/public/login.php');
    exit;
}

// Récupérer toutes les réservations de l'utilisateur
$stmt = $pdo->prepare('SELECT r.*, b.title AS titre, b.isbn, b.image 
    FROM reservations r 
    JOIN books b ON r.book_id = b.id 
    WHERE r.etudiant_id = ? 
    ORDER BY r.date_reservation DESC');
$stmt->execute([$etudiant_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$activePage = 'reservations';
$title = 'Mes Réservations - BiblioTech';

// Début du contenu à inclure dans le layout
ob_start();
?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title">Mes réservations</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Livre</th>
                            <th>Date de réservation</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reservations)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4">Aucune réservation trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($reservation['image'])): ?>
                                                <img src="<?= htmlspecialchars($reservation['image']) ?>" alt="Couverture du livre" class="img-thumbnail me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($reservation['titre']) ?></strong><br>
                                                <small class="text-muted">ISBN: <?= htmlspecialchars($reservation['isbn']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($reservation['date_reservation'])) ?></td>
                                    <td>
                                        <?php
                                        switch ($reservation['statut']) {
                                            case 'acceptee':
                                                echo '<span class="badge bg-success">Acceptée</span>';
                                                break;
                                            case 'refusee':
                                                echo '<span class="badge bg-danger">Refusée</span>';
                                                break;
                                            case 'rendu':
                                                echo '<span class="badge bg-info">Rendu</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-warning text-dark">En attente</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="dashboard.php" class="btn btn-primary mt-3">
                <i class="bi bi-arrow-left"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

// Top bar personnalisée
ob_start();
?>
<div class="top-bar">
    <div>
        <h3>Mes Réservations</h3>
        <p class="text-muted">Consultez l'état de vos réservations</p>
    </div>
</div>
<?php
$topBar = ob_get_clean();

require_once __DIR__ . '/../layouts/layout_user.php';
?>