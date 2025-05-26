<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

// Gestion de la levée de sanction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id']) && $_POST['action'] === 'lever') {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("UPDATE liste_noire SET levee_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Sanction levée avec succès.";
    header("Location: blacklist.php");
    exit;
}

// Recherche
$search = $_GET['search'] ?? '';
$params = [];
$sql = "SELECT ln.id, ln.user_id, e.nom, e.prenom, ln.motif, ln.date_ajout, ln.details
        FROM liste_noire ln
        JOIN etudiant e ON ln.user_id = e.id
        WHERE ln.levee_at IS NULL";
if ($search !== '') {
    $sql .= " AND (e.nom LIKE :search OR e.prenom LIKE :search OR ln.user_id = :idsearch)";
    $params[':search'] = "%$search%";
    $params[':idsearch'] = $search;
}
$sql .= " ORDER BY ln.date_ajout DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$blacklist = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-5">
    <h2>Liste Noire</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="get" class="d-flex flex-grow-1" style="max-width:400px;">
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher un utilisateur…" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
        <a href="blacklist.php" class="btn btn-secondary ms-2">Rafraîchir</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Motif</th>
                    <th>Date d'ajout</th>
                    <th>Détails</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($blacklist)): ?>
                <tr><td colspan="6" class="text-center">Aucun utilisateur dans la liste noire.</td></tr>
            <?php else: ?>
                <?php foreach ($blacklist as $row): ?>
                    <tr>
                        <td><?= $row['user_id'] ?></td>
                        <td><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></td>
                        <td><?= $row['motif'] === 'livre_perdu' ? 'Livre perdu' : 'Livre endommagé' ?></td>
                        <td><?= $row['date_ajout'] ?></td>
                        <td><?= nl2br(htmlspecialchars($row['details'])) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="action" value="lever">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Lever la sanction ?');" title="Lever sanction">✖</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 