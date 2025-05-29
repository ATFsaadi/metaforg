
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
('amani', 'Amani', 'Zitouni', 'amani@metaforg.com', '$2y$10$wXTxzRnmTbP7Q2kI3gP2EuPTG9paTGqDTjFtXQEl0QYK9VX3yoQ4y', 0, '102.150.22.22', '1995-05-05', 'Femme', 'ar', 'Tunisie', NULL),
('nour', 'Nour', 'Belkacem', 'nour@metaforg.com', '$2y$10$SNuKCFVEd/19R3h.R27TMOXwI5wVyKFOkWZrAO4n6w6vpgl0/I5eO', 0, '41.101.1.1', '1994-06-06', 'Femme', 'fr', 'Algérie', NULL),
('hassan', 'Hassan', 'Yahia', 'hassan@metaforg.com', '$2y$10$xRqAlXQr/ypPRq5b9kwDae0hq8nmXq0szBq/6ldxIkvcuuAq7t/yS', 0, '41.101.1.2', '1987-12-24', 'Homme', 'ar', 'Maroc', NULL),
('sami', 'Sami', 'Ben Amor', 'sami@metaforg.com', '$2y$10$0hmwTxu5c2cKg7iPSNlfHOXxOm/nfM1whV1v0D3aCHu6svhc3fWiW', 0, '102.100.1.3', '1996-03-18', 'Homme', 'fr', 'Tunisie', NULL),
('yasmine', 'Yasmine', 'Lahlou', 'yasmine@metaforg.com', '$2y$10$hEzFzj2QfEIVF/xY5HZt3O6WLyWlfS8xMziup31P2G8y7MJQU8K5e', 0, '102.100.1.4', '1993-08-08', 'Femme', 'fr', 'Maroc', NULL),
('fatima', 'Fatima', 'Bamba', 'fatima@metaforg.com', '$2y$10$7Fe8KxKKmuZzPbmCW58VxePka77R3EbDjhG6czGH8eOdGZlgdKU1y', 0, '102.100.1.5', '1992-10-10', 'Femme', 'fr', 'Côte d\'Ivoire', NULL),
('abdou', 'Abdou', 'Camara', 'abdou@metaforg.com', '$2y$10$C0iF60pt9Bxyld8shJKV3OGEAlYrT6PzY2wLl97mMPQSOoJWQ5qny', 0, '102.100.1.6', '1991-01-01', 'Homme', 'fr', 'Mali', NULL),
('rania', 'Rania', 'Bouzar', 'rania@metaforg.com', '$2y$10$8wLUM/xy9vgFgV/C82N5UuPpr/tM2DbDv7TtNyRkNGyTJUHfw19ti', 0, '102.100.1.7', '1998-09-09', 'Femme', 'fr', 'Algérie', NULL),
('ali', 'Ali', 'Ziani', 'ali@metaforg.com', '$2y$10$M4qWcSzn3rRJLRVvxPhGeu5e5/s5NLH1LdA4EVcQptOq0UlUdkdyK', 0, '102.100.1.8', '1989-04-04', 'Homme', 'ar', 'Algérie', NULL),
('selma', 'Selma', 'Nefzi', 'selma@metaforg.com', '$2y$10$xJQ3JzvExLqLSp.VQmq5zOMzTzqGZ.3m8XxRavqN8P3Orqbcqflbu', 0, '102.100.1.9', '1997-02-02', 'Femme', 'fr', 'Tunisie', NULL),
('omar', 'Omar', 'Boukhelifa', 'omar@metaforg.com', '$2y$10$1FVqGKrwhjW8oW45hRwi1uLgQ/9OomkqUv7INIK6yDcH3LdrDhL7m', 0, '102.100.2.1', '1986-06-06', 'Homme', 'ar', 'Algérie', NULL),
('nadia', 'Nadia', 'Sow', 'nadia@metaforg.com', '$2y$10$zWc43oDtrcPAqA4oCGKICeWryqn4r3Fk.xmYiykgCrRkBxk5v9DoG', 0, '102.100.2.2', '1990-10-10', 'Femme', 'fr', 'Sénégal', NULL),
('amine', 'Amine', 'Tounsi', 'amine@metaforg.com', '$2y$10$HlxiN1pmIg0wQZmtWyHRmeB44YP1VRV5H.FPjGkD3tpOyKl1ejp4i', 0, '102.100.2.3', '1993-03-03', 'Homme', 'fr', 'Tunisie', NULL),
('layla', 'Layla', 'Mbaye', 'layla@metaforg.com', '$2y$10$T1g/1iZ50VwA4eGSE/OUpeQqO1vFOz0DJxVBuTh/y4EK0aU1DsYya', 0, '102.100.2.4', '1994-04-04', 'Femme', 'fr', 'Sénégal', NULL),
('karim', 'Karim', 'Sankara', 'karim@metaforg.com', '$2y$10$V3x5OdtmzOPG1dMrrleqz.zk6DNWVKSwoF/GcBDJhYK03P4C7gVfq', 0, '102.100.2.5', '1992-05-05', 'Homme', 'fr', 'Burkina Faso', NULL),
('salim', 'Salim', 'Amrani', 'salim@metaforg.com', '$2y$10$Gm.P4x5Lyik8mf85nlEVJek96WCDU9JsFj5MvQ2t7I0uEim6ccwAO', 0, '102.100.2.6', '1985-07-07', 'Homme', 'ar', 'Algérie', NULL),
('lina', 'Lina', 'Abidi', 'lina@metaforg.com', '$2y$10$JxmC3pNxGLbYs.J6cWMCJeRHkjSj0y9/k9hGMaNpVxScm3RzPq6Pi', 0, '102.100.2.7', '1991-12-12', 'Femme', 'fr', 'Tunisie', NULL),
('zakaria', 'Zakaria', 'Ndao', 'zakaria@metaforg.com', '$2y$10$fDKq5Np2HK6S1tTse9OsXeeDcQ5vPo.7BTAOZc/NJtuU0.JTx4HxS', 0, '102.100.2.8', '1996-01-01', 'Homme', 'fr', 'Sénégal', NULL),
('samir', 'Samir', 'Ouattara', 'samir@metaforg.com', '$2y$10$C1iPM3LsMwg9t0UqRwYBZeHxwxWvo5zSxDfyPpA/NxlLZbGxQ74m6', 0, '102.100.2.9', '1993-11-11', 'Homme', 'fr', 'Côte d\'Ivoire', NULL),
('noura', 'Noura', 'Kouyate', 'noura@metaforg.com', '$2y$10$Cv5kOq0vgY1Mq8YvHnGrfeqX3N65AZDZnuXuzqGxLwmuMImq4MlAe', 0, '102.100.3.0', '1990-02-02', 'Femme', 'fr', 'Mali', NULL);







 INSERT INTO stories (user_id, image_path, expire_at) VALUES (1, 'story_7.jpg', NOW() + INTERVAL 1 DAY);