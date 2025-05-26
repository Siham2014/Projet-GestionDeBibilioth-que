<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

// Récupération des emprunts (réservations acceptées ou en cours)
$sql = "SELECT r.*, b.title AS livre, e.nom AS utilisateur, e.prenom, r.date_reservation, r.date_limite
        FROM reservations r
        JOIN books b ON r.book_id = b.id
        JOIN etudiant e ON r.etudiant_id = e.id
        WHERE r.statut IN ('acceptee', 'emprunte', 'en_retard', 'perdu', 'rendu')
        ORDER BY r.date_reservation DESC";
$stmt = $pdo->query($sql);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul automatique du statut
$now = new DateTime();
foreach ($emprunts as &$emprunt) {
    $date_limite = new DateTime($emprunt['date_limite']);
    $date_perdu = (clone $date_limite)->modify('+7 days');
    if (in_array($emprunt['statut'], ['acceptee', 'emprunte'])) {
        if ($now > $date_perdu) {
            $emprunt['statut'] = 'perdu';
        } elseif ($now > $date_limite) {
            $emprunt['statut'] = 'en_retard';
        } else {
            $emprunt['statut'] = 'emprunte';
        }
    }
}
unset($emprunt); // Bonnes pratiques

// Calcul des compteurs
$total = count($emprunts);
$empruntes = count(array_filter($emprunts, fn($e) => $e['statut'] === 'emprunte'));
$en_retard = count(array_filter($emprunts, fn($e) => $e['statut'] === 'en_retard'));
$perdus = count(array_filter($emprunts, fn($e) => $e['statut'] === 'perdu' ));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statut des livres</title>
    <link rel="stylesheet" href="/Projet-Cherradi/public/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Statut des livres</h2>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center" style="background:#f0f6ff;">
                <div class="card-body">
                    <h6 class="text-primary">Total</h6>
                    <h2 class="text-primary"><?php echo $total; ?></h2>
                    <small class="text-muted">Livres empruntés</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center" style="background:#f3fcf6;">
                <div class="card-body">
                    <h6 class="text-success">Empruntés</h6>
                    <h2 class="text-success"><?php echo $empruntes; ?></h2>
                    <small class="text-muted">Dans les délais</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center" style="background:#fffbea;">
                <div class="card-body">
                    <h6 class="text-warning">En retard</h6>
                    <h2 class="text-warning"><?php echo $en_retard; ?></h2>
                    <small class="text-muted">Dépassement de délai</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center" style="background:#fff0f0;">
                <div class="card-body">
                    <h6 class="text-danger">Perdus</h6>
                    <h2 class="text-danger"><?php echo $perdus; ?></h2>
                    <small class="text-muted">Non retournés</small>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Livre</th>
                <th>Utilisateur</th>
                <th>Date d'emprunt</th>
                <th>Date limite</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($emprunts as $emprunt): ?>
            <tr>
                <td><?= $emprunt['id'] ?></td>
                <td><?= htmlspecialchars($emprunt['livre']) ?></td>
                <td><?= htmlspecialchars($emprunt['prenom'] . ' ' . $emprunt['utilisateur']) ?></td>
                <td><?= htmlspecialchars($emprunt['date_reservation']) ?></td>
                <td><?= htmlspecialchars($emprunt['date_limite']) ?></td>
                <td>
                    <?php
                    switch ($emprunt['statut']) {
                        case 'emprunte':
                            echo '<span class="badge bg-primary">Emprunté</span>';
                            break;
                        case 'en_retard':
                            echo '<span class="badge bg-warning text-dark">En retard</span>';
                            break;
                        case 'perdu':
                            echo '<span class="badge bg-danger">Perdu</span>';
                            break;
                        case 'rendu':
                            echo '<span class="badge bg-success">Rendu</span>';
                            break;
                        default:
                            echo '<span class="badge bg-secondary">'.htmlspecialchars($emprunt['statut']).'</span>';
                    }
                    ?>
                </td>
                <td>
                    <?php if (in_array($emprunt['statut'], ['emprunte', 'en_retard'])): ?>
                        <button class="btn btn-success btn-sm" onclick="openModal(<?= $emprunt['id'] ?>)">Marquer comme rendu</button>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modale pour marquer comme rendu (à compléter avec JS/PHP pour le traitement réel) -->
<div class="modal" id="renduModal" tabindex="-1" style="display:none;">
  <div class="modal-dialog">
    <form method="post" action="/Projet-Cherradi/views/admin/marquer_rendu.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Marquer comme rendu</h5>
          <button type="button" class="btn-close" onclick="closeModal()"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="reservation_id" id="reservationIdInput">
          <p>Dans quel état le livre a-t-il été rendu ?</p>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="etat" id="etatBon" value="bon" checked>
            <label class="form-check-label" for="etatBon">Bon état</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="etat" id="etatEndommage" value="endommage">
            <label class="form-check-label" for="etatEndommage">Endommagé</label>
          </div>
          <div id="dommagesContainer" style="display: none; margin-top: 15px;">
            <label for="description_dommages" class="form-label">Description des dommages</label>
            <textarea class="form-control" id="description_dommages" name="description_dommages" rows="3"></textarea>
            <div id="dommagesError" class="invalid-feedback" style="display: none;">
              Veuillez décrire les dommages
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="submitBtn">Valider</button>
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(reservationId) {
    document.getElementById('reservationIdInput').value = reservationId;
    document.getElementById('renduModal').style.display = 'block';
    // Réinitialiser le formulaire
    document.getElementById('etatBon').checked = true;
    document.getElementById('dommagesContainer').style.display = 'none';
    document.getElementById('description_dommages').value = '';
    document.getElementById('dommagesError').style.display = 'none';
}

function closeModal() {
    document.getElementById('renduModal').style.display = 'none';
}

// Gestion de l'affichage du textarea
document.getElementById('etatEndommage').addEventListener('change', function() {
    document.getElementById('dommagesContainer').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('etatBon').addEventListener('change', function() {
    document.getElementById('dommagesContainer').style.display = 'none';
});

// Validation du formulaire
document.querySelector('form').addEventListener('submit', function(e) {
    const etatEndommage = document.getElementById('etatEndommage').checked;
    const description = document.getElementById('description_dommages').value.trim();
    
    if (etatEndommage && !description) {
        e.preventDefault();
        document.getElementById('dommagesError').style.display = 'block';
        document.getElementById('description_dommages').classList.add('is-invalid');
    } else {
        document.getElementById('dommagesError').style.display = 'none';
        document.getElementById('description_dommages').classList.remove('is-invalid');
    }
});
</script>
</body>
</html> 