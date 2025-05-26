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

<!-- Dégradé de fond -->
<div style="background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%); min-height: 90vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <span class="d-inline-block bg-primary bg-gradient rounded-circle p-4 shadow" style="margin-top:-60px;">
                                <i class="bi bi-envelope-check-fill text-white" style="font-size: 3rem;"></i>
                            </span>
                        </div>
                        <h2 class="mb-3 text-primary">Vérification de Compte</h2>
                        <p class="lead mb-4">
                            Merci pour votre inscription !<br>
                            Un email de vérification vient d’être envoyé à votre adresse.<br>
                            <span class="fw-semibold">Cliquez sur le lien dans l’email pour activer votre compte.</span>
                        </p>
                        <div class="alert alert-info mb-4" role="alert">
                            <i class="bi bi-info-circle-fill"></i>
                            Si vous ne voyez pas l’email, vérifiez votre dossier <b>spam</b> ou <b>courrier indésirable</b>.<br>
                            Besoin d’aide ? <a href="mailto:support@votresite.com" class="alert-link">Contactez-nous</a>.
                        </div>
                        <a href="login.php" class="btn btn-lg btn-primary px-4 shadow-sm">
                            <i class="bi bi-box-arrow-in-right"></i> Retour à la connexion
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>