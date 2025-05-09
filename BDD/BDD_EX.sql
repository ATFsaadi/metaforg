DROP TABLE IF EXISTS `album`;
CREATE TABLE IF NOT EXISTS `album` (
  `id_alb` int NOT NULL AUTO_INCREMENT,
  `nom_alb` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_alb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `album`;
CREATE TABLE IF NOT EXISTS `album` (
  `id_alb` int NOT NULL AUTO_INCREMENT,
  `nom_alb` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_alb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `amis`;
CREATE TABLE IF NOT EXISTS `amis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `ami_id` int NOT NULL,
  `statut` enum('en_attente','accepte','refuse') COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ami_pair` (`utilisateur_id`,`ami_id`),
  KEY `fk_ami` (`ami_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `amis` (`id`, `utilisateur_id`, `ami_id`, `statut`, `date_ajout`) VALUES
(1, 22, 1, 'en_attente', '2025-05-08 21:07:04'),
(2, 22, 23, 'accepte', '2025-05-08 21:07:33'),
(3, 23, 5, 'en_attente', '2025-05-08 21:12:07'),
(4, 23, 8, 'en_attente', '2025-05-08 21:15:19'),
(5, 23, 12, 'en_attente', '2025-05-08 21:17:01'),
(6, 23, 2, 'en_attente', '2025-05-08 21:18:18'),
(7, 23, 17, 'en_attente', '2025-05-08 21:18:42'),
(8, 23, 7, 'en_attente', '2025-05-08 21:20:46'),
(9, 23, 14, 'en_attente', '2025-05-08 21:21:01'),
(10, 23, 15, 'en_attente', '2025-05-08 21:22:08'),
(11, 23, 9, 'en_attente', '2025-05-08 21:23:06'),
(12, 23, 20, 'en_attente', '2025-05-08 21:31:18'),
(13, 23, 11, 'en_attente', '2025-05-08 21:31:37'),
(14, 23, 19, 'en_attente', '2025-05-08 21:32:13'),
(15, 23, 10, 'en_attente', '2025-05-08 22:00:18'),
(16, 23, 13, 'en_attente', '2025-05-08 22:02:37'),
(17, 22, 4, 'en_attente', '2025-05-09 05:57:10'),
(18, 22, 21, 'en_attente', '2025-05-09 05:59:49'),
(19, 22, 8, 'en_attente', '2025-05-09 07:01:00');

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id_c` int NOT NULL AUTO_INCREMENT,
  `id_img` int DEFAULT NULL,
  `id_u` int DEFAULT NULL,
  `contenu` text COLLATE utf8mb4_general_ci,
  `date_c` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_c`),
  KEY `id_img` (`id_img`),
  KEY `id_u` (`id_u`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `envoyer`;
CREATE TABLE IF NOT EXISTS `envoyer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_exp` int NOT NULL,
  `id_recept` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `date_env` datetime DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_exp` (`id_exp`),
  KEY `id_recept` (`id_recept`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `envoyer` (`id`, `id_exp`, `id_recept`, `message`, `date_env`, `lu`) VALUES
(29, 23, 22, 'hello', '2025-05-09 09:02:22', 1),
(28, 22, 23, 'hello', '2025-05-08 13:12:09', 1);

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id_img` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chemin` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_img` date DEFAULT NULL,
  `u_id` int DEFAULT NULL,
  `alb_id` int DEFAULT NULL,
  PRIMARY KEY (`id_img`),
  KEY `u_id` (`u_id`),
  KEY `alb_id` (`alb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` enum('message','invitation') COLLATE utf8mb4_general_ci NOT NULL,
  `contenu` text COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `lu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `photos_profil`;
CREATE TABLE IF NOT EXISTS `photos_profil` (
  `id_photo` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `photo_nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_upload` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_photo`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `publications`;
CREATE TABLE IF NOT EXISTS `publications` (
  `id_p` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `contenu` text COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  `media` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_p`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `publications` (`id_p`, `user_id`, `contenu`, `date`, `media`, `image`) VALUES
(34, 22, 'azd', '2025-05-09 09:01:43', NULL, 'img_681da857ba9e7.png'),
(33, 22, 'hello word', '2025-05-08 13:10:24', NULL, 'img_681c91209a4a5.png');

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_u` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `mdp` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lvl` int DEFAULT '0',
  `IP` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `langue` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pays` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo_profil` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_u`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id_u`, `login`, `email`, `mdp`, `lvl`, `IP`, `age`, `langue`, `pays`, `photo_profil`) VALUES
(1, 'admin', 'admin@metaforge.com', 'f865b53623b121fd34ee5426c792e5c33af8c227', 1, NULL, NULL, NULL, NULL, NULL),
(2, 'aminedz', 'amine.dz@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.1', 25, 'ar', 'Algeria', NULL),
(3, 'oumarmali', 'oumar.mali@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.2', 30, 'fr', 'Mali', NULL),
(4, 'mamadouci', 'mamadou.ci@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 1, '192.168.0.3', 28, 'fr', 'Côte d\'Ivoire', NULL),
(5, 'samirtunisie', 'sami.tunisie@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.4', 27, 'ar', 'Tunisia', NULL),
(6, 'johnuk', 'john.uk@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 1, '192.168.0.5', 35, 'en', 'United Kingdom', NULL),
(7, 'johndoeusa', 'john.doe.usa@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.6', 40, 'en', 'USA', NULL),
(8, 'peterfr', 'peter.fr@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.7', 32, 'fr', 'France', NULL),
(9, 'sofiade', 'sofia.de@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 1, '192.168.0.8', 29, 'de', 'Germany', NULL),
(10, 'caroluk', 'carol.uk@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.9', 22, 'en', 'United Kingdom', NULL),
(11, 'federicoit', 'federico.it@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 1, '192.168.0.10', 33, 'it', 'Italy', NULL),
(12, 'mariaportugal', 'maria.portugal@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.11', 26, 'pt', 'Portugal', NULL),
(13, 'klaussweden', 'klaussweden@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.12', 50, 'fr', 'Sweden', NULL),
(14, 'larissaesp', 'larissa.esp@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.13', 31, 'es', 'Spain', NULL),
(15, 'stefanogreece', 'stefano.greece@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 1, '192.168.0.14', 28, 'el', 'Greece', NULL),
(16, 'nicolasbelgium', 'nicolas.belgium@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.15', 27, 'fr', 'Belgium', NULL),
(17, 'anaczech', 'ana.czech@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.16', 25, 'cs', 'Czech Republic', NULL),
(18, 'emilfinland', 'emil.finland@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.17', 29, 'fi', 'Finland', NULL),
(19, 'aliosenegal', 'ali.senegal@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.18', 26, 'fr', 'Senegal', NULL),
(20, 'yassinealgerie', 'yassine.algerie@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.19', 32, 'ar', 'Algeria', NULL),
(21, 'fatimamaurice', 'fatima.maurice@example.com', 'cbfdac6008f9cab4083784cbd1874f76618d2a97', 0, '192.168.0.20', 30, 'fr', 'Mauritius', NULL),
(22, 'admin', 'admin@metaforg.com', '9cf95dacd226dcf43da376cdb6cbba7035218921', 0, NULL, 25, 'fr', 'france', NULL),
(23, 'leo', 'leo@gmail.com', '9cf95dacd226dcf43da376cdb6cbba7035218921', 0, NULL, 0, 'fr', '', NULL);

COMMIT;
