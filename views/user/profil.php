<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

$etudiant_id = $_SESSION['user_id'] ?? null;
if (!$etudiant_id) {
    header('Location: /Projet-Cherradi/public/login.php');
    exit;
}

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = ?');
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

// Statistiques des réservations
$stmt = $pdo->prepare('SELECT statut, COUNT(*) as total FROM reservations WHERE etudiant_id = ? GROUP BY statut');
$stmt->execute([$etudiant_id]);
$stats = ['en_attente' => 0, 'acceptee' => 0, 'refusee' => 0];
$total = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $stats[$row['statut']] = $row['total'];
    $total += $row['total'];
}

// Variables pour le layout
$title = "Mon Profil";
$activePage = "profil";
$topBar = '<div class="top-bar">
    <div>
        <h3>Mon Profil</h3>
        <div class="text-muted">Bienvenue, ' . htmlspecialchars($etudiant['username']) . '</div>
    </div>
    <div class="d-flex align-items-center">
        <span class="me-3">' . htmlspecialchars($etudiant['nom']) . '</span>
        <img src="/Projet-Cherradi/public/uploads/profils/' . htmlspecialchars($etudiant['photoprofil'] ?? 'default.png') . '" alt="Photo de profil" style="width:38px;height:38px;object-fit:cover;border-radius:50%;margin-left:10px;">
    </div>
</div>';

ob_start();
?>
<div class="row align-items-stretch">
    <!-- Colonne infos personnelles -->
    <div class="col-md-7 d-flex">
        <div class="card mb-4 h-100 w-100">
            <div class="card-body d-flex align-items-center">
                <img src="/Projet-Cherradi/public/uploads/profils/<?php echo htmlspecialchars($etudiant['photoprofil'] ?? 'default.png'); ?>"
                    class="rounded-circle me-4" width="100" height="100" alt="photoprofil">
                <div style="width:100%">
                    <h4><?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></h4>
                    <div class="text-muted mb-3">Membre depuis
                        <?php echo date('d/m/Y', strtotime($etudiant['date_inscription'])); ?>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="fw-bold">Nom</div>
                            <div><?php echo htmlspecialchars($etudiant['nom']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold">Prénom</div>
                            <div><?php echo htmlspecialchars($etudiant['prenom']); ?></div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div class="fw-bold">Email</div>
                        <div><?php echo htmlspecialchars($etudiant['email']); ?></div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="fw-bold">Nom d\'utilisateur</div>
                            <div><?php echo htmlspecialchars($etudiant['username']); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold">Téléphone</div>
                            <div><?php echo htmlspecialchars($etudiant['telephone']); ?></div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <div class="fw-bold">Date de naissance</div>
                        <div><?php echo htmlspecialchars($etudiant['date_naissance']); ?></div>
                    </div>
                    <a href="/Projet-Cherradi/views/user/edit_profil.php" class="btn btn-yellow-custom mt-3">Modifier mon profil</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Colonne statistiques -->
    <div class="col-md-5 d-flex">
        <div class="card mb-4 h-100 w-100">
            <div class="card-body">
                <h5 class="card-title mb-3">Statistiques</h5>
                <div class="mb-3 text-muted">Vos statistiques d\'emprunt</div>
                <div class="mb-3">
                    <strong>Réservations</strong>
                    <div class="progress mb-1">
                        <div class="progress-bar bg-primary" role="progressbar"
                            style="width: <?php echo ($total > 0 ? min(100, ($total / 3) * 100) : 0); ?>%;"
                            aria-valuenow="<?php echo $total; ?>" aria-valuemin="0" aria-valuemax="3"></div>
                    </div>
                    <div class="text-end small"><?php echo $total; ?>/3 réservations</div>
                </div>
                <div class="mb-2">En attente : <strong><?php echo $stats['en_attente']; ?></strong></div>
                <div class="mb-2">Approuvées : <strong><?php echo $stats['acceptee']; ?></strong></div>
                <div class="mb-2">Rejetées : <strong><?php echo $stats['refusee']; ?></strong></div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout_user.php';