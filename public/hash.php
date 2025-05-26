<?php
// Génère un hash sécurisé pour un mot de passe donné
$password = 'admin123'; // Change ce mot de passe si besoin
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Mot de passe : $password<br>";
echo "Hash généré : <br><textarea cols='80' rows='2'>$hash</textarea>"; 