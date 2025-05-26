<?php
// Include necessary files and perform authentication check here
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/ListeNoire.php';

$database = new Database();
$db = $database->getConnection();

$listeNoireModel = new ListeNoire($db);

$listeNoire = $listeNoireModel->getAll();

?>

<h2>Liste noire</h2>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom de l'utilisateur</th>
            <th>Motif</th>
            <th>Date d'ajout</th>
            <th>Détails</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($listeNoire) > 0) : ?>
            <?php foreach ($listeNoire as $entry) : ?>
                <tr>
                    <td><?= $entry['id'] ?></td>
                    <td><?= $entry['nom'] . ' ' . $entry['prenom'] ?></td>
                    <td><?= $entry['motif'] ?></td>
                    <td><?= $entry['date_ajout'] ?></td>
                    <td><?= $entry['details'] ?></td>
                    <td>
                        <form action="/Projet-Cherradi/controllers/AdminController.php?action=removeSanction" method="POST">
                            <input type="hidden" name="id" value="<?= $entry['id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm">Lever la sanction</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6">Aucun utilisateur dans la liste noire.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table> 