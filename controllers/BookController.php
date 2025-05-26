<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Book.php';

class BookController {
    private $db;
    private $bookModel;
    private $uploadDir;

    public function __construct() {
        $this->db = new Database();
        $this->bookModel = new Book($this->db);
        $this->uploadDir = __DIR__ . '/../public/static/images/books/';
    }

    public function addBook($bookData) {
        // Gérer l'upload de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->handleImageUpload($_FILES['image']);
            if ($imageName) {
                $bookData['image'] = $imageName;
            }
        } else {
            $bookData['image'] = 'default_book.jpg';
        }

        return $this->bookModel->create($bookData);
    }

    public function updateBook($id, $bookData) {
        // Gérer l'upload de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = $this->handleImageUpload($_FILES['image']);
            if ($imageName) {
                // Supprimer l'ancienne image si elle existe et n'est pas l'image par défaut
                $oldBook = $this->bookModel->getById($id);
                if ($oldBook && $oldBook['image'] !== 'default_book.jpg') {
                    $oldImagePath = $this->uploadDir . $oldBook['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $bookData['image'] = $imageName;
            }
        }

        return $this->bookModel->update($id, $bookData);
    }

    private function handleImageUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Vérifier le type de fichier
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Type de fichier non autorisé. Seuls les formats JPG et PNG sont acceptés.');
        }

        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            throw new Exception('Le fichier est trop volumineux. Taille maximale : 5MB.');
        }

        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('book_') . '.' . $extension;

        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $this->uploadDir . $newFileName)) {
            return $newFileName;
        }

        return false;
    }

    public function deleteBook($id) {
        // Supprimer l'image associée si elle existe et n'est pas l'image par défaut
        $book = $this->bookModel->getById($id);
        if ($book && $book['image'] !== 'default_book.jpg') {
            $imagePath = $this->uploadDir . $book['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        return $this->bookModel->delete($id);
    }

    public function getAllBooks() {
        return $this->bookModel->getAll();
    }

    public function getBookById($id) {
        return $this->bookModel->getById($id);
    }
} 