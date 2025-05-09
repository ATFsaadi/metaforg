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

