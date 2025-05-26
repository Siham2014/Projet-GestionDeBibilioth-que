<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/ListeNoire.php';

class AdminController
{
    private $db;
    private $userModel;
    private $bookModel;
    private $reservationModel;
    private $etudiant;

    public function __construct()
    {
        $this->db = new Database();
        $this->userModel = new User($this->db);
        $this->bookModel = new Book($this->db);
        $this->reservationModel = new Reservation($this->db);
        $this->etudiant = new Etudiant();
    }

    public function getPendingUsers()
    {
        return $this->userModel->getPendingUsers();
    }

    public function approveUser($userId)
    {
        return $this->userModel->approveUser($userId);
    }

    public function getPendingReservations()
    {
        return $this->reservationModel->getPendingReservations();
    }

    public function approveReservation($reservationId)
    {
        $stmt = $this->db->getConnection()->prepare("SELECT COUNT(*) as count FROM liste_noire WHERE user_id = ?");
        $stmt->execute([$reservationId]);
        $isBlacklisted = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        if ($isBlacklisted) {
            $_SESSION['error'] = "Impossible d'accepter la réservation : l'utilisateur est dans la liste noire.";
            header('Location: /Projet-Cherradi/views/admin/dashboard.php');
            exit;
        }
        return $this->reservationModel->approveReservation($reservationId);
    }

    public function rejectReservation($reservationId)
    {
        return $this->reservationModel->rejectReservation($reservationId);
    }

    public function addBook($bookData)
    {
        return $this->bookModel->create($bookData);
    }

    public function updateBook($bookId, $bookData)
    {
        return $this->bookModel->update($bookId, $bookData);
    }

    public function deleteBook($bookId)
    {
        return $this->bookModel->delete($bookId);
    }

    public function getAllBooks()
    {
        return $this->bookModel->getAll();
    }

    public function editUser($id)
    {
        // Vérification de l'authentification admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /Projet-Cherradi/login.php');
            exit();
        }

        $user = $this->etudiant->findById($id);
        if (!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: /Projet-Cherradi/views/admin/dashboard.php');
            exit();
        }

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->etudiant->update($id, $username, $email, $password)) {
                $_SESSION['success'] = "L'utilisateur a été modifié avec succès.";
                header('Location: /Projet-Cherradi/views/admin/dashboard.php');
                exit();
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la modification de l'utilisateur.";
            }
        }

        // Afficher le formulaire de modification
        require_once __DIR__ . '/../views/admin/edit_user.php';
    }

    public function removeSanction($id)
    {
        $database = new Database();
        $db = $database->getConnection();
        $listeNoireModel = new ListeNoire($db);

        // 1. Trouver l'utilisateur concerné par la sanction (user_id)
        $stmt = $db->prepare("SELECT user_id FROM liste_noire WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $row ? $row['user_id'] : null;

        // 2. Supprimer la sanction
        if ($listeNoireModel->removeSanction($id)) {
            // 3. Restaurer les réservations annulées ou vides à cause du blacklist
            $stmtRestore = $db->prepare("SELECT id, book_id FROM reservations WHERE etudiant_id = ? AND (statut IS NULL OR statut = '' OR statut = 'annulee')");
            $stmtRestore->execute([$user_id]);
            $toRestore = $stmtRestore->fetchAll(PDO::FETCH_ASSOC);

            foreach ($toRestore as $res) {
                // Vérifier la disponibilité du livre
                $stmtBook = $db->prepare("SELECT available_quantity, title FROM books WHERE id = ?");
                $stmtBook->execute([$res['book_id']]);
                $book = $stmtBook->fetch(PDO::FETCH_ASSOC);

                if ($book && $book['available_quantity'] > 0) {
                    // Remettre la réservation en attente et diminuer la quantité
                    $db->prepare("UPDATE reservations SET statut = 'en_attente' WHERE id = ?")->execute([$res['id']]);
                    $db->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?")->execute([$res['book_id']]);
                } else {
                    // Laisser la réservation annulée et notifier l'utilisateur
                    $db->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?")->execute([$res['id']]);
                    $message = "Le livre « " . htmlspecialchars($book['title']) . " » que vous aviez réservé avant votre blocage n'est plus disponible.";
                    $stmtNotif = $db->prepare("INSERT INTO notifications (etudiant_id, message, type) VALUES (?, ?, 'reservation_impossible')");
                    $stmtNotif->execute([$user_id, $message]);
                }
            }
            $_SESSION['success'] = "La sanction a été levée avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la levée de la sanction.";
        }
        header('Location: /Projet-Cherradi/public/admin-dashboard.php?tab=liste-noire');
        exit();
    }

    public function hasRetard($etudiantId)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM reservations 
                 WHERE etudiant_id = :etudiant_id 
                 AND date_retour IS NULL 
                 AND date_limite < CURDATE()";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->execute(['etudiant_id' => $etudiantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
}

// Simple request handling based on action parameter
if (isset($_GET['action'])) {
    $adminController = new AdminController();
    $action = $_GET['action'];

    // Basic routing based on action
    if ($action === 'removeSanction' && isset($_POST['id'])) {
        $adminController->removeSanction($_POST['id']);
    }
    // Add other actions here as needed
}

?>