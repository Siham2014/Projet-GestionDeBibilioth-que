<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../utils/Security.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/EmailService.php';
require_once __DIR__ . '/../config/Config.php';

class AuthController
{
    private $etudiant;
    private $security;
    private $validator;
    private $emailService;

    public function __construct()
    {
        $this->etudiant = new Etudiant();
        $this->security = new Security();
        $this->validator = new Validator();
        $this->emailService = new EmailService();

        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            $this->security->secureSessionStart();
        }
    }

    // Handle student registration
    public function register()
    {
        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF token
            if (!$this->security->verifyCsrfToken($_POST[Config::CSRF_TOKEN_NAME] ?? '')) {
                $_SESSION['error'] = "Session de sécurité expirée. Veuillez réessayer.";
                return false;
            }

            // Validate form data
            $errors = $this->validateRegistrationData($_POST);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                return false;
            }

            // Set student properties
            $this->etudiant->nom = $_POST['nom'];
            $this->etudiant->prenom = $_POST['prenom'];
            $this->etudiant->email = $_POST['email'];
            $this->etudiant->telephone = $_POST['telephone'] ?? null;
            $this->etudiant->date_naissance = $_POST['date_naissance'] ?? null;
            $this->etudiant->username = $_POST['username'];
            $this->etudiant->password = $_POST['password'];
            $this->etudiant->role = 'etudiant';
            $this->etudiant->is_active = 0;

// Gérer l'upload de la photo de profil
$photoprofil_name = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) {
        $_SESSION['errors']['photo'] = "Format de photo non autorisé.";
        $_SESSION['form_data'] = $_POST;
        return false;
    } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
        $_SESSION['errors']['photo'] = "La photo ne doit pas dépasser 2 Mo.";
        $_SESSION['form_data'] = $_POST;
        return false;
    } else {
        $photoprofil_name = uniqid('profil_') . '.' . $ext;
        $upload_dir = __DIR__ . '/../public/uploads/profils/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photoprofil_name);
    }
} else {
    $_SESSION['errors']['photo'] = "Veuillez sélectionner une photo de profil.";
    $_SESSION['form_data'] = $_POST;
    return false;
}

// Associer à l'objet étudiant
$this->etudiant->photoprofil = $photoprofil_name;

            // Generate verification token
            $this->etudiant->verification_token = $this->security->generateToken(32);

            // Check if email or username already exists
            if ($this->etudiant->emailExists()) {
                $_SESSION['errors']['email'] = "Cette adresse email est déjà utilisée.";
                $_SESSION['form_data'] = $_POST;
                return false;
            }

            if ($this->etudiant->usernameExists()) {
                $_SESSION['errors']['username'] = "Ce nom d'utilisateur est déjà pris.";
                $_SESSION['form_data'] = $_POST;
                return false;
            }

            // Create the student record
            if ($this->etudiant->create()) {
                // Send verification email
                $this->sendVerificationEmail();

                // Store success message
                $_SESSION['success'] = "Votre compte a été créé avec succès! Veuillez vérifier votre email pour activer votre compte.";

                // Redirect to verification page
                header("Location: " . Config::BASE_URL . "/verification.php");
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
                $_SESSION['form_data'] = $_POST;
                return false;
            }
        }

        // Generate CSRF token for the form
        $_SESSION[Config::CSRF_TOKEN_NAME] = $this->security->generateToken(Config::CSRF_TOKEN_LENGTH);

        return true;
    }

    // Validate registration form data
    private function validateRegistrationData($data)
    {
        $errors = [];

        // Validate nom
        if (!$this->validator->validateRequired($data['nom'] ?? '')) {
            $errors['nom'] = "Le nom est obligatoire.";
        } elseif (!$this->validator->validateLength($data['nom'], 2, 100)) {
            $errors['nom'] = "Le nom doit contenir entre 2 et 100 caractères.";
        }

        // Validate prenom
        if (!$this->validator->validateRequired($data['prenom'] ?? '')) {
            $errors['prenom'] = "Le prénom est obligatoire.";
        } elseif (!$this->validator->validateLength($data['prenom'], 2, 100)) {
            $errors['prenom'] = "Le prénom doit contenir entre 2 et 100 caractères.";
        }

        // Validate email
        if (!$this->validator->validateRequired($data['email'] ?? '')) {
            $errors['email'] = "L'email est obligatoire.";
        } elseif (!$this->validator->validateEmail($data['email'])) {
            $errors['email'] = "L'email n'est pas valide.";
        }

        // Validate telephone (optional)
        if (!empty($data['telephone']) && !$this->validator->validatePhone($data['telephone'])) {
            $errors['telephone'] = "Le numéro de téléphone n'est pas valide.";
        }

        // Validate date_naissance (optional)
        if (!empty($data['date_naissance']) && !$this->validator->validateDate($data['date_naissance'])) {
            $errors['date_naissance'] = "La date de naissance n'est pas valide.";
        }

        // Validate username
        if (!$this->validator->validateRequired($data['username'] ?? '')) {
            $errors['username'] = "Le nom d'utilisateur est obligatoire.";
        } elseif (!$this->validator->validateLength($data['username'], 4, 100)) {
            $errors['username'] = "Le nom d'utilisateur doit contenir entre 4 et 100 caractères.";
        } elseif (!$this->validator->validateUsername($data['username'])) {
            $errors['username'] = "Le nom d'utilisateur ne peut contenir que des lettres, des chiffres et des tirets bas.";
        }

        // Validate password
        if (!$this->validator->validateRequired($data['password'] ?? '')) {
            $errors['password'] = "Le mot de passe est obligatoire.";
        } elseif (!$this->validator->validatePassword($data['password'])) {
            $errors['password'] = "Le mot de passe doit contenir au moins " . Config::PASSWORD_MIN_LENGTH . " caractères, incluant une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }

        // Validate password confirmation
        if (!$this->validator->validateRequired($data['confirm_password'] ?? '')) {
            $errors['confirm_password'] = "La confirmation du mot de passe est obligatoire.";
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
        }

        return $errors;
    }

    // Send verification email
    private function sendVerificationEmail()
    {
        // Verification link
        $verificationLink = Config::BASE_URL . "/verify.php?id=" . $this->etudiant->id . "&token=" . $this->etudiant->verification_token;

        // Email subject
        $subject = "🎓 Bienvenue {$this->etudiant->prenom} - Activez votre compte bibliothèque";

        // Préparer les données pour le template
        $data = [
            'studentName' => $this->etudiant->prenom . ' ' . $this->etudiant->nom,
            'verificationLink' => $verificationLink
        ];

        // Charger le template
        ob_start();
        extract($data);
        include __DIR__ . '/../views/emails/verification.php';
        $content = ob_get_clean();

        // Send email
        $this->emailService->sendEmail(
            $this->etudiant->email,
            $this->etudiant->prenom . ' ' . $this->etudiant->nom,
            $subject,
            $content
        );
    }

    // Verify account with token
    public function verifyAccount()
    {
        if (isset($_GET['id']) && isset($_GET['token'])) {
            // Set student properties
            $this->etudiant->id = intval($_GET['id']);
            $this->etudiant->verification_token = $_GET['token'];

            // Attempt to verify account
            if ($this->etudiant->verifyAccount()) {
                $_SESSION['success'] = "Votre compte a été activé avec succès! Vous pouvez maintenant vous connecter.";
                return true;
            } else {
                $_SESSION['error'] = "Lien de vérification invalide ou expiré.";
                return false;
            }
        }

        $_SESSION['error'] = "Paramètres de vérification manquants.";
        return false;
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $etudiant = $this->etudiant->findByEmail($email);
        if (
            !$etudiant
            || !password_verify($password, $etudiant->password)
            || $etudiant->is_active != 1
        ) {
            $_SESSION['error'] = "Email ou mot de passe invalide, ou compte non activé.";
            header('Location: login.php');
            exit;
        }
        // Stocker toutes les infos utiles dans la session
        $_SESSION['user_id'] = $etudiant->id;
        $_SESSION['user_role'] = $etudiant->role;
        $_SESSION['user'] = [
            'id' => $etudiant->id,
            'username' => $etudiant->username,
            'email' => $etudiant->email,
            'role' => $etudiant->role
        ];
        // Redirection selon le rôle
        if ($etudiant->role === 'admin') {
            header('Location: /Projet-Cherradi/public/admin-dashboard.php');
        } else {
            header('Location: /Projet-Cherradi/views/user/dashboard.php'); // à adapter selon ta vue user
        }
        exit;
    }

    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (!$email) {
                $_SESSION['error'] = "Veuillez entrer une adresse email valide.";
                return;
            }

            $etudiant = $this->etudiant->findByEmail($email);

            if (!$etudiant) {
                // Pour des raisons de sécurité, on renvoie le même message même si l'email n'existe pas
                $_SESSION['success'] = "Si votre email est enregistré, vous recevrez un lien de réinitialisation.";
                return;
            }

            if ($etudiant->generateResetToken()) {
                // Envoyer l'email de réinitialisation
                $resetLink = Config::BASE_URL . "/reset-password.php?token=" . $etudiant->reset_token;

                $subject = "🔑 Réinitialisation de votre mot de passe";

                // Préparer les données pour le template
                $data = [
                    'studentName' => $etudiant->prenom . ' ' . $etudiant->nom,
                    'resetLink' => $resetLink
                ];

                // Charger le template
                ob_start();
                extract($data);
                include __DIR__ . '/../views/emails/reset-password.php';
                $content = ob_get_clean();

                // Envoyer l'email
                $this->emailService->sendEmail(
                    $etudiant->email,
                    $etudiant->prenom . ' ' . $etudiant->nom,
                    $subject,
                    $content
                );

                $_SESSION['success'] = "Si votre email est enregistré, vous recevrez un lien de réinitialisation.";
            } else {
                $_SESSION['error'] = "Une erreur est survenue. Veuillez réessayer.";
            }
        }
    }

    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            if (!$token || !$password || !$password_confirm) {
                $_SESSION['error'] = "Tous les champs sont requis.";
                header("Location: reset-password.php?token=" . urlencode($token));
                exit;
            }

            if ($password !== $password_confirm) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                header("Location: reset-password.php?token=" . urlencode($token));
                exit;
            }

            $etudiant = $this->etudiant->findByResetToken($token);

            if (!$etudiant) {
                $_SESSION['error'] = "Lien de réinitialisation invalide ou expiré.";
                header("Location: reset-password.php?token=" . urlencode($token));
                exit;
            }

            if ($this->etudiant->resetPassword($token, $password)) {
                $_SESSION['success'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                header('Location: login.php');
                exit;
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la réinitialisation du mot de passe.";
                header("Location: reset-password.php?token=" . urlencode($token));
                exit;
            }
        }
    }
}
?>