<?php

class Notification
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createNotification($etudiantId, $message, $type)
    {
        $query = "INSERT INTO notifications (etudiant_id, message, type, date_creation) 
                 VALUES (:etudiant_id, :message, :type, CURDATE())";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'etudiant_id' => $etudiantId,
            'message' => $message,
            'type' => $type
        ]);
    }

    public function getNotifications($etudiantId)
    {
        $query = "SELECT * FROM notifications 
                 WHERE etudiant_id = :etudiant_id 
                 ORDER BY date_creation DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['etudiant_id' => $etudiantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marquerCommeLu($notificationId)
    {
        $query = "UPDATE notifications 
                 SET lu = 1 
                 WHERE id = :notification_id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute(['notification_id' => $notificationId]);
    }

    public function getNombreNotificationsNonLues($etudiantId)
    {
        $query = "SELECT COUNT(*) as count 
                 FROM notifications 
                 WHERE etudiant_id = :etudiant_id 
                 AND lu = 0";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['etudiant_id' => $etudiantId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
}