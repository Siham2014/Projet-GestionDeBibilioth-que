<?php
session_start();

// 1. Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

// 2. Récupération de l'étudiant connecté
$etudiant_id = $_SESSION['user_id'] ?? null;
$etudiant = null;
if ($etudiant_id) {
    $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = ?');
    $stmt->execute([$etudiant_id]);
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$etudiant || $etudiant['role'] !== 'etudiant') {
    header('Location: /Projet-Cherradi/public/login.php');
    exit;
}

// Vérifier si l'utilisateur est blacklisté
$isBlacklisted = false;
if ($etudiant_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM liste_noire WHERE user_id = ?");
    $stmt->execute([$etudiant_id]);
    $isBlacklisted = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
}

// 3. Récupération des réservations
$reservations_actives = [];
if ($etudiant_id) {
    $stmt = $pdo->prepare('SELECT r.*, b.title AS titre, b.isbn, b.image 
        FROM reservations r 
        JOIN books b ON r.book_id = b.id 
        WHERE r.etudiant_id = ? AND (r.statut = "en_attente" OR r.statut = "acceptee")
        ORDER BY r.date_reservation DESC');
    $stmt->execute([$etudiant_id]);
    $reservations_actives = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 4. Récupération des notifications
$notifications = [];
if ($etudiant_id) {
    $stmt = $pdo->prepare('SELECT * FROM notifications WHERE etudiant_id = ?');
    $stmt->execute([$etudiant_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 5. Détection de retard
$hasRetard = false;
foreach ($reservations_actives as $reservation) {
    if (strtotime($reservation['date_limite']) < time()) {
        $hasRetard = true;
        break;
    }
}

// Récupérer les livres disponibles
$stmt = $pdo->query("SELECT * FROM books WHERE available_quantity > 0");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

setlocale(LC_TIME, 'fr_FR.UTF-8');

$nb_actives = count($reservations_actives);

// Variables pour le layout
$title = "Tableau de bord";
$activePage = "dashboard";
$topBar = '<div class="top-bar">
    <div>
        <h3>Bonjour, ' . htmlspecialchars($etudiant['username']) . ' 👋</h3>
        <div class="text-muted">' . strftime('%A %d %B %Y') . '</div>
    </div>
    <div class="d-flex align-items-center">
        <span class="me-3">' . htmlspecialchars($etudiant['nom']) . '</span>
        <img src="/Projet-Cherradi/public/uploads/profils/' . htmlspecialchars($etudiant['photoprofil'] ?? 'default.png') . '"
            alt="Photo de profil"
            style="width:38px;height:38px;object-fit:cover;border-radius:50%;margin-left:10px;">
        <a href="notifications.php" class="ms-3 position-relative">
            <i class="bi bi-bell" style="font-size: 1.7rem;"></i>';
$nbNotif = 0;
foreach ($notifications as $notif) {
    if (!$notif['lu'])
        $nbNotif++;
}
if ($nbNotif > 0) {
    $topBar .= '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'
        . $nbNotif . '</span>';
}
$topBar .= '</a>
    </div>
</div>';

ob_start();
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success'];
        unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if ($isBlacklisted): ?>
    <div class="alert alert-danger">
        Vous êtes actuellement bloqué(e) pour réservation suite à un incident. Veuillez contacter
        l'administration.
    </div>
<?php endif; ?>

<?php if ($hasRetard): ?>
    <div class="alert alert-danger">
        ⛔ Vous êtes en retard de retour de livre(s)
    </div>
<?php endif; ?>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Réservations Actives</h5>
                <h1><?php echo $nb_actives; ?></h1>
                <p><?php echo (3 - $nb_actives); ?> réservations restantes</p>
            </div>
        </div>
    </div>
</div>

<!-- Mes réservations -->
<h4 class="mb-3">Mes réservations</h4>
<div class="row">
    <?php if (empty($reservations_actives)): ?>
        <div class="col-12">
            <div class="alert alert-info">Vous n'avez aucune réservation active.</div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Date de réservation</th>
                        <th>Date limite</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations_actives as $reservation): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/Projet-Cherradi/public/static/images/books/<?php echo htmlspecialchars($reservation['image']); ?>"
                                        alt="<?php echo htmlspecialchars($reservation['titre']); ?>"
                                        style="width: 50px; height: 70px; object-fit: cover; margin-right: 15px; border-radius: 4px;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($reservation['titre']); ?></strong>
                                        <br>
                                        <small class="text-muted">ISBN:
                                            <?php echo htmlspecialchars($reservation['isbn']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($reservation['date_reservation'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($reservation['date_limite'])); ?></td>
                            <td>
                                <?php
                                switch ($reservation['statut']) {
                                    case 'en_attente':
                                        echo '<span class="badge bg-warning text-dark">En attente</span>';
                                        break;
                                    case 'acceptee':
                                        echo '<span class="badge bg-success">Acceptée</span>';
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($reservation['statut'] === 'en_attente'): ?>
                                    <form method="post" action="annuler_reservation.php" style="display:inline;">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
                                            <i class="bi bi-x-circle"></i> Annuler
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Livres disponibles -->
<h4 class="mb-3 mt-5">Livres disponibles</h4>
<div class="row">
    <?php foreach ($books as $book): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="/Projet-Cherradi/public/static/images/books/<?php echo htmlspecialchars($book['image']); ?>"
                    class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>"
                    style="height: 250px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                    <p class="card-text">Auteur : <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="card-text"><?php echo htmlspecialchars($book['description']); ?></p>
                    <form method="post" action="reserver.php">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="btn btn-primary">Réserver</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();

// Ajout des styles responsifs
$styles = '
<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #34495e;
        --accent-color: #3498db;
        --text-color: #2c3e50;
        --light-bg: #f8f9fa;
        --border-color: #e9ecef;
    }

    body {
        background-color: var(--light-bg);
        color: var(--text-color);
        font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
        overflow-x: hidden;
    }

    .top-bar {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-img-top {
        height: 200px;
        object-fit: cover;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .table-responsive {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 20px;
        margin: 20px 0;
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
    }

    .table thead th {
        background-color: var(--light-bg);
        border-bottom: 2px solid var(--border-color);
        color: var(--text-color);
        font-weight: 600;
        padding: 15px;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
    }

    @media (max-width: 1200px) {
        .card-img-top {
            height: 180px;
        }
    }

    @media (max-width: 992px) {
        .top-bar {
            padding: 15px;
        }

        .card-img-top {
            height: 160px;
        }

        .table-responsive {
            padding: 15px;
        }
    }

    @media (max-width: 768px) {
        .top-bar {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px;
        }

        .top-bar > div:last-child {
            width: 100%;
            justify-content: space-between;
        }

        .card-img-top {
            height: 200px;
        }

        .table-responsive {
            margin: 10px 0;
            padding: 10px;
        }

        .table thead th {
            font-size: 0.9rem;
            padding: 10px;
        }

        .table tbody td {
            font-size: 0.9rem;
            padding: 10px;
        }

        .btn {
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        h4 {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 576px) {
        .top-bar {
            padding: 10px;
        }

        .top-bar h3 {
            font-size: 1.2rem;
        }

        .card-img-top {
            height: 180px;
        }

        .table-responsive {
            padding: 8px;
        }

        .table thead th {
            font-size: 0.85rem;
            padding: 8px;
        }

        .table tbody td {
            font-size: 0.85rem;
            padding: 8px;
        }

        .btn {
            padding: 5px 10px;
            font-size: 0.85rem;
        }

        .badge {
            padding: 4px 8px;
            font-size: 0.8rem;
        }

        h4 {
            font-size: 1.1rem;
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-size: 1.1rem;
        }

        .card-text {
            font-size: 0.9rem;
        }
    }
</style>
';

// Ajouter les styles au contenu
$content = $styles . $content;

require_once __DIR__ . '/../layouts/layout_user.php';