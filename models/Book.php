<?php

class Book {
    private $db;

    // On reçoit un objet Database et on extrait la connexion PDO
    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    // Crée un nouveau livre
    public function create($bookData) {
        $stmt = $this->db->prepare(
            "INSERT INTO books (title, author, isbn, description, quantity, available_quantity, image)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $bookData['title'],
            $bookData['author'],
            $bookData['isbn'],
            $bookData['description'],
            $bookData['quantity'],
            $bookData['quantity'], // available_quantity initialisé comme quantity
            $bookData['image'] ?? 'default_book.jpg'
        ]);
    }

    // Récupère tous les livres
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM books");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Met à jour un livre
    public function update($id, $bookData) {
        $stmt = $this->db->prepare(
            "UPDATE books
             SET title = ?, author = ?, isbn = ?, description = ?, quantity = ?, available_quantity = ?, image = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            $bookData['title'],
            $bookData['author'],
            $bookData['isbn'],
            $bookData['description'],
            $bookData['quantity'],
            $bookData['available_quantity'] ?? $bookData['quantity'],
            $bookData['image'] ?? 'default_book.jpg',
            $id
        ]);
    }

    // Supprime un livre
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementQuantity($bookId) {
        $query = "UPDATE books SET available_quantity = available_quantity + 1 WHERE id = :book_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':book_id', $bookId);
        return $stmt->execute();
    }
} 