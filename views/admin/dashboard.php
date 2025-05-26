<?php
// session_start();
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: /login.php');
//     exit();
// }
?>
<?php
$pdo = new PDO('mysql:host=localhost;dbname=online-library', 'root', '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'], $_POST['action'], $_POST['etudiant_id'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $etudiant_id = intval($_POST['etudiant_id']);
    $action = $_POST['action'];

    if ($action === 'accepter') {
        // Générer notification d'acceptation
        $message = "Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.";
        $type = "acceptation";
        $stmt = $pdo->prepare("INSERT INTO notifications (etudiant_id, message, type) VALUES (?, ?, ?)");
        $stmt->execute([$etudiant_id, $message, $type]);
        // Mettre à jour le statut
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'acceptee' WHERE id = ?");
        $stmt->execute([$reservation_id]);
    } elseif ($action === 'refuser') {
        // Générer notification de refus
        $message = "Votre réservation a été refusée par l'administrateur.";
        $type = "refus";
        $stmt = $pdo->prepare("INSERT INTO notifications (etudiant_id, message, type) VALUES (?, ?, ?)");
        $stmt->execute([$etudiant_id, $message, $type]);
        // Récupérer l'id du livre pour réajuster la quantité
        $stmt = $pdo->prepare("SELECT book_id FROM reservations WHERE id = ?");
        $stmt->execute([$reservation_id]);
        $book_id = $stmt->fetchColumn();
        // Mettre à jour le statut
        $stmt = $pdo->prepare("UPDATE reservations SET statut = 'refusee' WHERE id = ?");
        $stmt->execute([$reservation_id]);
        // Réajuster la quantité du livre
        if ($book_id) {
            $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
            $stmt->execute([$book_id]);
        }
    }
    // Rafraîchir la page pour voir les changements
    header("Location: admin-dashboard.php");
    exit;
}

// Récupérer toutes les réservations avec infos étudiant et livre
$stmt = $pdo->query('
    SELECT r.*, e.nom, e.prenom, e.username, b.title AS titre, b.isbn
    FROM reservations r
    JOIN etudiant e ON r.etudiant_id = e.id
    JOIN books b ON r.book_id = b.id
    WHERE r.statut = "en_attente"
    ORDER BY r.date_reservation DESC
');
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtReclam = $pdo->query('
    SELECT r.*, e.nom, e.prenom, e.username
    FROM reclamation r
    JOIN etudiant e ON r.etudiant_id = e.id
    ORDER BY r.date_reclamation DESC
');
$reclamation = $stmtReclam->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        .container-fluid {
            padding: 0;
            margin: 0;
            width: 100%;
        }

        .row {
            margin: 0;
            width: 100%;
        }

        .sidebar {
            min-height: 100vh;
            background-color: var(--primary-color);
            padding-top: 20px;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            margin: 4px 0;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--secondary-color);
            border-left: 4px solid var(--accent-color);
        }

        .main-content {
            padding: 30px;
            margin-left: 250px;
            transition: all 0.3s;
            width: calc(100% - 250px);
            background-color: #fff;
            min-height: 100vh;
        }

        .card {
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .table-responsive {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin: 20px 0;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--light-bg);
            border-bottom: 2px solid var(--border-color);
            color: var(--text-color);
            font-weight: 600;
            padding: 15px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .search-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .search-container .input-group {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 5px;
        }

        .search-container input {
            border: none;
            padding: 10px 15px;
            font-size: 1rem;
        }

        .search-container input:focus {
            box-shadow: none;
        }

        .search-container .btn {
            padding: 10px 20px;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        @media (max-width: 1200px) {
            .main-content {
                padding: 20px;
            }
        }

        @media (max-width: 992px) {
            .table-responsive {
                padding: 15px;
            }

            .table thead th,
            .table tbody td {
                padding: 10px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
                width: 250px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }

            .main-content.active {
                margin-left: 250px;
                width: calc(100% - 250px);
            }

            #sidebarCollapse {
                display: block !important;
            }

            .table-responsive {
                margin: 10px 0;
                padding: 10px;
            }

            .table thead th {
                font-size: 0.9rem;
            }

            .table tbody td {
                font-size: 0.9rem;
            }

            .btn {
                padding: 6px 12px;
                font-size: 0.9rem;
            }

            .form-control {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 10px;
            }

            .table-responsive {
                padding: 8px;
            }

            .table thead th {
                font-size: 0.85rem;
                padding: 8px;
            }

            .table tbody td {
                font-size: 0.85rem;
                padding: 8px;
            }

            .btn {
                padding: 5px 10px;
                font-size: 0.85rem;
            }

            .form-control {
                font-size: 0.85rem;
                padding: 8px 12px;
            }

            h2 {
                font-size: 1.3rem;
                margin-bottom: 15px;
            }
        }

        #sidebarCollapse {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 9999;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #sidebarCollapse:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* Styles pour les modales */
        .modal-dialog {
            margin: 1rem;
            max-width: 500px;
        }

        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* Styles pour les cartes */
        .card-body {
            padding: 20px;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 15px;
            }
        }

        /* Styles pour les alertes */
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Styles pour les badges */
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <button type="button" id="sidebarCollapse" class="btn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="text-white text-center mb-4">Admin </h3>
                <nav>
                    <a href="#users" class="active" data-bs-toggle="tab">
                        <i class="fas fa-users me-2"></i> Print Users
                    </a>
                    <a href="#books" data-bs-toggle="tab">
                        <i class="fas fa-book me-2"></i> Manage Books
                    </a>
                    <a href="#reservations" data-bs-toggle="tab">
                        <i class="fas fa-calendar-check me-2"></i> Reservations
                    </a>
                    <a href="#historique" data-bs-toggle="tab">
                        <i class="fas fa-history me-2"></i> Historique
                    </a>
                    <a href="#reclamation" data-bs-toggle="tab">
                        <i class="fas fa-exclamation-circle me-2"></i> Réclamations
                    </a>
                    <a href="#statutlivres" data-bs-toggle="tab">
                        <i class="fas fa-book-reader me-2"></i> Statut des livres
                    </a>
                    <a href="#liste-noire" data-bs-toggle="tab">
                        <i class="fas fa-ban me-2"></i> Liste noire
                    </a>
                    <a href="/Projet-Cherradi/public/logout.php" class="mt-5">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>

                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">

                <div class="tab-content">
                    <!-- Print Users Section -->
                    <div class="tab-pane fade show active" id="users">
                        <h2 class="mb-4">Print Users</h2>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['success'];
                                unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['error'];
                                unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3 search-container d-flex align-items-center justify-content-between">
                            <div style="flex:1; max-width:400px;">
                                <div class="input-group align-items-center">
                                    <span class="input-group-text bg-white border-0 pe-1" style="margin-right:2px;">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input class="form-control border-0 rounded-start-pill ps-2" type="search" name="q"
                                        id="userSearchInput" placeholder="search by username" aria-label="Search"
                                        style="font-size: 1rem; background: #f5f6fa;">
                                    <button
                                        class="btn btn-primary rounded-end-pill px-4 ms-2 d-flex align-items-center gap-2"
                                        type="button" id="userSearchBtn" style="font-weight: 500;">
                                        <i class="fas fa-search"></i> search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Photo</th>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Registration Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <?php
                                    require_once __DIR__ . '/../../models/Etudiant.php';
                                    $etudiant = new Etudiant();
                                    $stmt = $etudiant->getAllEtudiants();
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        if (isset($row['role']) && $row['role'] === 'admin')
                                            continue;
                                        echo "<tr>";
                                        echo "<td>";
                                        $photoPath = '/Projet-Cherradi/public/uploads/profils/' . htmlspecialchars($row['photoprofil']);
                                        if (!empty($row['photoprofil']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $photoPath)) {
                                            echo "<img src='" . $photoPath . "' alt='Photo' style='width:40px;height:40px;object-fit:cover;border-radius:50%;'>";
                                        } else {
                                            echo "<img src='/Projet-Cherradi/public/uploads/profils/default.png' alt='Photo' style='width:40px;height:40px;object-fit:cover;border-radius:50%;'>";
                                        }
                                        echo "</td>";
                                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['date_inscription']) . "</td>";
                                        echo "<td class='action-buttons'>";
                                        echo "<a href='/Projet-Cherradi/public/admin.php?action=editUser&id=" . $row['id'] . "' class='btn btn-outline-primary btn-sm me-1' title='Modifier'><i class='fas fa-edit'></i></a>";
                                        echo "<button class='btn btn-outline-danger btn-sm btn-delete-user' data-id='" . $row['id'] . "' title='Supprimer'><i class='fas fa-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Books Management Section -->
                    <div class="tab-pane fade" id="books">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Manage Books</h2>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                                <i class="fas fa-plus me-2"></i>Add New Book
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>ISBN</th>
                                        <th>Available</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="booksTable">
                                    <!-- voilaa ou la table sera publier -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Reservations Section -->
                    <div class="tab-pane fade" id="reservations">
                        <h2 class="mb-4">Pending Reservations</h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($reservation['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['date_reservation']); ?></td>
                                            <td>
                                                <form method="post" action="admin-dashboard.php" style="display:inline;">
                                                    <input type="hidden" name="reservation_id"
                                                        value="<?php echo $reservation['id']; ?>">
                                                    <input type="hidden" name="etudiant_id"
                                                        value="<?php echo $reservation['etudiant_id']; ?>">
                                                    <input type="hidden" name="action" value="accepter">
                                                    <button type="submit" class="btn btn-success btn-sm">Accepter</button>
                                                </form>
                                                <form method="post" action="admin-dashboard.php" style="display:inline;">
                                                    <input type="hidden" name="reservation_id"
                                                        value="<?php echo $reservation['id']; ?>">
                                                    <input type="hidden" name="etudiant_id"
                                                        value="<?php echo $reservation['etudiant_id']; ?>">
                                                    <input type="hidden" name="action" value="refuser">
                                                    <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Historique des réservations -->
                    <div class="tab-pane fade" id="historique">
                        <h2 class="mb-4">Historique des réservations</h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Book</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmtHist = $pdo->query('
                        SELECT r.*, e.nom, e.prenom, e.username, b.title AS titre, b.isbn
                        FROM reservations r
                        JOIN etudiant e ON r.etudiant_id = e.id
                        JOIN books b ON r.book_id = b.id
                        WHERE r.statut IN ("acceptee", "refusee")
                        ORDER BY r.date_reservation DESC
                    ');
                                    $historique = $stmtHist->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($historique as $reservation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($reservation['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['date_reservation']); ?></td>
                                            <td>
                                                <?php
                                                if ($reservation['statut'] === 'acceptee') {
                                                    echo '<span class="badge bg-success">Acceptée</span>';
                                                } elseif ($reservation['statut'] === 'refusee') {
                                                    echo '<span class="badge bg-danger">Refusée</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Réclamations Section -->
                    <div class="tab-pane fade" id="reclamation">
                        <h2 class="mb-4">Réclamations des étudiants</h2>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Étudiant</th>
                                        <th>Sujet</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reclamation as $rec): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($rec['prenom'] . ' ' . $rec['nom']); ?></td>
                                            <td><?php echo htmlspecialchars($rec['sujet']); ?></td>
                                            <td><?php echo htmlspecialchars($rec['message']); ?></td>
                                            <td><?php echo htmlspecialchars($rec['date_reclamation']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Statut des livres -->
                    <div class="tab-pane fade" id="statutlivres">
                        <?php include __DIR__ . '/borrow_status.php'; ?>
                    </div>

                    <!-- Liste noire Section -->
                    <div class="tab-pane fade" id="liste-noire">
                        <?php include __DIR__ . '/liste_noire.php'; ?>
                    </div>
                </div>






                <!-- Add Book Modal -->
                <div class="modal fade" id="addBookModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Book</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addBookForm" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Author</label>
                                        <input type="text" class="form-control" name="author" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">ISBN</label>
                                        <input type="text" class="form-control" name="isbn" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" min="1" value="1"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Cover Image</label>
                                        <input type="file" class="form-control" name="image"
                                            accept="image/jpeg,image/png">
                                        <small class="form-text text-muted">Formats acceptés : JPG, PNG. Taille maximale
                                            :
                                            5MB</small>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveBookBtn">Save Book</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    // Load books
                    function loadBooks() {
                        $.get('/Projet-Cherradi/api/admin/books.php', { action: 'get' }, function (response) {
                            if (response.success) {
                                let html = '';
                                response.books.forEach(book => {
                                    html += `<tr>
                            <td>
                                <img src="/Projet-Cherradi/public/static/images/books/${book.image}" 
                                     alt="${book.title}" 
                                     style="width: 50px; height: 70px; object-fit: cover;"
                                     class="me-2">
                            </td>
                            <td>${book.title}</td>
                            <td>${book.author}</td>
                            <td>${book.isbn}</td>
                            <td>${book.available_quantity} / ${book.quantity}</td>
                            <td>
                                <button class='btn btn-primary btn-sm me-2' onclick='editBook(${book.id})'>
                                    <i class='fas fa-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-sm' onclick='deleteBook(${book.id})'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </td>
                        </tr>`;
                                });
                                $('#booksTable').html(html);
                            }
                        });
                    }

                    // Delete book function
                    function deleteBook(id) {
                        if (!confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')) {
                            return;
                        }

                        $.ajax({
                            url: '/Projet-Cherradi/api/admin/books.php',
                            type: 'POST',
                            data: {
                                action: 'delete',
                                id: id
                            },
                            success: function (response) {
                                if (response.success) {
                                    // Supprimer la ligne du tableau sans recharger la page
                                    $(`button[onclick='deleteBook(${id})']`).closest('tr').fadeOut(300, function () {
                                        $(this).remove();
                                    });
                                } else {
                                    alert(response.error || 'Une erreur est survenue lors de la suppression du livre');
                                }
                            },
                            error: function () {
                                alert('Une erreur est survenue lors de la communication avec le serveur');
                            }
                        });
                    }

                    // Edit book function
                    function editBook(id) {
                        // Rediriger vers la page d'édition avec l'ID du livre
                        window.location.href = `/Projet-Cherradi/views/admin/edit_book.php?id=${id}`;
                    }

                    // Load reservations
                    function loadReservations() {
                        $.get('/Projet-Cherradi/api/admin/pending-reservations.php', function (data) {
                            $('#reservationsTable').html(data);
                        });
                    }

                    // Initialize
                    $(document).ready(function () {
                        // loadPendingUsers(); // Commenté car nous utilisons maintenant PHP pour charger les utilisateurs
                        loadBooks();
                        loadReservations();

                        // Handle tab changes
                        $('.sidebar a').click(function (e) {
                            // Si le lien est le logout, on laisse le comportement normal
                            if ($(this).attr('href') === '/Projet-Cherradi/public/logout.php') {
                                return; // ne rien faire, laisser le navigateur suivre le lien
                            }
                            e.preventDefault();

                            const targetSelector = $(this).attr('href');
                            const $target = $(targetSelector);

                            if ($target.length) {
                                $('.sidebar a').removeClass('active');
                                $(this).addClass('active');
                                $('.tab-pane').removeClass('show active');
                                $target.addClass('show active');
                            } else {
                                console.warn('Target tab not found for:', targetSelector);
                            }
                        });


                        // Save new book
                        $('#saveBookBtn').click(function () {
                            const formData = new FormData($('#addBookForm')[0]);
                            formData.append('action', 'add');

                            $.ajax({
                                url: '/Projet-Cherradi/api/admin/books.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function (response) {
                                    if (response.success) {
                                        $('#addBookModal').modal('hide');
                                        loadBooks();
                                    } else {
                                        alert(response.error || 'Une erreur est survenue');
                                    }
                                },
                                error: function () {
                                    alert('Une erreur est survenue lors de l\'envoi du formulaire');
                                }
                            });
                        });

                        function fetchUsers(query = "") {
                            $.get('/Projet-Cherradi/api/admin/ajax_search_users.php', { q: query }, function (data) {
                                $('#usersTableBody').html(data);
                            });
                        }
                        $('#userSearchInput').on('input', function () {
                            fetchUsers($(this).val());
                        });
                        $('#userSearchBtn').on('click', function () {
                            fetchUsers($('#userSearchInput').val());
                        });

                        // Gestion de la sidebar sur mobile
                        $('#sidebarCollapse').on('click', function () {
                            $('.sidebar').toggleClass('active');
                            $('.main-content').toggleClass('active');
                        });

                        // Fermer la sidebar quand on clique en dehors sur mobile
                        $(document).on('click', function (e) {
                            if ($(window).width() <= 768) {
                                if (!$(e.target).closest('.sidebar, #sidebarCollapse').length) {
                                    $('.sidebar').removeClass('active');
                                    $('.main-content').removeClass('active');
                                }
                            }
                        });

                        // Gestion des onglets
                        $('.sidebar a[data-bs-toggle="tab"]').on('click', function (e) {
                            const target = $(this).attr('href');
                            localStorage.setItem('adminActiveTab', target);

                            // Fermer la sidebar sur mobile après avoir cliqué sur un onglet
                            if ($(window).width() <= 768) {
                                $('.sidebar').removeClass('active');
                                $('.main-content').removeClass('active');
                            }
                        });

                        // Restaurer l'onglet actif au chargement
                        const savedTab = localStorage.getItem('adminActiveTab');
                        if (savedTab && $(savedTab).length) {
                            $('.sidebar a').removeClass('active');
                            $('.tab-pane').removeClass('show active');
                            $('.sidebar a[href="' + savedTab + '"]').addClass('active');
                            $(savedTab).addClass('show active');
                        }
                    });
                </script>
                <script>
                    // Suppression AJAX d'un étudiant
                    $(document).on('click', '.btn-delete-user', function () {
                        if (!confirm("Êtes-vous sûr de vouloir supprimer cet étudiant ?")) return;
                        var btn = $(this);
                        var id = btn.data('id');
                        $.post('/Projet-Cherradi/api/admin/ajax_delete_user.php', { id: id }, function (response) {
                            if (response.success) {
                                btn.closest('tr').fadeOut(300, function () { $(this).remove(); });
                            } else {
                                alert(response.error || "Erreur lors de la suppression.");
                            }
                        }, 'json');
                    });
                </script>
                <script>
                    $(document).ready(function () {
                        // Quand on clique sur un onglet, on le mémorise
                        $('.sidebar a[data-bs-toggle="tab"]').on('click', function (e) {
                            const target = $(this).attr('href');
                            localStorage.setItem('adminActiveTab', target);
                        });

                        // À l'ouverture de la page, on regarde si un onglet est mémorisé
                        const savedTab = localStorage.getItem('adminActiveTab');
                        if (savedTab && $(savedTab).length) {
                            $('.sidebar a').removeClass('active');
                            $('.tab-pane').removeClass('show active');
                            $('.sidebar a[href="' + savedTab + '"]').addClass('active');
                            $(savedTab).addClass('show active');
                        }
                    });
                </script>

</body>

</html>