<?php

class UserController
{
    private $etudiantModel;
    private $reservationModel;
    private $notificationModel;
    private $livreModel;

    public function __construct($db)
    {
        $this->etudiantModel = new Etudiant($db);
        $this->reservationModel = new Reservation($db);
        $this->notificationModel = new Notification($db);
        $this->livreModel = new Livre($db);
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        $etudiant = $this->etudiantModel->getEtudiantById($_SESSION['user_id']);
        $reservations = $this->etudiantModel->getReservationsActives($_SESSION['user_id']);
        $hasRetard = $this->etudiantModel->hasRetard($_SESSION['user_id']);
        $notifications = $this->notificationModel->getNotifications($_SESSION['user_id']);

        require_once 'views/user/dashboard.php';
    }

    public function rechercherLivres()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        $critere = $_GET['critere'] ?? '';
        $valeur = $_GET['valeur'] ?? '';

        $livres = $this->livreModel->rechercherLivres($critere, $valeur);
        require_once 'views/user/recherche.php';
    }

    public function reserverLivre()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        $livreId = $_POST['livre_id'] ?? null;

        if (!$livreId) {
            $_SESSION['error'] = "ID du livre manquant";
            header('Location: /user/recherche.php');
            exit;
        }

        // Vérifier le nombre de réservations actives
        $nbReservations = $this->etudiantModel->getNombreReservationsActives($_SESSION['user_id']);
        if ($nbReservations >= 3) {
            $_SESSION['error'] = "Vous avez déjà atteint le maximum de 3 réservations";
            header('Location: /user/dashboard.php');
            exit;
        }

        // Vérifier si le livre est disponible
        if (!$this->reservationModel->isLivreDisponible($livreId)) {
            $_SESSION['error'] = "Ce livre n'est pas disponible";
            header('Location: /user/recherche.php');
            exit;
        }

        // Créer la réservation
        if ($this->reservationModel->createReservation($_SESSION['user_id'], $livreId)) {
            $this->notificationModel->createNotification(
                $_SESSION['user_id'],
                "Votre réservation a été confirmée",
                "reservation"
            );
            $_SESSION['success'] = "Réservation effectuée avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la réservation";
        }

        header('Location: /user/dashboard.php');
        exit;
    }

    public function profil()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        $etudiant = $this->etudiantModel->getEtudiantById($_SESSION['user_id']);
        require_once 'views/user/profil.php';
    }

    public function updateProfil()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        $data = [
            'email' => $_POST['email'] ?? '',
            'telephone' => $_POST['telephone'] ?? ''
        ];

        if ($this->etudiantModel->updateEtudiant($_SESSION['user_id'], $data)) {
            $_SESSION['success'] = "Profil mis à jour avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour du profil";
        }

        header('Location: /user/profil.php');
        exit;
    }

    public function soumettreReclamation()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        $message = $_POST['message'] ?? '';

        if (empty($message)) {
            $_SESSION['error'] = "Le message est requis";
            header('Location: /user/reclamation.php');
            exit;
        }

        // Créer la réclamation
        if ($this->reclamationModel->createReclamation($_SESSION['user_id'], $message)) {
            $this->notificationModel->createNotification(
                $_SESSION['user_id'],
                "Votre réclamation a été enregistrée",
                "reclamation"
            );
            $_SESSION['success'] = "Réclamation soumise avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la soumission de la réclamation";
        }

        header('Location: /user/dashboard.php');
        exit;
    }
}