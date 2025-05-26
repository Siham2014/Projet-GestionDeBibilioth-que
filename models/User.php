<?php

class User {
    private $db;

    // Constructeur : on passe l'objet Database et on récupère la connexion PDO
    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    // Récupère tous les utilisateurs non approuvés (pending)
    public function getPendingUsers() {
        $stmt = $this->db->query("SELECT * FROM users WHERE is_approved = 0");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Approuve un utilisateur via son ID
    public function approveUser($userId) {
        $stmt = $this->db->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    // Récupère un utilisateur par email (utile pour login)
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crée un nouvel utilisateur
    public function create($userData) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, role, is_approved)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $userData['username'],
            $userData['email'],
            $userData['password'],
            $userData['role'],
            $userData['is_approved'] ?? 0
        ]);
    }
} 