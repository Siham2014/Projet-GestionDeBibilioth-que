<?php

require_once __DIR__ . '/../config/Database.php';

class ListeNoire
{
    private $conn;
    private $table_name = "liste_noire";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT
                    ln.id,
                    ln.user_id,
                    e.nom, 
                    e.prenom,
                    ln.motif,
                    ln.date_ajout,
                    ln.details,
                    ln.levee_at
                  FROM " . $this->table_name . " ln
                  JOIN etudiant e ON ln.user_id = e.id
                  ORDER BY ln.date_ajout DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function removeSanction($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
} 