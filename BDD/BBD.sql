SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE album;
TRUNCATE TABLE amis;
TRUNCATE TABLE commentaires;
TRUNCATE TABLE envoyer;
TRUNCATE TABLE images;
TRUNCATE TABLE likes;
TRUNCATE TABLE notifications;
TRUNCATE TABLE photos_profil;
TRUNCATE TABLE publications;
TRUNCATE TABLE stories;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;


INSERT INTO users (login, prenom, nom, email, mdp, lvl, IP, birthdate, genre, langue, pays, photo_profil) VALUES
('admin', 'Admin', 'Root', 'admin@metaforg.com', '$2y$10$e0NR0YBk1pG9m8u6X6jz2eEwiNjCm99xzCwVUZqReLZT0XHu0zB6e', 2, '127.0.0.1', '1980-01-01', 'Homme', 'fr', 'France', NULL),
('leo', 'Leo', 'lys', 'leo@metaforg.com', '$2y$10$UuL0WhmM8EIo4KKC0z3pVe/Qk76rmYylEyoXzDK7XVk/J4ph9FPE6', 0, '41.201.10.10', '1992-03-15', 'Homme', 'fr', 'Algérie', NULL),
('atef', 'Atef', 'saadi', 'atef@metaforg.com', '$2y$10$43PoWKIwGqQz2TVn5uOwZ.qn3tOCB46fVfzD2eokMO4ZTnrm0PYiW', 0, '41.202.10.11', '1990-07-10', 'Homme', 'ar', 'Algérie', NULL),
('ibra', 'Ibra', 'haidara', 'ibra@metaforg.com', '$2y$10$9N3DqQFvQ4NlMkPvYGsuaeI2XkQXOlroGZ6kd7YflcprkDO2UDvKm', 0, '102.140.20.20', '1989-09-25', 'Homme', 'fr', 'Mali', NULL),
('daniel', 'Daniel', 'daniel', 'daniel@metaforg.com', '$2y$10$WgWk0Z6f3Tp7HsIT4UP9pu0Su9aOx5TJgKwN9J5njBpO9cGh9q4Ye', 0, '102.145.20.30', '1991-11-11', 'Homme', 'fr', 'Congo', NULL),
('nina', 'Nina', 'yno', 'nina@metaforg.com', '$2y$10$5h6Ruwz5OJUF.sZcuUz4LuQeyReTpIg3B6v8aKhp/CThKkx.ZzP5O', 0, '83.23.54.44', '1992-04-10', 'Femme', 'pl', 'Pologne', NULL),
('amani', 'Amani', 'amani', 'amani@metaforg.com', '$2y$10$wXTxzRnmTbP7Q2kI3gP2EuPTG9paTGqDTjFtXQEl0QYK9VX3yoQ4y', 0, '102.150.22.22', '1995-05-05', 'Femme', 'ar', 'Tunisie', NULL),

('max', 'Max', 'Dupont', 'max.dupont@example.com', '$2y$10$aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 0, '192.168.1.10', '1985-06-20', 'Homme', 'fr', 'France', NULL),
('emma', 'Emma', 'Lemoine', 'emma.lemoine@example.com', '$2y$10$bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 0, '192.168.1.11', '1993-12-01', 'Femme', 'fr', 'France', NULL),
('liam', 'Liam', 'Martin', 'liam.martin@example.com', '$2y$10$cccccccccccccccccccccccccccccccccccccccccccccccccc', 0, '192.168.1.12', '1988-08-15', 'Homme', 'fr', 'France', NULL),
('sara', 'Sara', 'Khan', 'sara.khan@example.com', '$2y$10$dddddddddddddddddddddddddddddddddddddddddddddddddd', 0, '192.168.1.13', '1990-03-22', 'Femme', 'en', 'UK', NULL),
('omar', 'Omar', 'Al-Farsi', 'omar.alfarsi@example.com', '$2y$10$eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', 0, '192.168.1.14', '1987-11-05', 'Homme', 'ar', 'Oman', NULL),
('zoe', 'Zoe', 'Moreau', 'zoe.moreau@example.com', '$2y$10$ffffffffffffffffffffffffffffffffffffffffffffffff', 0, '192.168.1.15', '1995-07-14', 'Femme', 'fr', 'France', NULL),
('youssef', 'Youssef', 'Benali', 'youssef.benali@example.com', '$2y$10$gggggggggggggggggggggggggggggggggggggggggggggggggg', 0, '192.168.1.16', '1991-10-30', 'Homme', 'fr', 'Maroc', NULL),
('maria', 'Maria', 'Garcia', 'maria.garcia@example.com', '$2y$10$hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 0, '192.168.1.17', '1989-01-25', 'Femme', 'es', 'Espagne', NULL),
('paul', 'Paul', 'Dubois', 'paul.dubois@example.com', '$2y$10$iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii', 0, '192.168.1.18', '1986-09-09', 'Homme', 'fr', 'France', NULL),
('lara', 'Lara', 'Schmidt', 'lara.schmidt@example.com', '$2y$10$jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', 0, '192.168.1.19', '1992-02-17', 'Femme', 'de', 'Allemagne', NULL),

('alex', 'Alex', 'Johnson', 'alex.johnson@example.com', '$2y$10$kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk', 0, '192.168.1.20', '1984-04-12', 'Homme', 'en', 'USA', NULL),
('mia', 'Mia', 'Brown', 'mia.brown@example.com', '$2y$10$llllllllllllllllllllllllllllllllllllllllllllllllll', 0, '192.168.1.21', '1990-06-23', 'Femme', 'en', 'USA', NULL),
('leo2', 'Leo', 'Smith', 'leo.smith@example.com', '$2y$10$mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm', 0, '192.168.1.22', '1988-08-08', 'Homme', 'en', 'UK', NULL),
('emma2', 'Emma', 'Williams', 'emma.williams@example.com', '$2y$10$nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn', 0, '192.168.1.23', '1994-11-11', 'Femme', 'en', 'Canada', NULL),
('jacob', 'Jacob', 'Taylor', 'jacob.taylor@example.com', '$2y$10$oooooooooooooooooooooooooooooooooooooooooooooooooo', 0, '192.168.1.24', '1989-05-29', 'Homme', 'en', 'USA', NULL),
('nora', 'Nora', 'Lee', 'nora.lee@example.com', '$2y$10$pppppppppppppppppppppppppppppppppppppppppppppppppp', 0, '192.168.1.25', '1991-12-05', 'Femme', 'en', 'USA', NULL),
('sam', 'Sam', 'Clark', 'sam.clark@example.com', '$2y$10$qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq', 0, '192.168.1.26', '1987-03-15', 'Homme', 'en', 'UK', NULL),
('olivia', 'Olivia', 'Walker', 'olivia.walker@example.com', '$2y$10$rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr', 0, '192.168.1.27', '1993-09-09', 'Femme', 'en', 'USA', NULL),
('mohamed', 'Mohamed', 'Ali', 'mohamed.ali@example.com', '$2y$10$ssssssssssssssssssssssssssssssssssssssssssssssssss', 0, '192.168.1.28', '1985-02-20', 'Homme', 'ar', 'Égypte', NULL),
('sophia', 'Sophia', 'Martinez', 'sophia.martinez@example.com', '$2y$10$tttttttttttttttttttttttttttttttttttttttttttttttttt', 0, '192.168.1.29', '1990-07-15', 'Femme', 'es', 'Mexique', NULL),

('adam', 'Adam', 'Johnson', 'adam.johnson@example.com', '$2y$10$uuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuu', 0, '192.168.1.30', '1986-10-10', 'Homme', 'en', 'USA', NULL),
('chloe', 'Chloe', 'Davis', 'chloe.davis@example.com', '$2y$10$vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv', 0, '192.168.1.31', '1992-01-18', 'Femme', 'en', 'Canada', NULL),
('ethan', 'Ethan', 'Miller', 'ethan.miller@example.com', '$2y$10$wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww', 0, '192.168.1.32', '1989-11-11', 'Homme', 'en', 'USA', NULL),
('ava', 'Ava', 'Wilson', 'ava.wilson@example.com', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 0, '192.168.1.33', '1991-04-04', 'Femme', 'en', 'UK', NULL),
('noah', 'Noah', 'Moore', 'noah.moore@example.com', '$2y$10$yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy', 0, '192.168.1.34', '1988-05-05', 'Homme', 'en', 'USA', NULL),
('isabella', 'Isabella', 'Taylor', 'isabella.taylor@example.com', '$2y$10$zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz', 0, '192.168.1.35', '1993-08-08', 'Femme', 'en', 'USA', NULL),
('lucas', 'Lucas', 'Anderson', 'lucas.anderson@example.com', '$2y$10$aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 0, '192.168.1.36', '1987-09-09', 'Homme', 'en', 'Canada', NULL),
('mia2', 'Mia', 'Thomas', 'mia.thomas@example.com', '$2y$10$bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 0, '192.168.1.37', '1990-12-12', 'Femme', 'en', 'UK', NULL),
('jack', 'Jack', 'Jackson', 'jack.jackson@example.com', '$2y$10$cccccccccccccccccccccccccccccccccccccccccccccccccc', 0, '192.168.1.38', '1989-07-07', 'Homme', 'en', 'USA', NULL),
('amelia', 'Amelia', 'White', 'amelia.white@example.com', '$2y$10$dddddddddddddddddddddddddddddddddddddddddddddddddd', 0, '192.168.1.39', '1992-03-03', 'Femme', 'en', 'Canada', NULL);





INSERT INTO amis (utilisateur_id, ami_id, statut)
SELECT u1.id_u, u2.id_u, 'en_attente'
FROM (
    SELECT id_u FROM users ORDER BY id_u ASC LIMIT 20
) AS u1
JOIN (
    SELECT id_u FROM users ORDER BY id_u ASC LIMIT 20
) AS u2
ON u1.id_u < u2.id_u;





INSERT INTO amis (utilisateur_id, ami_id, statut)
VALUES
(1, 2, 'accepte'),
(1, 3, 'accepte'),
(1, 4, 'accepte'),
(1, 5, 'accepte'),
(1, 6, 'accepte'),
(1, 7, 'accepte'),
(1, 8, 'accepte'),
(1, 9, 'accepte'),
(1, 10, 'accepte'),
(2, 3, 'accepte'),
(2, 4, 'accepte'),
(2, 5, 'accepte'),
(2, 6, 'accepte'),
(2, 7, 'accepte'),
(2, 8, 'accepte'),
(2, 9, 'accepte'),
(2, 10, 'accepte'),
(3, 4, 'accepte'),
(3, 5, 'accepte'),
(3, 6, 'accepte'),
(3, 7, 'accepte'),
(3, 8, 'accepte'),
(3, 9, 'accepte'),
(3, 10, 'accepte'),
(4, 5, 'accepte'),
(4, 6, 'accepte'),
(4, 7, 'accepte'),
(4, 8, 'accepte'),
(4, 9, 'accepte'),
(4, 10, 'accepte'),
(5, 6, 'accepte'),
(5, 7, 'accepte'),
(5, 8, 'accepte'),
(5, 9, 'accepte'),
(5, 10, 'accepte'),
(6, 7, 'accepte'),
(6, 8, 'accepte'),
(6, 9, 'accepte'),
(6, 10, 'accepte'),
(7, 8, 'accepte'),
(7, 9, 'accepte'),
(7, 10, 'accepte'),
(8, 9, 'accepte'),
(8, 10, 'accepte'),
(9, 10, 'accepte');





UPDATE users
SET lvl = 4
WHERE id_u IN (1, 2, 3, 4, 5, 6, 7);
