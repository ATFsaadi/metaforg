-- Suppression de la base si elle existe déjà
DROP DATABASE IF EXISTS forum1;

-- Création de la base avec un encodage UTF-8 universel
CREATE DATABASE forum1 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE forum1;

-- Création de la table des utilisateurs
CREATE TABLE users (
    id_u INT AUTO_INCREMENT PRIMARY KEY,                   -- ID unique de l'utilisateur
    login VARCHAR(50) NOT NULL,                            -- Nom d'utilisateur
    email VARCHAR(100) UNIQUE NOT NULL,                    -- Adresse email unique
    mdp VARCHAR(255) NOT NULL,                             -- Mot de passe (haché)
    lvl INT DEFAULT 0,                                     -- Niveau d'accès (0 = standard, 1 = admin...)
    IP VARCHAR(15)                                         -- Adresse IP
);

-- Table des albums (regroupe les images)
CREATE TABLE album (
    id_alb INT AUTO_INCREMENT PRIMARY KEY,                 -- ID unique de l'album
    nom_alb VARCHAR(50) NOT NULL                           -- Nom de l'album
);

-- Table des images associées aux utilisateurs et albums
CREATE TABLE images (
    id_img INT AUTO_INCREMENT PRIMARY KEY,                 -- ID de l'image
    nom VARCHAR(50),                                       -- Nom du fichier
    chemin VARCHAR(255),                                   -- Chemin d'accès au fichier
    date_img DATE,                                         -- Date d'ajout
    u_id INT,                                              -- Référence à l'utilisateur
    alb_id INT,                                            -- Référence à l'album
    FOREIGN KEY (u_id) REFERENCES users(id_u) ON DELETE CASCADE,       -- Supprime les images si l'utilisateur est supprimé
    FOREIGN KEY (alb_id) REFERENCES album(id_alb) ON DELETE SET NULL   -- Met alb_id à NULL si l'album est supprimé
);

-- Table des commentaires sur les images
CREATE TABLE comment (
    id_c INT AUTO_INCREMENT PRIMARY KEY,                   -- ID du commentaire
    id_img INT,                                            -- Image concernée
    id_u INT,                                              -- Utilisateur auteur du commentaire
    contenu TEXT,                                          -- Contenu du commentaire
    date_c DATETIME DEFAULT CURRENT_TIMESTAMP,             -- Date du commentaire
    FOREIGN KEY (id_img) REFERENCES images(id_img) ON DELETE CASCADE,
    FOREIGN KEY (id_u) REFERENCES users(id_u) ON DELETE CASCADE
);

-- Table des messages privés entre utilisateurs
CREATE TABLE envoyer (
    id INT AUTO_INCREMENT PRIMARY KEY,                     -- ID du message
    id_exp INT NOT NULL,                                   -- ID de l'expéditeur
    id_recept INT NOT NULL,                                -- ID du destinataire
    message TEXT,                                          -- Contenu du message
    date_env DATETIME DEFAULT CURRENT_TIMESTAMP,           -- Date d'envoi
    FOREIGN KEY (id_exp) REFERENCES users(id_u) ON DELETE CASCADE,
    FOREIGN KEY (id_recept) REFERENCES users(id_u) ON DELETE CASCADE
);

-- Table des relations d’amitié
CREATE TABLE amis (
    id INT AUTO_INCREMENT PRIMARY KEY,                     -- ID de la relation
    utilisateur_id INT NOT NULL,                           -- ID de l'utilisateur
    ami_id INT NOT NULL,                                   -- ID de son ami
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',  -- Statut de la demande
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,        -- Date de la demande
    CONSTRAINT fk_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES users(id_u) ON DELETE CASCADE,
    CONSTRAINT fk_ami FOREIGN KEY (ami_id) REFERENCES users(id_u) ON DELETE CASCADE,
    UNIQUE KEY unique_ami_pair (utilisateur_id, ami_id)    -- Empêche les doublons
);

-- Insertion d'un utilisateur administrateur de base
INSERT INTO users (login, email, mdp, lvl)
VALUES ('admin', 'admin@metaforge.com', SHA1('admin123'), 1);


-- table publications
CREATE TABLE publications (
    id_p INT AUTO_INCREMENT PRIMARY KEY,            -- ID de la publication
    user_id INT NOT NULL,                           -- Auteur de la publication (référence à users)
    contenu TEXT NOT NULL,                          -- Contenu de la publication
    date DATETIME DEFAULT CURRENT_TIMESTAMP,        -- Date de publication
    FOREIGN KEY (user_id) REFERENCES users(id_u) ON DELETE CASCADE
);

INSERT INTO publications (user_id, contenu) VALUES
(1, 'Bienvenue sur le forum !'),
(1, 'Deuxième publication test');


-- charger la base
INSERT INTO users (login, email, mdp, lvl, IP, age, langue, pays) VALUES
('user1', 'user1@example.com', 'mdp1', 1, '192.168.0.1', 25, 'fr', 'France'),
('user2', 'user2@example.com', 'mdp2', 2, '192.168.0.2', 30, 'en', 'USA'),
('user3', 'user3@example.com', 'mdp3', 1, '192.168.0.3', 22, 'es', 'Spain'),
('user4', 'user4@example.com', 'mdp4', 3, '192.168.0.4', 28, 'de', 'Germany'),
('user5', 'user5@example.com', 'mdp5', 0, '192.168.0.5', 21, 'it', 'Italy'),
('user6', 'user6@example.com', 'mdp6', 1, '192.168.0.6', 26, 'fr', 'Belgium'),
('user7', 'user7@example.com', 'mdp7', 2, '192.168.0.7', 27, 'en', 'Canada'),
('user8', 'user8@example.com', 'mdp8', 1, '192.168.0.8', 29, 'fr', 'France'),
('user9', 'user9@example.com', 'mdp9', 3, '192.168.0.9', 24, 'pt', 'Portugal'),
('user10', 'user10@example.com', 'mdp10', 1, '192.168.0.10', 31, 'fr', 'Morocco'),
('algerien1', 'algerien@example.com', 'mdp11', 2, '192.168.10.1', 26, 'ar', 'Algeria'),
('tunisien1', 'tunisien@example.com', 'mdp12', 1, '192.168.10.2', 30, 'fr', 'Tunisia'),
('maltais1', 'maltais@example.com', 'mdp13', 0, '192.168.10.3', 27, 'lma', 'Malta'),
('user14', 'user14@example.com', 'mdp14', 3, '192.168.0.14', 32, 'en', 'UK'),
('user15', 'user15@example.com', 'mdp15', 1, '192.168.0.15', 20, 'en', 'Ireland'),
('user16', 'user16@example.com', 'mdp16', 2, '192.168.0.16', 33, 'de', 'Germany'),
('user17', 'user17@example.com', 'mdp17', 0, '192.168.0.17', 26, 'es', 'Mexico'),
('user18', 'user18@example.com', 'mdp18', 1, '192.168.0.18', 28, 'fr', 'France'),
('user19', 'user19@example.com', 'mdp19', 2, '192.168.0.19', 29, 'en', 'USA'),
('user20', 'user20@example.com', 'mdp20', 3, '192.168.0.20', 22, 'it', 'Italy');

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,                             -- L'utilisateur qui reçoit la notification
    type ENUM('message', 'invitation') NOT NULL,      -- Type de notification
    contenu TEXT NOT NULL,                            -- Le contenu de la notification
    date DATETIME DEFAULT CURRENT_TIMESTAMP,          -- La date de la notification
    lu BOOLEAN DEFAULT FALSE,                         -- Si la notification a été lue ou pas
    FOREIGN KEY (user_id) REFERENCES users(id_u)     -- Référence à l'utilisateur
);

