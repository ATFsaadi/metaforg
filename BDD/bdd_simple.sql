-- Supprimer la base de données existante si elle existe déjà
DROP DATABASE IF EXISTS forum1;

-- Création de la base de données avec encodage UTF-8mb4 pour gérer les caractères spéciaux
CREATE DATABASE forum1 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE forum1;

-- Création de la table des utilisateurs (stocke les informations des utilisateurs)
CREATE TABLE users (
    id_u INT AUTO_INCREMENT PRIMARY KEY,       -- Identifiant unique de l'utilisateur
    login VARCHAR(50) NOT NULL,                -- Nom d'utilisateur
    email VARCHAR(100) UNIQUE NOT NULL,        -- Adresse email unique
    mdp VARCHAR(255) NOT NULL,                 -- Mot de passe haché (SHA1)
    lvl INT DEFAULT 0,                         -- Niveau d'accès (0 = standard, 1 = admin, etc.)
    IP VARCHAR(15),                            -- Adresse IP de l'utilisateur
    age INT,                                   -- Âge de l'utilisateur
    langue VARCHAR(10),                        -- Langue de l'utilisateur
    pays VARCHAR(50)                           -- Pays de l'utilisateur
);

-- Création de la table des albums (stocke les albums d'images)
CREATE TABLE album (
    id_alb INT AUTO_INCREMENT PRIMARY KEY,     -- Identifiant unique de l'album
    nom_alb VARCHAR(50) NOT NULL               -- Nom de l'album
);

-- Création de la table des images (stocke les images avec leur chemin et utilisateur)
CREATE TABLE images (
    id_img INT AUTO_INCREMENT PRIMARY KEY,     -- Identifiant unique de l'image
    nom VARCHAR(50),                           -- Nom du fichier image
    chemin VARCHAR(255),                       -- Chemin d'accès au fichier
    date_img DATE,                             -- Date d'ajout de l'image
    u_id INT,                                  -- Référence à l'utilisateur (propriétaire de l'image)
    alb_id INT,                                -- Référence à l'album auquel l'image appartient
    FOREIGN KEY (u_id) REFERENCES users(id_u) ON DELETE CASCADE, -- Lien avec la table users
    FOREIGN KEY (alb_id) REFERENCES album(id_alb) ON DELETE SET NULL -- Lien avec la table album
);

-- Création de la table des commentaires (pour que les utilisateurs puissent commenter les images)
CREATE TABLE comment (
    id_c INT AUTO_INCREMENT PRIMARY KEY,       -- Identifiant unique du commentaire
    id_img INT,                                -- Référence à l'image concernée
    id_u INT,                                  -- Référence à l'utilisateur qui a posté le commentaire
    contenu TEXT,                              -- Contenu du commentaire
    date_c DATETIME DEFAULT CURRENT_TIMESTAMP, -- Date et heure du commentaire
    FOREIGN KEY (id_img) REFERENCES images(id_img) ON DELETE CASCADE, -- Lien avec la table images
    FOREIGN KEY (id_u) REFERENCES users(id_u) ON DELETE CASCADE     -- Lien avec la table users
);

-- Création de la table des messages privés (pour que les utilisateurs puissent envoyer des messages)
CREATE TABLE envoyer (
    id INT AUTO_INCREMENT PRIMARY KEY,         -- Identifiant unique du message
    id_exp INT NOT NULL,                       -- Identifiant de l'expéditeur
    id_recept INT NOT NULL,                    -- Identifiant du destinataire
    message TEXT,                              -- Contenu du message
    date_env DATETIME DEFAULT CURRENT_TIMESTAMP, -- Date et heure de l'envoi
    FOREIGN KEY (id_exp) REFERENCES users(id_u) ON DELETE CASCADE,  -- Lien avec la table users
    FOREIGN KEY (id_recept) REFERENCES users(id_u) ON DELETE CASCADE -- Lien avec la table users
);

-- Création de la table des relations d'amitié (pour gérer les demandes et les acceptations d'amis)
CREATE TABLE amis (
    id INT AUTO_INCREMENT PRIMARY KEY,         -- Identifiant unique de la relation
    utilisateur_id INT NOT NULL,               -- Identifiant de l'utilisateur qui demande l'ami
    ami_id INT NOT NULL,                       -- Identifiant de l'ami
    statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente', -- Statut de la demande
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date d'ajout de la demande
    CONSTRAINT fk_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES users(id_u) ON DELETE CASCADE, -- Lien avec la table users
    CONSTRAINT fk_ami FOREIGN KEY (ami_id) REFERENCES users(id_u) ON DELETE CASCADE, -- Lien avec la table users
    UNIQUE KEY unique_ami_pair (utilisateur_id, ami_id) -- Empêche les doublons
);

-- Insertion d'un utilisateur administrateur par défaut
INSERT INTO users (login, email, mdp, lvl)
VALUES ('admin', 'admin@metaforge.com', SHA1('admin123'), 1);

-- Création de la table des publications (pour gérer les messages/post des utilisateurs sur le forum)
CREATE TABLE publications (
    id_p INT AUTO_INCREMENT PRIMARY KEY,       -- Identifiant unique de la publication
    user_id INT NOT NULL,                      -- Identifiant de l'utilisateur ayant posté
    contenu TEXT NOT NULL,                     -- Contenu de la publication
    date DATETIME DEFAULT CURRENT_TIMESTAMP,   -- Date et heure de la publication
    FOREIGN KEY (user_id) REFERENCES users(id_u) ON DELETE CASCADE -- Lien avec la table users
);

-- Insertion de publications d'exemple pour l'utilisateur administrateur
INSERT INTO publications (user_id, contenu) VALUES
(1, 'Bienvenue sur le forum !'),
(1, 'Deuxième publication test');
