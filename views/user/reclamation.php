<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

$etudiant_id = $_SESSION['user_id'] ?? null;
if (!$etudiant_id) {
    header('Location: /Projet-Cherradi/public/login.php');
    exit;
}

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($sujet && $message) {
        $stmt = $pdo->prepare('INSERT INTO reclamation (etudiant_id, sujet, message) VALUES (?, ?, ?)');
        $stmt->execute([$etudiant_id, $sujet, $message]);
        $success = "Votre réclamation a bien été envoyée.";
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

$activePage = 'reclamation';
$title = 'Réclamation - BiblioTech';

// Début du contenu à inclure dans le layout
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-1">Formulaire de réclamation</h4>
                <div class="text-muted mb-4" style="font-size: 1rem;">
                    Utilisez ce formulaire pour signaler un problème ou faire une demande à l'administration.
                </div>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="post" action="reclamation.php">
                    <div class="mb-3">
                        <label for="sujet" class="form-label">Sujet</label>
                        <input type="text" class="form-control" id="sujet" name="sujet"
                            placeholder="Sujet de votre réclamation" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5"
                            placeholder="Décrivez votre problème ou votre demande en détail..." required></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Envoyer la réclamation</button>
                    </div>
                </form>
            </div>
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
        <h3>Réclamation</h3>
        <p class="text-muted">Envoyez une demande à l'administration</p>
    </div>
</div>
<?php
$topBar = ob_get_clean();

require_once __DIR__ . '/../layouts/layout_user.php';
?>