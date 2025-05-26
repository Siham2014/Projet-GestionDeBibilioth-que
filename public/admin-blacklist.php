<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

// Gestion de la levée de sanction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id']) && $_POST['action'] === 'lever') {
    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("UPDATE liste_noire SET levee_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Sanction levée avec succès.";
    header("Location: admin-blacklist.php");
    exit;
}

// Gestion de la recherche
$search = $_GET['search'] ?? '';
$params = [];
$sql = "SELECT ln.id, ln.user_id, e.nom, e.prenom, ln.motif, ln.date_ajout, ln.details
        FROM liste_noire ln
        JOIN etudiant e ON ln.user_id = e.id
        WHERE ln.levee_at IS NULL";

if ($search !== '') {
    $sql .= " AND (e.nom LIKE :search OR ln.user_id = :idsearch)";
    $params[':search'] = "%$search%";
    $params[':idsearch'] = $search;
}
$sql .= " ORDER BY ln.date_ajout DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$blacklist = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inclusion du header/layout admin
require_once __DIR__ . '/../views/layouts/header.php';
?>

<div class="container mt-5">
    <h2>Liste noire des utilisateurs</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <form method="get" class="mb-3 d-flex" style="max-width:500px;">
        <input type="text" name="search" placeholder="Recherche par nom ou ID" class="form-control me-2" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary me-2">Rechercher</button>
        <a href="admin-blacklist.php" class="btn btn-secondary">Rafraîchir</a>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID utilisateur</th>
                <th>Nom utilisateur</th>
                <th>Motif</th>
                <th>Date d'ajout</th>
                <th>Détails</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($blacklist)): ?>
            <tr><td colspan="6" class="text-center">Aucun utilisateur dans la  noire.</td></tr>
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

<?php require_once __DIR__ . '/../views/layouts/footer.php'; ?> 