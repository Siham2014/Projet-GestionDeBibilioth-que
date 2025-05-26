-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 21 mai 2025 à 07:30
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `online-library`
--

-- --------------------------------------------------------

--
-- Structure de la table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(13) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `filiere` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `available_quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT 'default_book.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `description`, `filiere`, `quantity`, `available_quantity`, `created_at`, `updated_at`, `image`) VALUES
(12, 'Clean Code', 'Robert C. Martin', '9780132350884', 'Un guide incontournable pour apprendre à écrire un code propre, maintenable et efficace. Ce livre aborde les principes de qualité logicielle et les bonnes pratiques de développement.\r\n\r\n', NULL, 12, 11, '2025-05-21 05:21:12', '2025-05-21 05:25:20', 'book_682d62c7f40d1.jpeg'),
(13, 'Introduction to Algorithms', 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein', '9780262033848', 'Une référence mondiale en algorithmique, couvrant des sujets de base et avancés avec une rigueur mathématique. Idéal pour les étudiants en informatique.', NULL, 6, 6, '2025-05-21 05:22:47', '2025-05-21 05:28:48', 'book_682d6327add57.jpeg'),
(15, 'The Pragmatic Programmer', 'Andrew Hunt, David Thomas', '9780201616224', 'Un livre culte pour tout développeur, qui couvre des conseils pratiques sur le code, la gestion de projet et la mentalité à adopter dans le développement logiciel.', NULL, 1, 1, '2025-05-21 05:24:54', '2025-05-21 05:24:54', 'book_682d63a6cf815.jpeg');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `photo` varchar(255) DEFAULT 'default-avatar.png',
  `telephone` char(10) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('etudiant','admin') NOT NULL DEFAULT 'etudiant',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `photoprofil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`id`, `nom`, `prenom`, `email`, `photo`, `telephone`, `date_naissance`, `username`, `password`, `role`, `is_active`, `verification_token`, `email_verified_at`, `reset_token`, `reset_token_expiry`, `date_inscription`, `updated_at`, `photoprofil`) VALUES
(1, 'Admin', 'Principal', 'admin@example.com', 'default-avatar.png', '0600000000', '1990-01-01', 'admin', '$2y$10$Y5hRmLISEytqj.lqcuJTKOIcx3Yn6JnPJwDZlCwqdVOxfvSQrpMLK', 'admin', 1, NULL, '2025-05-13 17:39:50', NULL, NULL, '2025-05-13 17:39:50', '2025-05-14 14:24:48', NULL),
(13, 'gaiti', 'aya', 'aya.gaiti.7@gmail.com', 'default-avatar.png', '0666821518', '2004-04-02', 'alyassou_siham87', '$2y$10$MvePwGzR50o446z1dCQn6elChG50t3Di0OY1XbdsOfzVQ42iqojyW', 'etudiant', 1, NULL, '2025-05-13 16:00:46', '34d13052020bc80f2b0fc670a9d9a05e2065ab687a82c1079c4260a0d01e6d6b', '2025-05-13 18:02:04', '2025-05-13 16:00:16', '2025-05-21 05:15:47', NULL),
(16, 'haiti', 'aya', 'siham.alyassoul@etu.uae.ac.ma', 'default-avatar.png', '0526889599', '2005-02-02', 'Siham_578', '$2y$10$e.g2d5aRlI2rf0IztXz7J.FmSEMrAzGKR3UJxwzOyQy/1hgoiMh8e', 'etudiant', 0, 'b54fabf4bf20b77eb3b8b849944d60b7', NULL, NULL, NULL, '2025-05-14 18:51:45', '2025-05-14 16:51:45', NULL),
(17, 'Al yassoul', 'Siham', 'alyassoulsiham2004@gmail.com', 'default-avatar.png', '0655667788', '2004-03-23', 'Siham_12', '$2y$10$ngZ4.tHPaXBwNP/cAmDc7u4omt5R4N8QPn3/is4816gs/wyBXSir6', 'etudiant', 1, NULL, '2025-05-14 19:11:45', '2e675f20ac62347e84cd08aeac712aea216b32ad0f14dfd37e23371cc70ea5bf', '2025-05-14 22:05:25', '2025-05-14 19:11:23', '2025-05-21 05:15:03', NULL),
(22, 'Oulhadj', 'Mohammed', 'med300007@gmail.com', 'default-avatar.png', '0687005980', '2025-05-05', 'mohaasdt', '$2y$10$PYtHNfWNt04LlR2wgrxwhO6hq.8/ha7mZfkpwOXZCItvaBCZeqmZq', 'etudiant', 1, NULL, '2025-05-19 01:56:58', NULL, NULL, '2025-05-19 01:56:46', '2025-05-19 01:56:58', 'profil_682a8fdde37bc.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `liste_noire`
--

CREATE TABLE `liste_noire` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `motif` enum('livre_perdu','livre_endommage') NOT NULL,
  `details` text DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp(),
  `levee_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `etudiant_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `etudiant_id`, `message`, `type`, `date_creation`, `lu`) VALUES
(25, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:13:32', 1),
(26, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:13:33', 1),
(27, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:37:19', 1),
(28, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:44:50', 1),
(29, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:44:52', 1),
(30, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:44:53', 1),
(31, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:46:10', 1),
(32, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:46:12', 1),
(33, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 05:46:43', 1),
(34, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 05:46:58', 1),
(35, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:49:47', 1),
(36, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 05:49:49', 1),
(37, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:49:50', 1),
(38, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:52:14', 1),
(39, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:52:16', 1),
(40, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:52:21', 1),
(41, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:57:52', 1),
(42, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:57:53', 1),
(43, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 05:57:55', 1),
(44, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 06:06:18', 1),
(45, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 06:06:20', 1),
(46, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 06:06:58', 1),
(47, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 06:07:00', 1),
(48, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 20:42:57', 1),
(49, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 20:43:00', 1),
(50, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 20:43:01', 1),
(51, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 20:59:28', 1),
(52, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 21:11:43', 1),
(53, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 21:12:44', 1),
(54, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 21:12:46', 1),
(55, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 21:12:47', 1),
(56, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 21:16:02', 1),
(57, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 21:22:26', 1),
(58, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 21:22:27', 1),
(59, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 21:22:28', 1),
(60, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 21:30:21', 1),
(61, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 21:30:42', 1),
(62, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 21:30:43', 1),
(63, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 22:45:31', 1),
(64, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 23:11:39', 1),
(65, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 23:11:40', 1),
(66, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 23:11:41', 1),
(67, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-19 23:53:20', 1),
(68, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-19 23:53:21', 1),
(69, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 00:00:30', 1),
(70, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 00:56:10', 1),
(71, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 00:57:44', 1),
(72, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 00:57:47', 1),
(73, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 00:58:56', 1),
(74, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:32:23', 1),
(75, 13, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-20 01:32:25', 1),
(76, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 01:32:29', 1),
(77, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:38:29', 1),
(78, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:38:30', 1),
(79, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 01:38:39', 1),
(80, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 01:47:38', 1),
(81, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:48:49', 1),
(82, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 01:49:28', 1),
(83, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:51:41', 1),
(84, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:57:14', 0),
(85, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:57:15', 0),
(86, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 01:57:36', 0),
(87, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 01:57:47', 0),
(88, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 01:59:20', 0),
(89, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 01:59:24', 0),
(90, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:01:19', 0),
(91, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:01:20', 0),
(92, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 02:01:34', 0),
(93, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 02:04:56', 0),
(94, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:05:15', 0),
(95, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:25:46', 0),
(96, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:30:34', 0),
(97, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:30:34', 0),
(98, 13, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 02:30:40', 0),
(99, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:33:20', 0),
(100, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 02:36:47', 0),
(101, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 03:17:41', 0),
(102, 13, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 03:19:51', 0),
(103, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 07:08:28', 1),
(104, 17, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-20 07:08:29', 1),
(105, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 07:24:57', 1),
(106, 17, 'Merci d\'avoir rendu le livre en bon état !', 'remerciement', '2025-05-20 07:25:10', 1),
(107, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 07:36:01', 1),
(108, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 07:47:04', 1),
(109, 17, 'L\'administration a levé votre sanction, vous pouvez à nouveau réserver.', 'levee_sanction', '2025-05-20 07:55:34', 1),
(110, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 07:56:20', 1),
(111, 17, 'L\'administration a levé votre sanction, vous pouvez à nouveau réserver.', 'levee_sanction', '2025-05-20 07:57:22', 1),
(112, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 08:16:12', 1),
(113, 17, 'L\'administration a levé votre sanction, vous pouvez à nouveau réserver.', 'levee_sanction', '2025-05-20 08:17:11', 1),
(114, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-20 08:58:51', 1),
(115, 17, 'Votre réservation a été acceptée. Veuillez venir récupérer le livre à la bibliothèque.', 'acceptation', '2025-05-21 07:28:46', 1),
(116, 17, 'Votre réservation a été refusée par l\'administrateur.', 'refus', '2025-05-21 07:28:48', 1);

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `id` int(11) NOT NULL,
  `etudiant_id` int(11) DEFAULT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date_reclamation` datetime DEFAULT current_timestamp(),
  `statut` varchar(50) DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`id`, `etudiant_id`, `sujet`, `message`, `date_reclamation`, `statut`) VALUES
(12, 17, 'Justification de retard', 'j\'ecrire ce message pour ....', '2025-05-21 07:26:34', 'en_attente');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(10) UNSIGNED NOT NULL,
  `etudiant_id` int(10) UNSIGNED NOT NULL,
  `book_id` int(11) NOT NULL,
  `date_reservation` date NOT NULL,
  `date_limite` date NOT NULL,
  `statut` enum('en_attente','acceptee','refusee','emprunte','en_retard','perdu','rendu') NOT NULL DEFAULT 'en_attente',
  `etat_physique` varchar(50) DEFAULT NULL,
  `description_dommages` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `etudiant_id`, `book_id`, `date_reservation`, `date_limite`, `statut`, `etat_physique`, `description_dommages`) VALUES
(135, 17, 12, '2025-05-21', '2025-05-28', 'acceptee', NULL, NULL),
(136, 17, 13, '2025-05-21', '2025-05-28', 'refusee', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `liste_noire`
--
ALTER TABLE `liste_noire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etudiant_id` (`etudiant_id`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etudiant_id` (`etudiant_id`),
  ADD KEY `book_id` (`book_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `etudiant`
--
ALTER TABLE `etudiant`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `liste_noire`
--
ALTER TABLE `liste_noire`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `liste_noire`
--
ALTER TABLE `liste_noire`
  ADD CONSTRAINT `fk_liste_noire_user` FOREIGN KEY (`user_id`) REFERENCES `etudiant` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`);

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiant` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
