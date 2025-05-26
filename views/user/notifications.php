<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

$etudiant_id = $_SESSION['user_id'] ?? null;
if (!$etudiant_id) {
    header('Location: /Projet-Cherradi/public/login.php');
    exit;
}

// Marquer toutes les notifications comme lues
$pdo->prepare('UPDATE notifications SET lu = 1 WHERE etudiant_id = ?')->execute([$etudiant_id]);

// Pagination
$perPage = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Compter le total
$stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE etudiant_id = ?');
$stmt->execute([$etudiant_id]);
$totalNotif = $stmt->fetchColumn();
$totalPages = ceil($totalNotif / $perPage);

// Récupérer les notifications paginées
$stmt = $pdo->prepare('SELECT * FROM notifications WHERE etudiant_id = ? ORDER BY id DESC LIMIT ? OFFSET ?');
$stmt->bindValue(1, $etudiant_id, PDO::PARAM_INT);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Réservations en attente
$stmt = $pdo->prepare('SELECT r.*, b.title AS titre, b.isbn, b.image FROM reservations r JOIN books b ON r.book_id = b.id WHERE r.etudiant_id = ? AND r.statut = "en_attente"');
$stmt->execute([$etudiant_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Historique (acceptées ou refusées)
$stmt = $pdo->prepare('SELECT r.*, b.title AS titre, b.isbn, b.image FROM reservations r JOIN books b ON r.book_id = b.id WHERE r.etudiant_id = ? AND r.statut IN ("acceptee", "refusee")');
$stmt->execute([$etudiant_id]);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-5">
    <h2>Mes notifications</h2>
    <ul class="list-group mt-4">
        <?php if (empty($notifications)): ?>
            <li class="list-group-item">Aucune notification.</li>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <li class="list-group-item<?php if (!$notif['lu'])
                    echo ' list-group-item-warning'; ?>">
                    <?php echo htmlspecialchars($notif['message']); ?>
                    <span class="badge bg-secondary float-end"><?php echo htmlspecialchars($notif['type']); ?></span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
    <!-- Pagination -->
    <nav aria-label="Pagination notifications" class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item<?php if ($i == $page)
                    echo ' active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <a href="dashboard.php" class="btn btn-primary mt-4">Retour au tableau de bord</a>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>