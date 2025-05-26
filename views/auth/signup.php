<?php
require_once __DIR__ . '/../../controllers/AuthController.php';

$authController = new AuthController();
$authController->register();

$csrf_token = $_SESSION[Config::CSRF_TOKEN_NAME] ?? '';
$form_data = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['errors']);

include __DIR__ . '/../../views/layouts/header.php';
?>

<div class="container py-5" style="background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%); min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    
                    <h2 class="mb-0">Inscription Étudiant</h2>
                    <p class="mt-2" style="font-size: 1rem;">Bienvenue sur notre bibliothèque universitaire !</p>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="<?php echo Config::CSRF_TOKEN_NAME; ?>" value="<?php echo $csrf_token; ?>">

                        <h5 class="text-primary mb-3"><i class="bi bi-person-lines-fill"></i> Informations personnelles</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label"><i class="bi bi-person-fill"></i> Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>" id="nom" name="nom" value="<?php echo htmlspecialchars($form_data['nom'] ?? ''); ?>" required>
                                <?php if (isset($errors['nom'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['nom']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label"><i class="bi bi-person-fill"></i> Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?php echo isset($errors['prenom']) ? 'is-invalid' : ''; ?>" id="prenom" name="prenom" value="<?php echo htmlspecialchars($form_data['prenom'] ?? ''); ?>" required>
                                <?php if (isset($errors['prenom'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['prenom']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="bi bi-envelope-fill"></i> Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label"><i class="bi bi-telephone-fill"></i> Téléphone</label>
                                <input type="tel" class="form-control <?php echo isset($errors['telephone']) ? 'is-invalid' : ''; ?>" id="telephone" name="telephone" value="<?php echo htmlspecialchars($form_data['telephone'] ?? ''); ?>">
                                <?php if (isset($errors['telephone'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['telephone']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_naissance" class="form-label"><i class="bi bi-calendar-date-fill"></i> Date de naissance</label>
                                <input type="date" class="form-control <?php echo isset($errors['date_naissance']) ? 'is-invalid' : ''; ?>" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($form_data['date_naissance'] ?? ''); ?>">
                                <?php if (isset($errors['date_naissance'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['date_naissance']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="photo" class="form-label"><i class="bi bi-image"></i> Photo de profil <span class="text-danger">*</span></label>
                                <input type="file" class="form-control <?php echo isset($errors['photo']) ? 'is-invalid' : ''; ?>" id="photo" name="photo" accept="image/*" required>
                                <?php if (isset($errors['photo'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['photo']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h5 class="text-primary mt-4 mb-3"><i class="bi bi-shield-lock-fill"></i> Informations de compte</h5>
                        <div class="mb-3">
                            <label for="username" class="form-label"><i class="bi bi-person-badge-fill"></i> Nom d'utilisateur <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" required>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><i class="bi bi-lock-fill"></i> Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                            <div class="form-text">
                                Le mot de passe doit contenir au moins <?php echo Config::PASSWORD_MIN_LENGTH; ?> caractères, incluant une majuscule, une minuscule, un chiffre et un caractère spécial.
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label"><i class="bi bi-lock-fill"></i> Confirmer le mot de passe <span class="text-danger">*</span></label>
                            <input type="password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" id="confirm_password" name="confirm_password" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-person-plus-fill"></i> S'inscrire</button>
                            <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                        </div>
                        <div class="text-center mt-3">
                            <small>Déjà inscrit ? <a href="login.php" class="text-primary">Se connecter</a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../views/layouts/footer.php'; ?>