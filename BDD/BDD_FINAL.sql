
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
('admin', 'Admin', 'Root', 'admin@metaforg.com', '$2y$10$e0NR0YBk1pG9m8u6X6jz2eEwiNjCm99xzCwVUZqReLZT0XHu0zB6e', 2, '127.0.0.1', '1980-01-01', 'Homme', 'fr', 'France', NULL),
('leo', 'Leo', 'Benslimane', 'leo@metaforg.com', '$2y$10$UuL0WhmM8EIo4KKC0z3pVe/Qk76rmYylEyoXzDK7XVk/J4ph9FPE6', 0, '41.201.10.10', '1992-03-15', 'Homme', 'fr', 'Algérie', NULL),
('atef', 'Atef', 'Khaled', 'atef@metaforg.com', '$2y$10$43PoWKIwGqQz2TVn5uOwZ.qn3tOCB46fVfzD2eokMO4ZTnrm0PYiW', 0, '41.202.10.11', '1990-07-10', 'Homme', 'ar', 'Algérie', NULL),
('ibra', 'Ibra', 'Traore', 'ibra@metaforg.com', '$2y$10$9N3DqQFvQ4NlMkPvYGsuaeI2XkQXOlroGZ6kd7YflcprkDO2UDvKm', 0, '102.140.20.20', '1989-09-25', 'Homme', 'fr', 'Mali', NULL),
('daniel', 'Daniel', 'Okito', 'daniel@metaforg.com', '$2y$10$WgWk0Z6f3Tp7HsIT4UP9pu0Su9aOx5TJgKwN9J5njBpO9cGh9q4Ye', 0, '102.145.20.30', '1991-11-11', 'Homme', 'fr', 'Congo', NULL),
('amani', 'Amani', 'Zitouni', 'amani@metaforg.com', '$2y$10$wXTxzRnmTbP7Q2kI3gP2EuPTG9paTGqDTjFtXQEl0QYK9VX3yoQ4y', 0, '102.150.22.22', '1995-05-05', 'Femme', 'ar', 'Tunisie', NULL);




INSERT INTO users (login, prenom, nom, email, mdp, lvl, IP, birthdate, genre, langue, pays, photo_profil) VALUES
('maria', 'Maria', 'Gonzalez', 'maria@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '190.23.44.12', '1993-06-12', 'Femme', 'es', 'Espagne', NULL),
('li', 'Li', 'Wei', 'liwei@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '103.29.89.10', '1988-12-01', 'Homme', 'zh', 'Chine', NULL),
('hiro', 'Hiro', 'Tanaka', 'hiro@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '122.101.3.5', '1990-08-20', 'Homme', 'ja', 'Japon', NULL),
('nina', 'Nina', 'Kowalski', 'nina@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '83.23.54.44', '1992-04-10', 'Femme', 'pl', 'Pologne', NULL),
('john', 'John', 'Doe', 'john@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '66.102.1.1', '1985-03-03', 'Homme', 'en', 'États-Unis', NULL),
('amina', 'Amina', 'Yusuf', 'amina@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '105.112.122.1', '1994-09-09', 'Femme', 'en', 'Nigeria', NULL),
('lucas', 'Lucas', 'Silva', 'lucas@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '200.147.35.10', '1991-05-21', 'Homme', 'pt', 'Brésil', NULL),
('sara', 'Sara', 'Bianchi', 'sara@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '151.56.12.5', '1990-11-23', 'Femme', 'it', 'Italie', NULL),
('david', 'David', 'Nguyen', 'david@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '118.69.100.10', '1993-03-17', 'Homme', 'vi', 'Vietnam', NULL),
('zara', 'Zara', 'Mohamed', 'zara@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '41.250.90.20', '1996-07-07', 'Femme', 'ar', 'Maroc', NULL),
('emily', 'Emily', 'Clark', 'emily@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '81.2.69.142', '1992-10-10', 'Femme', 'en', 'Royaume-Uni', NULL),
('karim', 'Karim', 'El Amrani', 'karim@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '41.243.22.5', '1987-02-14', 'Homme', 'ar', 'Égypte', NULL),
('emma', 'Emma', 'Dubois', 'emma@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '92.184.105.5', '1995-01-01', 'Femme', 'fr', 'France', NULL),
('peter', 'Peter', 'Schmidt', 'peter@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '85.212.22.45', '1986-06-30', 'Homme', 'de', 'Allemagne', NULL),
('julie', 'Julie', 'Andersson', 'julie@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '193.11.10.20', '1991-12-24', 'Femme', 'sv', 'Suède', NULL),
('raj', 'Raj', 'Kapoor', 'raj@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '115.69.88.5', '1989-08-18', 'Homme', 'hi', 'Inde', NULL),
('sophia', 'Sophia', 'Papadopoulos', 'sophia@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '94.71.22.11', '1990-10-05', 'Femme', 'el', 'Grèce', NULL),
('ali', 'Ali', 'Rashid', 'ali@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '176.205.10.10', '1988-05-15', 'Homme', 'ar', 'Arabie Saoudite', NULL);