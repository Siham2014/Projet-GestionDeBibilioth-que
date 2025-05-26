<?php
session_start();
session_unset();      // Vide toutes les variables de session
session_destroy();    // Détruit la session
header('Location: /Projet-Cherradi/public/login.php'); // Redirige vers la page de login
exit();