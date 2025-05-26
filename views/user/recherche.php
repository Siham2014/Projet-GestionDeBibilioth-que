<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

// Vérification de l'authentification
$etudiant_id = $_SESSION['user_id'] ?? null;
if (!$etudiant_id) {
    header('Location: /Projet-Cherradi/public/login.php');
    exit;
}

// Gestion de la recherche
$livres = [];
$critere = $_GET['critere'] ?? 'title';
$valeur = trim($_GET['valeur'] ?? '');

$champsAutorises = ['isbn', 'title', 'author', 'filiere'];
$critere = in_array($critere, $champsAutorises) ? $critere : 'title';

if ($valeur !== '') {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE $critere LIKE ?");
    $stmt->execute(["%$valeur%"]);
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Affichage de tous les livres si pas de recherche
    $stmt = $pdo->query("SELECT * FROM books");
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Configuration pour le layout
$activePage = 'recherche';
$title = 'Recherche - BiblioTech';

// Début du contenu
ob_start();
?>
<div class="container-fluid px-4">
    <!-- Barre de recherche améliorée -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-search me-2"></i>Rechercher un livre</h5>
            <form method="get" action="recherche.php" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="critere" class="form-label">Critère de recherche</label>
                    <select name="critere" id="critere" class="form-select">
                        <option value="title" <?= $critere === 'title' ? 'selected' : '' ?>>Titre</option>
                        <option value="author" <?= $critere === 'author' ? 'selected' : '' ?>>Auteur</option>
                        <option value="isbn" <?= $critere === 'isbn' ? 'selected' : '' ?>>ISBN</option>
                        <option value="filiere" <?= $critere === 'filiere' ? 'selected' : '' ?>>Filière</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="valeur" class="form-label">Terme à rechercher</label>
                    <input type="text" name="valeur" id="valeur" class="form-control" 
                           placeholder="Entrez votre recherche..." 
                           value="<?= htmlspecialchars($valeur) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de recherche -->
    <div class="row">
        <?php if (empty($livres)): ?>
            <div class="col-12">
                <div class="alert alert-warning shadow-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Aucun livre ne correspond à votre recherche.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($livres as $livre): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="position-relative">
                            <img src="/Projet-Cherradi/public/static/images/books/<?= htmlspecialchars($livre['image']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($livre['title']) ?>" 
                                 style="height: 250px; object-fit: cover;">
                            <?php if ($livre['available_quantity'] > 0): ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-success">
                                    <?= $livre['available_quantity'] ?> disponible(s)
                                </span>
                            <?php else: ?>
                                <span class="position-absolute top-0 end-0 m-2 badge bg-danger">Indisponible</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($livre['title']) ?></h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-person me-1"></i> <?= htmlspecialchars($livre['author']) ?>
                            </p>
                            <p class="card-text small mb-2">
                                <i class="bi bi-upc me-1"></i> <?= htmlspecialchars($livre['isbn']) ?>
                            </p>
                            <?php if (!empty($livre['filiere'])): ?>
                                <span class="badge bg-info mb-3"><?= htmlspecialchars($livre['filiere']) ?></span>
                            <?php endif; ?>
                            
                            <div class="mt-auto">
                                <form method="post" action="reserver.php">
                                    <input type="hidden" name="book_id" value="<?= $livre['id'] ?>">
                                    <button type="submit" class="btn btn-primary w-100" 
                                        <?= $livre['available_quantity'] <= 0 ? 'disabled' : '' ?>>
                                        <i class="bi bi-bookmark-plus me-2"></i>Réserver
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();

// Top bar personnalisée
ob_start();
?>
<div class="top-bar">
    <div>
        <h3>Recherche de livres</h3>
        <p class="text-muted">Trouvez et réservez les livres disponibles</p>
    </div>
    <div class="text-muted small">
        <?= count($livres) ?> livre(s) trouvé(s)
    </div>
</div>
<?php
$topBar = ob_get_clean();

require_once __DIR__ . '/../layouts/layout_user.php';
?>