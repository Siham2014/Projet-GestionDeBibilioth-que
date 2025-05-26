<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BiblioTech - Accueil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
        .hero {
            background: url('https://images.unsplash.com/photo-1589998059171-988d887df646') center/cover no-repeat;
            min-height: 70vh;
            color: #fff;
            position: relative;
        }
        .hero-overlay {
            background: rgba(20, 40, 90, 0.80);
            position: absolute;
            top:0; left:0; right:0; bottom:0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }
        .navbar {
            background: #223366;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
        }
        .btn-yellow {
            background: #ffe066;
            color: #223366;
            font-weight: 600;
        }
        .btn-yellow:hover {
            background: #ffd43b;
            color: #223366;
        }
        .service-card {
            border-radius: 12px;
            box-shadow: 0 2px 10px #0001;
            transition: transform .2s;
        }
        .service-card:hover {
            transform: translateY(-5px) scale(1.03);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="https://cdn-icons-png.flaticon.com/512/29/29302.png" alt="logo" width="32" class="me-2">
            BiblioTech
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-3">
                <li class="nav-item">
                    <a class="nav-link" href="#livres">Livres</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#apropos">À propos</a>
                </li>
            </ul>
            <div class="d-flex align-items-center ms-auto">
                <a href="/Projet-Cherradi/public/login.php" class="btn btn-outline-light me-2">Connexion</a>
                <a href="/Projet-Cherradi/public/signup.php" class="btn btn-yellow"><i class="bi bi-person-plus"></i> Inscription</a>
            </div>
        </div>
    </div>
</nav>

<section class="hero position-relative">
    <div class="hero-overlay">
        <h1 class="display-4 fw-bold mb-3">Bienvenue à <span style="color:#ffe066;">BiblioTech</span></h1>
        <p class="lead mb-4">Votre bibliothèque numérique universitaire.<br>Des milliers de livres et de ressources à votre portée.</p>
        <div class="row mt-5 w-100 justify-content-center">
            <div class="col-6 col-md-3 mb-2">
                <div class="bg-white bg-opacity-25 rounded-3 py-2 px-3 text-white">
                    <i class="bi bi-book-half me-2"></i>+50 000 Livres
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="bg-white bg-opacity-25 rounded-3 py-2 px-3 text-white">
                    <i class="bi bi-infinity me-2"></i>Accès Illimité
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="bg-white bg-opacity-25 rounded-3 py-2 px-3 text-white">
                    <i class="bi bi-clock-history me-2"></i>24/7 Disponible
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="bg-white bg-opacity-25 rounded-3 py-2 px-3 text-white">
                    <i class="bi bi-lightbulb me-2"></i>Support Académique
                </div>
            </div>
        </div>
    </div>
</section>

<section id="livres" class="container py-5">
    <h2 class="text-center mb-4" style="color:#223366;">Nos Livres</h2>
    <p class="text-center mb-5">
        Découvrez une vaste collection de livres universitaires, scientifiques, techniques et culturels. 
        Notre bibliothèque numérique vous permet de consulter, réserver et emprunter des ouvrages adaptés à vos besoins académiques et personnels.
    </p>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="p-4 bg-white service-card h-100 text-center">
                <div class="mb-3" style="font-size:2.5rem;">📚</div>
                <h4>+50 000 Livres</h4>
                <p>Un large choix d’ouvrages dans toutes les disciplines, accessibles en ligne ou à la bibliothèque.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white service-card h-100 text-center">
                <div class="mb-3" style="font-size:2.5rem;">🔎</div>
                <h4>Recherche Facile</h4>
                <p>Recherchez rapidement un livre par titre, auteur, domaine ou ISBN grâce à notre moteur de recherche performant.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white service-card h-100 text-center">
                <div class="mb-3" style="font-size:2.5rem;">📝</div>
                <h4>Réservation en Ligne</h4>
                <p>Réservez vos livres préférés en quelques clics et suivez l’état de vos emprunts depuis votre espace personnel.</p>
            </div>
        </div>
    </div>
</section>

<section id="apropos" class="container py-5">
    <h2 class="text-center mb-4" style="color:#223366;">À propos</h2>
    <p class="text-center mb-5" style="max-width:700px;margin:auto;">
        BiblioTech est une bibliothèque universitaire moderne dédiée exclusivement aux livres. 
        Notre mission est de faciliter l’accès à la connaissance en offrant un catalogue riche et varié, 
        accessible à tous les étudiants et enseignants. Profitez d’une expérience de recherche, de réservation 
        et d’emprunt simple, rapide et entièrement en ligne.
    </p>
</section>

</body>
</html>