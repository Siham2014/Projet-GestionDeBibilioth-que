<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../utils/Security.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    $security = new Security();
    $security->secureSessionStart();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Gestion des Étudiants</title>
    <link rel="stylesheet" href="<?php echo Config::BASE_URL; ?>/public/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Système de Gestion de Bibliothèque</h1>
        </header>
        <main>
<?php 
// Display success messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// Display error messages
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>