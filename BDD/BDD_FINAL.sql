
DROP DATABASE IF EXISTS forum1;
CREATE DATABASE IF NOT EXISTS forum1;
USE forum1;

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
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id_u` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `mdp` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lvl` int DEFAULT '0',
  `IP` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `birthdate` date NOT NULL,
  `genre` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `langue` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pays` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo_profil` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_u`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




INSERT INTO users (login, prenom, nom, email, mdp, lvl, IP, birthdate, genre, langue, pays, photo_profil) VALUES
('jdoe', 'John', 'Doe', 'john.doe@example.com', 'hashed_mdp1', 1, '192.168.1.2', '1990-05-14', 'Homme', 'en', 'USA', NULL),
('mli', 'Mei', 'Li', 'mei.li@example.cn', 'hashed_mdp2', 0, '192.168.1.3', '1993-07-22', 'Femme', 'zh', 'Chine', NULL),
('alebrun', 'Alain', 'Lebrun', 'alain.lebrun@example.fr', 'hashed_mdp3', 2, '192.168.1.4', '1985-02-11', 'Homme', 'fr', 'France', NULL),
('kslimane', 'Karim', 'Slimane', 'karim.slimane@example.dz', 'hashed_mdp4', 1, '41.110.23.45', '1992-09-10', 'Homme', 'ar', 'Algérie', NULL),
('samina_b', 'Samina', 'Boualem', 'samina.b@example.dz', 'hashed_mdp5', 0, '41.110.54.12', '1996-03-18', 'Femme', 'fr', 'Algérie', NULL),
('hamedk', 'Hamed', 'Kallel', 'hamed.k@example.tn', 'hashed_mdp6', 1, '102.23.45.67', '1988-12-25', 'Homme', 'ar', 'Tunisie', NULL),
('nadia_cha', 'Nadia', 'Chatti', 'nadia.chatti@example.tn', 'hashed_mdp7', 0, '102.12.34.56', '1995-07-07', 'Femme', 'fr', 'Tunisie', NULL),
('koffik', 'Koffi', 'Konan', 'koffi.konan@example.ci', 'hashed_mdp8', 1, '102.124.23.12', '1991-11-20', 'Homme', 'fr', 'Côte d\'Ivoire', NULL),
('ama_m', 'Ama', 'Meite', 'ama.meite@example.ci', 'hashed_mdp9', 0, '102.124.34.56', '1994-04-15', 'Femme', 'fr', 'Côte d\'Ivoire', NULL),
('moussam', 'Moussa', 'Maiga', 'moussa.maiga@example.ml', 'hashed_mdp10', 1, '102.145.67.89', '1990-06-09', 'Homme', 'fr', 'Mali', NULL),
('fatou_d', 'Fatou', 'Diallo', 'fatou.diallo@example.ml', 'hashed_mdp11', 0, '102.145.90.12', '1997-08-30', 'Femme', 'fr', 'Mali', NULL);

