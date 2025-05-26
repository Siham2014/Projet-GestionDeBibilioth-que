<?php

class Reservation
{
    private $db;

    // On reçoit un objet Database, on extrait la connexion PDO
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createReservation($etudiantId, $livreId)
    {
        $dateLimite = date('Y-m-d', strtotime('+7 days'));

        $query = "INSERT INTO reservations (etudiant_id, livre_id, date_reservation, date_limite) 
                 VALUES (:etudiant_id, :livre_id, CURDATE(), :date_limite)";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'etudiant_id' => $etudiantId,
            'livre_id' => $livreId,
            'date_limite' => $dateLimite
        ]);
    }

    public function isLivreDisponible($livreId)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM reservations 
                 WHERE livre_id = :livre_id 
                 AND date_retour IS NULL";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['livre_id' => $livreId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] == 0;
    }

    public function getReservationDetails($reservationId)
    {
        $query = "SELECT r.*, l.titre, l.isbn, e.nom, e.prenom 
                 FROM reservations r 
                 JOIN livres l ON r.livre_id = l.id 
                 JOIN etudiants e ON r.etudiant_id = e.id 
                 WHERE r.id = :reservation_id";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['reservation_id' => $reservationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer toutes les réservations en attente
    public function getPendingReservations()
    {
        $stmt = $this->db->query(
            "SELECT r.id, u.username, b.title AS book_title, r.reservation_date
             FROM reservations r
             JOIN users u ON r.user_id = u.id
             JOIN books b ON r.book_id = b.id
             WHERE r.status = 'pending'"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Approuver une réservation
    public function approveReservation($reservationId)
    {
        $stmt = $this->db->prepare("UPDATE reservations SET status = 'approved' WHERE id = ?");
        return $stmt->execute([$reservationId]);
    }

    // Rejeter une réservation
    public function rejectReservation($reservationId)
    {
        $stmt = $this->db->prepare("UPDATE reservations SET status = 'rejected' WHERE id = ?");
        return $stmt->execute([$reservationId]);
    }

    public function getReservationById($id) {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function addNotification($etudiant_id, $message, $type) {
        $stmt = $this->db->prepare("INSERT INTO notifications (etudiant_id, message, type) VALUES (?, ?, ?)");
        return $stmt->execute([$etudiant_id, $message, $type]);
    }

    public function incrementBookQuantity($book_id) {
        $stmt = $this->db->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
        return $stmt->execute([$book_id]);
    }

    public function markAsReturned($id, $etatPhysique, $descriptionDommages = null)
    {
        $query = "UPDATE reservations 
                 SET statut = 'rendu', 
                     etat_physique = :etat_physique, 
                     description_dommages = :description_dommages, 
                     date_retour = NOW() 
                 WHERE id = :id";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':etat_physique', $etatPhysique);
        $stmt->bindParam(':description_dommages', $descriptionDommages);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}