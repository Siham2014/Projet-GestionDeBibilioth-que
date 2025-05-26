<?php
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h3>Mot de passe oublié</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center mb-4">Entrez votre adresse email ci-dessous et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    <form method="POST" action="forgot-password.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
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