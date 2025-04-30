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

-- Insertion d'un utilisateur administrateur
INSERT INTO users (login, email, mdp, lvl) VALUES
('admin', 'admin@metaforge.com', SHA1('admin123'), 1);