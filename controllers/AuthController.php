<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../utils/Security.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/EmailService.php';
require_once __DIR__ . '/../config/Config.php';

class AuthController {
    private $etudiant;
    private $security;
    private $validator;
    private $emailService;
    
    public function __construct() {
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
    public function register() {
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
    private function validateRegistrationData($data) {
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
    private function sendVerificationEmail() {
        // Verification link
        $verificationLink = Config::BASE_URL . "/verify.php?id=" . $this->etudiant->id . "&token=" . $this->etudiant->verification_token;
        
        // Email subject
        $subject = "Vérification de votre compte - Bibliothèque";
        
        // Email content
        $content = "
            <h2>Bienvenue sur notre système de gestion de bibliothèque!</h2>
            <p>Bonjour {$this->etudiant->prenom} {$this->etudiant->nom},</p>
            <p>Merci de vous être inscrit sur notre plateforme. Pour activer votre compte, veuillez cliquer sur le lien ci-dessous :</p>
            <p><a href='{$verificationLink}'>Activer mon compte</a></p>
            <p>Si vous n'avez pas demandé cette inscription, veuillez ignorer cet email.</p>
            <p>Cordialement,<br>L'équipe de la bibliothèque</p>
        ";
        
        // Send email
        $this->emailService->sendEmail(
            $this->etudiant->email,
            $this->etudiant->prenom . ' ' . $this->etudiant->nom,
            $subject,
            $content
        );
    }
    
    // Verify account with token
    public function verifyAccount() {
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
}
?>