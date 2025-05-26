<?php
session_start();
require_once __DIR__ . '/../../controllers/BookController.php';

// Vérification de l'authentification admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /Projet-Cherradi/login.php');
    exit();
}

$bookController = new BookController();
$book = null;

if (isset($_GET['id'])) {
    $book = $bookController->getBookById($_GET['id']);
    if (!$book) {
        $_SESSION['error'] = "Livre non trouvé.";
        header('Location: /Projet-Cherradi/views/admin/dashboard.php');
        exit();
    }
} else {
    header('Location: /Projet-Cherradi/views/admin/dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Modifier le livre</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/Projet-Cherradi/api/admin/books.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($book['id']); ?>">
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($book['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="author" class="form-label">Auteur</label>
                                <input type="text" class="form-control" id="author" name="author" 
                                       value="<?php echo htmlspecialchars($book['author']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn" 
                                       value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3"><?php echo htmlspecialchars($book['description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantité totale</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       value="<?php echo htmlspecialchars($book['quantity']); ?>" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="available_quantity" class="form-label">Quantité disponible</label>
                                <input type="number" class="form-control" id="available_quantity" name="available_quantity" 
                                       value="<?php echo htmlspecialchars($book['available_quantity']); ?>" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image de couverture</label>
                                <?php if ($book['image'] && $book['image'] !== 'default_book.jpg'): ?>
                                    <div class="mb-2">
                                        <img src="/Projet-Cherradi/public/static/images/books/<?php echo htmlspecialchars($book['image']); ?>" 
                                             alt="Couverture actuelle" style="max-width: 200px;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png">
                                <small class="form-text text-muted">Formats acceptés : JPG, PNG. Taille maximale : 5MB</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                <a href="/Projet-Cherradi/views/admin/dashboard.php" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html> 