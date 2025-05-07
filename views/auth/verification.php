<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../utils/Security.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    $security = new Security();
    $security->secureSessionStart();
}

// Include header
include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="auth-container">
    <h2>Vérification de Compte</h2>
    
    <div class="verification-message">
        <p>Un email de vérification a été envoyé à votre adresse email. Veuillez suivre les instructions dans l'email pour activer votre compte.</p>
        <p>Si vous n'avez pas reçu l'email, veuillez vérifier votre dossier de spam ou contactez-nous pour obtenir de l'aide.</p>
    </div>
    
    <div class="form-footer">
        <a href="login.php" class="btn btn-primary">Retour à la connexion</a>
    </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>