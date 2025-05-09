-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: forum1
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `album`
--

DROP TABLE IF EXISTS `album`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `album` (
  `id_alb` int(11) NOT NULL AUTO_INCREMENT,
  `nom_alb` varchar(50) NOT NULL,
  PRIMARY KEY (`id_alb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `album`
--

LOCK TABLES `album` WRITE;
/*!40000 ALTER TABLE `album` DISABLE KEYS */;
/*!40000 ALTER TABLE `album` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `amis`
--

DROP TABLE IF EXISTS `amis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int(11) NOT NULL,
  `ami_id` int(11) NOT NULL,
  `statut` enum('en_attente','accepte','refuse') DEFAULT 'en_attente',
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ami_pair` (`utilisateur_id`,`ami_id`),
  KEY `fk_ami` (`ami_id`),
  CONSTRAINT `fk_ami` FOREIGN KEY (`ami_id`) REFERENCES `users` (`id_u`) ON DELETE CASCADE,
  CONSTRAINT `fk_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id_u`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amis`
--

LOCK TABLES `amis` WRITE;
/*!40000 ALTER TABLE `amis` DISABLE KEYS */;
INSERT INTO `amis` VALUES (1,22,13,'en_attente','2025-05-09 11:44:23');
/*!40000 ALTER TABLE `amis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `id_c` int(11) NOT NULL AUTO_INCREMENT,
  `id_img` int(11) DEFAULT NULL,
  `id_u` int(11) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `date_c` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_c`),
  KEY `id_img` (`id_img`),
  KEY `id_u` (`id_u`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id_img`) REFERENCES `images` (`id_img`) ON DELETE CASCADE,
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`id_u`) REFERENCES `users` (`id_u`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `envoyer`
--

DROP TABLE IF EXISTS `envoyer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `envoyer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_exp` int(11) NOT NULL,
  `id_recept` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `date_env` datetime DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_exp` (`id_exp`),
  KEY `id_recept` (`id_recept`),
  CONSTRAINT `envoyer_ibfk_1` FOREIGN KEY (`id_exp`) REFERENCES `users` (`id_u`) ON DELETE CASCADE,
  CONSTRAINT `envoyer_ibfk_2` FOREIGN KEY (`id_recept`) REFERENCES `users` (`id_u`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `envoyer`
--

LOCK TABLES `envoyer` WRITE;
/*!40000 ALTER TABLE `envoyer` DISABLE KEYS */;
INSERT INTO `envoyer` VALUES (1,1,6,'rwtxfycg','2025-05-09 13:24:29',0);
/*!40000 ALTER TABLE `envoyer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id_img` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `chemin` varchar(255) DEFAULT NULL,
  `date_img` date DEFAULT NULL,
  `u_id` int(11) DEFAULT NULL,
  `alb_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_img`),
  KEY `u_id` (`u_id`),
  KEY `alb_id` (`alb_id`),
  CONSTRAINT `images_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`id_u`) ON DELETE CASCADE,
  CONSTRAINT `images_ibfk_2` FOREIGN KEY (`alb_id`) REFERENCES `album` (`id_alb`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` VALUES (1,'ipjzfeklsdw','uploads/img_681de3a12833d.png','2025-05-09',1,NULL),(2,'teste user ','uploads/img_681de74d51b79.png','2025-05-09',22,NULL),(3,'Photo de profil de admin','assets/images/Profil/profil_1.png','2025-05-09',1,NULL),(4,'Photo de profil de user1','assets/images/Profil/profil_2.png','2025-05-09',2,NULL),(5,'Photo de profil de user2','assets/images/Profil/profil_3.png','2025-05-09',3,NULL),(6,'Photo de profil de user3','assets/images/Profil/profil_4.png','2025-05-09',4,NULL),(7,'Photo de profil de user4','assets/images/Profil/profil_5.png','2025-05-09',5,NULL),(8,'Photo de profil de user5','assets/images/Profil/profil_6.png','2025-05-09',6,NULL),(9,'Photo de profil de user6','assets/images/Profil/profil_7.png','2025-05-09',7,NULL),(10,'Photo de profil de user7','assets/images/Profil/profil_8.png','2025-05-09',8,NULL),(11,'Photo de profil de user8','assets/images/Profil/profil_9.png','2025-05-09',9,NULL),(12,'Photo de profil de user9','assets/images/Profil/profil_10.png','2025-05-09',10,NULL),(13,'Photo de profil de user10','assets/images/Profil/profil_11.png','2025-05-09',11,NULL),(14,'Photo de profil de algerien1','assets/images/Profil/profil_12.png','2025-05-09',12,NULL),(15,'Photo de profil de tunisien1','assets/images/Profil/profil_13.png','2025-05-09',13,NULL),(16,'Photo de profil de maltais1','assets/images/Profil/profil_14.png','2025-05-09',14,NULL),(17,'Photo de profil de user14','assets/images/Profil/profil_15.png','2025-05-09',15,NULL),(18,'Photo de profil de user15','assets/images/Profil/profil_16.png','2025-05-09',16,NULL),(19,'Photo de profil de user16','assets/images/Profil/profil_17.png','2025-05-09',17,NULL),(20,'Photo de profil de user17','assets/images/Profil/profil_18.png','2025-05-09',18,NULL),(21,'Photo de profil de user18','assets/images/Profil/profil_19.png','2025-05-09',19,NULL),(22,'Photo de profil de user19','assets/images/Profil/profil_20.png','2025-05-09',20,NULL),(23,'Photo de profil de user20','assets/images/Profil/profil_21.png','2025-05-09',21,NULL),(24,'Photo de profil de Lienzo','assets/images/Profil/profil_22.png','2025-05-09',22,NULL);
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('message','invitation') NOT NULL,
  `contenu` text NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_u`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publications`
--

DROP TABLE IF EXISTS `publications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publications` (
  `id_p` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `type` enum('texte','image') DEFAULT 'texte',
  PRIMARY KEY (`id_p`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_u`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publications`
--

LOCK TABLES `publications` WRITE;
/*!40000 ALTER TABLE `publications` DISABLE KEYS */;
INSERT INTO `publications` VALUES (1,1,'Bienvenue sur le forum !','2025-05-09 09:32:46',NULL,'texte'),(2,1,'Deuxième publication test','2025-05-09 09:32:46',NULL,'texte'),(3,1,'ipjzfeklsdw','2025-05-09 00:00:00','uploads/img_681de3a12833d.png','image'),(4,22,'teste user ','2025-05-09 00:00:00','uploads/img_681de74d51b79.png','image'),(6,1,'ib test','2025-05-09 13:41:30','img_681de9ea74a28.png','texte'),(7,1,'xcghvjbk','2025-05-09 13:42:48',NULL,'texte'),(8,22,'eh toi la','2025-05-09 13:44:07','img_681dea86f2072.png','texte');
/*!40000 ALTER TABLE `publications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id_u` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `lvl` int(11) DEFAULT 0,
  `IP` varchar(15) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `langue` varchar(50) DEFAULT NULL,
  `pays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_u`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@metaforge.com','f865b53623b121fd34ee5426c792e5c33af8c227',100,NULL,21,'fr','fr'),(2,'user1','user1@example.com','mdp1',1,'192.168.0.1',25,'fr','France'),(3,'user2','user2@example.com','mdp2',2,'192.168.0.2',30,'en','USA'),(4,'user3','user3@example.com','mdp3',1,'192.168.0.3',22,'es','Spain'),(5,'user4','user4@example.com','mdp4',2,'192.168.0.4',28,'de','Germany'),(6,'user5','user5@example.com','mdp5',0,'192.168.0.5',21,'it','Italy'),(7,'user6','user6@example.com','mdp6',1,'192.168.0.6',26,'fr','Belgium'),(8,'user7','user7@example.com','mdp7',2,'192.168.0.7',27,'en','Canada'),(9,'user8','user8@example.com','mdp8',1,'192.168.0.8',29,'fr','France'),(10,'user9','user9@example.com','mdp9',2,'192.168.0.9',24,'pt','Portugal'),(11,'user10','user10@example.com','mdp10',1,'192.168.0.10',31,'fr','Morocco'),(12,'algerien1','algerien@example.com','mdp11',2,'192.168.10.1',26,'ar','Algeria'),(13,'tunisien1','tunisien@example.com','mdp12',1,'192.168.10.2',30,'fr','Tunisia'),(14,'maltais1','maltais@example.com','mdp13',0,'192.168.10.3',27,'lma','Malta'),(15,'user14','user14@example.com','mdp14',2,'192.168.0.14',32,'en','UK'),(16,'user15','user15@example.com','mdp15',1,'192.168.0.15',20,'en','Ireland'),(17,'user16','user16@example.com','mdp16',2,'192.168.0.16',33,'de','Germany'),(18,'user17','user17@example.com','mdp17',0,'192.168.0.17',26,'es','Mexico'),(19,'user18','user18@example.com','mdp18',1,'192.168.0.18',28,'fr','France'),(20,'user19','user19@example.com','mdp19',2,'192.168.0.19',29,'en','USA'),(21,'user20','user20@example.com','mdp20',2,'192.168.0.20',22,'it','Italy'),(22,'Lienzo','lienzo@example.com','2cdd0e4193dfc19c54c2cf5ef44d68c0f8be3619',0,NULL,21,'en','fr');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-09 14:07:19
