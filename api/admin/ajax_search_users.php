<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}
require_once __DIR__ . '/../../models/Etudiant.php';
header('Content-Type: text/html; charset=utf-8');

$q = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';
$etudiant = new Etudiant();
$stmt = $etudiant->getAllEtudiants();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (isset($row['role']) && $row['role'] === 'admin') continue;
    $idMatch = $q && strpos((string)$row['id'], $q) !== false;
    $usernameMatch = $q && strpos(strtolower($row['username']), $q) !== false;
    $emailMatch = $q && strpos(strtolower($row['email']), $q) !== false;
    if ($q && !$idMatch && !$usernameMatch && !$emailMatch) continue;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['date_inscription']) . "</td>";
    echo "<td>";
    echo "<a href='/Projet-Cherradi/public/admin.php?action=editUser&id=" . $row['id'] . "' class='btn btn-outline-primary btn-sm me-1' title='Modifier'><i class='fas fa-edit'></i></a>";
    echo "<button class='btn btn-outline-danger btn-sm btn-delete-user' data-id='" . $row['id'] . "' title='Supprimer'><i class='fas fa-trash'></i></button>";
    echo "</td>";
    echo "</tr>";
} 