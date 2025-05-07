<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

$authController = new AuthController();
$authController->register();

// Get CSRF token
$csrf_token = $_SESSION[Config::CSRF_TOKEN_NAME] ?? '';

// Get form data if available
$form_data = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];

// Clear form data and errors from session
unset($_SESSION['form_data']);
unset($_SESSION['errors']);

// Include header
include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="auth-container">
    <h2>Inscription Étudiant</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form">
        <input type="hidden" name="<?php echo Config::CSRF_TOKEN_NAME; ?>" value="<?php echo $csrf_token; ?>">
        
        <div class="form-section">
            <h3>Informations personnelles</h3>
            
            <div class="form-group">
                <label for="nom">Nom <span class="required">*</span></label>
                <input type="text" id="nom" name="nom" value="<?php echo isset($form_data['nom']) ? htmlspecialchars($form_data['nom']) : ''; ?>" required>
                <?php if (isset($errors['nom'])): ?>
                    <div class="error-message"><?php echo $errors['nom']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom <span class="required">*</span></label>
                <input type="text" id="prenom" name="prenom" value="<?php echo isset($form_data['prenom']) ? htmlspecialchars($form_data['prenom']) : ''; ?>" required>
                <?php if (isset($errors['prenom'])): ?>
                    <div class="error-message"><?php echo $errors['prenom']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="error-message"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" value="<?php echo isset($form_data['telephone']) ? htmlspecialchars($form_data['telephone']) : ''; ?>">
                <?php if (isset($errors['telephone'])): ?>
                    <div class="error-message"><?php echo $errors['telephone']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="date_naissance">Date de naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" value="<?php echo isset($form_data['date_naissance']) ? htmlspecialchars($form_data['date_naissance']) : ''; ?>">
                <?php if (isset($errors['date_naissance'])): ?>
                    <div class="error-message"><?php echo $errors['date_naissance']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-section">
            <h3>Informations de compte</h3>
            
            <div class="form-group">
                <label for="username">Nom d'utilisateur <span class="required">*</span></label>
                <input type="text" id="username" name="username" value="<?php echo isset($form_data['username']) ? htmlspecialchars($form_data['username']) : ''; ?>" required>
                <?php if (isset($errors['username'])): ?>
                    <div class="error-message"><?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <input type="password" id="password" name="password" required>
                <div class="password-requirements">
                    Le mot de passe doit contenir au moins <?php echo Config::PASSWORD_MIN_LENGTH; ?> caractères, incluant une majuscule, une minuscule, un chiffre et un caractère spécial.
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="error-message"><?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="error-message"><?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">S'inscrire</button>
            <button type="reset" class="btn btn-secondary">Réinitialiser</button>
        </div>
        
        <div class="form-footer">
            Déjà inscrit? <a href="login.php">Se connecter</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>