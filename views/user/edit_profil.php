<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

$etudiant_id = $_SESSION['user_id'] ?? null;
if (!$etudiant_id) {
    header('Location: /Projet-Cherradi/public/login.php');
    exit;
}

// Récupérer les infos actuelles
$stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = ?');
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch(PDO::FETCH_ASSOC);

$error = '';
$success = '';

// Traitement modification infos (hors mot de passe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $username = $_POST['username'];
    $telephone = $_POST['telephone'];

    // Gestion de la photo
    $photo = $etudiant['photoprofil'];
    if (isset($_FILES['photoprofil']) && $_FILES['photoprofil']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photoprofil']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('profil_') . '.' . $ext;
        $dest = __DIR__ . '/../../public/uploads/profils/' . $filename;
        if (move_uploaded_file($_FILES['photoprofil']['tmp_name'], $dest)) {
            $photo = $filename;
        }
    }

    $stmt = $pdo->prepare('UPDATE etudiant SET nom=?, prenom=?, username=?, telephone=?, photoprofil=? WHERE id=?');
    $stmt->execute([$nom, $prenom, $username, $telephone, $photo, $etudiant_id]);
    $success = "Profil modifié avec succès.";
    // Rafraîchir les infos
    $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = ?');
    $stmt->execute([$etudiant_id]);
    $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Traitement modification mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $new_password2 = $_POST['new_password2'] ?? '';

    if (empty($current_password) || !password_verify($current_password, $etudiant['password'])) {
        $error = "Le mot de passe actuel est incorrect.";
    } elseif (empty($new_password) || $new_password !== $new_password2) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE etudiant SET password=? WHERE id=?');
        $stmt->execute([$password_hash, $etudiant_id]);
        $success = "Mot de passe modifié avec succès.";
        // Rafraîchir les infos
        $stmt = $pdo->prepare('SELECT * FROM etudiant WHERE id = ?');
        $stmt->execute([$etudiant_id]);
        $etudiant = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Traitement suppression du compte

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $delete_password = $_POST['delete_password'] ?? '';
    if (password_verify($delete_password, $etudiant['password'])) {
        // Supprimer les notifications liées à l'étudiant
        $stmt = $pdo->prepare('DELETE FROM notifications WHERE etudiant_id = ?');
        $stmt->execute([$etudiant_id]);
        // Supprimer les réservations liées à l'étudiant
$stmt = $pdo->prepare('DELETE FROM reservations WHERE etudiant_id = ?');
$stmt->execute([$etudiant_id]);
        // Supprimer l'étudiant
        $stmt = $pdo->prepare('DELETE FROM etudiant WHERE id = ?');
        $stmt->execute([$etudiant_id]);
        session_unset();
        session_destroy();
        header('Location: /Projet-Cherradi/public/login.php?deleted=1');
        exit;
    } else {
        $error = "Mot de passe incorrect pour la suppression du compte.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Modifier mon profil</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="update_profile" value="1">
        <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($etudiant['nom']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" value="<?php echo htmlspecialchars($etudiant['prenom']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($etudiant['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control" value="<?php echo htmlspecialchars($etudiant['telephone']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Photo de profil</label><br>
            <img src="/Projet-Cherradi/public/uploads/profils/<?php echo htmlspecialchars($etudiant['photoprofil'] ?? 'default.png'); ?>" width="80" class="rounded-circle mb-2"><br>
            <input type="file" name="photoprofil" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="profil.php" class="btn btn-secondary">Annuler</a>
        <!-- Bouton pour ouvrir la modale mot de passe -->
        <button type="button" class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#passwordModal">
            Modifier le mot de passe
        </button>
        <!-- Bouton pour ouvrir la modale suppression -->
        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
            Supprimer mon compte
        </button>
    </form>
</div>

<!-- Modal modification mot de passe -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="passwordModalLabel">Modifier le mot de passe</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Mot de passe actuel</label>
            <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
          </div>
          <div class="mb-3">
            <label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="new_password" class="form-control" required autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label class="form-label">Confirmer le nouveau mot de passe</label>
            <input type="password" name="new_password2" class="form-control" required autocomplete="new-password">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" name="change_password" class="btn btn-warning">Modifier</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal suppression compte -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <p>Entrez votre mot de passe pour confirmer la suppression de votre compte. Cette action est irréversible.</p>
          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="delete_password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" name="delete_account" class="btn btn-danger">Supprimer définitivement</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>