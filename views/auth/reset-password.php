<?php
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
if (!$token) {
    header('Location: login.php');
    exit;
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h3>Réinitialiser votre mot de passe</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    <form method="POST" action="reset-password.php">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                        <a href="login.php" class="text-decoration-none">Retour à la connexion</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 