<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? htmlspecialchars($title) : 'BiblioTech' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('') center/cover no-repeat fixed;
            min-height: 100vh;
            position: relative;
        }

        .biblio-content {
            position: relative;
            z-index: 1;
        }

        #sidebarCollapse {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 9999;
            background: rgb(34, 62, 102);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #sidebarCollapse:hover {
            background: rgb(61, 109, 176);
            transform: translateY(-2px);
        }

        .sidebar-biblio {
            background: rgb(34, 62, 102) !important;
            min-height: 100vh;
            width: 250px;
            color: #fff;
            padding: 0;
            border-top-right-radius: 32px;
            border-bottom-right-radius: 32px;
            box-shadow: 2px 0 16px #0001;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            transition: all 0.3s ease;
            position: fixed;
            z-index: 1000;
        }

        .sidebar-biblio .sidebar-title {
            font-size: 2rem;
            font-weight: bold;
            color: rgba(241, 245, 250, 0.89);
            letter-spacing: 1px;
            text-align: center;
            padding: 36px 0 28px 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .sidebar-biblio .nav-link {
            color: rgb(94, 150, 194);
            background: rgb(61, 109, 176);
            margin: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.08rem;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            text-align: left;
            padding: 12px 22px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px #0001;
            border: none;
        }

        .sidebar-biblio .nav-link:hover,
        .sidebar-biblio .nav-link.active {
            background: rgb(239, 240, 243);
            color: rgb(34, 79, 102);
            text-decoration: none;
            box-shadow: 0 4px 16px #ffe06644;
        }

        .sidebar-biblio .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .sidebar-biblio .logout-link {
            margin: 40px 20px 24px 20px;
            background: none;
            color: #fff;
            border: none;
            font-weight: 600;
            text-align: left;
            width: auto;
            padding: 12px 22px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            transition: color 0.2s, background 0.2s;
            font-size: 1.08rem;
        }

        .sidebar-biblio .logout-link:hover {
            color: #223366;
            background: rgb(27, 71, 126);
            text-decoration: none;
        }

        .sidebar-biblio .logout-link i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .main-content-biblio {
            background: transparent;
            min-height: 100vh;
            padding: 0 0 0 250px;
            transition: all 0.3s ease;
        }

        .main-content-biblio .top-bar {
            background: rgba(255, 255, 255, 0.97);
            border-bottom: 1px solid #e0e0e0;
            padding: 32px 32px 24px 32px;
            border-top-left-radius: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px #0001;
        }

        .main-content-biblio h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #223366;
        }

        .main-content-biblio .text-muted {
            color: #888 !important;
            font-size: 1.1rem;
        }

        .main-content-biblio .card {
            border-radius: 18px;
            box-shadow: 0 2px 12px #0001;
            border: none;
            background: rgba(255, 255, 255, 0.97);
        }

        .main-content-biblio .card-title {
            color: #223366;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .main-content-biblio .btn-primary {
            background: #ffe066;
            color: #223366;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }

        .main-content-biblio .btn-primary:hover {
            background: #ffd43b;
            color: #223366;
        }

        .main-content-biblio .badge.bg-success {
            background: #51cf66 !important;
            color: #223366;
            font-weight: 600;
        }

        .main-content-biblio .badge.bg-warning {
            background: #ffe066 !important;
            color: #223366;
            font-weight: 600;
        }

        .main-content-biblio .badge.bg-danger {
            background: #fa5252 !important;
            color: #fff;
            font-weight: 600;
        }

        .main-content-biblio h4 {
            color: #223366;
            font-weight: 700;
            margin-top: 2rem;
        }

        .main-content-biblio .alert-info {
            background: #e7f5ff;
            color: #223366;
            border: none;
            border-radius: 10px;
        }

        .btn-yellow-custom {
            background-color: #ffe066;
            color: #223366;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s;
        }

        .btn-yellow-custom:hover,
        .btn-yellow-custom:focus {
            background-color: #ffd43b;
            color: #223366;
        }

        @media (max-width: 768px) {
            .sidebar-biblio {
                margin-left: -250px;
            }

            .sidebar-biblio.active {
                margin-left: 0;
            }

            .main-content-biblio {
                padding-left: 0;
            }

            .main-content-biblio.active {
                padding-left: 250px;
            }

            .top-bar {
                padding: 20px !important;
            }

            .top-bar h3 {
                font-size: 1.5rem !important;
            }
        }

        @media (max-width: 576px) {
            #sidebarCollapse {
                top: 10px;
                left: 10px;
                padding: 8px 12px;
            }

            .sidebar-biblio {
                width: 250px;
            }

            .top-bar {
                padding: 15px !important;
            }

            .top-bar h3 {
                font-size: 1.3rem !important;
            }

            .container {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <button type="button" id="sidebarCollapse" class="btn">
        <i class="bi bi-list"></i>
    </button>
    <div class="biblio-overlay"></div>
    <div class="biblio-content">
        <div class="d-flex">
            <!-- Sidebar -->
            <nav class="sidebar-biblio d-flex flex-column">
                <div class="sidebar-title">BiblioTech</div>
                <ul class="nav flex-column flex-grow-1">
                    <li class="nav-item mb-2"><a href="/Projet-Cherradi/views/user/dashboard.php"
                            class="nav-link<?= (isset($activePage) && $activePage === 'dashboard') ? ' active' : '' ?>"><i
                                class="bi bi-house"></i> Accueil</a></li>
                    <li class="nav-item mb-2"><a href="/Projet-Cherradi/views/user/recherche.php"
                            class="nav-link<?= (isset($activePage) && $activePage === 'recherche') ? ' active' : '' ?>"><i
                                class="bi bi-search"></i> Recherche</a></li>
                    <li class="nav-item mb-2"><a href="/Projet-Cherradi/views/user/reservations.php"
                            class="nav-link<?= (isset($activePage) && $activePage === 'reservations') ? ' active' : '' ?>"><i
                                class="bi bi-journal-bookmark"></i> Mes Réservations</a></li>
                    <li class="nav-item mb-2"><a href="/Projet-Cherradi/views/user/reclamation.php"
                            class="nav-link<?= (isset($activePage) && $activePage === 'reclamation') ? ' active' : '' ?>"><i
                                class="bi bi-exclamation-circle"></i> Réclamation</a></li>
                    <li class="nav-item mb-2"><a href="/Projet-Cherradi/views/user/profil.php"
                            class="nav-link<?= (isset($activePage) && $activePage === 'profil') ? ' active' : '' ?>"><i
                                class="bi bi-person"></i> Profil</a></li>
                </ul>
                <a href="/Projet-Cherradi/public/logout.php" class="logout-link"><i class="bi bi-box-arrow-right"></i>
                    Logout</a>
            </nav>
            <!-- Main content -->
            <div class="main-content-biblio flex-grow-1">
                <?php if (isset($topBar))
                    echo $topBar; ?>
                <div class="container mt-5">
                    <?= $content ?? '' ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Toggle sidebar
            $('#sidebarCollapse').on('click', function () {
                $('.sidebar-biblio').toggleClass('active');
                $('.main-content-biblio').toggleClass('active');
            });

            // Close sidebar when clicking outside on mobile
            $(document).on('click', function (e) {
                if ($(window).width() <= 768) {
                    if (!$(e.target).closest('.sidebar-biblio, #sidebarCollapse').length) {
                        $('.sidebar-biblio').removeClass('active');
                        $('.main-content-biblio').removeClass('active');
                    }
                }
            });

            // Close sidebar after clicking a link on mobile
            $('.sidebar-biblio .nav-link, .sidebar-biblio .logout-link').on('click', function () {
                if ($(window).width() <= 768) {
                    $('.sidebar-biblio').removeClass('active');
                    $('.main-content-biblio').removeClass('active');
                }
            });
        });
    </script>
</body>

</html>