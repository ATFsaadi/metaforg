-- Base de données forum1
CREATE DATABASE IF NOT EXISTS forum1;
USE forum1;

-- Table des utilisateurs
CREATE TABLE users (
    id_u INT AUTO_INCREMENT,
    login VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    mdp VARCHAR(255),
    lvl INT DEFAULT 0,
    IP VARCHAR(15),
    PRIMARY KEY (id_u)
);

-- Table des albums
CREATE TABLE album (
    id_alb INT AUTO_INCREMENT,
    nom_alb VARCHAR(50),
    PRIMARY KEY (id_alb)
);

-- Table des images
CREATE TABLE images (
    id_img INT AUTO_INCREMENT,
    nom VARCHAR(50),
    chemin VARCHAR(255),
    date_img DATE,
    u_id INT,
    alb_id INT,
    PRIMARY KEY (id_img),
    FOREIGN KEY (u_id) REFERENCES users(id_u),
    FOREIGN KEY (alb_id) REFERENCES album(id_alb)
);

-- Table des commentaires
CREATE TABLE comment (
    id_c INT AUTO_INCREMENT,
    id_img INT,
    id_u INT,
    PRIMARY KEY (id_c),
    FOREIGN KEY (id_img) REFERENCES images(id_img),
    FOREIGN KEY (id_u) REFERENCES users(id_u)
);
CREATE TABLE `envoyer` (
  `id_exp` int(11) NOT NULL,
  `id_recept` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `date_env` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des publications (pour le fil d'actualité)
CREATE TABLE `publications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_u`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Insertion d'un utilisateur administrateur
INSERT INTO users (login, email, mdp, lvl) VALUES
('admin', 'admin@metaforge.com', SHA1('admin123'), 1);*


-- trable amis
CREATE TABLE amis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    ami_id INT NOT NULL,
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES users(id_u),
    CONSTRAINT fk_ami FOREIGN KEY (ami_id) REFERENCES users(id_u),
    
    UNIQUE KEY unique_ami_pair (utilisateur_id, ami_id)
);