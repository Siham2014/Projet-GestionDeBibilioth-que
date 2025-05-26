<?php
require_once __DIR__ . '/../../controllers/BookController.php';
require_once __DIR__ . '/../../config/Database.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté et est admin
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

$bookController = new BookController();

try {
    // Ajout pour supporter GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get') {
        $books = $bookController->getAllBooks();
        echo json_encode(['success' => true, 'books' => $books]);
        exit;
    }

    switch ($_POST['action'] ?? '') {
        case 'add':
            $bookData = [
                'title' => $_POST['title'],
                'author' => $_POST['author'],
                'isbn' => $_POST['isbn'],
                'description' => $_POST['description'],
                'quantity' => $_POST['quantity']
            ];
            
            if ($bookController->addBook($bookData)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Erreur lors de l\'ajout du livre']);
            }
            break;

        case 'update':
            if (!isset($_POST['id'])) {
                throw new Exception('ID du livre manquant');
            }
            
            $bookData = [
                'title' => $_POST['title'],
                'author' => $_POST['author'],
                'isbn' => $_POST['isbn'],
                'description' => $_POST['description'],
                'quantity' => $_POST['quantity'],
                'available_quantity' => $_POST['available_quantity'] ?? $_POST['quantity']
            ];
            
            if ($bookController->updateBook($_POST['id'], $bookData)) {
                // Si la requête n'est pas AJAX, on redirige
                if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
                    $_SESSION['success'] = 'Livre modifié avec succès.';
                    header('Location: /Projet-Cherradi/views/admin/dashboard.php?tab=books');
                    exit;
                }
                echo json_encode(['success' => true]);
            } else {
                if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
                    $_SESSION['error'] = 'Erreur lors de la mise à jour du livre';
                    header('Location: /Projet-Cherradi/views/admin/dashboard.php?tab=books');
                    exit;
                }
                echo json_encode(['error' => 'Erreur lors de la mise à jour du livre']);
            }
            break;

        case 'delete':
            if (!isset($_POST['id'])) {
                throw new Exception('ID du livre manquant');
            }
            
            if ($bookController->deleteBook($_POST['id'])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Erreur lors de la suppression du livre']);
            }
            break;

        default:
            throw new Exception('Action non reconnue');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} 